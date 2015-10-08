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
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output
 * @subpackage screen
 * @name Screen
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Screen extends SIALAbstract
{
    /**
     * @var DecoratorAbstract
     * */
    protected $_decorator = NULL;

    /**
     * @var DocumentAbstract
     * */
    protected $_document = array();

    /**
     * @param string $type
     * @param DecoratorAbstract $decorator
     * @throws IllegalArgumentException
     * */
    public function __construct ($output = 'html', DecoratorAbstract $decorator = NULL)
    {
        $this->_document = DocumentAbstract::factory($output);
        $this->_decorator = $decorator;
    }

    /**
     * criador de elemento
     *
     * @param string $elementType
     * @param string $
     * */

    /**
     * (@fake: public static)
     *
     * @override
     * @throws IllegalArgumentException
     * */
    public function __get ($name)
    {
        $attr = "_{$name}";
        if (property_exists($this, $attr)) {
            return $this->$attr;
        }
        parent::__get($name);
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    public function __toString ()
    {
        try {
            return $this->_document->__toString();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}