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
namespace br\gov\sial\core\persist\query\database;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\Renderizable,
    br\gov\sial\core\persist\query\Entity,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist.query
 * @subpackage database
 * @name From
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class From extends SIALAbstract implements Renderizable
{
    /**
     * @var string
     * */
    private $_schema;

    /**
     * @var string
     * */
    private $_entity;

    /**
     * @var string
     * */
    private $_alias;

    /**
     * Construtor.
     *
     * @param Entity $ntity
     * @throws IllegalArgumentException
     * */
    public function __construct (Entity $entity)
    {
        # :init properties
        $this->_entity = $entity->name();
        $this->_schema = $entity->schema();
        $this->_alias  = $entity->alias();
    }

    /**
     * Retorna o nome da entidade.
     *
     * @return string
     * */
    public function entity ()
    {
        return $this->_entity;
    }

    /**
     * Retorna o apelido da entidade.
     *
     * @var string
     * */
    public function alias ()
    {
        return $this->_alias;
    }

    /**
     * Retorna o nome completo da entidade incluido apelido.
     *
     * @return string
     * */
    public function qualifiedName ()
    {
        $qualified = $this->_schema;

         if ($qualified) {
             $qualified .= ".{$this->_entity}";
         }

         if ($this->_alias) {
             $qualified .= " AS {$this->_alias}";
         }
         return  $qualified;
    }

    /**
     * Representação textual.
     *
     * @return string
     * */
    public function render ()
    {
        return "FROM {$this->qualifiedName()}";
    }

    /**
     * Fábrica de From.
     * 
     * @param Entity
     * @return From
     * */
    public static function factory (Entity $entity)
    {
        return new self($entity);
    }
}