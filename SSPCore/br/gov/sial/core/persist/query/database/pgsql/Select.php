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
namespace br\gov\sial\core\persist\query\database\pgsql;
use br\gov\sial\core\persist\query\Entity,
    br\gov\sial\core\persist\query\database\From,
    br\gov\sial\core\persist\query\database\SelectAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist.query.database
 * @subpackage pgsql
 * @name Select
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Select extends SelectAbstract
{
    /**
     * Construtor.
     *
     * @param Entity $entity
     * */
    public function __construct(Entity $entity = NULL)
    {
        parent::__construct($entity);
    }

    /**
     * Fábrica de Select
     *
     * @param Entity
     * @return Select
     * */
    public static function factory (Entity $entity = NULL)
    {
        return new self ($entity);
    }
}