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
namespace br\gov\sial\core\persist\database\pgsql;
use br\gov\sial\core\lang\Date,
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\persist\Persistable,
    br\gov\sial\core\util\AnnotationCache,
    br\gov\sial\core\persist\query\Entity,
    br\gov\sial\core\persist\database\ResultSet,
    br\gov\sial\core\persist\query\QueryAbstract,
    br\gov\sial\core\persist\query\database\Query,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\database\dml\pgsql\DMLData,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\persist\database\Persist as PDatabase;

/**
 * SIAL
 *
 * Persistência em banco de dados Postgres
 *
 * @package br.gov.sial.core.persist.database
 * @subpackage pgsql
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Persist extends SIALAbstract implements Persistable
{
    /**
     * Define o tipo de persistência
     *
     * @var string
     * */
    const PERSIST_TYPE = 'database';

    /**
     * Define o tipo de persistência Leaf
     *
     * @var string
     * */
    const PERSIST_LEAF = 'pgsql';

    /**
     * @var string
     * */
    const PERSIST_FILTER_NOT_FOUND = 'Nenhum filtro foi informado para pesquisa.';

    /**
     * @var string
     * */
    const PERSIST_PRIMARY_KEY_NOT_FOUND =  'Nenhuma chave primária foi encontrada para utilizar como parâmetro de filtro';

    /**
     * @var Persist PDatabase
     * */
    private $_persist = NULL;

    /**
     * Construtor.
     *
     * @param PDatabase $persist
     * */
    public function __construct (PDatabase $persist)
    {
        $this->_persist = $persist;
    }

    /**
     * @return string
     * */
    public function adapter ()
    {
        return $this->_persist->adapter();
    }

    /**
     * @return string
     * */
    public function driver ()
    {
        return $this->_persist->driver();
    }

    public function isAttrPersistable (\stdClass $attr, $adapter)
    {
        return $this->_persist->isAttrPersistable($attr, $adapter);
    }

    /**
     * Executa consulta.
     *
     * @param string $query
     * @param string[] $params
     * @return ResultSet
     * @throws PersistException
     * */
    public function execute ($query, $params = NULL)
    {
        try {
            if ($query instanceof QueryAbstract) {
                return $this->_persist->getConnect()->prepare($query->render())->retrieve();
            }
            return $this->_persist->getConnect()->prepare($query, $params)->retrieve();

        } catch (\PDOException $pExc) {
            throw new PersistException($pExc->getMessage(), 0, $pExc);
        }
    }

    /**
     * Efetua a pesquisa de todos os registro da entidade
     *
     * @return ResultSet
     * @throws PersistException
     * */
    public function findAll ()
    {
        try {
            $entity = $this->_persist->annotation()->load()->class;
            return $this->findByParam(new  $entity, 'ALL');
        // @codeCoverageIgnoreStart
        } catch (\PDOException $pExc) {
            throw new PersistException($pExc->getMessage(), 0, $pExc);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Efetua pesquisa parametrizada.
     *
     * Se informado, o segundo paramentro $limit define o numero de resultado que será retornado. O terceiro pramentro
     * $offSet que também é opcional define o registro inicial que será contato o limite.
     *
     * @param ValueObjectAbstract $valueObject
     * @param integer $limit
     * @param integer $offSet
     * @throws PersistException
     * */
    public function findByParam (ValueObjectAbstract $valueObject, $limit = 10, $offSet = 0)
    {
        try {
            $filter      = NULL;

            # sentinela de filtro Where
            $tmpOperator = array('where', 'and');

            # entidade de manipulacao
            $entity  = $this->getEntity($valueObject);

            # query base
            # SELETCT * FROM ValueObject
            $query   = $this->getQuery($entity);

            # percorre todos os atributos (Columns) para inclui-lo como filtro se necessario
            foreach ($entity->columns() as $col => $properties) {

                /* operator */
                $filter =  $tmpOperator[(bool) $filter];

                /* get column */
                $column = $query->column($col);

                /* content value */
                $content = $this->getValue($valueObject, $properties->get, $properties->type);

                /* i can use it for filter */
                if (TRUE == empty($content->value)) {
                    // @codeCoverageIgnoreStart
                    continue;
                    // @codeCoverageIgnoreEnd
                }

                /* where|and */
                $query->$filter($column->equals($content->value));
            }

            # aplica ordenacao, se aplicavel
            $query = $this->_persist->sorter($query);

            /* limit query */
            $query->limit((integer) $limit, (integer) $offSet);

            # executa a consulta
            return $this->execute($query);

        // @codeCoverageIgnoreStart
        } catch (PersistException $pExc) {
            throw $pExc;
        } catch (\Exception $exc) {
            dump( $exc );
            /* @todo efetua logo de error na execucacao da query */
            ;

            /* throw default message */
            throw new PersistException (self::WE_HAVE_A_PROBLEM_ON_GET_DATA, 0, $exc);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Dando um ValueObject, este metodo usará os valores informados em $valueObject
     * como filtro de pesquisa. Contrariamente ao método findByParam, que usa os valores
     * como termos exatos, findPartOf utilizará os valores informados como fragmento de
     * informações, ou seja, recuperará tudo que contenha parte da informação passada.
     * Opcionalmente, dados para paginação poderão ser informados.
     *
     * @param ValueObjectAbstract $valueObject
     * @param intger $limit
     * @param integer $offSet
     * @return ValueObjectAbstract[]
     * */
    public function findPartOf (ValueObjectAbstract $valueObject, $limit = 10, $offSet = 0)
    {
        try {
            $filter      = NULL;

            # sentinela de filtro Where
            $tmpOperator = array('where', 'and');

            # entidade de manipulacao
            $entity  = $this->getEntity($valueObject);

            # query base
            # SELETCT * FROM ValueObject
            $query = $this->getQuery($entity);

            # percorre todos os atributos (Columns) para inclui-lo como filtro se necessario
            foreach ($entity->columns() as $col => $properties) {

                /* operator */
                $filter =  $tmpOperator[(bool) $filter];

                /* get column */
                $column = $query->column($col);

                /* content value */
                $content = $this->getValue($valueObject, $properties->get, $properties->type);

                /* i can use it for filter */
                if (TRUE == empty($content->value)) {
                    continue;
                }

                /* where|and */
                $query->$filter($column->ilike(sprintf('%%%s%%', $content->value)));
            }

            # aplica ordenacao, se aplicavel
            $query = $this->_persist->sorter($query);

            /* limit query */
            $query->limit((integer) $limit, (integer) $offSet);

            # executa a consulta
            return $this->execute($query);

        // @codeCoverageIgnoreStart
        } catch (PersistException $pExc) {
            throw $pExc;
        } catch (\Exception $exc) {
            /* @todo efetua logo de error na execucacao da query */
            ;

            /* throw default message */
            throw new PersistException (self::WE_HAVE_A_PROBLEM_ON_GET_DATA, 0, $exc);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Este método aplica pesquisa por chave primária.
     * Caso nenhum registro seja encontrado uma exception será lançada.
     *
     * @param integer $key
     * @return Persist
     * @throws PersistException
     * */
    public function find ($key)
    {
        try {

            $filter      = NULL;

            # has Primary key
            $hasPrimary = FALSE;

            # sentinela de filtro Where
            $tmpOperator = array('where', 'and');

            # annontation
            $annon = $this->_persist->annotation()->load();

            # entidade de manipulacao
            $entity  = $this->getEntity($annon->class);

            # query base
            # SELETCT * FROM ValueObject WHERE PrimaryKeyColumn = ?, ... ,OtherPrimaryColumn = ?
            $query = $this->getQuery($entity);

            # percorre todos os atributos (Columns) para inclui-lo como filtro se necessario
            foreach ($entity->columns() as $col => $properties) {

                /* operator */
                $filter =  $tmpOperator[(bool) $filter];

                /* get column */
                $column = $query->column($col);

                /* is a primary key */
                if (FALSE == isset($properties->primaryKey)) {
                    // @codeCoverageIgnoreStart
                    continue;
                    // @codeCoverageIgnoreEnd
                }

                /* have at least one primary key  */
                $hasPrimary = TRUE;

                /* where|and */
                $query->$filter($column->equals($key));
            }

            /* fitler failt */
            if (empty($hasPrimary)) {
                // @codeCoverageIgnoreStart
                throw new PersistException(self::PERSIST_FILTER_NOT_FOUND);
                // @codeCoverageIgnoreEnd
            }

            return $this->execute($query);

        // @codeCoverageIgnoreStart
        } catch (\Exception $exc) {
            /* @todo efetua logo de error na execucacao da query */
            ;

            /* throw default message */
            throw new PersistException (self::WE_HAVE_A_PROBLEM_ON_GET_DATA, 0, $exc);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * {@inheritdoc}
     * */
    public function save (ValueObjectAbstract $valueObject)
    {
        $dmlQuery = $this->_persist
                         ->insertStr($valueObject);

        $dmlData  = DMLData::factory()
                           ->get($valueObject);

        try {

            return $this->execute($dmlQuery, $dmlData);

        } catch (\Exception $exp) {
            throw new PersistException (self::WE_HAVE_A_PROBLEM_ON_SAVE_DATA, 0, $exp);
        }
    }

    /**
     * Altera dados no repositório
     *
     * @param ValueObjectAbstract $valueObject
     * @return Persist
     * @throws PersistException
     * */
    public function update (ValueObjectAbstract $valueObject)
    {
        $dmlQuery = $this->_persist
                         ->updateStr($valueObject);

        $dmlData  = DMLData::factory()
                           ->get($valueObject);
        try{
            return $this->execute($dmlQuery, $dmlData);
        } catch (\Exception $exp) {
            throw new PersistException (self::WE_HAVE_A_PROBLEM_ON_SAVE_DATA, 0, $exp);
        }
    }

    /**
     * Deleta dados no repositório.
     *
     * @param ValueObjectAbstract $valueObject
     * @return Persist
     * @throws PersistException
     * */
    public function delete (ValueObjectAbstract $valueObject)
    {

        $dmlQuery = $this->_persist
                         ->deleteStr($valueObject);

        $dmlData  = DMLData::factory()
                           ->getFilledAttr($valueObject);

        try{

            return $this->execute($dmlQuery, $dmlData);

        } catch (\Exception $exp) {
            throw new PersistException (self::WE_HAVE_A_PROBLEM_ON_SAVE_DATA, 0, $exp);
        }
    }

    /**
     * {@inheritdoc}
     * @see br\gov\sial\core\persist.Persistable::getQuery()
     */
    public function getQuery ($entity)
    {
        return
        QueryAbstract::factory(
            self::PERSIST_LEAF,
            $entity
        );
    }

    /**
     * {@inheritdoc}
     * @see br\gov\sial\core\persist.Persistable::getEntity()
     */
    public function getEntity ($entity, array $columns = array())
    {
        return
        new Entity(
            $entity,
            $columns,
            self::PERSIST_TYPE
        );
    }

    /**
     * Recupera o valor do valueObject baseado na annotacao para ser usado na composição do comando SQL
     *
     * @param ValueObjectAbstract $valueObject
     * @param string $accessorMethod
     * @param string $dataType
     * @return \stdClass
     * */
    public function getValue (ValueObjectAbstract $valueObject, $accessorMethod, $dataType)
    {
        $data = $this->_persist->getValue($valueObject, $accessorMethod, $dataType);

        if ('string' == $data->type) {
            $data->value = pg_escape_string($data->value);
        }

        return $data;
    }
}