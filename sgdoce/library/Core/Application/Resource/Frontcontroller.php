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
class Core_Application_Resource_Frontcontroller extends Zend_Application_Resource_Frontcontroller
{
    /**
     * @return Zend_Controller_Plugin_Abstract
     */
    public function init()
    {
        $front = $this->getFrontController();

        $options = $this->getOptions();

        if (array_key_exists('plugins', $options)) {
            foreach ((array) $options['plugins'] as $pluginClass) {
                $stackIndex = null;
                $_options = array();
                if (is_array($pluginClass)) {
                    $pluginClass = array_change_key_case($pluginClass, CASE_LOWER);

                    if (isset($pluginClass['options'])) {
                        $_options = $pluginClass['options'];
                    }

                    if (isset($pluginClass['class'])) {
                        if(isset($pluginClass['stackindex'])) {
                            $stackIndex = $pluginClass['stackindex'];
                        }

                        $pluginClass = $pluginClass['class'];
                    }
                }

                $plugin = new $pluginClass($_options);
                $front->registerPlugin($plugin, $stackIndex);
                unset($options['plugins']);
            }
        }

        $this->_options = $options;

        return parent::init();
    }
}
