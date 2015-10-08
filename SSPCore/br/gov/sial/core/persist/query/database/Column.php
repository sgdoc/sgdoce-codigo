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
use br\gov\sial\core\persist\query\Entity,
    br\gov\sial\core\exception\IllegalArgumentException,
    \br\gov\sial\core\persist\query\Column as ParentColumn;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist.query
 * @subpackage database
 * @name Column
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Column extends ParentColumn
{
    /**
     * @var string
     * */
    const COLUMN_EXPECTED_PARAM = 'Esperado(s) %s param(s) para este operador';

    /**
     * @var string
     * */
    const COLUMN_UNAVAILABLE_OPERATOR = 'O operador "%s" não é suportado';

    /**
     * @var integer
     * */
    const COLUMN_QNT_PARAM_BETWEEN = 2;

    /**
     * @var Function
     * */
    private $_functions;

    /**
     * Armazena a definição de ordenação, (asc, desc), para coluna
     *
     * @var string
     * */
    private $_order;

    /**
     * Interpreta as chamadas aos operadores.
     *
     * @override
     * <ul>
     *     <li>Column::equals</li>
     *     <li>Column::notEquals</li>
     *     <li>Column::greater</li>
     *     <li>Column::less</li>
     *     <li>Column::greaterOrEqualsThan</li>
     *     <li>Column::lessOrEqualsThan</li>
     *     <li>Column::in</li>
     * </ul>
     *
     * @param string $method
     * @param mixed $arguments
     * @return Column
     * @throws IllegalArgumentException
     * */
    public function __call($method, $arguments)
    {
        $method    = strtolower($method);
        $value     = current($arguments);

        switch ($method) {
            case 'in':
            case 'equals':
                $namespace = __NAMESPACE__ . self::NAMESPACE_SEPARATOR;
                $operator  = $namespace . ucfirst($method);
                $operator = new $operator($this, current($arguments));
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'notin':
                $operator = new NotIn($this, $value);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'isnull':
                $operator = new IsNull($this);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'isnotnull':
                $operator = new IsNotNull($this);
                // @codeCoverageIgnoreStart
                break;

            case 'greaterthan':
                $operator = new GreaterThan($this, $value);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'lessthan':
                $operator = new LessThan($this, $value);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'notequals':
                $operator = new NotEquals($this, $value);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'greaterthanorequals':
                $operator = new GreaterThanOrEqueals($this, $value);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'lessthanorequeals':
                $operator = new LessThanOrEqueals($this, $value);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'like':
                $operator = new Like($this, $value);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'ilike':
                $operator = new Ilike($this, $value);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'between':
                $message = sprintf(self::COLUMN_EXPECTED_PARAM, self::COLUMN_QNT_PARAM_BETWEEN);
                IllegalArgumentException::throwsExceptionIfParamIsNull(self::COLUMN_QNT_PARAM_BETWEEN == sizeof($arguments), $message);
                $operator = new Between($this, $value, next($arguments));
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            default:

               throw new IllegalArgumentException(sprintf(self::COLUMN_UNAVAILABLE_OPERATOR, $method));
               // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd
        }
        return $operator;
    }

    /**
     * Retorna a ordenação da coluna.
     *
     * @return string
     * */
    public function order ()
    {
        return $this->_order;
    }

    /**
     * Define a ordenação da coluna.
     *
     * @param string $order
     * @return Column
     * */
    public function orderBy ($order = 'ASC')
    {
        $order = strtolower($order);
        $dic = array('asc'  => 'ASC', 'desc' => 'DESC');
        if (!isset($dic[$order])) {
            $order = 'asc';
        }
        $this->_order = $dic[$order];
        return $this;
    }

    /**
     * Define a coluna distinta.
     *
     * @return Column
     * */
    public function distinct ()
    {
        $this->_functions = new Distinct($this);
        return $this;
    }

    /**
     * Calcula o valor médio da coluna
     *
     * @return Column
     * */
    public function avg ()
    {
        $this->_functions = new Avg($this);
        return $this;
    }

    /**
     * Conta os elementos da coluna
     *
     * @return Column
     * */
    public function count ()
    {
        $this->_functions = new Count($this);
        return $this;
    }

    /**
     * Soma os valores da coluna
     *
     * @return Column
     * */
    public function sum ()
    {
        $this->_functions = new Sum($this);
        return $this;
    }

    /**
     * Retorna o maior valor da coluna
     *
     * @return Column
     * */
    public function max ()
    {
        $this->_functions = new Max($this);
        return $this;
    }

    /**
     * Retornar o menor valor da coluna
     *
     * @return Column
     * */
    public function min ()
    {
        $this->_functions = new Min($this);
        return $this;
    }

    /**
     * Representação textual da coluna.
     *
     * @return string
     * */
    public function render ()
    {
        if ($this->_entity instanceof Entity) {
            $column  = $this->_name;
            $columns = $this->_entity->columns();

            if (FALSE == isset($columns->$column->database)) {
                return FALSE;
            }

            $this->_name = $columns->$column->database;
        }

        if ($this->_functions) {
            return $this->_functions->render();
        }

        return parent::render();
    }
}