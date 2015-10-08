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
use br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage query
 * @name LogicAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class LogicAbstract extends OperatorAbstract
{
    /**
     * @var string
     * */
    const T_AND_OPERATOR = ' AND ';

    /**
     * @var string
     * */
    const T_OR_OPERATOR = ' OR ';

    /**
     * Referência para Query que está sendo manipulada. Esta referência é necessária para
     * permitir o encadeamento de vários operadores, ex: $query->where()->having()
     *
     * @var QueryAbstract
     * */
    private $_query;

    /**
     * @var string
     * */
    protected $_oper;

    /**
     * @var OperatorAbstract
     * */
    protected $_expression = array();

    /**
     * Construtor.
     *
     * @access protected
     * */
    private function __construct ()
    {
    }

    /**
     * @param OperatorAbstract $expression
     * @param string $operator (and, or)
     * @return LogicAbstract
     * */
    public function add (OperatorAbstract $expression, $operator = self::T_AND_OPERATOR)
    {
        if (TRUE == empty($this->_expression)) {
            $operator = NULL;
        }

        $exp = new \stdClass();
        $exp->expression     = $expression;
        $exp->operator       = $operator;
        $this->_expression[] = $exp;
        return $this;
    }

    /**
     * @param QueryAbstract $query
     * @return LogicAbstract
     * */
    public function setQuery (QueryAbstract $query)
    {
        if (NULL == $this->_query) {
            $this->_query = $query;
        }
        return $this;
    }

    /**
     * @param string $name
     * @param string[] $arguments
     * @return LogicAbstract
     * */
    public function __call ($name, $arguments)
    {
        $expression = current($arguments);
        switch ($name) {
            case 'and':
                $this->add($expression, self::T_AND_OPERATOR);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'or':
                $this->add($expression, self::T_OR_OPERATOR);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd
        }
        return $this;
    }

    /**
     * @return string
     * */
    public function render ()
    {
        $content = NULL;
        foreach ($this->_expression as $operator) {
            $content .= $operator->operator . $operator->expression->render();
        }
        return $this::T_COMMAND . " ({$content})";
    }

    /**
     * Fábrica de LogicAbstract
     * 
     * @param QueryAbstract $query
     * @return LogicAbstract
     * */
    public static function factory (QueryAbstract $query)
    {
        $operator = get_called_class();
        $operator = new $operator();
        $operator->setQuery($query);
        return $operator;
    }
}