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
namespace br\gov\sial\core\util\stack;
use br\gov\sial\core\SIALAbstract;

require_once 'DataStructAbstract.php';
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Factorable.php';

/**
 * SIAL
 *
 * @package br.gov.sial.core.util
 * @subpackage stack
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Stack extends DataStructAbstract
{
    /**
     * @var mixed[]
     * */
    protected $_container = array();

    /**
     * retorna elemento sem removê-lo da pilha
     *
     * @return mixed
     * */
    public function peek ()
    {
        $content = NULL;
        $last = sizeof($this->_container) - 1;

        if (isset($this->_container[$last])) {
            $content = $this->_container[$last];
        }

        return $content;
    }

    /**
     * remove e retorna elemento da pilha
     *
     * @return mixed
     * */
    public function pop ()
    {
        return array_pop($this->_container);
    }

    /**
     * insere um novo elemento na pilha
     *
     * @param mixed[] $element
     * @return Stack
     * */
    public function push ($element)
    {
        array_push($this->_container, $element);
        return $this;
    }

    /**
     * remove todos os elemento da pilha
     *
     * @return Stack
     * */
    public function flush ()
    {
        $this->_container = array();
        return $this;
    }

    /**
     * retorna TRUE se existir um proximo elemento
     *
     * @return boolean
     * */
    public function hasNext ()
    {
        return (boolean) sizeof($this->_container);
    }

    /**
     * retorna TRUE se a pilha estiver vazia
     *
     * @return bool
     * */
    public function isEmpty ()
    {
        return !$this->hasNext();
    }

    /**
     * inicializa a pilha
     *
     * @param mixed[] $elements
     * @return Stack
     * */
    public function init (array $elements = array())
    {
        return new self;
    }
}