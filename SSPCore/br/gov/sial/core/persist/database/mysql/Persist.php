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
namespace br\gov\sial\core\persist\database\mysql;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\persist\Persistable,
    br\gov\sial\core\persist\database\ResultSet,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\persist\database\Persist as PDatabase
    ;

/**
 * SIAL
 *
 * Persistência em banco de dados MySQL.
 *
 * @package br.gov.sial.core.persist.database
 * @subpackage mysql
 * @name Persist
 * @author Fabio Lima <fabioolima@gmail.com>
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
    const PERSIST_LEAF = 'mysql';

    /**
     * @var string
     * */
    const PERSIT_PRIMARYKEY_NOT_FOUND = 'Nenhuma chave primaria foi encontrada para utilizar como parâmetro de filtro';

    /**
     * @var br\gov\sial\core\persist\database\Persist PDatabase
     * */
    private $_persist = NULL;

    /**
     * Construtor.
     *
     * @param \br\gov\sial\core\persist\persistConfig $config
     * */
    public function __construct (PDatabase $persist)
    {
        $this->_persist = $persist;
    }

    /**
     * Executa consulta.
     *
     * @param string $query
     * @param string[] $params
     * @return \br\gov\sial\core\persist\ResultSet
     * @throws \br\gov\sial\core\persist\exception\PersistException
     * */
    public function execute ($query, $params = NULL)
    {
        try {
            return $this->_persist->getConnect()->prepare($query, $params)->retrieve();
        } catch (\PDOException $pdoExc) {
            throw new PersistException($pdoExc->getMessage(), 0);
        }
    }

    /**
     * Efetua a pesquisa de todos os registro da entidade.
     *
     * @return \br\gov\sial\core\persist\ResultSet
     * @throws \br\gov\sial\core\persist\exception\PersistException
     * */
    public function findAll ()
    {
        try {
            $entity = $this->_persist->annotation()->load()->class;
            return $this->findByParam(new $entity);
        } catch (\PDOException $pExc) {
            // @codeCoverageIgnoreStart
            throw new PersistException($pExc->getMessage(), 0);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Efetua pesquisa por parametrizada.
     *
     * Se informado, o segundo paramentro $limit define o numero de resultado que será retornado. O terceiro pramentro
     * $offSet que também é opcional define o registro inicial que sera contato o limite
     *
     * @param ValueObjectAbstract $valueObject
     * @param integer $limit
     * @param integer $offSet
     * @return ValueObject[]
     * @throws PersistException
     * */
    public function findByParam (ValueObjectAbstract $valueObject, $limit = 10, $offSet = 0)
    {
        try {
            # recupera anotacao do valuObject
            $tmpOperator = array('WHERE', 'AND');
            $annon       = $valueObject->annotation()->load();
            $fieldData   =
            $fieldList   = array();
            $filter      = NULL;

            # campos que devem ser utilizados para persistencia de database
            $attrs = $this->_persist->persistAttr($valueObject, 'select');

            foreach ($attrs as $attr) {
                $fieldList[] = $attr->database;
                $fieldData[$attr->database] = $this->_persist->getValue($valueObject, $attr->get, $attr->type);

                if (NULL === $fieldData[$attr->database]->value) {
                    unset($fieldData[$attr->database]);
                    // @codeCoverageIgnoreStart
                    continue;
                    // @codeCoverageIgnoreEnd
                }

                # insere o campo na listagem de filtro
                $filter .= sprintf(' %1$s %2$s = :%2$s', $tmpOperator[(bool) $filter], $attr->database);
            }

            # monta instrucao SQL de pesquisa
            // @codeCoverageIgnoreStart
            $queryString = sprintf('SELECT %s FROM %s%s'
            // @codeCoverageIgnoreEnd
                                  , implode(', ', $fieldList)
                                  , $annon->entity
                                  , $filter);

            # se houver, aplica ordenacao
            $queryString = $this->_persist->sorter($queryString);

            # aplica limitacao da consulta
            if ((($limit != NULL) && (0 < $limit)) && (($offSet != NULL) && (0 < $offSet))) {
                $queryString .= sprintf(' LIMIT %d, %d', (integer) $offSet, (integer) $limit);
            } elseif (($limit != NULL) && (0 < $limit)) {
                $queryString .= sprintf(' LIMIT %d', (integer) $limit);
            } elseif (0 > $limit) {
                $queryString .= sprintf(' LIMIT %d', 0);
            }

            # executa a consulta
            return $this->_persist->getConnect()->prepare($queryString, $fieldData)->retrieve();
        } catch (\PDOException $pdoExc) {
            throw new PersistException($pdoExc->getMessage(), 0);
        }
    }

    /**
     * @param integer $key
     * */
    public function find ($key)
    {
        try {
            # possiveis filtros utilizando na consulta por chave-primaria
            $tmpOperator = array('WHERE', 'AND');

            # filtro da pesquisa
            $filter = NULL;

            # parametros utiliados como valores para fisltros
            $params = array();

            # recupera todos os atributos
            $annon = $this->_persist->annotation()->load();

            # coloca no filtro todas as chaves primarias encontradas
            foreach ($annon->attrs as $attr) {
                $isPrimaryKey = 'TRUE' == strtoupper(self::getIfDefined($attr, 'primaryKey')) ? TRUE : FALSE;

                if (FALSE == $this->_persist->isAttrPersistable($attr, self::PERSIST_TYPE)) {
                    // @codeCoverageIgnoreStart
                    continue;
                    // @codeCoverageIgnoreEnd
                }

                # relacao dos campos que compora o select
                $fieldList[] = $attr->database;

                # se nao for pk passa para o proximo
                if (FALSE == $isPrimaryKey) {
                    // @codeCoverageIgnoreStart
                    continue;
                    // @codeCoverageIgnoreEnd
                }

                $filter .= sprintf(' %1$s %2$s = :%2$s',$tmpOperator[(bool) $filter], $attr->database);
                $params[$attr->database] = new \stdClass();
                $params[$attr->database]->type =  $attr->type;
                $params[$attr->database]->value = $key;
            }

            # monta instrucao SQL de pesquisa
            // @codeCoverageIgnoreStart
            $querySelect = sprintf('SELECT %s FROM %s%s'
            // @codeCoverageIgnoreEnd
                                  , implode(', ', $fieldList)
                                  , $annon->entity
                                  , $filter);

            return $this->_persist->getConnect()->prepare($querySelect, $params)->retrieve();

        } catch (\PDOException $pdoExcp) {
            throw new PersistException($pdoExcp->getMessage(), 0);
        }
    }

    public function save (ValueObjectAbstract $valueObject)
    {
        $attrs  = $this->_persist->persistAttr($valueObject, 'insert');
        $data   =
        $fields = array();
        $annon  = $valueObject->annotation()->load();

        foreach ($attrs as $attr) {
            $isPrimaryKey = 'TRUE'  == strtoupper(self::getIfDefined($attr, 'primaryKey')) ? TRUE : FALSE;
            $isAutoIncrement = 'TRUE'  == strtoupper(self::getIfDefined($attr, 'autoIncrement')) ? TRUE : FALSE;
            $data[$attr->database] = $this->_persist->getValue($valueObject, $attr->get, $attr->type);

            if ('boolean' != $attr->type) {
                $defaultValue = str_replace('NULL', '', self::getIfDefined($attr, 'defaultValue'));
                $data[$attr->database]->value = $this->toggle($data[$attr->database]->value, $defaultValue);
            }

            if (($isPrimaryKey == TRUE) && ($isAutoIncrement == TRUE)) {
                   unset($data[$attr->database]);
                   // @codeCoverageIgnoreStart
                   continue;
                   // @codeCoverageIgnoreEnd
            }
            $fields[] = $attr->database;
        }

        # monta o comando de insercao
        // @codeCoverageIgnoreStart
        $querySave = sprintf('INSERT INTO %s (%s) VALUES (:%s)'
        // @codeCoverageIgnoreEnd
                            , $annon->entity
                            , implode(', ', $fields)
                            , implode(', :', $fields));

        return $this->execute($querySave, $data);
    }

    /**
     * altera dados no repositorio
     *
     * @param ValueObjectAbstract $valueObject
     * @return br\gov\sial\core\persist\Persist
     * */
    public function update (ValueObjectAbstract $valueObject)
    {
        $attrs        = $this->_persist->persistAttr($valueObject, 'update');
        $annon        = $valueObject->annotation()->load();
        $fieldData    =
        $fieldList    = array();
        $tmpOperator  = array('WHERE', 'AND');
        $filter       = NULL;

        foreach ($attrs as $attr) {
            $isPrimaryKey = 'TRUE' == strtoupper(self::getIfDefined($attr, 'primaryKey')) ? TRUE : FALSE;
            $fieldData[$attr->database] = $this->_persist->getValue($valueObject, $attr->get, $attr->type);

            if (TRUE == $isPrimaryKey) {
                # insere o campo na listagem de filtro
                $filter .= sprintf(' %1$s %2$s = :%2$s', $tmpOperator[(bool) $filter], $attr->database);
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }
            $fieldList[] = sprintf('%1$s = :%1$s', $attr->database);
        }

        PersistException::throwsExceptionIfParamIsNull(NULL !== $filter, self::PERSIT_PRIMARYKEY_NOT_FOUND);

        // @codeCoverageIgnoreStart
        $queryUpdate = sprintf('UPDATE %s SET %s%s'
        // @codeCoverageIgnoreEnd
                              , $annon->entity
                              , implode(', ', $fieldList)
                              , $filter
                              );

        return $this->execute($queryUpdate, $fieldData);
    }

    /**
     * deleta dados no repositorio
     *
     * @param ValueObjectAbstract $valueObject
     * @return br\gov\sial\core\persist\Persist
     * */
    public function delete (ValueObjectAbstract $valueObject)
    {
        $attrs        = $this->_persist->persistAttr($valueObject, 'delete');
        $annon        = $valueObject->annotation()->load();
        $fieldData    = array();
        $tmpOperator  = array('WHERE', 'AND');
        $filter       = NULL;

        foreach ($attrs as $attr) {
            $fieldData[$attr->database] = $this->_persist->getValue($valueObject, $attr->get, $attr->type);
            if (TRUE == empty($fieldData[$attr->database]->value)) {
                unset($fieldData[$attr->database]);
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }
            $filter .= sprintf(' %1$s %2$s = :%2$s', $tmpOperator[(bool) $filter], $attr->database);
        }

        PersistException::throwsExceptionIfParamIsNull(NULL !== $filter, self::PERSIT_PRIMARYKEY_NOT_FOUND);

        // @codeCoverageIgnoreStart
        $queryDelete = sprintf('DELETE FROM %s%s'
        // @codeCoverageIgnoreStart
                              , $annon->entity
                              , $filter
                              );

        return $this->execute($queryDelete, $fieldData);
    }

    /**
     * {@inheritdoc}
     * @see br\gov\sial\core\persist.Persistable::getQuery()
     * @todo implementar Query tambem para MySQL
     */
    public function getQuery ($entity)
    {
        ;
    }

    /**
     * {@inheritdoc}
     * @see br\gov\sial\core\persist.Persistable::getEntity()
     * @todo implementar Query tambem para MySQL
     */
    public function getEntity ($entity, array $columns = array())
    {
        ;
    }
}