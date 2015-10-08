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
 * @name         Perfil
 * @version      1.0.0
 * @since        2012-08-17
 */
class Principal_PerfilController extends Sica_Controller_Action
{
    protected $_service = 'Perfil';

    /**
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity'  => 'Sica\Model\Entity\Perfil',
        'mapping' => array(
            'sqSistema'    => array('sqSistema' => 'Sica\Model\Entity\Sistema'),
            'sqTipoPerfil' => array('sqTipoPerfil' => 'Sica\Model\Entity\TipoPerfil')
        )
    );

    protected $_codeMessageToggleActive      = 'MN055';
    protected $_codeMessageToggleInactive    = 'MN053';

    public function indexAction()
    {
        $this->getCombos();
        if ($this->_getParam('sqSistema')) {
            $this->view->sqSistema = $this->_getParam('sqSistema');
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
        $action = 'create';
        $this->_redirectAction($action, NULL, NULL, $params);
    }

    /**
     * Combos padrões para tela de cadrastro e ediçao de um perfil
     * @return void
     */
    public function getCombos()
    {
        $this->view->sistemas    = $this->getService('Sistema')->systemsActives();
        $this->view->tiposPerfil = $this->getService('TipoPerfil')->getComboDefault();
    }

    /**
     * @return void
     */
    public function comboProfileAction()
    {
        $params = $this->_getAllParams();
        $dto    = new Core_Dto_Mapping($params, array('sqSistema', 'inPerfilExterno'));
        $this->view->profile = $this->getService()->comboProfile($dto);

        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_CrudDto::getConfigList()
     */
    public function getConfigList()
    {
        $array = array(
            'columns' => array(
                array('alias' => 'p.noPerfil'),
                array('alias' => 'p.inPerfilExterno'),
                array('alias' => 'p.stRegistroAtivo')
            )
        );

        return $array;
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_CrudDto::editAction()
     */
    public function editAction()
    {
        parent::editAction();
        $this->getCombos();
        $system                 = $this->view->data->getSqSistema()->getSqSistema();
        $this->view->menu       = $this->getService('Sistema')
                                       ->findMenuBySystem($system);
        $this->view->menuAcesso = $this->getService('PerfilFuncionalidade')
                                       ->menuAcessoById($this->view->data->getSqPerfil());

    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_CrudDto::createAction()
     */
    public function createAction()
    {
        if ($this->getHelper('persist')->has('data')) {
            $this->view->data                = $this->getHelper('persist')->get('data');
            $this->view->perfilPadraoExterno = $this->getHelper('persist')->get('perfilPadraoExterno');
            $this->view->menuAcesso          = array(array('sqMenu' => $this->getHelper('persist')->get('sqMenu')));

            if ($this->getHelper('persist')->get('sqMenu') == '0') {
                $this->view->menuAcesso          = array(array(),array());
            }

            $system                          = $this->view->data->getSqSistema()->getSqSistema();
            $this->view->menu                = $this->getService('Sistema')->findMenuBySystem($system);
        } else {
            $this->view->data               = $this->getService()->getDto();
            $this->view->menu               = array();
            $this->view->perfilPadraoExteno = NULL;
        }
        if ($this->_getParam('sqSistema')) {
            $this->view->data->getSqSistema()->setSqSistema($this->_getParam('sqSistema'));
        }
        $this->getCombos();
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_CrudDto::_factoryParamsExtrasSave()
     */
    protected function _factoryParamsExtrasSave($data)
    {
        if (array_key_exists('sqFuncionalidade', $data)) {
            foreach ($data['sqFuncionalidade'] as $value) {
                $arrayDto[] = Core_Dto::factoryFromData(
                                array('sqFuncionalidade' => $value),
                                'entity', array('entity' => 'Sica\Model\Entity\Funcionalidade')
                );
            }
        } else {
            $arrayDto = NULL;
        }

        if (isset($data['perfilPadraoExterno'])) {
            $map = array('perfilPadraoExterno');
            $arrayDto['perfilPadraoExterno'] = Core_Dto::factoryFromData(array(
                'perfilPadraoExterno' => $data['perfilPadraoExterno']
                ),
                             'Core_Dto_Mapping',$map);
        }

        $this->_persistDataError['perfilPadraoExterno'] = $data['perfilPadraoExterno'];
        $this->_persistDataError['sqMenu'] = $data['sqMenu'];

        return array($arrayDto);
    }

    /**
     * Visualização dos dados do perfil.
     * @return void
     */
    public function viewAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();
        $dto    = Core_Dto::factoryFromData($params, 'entity',$this->_optionsDtoEntity);

        $this->view->data = $this->getService()->view($dto);
    }

    /**
     * Funcionalidades de acordo com o perfil
     * @return json
     */
    public function funcionalityAction()
    {
        $sqPerfil = $this->_getParam('sqPerfil');

        $data = $this->getService('PerfilFuncionalidade')->funcionalityByProfile($sqPerfil);

        $this->_helper->json($data);
    }

    /**
     * Dados a serem utilizados para geração do PDF
     *
     * @see    Sica_Controller_Action::getDataPdf()
     * @return array
     */
    public function getDataPdf()
    {
        $this->_pdfName = 'Lista de Perfil.pdf';
        $dtoSearch      = new \Core_Dto_Search($this->_getAllParams());

        return $this->getService()->findProfilesFull($dtoSearch, TRUE);
    }

    public function checkUserExistsProfileAction()
    {
        $params = $this->_getAllParams();
        $dto    = Core_Dto::factoryFromData($params, 'entity',$this->_optionsDtoEntity);

        $result = $this->getService()->checkUserExistsProfile($dto);

        $data = array(
            'message' => ($params['stRegistroAtivo'] == '1')
                        ?Core_Registry::getMessage()->translate('MN052')
                        :Core_Registry::getMessage()->translate('MN054'),
            'exists' => (count($result))?TRUE:FALSE
        );

        if ($data['exists'] === TRUE && $params['stRegistroAtivo'] == '1') {
            $msg = Core_Registry::getMessage()->translate('MN015');
            $data['message'] = str_replace('<quantidade>', $result['users'], $msg);
        }

        $this->_helper->json($data);

    }

    /**
     * Remover perfil
     *
     * @return void
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $result = array('success'=>false, 'message'=>'MN187');
        try {
            $this->getService()->delete($this->_getParam('id'));
            $result['success' ] = true;
            $result['message' ] = 'MN188';
        } catch (\Exception $exc) {
        // } catch (\Core_Exception_ServiceLayer $exc) {
            $result['message' ] = $exc->getMessage();
        }
        $this->_helper->json($result);
    }

}
