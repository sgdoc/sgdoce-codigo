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
 * Classe Controller Index
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         Funcionalidade
 * @version      1.0.0
 */
class Principal_FuncionalidadeController extends Sica_Controller_Action
{

    protected $_codeMessageToggleActive = 'MN059';
    protected $_codeMessageToggleInactive = 'MN057';
    protected $_service = 'Funcionalidade';

    /**
     *
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => 'Sica\Model\Entity\Funcionalidade',
        'mapping' => array(
            'sqMenu' => 'Sica\Model\Entity\Menu',
        )
    );

    /**
     * Realiza operacoes iniciais
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->view->sistemas = $this->getService('Sistema')->systemsActives();
        $this->view->arrMenu = array();
        $this->view->isEdit = false;
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
            if ($this->_getParam('sqMenu')) {
                $this->view->sqMenu = $this->_getParam('sqMenu');
                $this->view->arrMenu = $this->getService('Sistema')->findMenuBySystem($this->view->sqSistema);
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_Abstract::_redirectActionDefault()
     */
    protected function _redirectActionDefault($actionDefault)
    {
        $params = $this->getRequest()->getPost();
        $params['params'] = array('sqSistema' => $params['sqSistema']);
        $comeFromEdit = isset($params['isEdit']) ? (bool) $params['isEdit'] : false;
        $action = 'create';
        if ($comeFromEdit) {
            $action = 'index';
            $params['params']['sqMenu']= $params['sqMenu'];
        }
        $this->_redirectAction($action, NULL, NULL, $params);
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_CrudDto::createAction()
     */
    public function createAction()
    {
        parent::createAction();
        if ($this->_getParam('sqSistema')) {
            $this->view->data->getSqMenu()->getSqSistema()->setSqSistema($this->_getParam('sqSistema'));
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
                array('alias' => 's.noSistema'),
                array('alias' => 'm.noMenu'),
                array('alias' => 'f.noFuncionalidade'),
                array('alias' => 'f.inFuncionalidadePrincipal'),
                array('alias' => 'f.stRegistroAtivo'),
                array('alias' => 'f.sqFuncionalidade')
            )
        );

        return $array;
    }

    /**
     * Adição de dados extras para o método save()
     *
     * @see Core_Controller_Action_CrudDto::_factoryParamsExtrasSave()
     * @return void
     */
    protected function _factoryParamsExtrasSave($data)
    {
        $dtos = array();
        $dto = $this->_persistDataError['data'];
        if (array_key_exists('rota', $data)) {
            foreach ((array) $data['rota'] as $rota) {
                $objRota = Core_Dto::factoryFromData(
                                $rota, 'entity', array('entity' => 'Sica\Model\Entity\Rota')
                );
                $dto->addRota($objRota);
                $dtos[] = $objRota;
            }
        }

        return array($dtos);
    }

    /**
     * Recupera Menus por sistema
     *
     * @return void
     */
    public function findMenuAction()
    {
        $this->_helper->layout->disableLayout();

        $system = $this->_getParam('id');
        $this->view->menus = $this->getService('Sistema')->findMenuBySystem($system, TRUE);
    }

    /**
     * Tela visualizar funcionalidade
     *
     * @return void
     */
    public function viewAction()
    {
        $identifier = $this->_getParam('id');
        $this->view->data = $this->getService()->find($identifier);
        $this->_helper->layout->disableLayout();
    }

    /**
     * @return void
     */
    public function listFuncionalityMenuAction()
    {
        $sqMenu = $this->_getParam('sqMenu');
        $criteria = array('sqMenu' => $sqMenu);
        $this->view->funcionalidade = $this->getService()->findBy($criteria);
    }

    /**
     * Persistência de dados extras para o formulário(criação ou edição) caso aconteça erro
     *
     * @see    Core_Controller_Action_CrudDto::_persistDataErrorSave()
     * @return void
     */
    protected function _persistDataErrorSave($dto)
    {
        $sistema = new \Sica\Model\Entity\Sistema();
        $sistema->setSqSistema($this->_getParam('sqSistema'));
        $dto->getSqMenu()->setSqSistema($sistema);
        $this->_persistDataError['data'] = $dto;
    }

    /**
     * Dados a serem utilizados para geração do PDF
     *
     * @see    Sica_Controller_Action::getDataPdf()
     * @return array
     */
    public function getDataPdf()
    {
        $this->_pdfName = 'Lista de Funcionalidade.pdf';
        $dtoSearch = new Core_Dto_Search($this->_getAllParams());
        return $this->getService()->findFuncionalities($dtoSearch);
    }

    /**
     * Editar funcionalidade
     */
    public function editAction()
    {
        parent::editAction();
        $sqSistema = $this->view->data->getSqMenu()->getSqSistema()->getSqSistema();
        $this->view->arrMenu = $this->getService('Sistema')->findMenuBySystem($sqSistema);
        $this->view->isEdit = true;
    }

    /**
     * Remover funcionalidade
     *
     * @return void
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $result = array('success'=>false, 'message'=>'MN176');
        try {
            $this->getService()->delete($this->_getParam('id'));
            $result['success' ] = true;
            $result['message' ] = 'MN179';
        } catch (\Core_Exception_ServiceLayer $exc) {
            $result['message' ] = $exc->getMessage();
        }
        $this->_helper->json($result);
    }
}
