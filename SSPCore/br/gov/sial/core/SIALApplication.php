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
use br\gov\sial\core\ClassLoader;
use br\gov\sial\core\SIALAbstract;
use br\gov\sial\core\util\Request;
use br\gov\sial\core\util\Location;
use br\gov\sial\core\util\ConfigIni;
use br\gov\sial\core\saf\SAFAbstract;
use br\gov\sial\core\hevent\EventManager;
use br\gov\sial\core\exception\IOException;
use br\gov\sial\core\util\CreateClassIfNotExists;
use br\gov\sial\core\mvcb\controller\ErrorController;
use br\gov\sial\core\exception\SIALApplicationException;
use br\gov\sial\core\exception\IllegalArgumentException;
use br\gov\sial\core\mvcb\controller\ControllerAbstract;
use br\gov\sial\core\mvcb\controller\exception\ControllerException;

/*
 * requires necessarios ate que o ClasseLoad seja carregado
 * */
require_once 'ClassLoader.php';
require_once 'SIALAbstract.php';
require_once 'Renderizable.php';
require_once 'util' . DIRECTORY_SEPARATOR . 'Request.php';
require_once 'util' . DIRECTORY_SEPARATOR . 'Location.php';
require_once 'util' . DIRECTORY_SEPARATOR . 'ConfigIni.php';
require_once 'exception' . DIRECTORY_SEPARATOR . 'IOException.php';
require_once 'exception' . DIRECTORY_SEPARATOR . 'SIALException.php';
require_once 'exception' . DIRECTORY_SEPARATOR . 'SIALApplicationException.php';

/**
 * SIAL Application vs 1.0.0
 * Esta classe utilitaria tem por finalidade facilitar a criacao de aplicacao usando SIAL
 * O uso deste utilitario permite ao programador utilizar um paradigma de desenvolvimento
 * baseado em eventos, embora seja opcional.
 *
 * @package br.gov.sial
 * @subpackage core
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class SIALApplication extends SIALAbstract
{
    /**
     * @var string
     * */
    const T_SIALAPPLICATION_IMPOSSIBLE_DEFINE_APP_HTDOCS = 'Impossível definir o local de armazenamento da aplicacao. O terceiro param nao foi definido e a funcao debug_backtrace nao está disponível';

    /**
     * @var string
     * */
    const T_SIALAPPLICATION_APP_TYPE_NOT_FOUND = 'A entrada *app.output.type*, no config.ini, não foi definida/encontrada. Esta definição é vital na definição do tipo de aplicacao';

    /**
     * @var stirng
     * */
    const T_SIALAPPLICATION_APP_DECORATOR_NOT_FOUND = 'A entrada *app.output.defaultDecorator* que define qual decorator utillizar não foi definida/encoantrada';

    /**
     * @var string
     * */
    const T_SIALAPPLICATION_INVALID_PLACE_TO_CREATE_DIRECTORY = 'O local informado não pode ser gravado';

    /**
     * @var string
     * */
    const T_SIALAPPLICATION_UNABLE_TO_CREATE_DIRECTORY = 'Não foi possíve criar o diretório: /...%s';

    /**
     * leitura e gravacao para que cria a pasta, o usuario do servidor web
     *  ---  000 0 0000
     *  --x  001 1 0100
     *  -w-  010 2 0200
     *  -wx  011 3 0300
     *  r--  100 4 0400
     *  r-x  101 5 0500
     *  rw-  110 6 0600
     *  rwx  111 7 0700
     *
     * @var string
     * */
    const T_SIALAPPLICATION_DIRECTORY_PERMISSION = 0700;

    /**
     * @var string
     * */
    const T_EVENT_APPLICATION_ON_BEFORE_READY = 'onBeforeApplicationReady';

    /**
     * @var string
     * */
    const T_EVENT_APPLICATION_ON_AFTER_READY = 'onAfterApplicationReady';

    /**
     * @var string
     * */
    const T_EVENT_APPLICATION_ON_READY = 'onApplicationReady';

    /**
     * @var string
     * */
    const T_APPLICATION_EVENT_ON_POST_DATA = 'onPostData';

    /**
     * @var string
     * */
    const T_APPLICATION_EVENT_ON_FILE_DATA = 'onFileData';

    /**
     * @var string
     * */
    const T_APPLICATION_EVENT_ON_COOKIE_DATA = 'onCookieData';

    /**
     * @var string
     * */
    const T_APPLICATION_EVENT_ON_GET_DATA = 'onGetData';

    /**
     * @var string
     * */
    const T_SIALAPPLICATION_LAYOUT_FILE_NOT_FOUND = 'O arquivo "%s" nao foi encontrado';

    /**
     * @var string
     * */
    private $_environment;

    /**
     * @var Config
     * */
    private $_config;

    /**
     * armazena o caminho da pasta da aplicacao, local onde fica
     * armazenado as pastas: public, application, cache, etc.
     *
     * @var string
     * */
    private $_htdocs;

    /**
     * gerenciador de eventos
     *
     * @var EventManager
     * */
    private $_eventManager;

    /**
     * application event
     *
     * @var EventElement
     * */
    private $_eventElement;

    /**
     * @var ISAF
     * */
    private $_isaf;

    /**
     * @var Bootstrap
     * */
    private $_bootstrap;

    /**
     * conjunto de variaveis que serao disponibilizadas para o layout
     * que for selecionado
     *
     * @var mixed[]
     * */
    private $_layoutRepoVariable = array();

    /**
     * @boolean
     * */
    private $_authStatus = FALSE;

    /**
     * @example SIALApplication::__construct
     * @code
     * <?php
     *     #
     *     # Ao criar uma nova aplicação é necessário informar o ambiente que esta ira ser executada
     *     # o valor informado para este param deve refletir a entrada do arquivo de configuracao (config.ini).
     *     # Um exemplo de config.ini válido pode ser obtido pelo utilitário 'SIALWizard' que acompanha o SIAL.
     *     #
     *     # Todas as configurações da aplicação deverão ficar acomodadas no arquivo config.ini,
     *     # por padrão, o arquivo de configuracao encontra-se em __APPLICATION_HOME__/application/config/config.ini
     *     # alternativamente ele pode ser acomodado em qualquer outra pasta.
     *     #
     *     # !!ATENÇÃO!!: Nunca crie o config.ini na pasta __APPLICATION_HOME__/public!
     *     #
     *     # criação da aplicação
     *     # Note que dois params sao requeridos:
     *     # o primeiro define onde a aplicacao vai rodar
     *     # o segundo define o local, endereço completo, do arquivo de configuração
     *     # #
     *     $app = SIALApplication::factory('development', '/fullpath/config.ini');
     * ?>
     * @endcode
     *
     * @param string $environment informa qual ambiente de execução a aplicação irá rodar
     * @param string $appConfigFilePath arquivo de configuração do aplicaçãoinforma o arquivo de configuração
     * */
    public function __construct ($environment, $appConfigFilePath)
    {
        $this->setEnvironment($environment)

             ->setAppConfig($appConfigFilePath)

             # verifica se todas as configuracoes do arquivo config.ini foram definidas
             ->chkConfigIniDefinition()

             # cria toda as constantes requeridas pelo SIAL
             ->initConst()

             # habilita o classe load
             ->enableClassloader()

             # verifica a estrutura de pastas do projeto
             ->chkStruture()

             # inicializa o bootstrap
             ->initBootstrap()

             # inicializa o gerenciado de eventos
             ->initEvent();
    }

    /**
     * @return Config
     * */
    public function config ()
    {
        return $this->_config;
    }

    /**
     * acesso as referencias dos objetos ou à 'saf' ou à 'bootstrap'
     *
     * @example SIALApplication::__get
     * @code
     * <?php
     *  use \br\gov\sial\core\exception\IllegalArgumentException;
     *
     *  try {
     *      # formato geral: $app->__VAR_NAME__
     *
     *      ...
     *      # recupera referencia do objeto SIAL Appllication Form (SAF)
     *      $saf = $app->saf;
     *
     *      ...
     *      # recupera referencia do objeto Bootstrat
     *      $bootstrap = $app->bootstrap;
     *
     *  } catch (IllegalArgumentException $iexc) {
     *      # tratar illegal exception conforme necessidade
     *  }
     *
     *  # recupera referencia do objeto Bootstrap
     *  $bootstrap = $app->bootstrap;
     * ?>
     * @endcode
     *
     * @param string $name informa o nome da referência que se deseja obter (ou 'saf' ou 'bootstrap')
     * @throws \br\gov\sial\core\exception\IllegalArgumentException lançada sempre que '__VAR_NAME__' não tiver disponível
     * */
    public function __get ($name)
    {
        # libera acesso somente leitura as objetos:
        switch ($name) {
            case 'saf': return $this->_isaf;
            case 'bootstrap': return $this->_bootstrap;
        }

        parent::__get($name);
    }

    /**
     * registra variavel para ser disponibilizadas no layout
     *
     * @example SIALApplication::set
     * @code
     * <?php
     *  ...
     *  $app->set('ident', $someValue);
     *  ...
     * ?>
     * @endcode
     *
     * @param string $key identificador da variavel no SAF
     * @param mixed $value valor atribuído à variável
     * @return SIALApplication
     * */
    public function set ($key, $value)
    {
        $this->_layoutRepoVariable[$key] = $value;
        return $this;
    }

    /**
     * registra o status de autenticacao do usuario
     *
     * @param boolean $status
     * @return SIALApplication
     * */
    public function auth ($status)
    {
        $this->_authStatus = (boolean) $status;
        return $this;
    }

    /**
     * retorna o status de autenticação do usuário
     * @return boolean
     * */
    public function isAuth ()
    {
        return $this->_authStatus;
    }

    /**
     * ISAF Delegator
     *
     * @example SIALApplication::add
     * @code
     * <?php
     *  # formato geral:
     *  # $app->add(__COMPONENT_NAME__, __OBJ_PARAM__, __PLACE__)
     *  ...
     *
     *  $app->add('grid', $gridParam, 'body');
     *
     *  # varios componentes podem ser acrescidos sequencialmente
     *  $app->add('title',       'Título da página', 'head')
     *      ->add('grid',        $gridParam, 'body')
     *      ->add('progressBar', $param, 'body')
     *      ->add('alert',       $param);
     *  ...
     * ?>
     * @endcode
     *
     * @param string $elType nome do componente que será acrescido no documento
     * @param stdClass $param objeto contendo os parâmetros do componente
     * @param string $place local onde o componente será adicionado no documento, válido apenas: ou head ou body
     * @return ISAF
     * */
    public function add ($elType, $param = NULL, $place = 'body')
    {
        return $this->_isaf->add($elType, $param, $place);
    }

    /**
     * retorna o controller
     *
     * toda requisica aponta para um controller, seja por meio da url (http://www.com.br/module/controller/action),
     * seja pela configuracao padrao do config.ini, este metodo retona o controller que por padrao sera executado
     *
     * @return ControllerAbstract
     * */
    public function controller ()
    {
        try {

            $ctrl = $this->_bootstrap->controller();

            if (FALSE === ($ctrl instanceof ControllerAbstract)) {
                throw new ControllerException;
            }

            return $ctrl->applicationRegister($this);

        } catch (ControllerException $ctre) {
            # inicializa processo de tratamento de error
            $cErr = new ErrorController($ctre);
            $cErr->getView()->set('exception', $ctre);

            # @todo registra de log interno do framework
            ;

            # inicializa view de error
            echo $cErr->errorAction();  die;

        } catch (\Exception $exc) {
            throw new ControllerException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * carrega parte ou todo o layout condigo no arquivo informado
     *
     * @param string $filename
     * @return SIALApplication
     * @throws IOException
     * */
    public function loadLayoutFromFile ($filename)
    {
        # @todo verificas URIInject
        $filename = Location::sandbox() . $filename . '.phtml';
        IOException::throwsExceptionIfParamIsNull(
            is_file($filename),
            sprintf(self::T_SIALAPPLICATION_LAYOUT_FILE_NOT_FOUND, $filename)
        );

        # disponibiliza as variaveis de 'layoutRepoVariable'
        foreach ($this->_layoutRepoVariable as $key => $value) {
            $$key = $value;
        }

        require $filename;

        return $this;
    }

    /**
     * configura a aplicacao
     * @return SIALApplication
     * */
    public function enableClassloader ()
    {
        # @todo lancar exception se:
        # - nao tiver dados de configuracao
        # - nao tiver configurado 'app.mainsystem'
        # - nao tiver permissao de leitura na estrutra do projeto
        # - nao tiver permissao de escrtita na estrutra de projeto (isso para criar as pastas necessarias, caso nao tenha sido ainda criadas: cache, tmpdir, etc)

        # ClassLoade: init...
        /* configura o autoload para carregar as classe do SIAL */
        ClassLoader::factory('br', current(preg_split('/\/br\//', self::SIALDocs())))->register();

        /* configura o autoload para carregar as classe da app */
        $APPNSSeparator = $this->_config->get('app.mainnamespace');
        $APPNSSeparator = current(explode(self::NAMESPACE_SEPARATOR, self::NAMESPACE_SEPARATOR == $APPNSSeparator[0] ? substr($APPNSSeparator, 1) : $APPNSSeparator));
        ClassLoader::factory($APPNSSeparator, constant('APPLICATION_HTDOCS'))->register();

        return $this;
    }

    /**
     * define o ambiente no qual a aplicacao vai rodar
     *
     * @param string $environment
     * @return SIALApplication
     * */
    public function setEnvironment ($environment)
    {
        $this->_environment = $environment;
        return $this;
    }

    public function setAppConfig ($configPath)
    {
        $module = Request::factory()->getModule();
        // dump( $module );
        // dump( Request::factory() );

        if (trim($module)) {
            $module = sprintf('.%s', $module);
        }

        $cfgFile = rtrim($configPath, DIRECTORY_SEPARATOR)
                 . DIRECTORY_SEPARATOR
                 . sprintf('config%s.ini', $module)
                 ;

        $this->_config = new ConfigIni($cfgFile, $this->_environment);

        return $this;
    }

    /**
     * define o caminho do arquivo de configuracao que a aplicacao usara
     *
     * @deprecated
     * @param string $appConfigFilePath
     * @return SIALApplication
     * @throws IllegalArgumentException
     * */
    public function setAppConfigFilePath ($appConfigFilePath)
    {
        return $this->setAppConfig(
            dirname($appConfigFilePath)
        );
    }

    /**
     * verifica e cria a estrutura de diretórios
     *
     * @return SIALApplication
     * @throws SIALApplicationException
     * */
    public function chkStruture ()
    {
        # cria os diretorios basicos a aplicacao
        $this->createDirIfNotExists(constant('APPLICATION_PATH'))
             ->createDirIfNotExists(constant('APPLICATION_TEMP_PATH'))
             ->createDirIfNotExists(constant('APPLICATION_PUBLIC_PATH'))
             ->createDirIfNotExists(constant('APPLICATION_CACHE_PATH'))
             ->createDirIfNotExists(constant('APPLICATION_LIBRARY_PATH'));

        # criar as classes basicas necessarias a aplicacao
        $this->createClassIfNotExists();
        return $this;
    }

    /**
     * cria o diretorio informado, se ainda nao existir, ou lanca um exception se
     * nao conseguir cria-lo
     *
     * @param string $dirpath
     * @return SIALApplication
     * @todo refatarar este metodo para outra classs
     * */
    public function createDirIfNotExists ($dirpath)
    {
        if (!is_dir($dirpath)) {

            # verifica se o diretorio solicitado esta abaixo da estrutura do projeto
            IOException::throwsExceptionIfParamIsNull(false !== strpos($dirpath, constant('APPLICATION_HTDOCS')), self::T_SIALAPPLICATION_INVALID_PLACE_TO_CREATE_DIRECTORY);

            # @todo criar rotina para verificar se existe permissao de escrita do diretorio
            # note que sera necessario criar uma rotina recursiva para esta verificacao,
            # tendo em vista que o usuario podera informar uma cadeia de diretorio (arvore)
            # e pedir para criar de forma recursivar, exemplo:
            # /var/www/app_folder (ate este ponto os diretorios jah existem)
            # o usuario solicita a criacao de:
            # /var/www/app_folder/br/com/foo/bar (note que o ultimo diretorio valido eh app_folder)
            IOException::throwsExceptionIfParamIsNull(
                mkdir($dirpath, self::T_SIALAPPLICATION_DIRECTORY_PERMISSION, TRUE),
                sprintf(self::T_SIALAPPLICATION_UNABLE_TO_CREATE_DIRECTORY, str_replace(constant('APPLICATION_HTDOCS'), '', $dirpath))
            );
        }

        return $this;
    }

    /**
     * Cria as classes necessarias caso nao existam
     * */
    public function createClassIfNotExists ()
    {
        # bootstrap
        $bootstrap  = $this->_config->get('app.mainnamespace') . self::NAMESPACE_SEPARATOR . 'library';

        $MVCBNamespace = $this->_config->get('app.mainnamespace') . self::NAMESPACE_SEPARATOR
                       . 'library'     . self::NAMESPACE_SEPARATOR
                       . 'mvcb'        . self::NAMESPACE_SEPARATOR;

        $view       = $MVCBNamespace . 'view' ;
        $model      = $MVCBNamespace . 'model';
        $business   = $MVCBNamespace . 'business';
        $controller = $MVCBNamespace . 'controller';

        $viewName  = ucfirst($this->_config->get('app.view.type'));
        IllegalArgumentException::throwsExceptionIfParamIsNull($viewName, 'O tipo de view não foi definido, informe o param app.view.type = HTML, por exemplo, no config.ini');

        CreateClassIfNotExists::create($bootstrap,  'Bootstrap',            '\br\gov\sial\core\BootstrapAbstract');

        CreateClassIfNotExists::create($view,       $viewName,              '\br\gov\sial\core\mvcb\view' . self::NAMESPACE_SEPARATOR . $viewName);
        CreateClassIfNotExists::create($model,      'ModelAbstract',        '\br\gov\sial\core\mvcb\model\ModelAbstract');
        CreateClassIfNotExists::create($business,   'BusinessAbstract',     '\br\gov\sial\core\mvcb\business\BusinessAbstract');
        CreateClassIfNotExists::create($controller, 'ControllerAbstract',   '\br\gov\sial\core\mvcb\controller\ControllerAbstract');
    }

    /**
     * Inicializa o sial application form e dispara os eventos iniciais
     *
     * @return SIALApplication
     * @throws SIALApplicationException
     * @todo tratar exception
     * */
    public function build ()
    {
        # inicializa sial application form
        $this->initISAF();

        $this->_eventManager->signal(self::T_EVENT_APPLICATION_ON_READY);

        /* dispara eventos iniciais */
        if ($this->bootstrap->request()->isPost()) {
            $this->_eventManager->signal(self::T_APPLICATION_EVENT_ON_POST_DATA);
        }

        if ($this->bootstrap->request()->isFile()) {
            $this->_eventManager->signal(self::T_APPLICATION_EVENT_ON_FILE_DATA);
        }

        if ($this->bootstrap->request()->isCookie()) {
            $this->_eventManager->signal(self::T_APPLICATION_EVENT_ON_COOKIE_DATA);
        }

        if ($this->bootstrap->request()->isGet()) {
            $this->_eventManager->signal(self::T_APPLICATION_EVENT_ON_GET_DATA);
        }

        return $this;
    }

    /**
     * verifica se todas as configuracoes do config.ini necessarias para a autoconfiguracao
     * da aplicacao foram definidas
     *
     * @return SIALApplication
     * */
    public function chkConfigIniDefinition ()
    {
        # @todo verificar se as propriedades: 'app.directory' e 'app.mainnamespace'
        return $this;
    }

    /**
     * Inicializa o gerenciador de eventos
     *
     * @return SIALAppllication
     * */
    public function initEvent ()
    {
        /* inicializa o gerenciador de eventos */
        $this->_eventManager = new EventManager;
        $this->_eventElement = $this->_eventManager->register($this);
        return $this;
    }

    /**
     * Inicializa o bootstrap
     *
     * @return SIALApplication
     * */
    public function initBootstrap ()
    {
        $namespace = $this->_config->get('app.mainnamespace')
                   . self::NAMESPACE_SEPARATOR . 'library'
                   . self::NAMESPACE_SEPARATOR . 'Bootstrap';

        require_once Location::realpathFromNamespace($namespace) . '.php';

        $this->_bootstrap = new $namespace($this->_config);

        return $this;
    }

    /**
     * Inicializa o ISAF
     *
     * @return SIALApplication
     * */
    public function initISAF ()
    {
        $saf = $this->_config->get('app.view.type');
        $decorator = $this->_config->get('app.output.defaultDecorator');

        SIALApplicationException::throwsExceptionIfParamIsNull($saf,       self::T_SIALAPPLICATION_APP_TYPE_NOT_FOUND);
        SIALApplicationException::throwsExceptionIfParamIsNull($decorator, self::T_SIALAPPLICATION_APP_DECORATOR_NOT_FOUND);

        # cria referencia do siaf
        $this->_isaf = new $decorator(SAFAbstract::factory($saf));

        return $this;
    }

    /**
     * cria todas as contantes necessarias ao SIAL
     *
     * @return SIALAppllication
     * */
    public function initConst ()
    {
        # determina o local de armazenamento da aplicacao
        $NSAppSep = $this->_config->get('app.directory');
        $this->_htdocs  = explode($NSAppSep, self::realpathFromNamespace($this->_config->get('app.mainnamespace')));
        $this->_htdocs  = current($this->_htdocs) . $NSAppSep;

        # constante que informa ao SIAL o ambiente de execucao
        # esta constate devera esta em sincroniza com as secoes definidas no arquivo de configuracao,
        # possiveis valores desta constante:
        # desenvolvimento.: development
        # producao........: prodution
        defined('APPLICATION_ENV')      || define('APPLICATION_ENV', $this->_environment, FALSE);

        # pasta base da applicacao
        # esta constante devera conter o caminho completo da raiz ateh a pasta que agrupa todas
        # as pasta da aplicacao, exemplo:
        # MS Windows.: c:\htdocs\appfolder
        # Linux......: /var/www/appfilder
        defined('APPLICATION_HTDOCS')   || define('APPLICATION_HTDOCS', $this->_htdocs , FALSE);

        # caminho completo da aplicacao incluindo seu namespace
        # esta constante devera conter o caminho completo da raiz ateh o ultimo pacote informado no namespace da app
        # por exemplo, sendo o namespace, br\com\appdemo
        # esta constante, 'APPLICATION_HOME', recebera
        # MS Windows.: c:\htdos\appfolder\com\appdemo
        # Linux......: /var/www/appfolder/com/appdemo
        defined('APPLICATION_HOME') || define('APPLICATION_HOME', self::realpathFromNamespace($this->_config->get('app.mainnamespace')), FALSE);

        # todo sistema deve possuir uma subpasta de nome 'application' no mesmo nivel da subpasta 'public' esta pasta
        # 'application' agrupa os modulos do sistema
        defined('APPLICATION_PATH') || define('APPLICATION_PATH', constant('APPLICATION_HOME') . DIRECTORY_SEPARATOR . 'application', FALSE);

        # o SIAL buscara em cada sistema uma pasta temporaria para trabalhar, esta constante define o local
        # onde esta pasta se encontra, por questao de problemas com permissão, sugerimos que ela fique no
        # mesmo nivel da pasta 'public'
        defined('APPLICATION_TEMP_PATH') || define('APPLICATION_TEMP_PATH', constant('APPLICATION_HOME') . DIRECTORY_SEPARATOR . 'tmpdir', FALSE);

        # esta constante armazena o caminho da pasta que tem acesso livre a seus elementos, ou seja,
        # tudo que podera ser recuperado externamento, pelo cliente, devera abaixo desta pasta
        defined('APPLICATION_PUBLIC_PATH')  || define('APPLICATION_PUBLIC_PATH', constant('APPLICATION_HOME') . DIRECTORY_SEPARATOR . 'public', FALSE);

        # sial faz cache de uma serie de objetos, principalmente valueObject, esta constante armazena o
        # cominho ate a pasta de cache
        defined('APPLICATION_CACHE_PATH')  || define('APPLICATION_CACHE_PATH', constant('APPLICATION_HOME') . DIRECTORY_SEPARATOR . 'cache', FALSE);

        # especializacao de classes do core
        defined('APPLICATION_LIBRARY_PATH')  || define('APPLICATION_LIBRARY_PATH', constant('APPLICATION_HOME') . DIRECTORY_SEPARATOR . 'library', FALSE);

        return $this;
    }

    /**
     * regitra evento para ser disparado
     *
     * @param string $event
     * @param callback $callback
     * @return SIALApplication
     * */
    public function addEvent ($event, $callback)
    {
        $this->_eventElement->$event($callback);
        return $this;
    }

    /**
     * dispara evento
     * podendo passar parametros para o callback do evento disparado
     *
     * @param string $event
     * @param mixed $eventParameters = NULL
     * @return SIALApplication
     * */
    public function raise ($event, $eventParameters = NULL)
    {
        $this->_eventManager->raise($event, $eventParameters);
        return $this;
    }

    /**
     * suspende a execucao do evento informado
     *
     * @param string $event
     * @return SIALApplication
     * */
    public function preventEvent ($event)
    {
        $this->_eventManager->preventEvent($event);
        return $this;
    }

    /**
     * a montagem do menu se da com base das informacoes do config.ini
     *
     * @return SIALApplication
     * */
    public function loadDefaultLayout ()
    {
        # @todo refatorar este metodo
        foreach($this->_layoutElements() as $section => $elements) {
            foreach ($elements as $element => $param) {

                if (is_array($param)) {
                    foreach ($param as $witch) {
                        $this->_isaf->add($element, json_decode(($witch = trim($witch)) ? $witch : ""), $section);
                    }
                } else {
                    $this->_isaf->add($element, json_decode(($param = trim($param)) ? $param : ""), $section);
                }

            }
        }

        return $this;
    }

    /**
     * recupera os elementos que serao inseridos no layout
     * @return string[]
     * */
    private function _layoutElements ()
    {
        $elements  = array();
        $appLayout = $this->_config->get('app.layout');
        $status    = $this->isAuth() ? 'auth' : 'unauth';

        # recupera elementos comum
        if ($tmpElm = $appLayout->get('all')) {
            $elements = array_merge($elements, $tmpElm->toArray());
        }

        if ($tmpElm = $appLayout->get($status)) {
            $elements = array_merge($elements, $tmpElm->toArray());
        }

        return $elements;
    }

    /**
     * Adiciona ao SAF os elementos CSS
     *
     * @return SIALApplication
     * */
    public function SAFAddStylesheet ()
    {
        $param = new \stdClass;
        $param->rel   = 'stylesheet';
        $param->media = 'media';
        $param->type  = 'text/css';
        $param->href  = NULL;

        if (($stylesheets = $this->_config->get('app.clientsite.stylesheet'))) {
            $stylesheets = $stylesheets->toArray();
        }

        foreach ((array) $stylesheets as $CSSFile) {
            $param->href = $CSSFile;
            $this->_isaf->add('stylesheet', $param, 'head');
        }

        return $this;
    }

    /**
     * Adiciona ao SAF os elementos Javascript
     *
     * @return SIALApplication
     * */
    public function SAFAddJavascript ()
    {
        $param = new \stdClass;
        $param->src  = NULL;

        if (($javascripts = $this->_config->get('app.clientsite.javascript'))) {
            $javascripts = $javascripts->toArray();
        }

        foreach ((array) $javascripts as $JSFile) {
            $param->src = $JSFile;
            $this->_isaf->add('javascript', $param, 'head');
        }

        return $this;
    }

    /**
     * Fábrica de objetos
     *
     * @param string $environment
     * @param string $appConfigFilePath
     * @param string $htdocs
     * @throws SIALApplicationException
     * @return SIALApplication
     * */
    public static function factory ($environment, $appConfigFilePath, $htdocs = NULL)
    {
        return new self($environment, $appConfigFilePath, $htdocs);
    }

    /**
     * Renderiza
     *
     * @param string $layout
     * @return string
     * */
    public function render ($layout = NULL)
    {
        # garante a compatibilidade da chamada com a view comum
        if (NULL !== $layout) {
            $this->loadLayoutFromFile($layout);
        } else {
            $output = $this->_isaf->render();
            return $output;
        }
    }

    /**
     * @inheritdoc
     * @return string
     * */
    public function __toString ()
    {
        return $this->render();
    }

    /**
     * exibe app
     * */
    public function show ()
    {
        echo $this->render();
    }
}