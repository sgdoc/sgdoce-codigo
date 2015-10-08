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
namespace br\gov\sial\core\mvcb\controller;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\Location,
    br\gov\sial\core\util\Registry,
    br\gov\sial\core\SIALApplication,
    br\gov\sial\core\BootstrapAbstract,
    br\gov\sial\core\persist\PersistConfig,
    br\gov\sial\core\mvcb\view\ViewAbstract,
    br\gov\sial\core\persist\meta\MetaAbstract,
    br\gov\sial\core\mvcb\business\BusinessAbstract,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\sial\core\mvcb\controller\exception\ControllerException;

/**
 * SIAL
 *
 * Superclasse da camada de View do SIAL
 *
 * @package br.gov.sial.core.mvcb
 * @subpackage controller
 * @author J. Augusto <augustowebd@gmail.com>
 * @todo implementar fabrica de Controller
 * */
abstract class ControllerAbstract extends SIALAbstract
{
    /**
     * @var string
     * */
    const T_CONTROLLER_BUSINESS_UNDEFINED = "A business %s não foi definida/encontrada";

    /**
     * @var string
     * */
    const T_CONTROLLER_METHOD_AFTER_BOOTSTRAP = "Método(Controller::getView) disponível apenas após o registro do bootstrap";

    /**
     * @var string
     * */
    const T_CONTROLLER_INVALID_TYPE_OF_CONTROLLER = "O tipo de controller informado não é válido para forward";

    /**
     * @var string
     * */
    const t_CONTROLLER_NON_EXISTENT_ACTION = "A action: %s, informada não existe.";

    /**
     * @var string
     * */
    const T_CONTROLLER_MISSING_BOOTSTRAP = "bootstrap não foi registrado";

    /**
     * @var string
     * */
    const T_CONTROLLERABSTRACT_SIALAPPLICATION_UNDEFINED = 'Nenhum objeto SIALApplication foi registrado na Controller';

    /**
     * Referência do bootstrap.
     *
     * @var Bootstrap
     * */
    private $_bootstrap = NULL;

    /**
     * Referência do business.
     *
     * @var BusinessAbstract
     * */
    private $_business = NULL;

    /**
     * Referência para request.
     *
     * @var br\gov\sial\core\util\Request
     * */
    private $_request = NULL;

    /**
     * Referência da view.
     *
     * @var View
     * */
    private $_view = NULL;

    /**
     * Define o tipo de saída que a view deverá gerar.
     *
     * @var string
     * */
    private $_viewType = NULL;

    /**
     * Referência da classe SIALApplication
     *
     * @var SIALApplication
     * */
    protected $_SIALApplication;

    /**
     * Objeto de configuração que será repassado para o model usado pela business.
     * <b>NOTA</b>: Se este objeto não for defindio será usado as definições do config.ini
     * <b>NOTA</b>: Para definir um config diferente do padrão, é necessário definir esta config
     *              antes de instanciar o Controller :/
     *
     * @param PersistConfig
     * */
    public static $persistConfig = NULL;

    /**
     * Construtor.
     *
     * @throws ControllerException
     * */
    public function __construct ()
    {
        $this->bootstrap();
        $this->_viewType = $this->_bootstrap->config('app.view.type');
        $this->registryViewScriptPath($this);
    }

    /**
     * Action default
     * */
    public function defaultAction ()
    {
    }

    /**
     * Define o tipo da view.
     *
     * @param string $type (html | json)
     * @return ControllerAbstract
     * @example ControllerAbstract::setViewType
     * @code
     * <?php
     *  $this->setViewType('html');
     * ?>
     * @encode
     * */
    public function setViewType ($type)
    {
        $this->_viewType = $type;
        return $this;
    }

    /**
     * Referência do bootstrap.
     *
     * @return br\gov\sial\core\Bootstrap
     * @throws ControllerException
     * @example ControllerAbstract::bootstrap
     * @code
     * <?php
     *  var_dump($this->bootstrap());
     * ?>
     * @encode
     * */
    public function bootstrap ()
    {
        if (NULL === $this->_bootstrap) {

            if (FALSE === Registry::isRegistered('bootstrap') || !(Registry::get('bootstrap') instanceof BootstrapAbstract)) {
                throw new ControllerException(self::T_CONTROLLER_MISSING_BOOTSTRAP);
            }

            $this->_bootstrap = Registry::get('bootstrap');
        }
        return $this->_bootstrap;
    }

    /**
     * Retorna o conteúdo da constant do SIAL (ConstantHandler) informada. Se o segundo param for informado será
     * assumido que a chamado foi realizando informadando apenas o nome da consntant (vide comentário da classe
     * ConstantHandler Regra#3) deixando  a cargo da classe
     * montar o nome completo.
     *
     * @param string $name
     * @param boolean $shortname
     * @return string
     * @example ControllerAbstract::constant
     * @code
     * <?php
     *  var_dump($this->constant('enviroment'));
     * ?>
     * @encode
     * */
    public function constant ($name, $shortname = TRUE)
    {
        return $this->bootstrap()->constant($name, $shortname);
    }

    /**
     * Referência do SIALApplication
     *
     * @param SIALApplication $SIALApplication
     * @return ControllerAbstract
     * */
    public function applicationRegister (SIALApplication $SIALApplication)
    {
        $this->_SIALApplication = $SIALApplication;
        return $this;
    }

    /**
     * Adiciona um evento.
     *
     * @param string $event
     * @param Function $callback
     * @return ControllerAbstract
     * */
    public function addEvent ($event, $callback)
    {
        $this->_SIALApplication->addEvent($event, $callback);
        return $this;
    }

    /**
     * Recupera um método da business referente a controller em uso.
     *
     * @param Controller $target
     * @return BusinessAbstract
     * @throws BusinessException
     * @deprecated
     * @example ControllerAbstract::getBusiness
     * @code
     * <?php
     * ...
     *
     *  try {
     *      $this->getBusiness('save')
     *           ->setViewType('html')
     *           ->getView('sucess');
     *      } catch (Exception $exp) {
     *          $this->setViewType('html')
     *               ->getView('error');
     *      }
     *
     *  ...
     * ?>
     * @encode
     * */
    public function getBusiness ($target = NULL)
    {
        $tmpBusinessTarget =
        $tmpNSBusiness     = NULL;
        $target = $this->toggle($target, $this);

        $tmpArrNS = explode('controller' . self::NAMESPACE_SEPARATOR, $this->getClassName($target));
        $tmpBusinessTarget = $this->erReplace(array('Controller$'  => 'Business'), (string) end($tmpArrNS));

        $tmpNSBusiness = $target->getNamespaceFuncionality()
                       . self::NAMESPACE_SEPARATOR . 'mvcb'
                       . self::NAMESPACE_SEPARATOR . 'business'
                       . self::NAMESPACE_SEPARATOR . $tmpBusinessTarget;

        $fileClass = Location::realpathFromNamespace($tmpNSBusiness) . '.php';

        if (!is_file($fileClass)) {
            throw BusinessException::businessNotImplemented($tmpNSBusiness);
        }

        return BusinessAbstract::factory($tmpNSBusiness)
                                ->applicationRegister($this->_SIALApplication);
    }

    /**
     * Retorna o namespace da funcionalidade do controller informado.
     * Caso o controller seja omitido será usado o controller que invocar o método.
     *
     * @param ControllerAbstract $ctrl
     * @return string
     * @example ControllerAbstract::getNamespaceFuncionality
     * @code
     * <?php
     *  // Recupera o namespace do controller em uso.
     *  var_dump($this->getNamespaceFuncionality());
     *
     *  // Recupera o namespace de um controller específico.
     *  var_dump($this->getNamespaceFuncionality('BarController'));
     * ?>
     * @encode
     * */
    public function getNamespaceFuncionality (self $ctrl = NULL)
    {
        $ctrl = $this->toggle($ctrl, $this);
        return current(preg_split("/\\\\mvcb\\\\/", $ctrl::getClassName()));
    }

    /**
     * Cria instância da View levando em consideração a especialização ou não da classe View pelo projeto.
     *
     * @return ViewAbstract
     * @throws ControllerException
     * @example ControllerAbstract::getView
     * @code
     * <?php
     *  // Busca em \mvcb\view\scripts\html o arquivo Foo.html
     *  $this->setViewType('html');
     *  $this->getView('foo');
     *
     *  // Busca em \mvcb\view\scripts\json o arquivo Bar.html
     *  $this->setViewType('json');
     *  $this->getView('bar');
     * ?>
     * @encode
     * */
    public function getView ()
    {
        # nome da view que sera instanciada
        $type = ucfirst($this->_viewType);

        if (!isset($this->_view[$type])) {
            # certifica-se de que o boostrap tenha sido inicializado
            ControllerException::throwsExceptionIfParamIsNull($this->_bootstrap, self::T_CONTROLLER_METHOD_AFTER_BOOTSTRAP);

            # complemento do namespace para o pacote view
            $view = '\mvcb\view\\';

            # namespace da library da aplicacao
            $namespace = $this->_bootstrap->config()->get('app.namespace');
            $namespace = $this->erReplace(array('application' => ''), $namespace) . 'library';

            # verifica se existe especializacao da view na aplicacao
            # caso nao exista aponta para a view da camada do SIAL/core
            if (!Location::hasClassInNamespace("{$namespace}{$view}{$type}")) {
                $namespace = '\br\gov\sial\core';
            }

            $namespace = "{$namespace}{$view}{$type}";
            $this->_view[$type] = new $namespace;
            $this->_view[$type]->registerViewScriptBasedFromController($this);
            $this->_view[$type]->set('constant', $this->_bootstrap->constantHandler());
        }

        return $this->_view[$type];
    }

    /**
     * Fábrica de ValueObject
     *
     * Cria ValueObject com base no Controller informado.
     * Um array com chave correspondente (attr=value, vide ValueObject::load)
     * poderá ser informado, assim o ValueObject criado já terá os valores definidos.
     *
     * @param Controller $target
     * @param string[] $data
     * @return ValueObjectAbstract
     * @todo refatorar este metodo colocando-o na fabrica de VO, aqui ficarah apenas a def do NS
     * @example ControllerAbstract::getValueObject
     * @code
     *  <?php
     *      // Recupera FooValueObject
     *      var_dump($this->getValueObject(FooValueObject));
     *
     *      // Recupera e gera carga inicial no BarValueObject, onde esse é composto pelos atributos co_bar e no_bar.
     *      var_dump($this->getValueObject(BarValueObject, array('co_bar' => 0123, 'no_bar' => 'bar')));
     *  ?>
     * @encode
     * */
    public function getValueObject ($target = NULL, $data = NULL)
    {
        return ValueObjectAbstract::factory($target ?: $this, $data, $this->meta());
    }

    /**
     * @return MetaAbstract
     * */
    public function meta ()
    {
        $dsName  = $this->_bootstrap->config('app.persist.default');
        $config  = $this->_bootstrap->config('app.persist')->toArray();
        $pConfig = PersistConfig::factory($dsName, $config);
        $persist = \br\gov\sial\core\persist\database\Connect::factory($pConfig);

        return MetaAbstract::factory($persist);
    }

    /**
     * @todo recarrega cache dos valuesObjects
     * */
    public function refreshCache ()
    {
    }

    /**
     * Retorna TRUE se action informada existir
     *
     * @param string $actionName
     * @return bool
     * @example ControllerAbstract::hasAction
     * @code
     *  <?php
     *      var_dump($this->hasAction('fooBar'));
     *  ?>
     * @encode
     * */
    public function hasAction ($actionName)
    {
        return $this->hasMethod($actionName);
    }

    /**
     * Retorna TRUE se algum cookie tiver sido enviado pela requisição.
     *
     * @return bool
     * @example ControllerAbstract::isCookie
     * @code
     *  <?php
     *      var_dump($this->isCookie());
     *  ?>
     * @encode
     * */
    public function isCookie ()
    {
        return $this->request()->isCookie();
    }

    /**
     * Retorna TRUE se algum param GET tiver sido enviado pela requisição.
     *
     * @return boolean
     * @example ControllerAbstract::isGet
     * @code
     *  <?php
     *      var_dump($this->isGet());
     *  ?>
     * @encode
     * */
    public function isGet ()
    {
        return $this->request()->isGet();
    }

    /**
     * Retorna TRUE se algum param POST tiver sido enviado pela requisição.
     *
     * @return bool
     * @example ControllerAbstract::isPost
     * @code
     *  <?php
     *      var_dump($this->isPost());
     *  ?>
     * @encode
     * */
    public function isPost ()
    {
        return $this->request()->isPost();
    }

    /**
     * Retorna TRUE se algum param arquivo tiver sido enviado pela requisição.
     *
     * @return bool
     * @example ControllerAbstract::isFile
     * @code
     *  <?php
     *      var_dump($this->isFile());
     *  ?>
     * @encode
     * */
    public function isFile ()
    {
        return $this->request()->isFile();
    }

    /**
     * Recupera a refência do request em Bootstrap.
     *
     * @return br\gov\sial\core\util\Request
     * @example ControllerAbstract::request
     * @code
     *  <?php
     *      var_dump($this->request());
     *  ?>
     * @encode
     * */
    public function request ()
    {
        if (NULL === $this->_request) {
            # a chamada do metodo self::bootstrap lanca uma exception para o caso do bootstrap
            # nao ter sido registrado, entao, para nao ficar verificando se tal metodo lancara
            # uma exception em todos os metodos deste controller eh realizado uma chamada a ele
            # logo no construtor e se a class for instanciada eh pq nao havera a necessidade de
            # verificações posteriores
            $this->_request = $this->bootstrap()->request();
        }

        return $this->_request;
    }

    /**
     * Este método é utilizado para redirecionar o fluxo da requisição.
     *
     * @param string $action
     * @param string $controller
     * @param string[] $params
     * @throws ControllerException
     * @example ControllerAbstract::_forward
     * @code
     *  <?php
     *      ...
     *      // Redirecionamento para método dentro do Controller em uso.
     *      public function defaultAction ()
     *      {
     *          $this->_forward('fooAction', NULL, NULL);
     *      }
     *
     *      ...
     *      // Redirecionamento para método em controller diferente do em uso.
     *      public function defaultAction ()
     *      {
     *          $this->_forward('barAction', BarController, array('bar' => TRUE));
     *      }
     *
     *      ...
     *  ?>
     * @encode
     * */
    final protected function _forward ($action, $controller = NULL, array $params = NULL)
    {
        if (NULL === $controller) {
            $controller = $this;
        } else {
            ControllerException::throwsExceptionIfParamIsNull('string' == gettype($controller),
                self::T_CONTROLLER_INVALID_TYPE_OF_CONTROLLER
            );

            $controller = new $controller();
        }

        if (NULL != $params) {
            $this->request()->setParams($params);
        }

        if(!strstr($action, 'Action')) {
            $action .= 'Action';
        }

        if (!$controller->hasAction($action)) {
            throw new ControllerException(sprintf(self::t_CONTROLLER_NON_EXISTENT_ACTION, $action));
        }

        # @todo remover este die() e implementar com o controle de dispatcher
        // @codeCoverageIgnoreStart
        die($controller->$action());
        // @codeCoverageIgnoreEnd
    }

    /**
     * Com base no controller informado registra seu repositório de script.
     *
     * @param \br\gov\sial\core\mvcb\controller\ControllerAbstract $ctrl
     * @return \br\gov\sial\core\mvcb\controller\ControllerAbstract
     * @example ControllerAbstract::registryViewScriptPath
     * @code
     *  <?php
     *      ...
     *      class FooController extends ControllerAbstract ()
     *      {
     *      }
     *      ...
     *      $fooController = new FooController();
     *      $this->registryViewScriptPath($fooController);
     *  ?>
     * @encode
     * */
    public function registryViewScriptPath (ControllerAbstract $ctrl)
    {
        $this->getView()->registerViewScriptBasedFromController($this);
        return $this;
    }
}