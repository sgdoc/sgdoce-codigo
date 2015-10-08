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
use br\gov\sial\core\lang\TFile,
    br\gov\sial\core\util\file\James,
    br\gov\sial\core\util\file\Tamburete,
    br\gov\sial\core\persist\persistConfig,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\Persist as ParentPersist;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage file
 * @name Persist
 * @author André Borges
 * */
class Persist extends ParentPersist
{
    /**
     * @var string
     * */
    const PERSIST_TYPE = 'pFile';

    /**
     * {@inheritdoc}
     * @todo recuperar a permissao do config.ini
     * */
    public function _connect (persistConfig $config)
    {
        ;
    }

    /**
     * @todo Pesquisar por termo dentro de um arquivo de texto.
     */
    public function find ($key)
    {// @codeCoverageIgnoreStart
    }// @codeCoverageIgnoreEnd

    /**
     * Persiste os dados no repositório.
     *
     * @param ValueObjectAbstract $voFile
     * @return Persist
     * @throws PersistException
     * */
    public function save (ValueObjectAbstract $voFile)
    {
        return James::factory($voFile)->filePersist();
    }

    /**
     * Altera os dados no repositório
     *
     * @param TFile $voFile
     * @return Persist
     * */
    public function update (ValueObjectAbstract $voFile)
    {
        return James::factory($voFile)->filePersist();
    }

    /**
     * Exclui dados no repositório.
     *
     * @param TFile $voFile
     * @return Persist
     * */
    public function delete (ValueObjectAbstract $voFile)
    {
        return James::factory($voFile)->filePersist();
    }

    /**
     * {@inheritdoc}
     * @see Persistable::getQuery()
     */
    public function getQuery ($entity)
    {
    }

    /**
     * {@inheritdoc}
     * @see Persistable::getEntity()
     */
    public function getEntity ($entity, array $columns = array())
    {
    }
}