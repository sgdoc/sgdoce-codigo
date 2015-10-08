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
namespace br\gov\sial\core\persist\ldap;
use br\gov\sial\core\persist\PersistConfig,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\persist\exception\PersistException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage ldap
 * @name Config
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class Config extends PersistConfig
{
    /**
     * @var string
     * */
    const CONFIG_MANDATORY_PROPERTY = 'É necessário definir a propriedade PersistConfig::%s';

    /**
     * @var string
     * */
    const CONFIG_UNSUPPORTED_VERSION = 'A versao %s do LDAP não é suportado pela SIAL::Persist';

    /**
     * Versões de Ldap aceitas
     *
     * @var string[]
     * */
    private static $_acceptedLdapVersion= array('3');

    /**
     * Construtor.
     *
     * @param string[] $config
     * @throws IllegalArgumentException
     * */
    public function __construct (array $config = array())
    {
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     * */
    public function getDSN()
    {
        $port     = $this->get('port');
        $hostname = $this->get('hostname');
        return "{$hostname}:{$port}";
    }

    /**
     * {@inheritdoc}
     * @throws PersistException
     * */
    protected function _valid (array $config)
    {
        $require = array('adapter', 'hostname', 'password', 'port', 'source', 'username', 'version');

        foreach ($require as $key) {
            PersistException::throwsExceptionIfParamIsNull(
                $this->exists($key), sprintf(self::CONFIG_MANDATORY_PROPERTY, $key)
            );
        }
    }

    /**
     * Fábrica de Config.
     *
     * @param string $dsName
     * @throws IllegalArgumentException
     * @return Config
     * */
    public static function factory ($dsName = NULL)
    {
        $data = self::$configs[$dsName];

        IllegalArgumentException::throwsExceptionIfParamIsNull(
            in_array($data['version'], self::$_acceptedLdapVersion),
            sprintf(self::CONFIG_UNSUPPORTED_VERSION, $data['version'])
        );

        $namespace = __NAMESPACE__ . self::NAMESPACE_SEPARATOR . 'Config';

        return new $namespace($data);
    }
}