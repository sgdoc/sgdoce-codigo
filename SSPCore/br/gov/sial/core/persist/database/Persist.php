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
namespace br\gov\sial\core\persist\database;
use br\gov\sial\core\lang\Date,
    br\gov\sial\core\persist\PersistConfig,
    br\gov\sial\core\persist\database\Connect,
    br\gov\sial\core\persist\query\QueryAbstract,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\database\dml\DMLAbstract,
    br\gov\sial\core\persist\Persist as ParentPersist,
    br\gov\sial\core\persist\exception\PersistException;

/**
 * SIAL
 *
 * Persistência em banco de dados
 *
 * @package br.gov.sial.core.persist
 * @subpackage database
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Persist extends ParentPersist
{
    /**
     * Define o tipo de persistência
     *
     * @var string
     * */
    const PERSIST_TYPE = 'database';

    /**
     * @var string
     * */
    const PERSIST_UNSUPPORTED_OPERATION = 'A operação informada "%s" não é suportada';

    /**
     * @var string
     * */
    const PERSIST_UNREGISTERED_ENTITY = 'Não existe registro de entidade para o valueObject informado';

    /**
     * @var stirng
     * */
    const PERSIST_ATTR_REQUIRED = 'O attr %s é requerido, mas o valor informado é NULL';

    /**
     * Cada UC possui uma especializacao desta classe, uma vez que UC_Persist nao tem/deve/precisa
     * conhecer qual banco de dados sera usado para persistir os dados fica a cargo dessa classe
     * delegar a sua classe filha (leaf|driver) a tarefa solicitada. Em resumo, a UC_persist
     * herda desta classe (Self) e solicita a execucao de uma operacao qualquer, como por exemplo,
     * save, e esta classe delega a execucao para leaf.
     *
     * @var Persist
     * */
    protected $_leaf;

    /**
     * Construtor.
     *
     * @param PersistConfig $config
     * @throws PersistException
     * */
    public function __construct (PersistConfig $config = NULL)
    {
        parent::__construct($config);
        $leafNamespace = sprintf('%1$s%2$s%3$s%2$sPersist', __NAMESPACE__, self::NAMESPACE_SEPARATOR, $config->get('driver'));
        $this->_leaf   = new $leafNamespace($this);
    }

    /**
     * Retorna TRUE se o attr informado for avaliado como requerido. Attr sera avaliado como requerido se
     * attr->nullable armazenar (false) ou sera avaliado como falso para qualquer outro valor, incluido
     * a omissao de sua definicao.
     *
     * @param stdClass $attrAnnon
     * @return boolean
     * */
    public function isRequired (\stdClass $attrAnnon)
    {
        if (!isset($attrAnnon->nullable)) {
            return FALSE;
        }
        return FALSE == $attrAnnon->nullable ||  'false' == strtolower($attrAnnon->nullable);
    }

    /**
     * Executa consulta.
     *
     * Nota: Este metodo DEVE ser evitado, uma vez que o mesmo fora incluido como forma de contornar uma limitacao
     * do SIAL em nao ter uma camada de persistencia com implementacao propria, mas que em versao futura tera e este
     * metodo sera removido.
     *
     * @param string $query
     * @param \stdClass $params
     * @return ResultSet
     * @throws PersistException
     * */
    public function execute ($query, $params = NULL)
    {
        return $this->_leaf->execute ($query, $params);
    }

    /**
     * Recupera registro por chave primária.
     *
     * <b>Nota</b>: Este metodo suporta pesquisa apenas com chave simples, ou seja, nao é suportado
     * chave composta por mais de um campo. Se necessário, este metodo deverá ser especializado
     *
     * @param integer $key
     * @return ResultSet
     * @throws PersistException
     * */
    public function find ($key)
    {
        return $this->_leaf->find($key);
    }

    /**
     * Recupera todos os registros da entidade.
     *
     * @return ResultSet
     * @throws PersistException
     * */
    public function findAll ()
    {
        return $this->_leaf->findAll();
    }

    /**
     * Efetua pesquisa parametrizada.
     *
     * Se informado, o segundo paramentro $limit define o numero de resultado que será retornado. O terceiro pramentro
     * $offSet que também é opcional define o registro inicial que sera contato o limite
     *
     * @param ValueObjectAbstract $valueObject
     * @param integer $limit
     * @param integer $offSet
     * @return ResultSet
     * @throws PersistException
     * */
    public function findByParam (ValueObjectAbstract $valueObject, $limit = 10, $offSet = 0)
    {
        return $this->_leaf->findByParam($valueObject, $limit, $offSet);
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
        return $this->_leaf->findPartOf($valueObject, $limit, $offSet);
    }

    /**
     * Persiste dados no repositório.
     *
     * @param ValueObjectAbstract $valueObject
     * @return Persist
     * @throws PersistException
     * */
    public function save (ValueObjectAbstract $valueObject)
    {
        $primaryKey = $this->_leaf->save($valueObject)->fetch();
        $primaryKey = (array) $this->_nameAttrToSial($primaryKey);
        self::_injectResultOnValueObject($valueObject, $this->findByParam($valueObject->loadData($primaryKey)));
        $this->log($valueObject, 'I' /* Insert */);
        return $this;
    }

    /**
     * Altera dados no repositório
     *
     * @param ValueObjectAbstract $valueObject
     * @return Persist
     * */
    public function update (ValueObjectAbstract $valueObject)
    {
        $this->_leaf->update($valueObject);
        $this->log($valueObject, 'U' /* Update */);
        self::_injectResultOnValueObject($valueObject, $this->findByParam($valueObject));
        return $this;
    }

    /**
     * Deleta dados no repositório
     *
     * @param ValueObjectAbstract $valueObject
     * @return Persist
     * */
    public function delete (ValueObjectAbstract $valueObject)
    {
        $this->_leaf->delete($valueObject);
        $this->log($valueObject, 'D' /* Delete */);
        self::_injectResultOnValueObject($valueObject, $this->findByParam($valueObject));
        return $this;
    }

    /**
     * {@inheritdoc}
     * */
    protected function _connect (PersistConfig $config)
    {
        return Connect::factory($config);
    }

    /**
     * Injeta os valores recuperados do banco no ValueObject.
     *
     * @param ValueObjectAbstract $valueObject
     * @param ResultSet $resultSet
     * */
    protected static function _injectResultOnValueObject (ValueObjectAbstract $valueObject, ResultSet $resultSet)
    {
        $adapter   = self::PERSIST_TYPE;
        $annon     = $valueObject->annotation()->load();
        $tmpRow    = $tmpResult = NULL;
        while (TRUE){
            $tmpRow = $resultSet->fetch();
            if (NULL == $tmpRow) {
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd
            }
            $tmpResult = $tmpRow;
        }

        foreach ($annon->attrs as $attr) {
            if (isset($attr->set) && isset($attr->$adapter)) {
                $setter = $attr->set;
                $fName = $attr->$adapter;
                $valueObject->$setter($tmpResult ? parent::getIfDefined($tmpResult, $fName) : NULL);
            }
        }
    }

    /**
     * Recupera o valor do valueObject baseado na annotacao para ser usado na composição do comando SQL
     *
     * @param ValueObjectAbstract $valueObject
     * @param string $accessorMethod
     * @param string $dataType
     * @return \stdClass
     * */
    public static function getValue (ValueObjectAbstract $valueObject, $accessorMethod, $dataType)
    {
        $data        = new \stdClass;
        $data->value =
        $data->type  = NULL;

        $data->type  = strtolower($dataType);

        $data->value = $valueObject->$accessorMethod();

        # ajusta apenas o tipo de dados e NAO seu VALOR
        if ('date' == $data->type) {
            $params->type = 'string';
        }

        /* verifica se o data é do tipo date */
        if ($data->value instanceof Date) {

            $data->type = 'string';

            $data->value = $data->value->output();
        }

        /* verifica se o data é do tipo ValueObject */
        if ($data->value instanceof ValueObjectAbstract) {

            $data = self::getValue(
                $data->value, $accessorMethod, $dataType
            );
        }

        return $data;
    }

    /**
     * Aplica ordenação a consulta.
     *
     * @param Query $query
     * @return Query
     * */
    public function sorter ($query)
    {
        if (!sizeof($this->_orderByList)) {
            return $query;
        }

        # determina o tipo de sorter (object Query ou string)
        $sorter  = '_sorterQuery' . (($query instanceof QueryAbstract) ? 'Object' : 'String');
        return  $this->$sorter($query);
    }

    /**
     * Aplica ordenação em objetos Query.
     *
     * @param Query $query
     * @return Query
     * */
    private function _sorterQueryObject ($query)
    {
        foreach ($this->_orderByList as $colName => $order) {
            $query->orderBy(array($colName => $order));
        }
        return $query;
    }

    /**
     * Aplica ordenação em query do tipo string
     *
     * @param string $query
     * @return string
     * */
    private function _sorterQueryString ($query)
    {
        $annon = $this->annotation();
        $queryOrder = '';
        foreach ($this->_orderByList as $field => $order) {
            $field = $annon->getAttr($field, $this::PERSIST_TYPE);
            if (trim($field)) {
                $queryOrder .= ", {$field} {$order}";
            }
        }
        if (trim($queryOrder)) {
            $query .= ' ORDER BY' . substr($queryOrder, 1);
        }
        return $query;
    }

    /**
     * Efetua log da operação.
     *
     * @param ValueObjectAbstract $valueObject
     * @param string $operation
     * */
    // @codeCoverageIgnoreStart
    public function log (ValueObjectAbstract $valueObject, $operation)
    {
        # checa se a camada de persistencia esta sendo logada
        if (self::$persistLoggerInstance instanceof \br\gov\sial\core\persist\PersistLogAbstract) {
            try {
                self::$persistLoggerInstance->setPersist($this);
                self::$persistLoggerInstance->save($valueObject, $operation);
            } catch (PersistException $pExc) {
                # @todo definir qual acao sera realizada para o caso de ocorrer erro no log
                dump($pExc);
            }
        }
    }
    // @codeCoverageIgnoreEnd

    /**
     * Recupera o nome da entidade que será persistida.
     *
     * @param \stdClass $annon
     * @return string
     * */
    public function getEntityName (\stdClass $annon)
    {
        # monta o nome da entidade, caso o drvier do banco nao deh suprote a namespace
        # especialize este a classe na pasta de driver especifica
        PersistException::throwsExceptionIfParamIsNull(isset($annon->entity), self::PERSIST_UNREGISTERED_ENTITY);

        $entity = $annon->entity;
        if (isset($annon->schema) && trim($annon->schema)) {
            $entity = "{$annon->schema}.{$annon->entity}";
        }
        return $entity;
    }

    /**
     * Retorna o limite a ser usado na consulta.
     *
     * @deprecated este metodo sera removido na versao 2.0 do SIAL, substituido por Query::limit
     * @param string|integer
     * @return string
     * */
    public function queryLimit ($limit)
    {
        $isString = FALSE;

        if ('string' === gettype($limit)) {
            $isString = (boolean) $limit = strtoupper($limit);
        }

        /* se for uma string diferente de ALL sera convertido para zero */
        if (TRUE === $isString && 'ALL' != $limit) {
            $limit = 0;
        }

        if (FALSE === $isString && 0 > (integer) $limit || NULL == $limit) {
            $limit = 0;
        }

        return $limit;
    }


    /**
     * Retorna todos os atributos do valueObject que podem ser utilizados na camada de persistência, definida por
     * 'self::PERSIST_TYPE'. Opcionalmente, o tipo da operacao podera ser informada (insert, update, delete). Nota:
     * Por padrao os atributos selecionados serao validos para pesquisa.
     *
     * @deprecated See DMLAbstract::persistAttr
     * @param ValueObjectAbstract $valueObject
     * @param string $operType
     * @return \stdClass
     * @throws PersistException
     * */
    public function persistAttr (ValueObjectAbstract $valueObject, $operType = NULL)
    {
        return
        DMLAbstract::factory($this->_leaf)
                   ->persistAttr($valueObject, $operType);
    }

    /**
     * Recupera o objecto Query
     *
     * @param Entity
     * @return Query
     * */
    public function getQuery ($entity)
    {
        return $this->_leaf->getQuery($entity);
    }

    /**
     * Cria entidade para manipulação no objeto de consulta (Query)
     *
     * <ul>
     *     <li>Entity::__construct(string $namespace, string[] $column)</li>
     *     <li>Entity::__construct(ValueObject $valueObject, string[] $column)</li>
     *     <li>Entity::__construct(array(string $alias => string $namespace), strin[] $column)</li>
     *     <li>Entity::__construct(array(string $alias => ValueObject $valueObject), string[] $column)</li>
     * </ul>
     *
     * @param mixed $entity
     * @param string[] $columns
     * @return Entity
     * */
    public function getEntity ($entity, array $columns = array())
    {
        return $this->_leaf
                    ->getEntity($entity, $columns);
    }

    public function insertStr (ValueObjectAbstract $valueObject)
    {
        return
        DMLAbstract::factory($this->_leaf)
                   ->save($valueObject);
    }

    public function updateStr (ValueObjectAbstract $valueObject)
    {
        return
        DMLAbstract::factory($this->_leaf)
                   ->update($valueObject);
    }


    public function deleteStr (ValueObjectAbstract $valueObject)
    {
        return
        DMLAbstract::factory($this->_leaf)
                   ->delete($valueObject);
    }

    private static function _nameAttrToSial (\stdClass $param)
    {
        $data = new \stdClass;
        foreach ($param as $key => $value) {
            $newKey = lcfirst(implode('', array_map('ucfirst', preg_split('/_/', $key))));
            $data->$newKey = $value;
        }
        return $data;
    }
}