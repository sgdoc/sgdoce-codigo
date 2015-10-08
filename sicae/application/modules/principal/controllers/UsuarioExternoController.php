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

require_once __DIR__ . '/UsuarioController.php';

/**
 * SISICMBio
 *
 * Classe Controller Usuario Externo
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         Usuario
 * @version      1.0.0
 * @since        2012-07-24
 */
class Principal_UsuarioExternoController extends Principal_UsuarioController
{
    /**
     * Constante para nome da sessão do usuário
     * @var string
     */

    const USER = 'USER';

    /**
     * Constante para entidade
     * @var string
     */
    const ENTITY = 'entity';

    /**
     * Nome do Serviço
     * @var string
     */
    protected $_service = 'UsuarioExterno';

    /**
     * Mapeamento para Dto da entidade do Usuario
     * @var array
     */
    protected $_optionsDtoEntityUser = array(
        'entity' => 'Sica\Model\Entity\UsuarioExterno',
        'mapping' => array()
    );
    protected $_codeMessageToggleActive = "";
    protected $_codeMessageToggleInactive = "";

    /**
     * Redireciona o usuario para tela de pessoa fisica ou juridica
     */
    public function rotaAction()
    {
        $session = Core_Integration_Sica_User::has();
        if ($session){
            Core_Integration_Sica_User::destroy();

            $this->_redirect('usuario-externo/login');
        }
        $this->_helper->layout->setLayout('create-usuario-externo');
    }

    /**
     * Action de autenticação do usuário
     * @return void
     */
    public function autenticateAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('/usuario-externo/logout');
        }

        $formData = $this->_getAllParams();
        $this->_persistDataError['data'] = $formData;

        if (isset($formData['captcha_code'])) {
            $this->getService('Usuario')->validateCaptcha($formData['captcha_code']);
        }

        $adapter = new Sica_Auth_Adapter(
                $this->getService('UsuarioExterno')->userEntity(), $formData['email'], $formData['txSenha']
        );

        $adapter->setLdap(FALSE);

        $result = \Zend_Auth::getInstance()->authenticate($adapter);

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
            $this->_setCookie('sicaeuser', 1, time() - 3600, NULL, 'icmbio.gov.br');
            $this->_redirect('/index/home');
        } else {
            $msg = $result->getMessages();
            $this->getMessaging()->addErrorMessage($this->_getMessageTranslate(current($msg)));
            $this->getMessaging()->dispatchPackets();

            $this->_checkAttemp();

            foreach ($this->_persistDataError as $key => $value) {
                $this->getHelper('persist')->set($key, $value);
            }

            $this->_redirect('/usuario-externo/login');
        }
    }

    /**
     * @return void
     */
    public function logoutAction()
    {
        \Zend_Auth::getInstance()->clearIdentity();

        $urlToRedirect = '/usuario-externo/login';

        $urlBack = $this->_getParam('url-back', '');

        if (! empty($urlBack)) {
            $urlToRedirect = $urlBack;
        }

        $this->_redirect($urlToRedirect);
    }

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
                'sqUsuarioExterno' => $user->sqUsuario
            );

            $_optionsDtoUsuario = array(
                'entity' => 'Sica\Model\Entity\UsuarioExterno',
                'mapping' => array(
                    'sqPessoa' => array('sqPessoa' => 'Sica\Model\Entity\Pessoa'),
                )
            );
            $dtoUser = Core_Dto::factoryFromData($data, 'entity', $_optionsDtoUsuario);
            $this->getService()->changePass($dtoPass, $dtoUser);

            $this->getMessaging()->addSuccessMessage($this->_getMessageTranslate('MN014'));
            $this->getMessaging()->dispatchPackets();

            $this->_helper->json(TRUE);
        } catch (\Core_Exception $exc) {
            $gwmsg = $this->getService()->getMessaging();
            $pkt = $gwmsg->retrievePackets('Service');
            foreach ($pkt->getMessages('error') as $message) {
                $msg['error'] = $message;
            }

            $this->_helper->json($msg);
        }
    }

    public function changePassWithMailAction()
    {
        $options = array('txSenha', 'txSenhaNova', 'txSenhaNovaConfirm');
        $dtoPass = new Core_Dto_Mapping($this->getRequest()->getPost(), $options);

        $this->getService()->changePassWithMail($dtoPass, NULL);

        $this->getMessaging()->addSuccessMessage($this->_getMessageTranslate('MN014'));
        $this->getMessaging()->dispatchPackets();

        $this->_redirect('/usuario-externo/login');
    }

    protected function _getFailTargetMap()
    {
        $targetMap = parent::_getFailTargetMap();
        $targetMap['autenticate'] = 'usuario-externo/login';
        $targetMap['login'] = '/usuario-externo/login';

        return $targetMap;
    }

    /**
     *
     */
    public function recoverPassAction()
    {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();

            if ($this->_getParam('tpValidacao') != 'passaporte') {
                $this->_setParam('nuCpfRecover', Zend_Filter::filterStatic($this->_getParam('nuCpfRecover'), 'digits'));
            }

            $dto = new Core_Dto_Mapping($this->getRequest()->getPost(), array());

            $this->getService()->recoverPass($dto);

            $this->getMessaging()->addSuccessMessage($this->_getMessageTranslate('MN007'));
            $this->getMessaging()->dispatchPackets();

            $retorno = array();
        } catch (\Core_Exception $exc) {
            $gwmsg = $this->getService()->getMessaging();
            $pkt = $gwmsg->retrievePackets('Service');
            $error = $pkt->getMessages('error');

            $retorno = array(
                'msg' => current($error),
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

            if (!isset($params['token']) || $params['token'] == '') {
                $this->_redirect('/usuario-externo/login');
            }

            $this->view->sqUsuarioExterno = $this->getService()->validateToken($params);
        } catch (Core_Exception_ServiceLayer_Verification $exc) {
            $this->_redirect('/usuario-externo/login');
        }
    }

    public function changePassIframeAction()
    {
        $this->_helper->layout->setLayout('change-pass-iframe');
    }

    public function checkCredencialsAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $data = new Core_Dto_Mapping($this->getRequest()->getPost(), $this->getRequest()->getPost());
        $this->_helper->json($this->getService()->checkCredencials($data));
    }

    public function confirmMailActivationAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

	$exc = new Exception();

        try {
            $this->getService()->confirmMailActivation($this->_getAllParams());

            $this->getMessaging()->addSuccessMessage($this->_getMessageTranslate('MN146'));
            $this->getMessaging()->dispatchPackets();
        } catch (\Core_Exception_Verification $exc) {
            $this->_persistDataError();
            $this->addErrorMessages();
            $this->addAlertMessages();
            $this->getMessaging()->dispatchPackets();
        }

        $this->_dispatchException('login', $exc);
    }

    public function editAction()
    {
        $entity = $this->getService()->find($this->_getParam('id'));
        $urlSica = \Core_Registry::get('configs');
        $urlSica = rtrim($urlSica['urlSica'], '/') . '/';

        if ($entity->getSqUsuarioPessoaJuridica()->getNuCnpj()) {
            $this->_redirect($urlSica . '/usuario-externo-pessoa-juridica/edit/id/' . $this->_getParam('id'));
        } else {
            $this->_redirect($urlSica . '/usuario-externo-pessoa-fisica/edit/id/' . $this->_getParam('id'));
        }
    }

    public function indexAction()
    {
        $sqPerfil = \Core_Integration_Sica_User::getUserProfile();
        if ($sqPerfil) {
            $this->_sqTipoPerfil = $this->getService('Perfil')
                                        ->find($sqPerfil)
                                        ->getSqTipoPerfil()
                                        ->getSqTipoPerfil();

            $this->view->id = $this->_getParam('id');
            $this->view->sistemas = $this->getService('Sistema')->systemsActives($this->_sqTipoPerfil, array(2));
        } else {
            $this->_redirect('/usuario/login');
        }
    }

    /**
     * Configuracao da grid
     *
     * @return array
     */
    public function getConfigList()
    {
        $array = array(
            'columns' => array(
                array(
                    'alias' => 'u.sqUsuarioExterno'
                ),
                array(
                    'alias' => 'pf.nuCpf'
                ),
                array(
                    'alias' => 'u.noUsuarioExterno'
                )
            )
        );

        return $array;
    }

    /**
     * Método para preencher os dados da pesquisa
     *
     * @param Core_Dto_Search $dto Dados da requisição
     */
    public function getResultList(\Core_Dto_Search $dto)
    {
        return $this->getService()->listGridUsersExternals($dto);
    }
    /**
     * Reenvio do e-mail de ativação
     *
     * @return void
     */
    public function resendMailAction()
    {
        $result = array(
            'error' => FALSE,
            'message' => ''
        );

        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();

            $this->getService()->resendMail($this->_getParam('id',0));

        } catch (\Exception $exp) {
            $result = array(
                'error' => TRUE,
                'message' => $exp->getMessage()
            );
        }

        $this->_helper->json($result);
    }

    /**
     * Tela vínculo Perfil
     *
     * @return void
     */
    public function bindAction()
    {
        $identifier = $this->_getParam('id');
        $this->view->data = $this->getService()->find($identifier);

        if (!count($this->view->data)) {
            $this->_redirect('/usuario-externo/');
        }

        $this->view->binds = $this->getService()->findProfilesBind($identifier);

        if (0 === count($this->view->binds)) {
            $this->getMessaging()->addErrorMessage('MN016');
            $this->getMessaging()->dispatchPackets();
        }
    }

    public function createBindAction()
    {
        $this->view->sistemas = $this->getService('Sistema')->systemsActives();
        $this->view->tpOperacao = TRUE;
        $this->_helper->layout()->disableLayout();
    }

    public function perfisAction()
    {
        $params = $this->_getAllParams();
        $dto = new Core_Dto_Mapping($params + array('inPerfilExterno' => '1'), array('sqSistema', 'inPerfilExterno'));
        $this->view->perfis = $this->getService('Perfil')->comboProfile($dto, FALSE);

        $criteria = array('sqUsuarioExterno' => $this->_getParam('sqUsuarioExterno'));
        $this->view->binds = $this->getService('UsuarioExternoPerfil')->findBy($criteria);

        $this->_helper->layout()->disableLayout();
        $this->render('table-perfis');
    }

    public function saveBindProfileAction()
    {
        $params = $this->_getAllParams();
        $perfis = $this->_getParam('perfil');
        $mapping = new Core_Dto_Mapping($params, array('usuario', 'sqSistema'));

        $dtosPerfis = array();
        foreach ((array) $perfis as $perfil) {
            $dtosPerfis[] = Core_Dto::factoryFromData(
                            array('sqPerfil' => $perfil), 'entity', array('entity' => 'Sica\Model\Entity\Perfil')
            );
        }

        $msg = $this->_messageEdit;

        if ($this->_getParam('tpOperacao')) {
            $msg = $this->_messageCreate;
        }

        $this->getMessaging()->addSuccessMessage($msg);
        $this->getMessaging()->dispatchPackets();

        $criteria = array('sqPerfil' => $perfis, 'sqUsuarioExterno' => $this->_getParam('usuario'));
        $arrPefil = $this->getService('UsuarioExternoPerfil')->findBy($criteria);

        $this->getService()->saveBindProfile($mapping, $dtosPerfis);

        $arrSqPefil = array();
        foreach ($arrPefil as $value) {
            $arrSqPefil[] = $value->getSqPerfil()->getSqPerfil();
        }

        foreach ($dtosPerfis as $key => $value) {
            if (in_array($value->getSqPerfil(), $arrSqPefil)) {
                unset($dtosPerfis[$key]);
            }
        }

        if ($dtosPerfis) {
            $this->getService()->sendMail($mapping, $dtosPerfis);
        }

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }

    public function _factoryParamsExtrasSave($data)
    {
        $mapping = new Core_Dto_Mapping($data, array('nuCpf'));
        return array($mapping);
    }

    public function editBindAction()
    {
        $params = $this->_getAllParams();
        $identifier = $this->_getParam('usuario');

        $this->view->sistemas = $this->getService('Sistema')->systemsActives();

        $dto = new Core_Dto_Mapping($params + array('inPerfilExterno' => '1'), array('sqSistema', 'inPerfilExterno'));
        $this->view->perfis = $this->getService('Perfil')->comboProfile($dto, FALSE);

        $this->view->binds = $this->getService()->findProfilesBind($identifier);

        $this->view->sistema = $params['sqSistema'];
        $this->_helper->layout()->disableLayout();
    }

    public function deleteProfileAction()
    {
        $params = $this->_getAllParams();
        $mapping = new Core_Dto_Mapping(
                $params, array('perfil', 'usuario')
        );

        $this->getService()->deleteProfile($mapping);
        $this->getService()->finish();
        $this->getMessaging()->addSuccessMessage('MN131');
        $this->_redirect('/usuario-externo/bind/id/' . $params['usuario']);
    }

    /**
     * Dados a serem utilizados para geração do PDF
     *
     * @see    Sica_Controller_Action::getDataPdf()
     * @return array
     */
    public function getDataPdf()
    {
        $this->_pdfName = 'Lista de Usuario Externo.pdf';
        $dtoSearch = new Core_Dto_Search($this->_getAllParams());
        return $this->getService()->listGridUsersExternals($dtoSearch);
    }

    /**
     * Tela visualizar Usuário Externo
     *
     * @return void
     */
    public function viewAction()
    {
        $identifier = $this->_getParam('id');
        $this->view->data = $this->getService()->findDataViewUserExternal($identifier);
        $this->view->binds = $this->getService()->findProfilesBind($identifier);
        $this->_helper->layout->disableLayout();
    }

    /**
     * @return void
     */
    public function toggleStatusAction()
    {
        $this->_codeMessageToggleActive = $this->_getMessageTranslate('MN073');
        $this->_codeMessageToggleInactive = $this->_getMessageTranslate('MN071');
        parent::toggleStatusAction();
    }

    /**
     *
     */
    public function listAction()
    {
        parent::listAction();
        $this->view->total = $this->getService()->listGridUsersExternalsCount($this->view->dto);
    }

}
