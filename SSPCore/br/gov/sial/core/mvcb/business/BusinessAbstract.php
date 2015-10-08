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
namespace br\gov\sial\core\mvcb\business;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\SIALApplication,
    br\gov\sial\core\mvcb\model\ModelAbstract,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\mvcb\controller\ControllerAbstract,
    br\gov\sial\core\mvcb\business\exception\BusinessException;

/**
 * SIAL
 *
 * Superclasse da camada de negócio.
 *
 * @package br.gov.sial.core.mvcb
 * @subpackage business
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class BusinessAbstract extends SIALAbstract
{
   /**
    * Mensagem do SIAL.
    *
     * @var string
     * */
    const MUST_CREATE_MODEL = 'A operação solicitada requer a criação do model';

    /**
     * Mensagem do SIAL.
     *
     * @var string
     * */
    const INVALID_PARAMETER_FOR_BUSINESS = 'O parâmetro informado não válido para construcao de um Business';

    /**
     * Mensagem do SIAL.
     *
     * @var string
     * */
    const MISSING_PERSISTENCE = 'É obrigatório informar a persistencia a ser utilizada';

    /**
     * Referência para a camada Model
     *
     * @var ModelAbstract
     * */
    protected $_model = NULL;

    /**
     * Referência para a classe SIALApplication
     *
     * @var SIALApplication
     * */
    protected $_SIALApplication;

    /**
     * Registra referência do SIALApplication.
     * @example BusinessAbstract::applicationRegister
     * @code
     * <?php
     *     ...
     *     $business->applicationRegister($app);
     *     ...
     * ?>
     * @endcode
     * @param SIALApplication $SIALApplication
     * @return BusinessAbstract
     * */
    public function applicationRegister (SIALApplication $SIALApplication)
    {
        $this->_SIALApplication = $SIALApplication;
        return $this;
    }

    /**
     * Commit da transação.
     * @example BusinessAbstract::commit
     * @code
     * <?php
     *     ...
     *     $business->commit();
     *     ...
     * ?>
     * @endcode
     * @return BusinessAbstract
     * */
    public function commit ()
    {
        $this->getModel()
             ->getPersist()
             ->getConnect()
             ->commit();
        return $this;
    }

    /**
     * Inicializa transação.
     * @example BusinessAbstract::transaction
     * @code
     * <?php
     *     ...
     *     $business->transaction();
     *     ...
     * ?>
     * @endcode
     * @return BusinessAbstract
     * */
    public function transaction ()
    {
        $this->getModel()
             ->getPersist()
             ->getConnect()
             ->transaction();
        return $this;
    }

    /**
     * Anula as operações realizadas em uma transação.
     * @example BusinessAbstract::rollback
     * @code
     * <?php
     *     ...
     *     $business->rollback();
     *     ...
     * ?>
     * @endcode
     * @return BusinessAbstract
     * */
    public function rollback ()
    {
        $this->getModel()
             ->getPersist()
             ->getConnect()
             ->rollback();
        return $this;
    }

    /**
     * Busca por chave primária.
     * @example BusinessAbstract::find
     * @code
     * <?php
     *     ...
     *     $business->find(3);
     *     ...
     * ?>
     * @endcode
     * @param integer $key
     * @return ValueObjectAbstract
     * */
    public function find ($key)
    {
        return $this->getModel()
                    ->find((integer) $key)
                    ->getValueObject();
    }

    /**
     * Recupera todos os registros referênciados a uma entidade.
     * @example BusinessAbstract::findAll
     * @code
     * <?php
     *     ...
     *     $business->findAll();
     *
     *     //ou
     *
     *     $business->findAll(array('campo' => 'ASC'));
     *     ...
     * ?>
     * @endcode
     * @param string[] $order
     * @return ValueObjectAbstract[]
     * @throws ModelException
     * */
    public function findAll (array $order = NULL)
    {
        $model = $this->getModel();

        if (NULL != $order) {
            $method = 'orderBy' . ucfirst(key($order));
            $model->$method(current($order));
        }

        return $model->findAll()
                     ->getAllValueObject();
    }

    /**
     * Busca parametrizada.
     * @example BusinessAbstract::findByParam
     * @code
     * <?php
     *     ...
     *     $valueObject = FoobarValueObject::factory();
     *     $valueObject->setNoFoobar('foobar');
     *     $business->findByParam($valueObject, 10, 2);
     *     ...
     * ?>
     * @endcode
     * @param ValueObjectAbstract $valueObject
     * @param integer $limit
     * @param integer $offset
     * @return ValueObjectAbstract[]
     * */
    public function findByParam (ValueObjectAbstract $valueObject, $limit = 10, $offSet = 0)
    {
        return $this->getModel()
                    ->findByParam($valueObject, $limit, $offSet)
                    ->getAllValueObject();
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
        return $this->getModel()
                    ->findPartOf($valueObject, $limit, $offSet)
                    ->getAllValueObject();
    }

    /**
     * Fabrica de Model.
     *
     * Opcionalmente pode ser informado a entrada de configuracao de persistencia
     * util quando deseja trocar de conexao no mei do UC
     * @example BusinessAbstract::getModel
     * @code
     * <?php
     *     ...
     *     $business->getModel('default');
     *     ...
     * ?>
     * @endcode
     * @return ModelAbstract
     * @throws BusinessException
     * */
    public function getModel ($dsName = NULL)
    {
        if (NULL == $this->_model) {
            $namespace    = $this->getClassName();
            $namespace    = $this->erReplace(array('business'  => 'model', 'Business$' => 'Model'), $namespace);

            # lanca exception se a classe ainda nao tiver sido criada
            BusinessException::throwsExceptionIfParamIsNull(ModelAbstract::exists($namespace), self::MUST_CREATE_MODEL . " '{$namespace}'");

            # delega a fabrica de ModelAbstract que crie um model
            $this->_model = $namespace::factory($namespace, $dsName);

            # a persist depende diretamente do valueObject para montagem das consultas basicas e operacoes
            # simples de escrita: update, delete. assim, se o model existir seu ValueObject correspondente
            # tambem devera existir;
            $this->registerAnnotation($namespace);
        }

        return $this->_model;
    }

    /**
     * Fábrica de Model por persistência.
     * @example BusinessAbstract::getModelPersist
     * @code
     * <?php
     *     ...
     *     $business->getModelPersist($persist);
     * ?>
     * @endcode
     * @param string $persist
     * @return ModelAbstract
     * @throws IllegalArgumentException
     * */
    public function getModelPersist ($persist)
    {
        $tmpPersist = NULL;

        if ('string' == gettype($persist)) {
            $tmpPersist = $persist;
        }

        $className = $this->getModel($tmpPersist)->getClassName();

        IllegalArgumentException::throwsExceptionIfParamIsNull($tmpPersist, self::MISSING_PERSISTENCE);

        $model = ModelAbstract::factory($className, $tmpPersist);

        $model->registerAnnotation(
            parent::erReplace(array('mvcb\\model' => 'valueObject', 'Model$' => 'ValueObject'), $className)
        );

        return $model;
    }

    /**
     * Fábrica de Business.
     * @example BusinessAbstract::getModel
     * @code
     * <?php
     *     ...
     *     //constroi um objeto business do modulo que invoca o método
     *     BusinessAbstract::factory();
     *
     *     //ou
     *
     *     BusinessAbstract::factory('\foobar\business\FooBusiness', 'libcorp');
     *     ...
     * ?>
     * @endcode
     * @param string | ControllerAbstract $namespace
     * @param string $persist
     * @return BusinessAbstract
     * @throws IllegalArgumentException
     * */
    public static function factory ($namespace = NULL, $dsName = NULL)
    {
        $business = NULL;
        $type = gettype($namespace);

        if ('string' == $type) {
            $business = new $namespace;
        }

        if (NULL === $namespace) {
            $business = get_called_class();
        }

        if ('object' == $type && $namespace instanceof ControllerAbstract) {
            $business = self::_factoryByController($namespace);
        }

        IllegalArgumentException::throwsExceptionIfParamIsNull($business, self::INVALID_PARAMETER_FOR_BUSINESS);

        $business = new $business;

        if (NULL != $dsName) {
            $business->getModel($dsName);
        }

        return $business;
    }

    /**
     * Fábrica de Controller.
     *
     * @param Controller $namespace
     * @return BusinessAbstract
     * @throws BusinessException
     * */
    private static function _factoryByController (ControllerAbstract $namespace)
    {
        $namespace = (string) $namespace->getClassName();

        return parent::erReplace(array('controller'  => 'business', 'Controller$' => 'Business'), $namespace);
    }

    /**
     * Registra o Annotation para o ValueObject.
     * @example BusinessAbstract::registerAnnotation
     * @code
     * <?php
     *     ...
     *     $valueObject = FoobarValueObject::factory();
     *     $business->registerAnnotation($valueObject->annotation());
     * ?>
     * @endcode
     * @param string $namespace
     * @return ModelAbstract
     */
    public function registerAnnotation ($namespace)
    {
        $namespace = parent::erReplace(array('mvcb\\business'   => 'valueObject',
                                             'mvcb\\controller' => 'valueObject',
                                             'mvcb\\model'      => 'valueObject',
                                             'Model$'           => 'ValueObject',
                                             'Business$'        => 'ValueObject',
                                             'Controller$'      => 'ValueObject',
                                           ), $namespace);

        $this->getModel()->registerAnnotation($namespace);
    }
}