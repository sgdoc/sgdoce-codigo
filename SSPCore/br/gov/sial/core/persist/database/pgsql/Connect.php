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
namespace br\gov\sial\core\persist\database\pgsql;
use br\gov\sial\core\persist\persistConfig,
    br\gov\sial\core\persist\database\Config as PersistDatabaseConfig,
    br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\persist\database\Connect as ParentConnect;

/**
 * SIAL
 *
 * Componente de conexão com o respositório
 *
 * @package br.gov.sial.core.persist
 * @subpackage database
 * @name Connect
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Connect extends ParentConnect
{
    /**
     * Construtor.
     *
     * @param persistConfig $config
     * @throws PersistException
     * */
    public function __construct (PersistDatabaseConfig $config)
    {
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     * */
    protected function _connect(persistConfig $config)
    {
        try {
            $resource = new \PDO($config->getDSN(), $config->get('username'), $config->get('password'));

            # throws exception if anything wrong
            $resource->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            # get values as objects
            $resource->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);

            # column names aways lowercase
            $resource->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);

            return $resource;

        } catch (\PDOException $pdoe) {
            # @todo um log com o error do PDO devera ser guardo

            throw new PersistException($pdoe->getMessage(), $pdoe->getCode(), $pdoe);
        }
    }
}