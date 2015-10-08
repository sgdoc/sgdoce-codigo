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
namespace br\gov\sial\core;
use br\gov\sial\core\util\Request;
use br\gov\sial\core\util\Location;
use br\gov\sial\core\util\Registry;
use br\gov\sial\core\util\ConfigIni;
use br\gov\sial\core\persist\Persist;
use br\gov\sial\core\util\ConfigAbstract;
use br\gov\sial\core\util\AnnotationCache;
use br\gov\sial\core\util\ConstantHandler;
use br\gov\sial\core\persist\PersistConfig;
use br\gov\sial\core\persist\PersistLogAbstract;
use br\gov\sial\core\mvcb\controller\ErrorController;
use br\gov\sial\core\mvcb\controller\exception\ControllerException;

/**
 * SIAL
 *
 * @package br.gov.sial
 * @subpackage core
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class BootstrapAbstract extends SIALAbstract
{
    /**
     * habilita o carregamento automatico de constantes
     *
     * @var boolean
     * */
    const AUTOLOAD_CONSTANT = TRUE;

    /**
     * @var Request
     * */
    private $_request;

    /**
     * @var ConfigAbstract
     * */
    private $_config;

    /**
     * @var ConstantHandler
     * */
    private $_constantHandler;

    /**
     * construtor
     *
     * @param ConfigAbstract $config
     * */
    public function __construct (ConfigAbstract $config)
    {
        $this->_config = $config;

        # inicia o setup das configurações de ambiente
        $this->setupEnvironment($this->_config);

        # registra o bootstrap na registry
        Registry::set('bootstrap', $this);

        # setup default module/functionaly/action
        $tmpCfgApp = $this->_config->get('app');

        # setup persistcofig
        PersistConfig::registerConfigs($tmpCfgApp->get('persist')->toArray());

        $moduleDefault                 = $tmpCfgApp->get('module.default');
        Request::$moduleDefault        = $moduleDefault->get('name');
        Request::$functionalityDefault = $moduleDefault->get('functionality');
        Request::$actionDefault        = $moduleDefault->get('action');

        /* recupera */
        $this->_request = new Request();

        /* aponta para o config.ini do módulo selecionado, caso este possui um .ini próprio */
        $this->_useEspecificConfigIniFileIfExistsOne();

        # registra o local de armazenamento do cache dos Values Object
        AnnotationCache::$cacheDir = $tmpCfgApp->get('cache.home');

        # inicializa a requisicao
        $this->_request->setBehaviorMagicQuotesGPC(
            $config->get('php.environment.magic_quotes_gpc')
        );

        # inicializa o manipulador de constantes
        $this->_constantHandler = ConstantHandler::factory($this);
        $this->_autoloadConstant();

        # habilita ou nao uso de registro de log
        $this->_enablePersistLog($this->_config);
    }

    /**
     * se existir um arquivo de configuração especifico da aplicação, o bootstrap
     * passará a usar o configurador mais especializado.
     * */
    private function _useEspecificConfigIniFileIfExistsOne ()
    {
        if (! ($this->_config instanceof ConfigIni)) {
            return;
        }

        $module  = $this->_request->getModule();
        $iniInfo = pathinfo($this->_config->getConfigIniFilename());
        $iniModule = $iniInfo['dirname'] . DIRECTORY_SEPARATOR . $iniInfo['filename'] . '.' . $module . '.ini';

        if (is_file($iniModule)) {
            $this->_config = new ConfigIni($iniModule ,$this->_config->section());
        }
    }

    /**
     * habilita ou nao o log da camada de persistencia
     *
     * @param ConfigAbstract $config
     * */
    private function _enablePersistLog(ConfigAbstract $config)
    {
        $persistLogger = $config->get('app.persist.logger');

        if ($persistLogger) {
            #instancia o reponsável pelo log do banco
            if (class_exists($persistLogger)) {
                Persist::$persistLoggerInstance = new $persistLogger;
            } else {
                error_log("[SSPCore] config app.persist.logger = {$persistLogger} (class not exists)");
            }

            # registra o bootstrap na persistlog para que ela seja
            # capaz de recuperar informacoes do sistema, modulo, action
            # que realizou a operacao que sera logada
            PersistLogAbstract::bootstrap($this);
        }
    }

    /**
     * carrega o arquivo de constante do sistema/modulo/funcionalidade se o arquivo existir
     * */
    private function _autoloadConstant()
    {
        $this->_constantHandler->requestConstantAutoload();
    }

    /**
     * metodo responsavel por iniciar a execucao da applicacao
     *
     * @access public
     * @codeCoverageIgnoreStart
     * */
    public function run()
    {
        try {

            $ctrl = $this->controller();
            echo $ctrl->$action();

        } catch (ControllerException $ctre) {
            # inicializa processo de tratamento de error
            $ce = new ErrorController($ctre);
            $ce->getView()->set('exception', $ctre);

            # @todo registra de log interno do framework
            ;

            # inicializa view de error
            echo $ce->errorAction();
        } catch (\Exception $e) {
            # @todo remover esta saida de dump e trata-la adequadamente
            dump($e);
        }
    }

    /**
     * retorna o controller que sera executado
     *
     * @return ControllerAbstract
     * @throws ControllerException
     * */
    public function controller ()
    {
        $config  = $this->config();
        $request = $this->request();

        $module       = $request->getModule();
        $funcionality = $request->getFuncionality();
        $action       = $request->getAction();
        $namespace    = $config->get('app.namespace').self::NAMESPACE_SEPARATOR;

        if (TRUE === Request::$useModule) {
            $namespace .= $module;
        }

        $namespace .= sprintf('%1$s%2$s%1$smvcb%1$scontroller%1$s%3$sController'
            , self::NAMESPACE_SEPARATOR
            , $funcionality
            , ucfirst($funcionality));

        $fullpath = Location::realpathFromNamespace($namespace).'.php';

        ControllerException::throwsExceptionIfParamIsNull(is_file($fullpath), "Controller::{$funcionality} não implementada");

        $ctrl = new $namespace();
        ControllerException::throwsExceptionIfParamIsNull($ctrl->hasAction($action), "Action '{$namespace}::{$action}' não existe.");

        return $ctrl;
    }

    /**
     * registra a classe de log de persistencia
     *
     * @return BootstrapAbstract
     * @codeCoverageIgnoreStart
     * */
    public function persistLog (PersistLogAbstract $persistLog)
    {
    }

    /**
     * recupera configuracao definida em Config. Se uma chave nao for informada sera retornado
     * o objeto de configuracao
     *
     * @param string $key
     * @return mixed
     * */
    public function config ($key = NULL)
    {
        if (NULL == $key) {
            return $this->_config;
        }
        return $this->_config->get($key);
    }

    /**
     * retorna o objeto de request
     *
     * @return Request
     * */
    public function request ()
    {
        return $this->_request;
    }

    /**
     * retorna o módulo
     *
     * @return string
     * */
    public function getModule ()
    {
        return (string) $this->_request->getModule();
    }

    /**
     * retorna a funcionalidade
     *
     * @return string
     * */
    public function getFuncionality ()
    {
        return $this->_request->getFuncionality();
    }

    /**
     * retorna a action
     *
     * @return string
     * */
    public function getAction ()
    {
        return $this->_request->getAction();
    }

    /**
     * retorna o parametro informado da requisição
     *
     * @param string $key
     * @param string|null $scope
     * @return string
     * */
    public function getParam ($key, $scope = NULL)
    {
        return $this->_request->getParam($key, $scope);
    }

    /**
     * retorna todos os parametros da requisiao
     *
     * @param string|null $scope
     * @return string[]
     * */
    public function getParams ($scope = NULL)
    {
        return $this->_request->getParams($scope);
    }

    /**
     * retorna o conteudo da constant do SIAL (ConstantHandler) informada, se o segundo param for informado sera
     * assumido que a chamado foi realizando informadando apenas o nome da consntant (vide comentário da classe
     * ConstantHandler Regra#3) deixando  a cargo da classe
     * montar o nome completo.
     *
     * @param string $name
     * @param boolean $shortname
     * @return string
     * */
    public function constant ($name, $shortname = TRUE)
    {
        return $this->_constantHandler->get($name, $shortname);
    }

    /**
     * retorna instancia do gerenciador de constantes
     *
     * @return ConstantHandler
     * */
    public function constantHandler ()
    {
        return $this->_constantHandler;
    }

    /**
     * Inicializa o ambiente
     *
     * @param ConfigAbstract
     * */
    public static function setupEnvironment (ConfigAbstract $config) {
        self::_setEnvironmentIni($config->get('php.environment.ini')->toArray());
        self::_setEnvironmentFnc($config->get('php.environment.fnc')->toArray());
    }

    /**
     * Seta as variaveis de configuração
     *
     * @param string[]
     * */
    private static function _setEnvironmentIni (array $iniCnf = array())
    {
        foreach ($iniCnf as $varname => $newvalue) {

            if (strpos($varname, '-')) {
                $varname = str_replace('-', '.', $varname);
            }

            ini_set($varname, $newvalue);
        }
    }

    /**
     * Invoca as funções setadas para inicialização
     *
     * @param string[]
     * */
    private static function _setEnvironmentFnc (array $iniCnf = array())
    {
        foreach ($iniCnf as $fnc => $param) {
            $fnc($param);
        }
    }
}