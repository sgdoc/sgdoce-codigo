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
    br\gov\sial\core\persist\query\Entity,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage query
 * @name Column
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Column extends SIALAbstract implements Renderizable
{
    /**
     * @var string
     * */
    const COLUMN_NOT_FOUND = 'A coluna %s não existe.';

    /**
     * @var string
     * */
    const COLUMN_ALIAS_MUST_BE_STRING = 'O apelido da coluna deve ser uma string.';

    /**
     * @var string
     * */
    const COLUMN_MANDATORY_REPOSITORY = 'Informando a entidade é obrigatório informar o tipo de repositório.';

    /**
     * @var string
     * */
    const COLUMN_UNAVAILABLE = 'A coluna informada não está disponível para o tipo de repostório informado.';

    /**
     * @var string
     * */
    protected $_entity;

    /**
     * @var string
     * */
    protected $_name;

    /**
     * @var string
     * */
    protected $_alias = NULL;

    /**
     * @var string
     * */
    protected $_dataType;

    /**
     * Construtor.
     *
     * @override
     * <ul>
     *     <li>Column::__construct(string $column, string $entity)</li>
     *     <li>Column::__construct(Expression $expression, string $entity)</li>
     *     <li>Column::__construct(array(string $alias => $name), string $entity)</li>
     *     <li>Column::__construct(array(string $alias => Expression $expression), string $entity)</li>
     * </ul>
     *
     * @param string $column
     * @param string $entity
     * @param string $dataType
     * */
    public function __construct ($column, $entity = NULL, $dataType = 'string')
    {
        if (TRUE == is_array($column)) {
            $this->setAlias(key($column));
            $column = current($column);
        }
        $this->_entity   = $entity;
        $this->_name     = $column;
        $this->_dataType = strtolower($dataType);
    }

    /**
     * Recupera o nome de Column.
     *
     * @return string
     * */
    public function name ()
    {
        return $this->_name;
    }

    /**
     * Recupera o tipo de dados de Column.
     *
     * @return string
     * */
    public function getDataType ()
    {
        return $this->_dataType;
    }

    /**
     * Retorna o nome da coluna precedido do nome, ou alias, da entidade.
     *
     * @return string
     * */
    public function qualifiedName ()
    {
        return current(preg_split('/\s*AS\s*/', $this->render()));
    }

    /**
     * Recupera o apelido - Alias - de Column.
     *
     * @return string
     * */
    public function alias ()
    {
        return $this->_alias;
    }

    /**
     * Retorna o nome da entidade que será usado para definir o nome da coluna.
     *
     * @return string
     * */
    public function entity ()
    {
         return $this->_entity ?: NULL;
    }

    /**
     * Define um apelido para coluna.
     *
     * @param string
     * @return Column
     * @throws IllegalArgumentException
     * */
    public function setAlias ($alias)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull(TRUE == is_string($alias), self::COLUMN_ALIAS_MUST_BE_STRING);

        if ('$self' == $alias) {
            $alias = $this->name();
        }

        $this->_alias = trim($alias);
        return $this;
    }

    /**
     * Representação textual da coluna.
     *
     * @return string
     * */
    public function render ()
    {
        $column = QueryAbstract::quotesIf($this->_name);
        $entity = $this->entity();

        if ($entity instanceof Entity) {
            $entity = $this->toggle($entity->alias(), $entity->name());
        }

        $entity = $entity ? $entity . '.' : NULL;

        # esta verifica eh necessario por causa das Expression
        if ($this->_name instanceof Renderizable) {
            $column = $this->_name->render();
            $entity = NULL;
        }

        if ($this->_alias) {
            $column .= " AS {$this->_alias}";
        }

        return "{$entity}{$column}";
    }

    /**
     * @return string
     * */
    public function __toString ()
    {
        return $this->render();
    }

    /**
     * Retorna TRUE se a coluna deve ser ignorada para o respositório informado.
     *
     * @param string $column
     * @param Entity $entity
     * @param string $dataSource
     * @return boolean
     * */
    public function ignoreColumn ($column, $entity, $dataSource)
    {
        $columns = $entity->columns();
        if (TRUE == isset($columns->$column->ignoreSaveIn) &&
            FALSE !== strpos($dataSource, $columns->$column->ignoreSaveIn)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Verifica se a coluna existe.
     *
     * @param string $column
     * @param Entity $entity
     * @param string $dataSource
     * @return boolean
     * */
    public function columnExists ($column, $entity, $dataSource)
    {
        # relacao de colunas disponiveis na entidade
        $columns = $entity->columns();

        # se a coluna nao estiver disponivel ou na existir uma coluna para o dataSource informado, retorna TRUE;
        if (FALSE == isset($columns->$column) || FALSE == isset($columns->$column->$dataSource)) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * <p>
     *      Esta fabrica tem comportamente levemente diferente do construtor desta classe isso porque esta fabrica criar
     *      colunas para diferentes tipos de repositorios de dados (database, ldap, webservice), ja o construtor e' herdado
     *      por estes tipos
     * </p>
     * <p>
     *      o primeiro <b><i>param</i></b> define o nome da coluna que sera criada, o segundo define a entidade que a
     *      coluna sera associada, o terceiro define a fonte de dados (database, ldap, webservice).
     *  </p>
     *
     * @param mixed $column
     * @param Entity $entity
     * @param string $type
     * @return Column
     * @throws IllegalArgumentException
     * */
    public static function factory ($column, Entity $entity, $type)
    {
        # verifica se a coluna existe  na entidade
        $message = sprintf(self::COLUMN_NOT_FOUND, $column);
        IllegalArgumentException::throwsExceptionIfParamIsNull(self::columnExists($column, $entity, $type), $message);

        # verifica se a coluna deve ou nao ser ignorada (annontation ignoreSaveIn)
        if (self::ignoreColumn($column, $entity, $type)) {
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }

        $namespace = get_called_class();
        $namespace = sprintf('%1$s%2$s%3$s%2$sColumn', __NAMESPACE__, self::NAMESPACE_SEPARATOR, $type);
        IllegalArgumentException::throwsExceptionIfParamIsNull((NULL !== $type), self::COLUMN_MANDATORY_REPOSITORY);

        # column pode ser definido como array ou string, uma vez definido como array
        # o formato valido/aceito e' array(string $alias => $name)
        #
        # nota: o epelido refere-se ao nome da propriedade no valueObject
        # o nome, refere-se ao nome no repositorio de dados
        $columnName = TRUE == is_array($column) ? current($column) : $column;

        # colunas disoniveis
        $avaliableCol = $entity->columns();

        # verifica se a coluna informada existe na entidade
        IllegalArgumentException::throwsExceptionIfParamIsNull(isset($avaliableCol->$columnName->$type), self::COLUMN_UNAVAILABLE);

        # recupera o nome da coluna de acordo o tipo de repositorio
        $column = $avaliableCol->$columnName->$type;

        # recupera o tipo de dado
        $dataType = $avaliableCol->$columnName->type;
        $entity   = self::toggle($entity->alias(), $entity->name());
        return new $namespace($column, $entity, $dataType);
    }
}