<?php
/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */
/**
 * SISICMBio
 *
 * Classe Controller Usuario
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         Usuario
 * @version      1.0.0
 * @since        2012-07-24
 */
class Principal_UsuarioController extends Sica_Controller_Action
{
    /**
     * Constante para entidade
     * @var string
     */
    const ENTITY = 'entity';

    /**
     * Nome do Serviço
     * @var string
     */
    protected $_service = 'Usuario';
    /**
     * Mapeamento para Dto da entidade do Usuario
     * @var array
     */
    protected $_optionsDtoEntityUser = array(
        'entity' => 'Sica\Model\Entity\Usuario',
        'mapping' => array()
    );
    /**
     * Mapeamento para Dto da entidade de Pessoa Fisica
     * @var array
     */
    protected $_optionsDtoEntityPessoaFisica = array(
        'entity' => 'Sica\Model\Entity\PessoaFisica',
        'mapping' => array()
    );

    /**
     * Action de autenticação do usuário
     * @return void
     */
    public function autenticateAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('/');
        }

        $formData            = $this->_getAllParams();
        $this->_persistDataError['data'] = $formData;

        if (isset($formData['captcha_code'])) {
            $this->getService()->validateCaptcha($formData['captcha_code']);
        }

        $adapter = new Sica_Auth_Adapter(
            $this->_entity,
            Zend_Filter::filterStatic($formData['nuCpf'], 'digits'),
            $formData['txSenha']
        );
        $adapter->appendDataExtras(TRUE);
        $result  = Zend_Auth::getInstance()->authenticate($adapter);

        $this->_checkAutenticate($result);
    }

    /**
     *
     * @return void
     * @param Zend_Auth $result
     */
    protected function _checkAutenticate($result)
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_setCookie('sicaeuser', 1, time() - 3600);
            $this->_redirect('/index/home');
        } else {
            $msg = $result->getMessages();
            $this->getMessaging()->addErrorMessage($this->_getMessageTranslate(current($msg)));
            $this->getMessaging()->dispatchPackets();

            $this->_checkAttemp();

            foreach ($this->_persistDataError as $key => $value) {
                $this->getHelper('persist')->set($key, $value);
            }

            $this->_redirect('/usuario/login');
        }
    }

    /**
     * Action responsável por incrementar o cookie referente as tentativas de login inválidas do usuario.
     * @return void
     */
    protected function _checkAttemp()
    {
        $request = new Zend_Controller_Request_Http();
        $cookie = (int) $request->getCookie('sicaeuser');
        $attempt = $cookie + 1;
        $this->_setCookie('sicaeuser', $attempt);
    }

    /**
     * Metodo que cria um novo cookie
     * @param type $name
     * @param type $value
     * @param type $expire
     * @param type $path
     * @param type $domain
     * @param type $secure
     */
    protected function _setCookie($name, $value = NULL, $expire = 0)
    {
        setcookie($name, $value, $expire);
    }

    /**
     * @return void
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        
        $urlToRedirect = '';

        $urlBack = $this->_getParam('url-back', '');

        if (! empty($urlBack)) {
            $urlToRedirect = $urlBack;
        } else {
            $urlToRedirect = rtrim(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('urlSica'), '/');
            if (\Core_Integration_Sica_User::getUserProfileExternal()) {
                $urlToRedirect .= '/usuario-externo/login';
            }
        }

        $this->_redirect($urlToRedirect);
    }

    /**
     * @return void
     */
    public function loginAction()
    {
        \Zend_Auth::getInstance()->clearIdentity();

        if ($this->getHelper('persist')->has('data')) {
            $this->view->data = $this->getHelper('persist')->get('data');
        }
        $this->_helper->layout->setLayout('login');
    }

    /**
     * @return void
     */
    // @codeCoverageIgnoreStart
    public function captchaAction()
    {
        $captcha = Sica_Captcha_Adapter::factory();
        $captcha->render();
    }
    // @codeCoverageIgnoreEnd
    /**
     * @return void
     */
    // @codeCoverageIgnoreStart
    public function captchaAudioAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $img = Sica_Captcha_Adapter::factory();

        $img->audioPath(APPLICATION_PATH . '/../public/captcha/audio/pt/');

        $img->playAudio();
    }
    // @codeCoverageIgnoreEnd
    /**
     * @return void
     */
    public function changePassAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        try {
            $params = $this->_getAllParams();
            unset($params['controller'], $params['action'], $params['module']);

            $options = array('txSenha', 'txSenhaNova', 'txSenhaNovaConfirm');
            $dtoPass = Core_Dto::factoryFromData($params, 'Core_Dto_Mapping', $options);

            $user = Zend_Auth::getInstance()->getIdentity();

            $data = array(
                'sqPessoa' => $user->sqPessoa,
                'sqUsuario' => $user->sqUsuario
            );

            $_optionsDtoUsuario = array(
                'entity' => 'Sica\Model\Entity\Usuario',
                'mapping' => array(
                    'sqPessoa' => array('sqPessoa' => 'Sica\Model\Entity\Pessoa'),
                )
            );
            $dtoUser = Core_Dto::factoryFromData($data, 'entity', $_optionsDtoUsuario);

            $this->getService()->changePass($dtoPass, $dtoUser);

            $this->getMessaging()->addSuccessMessage($this->_getMessageTranslate('MN014'));
            $this->getMessaging()->dispatchPackets();

            $this->_helper->json(TRUE);

        } catch (\Core_Exception $coreExc) {
            $message = sprintf('[SICA-e] Exception %s in %s: "%s"', get_class($coreExc), __METHOD__, $coreExc->getMessage());
            error_log( $message );
            $message = \Core_Registry::getMessage()->_('MN171') . ' ' . $coreExc->getMessage();
            $this->_helper->json(array('error' => $message));
        }
    }

    public function changePassWithMailAction()
    {
        try {
            $params = $this->_getAllParams();
            $sqUsuario = $params['sqUsuario'];
            unset($params['controller'], $params['action'], $params['module'], $params['sqUsuario']);

            $params['txSenha'] = $params['txSenha'];
            $params['txSenhaNova'] = $params['txSenhaNova'];
            $params['txSenhaNovaConfirm'] = $params['txSenhaNovaConfirm'];

            $options = array('txSenha', 'txSenhaNova', 'txSenhaNovaConfirm');
            $dtoPass = Core_Dto::factoryFromData($params, 'Core_Dto_Mapping', $options);

            $_optionsDtoUsuario = array(
                'entity' => 'Sica\Model\Entity\Usuario',
                'mapping' => array(
                    'sqPessoa' => array('sqPessoa' => 'Sica\Model\Entity\Pessoa'),
                )
            );

            $data = array(
                'sqUsuario' => $sqUsuario
            );
            $dtoUser = Core_Dto::factoryFromData($data, 'entity', $_optionsDtoUsuario);
            $this->getService()->changePass($dtoPass, $dtoUser, FALSE, TRUE);

            $this->getMessaging()->addSuccessMessage($this->_getMessageTranslate('MN014'));

        } catch (\Zend_Ldap_Exception $ldapExc) {
            $message = sprintf('[SICA-e] LDAP Error in %s: "%s"', __METHOD__, $ldapExc->getMessage());
            error_log( $message );
            $message = sprintf('[Erro no LDAP] %s', $ldapExc->getMessage());
            $ldapCode = $ldapExc->getCode();
            if ($ldapCode > 0) {
                $message = sprintf('LDAP0x%x',$ldapCode);
            }
            $this->getMessaging()->addErrorMessage($this->_getMessageTranslate($message));
        } catch (\Core_Exception $coreExc) {
            $this->getMessaging()->addErrorMessage($this->_getMessageTranslate($coreExc->getMessage()));
        }
        $this->getMessaging()->dispatchPackets();
        $this->_redirect('/usuario/login');
    }

    protected function _getFailTargetMap()
    {
        return parent::_getFailTargetMap() +
                array(
                    'changePassWithMail' => 'save',
                    'autenticate' => 'usuario/login'
        );
    }

    /**
     *
     */
    public function recoverPassAction()
    {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();

            $params = $this->_getAllParams();
            $params['nuCpf'] = Zend_Filter::filterStatic($params['nuCpf'], 'digits');
            $dtoPerson = Core_Dto::factoryFromData($params, self::ENTITY, $this->_optionsDtoEntityPessoaFisica);

            $_optionsDtoEmail = array(
                'entity'  => 'Sica\Model\Entity\Email',
                'mapping' => array(
                    'sqPessoa' => array('sqPessoa' => 'Sica\Model\Entity\Pessoa'),
                )
            );

            $dtoMail = Core_Dto::factoryFromData($params, self::ENTITY, $_optionsDtoEmail);

            $this->getService()->recoverPass($dtoPerson, $dtoMail);

            $this->getMessaging()->addSuccessMessage($this->_getMessageTranslate('MN007'));
            $this->getMessaging()->dispatchPackets();

            $retorno = array();

        } catch (\Exception $exc) {
            $message = sprintf('[SICA-e] Exception %s in %s: "%s"', get_class($exc), __METHOD__, $exc->getMessage());
            error_log( $message );
            $gwmsg = $this->getService()->getMessaging();
            $pkt   =  $gwmsg->retrievePackets('Service');
            $error = array('Erro desconhecido.');
            if ($pkt) {
                $error = $pkt->getMessages('error');
            }
            $retorno = array(
                'msg'   => current($error),
                'error' => TRUE
            );
        }

        $this->_helper->json($retorno);
    }

    public function changePassTokenAction()
    {
        try {
            $this->_helper->layout->setLayout('login');
            $params = $this->_getAllParams();

            if (isset($params['token']) === FALSE || $params['token'] == '') {
                $this->_redirect('/');
            }

            $this->view->sqUsuario = $this->getService()->validateToken($params);
        } catch (Core_Exception_ServiceLayer_Verification $exc) {
            $this->_redirect('/');
        }
    }

    public function changePassIframeAction()
    {
        $this->_helper->layout->setLayout('change-pass-iframe');
    }

}
