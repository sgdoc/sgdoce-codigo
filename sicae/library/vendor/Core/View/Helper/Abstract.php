<?php
class Core_View_Helper_Abstract extends Zend_View_Helper_Abstract
{
    protected function _checkAcl(array $resource=array())
    {

        $action     = (isset($resource['action'    ])) ? $resource['action'    ] : NULL;
        $controller = (isset($resource['controller'])) ? $resource['controller'] : NULL;
        $module     = (isset($resource['module'    ])) ? $resource['module'    ] : NULL;

        return $this->_hasPermission($module, $controller, $action);
    }

    private function _hasPermission($module=NULL, $controller=NULL, $action=NULL)
    {
        $acl      = \Zend_Registry::get('acl');
        $profile  = \Core_Integration_Sica_User::getUserNoProfile();

        $resource = '';

        $request = Zend_Controller_Front::getInstance()->getRequest();

        if (NULL === $module) {
            $resource .= $request->getModuleName();
        }else{
            $resource .= $module;
        }
        $resource .=  '/';
        if (NULL === $controller) {
            $resource .= $request->getControllerName();
        }else{
            $resource .= $controller;
        }
        $resource .=  '/';
        if (NULL === $action) {
            $resource .= $request->getActionName();
        }else{
            $resource .= $action;
        }

        $permission = FALSE;
        if ($acl->has($resource) && $acl->isAllowed($profile, $resource)) {
            $permission = TRUE;
        }

        return $permission;
    }
}
