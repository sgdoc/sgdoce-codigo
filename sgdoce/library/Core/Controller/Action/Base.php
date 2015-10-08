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
 * Base para as Controllers do framework que utilizem Service
 *
 * @package    Core
 * @subpackage Controller
 * @subpackage Action
 * @name       Base
 * @category   Controller
 */
class Core_Controller_Action_Base extends Core_Controller_Action_Abstract
{

    public function preDispatch ()
    {
        parent::preDispatch();
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->view->notifications = $this->getService('AreaTrabalho')->getNotification();
        }
    }

    public function init ()
    {
        parent::init();
        $dto = \Core_Dto::factoryFromData((array) Core_Integration_Sica_User::get(), 'search');

        $isUserSgi = $this->getService('VwUsuario')->isUserSgi($dto);
        \Zend_Registry::set('isUserSgi', $isUserSgi);
    }

    /**
     * @return Bisna\Service\Service
     */
    public function getService ($service = NULL)
    {
        if (null === $service) {
            $service = $this->_service;
        }

        return $this->getServiceLocator()->getService($service);
    }

    /**
     * @return Bisna\Service\ServiceLocator
     */
    public function getServiceLocator ()
    {
        return Zend_Registry::get('serviceLocator');
    }

    protected function _isUserSgi ()
    {
        return \Zend_Registry::get('isUserSgi');
    }

}
