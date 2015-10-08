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

namespace br\gov\sial\core\persist\file;
use br\gov\sial\core\persist\PersistConfig,
    br\gov\sial\core\persist\Connect as ParentConnect;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage file
 * @name Connect
 * @author André Borges
 * */
class Connect extends ParentConnect
{
    /**
     * Método construtor.
     * @param persistConfig $config
     * @throws PersistException
     * */
    public function __construct (PersistConfig $config)
    {
        parent::__construct($config);
    }

    /**
     * Método de conexão com a persistência de arquivo.
     *
     * (non-PHPdoc)
     * @see Parent::_connect()
     */
    // @codeCoverageIgnoreStart
    public function _connect(PersistConfig $config)
    {
        ;
    }
    // @codeCoverageIgnoreEnd

    /**
     * (non-PHPdoc)
     * @see br\gov\sial\core\persist.Connect::commit()
     */
    // @codeCoverageIgnoreStart
    public function commit ()
    {
        ;
    }
    // @codeCoverageIgnoreEnd

    /**
     * (non-PHPdoc)
     * @see br\gov\sial\core\persist.Connect::hasTransactionRunning()
     */
    // @codeCoverageIgnoreStart
    public function hasTransactionRunning ()
    {
        ;
    }
    // @codeCoverageIgnoreEnd

    /**
     * (non-PHPdoc)
     * @see br\gov\sial\core\persist.Connect::retrieve()
     */
    // @codeCoverageIgnoreStart
    public function retrieve ()
    {
        ;
    }
    // @codeCoverageIgnoreEnd

    /**
     * (non-PHPdoc)
     * @see br\gov\sial\core\persist.Connect::rollback()
     */
    // @codeCoverageIgnoreStart
    public function rollback ()
    {
        ;
    }
    // @codeCoverageIgnoreEnd

    /**
     * (non-PHPdoc)
     * @see br\gov\sial\core\persist.Connect::transaction()
     */
    // @codeCoverageIgnoreStart
    public function transaction ()
    {
        ;
    }
    // @codeCoverageIgnoreEnd

    /**
     * (non-PHPdoc)
     * @see br\gov\sial\core\persist.Connect::update()
     */
    // @codeCoverageIgnoreStart
    public function update ()
    {
        ;
    }
    // @codeCoverageIgnoreEnd
}