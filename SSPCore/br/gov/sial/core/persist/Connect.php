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
namespace br\gov\sial\core\persist;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\persist\PersistConfig,
    br\gov\sial\core\persist\exception\PersistException;

/**
 * SIAL
 *
 * Componente de conexão com o repositório.
 *
 * @package br.gov.sial.core
 * @subpackage persist
 * @name Connect
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class Connect extends SIALAbstract
{
    /**
     * @var Connect[]
     * */
    protected static $_instance = array();

    /**
     * Link de conexão com o repositório.
     * @var \PDO
     * */
    protected $_resource = NULL;

    /**
     * Adaptador em uso
     *
     * @var string
     * */
    protected $_adapter = NULL;

    /**
     * Driver em uso
     *
     * @var string
     * */
    protected $_driver = NULL;

    /**
     * @var string
     * */
    protected $_source = NULL;

     /**
     * Autocommit das operações.
     *
     * @var bool
     * */
    public static $autoCommit = FALSE;

    /**
     * Construtor.
     *
     * @param PersistConfig $config
     * @throws PersistException
     * */
    public function __construct (PersistConfig $config)
    {
        $this->_resource = $this->_connect($config);
        $this->_adapter  = $config->get('adapter');
        $this->_driver   = $config->get('driver');
        $this->_source   = $config->get('source');
    }

    /**
     * Efetua conexão com o repositório.
     *
     * @abstract
     * @param PersistConfig
     * @return \br\gov\sial\core\persist\Connect
     * */
    // @codeCoverageIgnoreStart
    protected abstract function _connect (PersistConfig $config);
    // @codeCoverageIgnoreEnd

    /**
     * Grava todos os dados pendentes e finaliza transação.
     *
     * @return br\gov\sial\core\persist\Connect
     * @throws PersistException
     * */
    // @codeCoverageIgnoreStart
    public abstract function commit ();
    // @codeCoverageIgnoreEnd

    /**
     * Retorna o adaptador em uso.
     *
     * @return string
     * */
    public function getAdapter ()
    {
        return $this->_adapter;
    }

    /**
     * Retorna o driver em uso.
     *
     * @return string
     * */
    public function getDriver ()
    {
        return $this->_driver;
    }

    /**
     * Retorna o source.
     *
     * @return string
     * */
    public function getSource ()
    {
        return $this->_source;
    }

    /**
     * @return bool
     * */
    // @codeCoverageIgnoreStart
    public abstract function hasTransactionRunning ();
    // @codeCoverageIgnoreEnd

    /**
     * Executa comandos de consulta contra o repositório.
     *
     * @return \br\gov\sial\core\persist\ResultSet
     * */
    // @codeCoverageIgnoreStart
    public abstract function retrieve ();
    // @codeCoverageIgnoreEnd

    /**
     * Descarta toda as operações na transação.
     *
     * @return br\gov\sial\core\persist\Persist
     * @throws PersistException
     * */
    // @codeCoverageIgnoreStart
    public abstract function rollback ();
    // @codeCoverageIgnoreEnd

    /**
     * Inicializa transação.
     *
     * @return br\gov\sial\core\persist\Connect
     * */
    // @codeCoverageIgnoreStart
    public abstract function transaction ();
    // @codeCoverageIgnoreEnd

    /**
     * Executa comando de alteração contra o repositório.
     * */
    // @codeCoverageIgnoreStart
    public abstract function update ();
    // @codeCoverageIgnoreEnd

    /**
     * Fábrica Connect
     *
     * @param PersistConfig $config
     * @return \br\gov\sial\core\persist\Connect
     * @throws PersistException
     * */
    public static function factory (PersistConfig $config)
    {
        $hash = $config->hash();
        $tmpID = $config->hash();
        if (FALSE === isset(self::$_instance[$tmpID])) {
            $namespace = sprintf('\br\gov\sial\core\persist\%s\Connect', $config->get('adapter'));
            self::$_instance[$tmpID] = $namespace::factory($config);
        }
        return self::$_instance[$tmpID];
    }
}