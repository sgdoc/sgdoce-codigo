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
namespace br\gov\sial\core\output\screen;
use br\gov\sial\core\Component,
    br\gov\sial\core\Composable,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\exception\IndexOutOfBoundsException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output
 * @subpackage screen
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class ElementContainerAbstract extends ElementAbstract implements Composable
{
    /**
     * @param ElementAbstract
     * */
    protected $_children = array();

    /**
     * retorna element de ID 'idx'
     *
     * @return ElementAbstract
     * */
    public function __get ($idx)
    {
        try {

            # matem retrocompatibilidade
            $elm = parent::__get($idx);

            if ($elm instanceof parent) {
                return $elm;
            }
        } catch (\Exception $exp) { ; }

        # se o ID for solicitado, retorna-o
        if ('id' == $idx) {
            return parent::__get('id');
        }

        # procura o elemento examente pelo ID informado
        $elm = $this->getElementById($idx);

        if ($elm) {
            return $elm;
        }

        # procura por sublemento
        # subelement neste contexto refere-se a elemento
        # que possui seu ID composto pelo ID do container
        # que esta inserido.
        # <id#master>
        #   <h1#master-title>
        # </dir>
        # a linha abaixo permitira buscar o elemento info-
        # mando apenas a parte diferencia do container: 'title'
        $elm = $this->getElementById(parent::__get('id') . '-' . $idx);

        if ($elm) {
            return $elm;
        }

        return null;
    }

    /**
     * @param Component
     * @return ElementContainerAbstract
     * @throws IllegalArgumentException
     * */
    public function add ($component)
    {
        /*
         * esta lambaça toda eh devido a limitacao do php em reconhecer
         * param formais como method (Component[] $component)
         * */
        if (!is_array($component)) {
            $component = array($component);
        }

        foreach ($component as $cmp) {
            IllegalArgumentException::throwsExceptionIfParamIsNull($cmp instanceof Component, 'tipo não aceito');
            $this->_children[] = $cmp;
        }

        return $this;
    }

    /**
     * recupera elemento a partir da posição informada
     *
     * @param integer $elmid
     * @return ElementAbstract
     * */
    public function getElementByIndex ($position)
    {
        IndexOutOfBoundsException::throwsExceptionIfParamIsNull(isset($this->_children[$position]), 'elemento inexistente');
        return $this->_children[$position];
    }

    /**
     * @return ElementAbstract[]
     * */
    public function children ()
    {
        return $this->_children;
    }

    /**
     * @param Component
     * @return ElementContainerAbstract
     * */
    public function replaceChild (Component $component)
    {
        $key = array_search($component, $this->_children);
        $this->_children[$key] = $component;
        return $this;
    }

    /**
     * @return ElementContainerAbstract
     * */
    public function clear ()
    {
        $this->_children = array();
        return $this;
    }

    /**
     * @override
     * @return string
     * */
    public function render ()
    {
        $content = '';

        foreach ($this->_children as $child) {
            $content .= $child->render();
        }

        $this->setContent($content . $this->content());

        return sprintf('<%1$s%2$s>%3$s</%1$s>', $this::T_TAG, $this->renderProperty(), $this->content());
    }

    /**
     * Procura recursivamente um elemento com o id informado
     * @param string $id
     * @return ElementAbstract
     */
    public function getElementById ($id)
    {
        $element = NULL;

        foreach ($this->children() as $item) {

            if ($item->propertyExists('id') && $item->id == $id) {
                $element = $item;

            } elseif ($item instanceof ElementContainerAbstract) {
                $element = $item->getElementById($id);
            }

            if (!empty($element)) {
                break;
            }
        }

        return $element;
    }

    /**
     * Procura recursivamente todos os elementos que possuem a classe informada
     * @param string $class
     * @return ElementAbstract[]
     */
    public function getElementsByClass ($class)
    {
        $elements = array();

        foreach ($this->children() as $item) {

            if ($item->hasClass($class)) {
                $elements[] = $item;
            }

            if ($item instanceof ElementContainerAbstract) {
                $recursive = $item->getElementsByClass($class);

                if (!empty($recursive)) {
                    $elements = array_merge($elements, $recursive);
                }
            }
        }

        return $elements;
    }

    /**
     * Procura recursivamente todos os elementos que possuem o atributo name informado
     * @param string $class
     * @return ElementAbstract[]
     */
    public function getElementsByName ($name)
    {
        $elements = array();

        foreach ($this->children() as $item) {

            if ($item->propertyExists('name') && $item->name == $name) {
                $elements[] = $item;
            }

            if ($item instanceof ElementContainerAbstract) {
                $recursive = $item->getElementsByName($name);

                if (!empty($recursive)) {
                    $elements = array_merge($elements, $recursive);
                }
            }
        }

        return $elements;
    }
}