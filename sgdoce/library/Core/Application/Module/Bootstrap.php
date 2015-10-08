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
 * @see Core_Application_Bootstrap_Bootstrap
 */
/**
 * Registra e disponibiliza os recursos necessários para o módulo
 *
 * @package    Core
 * @subpackage Application
 * @subpackage Module
 * @name       Bootstrap
 * @category   Bootstrap
 */
abstract class Core_Application_Module_Bootstrap
    extends Core_Application_Bootstrap_Bootstrap
{

    /**
     * Set this explicitly to reduce impact of determining module name
     * @var string
     */
    protected $_moduleName;

    /**
     * Constructor
     *
     * @param  Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application
     * @return void
     */
    public function __construct($application)
    {
        $this->setApplication($application);

        // Use same plugin loader as parent bootstrap
        if ($application instanceof Zend_Application_Bootstrap_ResourceBootstrapper) {
            $this->setPluginLoader($application->getPluginLoader());
        }

        $key = strtolower($this->getModuleName());
        if ($application->hasOption($key)) {
            // Don't run via setOptions() to prevent duplicate initialization
            $this->setOptions($application->getOption($key));
        }

        if ($application->hasOption('resourceloader')) {
            $this->setOptions(array(
                'resourceloader' => $application->getOption('resourceloader')
            ));
        }
        $this->initResourceLoader();

        // ZF-6545: ensure front controller resource is loaded
        if (!$this->hasPluginResource('FrontController')) {
            $this->registerPluginResource('FrontController');
        }

        // ZF-6545: prevent recursive registration of modules
        if ($this->hasPluginResource('modules')) {
            $this->unregisterPluginResource('modules');
        }
    }

    /**
     * Ensure resource loader is loaded
     *
     * @return void
     */
    public function initResourceLoader()
    {
        $this->getResourceLoader();
    }

    /**
     * Get default application namespace
     *
     * Proxies to {@link getModuleName()}, and returns the current module
     * name
     *
     * @return string
     */
    public function getAppNamespace()
    {
        return $this->getModuleName();
    }

    /**
     * Retrieve module name
     *
     * @return string
     */
    public function getModuleName()
    {
        if (empty($this->_moduleName)) {
            $class = get_class($this);
            if (preg_match('/^([a-z][a-z0-9]*)_/i', $class, $matches)) {
                $prefix = $matches[1];
            } else {
                $prefix = $class;
            }
            $this->_moduleName = $prefix;
        }
        return $this->_moduleName;
    }
}
