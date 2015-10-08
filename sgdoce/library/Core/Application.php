<?php
/**
 * Copyright 2012 do ICMBio
 *
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
/**
 * @uses        Zend_Application
 * @package     Core
 * @subpackage  Application
 * @name        Application
 * @category    Application
 */
class Core_Application extends Zend_Application
{
    /**
     * @var Zend_Cache_Core
     */
    protected static $_cache = null;

    /**
     * @var array
     */
    protected static $_defaultFrontendBackendOptions = array(
        'frontend'        => 'File',
        'frontendOptions' => array(
            'automatic_serialization'     => true,
            'master_files'                => array('./../application/configs/application.ini'),
            'lifetime'                    => 86400
        ),
        'backend'         => 'Apc',
        'backendOptions'  => array()
    );

    /**
     * @var array
     */
    protected $_filesRead = array();

    /**
     * @see Zend_Application::setOptions()
     * @param  string                   $environment
     * @param  string|array|Zend_Config $options String path to configuration file, or array/Zend_Config of configuration options
     * @return void
     */
    public function __construct($environment, $options = null)
    {
        $cached  = false;
        $cache   = false;

        require_once 'Zend/Loader/Autoloader.php';
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Core');

        require_once 'Doctrine/Common/ClassLoader.php';
        $fmmAutoloader = new \Doctrine\Common\ClassLoader('Bisna');
        $autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'Bisna');

        if (is_string($options)) {

            $this->_appendFileRead($options);

            if (self::hasCache()) {

                $cache    = self::getCache();
                $idCache  = 'SGDOCe_Main_Application_Config';
                $_options = self::_loadCache($idCache);

                if (count($_options)) {
                    $cached  = true;
                    $options = $_options;
                    unset($_options);
                }
            }
        }

        parent::__construct($environment, $options);

        if (false === $cached && $cache) {

            $options = $this->getOptions();

            $data = array(
                'options'    => $options,
                'filesRead'  => array_unique($this->_filesRead)
            );

            $cache->save($data, $idCache);
        }
    }

    public static function hasCache()
    {
        try {
            return self::getCache() instanceof Zend_Cache_Core;
        } catch (Zend_Cache_Exception $e) {
            return false;
        }
    }

    protected function _loadCache($idCache)
    {
        $cache          = self::getCache();
        $options        = array();

        if ($cache->getBackend()->test($idCache)) {

            $dataCache = $cache->load($idCache, true);
            $options   = (array) $dataCache['options'];
        }

        return $options;
    }

    /**
     * @see    Zend_Application::setOptions()
     * @param  array $options
     * @return Core_Application provides a fluent interface
     */
    public function setOptions(array $options)
    {
        if (!empty($options['config'])) {
            $options = $this->_mergeFilesRecursive($options);
            unset($options['config']);
        }

        if (!empty($options['pluginCache'])) {
            if (!is_string($options['pluginCache'])) {
                throw new Zend_Application_Exception('Plugin cache deve ser string');
            }

            Zend_Loader_PluginLoader::setIncludeFileCache($options['pluginCache']);

            if (file_exists($options['pluginCache'])) {
                include_once $options['pluginCache'];
            }
        }

        return parent::setOptions($options);
    }

    /**
     * @param  Zend_Cache_Core $cache
     * @return void
     */
    public static function setCache(Zend_Cache_Core $cache)
    {
        self::$_cache = $cache;
    }

    /**
     * @return Zend_Cache_Core
     */
    public static function getCache()
    {
        if (null === self::$_cache) {
            $options = self::getDefaultFrontendBackendOptions();

            self::$_cache = Zend_Cache::factory(
                $options['frontend'],
                $options['backend'],
                $options['frontendOptions'],
                $options['backendOptions']
            );
        }

        return self::$_cache;
    }

    /**
     * @param  array $options
     * @param  boolean $merge
     * @throws Core_Application_Exception
     */
    public static function setDefaultFrontendBackendOptions(array $options, $merge = false)
    {
        if (!$merge) {
            if (!isset($options['frontend'])) {
                require_once 'Core/Application/Exception.php';
                throw new Core_Application_Exception("Está faltando a opção 'frontend' que é requerida.");
            }

            if (!isset($options['backend'])) {
                require_once 'Core/Application/Exception.php';
                throw new Core_Application_Exception("Está faltando a opção 'backend' que é requerida.");
            }

            if (!isset($options['frontendOptions'])) {
                $options['frontendOptions'] = array();
            }

            if (!isset($options['backendOptions'])) {
                $options['backendOptions'] = array();
            }

            self::$_defaultFrontendBackendOptions = $options;
        } else {
            if (isset($options['frontend'])) {
                self::$_defaultFrontendBackendOptions['frontend'] = $options['frontend'];
            }

            if (isset($options['backend'])) {
                self::$_defaultFrontendBackendOptions['backend'] = $options['backend'];
            }

            if (isset($options['frontendOptions'])) {
                foreach ((array) $options['frontendOptions'] as $key => $option) {
                    self::$_defaultFrontendBackendOptions['frontendOptions'][$key] = $option;
                }
            }

            if (isset($options['backendOptions'])) {
                foreach ((array) $options['backendOptions'] as $key => $option) {
                    self::$_defaultFrontendBackendOptions['backendOptions'][$key] = $option;
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function getDefaultFrontendBackendOptions()
    {
        return self::$_defaultFrontendBackendOptions;
    }

    /**
     * @param  array $options
     * @return array
     */
    protected function _mergeFilesRecursive (array $options)
    {
        if (is_array($options['config'])) {
            $_options = array();

            foreach ($options['config'] as $tmp) {
                $optionsFile = $this->_loadConfig($tmp);
                $this->_appendFileRead($tmp);

                if (!empty($optionsFile['config'])) {
                    $optionsFile = $this->_mergeFilesRecursive($optionsFile);
                }
                $_options = $this->mergeOptions($_options, $optionsFile);
            }

            $options = $this->mergeOptions($options, $_options);
        } else {
            $optionsFile = $this->_loadConfig($options['config']);
            $this->_appendFileRead($options['config']);

            if (!empty($optionsFile['config'])) {
                $optionsFile = $this->_mergeFilesRecursive($optionsFile);
            }

            $options = $this->mergeOptions($options, $optionsFile);
        }

        return $options;
    }

    protected function _appendFileRead ($file)
    {
        if (!in_array($file, $this->_filesRead)) {
            $this->_filesRead[] = $file;
        }
    }


    /**
     * Get bootstrap object
     *
     * @return Zend_Application_Bootstrap_BootstrapAbstract
     */
    public function getBootstrap ()
    {
        if (null === $this->_bootstrap) {
            $this->_bootstrap = new Core_Application_Bootstrap_Bootstrap($this);
        }
        return $this->_bootstrap;
    }
}