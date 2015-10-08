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
    br\gov\sial\core\persist\query\QueryAbstract,
    br\gov\sial\core\persist\query\database\Column,
    br\gov\sial\core\persist\query\RelationalAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist.query
 * @subpackage database
 * @name Query
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Query extends QueryAbstract
{
    /**
     * @var string
     * */
    const QUERY_MANDATORY_ALIAS = 'O alias da expressão não pode ser nulo.';

    /**
     * @var string
     * */
    const QUERY_MANDATORY_EXPRESSION = 'A expressão não pode ser nula.';

    /**
     * @var string
     * */
    const QUERY_METHOD_UNAVAILABLE = 'O método %s::%s não existe.';

    /**
     * @var Select
     * */
    protected $_select;

    /**
     * @var Where
     * */
    private $_where;

    /**
     * @var GroupBy
     * */
    private $_groupBy;

    /**
     * @var Having
     * */
    private $_having;

    /**
     * @var OrderBy
     * */
    private $_orderBy;

    /**
     * @var Limit
     * */
    private $_limit;

    /**
     * Construtor.
     *
     * O primeiro <i>param</i> define o tipo de banco de dados que sera utilizado (pgsql, mysql, sqlite, etc), o segundo
     * define a entidade (valueObject ou namespace para o mesmo) no qual sera obtido as informacoes necessarias para
     * montar a consulta (nome da entidade, apelido - se houver - e a relacao de colunas que poderao ser utilizadas.
     *
     * @see vide Query::factory
     * @param string $driver
     * @param Entity $entity
     * */
    private function __construct ($driver, Entity $entity = NULL)
    {
        parent::$_useQuotes = TRUE;
        $namespace = __NAMESPACE__ . self::NAMESPACE_SEPARATOR . $driver . self::NAMESPACE_SEPARATOR . 'Select';
        $this->_select = $namespace::factory($entity);
        $this->_entity = $entity;
    }

    /**
     * Remove a relação de colunas que serão recuperadas na pesquisa.
     *
     * <p>
     *    <b>Nota</b>: Ao executar este método será necessário registrar uma ou mais colunas na Query
     * </p>
     *
     * @return QueryAbstract
     * */
    public function cleanColumns ()
    {
        $this->_select->cleanColumns();

        return $this;
    }


    /**
     * Ao efetuar um JOIN entre as entidades 'A' e 'B' em que se deseje obter as colunas
     * da entidade A ou B use este metodo para informar qual(is) entidades sera retornadas
     *
     * @param Entity[] $entities
     * @return Query
     * */
    public function usingOnlyColumnOf (array $entites)
    {
        $this->_select->usingOnlyColumnOf($entites);

        return $this;
    }

    /**
     * Adiciona uma coluna a Query
     *
     * <p>
     *    Sendo $coluna uma string, sera verificado em Entity a existencia de uma coluna que corresponda ao string
     *    informado e se nenhuma correpondencia for encontrada uma exception do tioi IllegalArgumentException sera
     *    lancada.
     * </p>
     * <p>
     *    Sendo $column do tipo database/Column esta sera adicionado diretamente
     * </p>
     *
     * @example database::Query::addColumn
     * <code>
     * <?php
     *   # vide Query::factory
     *   $query = Query::factory(...);
     *
     *   # adiciona uma coluna a query informando o nome da mesma
     *   $query->addColumn('columnNam');
     *
     *   # adiciona uma coluna informando um objeto
     *   $column = Column::factory(...);
     *   $query->addColumn($column);
     * ?>
     * </code>
     *
     * @override
     * <ul>
     *     <li>Query::addColumn(string $column)</li>
     *     <li>Query::addColumn(Column $column)</li>
     * </ul>
     *
     * @param $column
     * @return Query
     * @throws IllegalArgumentException
     * */
    public function addColumn ($column)
    {
        if (TRUE == is_string($column)) {
            $column = Column::factory($column, $this->_entity, 'database');
        }

        $this->_select->addColumn($column);

        return $this;
    }

    /**
     * Adiciona uma expressão a Query
     *
     * @example database::Query::expression
     * <code>
     * <?php
     *    # vide Query::factory
     *    $query = Query::factory(...);
     *
     *    # adiciona a expresao na relacao de colunas
     *    $query->expression('name', 'expression definition');
     * ?>
     * </code>
     *
     * @param string $alias
     * @param string $expression
     * @return Query
     * */
    public function expression ($alias, $expression)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull(trim($alias), self::QUERY_MANDATORY_ALIAS);
        IllegalArgumentException::throwsExceptionIfParamIsNull(trim($expression), self::QUERY_MANDATORY_EXPRESSION);
        $this->addColumn(new Column(array($alias => new Expression($expression))));

        return $this;
    }

    /**
     * Intercepta a chamada aos métodos Query::and e Query::or
     *
     * @example database::Query::and
     * @example database::Query::or
     * <code>
     * <?php
     *     # vide Query::factory
     *     $query  = Query::factory($driver, $entity);
     *
     *     # aplica codicao where a pesquisa
     *     $query->where($conditional)
     *
     *     # aplica segunda condicao (AND) a pesquisa
     *           ->and($conditional)
     *
     *     # aplica terceira condicao (OR) a pesquisa
     *           ->or($conditional)
     *
     *     # obtem representacao textual da pesquisa
     *           ->render();
     * ?>
     * </code>
     *
     * @param string $method
     * @param mixed[] $arguments
     * @return Query
     * @throws IllegalArgumentException
     * */
    public function __call($method, $arguments)
    {
        $expression = current($arguments);
        $message = sprintf(self::QUERY_METHOD_UNAVAILABLE, __CLASS__, $method);
        IllegalArgumentException::throwsExceptionIfParamIsNull($expression instanceof RelationalAbstract, $message);

        $method = strtolower($method);
        switch ($method) {
            case 'and':
                $this->where($expression);
                break;

            case 'or':
                $this->where($expression, Where::T_OR_OPERATOR);
                break;
        }

        return $this;
    }

    /**
     * Cláusula WHERE
     *
     * @example database::Query::where
     * <code>
     * <?php
     *     # vide Query::factory
     *     $query = Query::factory(..);
     *
     *     # foo = 'foo'
     *     $filter = $query->foo->equals('foo');
     *
     *     # aplica condicao a query
     *     $query->where($filter);
     * ?>
     * </code>
     *
     * {@ineritdoc}
     * */
    public function where (RelationalAbstract $conditional, $operator = Where::T_AND_OPERATOR)
    {
        if (NULL == $this->_where) {
            # a Query e' requerida para permitir o encadeamento de filtro
            # ex.: $query->where()->having()..->otherPossibleFilter();
            $this->_where = Where::factory($this);

            # registra WHERE no SELECT
            $this->_select->where($this->_where);
        }

        # adiciona o filtro ao WHERE
        $this->_where->add($conditional, $operator);

        # retorna regerencia de WHERE para o objeto que invocou o metodo
        return $this;
    }

    /**
     * @example database::Query::join
     * <code>
     * <?php
     *
     *     # vide Query::factory
     *     $query = Query::factory(namespace\Foo);
     *
     *     # vide Column::factory
     *     $joinConditional = Column::factory('bar', new Entity(namespace\bar), 'database');
     *     $joinConditional = $joinConditional->equals( $query->column('bar') )
     *
     *     # aplica o join
     *     $query->join($bar, $joinConditional)
     *
     *     # aplica condicao a query
     *           ->where(...);
     * ?>
     * </code>
     *
     * {@ineritdoc}
     * */
    public function join (Entity $entity, RelationalAbstract $conditional)
    {
        $this->_select->join(InnerJoin::factory($entity, $conditional));

        return $this;
    }

    /**
     * @example database::Query::leftJoin
     * <code>
     * <?php
     *
     *     # vide Query::factory
     *     $query = Query::factory(namespace\Foo);
     *
     *     # vide Column::factory
     *     $joinConditional = Column::factory('bar', new Entity(namespace\bar), 'database');
     *     $joinConditional->equals( $query->column('bar') )
     *
     *     # aplica o join
     *     $query->leftJoin($bar, $joinConditional);
     * ?>
     * </code>
     *
     * {@ineritdoc}
     * */
    public function leftJoin (Entity $entity, RelationalAbstract $conditional)
    {
        $this->_select->join(LeftJoin::factory($entity, $conditional));

        return $this;
    }

    /**
     * @example database::Query::rightJoin
     * <code>
     * <?php
     *
     *     # vide Query::factory
     *     $query = Query::factory(namespace\Foo);
     *
     *     # vide Column::factory
     *     $joinConditional = Column::factory('bar', new Entity(namespace\bar), 'database');
     *     $joinConditional->equals( $query->column('bar') )
     *
     *     # aplica o join
     *     $query->rightJoin($bar, $joinConditional);
     * ?>
     * </code>
     * {@ineritdoc}
     * */
    public function rightJoin (Entity $entity, RelationalAbstract $conditional)
    {
        $this->_select->join(RightJoin::factory($entity, $conditional));

        return $this;
    }

    /**
     * @example database::Query::groupBy
     * @code
     * <?php
     *     # cria objeto de consulta
     *     $query = Query::factory(...);
     *
     *     # coluna que sera agrupada
     *     $column = $query->bar;
     *
     *     # aplica agrupamento
     *     $query->groupBy($column)
     * ?>
     * @endcode
     *
     * {@inheritdoc}
     * */
    public function groupBy ($columns)
    {
        if (NULL == $this->_groupBy) {
            $this->_groupBy = GroupBy::factory();
            $this->_select->groupBy($this->_groupBy);
        }

        $this->_groupBy->column($columns);

        return $this;
    }

    /**
     * @example database::Query::having
     * @code
     * <?php
     *     # cria objeto de consulta
     *     $query = Query::factory(...);
     *
     *     # coluna que sera agrupada
     *     $column = $query->bar;
     *
     *     # aplica condicao ao agrupamento
     *     $query->having($column->greaterTehn(10))
     * ?>
     * @endcode
     *
     * {@inheritdoc}
     * */
    public function having ($columns)
    {
        if (NULL == $this->_having) {
            $this->_having = Having::factory();
            $this->_select->having($this->_having);
        }

        $this->_having->column($columns);

        return $this;
    }

    /**
     * @example database::Query::orderBy
     * @code
     * <?php
     *     # cria objeto de consulta
     *     $query = Query::factory(...);
     *
     *     # coluna que sera agrupada
     *     $query->orderBy(array('foo' => 'desc', 'bar' => 'asc'));
     * ?>
     * @endcode
     *
     * {@inheritdoc}
     * @todo implementar suporte a ordenacao por Column Object
     * */
    public function orderBy ($columns)
    {
        if (NULL == $this->_orderBy) {
            $this->_orderBy = OrderBy::factory();
            $this->_select->orderBy($this->_orderBy);
        }

        $tmpColumns = array();
        foreach ($columns as $column => $order) {
            $column = $this->col($column);
            $column->orderBy($order);
            $tmpColumns[] = $column;
        }

        $this->_orderBy->column($tmpColumns);

        return $this;
    }

    /**
     * @example database::Query::limit
     * @code
     * <?php
     *     # cria objeto de consulta
     *     $query = Query::factory(...);
     *
     *     # coluna que sera agrupada
     *     $query->limit(10, 0);
     * ?>
     * @endcode
     *
     * {@ineritdoc}
     * */
    public function limit ($limit, $offset)
    {
        if (NULL == $this->_limit) {
            $this->_limit = LimitAbstract::factory($this->_driver);
            $this->_select->limit($this->_limit);
        }

        $this->_limit->set($limit, $offset);

        return $this;
    }

    /**
     * Representação textual da query
     *
     * @return string
     * */
    public function render ()
    {
        return $this->_select->render();
    }

    /**
     * Fábrica de objetos <i>Query</i> para banco de dados.
     *
     * @example database::Query::factory
     * @code
     * <?php
     *    # definicao de FOO (para todos os exemplos utilizando uma entidade sera usado esta definicao)
     *     Foo { $foor, $bar, $fooBar, $barFoo }
     *
     *     # define o driver que sera utilizado
     *     $driver = 'pgsql';
     *
     *     # cria entidade que sera utilizada para definicao da Query
     *     # esta entidade dara subsidios ao objeto Query na obtencao do nome/apelido da entidade bem como
     *     # informara a relacao de colunas que poderao ser utilizadas na pesquisa. a decisao de qual coluna
     *     # podera ser utilizada sera decidida com base na anotacao da mesma.
     *     $entity = new Entity('fulnamespace\Foo');
     *
     *     # cria query especifico para o banco de dados Postgres
     *     $query  = Query::factory($driver, $entity);
     *
     *     # exibe representacao textual da query
     *     echo $query->render;
     * ?>
     * @endcode
     *
     * @param string $driver
     * @param Entity $entity
     * @return Query
     * @throws IllegalArgumentException
     * */
    public static function factory ($driver, $entity = NULL)
    {
        return new self($driver, $entity);
    }
}