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
class Core_Application_Resource_Container extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @return Zend_Controller_Plugin_Abstract
     */
    public function init()
    {
        $containers  = $this->getOptions();

        foreach ($containers as $keyContainer => $container) {
            if (!isset($container['class'])) {
                continue;
            }

            $configs      = isset($container['configs'])
                          ? $container['configs']
                          : array();

            $configs += array(
                'options' => array(),
                'class'   => 'Core_Model_OWM_Configuration',
            );

            $objectConfig = new $configs['class'];

            foreach ($configs['options'] as $key => $config) {
                $methodSet = 'set' . $key;
                $methodAdd = 'add' . $key;
                if (method_exists($objectConfig, $methodSet) ||
                    method_exists($objectConfig, $methodAdd)) {
                    $objectConfig->$methodSet($config);
                }
            }

            unset($configs['class'], $configs['options']);
            $objectConfig->setConfigs($configs);
            $container = $container['class']::create($container['options'], $objectConfig);
            Core_Registry::setContainer($keyContainer, $container);
        }

        return Core_Registry::getContainers();
    }
}
