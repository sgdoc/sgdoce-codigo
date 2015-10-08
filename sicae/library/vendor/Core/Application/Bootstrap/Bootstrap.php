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
 * @see Zend_Application_Bootstrap_Bootstrap
 */
/**
 * Registra e disponibiliza os recursos necessários para a aplicação
 *
 * @package    Core
 * @subpackage Application
 * @subpackage Bootstrap
 * @name       Bootstrap
 * @category   Bootstrap
 */
class Core_Application_Bootstrap_Bootstrap
    extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Retrieve module resource loader
     *
     * @return Zend_Loader_Autoloader_Resource
     */
    public function getResourceLoader()
    {
        if ((null === $this->_resourceLoader)
            && (false !== ($namespace = $this->getAppNamespace()))
        ) {
            $r    = new ReflectionClass($this);
            $path = $r->getFileName();
            $this->setResourceLoader(new Core_Application_Module_Autoloader(array(
                'namespace' => $namespace,
                'basePath'  => dirname($path),
            )));
        }
        return $this->_resourceLoader;
    }

    /**
     * @inheritdoc
     */
    public function getPluginLoader()
    {
        if (null === $this->_pluginLoader) {
            parent::getPluginLoader();
            $this->_pluginLoader->addPrefixPath(
                'Core_Application_Resource',
                'Core/Application/Resource'
            );
        }

        return $this->_pluginLoader;
    }

    protected function _initAcl()
    {
        $session = new Core_Session_Namespace('USER', FALSE, TRUE);
        if (isset($session->acl)) {
            Core_Registry::setAcl($session->acl);
            return $session->acl;
        }
    }

    protected function _initValidateMessages()
    {
        if (!$this->hasPluginResource('message')) {
            return;
        }

        $this->bootstrap('message');
        Zend_Validate_Abstract::setDefaultTranslator($this->getResource('message'));
    }
}
