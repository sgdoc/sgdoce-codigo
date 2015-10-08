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

use Principal\Service\Laioute;

/**
 * SISICMBio
 *
 * Classe Controller Sistema
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         Sistema
 * @version      1.0.0
 */
class Principal_SistemaController extends Sica_Controller_Action
{

    /**
     * Nome do servico
     * @var string
     */
    protected $_service = 'Sistema';

    /**
     * @var string
     */
    protected $_codeMessageToggleActive = 'MN047';

    /**
     * @var string
     */
    protected $_codeMessageToggleInactive = 'MN045';
    protected $_optionsDtoEntity = array(
        'entity' => 'Sica\Model\Entity\Sistema',
        'mapping' => array(
            'sqArquitetura' => 'Sica\Model\Entity\Arquitetura',
            'sqLeiaute' => 'Sica\Model\Entity\Leiaute',
            'sqPessoaResponsavel' => array('sqPessoa' => 'Sica\Model\Entity\Pessoa')
        )
    );

    public function trocarSistemaAction()
    {
        $sqSistema = $this->_getParam('sqSistema');

        $this->view->sqSistema = $sqSistema;
    }

    public function systemLoggedAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $infoSystem = \Core_Integration_Sica_User::getInfoSystem();

        $this->_helper->json($infoSystem);
    }

    /**
     * Página inicial tela de pesquisa
     *
     * @see Core_Controller_Action_Abstract::indexAction()
     */
    public function indexAction()
    {
        $this->view->sistemas = $this->getService('Sistema')->getAll();
        $this->view->architetures = $this->getService()->findAllArchitetures();
        $this->view->status = $this->getService()->getStatus();
        parent::indexAction();
    }

    /**
     * Configuração da GRID
     *
     * @see    Core_Controller_Action_CrudDto::getConfigList()
     * @return array
     */
    public function getConfigList()
    {
        $array = array(
            'columns' => array(
                array('alias' => 's.sgSistema'),
                array('alias' => 's.noSistema'),
                array('alias' => 'r.noPessoa'),
                array('alias' => 'a.noArquitetura'),
                array('alias' => 's.stRegistroAtivo'),
            )
        );

        return $array;
    }

    /**
     * Filtra menus por usuário e sistema e envia resposta por JSON
     *
     * @return void
     */
    public function userSystemMenuAction()
    {
        $this->_helper->layout->disableLayout();

        $sqSistema = $this->_getParam('sqSistema');
        $sistema = $this->_userSystemMenu($sqSistema);
        $this->view->callback = $this->_getParam('callback', '');
        $this->view->response = \Zend_Json::encode($sistema);
    }

    /**
     * Tela de criação
     *
     * @see    Core_Controller_Action_CrudDto::createAction()
     * @return void
     */
    public function createAction()
    {
        parent::createAction();
        $this->view->architetures = $this->getService()->findAllArchitetures();
        $this->view->layouts = $this->getService()->findAllLayouts();

        if ($this->_helper->persist->has('sqArquitetura')) {
            $this->view->data->getSqArquitetura()->setSqArquitetura(
                $this->_helper->persist->get('sqArquitetura')
            );
        }

        if ($this->getHelper('persist')->has('responsavel')) {
            $this->view->responsavel = $this->getHelper('persist')->get('responsavel');
        } else {
            $this->view->responsavel = $responsavel = $this->getService('PessoaFisica')->findDataInstitucional(NULL);
        }
    }

    /**
     * @see    Core_Controller_Action_CrudDto::editAction()
     * @return void
     */
    public function editAction()
    {
        parent::editAction();
        $this->view->architetures = $this->getService()->findAllArchitetures();
        $this->view->layouts = $this->getService()->findAllLayouts();

        if ($this->getHelper('persist')->has('responsavel')) {
            $this->view->responsavel = $this->getHelper('persist')->get('responsavel');
        } else {
            $this->view->responsavel = $this->getService('PessoaFisica')->findDataInstitucional(
                $this->view->data->getSqPessoaResponsavel()->getSqPessoa()
            );
        }
    }

    /**
     * Persistência de dados extras para o formulário(criação ou edição) caso aconteça erro
     *
     * @see    Core_Controller_Action_CrudDto::_persistDataErrorSave()
     * @return void
     */
    protected function _persistDataErrorSave($dto)
    {
        $post = $this->_request->getPost();
        $this->_persistDataError['sqArquitetura'] = $this->_getParam('sqArquitetura');

        $responsavel = $this->getService('PessoaFisica')->findDataInstitucional($post['sqPessoaResponsavel']);
        $this->_persistDataError['responsavel'] = $responsavel;
    }

    /**
     * Recupera possíveis responsavéis para o sistema, envia resposta por JSON
     *
     * @return void
     */
    public function findResponsibleAction()
    {
        $this->_helper->layout->disableLayout();
        $nome = $this->_getParam('query');
        $validate = $this->_getParam('validate', TRUE);
        $data = $this->getService()->findResponsible($nome);

        if (0 === count($data) && $validate) {
            $data = array(
                '__NO_CLICK__' => Core_Registry::getMessage()->_('MN115')
            );
        }

        $this->_helper->json($data);
    }

    /**
     * Mudança de sistema por usuário logado
     *
     * @return void
     */
    public function changeSystemAction()
    {
        $this->_helper->layout->setLayout('iframe');

        $_entityUsuarioPerfil = array(
            'entity' => 'Sica\Model\Entity\UsuarioPerfil',
            'mapping' => array(
                'sqUsuario' => 'Sica\Model\Entity\Usuario',
                'sqSistema' => 'Sica\Model\Entity\Sistema',
                'sqUnidadeOrgPessoa' => array('sqPessoa' => 'Sica\Model\Entity\UnidadeOrg'),
                'sqPerfil' => array('sqPeril' => 'Sica\Model\Entity\Perfil')
            )
        );

        $user = Zend_Auth::getInstance()->getIdentity();
        $sqSistema = $this->_getParam('sqSistema', FALSE);

        $data = array(
            'sqUsuario' => $user->sqUsuario,
            'sqSistema' => $sqSistema,
            'sqUnidadeOrgPessoa' => NULL
        );

        $map = array('sqUsuario', 'sqSistema', 'sqUnidadeOrgPessoa');
        $dto = Core_Dto::factoryFromData($data, 'Core_Dto_Mapping', $map);

        $unit = $this->getService('UsuarioPerfil')->userUnit($dto);

        if (count($unit) == 0) {
            $this->_redirect('/usuario/logout');
        }

        if (count($unit) == 1) {
            $this->_setSessionInfoUser(current($unit), $dto);
            $sistema = $this->_userSystemMenu($sqSistema);
            $this->_redirectSystem($sistema);
        }

        $session = new Core_Session_Namespace('USER', FALSE, TRUE);
        $session->profile = count($unit);

        $request = $this->getRequest();
        $this->view->lastUrl = $request->getHeader('referer');

        $this->view->listUnidade = $unit;
        $this->view->sistema = $sqSistema;
    }

    /**
     * Atribuir dados extras a SESSION['USER']
     *
     * @param  integer $unit
     * @param  object  $dto
     * @return void
     */
    protected function _setSessionInfoUser($unit, $dto)
    {
        $data = array('sqUsuario' => $dto->getSqUsuario(),
            'sqSistema' => $dto->getSqSistema(),
            'sqUnidadeOrgPessoa' => $unit['sqPessoa']);
        $dto->setInput($data);
        $data = $this->getService('UsuarioPerfil')->unitProfile($dto);

        $session = new Core_Session_Namespace('USER', FALSE, TRUE);
        $session->acl = NULL;

        if (!\Core_Integration_Sica_User::getUserProfileExternal()) {
            $session->sqUnidadeOrg = current($data)->getSqUnidadeOrgPessoa()->getSqPessoa();
            $session->noUnidadeOrg = current($data)->getSqUnidadeOrgPessoa()->getNoPessoa();
        }

        $session->sqPerfil = current($data)->getSqPerfil()->getSqPerfil();
        $session->noPerfil = current($data)->getSqPerfil()->getNoPerfil();
        $session->sqSistema = $dto->getSqSistema();
    }

    /**
     * @param  integer $sqSistema
     * @return array
     */
    protected function _userSystemMenu($sqSistema)
    {
        $user = new Core_Session_Namespace('USER', FALSE, TRUE);
        if (FALSE === isset($user->sqUsuario)) {
            return array();
        }

        $data = array(
            'sqUsuario' => $user->sqUsuario,
            'sqSistema' => $sqSistema,
            'sqPerfil' => $user->sqPerfil
        );

        $options = array(
            'entity' => 'Sica\Model\Entity\UsuarioPerfil',
            'mapping' => array(
                'sqUsuario' => array('sqUsuario' => 'Sica\Model\Entity\Usuario'),
                'sqSistema' => array('sqSistema' => 'Sica\Model\Entity\Sistema'),
                'sqPerfil' => array('sqPerfil' => 'Sica\Model\Entity\Perfil')
            )
        );

        $dto = Core_Dto::factoryFromData($data, 'entity', $options);
        $menu = $this->getService('Menu')->userMenu($dto);
        $session = new Core_Session_Namespace('USER', FALSE, TRUE);

//        $arrSistema = \Core_Integration_Sica_User::getInfoSystem($sqSistema);
//        $session->{strtoupper($arrSistema['sgSistema'])} = $menu;

        $session->MenuExterno = $menu;

        $sistema = $this->getService()->find($sqSistema)->toArray();
        $sistema['sqSystemEnconded'] = base64_encode($sistema['sqSistema']);
        $sistema['sqLeiaute'] = $sistema['sqLeiaute']->getSqLeiaute();
        $sistema['sqArquitetura'] = $sistema['sqArquitetura']->getSqArquitetura();

        return $sistema;
    }

    /**
     * Recupera sistema de acordo com Identificador
     * @return  void
     */
    public function findAction()
    {
        $identifier = $this->_getParam('id', FALSE);
        $data = array();

        if ($identifier) {
            $data = $this->getService()->findById($identifier);
            $data['nuCpf'] = Zend_Filter::filterStatic(
                $data['nuCpf'], 'MaskNumber', array(array(
                'mask' => 'cpf'
            )), array('Core_Filter'));

            $data['stRegistroAtivo'] = (int) $data['stRegistroAtivo'];
        }

        $this->_helper->json($data);
    }

    /**
     * Autocomplete sistema
     *
     * @return void
     */
    public function autocompleteAction()
    {
        $noSistema = $this->_getParam('query');
        $data = $this->getService()->findByNoSistema($noSistema);

        $this->_helper->json($data);
    }

    /**
     * Recupera Menus por sistema
     *
     * @return void
     */
    public function findMenuAction()
    {
        $system = $this->_getParam('id');
        $this->view->menus = $this->getService()->findMenuBySystem($system, TRUE);

        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
    }

    /**
     * @param  array $sistema
     * @return void
     */
    protected function _redirectSystem($sistema)
    {
        if ($sistema['sqLeiaute'] == Laioute::LAYOUT_DEFAULT) {
            $this->_redirect('/iframe/sistema/sys/' . $sistema['sqSystemEnconded']);
        }

        $this->_redirect($sistema['txUrl']);
    }

    /**
     * Tela visualizar sistema
     *
     * @return void
     */
    public function viewAction()
    {
        $this->_helper->layout->disableLayout();
        $identifier = $this->_getParam('id');
        $this->view->data = $this->getService()->findSystemFull($identifier);
    }

    /**
     * Renderiza logo sistema do sistema de acordo com identificador
     *
     * @return void
     */
    public function renderLogoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $identifier = $this->_getParam('id');
        $contentFile = $this->getService()->retrieveLogo($identifier);

        if (!$contentFile) {
            $this->_response->setHttpResponseCode(404);
            return;
        }

        $this->_response
            ->setHeader('Content-Type', 'image/png')
            ->sendHeaders()
            ->setBody($contentFile)
            ->sendResponse();

        exit;
    }

    /**
     * Dados a serem utilizados para geração do PDF
     *
     * @see    Sica_Controller_Action::getDataPdf()
     * @return array
     */
    public function getDataPdf()
    {
        $this->_pdfName = 'Lista de Sistema.pdf';
        $dtoSearch = new \Core_Dto_Search($this->_getAllParams());
        return $this->getService()->findSystemsFull($dtoSearch);
    }

    protected function _factoryParamsExtrasSave($data)
    {
        if (isset($data['sqSistema']) && $data['sqSistema']) {
            $entity = $this->getService()->find($data['sqSistema']);
        } else {
            $entity = new \Sica\Model\Entity\Sistema();
        }

        return array($entity);
    }

}
