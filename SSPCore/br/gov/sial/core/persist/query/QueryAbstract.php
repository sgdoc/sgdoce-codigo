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
    br\gov\sial\core\persist\query\Expression,
    br\gov\sial\core\persist\DSLinkerReferenceable,
    br\gov\sial\core\persist\query\RelationalAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * Fábrica de objeto de consulta.
 *
 * @package br.gov.sial.core.persist
 * @subpackage query
 * @name QueryAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class QueryAbstract extends SIALAbstract implements Renderizable, DSLinkerReferenceable
{
    /**
     * @var string
     * */
    const QUERYABSTRACT_MANDATORY_REGISTER_ENTITY = 'Nenhuma entidade registrada.';

    /**
     * @var string
     * */
    const QUERYABSTRACT_UNSUPPORTED_TYPE = 'Tipo "%s" não suportado.';

    /**
     * @var string
     * */
    const QUERYABSTRACT_NAMESPACE_PLACEHOLDER = 'br\gov\sial\core\persist\query\%s\Query';

    /**
     * Relação das fábricas de objetos de consultas implementadas.
     *
     * @var string[]
     * */
    private static $dicQuery = array(
        'pgsql'  => 'database',
        /*
        'mysql'  => 'database\mysql',
        'sqlite' => 'database\sqlite',
        'text'   => 'file\text',
        'csv'    => 'file\csv',
        */
    );

    /**
     * @var boolean
     * */
    public static $_useQuotes = FALSE;

    /**
     * Armazena o driver em uso
     *
     * @var string
     * */
    protected $_driver;

    /**
     * Referência da entidade.
     *
     * @var Entity
     * */
    protected $_entity;

    /**
     * Retorna TRUE se existir uma entidade registrada.
     *
     * @return boolean
     * */
    public function hasEntity ()
    {
        return (boolean) $this->_entity;
    }

    /**
     * Retorna Entity ou lançaa uma IllegalArgumentException se nao houver uma entidade registrada.
     *
     * Antes de chamar este método certifique-se, por meio de <i>self::hasEntity</i>, de haver uma Entity
     * registrada.
     *
     * @return Entity
     * @throws IllegalArgumentException
     * */
    public function entity ()
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull($this->_entity, self::QUERYABSTRACT_MANDATORY_REGISTER_ENTITY);
        return $this->_entity;
    }

    /**
     * @example QueryAbstract::->$column
     * @code
     * <?php
     *     # definicao de FOO
     *     Foo { $foor, $bar, $fooBar, $barFoo }
     *
     *     # cria objeto de consulta
     *     $query = QueryAbstract::factory($type, new fullnamespace\valeuObject\Foo);
     *
     *     # esta chamada e' valida
     *     $fooBar = $query->fooBar;
     *
     *     # esta chamada lanca uma exception
     *     $fake = $query->fake;
     * * ?>
     * @endcode
     *
     * @override
     * @final
     * @param string $column
     * @return mixed
     * @throws IllegalArgumentException
     * */
    public function __get ($column)
    {
        return $this->column($column);
    }

    /**
     * Retorna a coluna informada.
     *
     * @example QueryAbstract::column
     * @code
     * <?php
     *     # definicao de FOO
     *     Foo { $foor, $bar, $fooBar, $barFoo }
     *
     *     # cria objeto de consulta
     *     $query = QueryAbstract::factory($type, new fullnamespace\valeuObject\Foo);
     *
     *     # esta chamada e' valida
     *     $fooBar = $query->column('fooBar');
     *
     *     # esta chamada lanca uma exception
     *     $fake = $query->column('fake');
     * * ?>
     * @endcode
     *
     * @param string $column
     * @return Column
     * @throws IllegalArgumentException
     * */
    public function column ($column)
    {
        return $this->_select->getColumn($column);
    }

    /**
     * Alias para o método self::column
     *
     * {@ineritdoc}
     * @return Column
     * */
    public function col ($column)
    {
        return $this->column($column);
    }

    /**
     * Aplica quotes texto informado.
     *
     * @param string $text
     * @return string
     * */
    public function quotesIf ($text)
    {
        if (TRUE == self::$_useQuotes) {
            return sprintf('"%s"', $text);
        }
        return $text;
    }

    /**
     * Adiciona coluna.
     *
     * <p>
     *     <b>Nota</b>: O nome da coluna deve estar presente em entity.
     * </p>
     *
     * @override
     * @param string|Column $column
     * @return QueryAbstract
     * */
    // @codeCoverageIgnoreStart
    public abstract function addColumn ($column);
    // @codeCoverageIgnoreEnd

    /**
     * @param string $alias
     * @param string $expression
     * @return Column
     * */
    // @codeCoverageIgnoreStart
    public abstract function expression ($alias, $expression);
    // @codeCoverageIgnoreEnd

    /**
     * aplica condicao a pesquisa
     *
     * @param RelationalAbstract $conditional
     * @return Where
     * */
    // @codeCoverageIgnoreStart
    public abstract function where (RelationalAbstract $conditional);
    // @codeCoverageIgnoreEnd

    /**
     * apelido para innerJoin
     *
     * @param Entity $entity
     * @param RelationalAbstract $conditional
     * @return QueryAbstract
     * */
    // @codeCoverageIgnoreStart
    public abstract function join (Entity $entity, RelationalAbstract $conditional);
    // @codeCoverageIgnoreEnd

    /**
     * @param Entity $entity
     * @param RelationalAbstract $conditional
     * @return QueryAbstract
     * */
    // @codeCoverageIgnoreStart
    public abstract function leftJoin (Entity $entity, RelationalAbstract $conditional);
    // @codeCoverageIgnoreEnd

    /**
     * @param Entity $entity
     * @param RelationalAbstract $conditional
     * @return QueryAbstract
     * */
    // @codeCoverageIgnoreStart
    public abstract function rightJoin (Entity $entity, RelationalAbstract $conditional);
    // @codeCoverageIgnoreEnd

    /**
     * Agrupa resultado da pesquisa
     *
     * @param Column[] $columns
     * @return GroupBy
     * @throws IllegalArgumentException
     * */
    // @codeCoverageIgnoreStart
    public abstract function groupBy ($columns);
    // @codeCoverageIgnoreEnd

    /**
     * Aplica filtro ao agrupamento.
     *
     * @param Column[] $columns
     * */
    // @codeCoverageIgnoreStart
    public abstract function having ($columns);
    // @codeCoverageIgnoreEnd

    /**
     * Aplica ordenação a pesquisa.
     *
     * @example  QueryAbstract::orderBy
     * @code
     * <?php
     *     # definicao de FOO
     *     Foo { $foo, $bar, $fooBar, $barFoo }
     *
     *     # cria objeto de consulta
     *     $query = QueryAbstract::factory($type, new fullnamespace\valeuObject\Foo);
     *
     *     # aplica ordenacao
     *     $query->orderBy(array('foo' => 'asc'));
     * ?>
     * @endcode
     *
     * @param Column[] $columns
     * @return QueryAbstract
     * */
    // @codeCoverageIgnoreStart
    public abstract function orderBy ($columns);
    // @codeCoverageIgnoreEnd

    /**
     * @param integer $limit
     * @param integer $offset
     * */
    // @codeCoverageIgnoreStart
    public abstract function limit ($limit, $offset);
    // @codeCoverageIgnoreEnd

    /**
     * Fábrica de objetos Query
     * <p>
     *     O primeiro <i>param</i> define o tipo de <i>Query</i> que sera criada, a disponibilidade dos tipos e'
     *     limitada a relacao registrada em <b>QueryAbstract::$dicQuery</b>. O segundo <i>param</i>, opcionamente,
     *     define a entidade que sera manipulada.
     * </p>
     * <p>
     *      Ao criar uma <i>Query</i> informando a entidade todos os seus atributos definido em anotacao para a camada
     *      em uso serao automaticamente utilizados como colunas de manipulacao.
     * </p>
     * <p>
     *      O segundo argumento pode variar entre uma string que deve corresponder ao namespace namespace do
     *      <i>ValueObject</i> que representa a entidade na aplicacao ou um objeto deste <i>ValueObect</i>.
     * </p>
     * <p>
     *    <b>note</b>: Embora o segundo <i>param</i> possa ser omitido na criação de Query, esteja conciente que
     *    basicamente sem esta definicao, apenas o uso de <i>Expresion</i> e <i>Function</i> serao permitidas.
     * </p>
     *
     * @example QueryAbstract::factory
     * @code
     * <?php
     *     # cria objeto de consulta para banco postgres
     *     $query = Query::factory('pgsql');
     *
     *     # cria objeto de consulta para banco mysql
     *     $query = Query::factory('mysql');
     *
     *     # cria objeto de consulta para banco sqlite
     *     $query = Query::factory('sqlite');
     *
     *     # cria objeto de consulta para banco de dados postgres para a entidade 's_foo'
     *     $query = Query::factory('pgsql', 'fullNamespace\FooValueObject');
     *
     *     # cria objeto de consulta para banco de dados postgres na entidade 's_foo'
     *     # dando-lhe um apelido de 'foo'
     *     $query = Query::factory('pgsql', array('foo' => 'fullNamespace\FooValueObject'));
     *
     *     # cria objeto de consulta para arquivo texto
     *     $query = Query::factory('text');
     *
     *     # cria objeto de consulta para arquivo csv
     *     $query = Query::factory('csv');
     *
     *     # uma vez criado o objeto de consulta todos os comandos disponivel
     *     # para pesquisa selecionada estarao disponiveis
     *     $query->addColumn('bar');
     * ?>
     * @endcode
     *
     * @override
     * <ul>
     *     <li>Query::factory(string $type, string $entity)</li>
     *     <li>Query::factory(string $type, Entity $entity)</li>
     *     <li>Query::factory(string $type, array(string $alias => string $entity))</li>
     *     <li>Query::factory(string $type, array(string $alias => Entity $entity))</li>
     * </ul>
     *
     * @param string $type
     * @param mixed $entity
     * @return QueryAbstract
     * @throws IllegalArgumentException
     * */
    public static function factory ($type, $entity = NULL)
    {
        $message = sprintf(self::QUERYABSTRACT_UNSUPPORTED_TYPE, $type);
        IllegalArgumentException::throwsExceptionIfParamIsNull(isset(self::$dicQuery[$type]), $message);
        $namespace = sprintf(self::QUERYABSTRACT_NAMESPACE_PLACEHOLDER, self::$dicQuery[$type]);

        if (!($entity instanceof Entity)) {
            $entity = new Entity($entity);
        }

        $query = $namespace::factory($type, $entity);
        $query->_driver = $type;
        return $query;
    }

    /**
     * Representação textual.
     *
     * @return string
     * */
    public function __toString ()
    {
        return $this->render();
    }
}