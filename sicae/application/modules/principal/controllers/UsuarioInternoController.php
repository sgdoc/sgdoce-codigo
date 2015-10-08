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
class Principal_UsuarioInternoController extends Sica_Controller_Action
{

    protected $_codeMessageToggleActive = 'MN073';
    protected $_codeMessageToggleInactive = 'MN071';

    /**
     * Nome do Serviço
     * @var string
     */
    protected $_service = 'Usuario';

    /**
     * Mapeamento para Dto da entidade do Usuario
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => 'Sica\Model\Entity\Usuario',
        'mapping' => array(
            'sqPessoa' => 'Sica\Model\Entity\Pessoa'
        )
    );

    public function init()
    {
        $sqPerfil = \Core_Integration_Sica_User::getUserProfile();

        if ($sqPerfil) {
            $this->_sqTipoPerfil = $this->getService('Perfil')
                            ->find($sqPerfil)->getSqTipoPerfil()->getSqTipoPerfil();
            parent::init();
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
                    'alias' => 'u.sqUsuario'
                ),
                array(
                    'alias' => 'pf.nuCpf'
                ),
                array(
                    'alias' => 'p.noPessoa'
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
        return $this->getService()->listGridUsersInternals($dto);
    }

    public function indexAction()
    {
        $this->view->id = $this->_getParam('id');

        $this->verificaTipoPerfil($this->_sqTipoPerfil);
        $this->view->sistemas = $this->getService('Sistema')->systemsActives($this->_sqTipoPerfil, array(2));
    }

    public function verificaTipoPerfil($sqTipoPerfil, array $arrTipoPerfil = array(1, 2, 3))
    {
        if (!in_array($sqTipoPerfil, $arrTipoPerfil)) {
            \Core_Messaging_Manager::getGateway('Service')->addErrorMessage('MN148');
            throw new \Core_Exception_ServiceLayer_Verification();
        }
    }

    /**
     * Tela visualizar Usuário Interno
     *
     * @return void
     */
    public function viewAction()
    {
        $identifier = $this->_getParam('id');
        $this->view->data = $this->getService()->findDataViewUserInternal($identifier);
        $this->view->binds = $this->getService()->findProfilesBind($identifier);
        $this->_helper->layout->disableLayout();
    }

    /**
     * Salva o usuario
     */
    public function _save()
    {
        $sqPessoa = $this->_getParam('sqPessoa');
        parent::_save();

        $entity = $this->getService()->findOneBy(array('sqPessoa' => $sqPessoa));
        $this->_addMessageSave();
        $this->_redirect("/principal/usuario-interno/index/id/{$entity->getSqUsuario()}/");
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
            $this->_redirect('/usuario-interno/');
        }

        $this->view->binds = $this->getService()->findProfilesBind($identifier);

        if (0 === count($this->view->binds)) {
            $this->getMessaging()->addErrorMessage('MN016');
            $this->getMessaging()->dispatchPackets();
        }

        $this->view->sistemas = $this->getService('Sistema')
                ->getSistemasPorTipoPerfil($this->_sqTipoPerfil, array(1, 2), FALSE);
        $this->view->sqPerfil = $this->_sqTipoPerfil;
    }

    public function deleteProfileAction()
    {
        $params = $this->_getAllParams();
        $mapping = new Core_Dto_Mapping(
                $params, array('unidade', 'perfil', 'usuario')
        );

        $this->getService()->deleteProfile($mapping);
        $this->getService()->finish();
        $this->getMessaging()->addSuccessMessage('MN131');
        $this->_redirect('/usuario-interno/bind/id/' . $params['usuario']);
    }

    public function createBindAction()
    {
        $this->view->sistemas = $this->getService('Sistema')->systemsActives($this->_sqTipoPerfil);

        $this->view->tpOperacao = TRUE;
        $this->_helper->layout()->disableLayout();
    }

    public function editBindAction()
    {
        $params = $this->_getAllParams();
        $this->view->sistemas = $this->getService('Sistema')->systemsActives();

        $dto = new Core_Dto_Mapping($params + array('inPerfilExterno' => '0'), array('sqSistema', 'inPerfilExterno'));
        $this->view->perfis = $this->getService('Perfil')->comboProfile($dto, FALSE);

        $dtoMapping = new Core_Dto_Mapping($params, array('unidade', 'usuario'));
        $this->view->perfisUnidade = $this->getService('UsuarioPerfil')->getProfilesByUnit($dtoMapping);

        $this->view->unidade = $this->getService('UnidadeOrganizacional')->find($dtoMapping->getUnidade());
        $this->view->sistema = $params['sqSistema'];
        $this->_helper->layout()->disableLayout();
    }

    public function perfisAction()
    {
        $params = $this->_getAllParams();
        $dto = new Core_Dto_Mapping($params + array('inPerfilExterno' => '0'), array('sqSistema', 'inPerfilExterno'));
        $this->view->perfis = $this->getService('Perfil')->comboProfile($dto, FALSE);
        $this->_helper->layout()->disableLayout();
        $this->render('table-perfis');
    }

    public function perfisUnidadeAction()
    {
        $params = $this->_getAllParams();
        $dto = new Core_Dto_Mapping($params, array('unidade', 'usuario'));
        $perfisUnidade = $this->getService('UsuarioPerfil')->getProfilesByUnit($dto);
        $this->_helper->json($perfisUnidade);
    }

    public function saveBindProfileAction()
    {
        $params = $this->_getAllParams();
        $perfis = $this->_getParam('perfil');
        $mapping = new Core_Dto_Mapping($params, array('usuario', 'unidade', 'sqSistema'));

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

        $criteria = array(
            'sqPerfil' => $perfis,
            'sqUnidadeOrgPessoa' => $this->_getParam('unidade'),
            'sqUsuario' => $this->_getParam('usuario')
        );
        $arrPefil = $this->getService('UsuarioPerfil')->findBy($criteria);

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

    /**
     * Dados a serem utilizados para geração do PDF
     *
     * @see    Sica_Controller_Action::getDataPdf()
     * @return array
     */
    public function getDataPdf()
    {
        $this->_pdfName = 'Lista de Usuario.pdf';
        $dtoSearch = new Core_Dto_Search($this->_getAllParams());
        return $this->getService()->findUsers($dtoSearch);
    }

    public function _getFailTargetMap()
    {
        return parent::_getFailTargetMap() + array('index' => '/index/blank');
    }

}
