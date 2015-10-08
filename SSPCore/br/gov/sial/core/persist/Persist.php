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
namespace br\gov\sial\core\persist;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\Annotation,
    br\gov\sial\core\persist\PersistConfig,
    br\gov\sial\core\mvcb\model\ModelAbstract,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage persist
 * @author J. Augusto <augustowebd@gmail.com>
 * @todo criar Persist::execute
 * */
abstract class Persist extends SIALAbstract implements Persistable
{
    /**
     * @var string
     * */
    const PERSIST_INVALID_OBJECT = 'Objeto de configuração não informado/invalido.';

    /**
     * @var string
     * */
    const PERSIST_INVLAID_PARAMETER = '"%s" não um paramentro aceito';

    /**
     * @var string
     * */
    const PERSIST_INVALID_ORDERBY_ARG = 'Operador de ordenação orderBy(%s) é invalido.';

    /**
     * @var string
     * */
    const PERSIST_NAMESPACE_OR_MODEL_REQUIRED = 'Só possível instanciar "Persist" informando seu namesace completo ou um objeto do tipo "Model" como referência.';

    /**
     * @var string
     * */
    const PERSIST_CLASS_NOT_FOUND = 'A classe de persistência "%s" não foi localizado.';

    /**
     * @var string
     * */
    const PERSIST_NOT_REGISTERED = '"Annotation" ainda não foi registrada.';

    /**
     * Tipo de persistência.
     *
     * @var string
     * */
    const PERSIST_TYPE = NULL;

    /**
     * Se definida ativa o log dos dados manipulados pela camada de persistência.
     *
     * @var PersistLogAbstract
     */
    public static $persistLoggerInstance = null;

    /**
     * Tipos aceitos de persistência.
     *
     * @var string[]
     * */
    private static $_acceptedType = array('database', 'ldap', 'webservice');

    /**
     * Referência do anotation do valueObject de trabalho.
     *
     * @var Annotation
     * */
    private $_annotation = NULL;

    /**
     * @var persistConfig
     * */
    protected $_config;

    /**
     * Lista de ordenadores da pesquisa.
     *
     * @var string[]
     * */
    protected $_orderByList = array();

    /**
     * Conexão com o repositório de dados.
     *
     * @var Connect
     * */
    protected static $_connect;

    /**
     * ID do usuario logado.
     *
     * @var integer
     * */
    protected $_userId;

    /**
     * Dados do usuario logado.
     *
     * @var \stdClass
     * */
    protected $_userData;

    /**
     * Construtor.
     * @param PersistConfig $config
     * @throws IllegalArgumentException
     * */
    public function __construct (PersistConfig $config)
    {
        $this->_config = $config;
        PersistException::throwsExceptionIfParamIsNull($config, self::PERSIST_INVALID_OBJECT);
        $this->_connect($config);
    }

    /**
     * @return string
     * */
    public function adapter ()
    {
        return $this->_config->get('adapter');
    }

    /**
     * @return string
     * */
    public function driver ()
    {
        return $this->_config->get('driver');
    }

    /**
     * Executa consulta.
     * @example Persist::execute
     * @code
     * <?php
     *     ...
     *     $persist->execute($query, $params);
     *     ...
     * ?>
     * @endcode
     * @param string $query
     * @param stdClass $params
     * @return ResultSet
     * @throws PersistException
     * */
    public abstract function execute ($query, $params = NULL);

    /**
     * Verifica se uma classe de persistência existe.
     * @example Persist::exists
     * @code
     * <?php
     *     ...
     *     Persist::exists('/br/gov/persist');
     *     ...
     * ?>
     * @endcode
     * @param string $namespace
     * @param boolean $throws
     * @return boolean
     * @throws IllegalArgumentException
     * */
    public static function exists ($namespace, $throws = FALSE)
    {
        $result = is_file(self::realpathFromNamespace($namespace) . '.php');
        IllegalArgumentException::throwsExceptionIfParamIsNull($result, sprintf(self::PERSIST_CLASS_NOT_FOUND, $namespace));

        return $result;
    }

    /**
     * @param PersistConfig $config
     * @return Connect
     * */
    protected abstract function _connect (PersistConfig $config);

    /**
     * Retorna a referência da Annotation.
     *
     * <b>Nota</b>: Se o annotation não tiver sido registrada este método lançará uma exception.
     *
     * @example Persist::annotation
     * @code
     * <?php
     *     ...
     *     $persist->annotation();
     *     ...
     * ?>
     * @endcode
     * @return \stdClass
     * @throws PersistException
     * */
    public function annotation ()
    {
        PersistException::throwsExceptionIfParamIsNull($this->_annotation, self::PERSIST_NOT_REGISTERED);

        return $this->_annotation;
    }

    /**
     * Recupera o objeto de conexão
     *
     * <b>IMPORTANTE</b>: este metodo não deve ser invocado diretamente, apenas metodos da classe
     * datababase\driver\Persist tem permissao de acessa-lo.
     * @todo criar controle de backtrace para para verifica se alguma classe indevida esta invocando este metodo
     *
     * @example Persist::getConnect
     * @code
     * <?php
     *     ...
     *     $persist->getConnect();
     *     ...
     * ?>
     * @endcode
     *
     * @return Connect
     * */
    public function getConnect ()
    {
        return $this->_connect($this->_config);
    }

    /**
     * Registra a anotação do valueObject.
     * Pode ser registrado conforme opções abaixo:
     *
     * <ul>
     *   <li><b>string</b>: namespace do valueObject</li>
     *   <li><b>ValueObject</b>: instancia do ValueObject</li>
     *   <li><b>Annotation</b>: instancia da anotação do ValueObject</li>
     * </ul>
     *
     * <b>Nota</b>: Se o namespace informado foi inválido uma IOException será lançada. <br />
     * <b>Nota</b>: Será lançada uma IllegalArgumentException para qualquer outro tipo informado
     *
     * @example Persist::registerAnnotation
     * @code
     * <?php
     *     ...
     *     $persist->registerAnnotation($valueObject->annotation());
     *     ...
     * ?>
     * @endcode
     * @param [string | ValueObjectAbstract | Annotation] $param
     * @return Persist
     * @throws IOException
     * @throws IllegalArgumentException
     * */
    public function registerAnnotation ($annotation)
    {
        $type = gettype($annotation);

        if ('string' == $type) {
            $valueObject = new $annotation();
            $this->_annotation = $valueObject->annotation();

        } elseif ('object' == $type && $annotation instanceof ValueObjectAbstract) {
            $this->_annotation = $annotation->annotation();

        } elseif ('object' == $type && $annotation instanceof Annotation) {
            $this->_annotation = $annotation;

        } else {
            throw new IllegalArgumentException(sprintf(self::PERSIST_INVLAID_PARAMETER, $annotation));
        }

        return $this;
    }

    /**
     * Retorna o Config utilizado.
     * @example Persist::getConfig
     * @code
     * <?php
     *     ...
     *     $persist->getConfig();
     *     ...
     * ?>
     * @endcode
     * @return PersistConfig
     */
    public function getConfig ()
    {
        return $this->_config;
    }

    /**
     * Registra o usuário logado para efeito de Log
     * @example Persist::registerUserId
     * @code
     * <?php
     *     ...
     *     $persist->registerUserId(10);
     *     ...
     * ?>
     * @endcode
     * @param integer $userId
     * @return Persist
     * @deprecated A implementação do log da camada de persistencia será de cada sistema
     */
    public function registerUserId($userId)
    {
        $this->_userId = (integer) $userId;

        return $this;
    }

    /**
     * Retorna o usuário logado para efeito de Log
     * @example Persist::getUserId
     * @code
     * <?php
     *     ...
     *     $persist->getUserId();
     *     ...
     * ?>
     * @endcode
     * @return integer
     * @deprecated A implementação do log da camada de persistencia será de cada sistema
     */
    public function getUserId()
    {
        return $this->_userId;
    }

    /**
     * Registra lista de ordenadores da pesquisa.
     * @example Persist::orderBy
     * @code
     * <?php
     *     ...
     *     $persist->orderBy('nome', 'ASC');
     *     ...
     * ?>
     * @endcode
     * @param string $field
     * @param string $order (ASC | DESC)
     * */
    public function orderBy ($field, $order = 'ASC')
    {
        # o nome da diretriz de ordenacao deve ser em maiusculo
        $order  = ($order = trim($order)) ? strtoupper($order) : 'ASC';

        IllegalArgumentException::throwsExceptionIfParamIsNull(
            in_array($order, array('ASC', 'DESC')),
            self::PERSIST_INVALID_ORDERBY_ARG
        );

        $this->_orderByList[$field] = $order;

        return $this;
    }

    /**
     * Aplica ordenação a consulta.
     * @example Persist::sorter
     * @code
     * <?php
     *     ...
     *     $persist->sorter($query)
     *     ...
     * ?>
     * @endcode
     * @param string $query
     * @return string
     * */
    public function sorter ($query)
    {
        # isso garante que mesmo que o driver escolhido nao suporte ordenacao nao estrague ;D
        return $query;
    }

    /**
     * Verifica se o atributo informado pode ser persistido pelo adapter informado.
     * @example Persist::isAttrPersistable
     * @code
     * <?php
     *     ...
     *     $attr = new \stdClass();
     *     $persist->isAttrPersistable($attr, $adapter)
     *     ...
     * ?>
     * @endcode
     * @param stdClass $attrAnnon
     * @param string $adapter
     * @return boolean
     * */
    public function isAttrPersistable (\stdClass $attr, $adapter)
    {
        $status = TRUE;

        if (FALSE == isset($attr->$adapter)) {
            $status = FALSE;
        }

        if (isset($attr->ignoreSaveIn)) {
            $status = !(in_array($adapter, explode(':', $attr->ignoreSaveIn)));
        }

        return $status;
    }

    /**
     * Fábrica de Persist.
     * @example Persist::factory
     * @code
     * <?php
     *     ...
     *     Persist::factory('\lib\Persist', $persistConfig);
     *     ...
     * ?>
     * @endcode
     * @param [string | Model] $namespace
     * @param  PersistConfig $config
     * @return Persist
     * @throws PersistException
     * */
    public static function factory ($namespace = NULL, PersistConfig $config)
    {
        if (is_string($namespace)) {
            self::exists($namespace, TRUE);

            return new $namespace($config);
        }

        PersistException::throwsExceptionIfParamIsNull(($namespace instanceof ModelAbstract), self::PERSIST_NAMESPACE_OR_MODEL_REQUIRED);

        # get the funcionality namespace
        $tmpNSFunc = current(explode(self::NAMESPACE_SEPARATOR . 'mvcb', $namespace->getNamespace()));

        $arrNS = explode(self::NAMESPACE_SEPARATOR, $tmpNSFunc);

        # get the funcionality name
        $tmpFuncy  = end($arrNS);
        $tpl       = '%1$s%2$spersist%2$s%3$s%2$s%4$sPersist';
        $namespace = sprintf($tpl, $tmpNSFunc, self::NAMESPACE_SEPARATOR, $config->get('adapter'), ucfirst($tmpFuncy));
        self::exists($namespace, TRUE);

        # instancia o ModuleDatabasePersist
        $tmpPersist = new $namespace($config);

        return $tmpPersist;
    }
}