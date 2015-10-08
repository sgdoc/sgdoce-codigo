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
use br\gov\sial\core\SIALAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.mvcb.view
 * @subpackage skeleton
 * @name Reader
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Reader extends SIALAbstract
{
    /**
     * @var resource
     * */
    private $_resource;

    /**
     * Construtor.
     *
     * @param string $filename
     * */
    public function __construct ($filename)
    {
        $this->_resource = self::tokenize(file_get_contents($filename));
    }

    /**
     * Converte cada um dos tokens em um elemento.
     * É considerado token a sequência de texto separado por ';'
     *
     * @param string $content
     * @return Element[]
     * */
    public static function tokenize ($content)
    {
        $tokens = array();
        foreach (json_decode($content) as $element) {
            $tokens[] = Element::factory($element);
        }
        reset($tokens);
        return $tokens;
    }

    /**
     * Recupera o token do elemento atual.
     *
     * @return Element
     * */
    public function read ()
    {
        $elmnt = current($this->_resource);
        next($this->_resource);
        return $elmnt;
    }

    /**
     * Fábrica de Reader.
     *
     * @return Reader
     * */
    public static function factory ($filename)
    {
        return new self($filename);
    }
}