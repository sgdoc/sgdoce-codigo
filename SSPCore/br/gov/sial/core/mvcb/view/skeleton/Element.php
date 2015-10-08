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
namespace br\gov\sial\core\mvcb\view\skeleton;
use \stdClass as Object,
    br\gov\sial\core\SIALAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.mvcb.view
 * @subpackage skeleton
 * @name Element
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Element extends SIALAbstract
{
    /**
     * @var string
     * */
    private $_type;

    /**
     * @var stdClass
     * */
    private $_properties;

    /**
     * @var string
     */
    private $_text;

    /**
     * @var stdClass[]
     * */
    private $_children = array();

    /**
     * Construtor.
     *
     * @param string $type
     * @param stdClass $properties
     * @param stdClass[] $children
     * */
    public function __construct ($type, Object $properties, array $children = array())
    {
        $this->_type = $type;
        $this->_properties = $properties;
        foreach ($children as $child) {
            $this->_children[] = self::factory($child);
        }
    }

    /**
     * Recupera o tipo do elemento.
     *
     * @return string
     * */
    public function type ()
    {
        return $this->_type;
    }

    /**
     * Recupera o nome da propriedade do elemento.
     *
     * @param string $name
     * @return string
     * */
    public function property ($name)
    {
        if (!isset($this->_properties->$name)) {
            return NULL;
        }
        return $this->_properties->$name;
    }

    /**
     * Retorna todos as propriedades.
     *
     * @return stdClass
     * */
    public function getProperties ()
    {
        return $this->_properties;
    }

    /**
     * Retorna todos os filhos.
     *
     * @return stdClass
     * */
    public function children ()
    {
        return $this->_children;
    }

    /**
     * Retorna TRUE se existir filhos.
     *
     * @return boolean
     * */
    public function hasChildren ()
    {
        return (0 < sizeof($this->_children));
    }

    /**
     * Fábrica de Element.
     * 
     * @param stdClass $data
     *
     * @todo Checar existência da propriedade.
     * */
    public static function factory ($data)
    {
        return new self(
            $data->type,
            $data->properties,
            isset($data->children) ? $data->children : array()
        );
    }
}