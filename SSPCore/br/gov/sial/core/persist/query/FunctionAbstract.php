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
namespace br\gov\sial\core\persist\query;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\Renderizable;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage query
 * @name FunctionAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class FunctionAbstract extends SIALAbstract implements Renderizable
{
    /**
     * @var string
     */
    const T_COMMAND = 'INVALID_FUNCTION';

    /**
     * @var Renderizable
     * */
    private $_element;

    /**
     * Construtor.
     * 
     * @param Renderizable $element
     * */
    public function __construct (Renderizable $element)
    {
        $this->_element = $element;
    }

    /**
     * @return string
     * */
    public function render ()
    {
        $alias   = NULL;
        $content = NULL;
        if ($this->_element instanceof Column) {
            $alias   = $this->_element->alias();
            $alias   = $alias ? " AS {$alias}" : NULL;

            $content = $this->_element->entity()->name();
            $content = $content ? "{$content}." : NULL;
            $content = $content . "\"{$this->_element->name()}\"";
        } else {
            $content = $this->_element->render();
        }
        return $this::T_COMMAND . '(' . $content . ')' .  $alias;
    }
}