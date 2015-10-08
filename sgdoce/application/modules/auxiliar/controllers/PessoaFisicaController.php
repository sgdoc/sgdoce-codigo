<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
/**
 * Classe para Controller de Assunto
 *
 * @package  Auxiliar
 * @category Controller
 * @name     PessoaFisica
 * @version  1.0.0
 */
class Auxiliar_PessoaFisicaController extends \Core_Controller_Action_CrudDto
{
    protected $_messageCreate = 'MN013';
    protected $_messageEdit   = 'MN043';

    /**
     * @var string
     */
    protected $_service = 'VwPessoaFisica';

    protected $_optionsDtoEntity = array(
            'entity' => '\Sgdoce\Model\Entity\VwPessoaFisica',
            'mapping' => array(
                    'sqPais'       => '\Sgdoce\Model\Entity\VwPais',
                    'sqTipoPessoa' => '\Sgdoce\Model\Entity\VwTipoPessoa'
            )
    );

    public function init()
    {
        parent::init();
        $comboDas        = $this->getService('VwChefia')->getComboDas();
        $listaPais       = $this->getService('VwPais')->comboPais();
        $listaDocumentos = $this->getService('VwTipoDocumento')->listTipoDocumento();

        $this->view->listaDocumentos = $listaDocumentos;
        $this->view->listaPais = $listaPais;
        $this->view->comboDas  = $comboDas;
        $this->view->user      = array(
                'id'   => Core_Integration_Sica_User::getUserId(),
                'name' => $this->getSicaUsername()
        );
        $this->view->pessoa      = Core_Integration_Sica_User::getPersonId();
    }

    public function getSicaUsername()
    {
        $sicaUser = $this->getCoreSica();

        if(isset($sicaUser->noUsuario)) {
            return $sicaUser->noUsuario;
        }
    }

    public function getCoreSica()
    {
        return Core_Integration_Sica_User::get();
    }

    public function editAction()
    {
        $this->_helper->layout->setLayout('modal');
        $sqPessoaSgdoce = $this->getService('PessoaSgdoce')->findPessoaBySqCorporativo(
                new \Core_Dto_Search(array('sqPessoaCorporativo' => $this->_getParam('id')))
        );

        if($sqPessoaSgdoce) {
            $this->view->sqPessoaSgdoce           = $sqPessoaSgdoce->getSqPessoaSgdoce();
            $this->view->txInformacaoComplementar = $sqPessoaSgdoce->getTxInformacaoComplementar();
        }

        $this->view->data = $this->getService('VwPessoaFisica')->find($this->_getParam('id'));
        $this->view->new  = $this->_getParam('new') ? : null;
        $this->view->campoPessoa = $this->_getParam('campoPessoa');
        $this->view->campoCpf = $this->_getParam('campoCpf');
        $this->view->form = $this->_getParam('form');
    }

    public function indexAction()
    {
        $this->_redirect('/auxiliar/pessoa-fisica/create');
    }

    public function _factoryParamsExtrasSave($params)
    {
        $arrDto = array();

        $arrDto['params'] = new Core_Dto_Search($params);

        if(isset($params['txJustificativa']) && $params['txJustificativa']) {
            $arrData = array(
                    'dtInclusao'      => Zend_Date::now()->get('dd/MM/YYYY'),
                    'txJustificativa' => $params['txJustificativa'],
                    'sqPessoaAutora'=> Core_Integration_Sica_User::getPersonId(),
                    'sqPessoaAutoriza'=> $params['sqPessoaResponsavel']
            );
            $arrDto['sqCadastroSemCpf'] = Core_Dto::factoryFromData($arrData, 'entity', array(
                    'entity' => '\Sgdoce\Model\Entity\VwCadastroSemCpf'
            ));
        }

        return $arrDto;
    }

    public function saveAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();

        if($this->_request->isXmlHttpRequest()) {
            $result = array();

            try {
                $this->saveAjaxMode();
                $pessoa = parent::_save();
                $this->getService()->finish();

                if($pessoa) {
                    $result['return']         = true;
                    $result['sqPessoa']       = $pessoa['sqPessoa'];
                    $result['sqPessoaSgdoce'] = $pessoa['sqPessoaSgdoce'];
                    $result['campoPessoa'] = $pessoa['campoPessoa'];
                    $result['campoCpf'] = $pessoa['campoCpf'];
                    $result['nuCpf'] = $pessoa['nuCpf'];
                    $result['form'] = $pessoa['form'];
                    $result['noPessoa'] = $pessoa['noPessoa'];
                }
            } catch(\Exception $e) {
                $result['return']  = false;
                $result['message'] = $e->getMessage();
            }

            $this->_helper->json($result);
        }
    }

    private function saveAjaxMode()
    {
        if((int) $this->_request->getPost('sqNacionalidadeBrasileira') === 1) {
            $this->_request->setPost('sqPais', '1');
        }
    }

    public function searchCpfAction()
    {
        if($this->_getParam('nuCpf')) {
            $criteria = array('nuCpf' => Zend_Filter::filterStatic($this->_getParam('nuCpf'), 'Digits'));
        }

        $result = $this->getService('VwPessoaFisica')->searchCpf($criteria);
        $this->_helper->json($result);
    }

    public function searchNomePessoaAction()
    {
        $response = array();

        if($this->_getParam('noPessoaFisica')) {
            $criteria = array('noPessoaFisica' => $this->_getParam('noPessoaFisica'));
        }

        $result = $this->getService('VwPessoaFisica')->findBy($criteria);

        if($result) {
            $response = array(
                    'sqPessoa' => $result->getSqPessoaFisica()->getSqPessoa()
            );
        }

        $this->_helper->json($response);
    }

    public function visualizarPessoaFisicaAction()
    {
        $criteria = array(
                'sqPessoa' => $this->_getParam('sqPessoa')
        );
        $dtoSearch = Core_Dto::factoryFromData($criteria, 'search');
        $entity   = $this->getService('VwPessoaFisica')->getDadosPessoaFisica($criteria);


        $this->view->entity       = $entity;
        $this->_helper->layout()->disableLayout();
    }

    public function searchPessoaFisicaAction() {
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();

        $dto    = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $result = $this->getService('VwPessoaFisica')->searchPessoaFisica($dto);

        return $this->_helper->json($result);
    }

    public function createAction()
    {
        $this->_helper->layout->setLayout('modal');
        parent::createAction();
        $this->view->campoPessoa = $this->_getParam('campoPessoa');
        $this->view->campoCpf = $this->_getParam('campoCpf');
        $this->view->form = $this->_getParam('form');
    }
}
