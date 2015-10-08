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
 * @see Core_Loader_Autoloader_Resource
 */
/**
 * Registra e disponibiliza os recursos necessários para o módulo
 *
 * @package    Core
 * @subpackage Application
 * @subpackage Module
 * @name       Autoloader
 * @category   Autoloader
 */
class Core_Application_Module_Autoloader extends Core_Loader_Autoloader_Resource
{
    /**
     * Constructor
     *
     * @param  array|Zend_Config $options
     * @return void
     */
    public function __construct($options)
    {
        parent::__construct($options);
        $this->initDefaultResourceTypes();
    }

    /**
     * Initialize default resource types for module resource classes
     *
     * @return void
     */
    public function initDefaultResourceTypes()
    {
        $separator = $this->getNamespaceSeparator();
        $basePath = $this->getBasePath();
        $this->addResourceTypes(array(
            'dbtable' => array(
                'namespace' => "Model{$separator}DbTable",
                'path'      => 'models/DbTable',
            ),
            'mappers' => array(
                'namespace' => "Model{$separator}Mapper",
                'path'      => 'models/mappers',
            ),
            'entity' => array(
                'namespace' => "Model{$separator}Entity",
                'path'      => 'models/entity',
            ),
            'repository' => array(
                'namespace' => "Model{$separator}Repository",
                'path'      => 'models/repository',
            ),
            'form'    => array(
                'namespace' => 'Form',
                'path'      => 'forms',
            ),
            'model'   => array(
                'namespace' => 'Model',
                'path'      => 'models',
            ),
            'plugin'  => array(
                'namespace' => 'Plugin',
                'path'      => 'plugins',
            ),
            'service' => array(
                'namespace' => 'Service',
                'path'      => 'services',
            ),
            'viewhelper' => array(
                'namespace' => "View{$separator}Helper",
                'path'      => 'views/helpers',
            ),
            'viewfilter' => array(
                'namespace' => "View{$separator}Filter",
                'path'      => 'views/filters',
            ),
        ));
        $this->setDefaultResourceType('model');
    }
}
