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
use br\gov\sial\core\Factorable;

/* classes necessarias para execuacao do SIALAplication */
require_once 'DataStructAbstract.php';
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Factorable.php';

/**
 * SIAL
 *
 * manipulador de fila primeiro a entrar primeiro a sair
 *
 * @package br.gov.sial.core.util
 * @subpackage stack
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class FIFO extends DataStructAbstract implements Factorable
{
    /**
     * retorna o proximo elemento sem removê-lo
     *
     * @return mixed
     * */
    public function peek ()
    {
        return current($this->_container);
    }

    /**
     * remove e retorna o proximo elemento
     *
     * @return mixed
     * */
    public function pop ()
    {
        return array_shift($this->_container);
    }

    /**
     * insere um objeto na parte superior do Stack
     *
     * @param mixed[] $element
     * @return br\gov\sial\core\util\stack\Stack
     * */
    public function push ($element)
    {
        $this->_container[] = $element;
        return $this;
    }

    /**
     * {@inheritdoc}
     * */
    public function flush()
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
        return isset($this->_container[1]);
    }

    /**
     * {@inheritdoc}
     * */
    public function isEmpty ()
    {
        return empty($this->_container);
    }

    /**
     * inicializa a pilha
     *
     * @param mixed[] $elements
     * @return br\gov\sial\core\util\stack\Stack
     * */
    public function init (array $elements = array())
    {
        foreach ($elements as $element) {
            $this->push($element);
        }
        return $this;
    }

    /**
     * cria instancia e retorna objeto de FIFO
     *
     * @return br\gov\sial\core\util\stack\FIFO
     * */
    public static function factory ()
    {
        return new self;
    }
}