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
 * @name       TitlePage
 * @category   View Helper
 */
class Core_View_Helper_PageTitle extends Zend_View_Helper_Abstract
{
    protected $_translator;

    public function pageTitle($title = NULL)
    {
        if (NULL === $title && isset($this->view->pageTitle)) {
            $title = $this->view->pageTitle;
        }

        if (NULL === $title) {
            $title = $this->_getTitle();
        }

        $translator = $this->getTranslator();
        if ($translator) {
            $title = $translator->_($title);
        }

        if ($title) {
            return '<h1 class="title-main">' . $title . '</h1>';
        }

        return NULL;
    }

    public function setTranslator($translator = NULL)
    {
        if ((null === $translator) || ($translator instanceof Zend_Translate_Adapter)) {
            $this->_translator = $translator;
        } elseif ($translator instanceof Zend_Translate) {
            $this->_translator = $translator->getAdapter();
        } else {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception('Invalid translator specified');
        }

        return $this;
    }

    protected function _getTitle()
    {
        $translator = $this->getTranslator();

        if (!$translator) {
            return NULL;
        }

        $request = Zend_Controller_Front::getInstance()
                          ->getRequest();

        $controller = $request->getControllerName();
        $action     = $request->getActionName();

        if ($translator->isTranslated($controller . '_' . $action)) {
            return $controller . '_' . $action;
        }

        if ($translator->isTranslated($action)) {
            return $action;
        }

        return NULL;
    }

    /**
     * Return translation object
     *
     * @return Zend_Translate_Adapter|null
     */
    public function getTranslator()
    {
        if (null === $this->_translator) {
            $this->setTranslator(static::getDefaultTranslator());
        }

        return $this->_translator;
    }

    public function getDefaultTranslator()
    {
        if (Zend_Registry::isRegistered('Zend_Translate')) {
            $translator = Zend_Registry::get('Zend_Translate');
            if ($translator instanceof Zend_Translate_Adapter) {
                return $translator;
            } elseif ($translator instanceof Zend_Translate) {
                return $translator->getAdapter();
            }
        }

        return NULL;
    }
}
