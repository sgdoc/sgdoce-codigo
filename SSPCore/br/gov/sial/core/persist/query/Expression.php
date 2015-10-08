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
    br\gov\sial\core\Renderizable,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage query
 * @name Expression
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Expression extends SIALAbstract implements Renderizable
{
    /**
     * @var string
     * */
    const EXPRESSION_MUST_BE_STRING = 'Expressão deve ser uma string ou objeto de Renderizable.';

    /**
     * @var string
     * */
    private $_content;

    /**
     * Construtor.
     *
     * <ul>
     *     @override
     *     <li>Expression::__construct(string $expression)</li>
     *     <li>Expression::__construct(Renderizable $expression)</li>
     * </ul>
     *
     * @param string|Renderizable $expression
     * @throws IllegalArgumentException
     * */
    public function __construct ($expression)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull((is_string($expression) ||$expression instanceof Renderizable ), self::EXPRESSION_MUST_BE_STRING);
        $this->_content = $expression;
    }

    /**
     * @return string
     * */
    public function render ()
    {
        if ($this->_content instanceof Renderizable) {
            return $this->_content->render();
        }

        return $this->_content;
    }
}