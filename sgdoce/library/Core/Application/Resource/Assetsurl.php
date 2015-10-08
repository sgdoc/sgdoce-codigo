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
 * Registra a configuração das chamadas de URLs da aplicação
 *
 * @package    Core
 * @subpackage Application
 * @subpackage Resource
 * @name       Assetsurl
 * @category   Resource
 */
class Core_Application_Resource_Assetsurl extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @return Zend_Controller_Plugin_Abstract
     */
    public function init()
    {
        $options = $this->getOptions();

        unset(
            $options['defaults'],
            $options['defaultpreffix'],
            $options['defaultsuffix'],
            $options['addressdefault']
        );

        foreach ($options as $type => $address) {
            foreach ((array) $address as $key => $value) {
                Core_View_Helper_AssetUrl::setAddress($type, $key, $value);
            }
        }

        return $this;
    }

    public function setDefaults()
    {
        if (!$this->_bootstrap->hasPluginResource('view')) {
            return;
        }

        $this->_bootstrap->bootstrap('view');
        $view = $this->_bootstrap->getResource('view');
        $this->setAddressDefault(array(
            'img' =>  'img',
            'css' =>  'css',
            'js'  =>  'js'
        ));
    }

    public function setAddressDefault($address)
    {
        foreach ($address as $type => $value) {
            Core_View_Helper_AssetUrl::setDefaultAddress($type, $value);
        }

        return $this;
    }

    public function setDefaultSuffix($suffix)
    {
        if (is_array($suffix)) {
            # Adicionando a versão da aplicação?
            if (isset($suffix['appVersionPath'])) {
                $version = Core_Application_Version::getVersionFromPath($suffix['appVersionPath']);
                # Adicionando a versão da aplicação de acordo com o
                # Item: 3.3. HTTP da
                # RFC 1738 - Uniform Resource Locators (URL)
                # Fonte: http://www.faqs.org/rfcs/rfc1738.html
                $suffix = sprintf('_vs%s',trim($version));
            }
        }

        Core_View_Helper_AssetUrl::setDefaultSuffix($suffix);
        return $this;
    }
}
