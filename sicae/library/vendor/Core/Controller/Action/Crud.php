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
 * Base para as Controllers do framework que implementem CRUD
 *
 * @package    Core
 * @subpackage Controller
 * @subpackage Action
 * @name       Crud
 * @category   Controller
 */
class Core_Controller_Action_Crud extends Core_Controller_Action_Base
{
    protected $routedData;

    protected function _getUser()
    {
        return Core_Integration_Sica_User::get();
    }

    public function init()
    {
        parent::init();

        $module     = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();

        if($module && $controller){
            $this->routedData       = new Zend_Session_Namespace($module.$controller);
        }

    }

    public function getConfigList()
    {
        return NULL;
    }

    public function listAction()
    {
        $this->getHelper('layout')->disableLayout();

        $params      = $this->_getAllParams();
        $configArray = $this->getConfigList();

        if (is_array($configArray)) {
            $grid   = new Core_Grid($configArray);
            $params = $grid->mapper($params);
        }

        $result = $this->getResultList($params);

        $this->view->result = $result;
        $this->view->params = $params;
    }

    public function deleteAction()
    {
        $module     = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();

        try {
             $this->getService()->delete($this->_getParam('id'));
             $gwmsg = $this->getService()->getMessaging();
             $pkt = $gwmsg->retrievePackets('Service');
             if ($pkt) {
             	foreach ($pkt->getMessages('success') as $message) {
             		$this->getMessaging()->addSuccessMessage($message);
             	}
             }
        }
        catch (Core_Exception $e) {
            $this->getMessaging()->addErrorMessage($e->getMessage());
        }
        catch (Exception $e) {
            $this->getMessaging()->addErrorMessage(
                (APPLICATION_ENV == 'development' || APPLICATION_ENV == 'testing' || APPLICATION_ENV == 'staging') ?
                    $e->getMessage() :
                    'Erro na Operação.'
            );
        }

        $this->getMessaging()->dispatchPackets();
        $this->_redirect($module.'/'.$controller);
    }

    public function createAction()
    {
        if (isset($this->routedData->data) && $this->routedData->data) {
            $data = $this->routedData->data;
            $this->routedData->data = false;
            $rData = $this->getService()->getEntityFromArray($data);
            $this->view->data = $rData['entity'];
        }
        else {
            $this->view->data = $this->getService()->getNewEntity();
        }
    }

    public function editAction()
    {
        try {
            if (isset($this->routedData->data) && $this->routedData->data) {
                $data = $this->routedData->data;
                $this->routedData->data = false;
                $rData = $this->getService()->getEntityFromArray($data);
                $this->view->data = $rData['entity'];
            }
            else {
                $id = $this->_getParam('id');
                if (!$id) {
                    throw new RuntimeException('É necessário passar o ID');
                }
                $this->view->data = $this->getService()->find($id);
            }
        }
        catch (Exception $e) {
            $this->getMessaging()->addErrorMessage(
                (APPLICATION_ENV == 'development' || APPLICATION_ENV == 'testing' || APPLICATION_ENV == 'staging') ?
                    $e->getMessage() :
                    'Erro na Operação.'
            );
            $this->getMessaging()->dispatchPackets();
            $module     = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $this->_redirect($module.'/'.$controller);
        }
    }

    public function saveAction()
    {
        $module     = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $origin = $this->getRequest()->getActionName();
        $data       = $this->_getAllParams();
        $origin     = $this->getSaveFailRoute();

        try {
            $this->getService()->save($data);
            $gwmsg = $this->getService()->getMessaging();
            $pkt = $gwmsg->retrievePackets('Service');
            if ($pkt) {
    	        foreach ($pkt->getMessages('success') as $message) {
    	             $this->getMessaging()->addSuccessMessage($message);
    	        }
            }
            $origin = '';
        }
        catch (\Core_Exception_Model_Validation $e) {
            if ($e->getMessage()) {
                $this->getMessaging()->addErrorMessage($e->getMessage());
            }

            $gwmsg = $this->getService()->getMessaging();
            $pkt = $gwmsg->retrievePackets();
            if ($pkt) {
    	        foreach ($pkt->getMessages('error') as $message) {
    	             $this->getMessaging()->addErrorMessage($message);
    	        }
            }
            $this->routedData->data = $this->getService()->getData() + $data;
        }
        catch (\Core_Exception_ServiceLayer_Verification $e) {
            if ($e->getMessage()) {
                $this->getMessaging()->addErrorMessage($e->getMessage());
            }

            $gwmsg = $this->getService()->getMessaging();
            $pkt = $gwmsg->retrievePackets();
            if ($pkt) {
    	        foreach ($pkt->getMessages('error') as $message) {
    	             $this->getMessaging()->addErrorMessage($message);
    	        }
            }
            $this->routedData->data = $this->getService()->getData() + $data;
        }
        catch (Core_Exception $e) {
            $this->getMessaging()->addErrorMessage($e->getMessage());
            $this->routedData->data = $this->getService()->getData() + $data;
        }
        catch (Exception $e) {
            $this->getMessaging()->addErrorMessage(
                (APPLICATION_ENV == 'development' || APPLICATION_ENV == 'testing' || APPLICATION_ENV == 'staging') ?
                    $e->getMessage() :
                    'Erro na Operação.'
            );
            $this->routedData->data = $this->getService()->getData() + $data;
        }
        $this->getMessaging()->dispatchPackets();
        $this->_redirect($module.'/'.$controller.'/'.$origin);
    }

    public function getSaveFailRoute()
    {
        return strstr($_SERVER['HTTP_REFERER'], 'create') ? 'create' :
               (strstr($_SERVER['HTTP_REFERER'], 'edit') ? 'edit': 'index');
    }
}