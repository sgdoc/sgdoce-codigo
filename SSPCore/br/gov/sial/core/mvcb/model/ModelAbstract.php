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
namespace br\gov\sial\core\mvcb\model;
use br\gov\sial\core\SIALAbstract;
use br\gov\sial\core\persist\Persist;
use br\gov\sial\core\persist\PersistConfig;
use br\gov\sial\core\valueObject\DataViewObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract;
use br\gov\sial\core\persist\exception\PersistException;
use br\gov\sial\core\exception\IllegalArgumentException;
use br\gov\sial\core\mvcb\model\exception\ModelException;
use br\gov\sial\core\persist\database\dml\exception\DMLException;

/**
 * SIAL
 *
 * Superclasse da camada de modelo
 *
 * @package br.gov.sial.core.mvcb
 * @subpackage model
 * @name ModelAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class ModelAbstract extends SIALAbstract
{
    /**
     * @var string
     * */
    const NON_EXISTENT_ATTRIBUTE = "O atributo para ordenação 'orderBy (%s)' não existe.";

    /**
     * @var string
     * */
    const UNAVAILABLE_METHOD = "Este método não esta disponível para '%s'.";

    /**
     * @var string
     * */
    const MISSING_NAMESPACE = "Namespace é obrigatório para criacao do Model.";

    /**
     * Referência da camada de persistência.
     *
     * @access protected
     * @var Persist
     * */
    protected $_persist;

    /**
     * Resultado de pesquisa realizada contra o repositório.
     *
     * @access protected
     * @var ResultSet
     * */
    protected $_resultSet = NULL;

    /**
     * Auxilia na montagem do getAllValueObject.
     *
     * @var bool
     * */
    private $_isValueObjectNull = FALSE;

    /**
     * Construtor.
     *
     * @access public
     * @param string $dsName
     * @throws IllegalArgumentException
     * */
    public function __construct ($dsName)
    {
        $this->_persist = $this->_factory($dsName);
    }

    /**
     * Intercepta chamada a método inexistente e redireciona para funcionalidades pre-definidas.
     *
     * @param string $name
     * @param string[] $arguments
     * @return ModelAbstract
     * */
    public function __call ($name, array $arguments = array())
    {
        # registra ordenacao de pesquisa
        if ('orderByList' == $name) {
            $this->_registerOrderByList($arguments);

        } elseif ('orderBy' == substr($name, 0, 7)) {
            $this->_registerOrderBy(substr($name, 7), current($arguments));

        } else {
            parent::__call($name, $arguments);
        }

        return $this;
    }

    /**
     * Reseta definição de valueObject nulo.
     * @example ModelAbstract::resetValueObjectNullDefinition
     * @code
     * <?php
     *     ...
     *     $model->resetValueObjectNullDefinition();
     *     ...
     * ?>
     * @endcode
     * @return ModelAbstract
     * */
    public function resetValueObjectNullDefinition ()
    {
        $this->_isValueObjectNull = FALSE;
        return $this;
    }

    /**
     * Registra campos de ordenação.
     *
     * @param string $name
     * @param string $order
     * @throws IllegalArgumentException
     * @return ModelAbstract
     * */
    private function _registerOrderBy ($name, $order = 'ASC')
    {
        # converte apenas a primeira letra do nome para minusculo
        $name[0] = strtolower($name [0]);
        $message = sprintf(self::NON_EXISTENT_ATTRIBUTE, $name);

        IllegalArgumentException::throwsExceptionIfParamIsNull(
            $this->_persist->annotation()->hasAttr($name), $message
        );

        $this->_persist->orderBy($name, $order);

        return $this;
    }

    /**
     * Registra uma lista de ordenadores.
     * O formato esperado é array('field' => 'asc|desc')
     *
     * @param string[] $orders
     * @throws IllegalArgumentException
     * @return br\gov\sial\core\mvcb\model\ModelAbstract
     * */
    private function _registerOrderByList (array $orders = array())
    {
        $orders = current($orders);
        foreach ( $orders as $field => $order ) {

            # verifica se o nome do atributo e' um numero. Uma das possibilidades disto ocorrer sera quando apenas o
            # nomedo atributo for informado sem informar a ordenacao. neste caso, faz a correcao (trocando as chaves
            # e informando o ordenador padrao)
            if (is_numeric($field)) {
                $field = $order;
                $order = 'ASC';
            }
            $this->_registerOrderBy($field, $order);
        }
        return $this;
    }

    /**
     * Cria objeto persist do mesmo tipo da Model.
     *
     * @name _getPersist
     * @param string $dsName
     * @return PersistConfig
     * */
    private function _factory ($dsName)
    {
        return Persist::factory($this, PersistConfig::factory($dsName));
    }

    /**
     * Retorna TRUE se o model informado existir.
     * @example ModelAbstract::exists
     * @code
     * <?php
     *     ...
     *     ModelAbstract::exists('\foobar\Model');
     *     ...
     * ?>
     * @endcode
     * @param string namespace
     * @return boolean
     * */
    public static function exists ($namespace)
    {
        return is_file(self::realpathFromNamespace($namespace) . '.php');
    }

    /**
     * Retorna referência de persistência.
     * Nota: Esta chamada só é válida a partir de BusinessAbstract
     *
     * @example ModelAbstract::getPersist
     * @code
     * <?php
     *     ...
     *     $model->getPersist();
     *     ...
     * ?>
     * @endcode
     * @return Persist
     * @throws ModelException
     * */
    public function getPersist ()
    {
        return $this->_persist;
    }

    /**
     * Apaga dados do repositório.
     * @example ModelAbstract::delete
     * @code
     * <?php
     *     ...
     *     $model->delete($valueObject);
     *     ...
     * ?>
     * @endcode
     * @access public
     * @name delete
     * @param ValueObjectAbstract $valueObject
     * @return ModelAbstract
     * @throws ModelException
     * */
    public function delete (ValueObjectAbstract $valueObject)
    {
        try {
            $this->_persist->delete($valueObject);

     // @codeCoverageIgnoreStart
        } catch ( \br\gov\sial\core\persist\exception\PersistException $pExc ) {
            ; # realiza log de error ocorrido na persistencia

            throw new ModelException($pExc->getMessage(), 0, $pExc);
        } catch ( \PDOException $pExc ) {
            throw new ModelException($pExc->getMessage(), 0);
        } catch (DMLException $dExc) {
            throw new ModelException($dExc->getMessage(), 0);
        }

     // @codeCoverageIgnoreEnd
    }

    /**
     * Salva dados no repositório.
     * @example ModelAbstract::save
     * @code
     * <?php
     *     ...
     *     $valueObject = FoobarValueObject::factory();
     *     $model->save($valueObject);
     *     ...
     * ?>
     * @endcode
     * @name save
     * @access public
     * @param ValueObject $valueObject
     * @return ModelAbstract
     * @throws ModelException
     * */
    public function save (ValueObjectAbstract $valueObject)
    {
        try {
            $this->_persist->save($valueObject);

        // @codeCoverageIgnoreStart
        } catch ( PersistException $pExc ) {
            ; # realiza log de error ocorrido na persistencia
            throw new ModelException($pExc->getMessage(), 0, $pExc);
        } catch (DMLException $dExc) {
            throw new ModelException($dExc->getMessage(), 0, $dExc);
        } catch ( \PDOException $pdoe ) {
            ; # efetua log de error em operacao com repositorio
            throw new ModelException($pdoe->getMessage(), 0);
        }

     // @codeCoverageIgnoreEnd
    }

    /**
     * Atualiza dados no repositório.
     * @example ModelAbstract::update
     * @code
     * <?php
     *     ...
     *     $valueObject = $model->find(3);
     *     $valueObject->setNome('asdasdas');
     *     $model->update($valueObject);
     *     ...
     * ?>
     * @endcode
     * @access public
     * @name update
     * @param ValueObject $valueObject
     * @return ModelAbstract
     * @throws ModelException
     * */
    public function update (ValueObjectAbstract $valueObject)
    {
        try {
            $this->_persist->update($valueObject);

     // @codeCoverageIgnoreStart
        } catch ( PersistException $pExc ) {
            ; # realiza log de error ocorrido na persistencia


            throw new ModelException($pExc->getMessage(), 0, $pExc);
        } catch ( \PDOException $pdoe ) {
            ; # efetua log de error em operacao com repositorio
            ;
            throw new ModelException($pdoe->getMessage(), 0);
        }

     // @codeCoverageIgnoreEnd
    }

    /**
     * Recupera por chave primária.
     * @example ModelAbstract::find
     * @code
     * <?php
     *     ...
     *     $valueObject = $model->find(3);
     *     ...
     * ?>
     * @endcode
     * @param integer $idx
     * @return ModelAbstract
     * */
    public function find ($idx)
    {
        $this->_resultSet = $this->_persist->find($idx);
        return $this;
    }

    /**
     * Recupera todos os dados do repositório.
     * @example ModelAbstract::findAll
     * @code
     * <?php
     *     ...
     *     $valueObject = $model->findAll();
     *     ...
     * ?>
     * @endcode
     * @return ModelAbstract
     * */
    public function findAll ()
    {
        $this->_resultSet = $this->_persist->findAll();
        return $this;
    }

    /**
     * Efetua pesquisa com base nos parâmentros preenchidos no ValueObject informado.
     * @example ModelAbstract::findByParam
     * @code
     * <?php
     *     ...
     *     $model->findByParam($valueObject, 10, 0);
     *     ...
     * ?>
     * @endcode
     * @param ValueObjectAbstract
     * @param integer $limit
     * @param integer $offSet
     * @return ValueObjectAbstract[]
     * */
    public function findByParam (ValueObjectAbstract $valueObject, $limit = 10, $offSet = 0)
    {
        $this->_resultSet = $this->_persist->findByParam($valueObject, $limit, $offSet);
        return $this;
    }

    /**
     * <?php
     *     ...
     *     $model->findPartOf($valueObject, 10, 0);
     *     ...
     * ?>
     * @endcode
     * @param ValueObjectAbstract
     * @param integer $limit
     * @param integer $offSet
     * @return ValueObjectAbstract[]
     * */
    public function findPartOf (ValueObjectAbstract $valueObject, $limit = 10, $offSet = 0)
    {
        $this->_resultSet = $this->_persist->findPartOf($valueObject, $limit, $offSet);
        return $this;
    }

    /**
     * Retorna um ArrayList de ValueObject.
     * @example ModelAbstract::getAllValueObject
     * @code
     * <?php
     *     ...
     *     $model->getAllValueObject();
     *     ...
     * ?>
     * @endcode
     * @access public
     * @name getAllValueObject
     * @return ValueObjectAbstract[]
     * */
    public function getAllValueObject ()
    {
        $valueObjects = new \ArrayObject();

        while ( TRUE ) {
            $valueObject = $this->getValueObject();

            if ($this->_isValueObjectNull) {
                break;
            }

            $valueObjects->append($valueObject);
        }

        $this->resetValueObjectNullDefinition();

        return $valueObjects;
    }

    /**
     * Retorna um ValueObject para cada linha recuperada do repositório e
     * NULL quando não houver mais registros para retorno.
     * @example ModelAbstract::getValueObject
     * @code
     * <?php
     *     ...
     *     $model->getValueObject();
     *     ...
     * ?>
     * @endcode
     * @return ValueObjectAbstract
     * */
    public function getValueObject ()
    {
        $tmpResult = $this->_resultSet->fetch();

        if (NULL == $tmpResult) {
            $this->_isValueObjectNull = TRUE;
        }

        return ValueObjectAbstract::factory($this, $tmpResult);
    }

    /**
     * Retorna o resultaddo da pesquisa em DataViewObject.
     * @example ModelAbstract::getDataViewObject
     * @code
     * <?php
     *     ...
     *     $model->getDataViewObject();
     *     ...
     * ?>
     * @endcode
     * @return DataViewObject
     * */
    public function getDataViewObject ()
    {
        $tmpResult = $this->_resultSet->fetch();

        if (NULL == $tmpResult) {
            $this->_isValueObjectNull = TRUE;
        }

        return DataViewObject::factory( !$tmpResult ? new \stdClass : (object) $tmpResult );
    }

    /**
     * Retorna um ArrayList de DataViewObject
     * @example ModelAbstract::getAllDataViewObject
     * @code
     * <?php
     *     ...
     *     $model->getAllDataViewObject();
     *     ...
     * ?>
     * @endcode
     * @access public
     * @name getAllDataViewObject
     * @return DataViewObject[]
     * */
    public function getAllDataViewObject ()
    {
        $result = new \ArrayObject();

        while (TRUE) {
            $dvo = $this->getDataViewObject();

            if ($this->_isValueObjectNull) {
                break;
            }

            $result->append($dvo);
        }

        $this->resetValueObjectNullDefinition();

        return $result;
    }

    /**
     * Registra o Annotation para o ValueObject.
     * @example ModelAbstract::registerAnnotation
     * @code
     * <?php
     *     ...
     *     $model->registerAnnotation($valueObject->annotation());
     *     ...
     * ?>
     * @endcode
     * @param string $namespace
     * @return ModelAbstract
     */
    public function registerAnnotation ($namespace)
    {
        $this->_persist->registerAnnotation($namespace);
        return $this;
    }

    /**
     * Fábrica de Model.
     *
     * Opcionalmente pode ser informado qual configuração de repositório será utilizada.
     * @example ModelAbstract::factory
     * @code
     * <?php
     *     ...
     *     ModelAbstract::factory();
     *
     *     //ou
     *
     *     ModelAbstract::factory('/br/gov/algumaModel', ''libcorp);
     *     ...
     * ?>
     * @endcode
     * @static
     * @name factory
     * @access public
     * @param string $namespace
     * @param string $dsName
     * @return ModelAbstract
     * @throws IllegalArgumentException
     * @todo implementar singleton
     * */
    public static function factory ($namespace = NULL, $dsName = NULL)
    {
        if (NULL === $namespace) {
            $namespace = get_called_class();
        }

        try {
            IllegalArgumentException::throwsExceptionIfParamIsNull($namespace, self::MISSING_NAMESPACE);
            return new $namespace($dsName);

        } catch ( PersistException $pExc ) {
            throw new ModelException($pExc->getMessage(), $pExc->getCode());
        }
    }

    /**
     * Registra o usuário logado.
     * @example ModelAbstract::registerUserId
     * @code
     * <?php
     *     ...
     *     $model->registerUserId($id);
     *     ...
     * ?>
     * @endcode
     * @param integer $userId
     * @return ModelAbstract;
     * @deprecated A implementação do log da camada de persistencia será de cada sistema
     */
    public function registerUserId ($userId)
    {
        $this->_persist->registerUserId($userId);
        return $this;
    }
}