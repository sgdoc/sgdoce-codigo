<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */
namespace br\gov\sial\core\persist\webservice;
use br\gov\sial\core\persist\PersistConfig,
    br\gov\sial\core\persist\webservice\Config,
    br\gov\sial\core\persist\webservice\ResultSet,
    br\gov\sial\core\persist\Connect as ParentConnect,
    br\gov\sial\core\persist\exception\PersistException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage webservice
 * @name Connect
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class Connect extends ParentConnect
{
    /**
     * @var Config
     */
    private $_config;

    /**
     * @var String
     */
    private $_wsQuery;

    /**
     * @var String
     */
    private $_wsParam;

    /**
     * Construtor.
     *
     * @param Config $config
     * @throws PersistException
     * */
    public function __construct (Config $config)
    {
        parent::__construct($config);
    }

    /**
     * @param PersistConfig $config
     * @return SoapClient
     * @throws PersistException
     * */
    protected function _connect(PersistConfig $config)
    {
        try {

            $paramArray = array('local_cert' => $config->get('certificate'),
                                'proxy_host' => $config->get('proxyhost'),
                                'proxy_port' => $config->get('proxyport'),
                                'proxy_login' => $config->get('username'),
                                'proxy_password' => $config->get('password'),
                                'trace' => 1); # Retornar em XML

            $resource = new \SoapClient($config->getDSN(), $paramArray);

            $this->_config = $config;
            return $resource;

        } catch (\Exception $ldape) {
            # @todo da mesma forma do PDO guardar log para o LDAP
            ;
            throw new PersistException($ldape->getMessage(), $ldape->getCode());
        }
    }

    /**
     * {@inheritdoc}, tais como: Delete, Update, Insert, Create, etc.
     *
     * @return ResultSet
     * @throws PersistException
     * */
    public function update ()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     * @return ResultSet
     * */
    public function retrieve ()
    {
        $method = $this->_wsQuery;
        return new ResultSet($this, $this->_resource->$method($this->_wsParam));
    }

    /**
     * Prepara o comando que será executado no repositório.
     *
     * @param string $query
     * @param stdClass[] $params
     * @return Connect
     * */
    public function prepare ($query, $params)
    {
        $this->_wsQuery = $query;
        $this->_wsParam = is_array($params) ? $params : array($params);
        return $this;
    }

    /**
     * {@inheritdoc}
     * */
    public function hasTransactionRunning ()
    {
        return FALSE;
    }

    /**
     * {@inheritdoc}
     * */
    public function transaction ()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     * */
    public function commit ()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     * */
    public function rollback ()
    {
        return $this;
    }

    /**
     * Fábrica de Connect.
     *
     * @param PersistConfig $config
     * @return Connect
     * */
    public static function factory (PersistConfig $config)
    {
        $namespace  = __NAMESPACE__ . self::NAMESPACE_SEPARATOR . 'Connect';
        return new $namespace($config);
    }

    /**
     * @return \PDO
     */
    public function getResource ()
    {
        return $this->_resource;
    }
}