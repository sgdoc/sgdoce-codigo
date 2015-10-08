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
 * Controller utilizada para lidar com erros
 *
 * @package    Core
 * @subpackage Application
 * @subpackage Module
 * @name       Bootstrap
 * @category   Bootstrap
 */
class Core_Controller_Action_Error extends Zend_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Página não encontrada.';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Erro interno na aplicação.';
                break;
        }

        if ($errors->exception->getCode() == 403) {
            $this->render('acesso-negado');
        }

        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log(
                'Exception: ' . $errors->exception->getMessage(),
                $priority
            ); // add format log
            $log->log(
                'Request Parameters: ' . var_export($errors->request->getParams(), true),
                 $priority
            ); // add format log
            $log->log(
                'Stack trace: ' . $errors->exception->getTraceAsString(),
                $priority
            ); //
        }

        if ( $priority == Zend_Log::CRIT) {

            $mail = new Zend_Mail();

            $config = Zend_Registry::get('configs');

            $mail->setFrom($config['resources']['mail']['defaultReplyTo']['email'])
                 ->addTo($config['resources']['mail']['defaultReplyTo']['emailError']);

            $htmlMail = sprintf("<html> Exception : %s<br><hr> Request Parameters : %s<br><hr> Stack trace : %s<br><hr></html>",
                $errors->exception->getMessage(),
                var_export($errors->request->getParams(), true),
                $errors->exception->getTraceAsString()
                );

            $mail->setBodyHtml($htmlMail);
            $mail->setSubject('Erros no SICAe [ ' . getenv('APPLICATION_ENV') . ' ]');
            $mail->send();
        }

         
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }

}

