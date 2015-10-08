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
    br\gov\sial\core\persist\Persist,
    br\gov\sial\core\util\AnnotationCache,
    br\gov\sial\core\persist\query\database\Column,
    br\gov\sial\core\persist\DSLinkerReferenceable,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage query
 * @name Entity
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Entity extends SIALAbstract implements DSLinkerReferenceable
{
    /**
     * @var string
     * */
    const ENTITY_COLUMN_UNAVAILABLE = 'A coluna "%s" não está disponível';

    /**
     * @var string
     * */
    private $_schema;

    /**
     * @var string
     * */
    private $_name;

    /**
     * @var string
     * */
    private $_alias = NULL;

    /**
     * @var stdClass[]
     * */
    private $_columns = NULL;

    /**
     * Tipo de persistência.
     *
     * @var string
     * */
    private $_persistType = NULL;

    /**
     * Construtor.
     *
     * @override
     * <ul>
     *     <li>Entity::__construct(string $namespace, string[] $column)</li>
     *     <li>Entity::__construct(ValueObject $valueObject, string[] $column)</li>
     *     <li>Entity::__construct(array(string $alias => string $namespace), strin[] $column)</li>
     *     <li>Entity::__construct(array(string $alias => ValueObject $valueObject), string[] $column)</li>
     * </ul>
     *
     * @param mixed $entity
     * @param string[]|Column[] $columns
     * @param string $persistType
     * @throws ValueObjectException
     * */
    public function __construct ($entity, array $columns = array(), $persistType = NULL)
    {
        $type = gettype($entity);

        $this->_persistType = $persistType;

        if ('array' == $type) {
            $this->_alias = key($entity);
            $entity = current($entity);
            $type   = gettype($entity);
        }

        if ('string' == $type) {
            $this->_setup(AnnotationCache::load($entity), $columns);
        }

        if ($entity instanceof ValueObjectAbstract) {
            $this->_setup($entity->annotation()->load(), $columns);
        }
    }

    /**
     * @param string $column
     * @return Column
     * @throws IllegalArgumentException
     * */
    public function column ($column)
    {
        $message = sprintf(self::ENTITY_COLUMN_UNAVAILABLE, $column);
        IllegalArgumentException::throwsExceptionIfParamIsNull(TRUE == isset($this->_columns->$column), $message);

        # recupera/define o tipo de dados para coluna
        $dataType =  isset($this->_columns->$column->type) ? $this->_columns->$column->type : 'string';

        # o uso do namespace completo eh obrigatorio para evitar um coflito de
        # de namespace entre Entity e $this
        return new Column($column, $this, $dataType);
    }

    /**
     * Configura a entidade baseado no valueObject.
     *
     * @param ValueObjectAbstract $valueObject
     * @param string[]|Column[] $columns
     * */
    protected function _setup ($annotation, $columns)
    {
        $this->_name       = $annotation->entity;
        $this->_schema     = $annotation->schema;
        $this->_columns    = $this->_setupColumn($annotation->attrs, $columns);
    }

    /**
     * Configura as colunas de trabalho.
     * Se informado o segundo param define a relação de colunas que serão utilizadas dentro de columns.
     *
     * @param stdClass $columns
     * @param string[] $scope
     * @return stdClass[]
     * */
    private function _setupColumn ($columns, array $scope = NULL)
    {
        $content  = new \stdClass();
        $scope    = NULL == $scope ? (array) $columns : $scope;
        $pType    = $this->_persistType;

        foreach ($scope as $key => $column) {

            # troca a chave pelo conteudo do array caso seja um array indexado
            if (is_int($key)) {
                $key = (string) $column;
            }

            if (FALSE == isset($columns->$key)) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }

            $col = $columns->$key;
            # descarta a coluna se o tipo de persistencia tiver sido
            # definida o a definicao da coluna nao tenha destinacao a
            # este tipo de persistencia
            if (NULL !== $pType && !isset($columns->$key->$pType)) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }
            $content->$key = $col;
        }
        return $content;
    }

    /**
     * Define o alias da entidade.
     *
     * @param string $alias
     * @returns Entity
     * */
    public function setAlias ($alias)
    {
        $this->_alias = $alias;
        return $this;
    }

    /**
     * Retorna o nome completo da entidade incluido apelido.
     *
     * @return string
     * */
    public function qualifiedName ()
    {
        $qualified = $this->schema();

         if ($this->_schema) {
             $qualified .= ".{$this->name()}";
         }

         if ($this->_alias) {
             $qualified .= " AS {$this->_alias}";
         }
         return  $qualified;
    }

    /**
     * @return string
     * */
    public function schema ()
    {
        return QueryAbstract::quotesIf($this->_schema);
    }

    /**
     * @return string
     * */
    public function name ()
    {
        return QueryAbstract::quotesIf($this->_name);
    }

    /**
     * Retorna o alias atribuído a entidade.
     *
     * @returns string
     * */
    public function alias ()
    {
        return $this->_alias;
    }

    /**
     * Retorna todas as colunas.
     *
     * @return stdClass[]
     * */
    public function columns ()
    {
        return $this->_columns;
    }

    /**
     * Fábrica de Entity
     * @override
     * <ul>
     *     <li>Entity::__construct(string $namespace, string[] $column)</li>
     *     <li>Entity::__construct(ValueObject $valueObject, string[] $column)</li>
     *     <li>Entity::__construct(array(string $alias => string $namespace), strin[] $column)</li>
     *     <li>Entity::__construct(array(string $alias => ValueObject $valueObject), string[] $column)</li>
     * </ul>
     *
     * @param mixed $entity
     * @param string[]|Column[] $columns
     * @param string $persistType
     * @return Entity
     * */
    public static function factory ($entity, array $columns = array(), $persistType = NULL)
    {
        return new self($entity, $columns, $persistType);
    }
}