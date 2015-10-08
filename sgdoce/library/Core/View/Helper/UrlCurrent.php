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
 * @package    Core
 * @subpackage View
 * @subpackage Helper
 * @name       Menu
 * @category   View Helper
 */
class Core_View_Helper_UrlCurrent extends Zend_View_Helper_Url
{
    public function urlCurrent($urlOptions = array(), $name = null)
    {
        if (!isset($urlOptions['module'])) {
            $urlOptions['module'] = $this->_getRequest()->getModuleName();
        }

        if (!isset($urlOptions['controller'])) {
            $urlOptions['controller'] = $this->_getRequest()->getControllerName();
        }

        if (!isset($urlOptions['action'])) {
            $urlOptions['action'] = $this->_getRequest()->getActionName();
        }

        return $this->url($urlOptions, $name, true);
    }

    protected function _getRequest()
    {
        return Zend_Controller_Front::getInstance()->getRequest();
    }
}