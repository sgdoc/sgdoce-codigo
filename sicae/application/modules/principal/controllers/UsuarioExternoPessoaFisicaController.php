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
 * Classe Controller Usuario Externo Pessoa Fisica
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         Usuario
 * @version      1.0.0
 * @since        2012-07-24
 */
class Principal_UsuarioExternoPessoaFisicaController extends Sica_Controller_Action
{

    protected $_messageCreate = 'Cadastro realizado com sucesso.
                                 Um link foi enviado ao seu e-mail para ativação do cadastro.';

    /**
     * Nome do Serviço
     * @var string
     */
    protected $_service = 'UsuarioExternoPessoaFisica';

    /**
     * Mapeamento para Dto da entidade do Usuario
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => '\Sica\Model\Entity\UsuarioExterno'
    );

    public function init()
    {
        parent::init();

        if ($this->getRequest()->getActionName() == 'create') {
            $this->_helper->layout->setLayout('create-usuario-externo');
        }

        $this->view->cmb = $this->getService('UsuarioExterno')->getAllCombos();
    }

    public function editAction()
    {
        parent::editAction();

        if ($this->view->data) {
            $sqEstado = $this->view->data->getDadoComplementar()->getSqEstado()->getSqEstado();

            if ($sqEstado) {
                $this->view->cmb['sqMunicipio'] = $this->getService('Endereco')->comboMunicipio($sqEstado);
            }
            $this->view->dtCadastro = \Zend_Date::now()->toString('yyyy-MM-dd hh:mm:ss');

            $this->_helper->layout->setLayout('edit-usuario-externo');
        } else {
            $urlSica = rtrim(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('urlSica'), '/');
            $this->_redirect($urlSica . '/usuario-externo/login');
        }
    }

    public function _redirectActionDefault($param)
    {
        $action = 'login';
        $controller = 'usuario-externo';

        if ($this->_getParam('sqUsuarioExterno')) {
            $action = 'index';
            $controller = 'index';
        }

        return $this->_redirectAction($action, $controller, 'principal');
    }

    /**
     * @param type $data
     * @return type
     */
    public function _factoryParamsExtrasSave($data)
    {
        return array(new Core_Dto_Mapping($this->getRequest()->getPost(), $this->getRequest()->getPost()));
    }

    public function createAction()
    {
        $session = Core_Integration_Sica_User::has();
        if ($session){
            Core_Integration_Sica_User::destroy();

            $this->_redirect('usuario-externo/login');
        }
        parent::createAction();
        $this->view->dtCadastro = \Zend_Date::now()->toString('yyyy-MM-dd hh:mm:ss');
        $this->view->btCancelar = isset($_SESSION["USER"]);
    }

}
