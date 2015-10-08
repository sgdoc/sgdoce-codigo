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
 * @see Zend_Application_Resource_ResourceAbstract
 */
/**
 * Registra e disponibiliza o ZFDebug para a aplicação
 *
 * @package    Core
 * @subpackage Application
 * @subpackage Resource
 * @name       Zfdebug
 * @category   Resource
 */
class Core_Application_Resource_Zfdebug extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @return Zend_Controller_Plugin_Abstract
     */
    public function init()
    {
        $options = $this->getOptions();

        if (isset($options['disable']) && $options['disable']) {
            return '';
        }

        // initialize namespace
        $this->getBootstrap()
             ->getApplication()
             ->getAutoloader()
             ->registerNamespace('ZFDebug');

        // prepare options to plugins
        if (isset($options['plugins']) && is_array($options['plugins'])) {
            foreach ($options['plugins'] as $plugin => $option) {
                $separateClasses = explode('_', $plugin);
                $prepareName     = strtolower(end($separateClasses));
                $methodPrepare = "_prepareOptionsPlugin$prepareName";
                if (method_exists($this, $methodPrepare)) {
                    $options['plugins'][$plugin] = $this->$methodPrepare($option);
                }
            }
        }

        // initalize frontController
        $this->_bootstrap->bootstrap('frontController');

        //get front controller
        $front = $this->_bootstrap->getResource('frontController');

        $zfdebug = new ZFDebug_Controller_Plugin_Debug($options);
        $front->registerPlugin($zfdebug);

        return $zfdebug;
    }

    /**
     * @param array|null $options
     * @return array
     */
    protected function _prepareOptionsPluginCache($options = null)
    {
        if (isset($options['backend'])) {
            if (!is_array($options['backend'])) {
                $options['backend'] = array($options['backend']);
            }

            foreach ($options['backend'] as $key => $backend) {
                if ($backend instanceof Zend_Cache_Core) {
                    $options['backend'][$key] = $backend->getBackend();
                } else if (is_string($backend) && $this->_bootstrap->hasPluginResource('cachemanager')) {
                    $this->_bootstrap->bootstrap('cachemanager');
                    $cacheManager = $this->_bootstrap->getResource('cachemanager');
                    if ($cacheManager->hasCache($backend)) {
                        $options['backend'][$key] = $cacheManager->getCache($backend)->getBackend();
                    }
                }
            }
        } else {
            if ($this->_bootstrap->hasPluginResource('cachemanager')) {
                $this->_bootstrap->bootstrap('cachemanager');
                $cacheManager = $this->_bootstrap->getResource('cachemanager');
                $cachesCore   = $cacheManager->getCaches();
                foreach ($cachesCore as $key => $cacheCore) {
                    $options['backend'][$key] = $cacheCore->getBackend();
                }
            }
        }

        return $options;
    }

    protected function _prepareOptionsPluginDoctrine2($options = null)
    {
        $this->getBootstrap()->bootstrap('doctrine');
        $plugin   = $this->getBootstrap()->getResource('doctrine');

        if (!isset($options['entityManagers'])) {
            return array('entityManagers' => $plugin->getEntityManager());
        }

        if (!is_array($options['entityManagers'])) {
            return array('entityManagers' => $plugin->getEntityManager());
        }

        if (0 === count($options['entityManagers'])) {
            return array('entityManagers' => $plugin->getEntityManager());
        }

        $managers = array();

        foreach ($options['entityManagers'] as $key => $name) {
            $managers[$key] = $plugin->getEntityManager($name);
        }

        return array('entityManagers' => $managers);
    }

    /**
     * @param array|null $options
     * @return array
     */
    protected function _prepareOptionsPluginDatabase($options = null)
    {
        if ($this->_bootstrap->hasPluginResource('multidb')) {

            $this->_bootstrap->bootstrap('multidb');
            $multiDb = $this->_bootstrap->getResource('multidb');

            if (isset($options['adapter'])) {
                if (!is_array($options['adapter'])) {
                    $options['adapter'] = array($options['adapter']);
                }

                foreach ($options['adapter'] as $key => $adapter) {
                    if (is_string($adapter)) {
                        $options['adapter'][$key] = $multiDb->getDb($adapter);
                    }
                }
            } else {
                $optionsMultiDb = $multiDb->getOptions();

                if (isset($optionsMultiDb['defaultMetadataCache'])) {
                    unset($optionsMultiDb['defaultMetadataCache']);
                }

                foreach ($optionsMultiDb as $id => $adapter) {
                    $options['adapter'][$id] = $multiDb->getDb($id);
                }
            }
        }

        return $options;
    }
}
