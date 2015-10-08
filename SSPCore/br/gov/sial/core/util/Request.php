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
namespace br\gov\sial\core\util;
use br\gov\sial\core\Factorable,
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\client\JQuery,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Factorable.php';

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @author J. Augusto <augustowebd@gmail.com>
 * */
final class Request extends SIALAbstract implements Factorable
{
    /**
     * define a posicao do parametro modulo no array de configuracao
     * */
    const SEQUENCE_PARAMETER_MODULE = 1;

    /**
     * define a posicao do parametro funcionalidade no array de configuracao
     * */
    const SEQUENCE_PARAMETER_FUNCIONALITY = 2;

    /**
     * define a posicao do parametro acao no array de configuracao
     * */
    const SEQUENCE_PARAMETER_ACTION = 3;

    /**
     * nome do modulo default a ser executado sempre que o mesmo for omitido
     *
     * @var string
     * */
    public static $moduleDefault = 'main';

    /**
     * nome da funcionalidade default a ser executado sempre que o mesmo for omitido
     *
     * @var string
     * */
    public static $functionalityDefault = 'init';

    /**
     * nome da acao default a ser executado sempre que a mesma for omitido
     *
     * @var string
     * */
    public static $actionDefault = 'default';

    /**
     * true quando a app for usar separacao em modulos
     *
     * @var boolean
     * */
    public static $useModule = TRUE;

    /**
     * @var Request
     * */
    private static $_instance = NULL;

    /**
     * storage default de parametros
     *
     * @var string
     * */
    private static $_defaultStorageParams = 'get';

    /**
     * lista de params chaves
     *
     * @var string[]
     * */
    private static $_paramList = array();

    /**
     * metodos suportados
     *
     * @var string[]
     * */
    private static $_acceptedMethods = array(
        'get',
        'post',
        'cookie',
        'files'
    );

    /**
     * lista de parametros
     *
     * @var string[]
     * */
    private $_params = array();

    /**
     * nome da chave que identifica o modulo na lista de params
     *
     * @var string
     * */
    private $_moduleKey = 'm';

    /**
     * nome da chave que identifica o funcionalidade na lista de params
     *
     * @var string
     * */
    private $_funcionalityKey = 'f';

    /**
     * nome da chave que identifica a action na lista de params
     *
     * @var string
     * */
    private $_actionKey = 'a';

    /**
     * @var boolean
     * */
    private $_useMagicQuotesGpc = FALSE;

    /**
     * construtor
     * */
    public function __construct ()
    {
        $this->_initParams();
    }

    /**
     *
     * @deprecated
     *   para as próximas versões do SIAL este método, bem como seu comportamente,
     *   será removido, uma vez que o PHP deixou de ter suporte a este comportamento
     *
     * @param boolean
     * */
    public function setBehaviorMagicQuotesGPC ($static)
    {
        $this->_useMagicQuotesGpc = (boolean) $static;

        return $this;
    }

    /**
     * configura o modulo do sistema a ser executado
     *
     * @param string[] $params
     * @throws IllegalArgumentException
     * */
    private function _setupParameterModule (array &$params)
    {
        $this->setModule($params[self::SEQUENCE_PARAMETER_MODULE - 1]);
        unset($params[self::SEQUENCE_PARAMETER_MODULE - 1]);
    }

    /**
     * configura a funcionalidade do modulo ser executado
     *
     * @param string[] $params
     * */
    private function _setupParameterModuleFunctionality (array &$params)
    {
        $this->_setupParameterModule($params);
        $this->setFuncionality($params[self::SEQUENCE_PARAMETER_FUNCIONALITY - 1]);
        unset($params[self::SEQUENCE_PARAMETER_FUNCIONALITY - 1]);
    }

    /**
     * configura a acao da funcionalidade a ser executada
     *
     * @param string[] $params
     * */
    private function _setupParameterModuleFunctionalityAction (array &$params)
    {
        $this->_setupParameterModuleFunctionality($params);
        $this->setAction($params[self::SEQUENCE_PARAMETER_ACTION - 1]);
        unset($params[self::SEQUENCE_PARAMETER_ACTION - 1]);
    }

    /**
     * inicializa todos os params de request
     * Obtem os parametros passados via POST, GET, COOKIE ou FILES
     *
     * @todo ao reimplementar os teste unitários, refatorar este método
     * */
    private function _initParams ()
    {
        $scopes = array(
            'get' => &$_GET,
            'post'=> &$_POST,
            'cookie' => &$_COOKIE,
            'files' => &$_FILES
        );

        $tmpSwap   = NULL;

        if (!isset($_SERVER['REQUEST_URI'])) {
            $_SERVER['REQUEST_URI'] = '';
        }

        $urlParams = $this->_getUrlparam($_SERVER['REQUEST_URI']);

        $urlParams = preg_replace('/\?/', '/', $urlParams);

        $urlParams = $urlParams ? explode('/', $urlParams) : array();

        # avalia se a qnt de parametos sao sucientes para configuracao de modulo
        $paramLen  = sizeof($urlParams);

        if (self::SEQUENCE_PARAMETER_FUNCIONALITY == $paramLen) {
            array_push($urlParams, self::$actionDefault);
        } elseif(self::SEQUENCE_PARAMETER_MODULE == $paramLen) {
            array_push($urlParams, self::$functionalityDefault, self::$actionDefault);
        } elseif (0 === $paramLen) {
            $urlParams = array(self::$moduleDefault, self::$functionalityDefault, self::$actionDefault);
        }

        # configura os parametros de controler da requisicao
        $this->_setupParameterModuleFunctionalityAction($urlParams);

        # garante parametro sem valor
        if (1 === sizeof($urlParams) % 2) {
            array_push($urlParams, NULL);
        }

        $tmpSwap = NULL;

        foreach ($urlParams as $key) {
            if (NULL === $tmpSwap) {
                $tmpSwap = $key;
            } else {
                $_GET[$tmpSwap] = $key;
                $tmpSwap = NULL;
            }
        }

        # armazena tudo que foi enviado pela url
        foreach ($scopes as $scope => $values) {
            foreach ($values as $key => $value) {
               $this->_params[$scope][$key] = $this->_getValue($value, $scope);
            }
        }
    }

    public function _getValue ($value, $scope)
    {
        if (! in_array($scope, array('get', 'post', 'cookie'))) {
            return $value;
        }

        $phpStatusGpc = get_magic_quotes_gpc();
        $appStatusGpc = $this->_useMagicQuotesGpc;

        $stripslashes = function ($value) use (&$stripslashes) {
            return is_array($value)
                  ? array_map($stripslashes, $value)
                  : stripslashes($value)
                  ;
        };

        $addslashes = function ($value) use (&$addslashes) {
            return is_array($value)
                  ? array_map($addslashes, $value)
                  : addslashes($value)
                  ;
        };

        # app não quer e o suporte está desabilitado: não há o que fazer
        if (!$appStatusGpc && !$phpStatusGpc) { return $value; }

        # app quer gpc e o suporte está HABILITADO: não há o que fazer
        if ($appStatusGpc && $phpStatusGpc) { return $value; }

        # app quer gpc e o suporte está DESABILITADO: aplica o addslashes
        if ($appStatusGpc && !$phpStatusGpc) { return $addslashes($value); }

        # app não quer gpc e o suporte está habilitado: aplica stripslashes
        if (! $appStatusGpc && $phpStatusGpc) { return $stripslashes($value); }

        error_log(
            sprintf('_getValue(%s) : [$appStatusGpc: %s] && [$phpStatusGpc: %s]'
                    , $value
                    , $appStatusGpc
                    , $phpStatusGpc
            )
        );
    }

    /**
     * retorna os parametros passados na url em um array
     *
     * @name _getUrlparam
     * @access private
     * @return string[]
     * */
    private function _getUrlparam ()
    {
        # assume que está sendo usado url amigavel
        $urlParams = preg_replace('!(//)+!is', '/', $_SERVER['REQUEST_URI']);

        $urlParams =  '/' == substr($urlParams, -1)
                           ? substr($urlParams,  1, -1)
                           : substr($urlParams,  1);

        return trim($urlParams);
    }

    /**
     * remove um scopo dos parametros de acordo com os Métodos Aceitos
     * @param string $scope
     * @throws IllegalArgumentException
     */
    public function clearParams ($scope)
    {
        if (!in_array($scope, self::$_acceptedMethods)) {
            throw IllegalArgumentException::argumentCantBeNULL("method: {$scope}");
        }

        unset($this->_params[$scope]);
    }

    /**
     * verifica se a requisição foi por post
     *
     * @name isPost
     * @access public
     * @return bool
     * */
    public function isPost ()
    {
        if (isset($this->_params['post']) && sizeof($this->_params['post'])) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * verifica se existe arquivo enviado
     *
     * @return boolean
     * */
    public function isFile ()
    {
        if (isset($this->_params['files']) && sizeof($this->_params['files'])) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * @return boolean
     * */
    public function isCookie ()
    {
        if (isset($this->_params['cookie']) && sizeof($this->_params['cookie'])) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * verifica se a requisição foi por get
     *
     * @param boolean
     * @return boolean
     * */
    public function isGet ()
    {
        if (isset($this->_params['get']) && sizeof($this->_params['get'])) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * redireciona o fluxo para nova url. Se o segundo argumento for informado
     * os valores postados via POST serao portados para a URL destino
     *
     * @example Request::redirect
     *          @code
     *            #Descarta $_POST -
     *            Request::redirect('/system/module/action/paramname/paramvalue');
     *            #Preserva $_POST -
     *            Request::redirect('/system/module/action/paramname/paramvalue', true);
     *          @endcode
     * @param string $url
     * @param boolean $preservePostStantment
     * @todo Alterar função para apenas montar o conteudo de header e mover a função para outra classe fora dos tsts
     * */
    public function redirect ($url, $preservePostStantment = FALSE)
    {
        if ($preservePostStantment) {
            header('HTTP/1.0 307 Temporary redirect');
        }

        header("Location: {$url}");
    }

    /**
     * retorna o conteudo do parametro que tenha como indice a chave igual a informada
     * opcionalmente, o metodo pode ser informado. Se a chave informada nao for localizada
     * o metodo retorna NULL
     *
     * @example Request::getParam
     *          @code
     *            $this->request()->getParam('filter','post');
     *          @endcode
     *
     * @name getParam
     * @access public
     * @param string $key
     * @param string ('get', 'post', 'cookie', 'files') $scope
     * @return mixed
     * */
    public function getParam ($key, $scope = NULL)
    {
        $key = self::isValidKey($key);
        $param = $this->getParams($scope);

        if (!isset($param[$key])) {
            return NULL;
        }

        return $param[$key];
    }

    /**
     * retorna todos os parametros. Se for informado o scopo, retornara somente os parametros
     * do scopo passado
     *
     * @example Request::getParams
     *          @code
     *            $this->request()->getParams('post');
     *          @endcode
     *
     * @param string $scope
     * @return mixed[]
     * */
    public function getParams ($scope = NULL)
    {
        $scope = $scope ?: self::$_defaultStorageParams;
        self::isValidMethod($scope);

        if (isset($this->_params[$scope])) {

            if ('files' == $scope) {
                $arrVOFiles = array();

                foreach ($this->_params[$scope] as $key => $value) {
                    $arrVOFiles[$key] = ValueObjectAbstract::factory('br\gov\sial\core\lang\TFile')
                                                         ->loadData($value)
                                                         ->setSource($value['tmp_name']);
                }

                return $arrVOFiles;

            } else {

                return $this->_params[$scope];

            }
        }

        return NULL;
    }

    /**
     * retorna a chave do modulo utilizada pra identificar no array de parametros
     *
     * @return string
     */
    public function getModuleKey ()
    {
        return (string) $this->_moduleKey;
    }

    /**
     * retorna a chave da funcionalidade utilizada pra identificar no array de parametros
     *
     * @return string
     */
    public function getFuncionalityKey ()
    {
        return (string) $this->_funcionalityKey;
    }

    /**
     * retorna a chave da action utilizada pra identificar no array de parametros
     *
     * @return string
     */
    public function getActionKey ()
    {
        return (string) $this->_actionKey;
    }

    /**
     * retorna o modulo
     *
     * @return string
     * @throws \br\gov\sial\core\exception\IllegalArgumentException
     * */
    public function getModule ()
    {
        return (string) $this->getParam($this->_moduleKey);
    }

    /**
     * retorna a funcionalidade
     *
     * @return string
     * */
    public function getFuncionality ()
    {
        return (string) $this->getParam($this->_funcionalityKey);
    }

    /**
     * retorna o nome da action que sera executada.
     *
     * @param boolean $wSuffix
     * @return string
     * */
    public function getAction ($wSuffix = FALSE)
    {
        $action = $this->getParam($this->_actionKey);

        if ($wSuffix) {
            $action = str_replace('Action', '', $action);
        }

        return $action;
    }

    /**
     * desserializa os dados serializado pelo JQuery::serialize
     *
     * @param string $scope = post
     * @return string
     * @throws IllegalArgumentException
     * */
    public function jQueryUnserialize ($scope = 'post')
    {
        self::isValidMethod($scope);

        return JQuery::factory()->unserialize($this->getParam('form', $scope));
    }

    /**
     * desserializa os dados serializado pelo JQuery::serializeArray
     *
     * @param string $scope = post
     * @return string
     * @throws IllegalArgumentException
     * */
    public function jQueryUnserializeArray ($scope = 'post')
    {
        self::isValidMethod($scope);

        return JQuery::factory()->unserializeArray($this->getParam('form', $scope));
    }

    /**
     * seta valor do parametro. E necessario informar a chave, o valor e opcionalmente
     * o escopo
     *
     * @param string $key
     * @param mixed $value
     * @param string $method
     * @return Request
     * @throws IllegalArgumentException
     * */
    public function setParam ($key, $value, $scope = NULL)
    {
        $key = self::isValidKey($key);
        $scope = $scope ?: self::$_defaultStorageParams;
        $scope = self::isValidMethod($scope);
        $this->_params[$scope][$key] = $value;

        return $this;
    }

   /**
    * Implementa a rotina setParam para 'n' parâmetros
    *
    * @param array $params
    * @param string $scope
    * */
    public function setParams (array $params, $scope = 'GET')
    {
        foreach ($params as $key => $param){
            $this->setParam($key, $param, $scope);
        }
    }

    /**
     * seta a chave do modulo utilizada para identifcar no array de parametros
     *
     * @param string $key
     * @return Request
     * @throws IllegalArgumentException
     * */
    public function setModuleKey ($key)
    {
        $this->_setupKeyParameter('_moduleKey', $key);

        return $this;
    }

    /**
     * seta a chave da funcionalidade utilizada para identifcar no array de parametros
     *
     * @name setFuncionalityKey
     * @access public
     * @param string $key
     * @return Request
     * @throws IllegalArgumentException
     * */
    public function setFuncionalityKey ($key)
    {
        $this->_setupKeyParameter('_funcionalityKey', $key);
        return $this;
    }

    /**
     * seta a chave da action utilizada para identificar no array de parametros
     *
     * @name setActionKey
     * @access public
     * @param string $key
     * @return Request
     * @throws IllegalArgumentException
     * */
    public function setActionKey ($key)
    {
        $this->_setupKeyParameter('_actionKey', $key);

        return $this;
    }

    /**
     * configura o nome do modulo
     *
     * @param string $module
     * @return Request
     * @throws IllegalArgumentException
     * */
    public function setModule ($module)
    {
        $this->setParam($this->_moduleKey, $module);

        return $this;
    }

    /**
     * configura o nome da funcionalidade
     *
     * @param string $funcionality
     * @return Request
     * @throws IllegalArgumentException
     * */
    public function setFuncionality ($funcionality)
    {
        $this->setParam($this->_funcionalityKey, $funcionality);

        return $this;
    }

    /**
     * configura o nome da action
     * este metodo modifica o conteudo informado para que este passe a refletir o nome de uma
     * action valida.
     *
     *  @example Request::setAction
     *  <b>name</b><i>Action</i>
     *  <ul>
     *    <li> Request::setAction('add') :: Request::getAction(); output: (string) addAction</li>
     *    <li> Request::setAction('new') :: Request::getAction(); output: (string) newAction</li>
     *  </ul>
     *
     * @param string $action
     * @return Request
     * @throws IllegalArgumentException
     * */
    public function setAction ($action)
    {
        if (strtolower(substr($action, -6)) != 'action') {
            $action .= 'Action';
        }

        $this->setParam($this->_actionKey, $action);

        return $this;
    }

    /**
     * realiza os procedimentos de configuracao para a alteracao de chave de identificacao no array
     * de parametros. Altera a chave movendo seu valor para a nova
     *
     * @param string $curKey
     * @param string $newkey
     * @param string $scope
     * @throws IllegalArgumentException
     * */
    private function _setupKeyParameter ($curKey, $newkey, $scope = NULL)
    {
        $scope = $scope ?: self::$_defaultStorageParams;
        $newkey = self::isValidKey($newkey);
        $curValue = $this->getParam($this->$curKey, $scope);
        unset($this->_params[$scope][$this->$curKey]);
        $this->setParam($newkey, $curValue, $scope);
        $this->$curKey = $newkey;
        $this->_params[$scope][$newkey] = $curValue;
    }

    /**
     * verifica se o metodo informado e valido e o retorna
     *
     * @param string $method
     * @param string string
     * @return string
     * @throws IllegalArgumentException
     * */
    public static function isValidMethod ($method)
    {
        $method = trim(strtolower($method));

        if (!in_array($method, self::$_acceptedMethods)) {
            throw IllegalArgumentException::argumentCantBeNULL("method: {$method}");
        }

        return $method;
    }

    /**
     * verifica se a chave informada e valida e a retorna
     *
     * @param string $key
     * @return string
     * @throws IllegalArgumentException
     * */
    public static function isValidKey ($key)
    {
        $key = trim($key);

        if (!$key) {
            throw IllegalArgumentException::argumentCantBeNULL("key: {$key}");
        }

        return $key;
    }

    /**
     * define/retorna o storage default para armazenamento de paramentos
     *
     * @param string
     * @return string
     * */
    public static function defaultStorageParam ($name = NULL)
    {
        if (NULL !== $name) {
            self::isValidMethod($name);
            self::$_defaultStorageParams = strtolower($name);
        }

        return self::$_defaultStorageParams;
    }

    /**
     * fábrica de objetos
     *
     * @return Request
     * */
    public static function factory ()
    {
        if (NULL == self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }
}