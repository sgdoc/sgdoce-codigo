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
namespace br\gov\sial\core\mvcb\view;
use br\gov\sial\core\saf\SAFHTML,
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\Registry,
    br\gov\sial\core\util\Location,
    br\gov\sial\core\mvcb\view\Helper,
    br\gov\sial\core\mvcb\view\exception\ViewException,
    br\gov\sial\core\exception\IndexOutOfBoundsException,
    br\gov\sial\core\mvcb\controller\ControllerAbstract;

/**
 * SIAL
 *
 * Superclasse da Camada de visualizacao do SIAL
 *
 * @package br.gov.sial.core.mvcb
 * @subpackage view
 * @name View
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class ViewAbstract extends SIALAbstract
{
    /**
     * @var string
     * */
    const T_TYPE = NULL;

    /**
     * Extensão padrao do arquivo viewScript
     *
     * @var string
     * */
    const T_EXTENSION = NULL;

    /**
     * @var string
     * */
    const T_VIEWABSTRACT_STR_ATTR_UNAVAILABLE = '%s::$%s não está definido/disponível';

    /**
     * @deprecated
     * @var string
     */
    protected $_TYPE = NULL;

    /**
     * Define ou não o mime na saida da view
     *
     * @var boolean
     * */
    public static $forceMime = FALSE;

    /**
     * @var Config
     * */
    protected $_config;

    /**
     * @var Helper
     * */
    protected $_helper;

    /**
     * Adatapter do SIAL Appllication Form para view.
     *
     * @var SAFViewAdapter
     * */
    protected $_saf;

    /**
     * @var Slot
     * */
    protected $_slot;

    /**
     * Armazena relação de diretórios comuns, sao considerados diretorios comuns os diretorios
     * que atendam a todo o projeto e ao modulo especificamente.
     *
     * @var string
     * */
    protected $_commonDir = array();

    /**
     * @var string
     * */
    protected $_encode = 'UTF-8';

    /**
     * Armazena caminho dos scripts usado para renderizar as telas
     *
     * @param string[] $path
     * */
    protected $_scriptPath = array();

    /**
     * Armazena dados que serão usados nos viewScripts
     *
     * @var mixed[]
     * */
    protected $_data = array();

    /**
     * Construtor.
     *
     * @param string $viewType
     * @param string[] $config
     * @example ViewAbstract::__construct
     * @code
     * <?php
     *      ...
     *      $result = new FooView('html', array());
     *      var_dump($result);
     * ?>
     * @encode
     * */
    public function __construct ($viewType, array $config = array())
    {
        $this->_TYPE = $viewType;
        $this->_config = empty($config) ? Registry::get('bootstrap')->config() : $config;

        // # registro scripts basicos do sial para o tipo de view selecionado
        $this->addScriptPath(__DIR__ . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . $this::T_TYPE);

        # registra o repositorio principal de helpers
        Helper::registerNamespace(__NAMESPACE__ . self::NAMESPACE_SEPARATOR . 'helper');

        # inicializa o helper de view
        $this->_helper = new Helper();

        # define o cabecalho (MIME-TYPE) do tipo de view
        if ((boolean) self::$forceMime) {
            $this->mime();
        }

        # criar instancia do SAF se o mesmo tiver sido habilidado no arquivo de configuracao do projeto:
        # .project/application/config/config.ini
        $safConf = $this->_config->get('app.saf');
        if ($safConf && $safConf->get('enable')) {
            $this->saf();
        }

        # mantem compatibilidade, a chamada a _commonDir nao deve
        # ser feita diretamente das subclasses
        # @deprecated
        if (isset($this->_commonDir[$this::T_TYPE])) {
            foreach ($this->_commonDir[$this::T_TYPE] as $dir) {
                $this->addScriptPath($dir);
            }
        }
    }

    /**
     * @param string $key
     * @return mixed
     * @throws ViewException
     * @example ViewAbstract::__get
     * @code
     * <?php
     *      ...
     *      $this->__get('slot');
     *      ...
     * ?>
     * @encode
     * */
    public function __get ($key)
    {
        # inicializa Slot na primeira vez que for invocado
        if ('slot' == $key) {
            if (NULL == $this->_slot) {
                $this->initSlot();
            }

            return $this->_slot;
        }

        if (!$this->isAssign($key)) {
            return NULL;
        }

        return $this->get($key);
    }

    /**
     * Proxy para o helper
     *
     * @param string $helper
     * @param mixed $args
     * @return mixed
     * @example ViewAbstract::__call
     * @code
     * <?php
     *      ...
     *      $this->__call('fooHelper', array('foo' => 1, 'bar' => 2));
     *      ...
     * ?>
     * @encode
     * */
    public function __call ($helper, $args)
    {
        Helper::has($helper);
        return call_user_func_array(array($this->_helper, $helper), $args);
    }

    /**
     * @return ConfigAbstract
     * @example ViewAbstract::config
     * @code
     * <?php
     *      ...
     *      var_dump($this->config());
     *      ...
     * ?>
     * @encode
     * */
    public function config ()
    {
        return $this->_config;
    }

    /**
     * Mime do tipo de view.
     *
     * @return ViewAbstract
     * @example ViewAbstract::mime
     * @code
     * <?php
     *      ...
     *      var_dump($this->mime());
     *      ...
     * ?>
     * @encode
     * */
    public function mime ()
    {
        header(sprintf('Content-Type: text/%s; charset=%s', $this::T_TYPE, $this->_encode));
        return $this;
    }

    /**
     * Cria instância do SIAL Application Form (SAF)
     *
     * @return ViewAbstract
     * */
    public abstract function saf ();

    /**
     * Adiciona novo diretório de scripts
     *
     * @param string $scriptPath
     * @return br\gov\sial\core\mvcb\view\ViewAbstract
     * @example ViewAbstract::addScriptPath
     * @code
     * <?php
     *      ...
     *      var_dump($this->addScriptPath('fooScriptLocation'));
     *      ...
     * ?>
     * @encode
     * */
    public function addScriptPath ($scriptPath)
    {
        if (!in_array($scriptPath, $this->_scriptPath)) {
            $this->_scriptPath[] = $scriptPath;
        }

        return $this;
    }

    /**
     * Desregistra todas as variáveis.
     *
     * @return br\gov\sial\core\mvcb\view\ViewAbstract
     * @example ViewAbstract::clean
     * @code
     * <?php
     *      ...
     *      var_dump($this->clean());
     *      ...
     * ?>
     * @encode
     * */
    public function clean ()
    {
        $this->_data = array();
        return $this;
    }

    /**
     * Recupera valor armazenado na view.
     *
     * @param string $key
     * @return mixed
     * @throws IndexOutOfBoundsException
     * @example ViewAbstract::get
     * @code
     * <?php
     *      ...
     *      var_dump($this->get('foo'));
     *      ...
     * ?>
     * @encode
     * */
    public function get ($key)
    {
        IndexOutOfBoundsException::throwsExceptionIfParamIsNull(
            $this->isAssign($key), sprintf(self::T_VIEWABSTRACT_STR_ATTR_UNAVAILABLE, ucfirst($this::T_TYPE), $key)
        );

        return $this->_data[$key];
    }

    /**
     * Registra variável.
     *
     * @param string $key
     * @param mixed $value
     * @return br\gov\sial\core\mvcb\view\ViewAbstract
     * @example ViewAbstract::set
     * @code
     * <?php
     *      ...
     *      var_dump($this->set('foo', 123456));
     *      ...
     * ?>
     * @encode
     * */
    public function set ($key, $value)
    {
        $this->_data[(string) $key] = $value;
        return $this;
    }

    /**
     * Recupera lista com todas variáveis registradas na view.
     *
     * @return mixed[]
     * @example ViewAbstract::getAll
     * @code
     * <?php
     *      ...
     *      var_dump($this->getAll());
     *      ...
     * ?>
     * @encode
     * */
    public function getAll ()
    {
        return $this->_data;
    }

    /**
     * Inicializa o componente Slot.
     * */
    public abstract function initSlot ();

    /**
     * Verifica se a variável informada esta registrada.
     *
     * @param string $key
     * @return bool
     * @example ViewAbstract::isAssign
     * @code
     * <?php
     *      ...
     *      $this->set('bar', 123456);
     *      var_dump($this->isAssign('foo'));
     *      ...
     * ?>
     * @encode
     * */
    public function isAssign ($key)
    {
        return array_key_exists($key, $this->_data);
    }

    /**
     * Imprime o conteúdo indicado.
     *
     * @param string $key
     * @return void
     * @example ViewAbstract::output
     * @code
     * <?php
     *      ...
     *      $this->set('foo', 123456);
     *      var_dump($this->output('foo'));
     *      ...
     * ?>
     * @encode
     * */
    public function output ($key)
    {
        ;
    }

    /**
     * Remove uma variável registrada na view.
     *
     * @param string $key
     * @return br\gov\sial\core\mvcb\view\ViewAbstract
     * @example ViewAbstract::remove
     * @code
     * <?php
     *      ...
     *      $this->set('foo', 123456);
     *      $this->remove('foo');
     *      $this->isAssign('foo');
     *      ...
     * ?>
     * @encode
     * */
    public function remove ($key)
    {
        if ($this->isAssign($key)) {
            unset($this->_data[$key]);
        }

        return $this;
    }

    /**
     * Renderiza o template informado por $script
     *
     * @access public
     * @param string $template
     * @return string
     * @throws ViewException
     * @codeCoverageIgnoreStart
     * */
    public abstract function render ($script);
    // @codeCoverageIgnoreEnd

    /**
     * Recupera a página de codificação a ser utilizada.
     *
     * @return string
     * @example ViewAbstract::getEncoding
     * @code
     * <?php
     *      ...
     *      var_dump($this->getEncoding());
     *      ...
     * ?>
     * @encode
     * */
    public function getEncoding ()
    {
        return $this->_encode;
    }

    /**
     * Define a página de codificação a ser utilizada.
     *
     * @param string $encode
     * @return br\gov\sial\core\mvcb\view\ViewAbstract
     * @example ViewAbstract::setEncoding
     * @code
     * <?php
     *      ...
     *      $this->setEncoding('UTF-8');
     *      var_dump($this->getEncoding());
     *      ...
     * ?>
     * @encode
     * */
    public function setEncoding ($encode)
    {
        $this->_encode = $encode;
        return $this;
    }

    /**
     * Define o caminho dos script/templates.
     *
     * @param string $scriptPath
     * @return br\gov\sial\core\mvcb\view\ViewAbstract
     * @example ViewAbstract::setScriptPath
     * @code
     * <?php
     *      ...
     *      $this->setScriptPath('fooScriptPath');
     *      ...
     * ?>
     * @encode
     * */
    public function setScriptPath ($scriptPath)
    {
        $this->_scriptPath = array();
        $this->_scriptPath[] = $scriptPath;
        return $this;
    }

    /**
     * Retorna os caminho dos scripts/templates.
     *
     * @return string[]
     * @example ViewAbstract::getScriptPaths
     * @code
     * <?php
     *      ...
     *      var_dump($this->getScriptPaths());
     *      ...
     * ?>
     * @encode
     * @codeCoverageIgnoreStart
     * */
    public function getScriptPaths ()
    {
        return $this->_scriptPath;
    }

    /**
     * Registra o caminho dos scripts de view baseando-se no controller informado.
     *
     * @param br\gov\sial\core\mvcb\controller\ControllerAbstract $ctrl
     * @return br\gov\sial\core\mvcb\view\ViewAbstract
     * @example ViewAbstract::registerViewScriptBasedFromController
     * @code
     * <?php
     *      ...
     *      class FooController extends ControllerAbstract ()
     *      {
     *      }
     *      ...
     *      $fooController = new FooController();
     *      $this->registerViewScriptBasedFromController($fooController);
     * ?>
     * @encode
     * */
    public function registerViewScriptBasedFromController (ControllerAbstract $ctrl)
    {
        $scriptPath = current(explode('mvcb', $ctrl->getClassName()))
                    . 'mvcb'    . self::NAMESPACE_SEPARATOR
                    . 'view'    . self::NAMESPACE_SEPARATOR
                    . 'scripts' . self::NAMESPACE_SEPARATOR
                    . $this::T_TYPE;

        $this->addScriptPath(Location::realpathFromNamespace($scriptPath));
        return $this;
    }

    /**
     * Fábrica de view.
     *
     * O parâmetro type define o tipo de view a ser criada (html, gtk, etc)
     *
     * @param string $type
     * @param string $namespace
     * @return ViewAbstract
     * @example ViewAbstract::factory
     * @code
     * <?php
     *      ...
     *     $fooView = ViewAbstract::factory('html', '\example\mvcb\view\');
     *     var_dump($fooView);
     * ?>
     * @encode
     * */
    public static function factory ($type, $namespace)
    {
       return $namespace::factory($type);
    }
}