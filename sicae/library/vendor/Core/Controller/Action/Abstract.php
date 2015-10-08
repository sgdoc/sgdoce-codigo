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
 * @see Zend_Controller_Action
 */
/**
 * Base para as Controllers do framework
 *
 * @package    Core
 * @subpackage Controller
 * @subpackage Action
 * @name       Abstract
 * @category   Controller
 */
abstract class Core_Controller_Action_Abstract extends Zend_Controller_Action
{
    /**
     * @var string
     */
    protected $_service;

    /**
     * Redirect
     *
     * @var string
     */
    protected $_redirect;

    /**
     * @var array
     */
    protected $_persistDataError = array();

    /**
     * Retorna o Gateway com o qual esta camada deve lidar
     * @return Core_Messaging_Gateway
     */
    public function getMessaging()
    {
        return Core_Messaging_Manager::getGateway('User');
    }

    public function retrievePackets($receiver = 'Service')
    {
        return $this->getMessaging()->retrievePackets($receiver);
    }

    public function getErrorMessages()
    {
        return $this->getService()
                    ->getMessaging()
                    ->getErrorMessages();
    }

    public function getInfoMessages()
    {
        return $this->getService()
                    ->getMessaging()
                    ->getInfoMessages();
    }

    public function getAlertMessages()
    {
        return $this->getService()
                    ->getMessaging()
                    ->getAlertMessages();
    }

    public function getSuccessMessages()
    {
        return $this->getService()
                    ->getMessaging()
                    ->getSuccessMessages();
    }

    /**
     * @return void
     */
    public function addErrorMessages()
    {
        foreach ($this->getErrorMessages() as $message) {
            $this->getMessaging()->addErrorMessage($message);
        }
    }

    /**
     * @return void
     */
    public function addAlertMessages()
    {
        foreach ($this->getAlertMessages() as $message) {
            $this->getMessaging()->addAlertMessage($message);
        }
    }

    /**
     * @return void
     */
    public function addInfoMessages()
    {
        foreach ($this->getInfoMessages() as $message) {
            $this->getMessaging()->addInfoMessage($message);
        }
    }

    /**
     * @return void
     */
    public function addSuccessMessages()
    {
        foreach ($this->getSuccessMessages() as $message) {
            $this->getMessaging()->addSuccessMessage($message);
        }
    }

    public function dispatchErrorMessages()
    {
        foreach ($this->getErrorMessages() as $message) {
            $this->getMessaging()->addErrorMessage($message);
        }

        $this->getMessaging()->dispatchPackets();
    }

    public function dispatchAlertMessages()
    {
        $this->retrievePackets();

        foreach ($this->getAlertMessages() as $message) {
            $this->getMessaging()->addAlertMessage($message);
        }

        $this->getMessaging()->dispatchPackets();
    }

    public function dispatchInfoMessages()
    {
        foreach ($this->getInfoMessages() as $message) {
            $this->getMessaging()->addInfoMessage($message);
        }

        $this->getMessaging()->dispatchPackets();
    }

    public function dispatchSuccessMessages()
    {
        foreach ($this->getSuccessMessages() as $message) {
            $this->getMessaging()->addSuccessMessage($message);
        }

        $this->getMessaging()->dispatchPackets();
    }

    /**
     * Action inicial do Crud. Normalmente, apresenta o formulário que será utilizado para listagem
     */
    public function indexAction(){}

    protected function _redirectActionDefault($actionDefault)
    {
        if ($this->_redirect) {
            $actionDefault = $this->_redirect;
        }

        if (is_string($actionDefault)) {
            return $this->_redirectAction($actionDefault);
        }

        if (!is_array($actionDefault)) {
            throw new InvalidArgumentException('É necessário que redirect seja um array');
        }

        if (!isset($actionDefault['action'])) {
            throw new InvalidArgumentException("É necessário passar 'Action'.");
        }

        $actionDefault += array(
            'module'     => NULL,
            'controller' => NULL,
        );

        return $this->_redirectAction(
            $actionDefault['action'],
            $actionDefault['controller'],
            $actionDefault['module'],
            $actionDefault
        );
    }

    protected function _redirectAction($action, $controller = null, $module = null, array $options = array())
    {
        if (is_array($controller)) {
            $options = $controller;
            $controller = NULL;
        }

        if (is_array($module)) {
            $options = $module;
            $module  = NULL;
        }

        if (NULL === $controller) {
            $controller = $this->_request->getControllerName();
        }

        if (NULL === $module) {
            $module = $this->_request->getModuleName();
        }

        $params = '';
        if (isset($options['params'])) {
            $options['params'] = (array) $options['params'];
            array_walk($options['params'], function($value, $key) use(&$params) {
                $params .= '/' . $key . '/' . $value;
            });
            unset($options['params']);
        }

        $url = $module
             . '/' . $controller
             . '/' . $action . $params;

        return $this->_redirect($url, $options);
    }

    public function dispatch($action)
    {
        try {
            parent::dispatch($action);
            $this->addInfoMessages();
            $this->addSuccessMessages();
        } catch (Core_Exception_Validation $exc) {
            $this->_persistDataError();
            $this->addErrorMessages();
            $this->addAlertMessages();
            $this->getMessaging()->dispatchPackets();
            $this->_dispatchException($action, $exc);
        } catch (Core_Exception_Verification $exc) {
            $this->_persistDataError();
            $this->addErrorMessages();
            $this->addAlertMessages();
            $this->getMessaging()->dispatchPackets();
            $this->_dispatchException($action, $exc);
        } catch (Core_Exception $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    protected function _persistDataError()
    {
        foreach ($this->_persistDataError as $key => $value) {
            $this->getHelper('persist')->set($key, $value);
        }
    }

    protected function _dispatchException($action, Exception $exc)
    {
        $actionNotPrefix = str_ireplace($this->_request->getActionKey(), '', $action);

        if ($this->_request instanceof Zend_Controller_Request_Http &&
             $this->_request->isXmlHttpRequest()) {
             // @todo implementar
                throw $exc;
        } else {
            $redirect = $this->_getFailTarget($actionNotPrefix);

            if (!$redirect) {
                throw $exc;
            }

            if (is_string($redirect)) {
                $this->_redirect($redirect);
                return;
            }

            $this->_redirectAction(
                $redirect['action'],
                $redirect['controller'],
                $redirect['module'],
                $redirect
            );
        }
    }

    final protected function _getFailTarget($action)
    {
        $actions = $this->_getFailTargetMap();

        if (!isset($actions[$action])) {
            return FALSE;
        }

        $redirect = $actions[$action];

        if (is_callable($redirect)) {
            $redirect = call_user_func($redirect, $this->_request, $this->_response);
        }

        if (is_string($redirect)) {
            $redirectRecursive = $this->_getFailTarget($redirect);

            if ($redirectRecursive) {
                $redirect = $redirectRecursive;
            }

            return $redirect;
        }

        if (!is_array($redirect)) {
            return $redirect;
        }

        return $redirect + array(
            'action'     => NULL,
            'controller' => NULL,
            'module'     => NULL,
        );
    }

    protected function _getFailTargetMap()
    {
         return array(
            'save' => function($request, $response) {
                $referer         = $request->getHeader('referer');
                $uri             = str_replace(array('http://', 'https://'), '', $referer);
                $pathInfoReferer = substr($uri, strpos($uri, '/'));

                return $pathInfoReferer;
            },

            'delete' => function($request, $response) {
                $referer         = $request->getHeader('referer');
                $uri             = str_replace(array('http://', 'https://'), '', $referer);
                $pathInfoReferer = substr($uri, strpos($uri, '/'));

                return $pathInfoReferer;
            }
        );
    }

    protected function _getMessageTranslate($code)
    {
        if (is_string($code)) {
            if (!Core_Registry::getMessage()->isTranslated($code)) {
                return $code;
            }

            return Core_Registry::getMessage()->translate($code);
        }

        if (is_array($code)) {
            if (Core_Registry::getMessage()->isTranslated($code[0])) {
                return Core_Registry::getMessage()
                          ->translate(
                              $code[0],
                              NULL,
                              isset($code[1]) ? $code[1] : array()
                          );
            }
        }

        return $code;
    }
}
