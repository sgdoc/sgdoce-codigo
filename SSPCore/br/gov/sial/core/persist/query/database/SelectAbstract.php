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
    br\gov\sial\core\Renderizable,
    br\gov\sial\core\persist\query\JoinAbstract,
    br\gov\sial\core\persist\query\database\Column,
    br\gov\sial\core\persist\query\OperatorAbstract,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\persist\query\SelectAbstract as ParentSelect;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist.query
 * @subpackage database
 * @name Select
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class SelectAbstract extends ParentSelect
{
    /**
     * @var string
     * */
    const SELECTABSTRACT_COLUMN_NOT_REGISTERED = 'A coluna "%s" não está registrada.';

    /**
     * @var string
     * */
    const T_COMMAND = 'SELECT';

    /**
     * @var From
     * */
    protected $_from;

    /**
     * @var Column[]
     * */
    protected $_columns = array ();

    /**
     * @var Clause[]
     * */
    protected $_clauses = array();

    /**
     * @var Entity[]
     * */
    private $_inJoinUsingOnly;

    /**
     * Construtor.
     *
     * Se informado uma entidade, todas as colunas de Entity, referente a banco de dados, serão
     * usadas para compor a relação de colunas do Select.
     *
     * @param Entity $ntity
     * */
    public function __construct (Entity $entity = NULL)
    {
        if (NULL === $entity) {
            return;
        }

        # define a entidade trabalho
        $this->from($entity);

        # define as colunas
        if (NULL != $entity) {

            foreach ($entity->columns() as $column) {

                $col = NULL;
                try { $col = Column::factory($column->name, $entity, 'database');
                } catch (IllegalArgumentException $iae) { ; }

                if (NULL != $col) {
                    $this->_columns[$column->name] = $col;
                }
            }
        }
    }

    /**
     * Remove todas as colunas da consulta
     *
     * @return SelectAbstract
     * */
    public function cleanColumns ()
    {
        $this->_columns = array();
        return $this;
    }

    /**
     * Define a entidade onde será efetuado a pesquisa
     *
     * @param Entity $entity
     * @return SelectAbstract
     * */
    public function from (Entity $entity)
    {
        $this->_from = From::factory($entity);
        return $this;
    }

    /**
     * @param Where $where
     * @return SelectAbstract
     * */
    public function where (Where $where)
    {
        $this->_clauses['where'] = $where;
        return $this;
    }

    /**
     * @param JoinAbstract
     * @return SelectAbstract
     * */
    public function join (JoinAbstract $join)
    {
        $this->_clauses[] = $join;
        return $this;
    }

    /**
     * @param GroupBy $groupBy
     * @return SelectAbstract
     * */
    public function groupBy (GroupBy $groupBy)
    {
        $this->_clauses['groupBy'] = $groupBy;
        return $this;
    }

    /**
     * @param OrderBy $orderBy
     * @return SelectAbstract
     * */
    public function orderBy ($orderBy)
    {
        $this->_clauses['orderBy'] = $orderBy;
        return $this;
    }

    /**
     * @param having $having
     * @return SelectAbstract
     * */
    public function having ($having)
    {
        $this->_clauses['having'] = $having;
        return $this;
    }

    /**
     * @param LimitAbstract $limit
     * @return SelectAbstract
     * */
    public function limit (LimitAbstract $limit)
    {
        $this->_clauses['limit'] = $limit;
        return $this;
    }

    /**
     * Trata o valor de filtro
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     * */
    public function valueCare ($value, $type)
    {
        switch (strtolower($type)) {
            case 'int':
            case 'integer':
            case 'boolean':
            case 'float':
            case 'double':
                return $value;
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'string':
            case 'char':
            case 'date':
                default:
                return "'{$value}'";
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Adiciona coluna
     *
     * @param Column $column
     * @return SelectAbstract
     * */
    public function addColumn (Column $column)
    {
        if ($column->name() instanceof Expression) {
            $name = $column->alias();
        } else {
            $name = $column->name();
        }
        $this->_columns[$name] = $column;
        return $this;
    }

    /**
     * Adiciona várias colunas
     *
     * @param Column[] $columns
     * @return SelectAbstract
     * */
    public function addColumns (array $columns)
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
        return $this;
    }

    /**
     * Retorna TRUE se a coluna informar estiver registrada
     *
     * @param string $name
     * @return boolean
     * */
    public function hasColunm ($name)
    {
        return isset($this->_columns[$name]);
    }

    /**
     * Retorna a coluna informada
     *
     * @param string $name
     * @return Column
     * @throws IllegalArgumentException
     * */
    public function getColumn ($name)
    {
        $message = sprintf(self::SELECTABSTRACT_COLUMN_NOT_REGISTERED, $name);
        IllegalArgumentException::throwsExceptionIfParamIsNull($this->hasColunm($name), $message);
        return $this->_columns[$name];
    }

    /**
     * Ao efetuar um JOIN entre as entidades 'A' e 'B' em que se deseje obter as colunas
     * da entidade A ou B use este método para informar qual(is) entidades serão retornadas.
     *
     * @param Entity[] $entities
     * @return SelectAbstract
     * */
    public function usingOnlyColumnOf (array $entites)
    {
        # $eUnidadeOrg->qualifiedName()
        foreach ($entites as $entity) {
            $this->_inJoinUsingOnly[$entity->qualifiedName()] = $entity;
        }
        return $this;
    }

    /**
     * Representação textual do Select
     *
     *  @return string
     * */
    public function render ()
    {
        $select = self::T_COMMAND;

        $distinct = NULL;
        $columns  = NULL;

        foreach ($this->_columns as $column) {
            $columns .= ", {$column->render()}";
        }

        $clause       =  NULL;
        $columns      = substr($columns, 2);
        $from         = ($this->_from instanceof Renderizable) ? $this->_from->render() : NULL;
        foreach ($this->_clauses as $element) {

            # captura as colunas do join para adiciona-las a relacao de colunas
            if($element instanceof  JoinAbstract) {

                # se informado o limitador (inJoinUsingOnly) sera udado para definir de quais entidades
                # serao coletadas as entidades
                if (empty($this->_inJoinUsingOnly) || isset($this->_inJoinUsingOnly[$element->entity()->qualifiedName()])) {
                    $extraColumns = $element->renderColumns();

                    if ($extraColumns) {
                        $columns .= ", {$extraColumns}";
                    }
                }
            }

            $clause .= ' ' . $element->render();
        }

        # se nenhuma coluna for informada
        if (!$columns && !$from) {
            return $select;
        }

        return "{$select} {$columns} {$from} {$clause}";
    }
}