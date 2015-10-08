<?php

/**
 * Application initialization plugin
 *
 * @uses Zend_Controller_Plugin_Abstract
 */
class Core_Controller_Plugin_Security extends Zend_Controller_Plugin_Abstract
{

    /**
     * Route Shutdown - Verificações de segurança - SESSION, ACL, AUTH
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        if ($this->skip($request)) {
            return;
        }

        if (!Core_Integration_Sica_User::has()) {
            $this->getRedirectLogin($request);
            return;
        }

        $sgSistema = strtoupper(Core_Integration_Sica_User::getSgSystemActive());
        $session = new Core_Session_Namespace('USER', FALSE, TRUE);
        if (!isset($session->acl)) {
            if (!$this->skipHome($request)) {
                $this->getRedirectHome($request);
            }
            return;
        }

        if (!$session->acl->hasRole($session->noPerfil)) {
            throw new UnexpectedValueException('Perfil não identificado.');
        }

        if ($this->skipAcl($request) || $this->skipIndexBlank($request)) {
            return;
        }

        $permission = FALSE;
        foreach ($this->getResources() as $resource) {
            if ($session->acl->has($resource) && $session->acl->isAllowed($session->noPerfil, $resource)) {
                $permission = TRUE;
                break;
            }
        }

        if (!$permission) {
            throw new Exception('Acesso Negado!', 403);
        }
    }

    public function getRedirectIndexBlank()
    {
        $front = \Zend_Controller_Front::getInstance();
        $actionBlank = $front->getParam('bootstrap')->getOption('actionBlank');

        if (!$actionBlank) {
            $actionBlank = $front->getDefaultAction();
        }

        return $front->getDefaultModule() . '/' . $front->getDefaultControllerName() . '/' . $actionBlank;
    }

    public function getRedirectLogin(Zend_Controller_Request_Abstract $request)
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
            $json->sendJson(array('error'=>TRUE,'status'=>FALSE,'message'=>'Sessão expirada, atualize a página'));
        } else {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $urlSistema = rtrim(Zend_Controller_Front::getInstance()->getParam('bootstrap')
                ->getOption('urlSica'), '/');
            $redirector->gotoUrl($urlSistema)->redirectAndExit();
        }

    }

    public function getRedirectHome(Zend_Controller_Request_Abstract $request)
    {
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $urlSistema = rtrim(Zend_Controller_Front::getInstance()->getParam('bootstrap')
            ->getOption('urlSica'), '/');

        $redirector->gotoUrl($urlSistema . '/index/home')->redirectAndExit();
    }

    /**
     * Override
     *
     * Método disponibilizado para pular controller sem necessidade da aplicação da ACL
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function skip(Zend_Controller_Request_Abstract $request)
    {
        return false;
    }

    /**
     * Override
     *
     * Método disponibilizado para pular controller sem necessidade da aplicação da ACL
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function skipAcl(Zend_Controller_Request_Abstract $request)
    {
        return $this->_checkSkip($request,'acl');
    }

    /**
     * Override
     *
     * Método disponibilizado para pular controller sem necessidade da aplicação da ACL
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function skipHome(Zend_Controller_Request_Abstract $request)
    {
        return false;
    }

    /**
     * Override
     *
     * Método disponibilizado para pular controller sem necessidade da aplicação da ACL
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function skipIndexBlank(Zend_Controller_Request_Abstract $request)
    {
        $front = \Zend_Controller_Front::getInstance();
        $actionBlank = $front->getParam('bootstrap')->getOption('actionBlank');

        if (!$actionBlank) {
            $actionBlank = $front->getDefaultAction();
        }

        $allowedUrl = $front->getDefaultModule() . '/' . $front->getDefaultControllerName() . '/' . $actionBlank;

        return in_array($allowedUrl, $this->getResources());
    }

    public function getResources()
    {
        $request = $this->getRequest();

        $resources = array(
            $request->getModuleName() . '/' . $request->getControllerName() . '/' . $request->getActionName(),
        );

        return $resources;
    }

    protected function _checkSkip(Zend_Controller_Request_Abstract $request, $type)
    {
        // verificação de requisicao - Caso ajax, verifica se a action é delete, senao, SKIP nele.
        if( $request->isXmlHttpRequest() && $request->getActionName() !== 'delete' ){
            return TRUE;
        }

        $configs    = Zend_Registry::get('configs');
        $skip = $configs['security']['skip'][$type];
        $result = FALSE;
        foreach($skip as $routers){
            $route = explode('/',$routers);
            switch(count($route)){
                case 1:// action
                    $result = in_array(
                        $request->getActionName(),
                        $skip
                    );
                    break;
                case 2:// controller/action
                    $result = in_array(
                        $request->getControllerName().'/'.$request->getActionName(),
                        $skip
                    );
                    break;
                case 3:// module/controller/action
                    $result = in_array(
                        $request->getModuleName().'/'.$request->getControllerName().'/'.$request->getActionName(),
                        $skip
                    );
                    break;
            }

            if ($result){
                return TRUE;
            }
        }
        return $result;
    }
}