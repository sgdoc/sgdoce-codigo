<?php

class Principal_ErrorController extends Core_Controller_Action_Error
{
    public function errorAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getHelper('layout')->disableLayout();
            $this->_helper->viewRenderer->setNoRender();

            $errors    = $this->_getParam('error_handler');
            $errorCode = $errors->exception->getCode();
            if ($errorCode === 403) {
                $msg = 'Você não possui permissão para efetuar essa operação. Fale com o adminstrador do sistema.';
            } else {
                $msg = 'Ocorreu um erro na requisição: ' . $errors->exception->getMessage();
            }

            $this->_helper->json(array('success'=>false, 'code'=>$errorCode, 'message'=>$msg));
        } else {
            $this->_helper->layout()->setLayout('login');
            parent::errorAction();
        }
    }
}