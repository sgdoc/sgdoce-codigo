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
    br\gov\sial\core\persist\webservice\Connect,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\Persist as ParentPersist;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage webservice
 * @name Persist
 * @author Fábio Lima <fabioolima@gmail.com>
 * */
class Persist extends ParentPersist
{
    /**
     * Tipo válido de persistência para o construtor.
     *
     * @var string
     * */
    const PERSIST_TYPE = 'webservice';

    /**
     * Construtor.
     *
     * @param PersistConfig $config
     * */
    public function __construct (PersistConfig $config = NULL)
    {
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     * */
    protected function _connect (PersistConfig $config)
    {
        return Connect::factory($config);
    }

    /**
     * {@inheritdoc}
     */
    public function execute ($query, $params = NULL)
    {
    }

    /**
     * {@inheritdoc}
     * */
    public function find ($key)
    {
    }

    /**
     * {@inheritdoc}
     * */
    public function save (ValueObjectAbstract $valueObject)
    {
    }

    /**
     * {@inheritdoc}
     * */
    public function update (ValueObjectAbstract $valueObject)
    {
    }

    /**
     * {@inheritdoc}
     * */
    public function delete (ValueObjectAbstract $valueObject)
    {
    }

    /**
     * {@inheritdoc}
     * */
    public function getQuery ($entity)
    {
    }

    /**
     * {@inheritdoc}
     * */
    public function getEntity ($entity, array $columns = array())
    {
    }
}