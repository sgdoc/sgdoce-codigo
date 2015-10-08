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
 * Classe Controller Sistema
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         Menu
 * @version      1.0.0
 * @since        2012-08-02
 */
class Principal_MenuController extends Sica_Controller_Action
{

    /**
     * Nome do servico
     * @var string
     */
    protected $_service = 'Menu';

    /**
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => 'Sica\Model\Entity\Menu',
        'mapping' => array(
            'sqSistema' => 'Sica\Model\Entity\Sistema',
            'sqMenuPai' => array('sqMenu' => 'Sica\Model\Entity\Menu')
        )
    );

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        parent::init();
        $sistema = $this->getService('Sistema')->systemsActives();
        $this->view->sistemas = $sistema;
        $this->view->pageTitle = 'Pesquisar Menu';
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_CrudDto::indexAction()
     */
    public function indexAction()
    {
        parent::indexAction();
        if ($this->_getParam('sqSistema')) {
            $this->view->sqSistema = $this->_getParam('sqSistema');
        }
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_CrudDto::listAction()
     */
    public function listAction()
    {
        $this->_helper->layout->disableLayout();
        $system = $this->_getParam('sqSistema');

        $this->view->grid = $this->getService('Sistema')->findMenuBySystem($system, FALSE, 'DESC');
        $this->view->ordenar = $this->_getParam('ordenar');
        $this->view->message = $this->_getMessageTranslate('MN016');
    }

    /**
     * Action para troca de status
     * @return void
     */
    public function switchStatusAction()
    {
        $sqMenu = $this->_getParam('sqMenu');
        $msg = $this->getService()->switchStatus($sqMenu);
        $msg = $this->_getMessageTranslate($msg);
        $this->_helper->json(array('success' => TRUE, 'message' => $msg));
    }

    /**
     * Action para ordenação de itens de menu
     * @return void
     */
    public function orderAction()
    {
        $params = $this->_getAllParams();
        unset($params['controller'], $params['action'], $params['module']);

        $map = array('sqMenu', 'direcao', 'sqSistema');
        $dto = Core_Dto::factoryFromData($params, 'Core_Dto_Mapping', $map);
        $this->getService()->order($dto);

        $this->_helper->json(TRUE);
    }

    /**
     *
     */
    public function findMenuAction()
    {
        $_optionsDtoEntityHierarq = array(
            'entity' => 'Sica\Model\Entity\MenuHierarqManter',
            'mapping' => array(
                'sqSistema' => 'Sica\Model\Entity\Sistema',
                'sqMenuPai' => array('sqMenu' => 'Sica\Model\Entity\Menu')
            )
        );
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params, 'Entity', $_optionsDtoEntityHierarq);
        $this->view->menus = $this->getService()->findMenu($dto);

        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_CrudDto::_factoryParamsExtrasSave()
     */
    protected function _factoryParamsExtrasSave($data)
    {
        $map = array('abaixoDe');
        $params = array(
            'abaixoDe' => $data['abaixoDe']
        );

        $dtoMapping = Core_Dto::factoryFromData($params, 'Core_Dto_Mapping', $map);

        return $dtoMapping;
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_Abstract::_redirectActionDefault()
     */
    protected function _redirectActionDefault($actionDefault)
    {
        $params = $this->getRequest()->getPost();
        $params['params'] = array('sqSistema' => $params['sqSistema']);
        $this->_redirectAction('create', NULL, NULL, $params);
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_CrudDto::createAction()
     */
    public function createAction()
    {
        parent::createAction();
        if ($this->_getParam('sqSistema')) {
            $this->view->data->getSqSistema()->setSqSistema($this->_getParam('sqSistema'));
        }
        $this->view->pageTitle = 'Cadastrar Menu';
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_CrudDto::editAction()
     */
    public function editAction()
    {
        parent::editAction();
        $this->view->pageTitle = 'Alterar Menu';
        $menus = $this->getService('Sistema')
                ->findMenuBySystem($this->view->data->getSqSistema()->getSqSistema());

        foreach ($menus as $key => $menu) {
            if ($this->view->data->getSqMenu() == $menu['sqMenu']) {
                unset($menus[$key]);
                break;
            }
        }

        $this->view->menus = $menus;

        $_optionsDtoEntityHierarq = array(
            'entity' => 'Sica\Model\Entity\MenuHierarqManter',
            'mapping' => array(
                'sqSistema' => 'Sica\Model\Entity\Sistema',
                'sqMenuPai' => array('sqMenu' => 'Sica\Model\Entity\Menu')
            )
        );

        $menuParam = NULL == $this->view->data->getSqMenuPai() ? NULL : $this->view->data->getSqMenuPai()->getSqMenu();
        $params = array(
            'nuNivel' => (NULL == $this->view->data->getSqMenuPai()) ? '1' : NULL,
            'sqMenuPai' => $menuParam,
            'sqSistema' => $this->view->data->getSqSistema()->getSqSistema(),
            'sqMenuLista' => $this->_getParam('id'),
            'removeDaLista' => true,
        );

        $dto = Core_Dto::factoryFromData($params, 'Entity', $_optionsDtoEntityHierarq);
        $this->view->dependencia = $this->getService()->findMenu($dto);
    }

    public function getDataPdf()
    {
        $this->_pdfName = 'Lista de Menu.pdf';
        $data = $this->getService('Sistema')->findMenuBySystem($this->_getParam('sqSistema'));
        $data['sistema'] = $this->getService('Sistema')->findById($this->_getParam('sqSistema'));
        return $data;
        return $this->getService('Sistema')->findMenuBySystem($this->_getParam('sqSistema'));
    }

    public function menuFuncionalityAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params, 'Entity', $this->_optionsDtoEntity);
        $perfil = isset($params["sqPerfil"]) ? $params["sqPerfil"] : NULL;
        $this->view->data = $this->getService('Funcionalidade')->menuFuncionality($dto, $perfil);
    }

    /**
     * Remover menu
     *
     * @return void
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $result = array('success'=>false, 'message'=>'MN181');
        try {
            $this->getService()->delete($this->_getParam('id'));
            $result['success' ] = true;
            $result['message' ] = 'MN184';
        } catch (\Core_Exception_ServiceLayer $exc) {
            $result['message' ] = $exc->getMessage();
        }
        $this->_helper->json($result);
    }

}