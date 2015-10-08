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
class Auxiliar_PessoaJuridicaController extends \Core_Controller_Action_CrudDto
{
    protected $_messageCreate = 'MN013';
    protected $_messageEdit   = 'MN043';

    /**
     * @var string
     */
    protected $_service = 'VwPessoaJuridica';

    protected $_optionsDtoEntity = array(
        'entity' => '\Sgdoce\Model\Entity\VwPessoaJuridica',
    );

    public function init()
    {
        parent::init();

        $combo = array();
        $combo['naturezaJuridica'] = $this->getService('VwNaturezaJuridica')->comboSociedade();

        $this->view->combo = $combo;
    }

    public function indexAction()
    {
        $this->_redirect('/auxiliar/pessoa-juridica/create');
    }

    public function editAction()
    {
        $this->_helper->layout->setLayout('modal');
        $this->view->new  = $this->_getParam('new') ? : null;

        $sqPessoaSgdoce = $this->getService('PessoaSgdoce')->findPessoaBySqCorporativo(
            new \Core_Dto_Search(array('sqPessoaCorporativo' => $this->_getParam('id')))
        );

        if($sqPessoaSgdoce) {
            $this->view->sqPessoaSgdoce = $sqPessoaSgdoce->getSqPessoaSgdoce();
        }

        parent::editAction();
    }

    public function saveAction()
    {
        if($this->_request->isXmlHttpRequest()) {
            $result = array();

            try {
                $pessoa = $this->save();
                $this->getService()->finish();

                if($pessoa) {
                    $result['return']         = true;
                    $result['sqPessoa']       = $pessoa['sqPessoa'];
                    $result['sqPessoaSgdoce'] = $pessoa['sqPessoaSgdoce'];
                    $result['sqDocumento']    = $pessoa['sqDocumento'];
                    $result['campoPessoa']    = $pessoa['campoPessoa'];
                    $result['campoCnpj']      = $pessoa['campoCnpj'];
                    $result['nuCnpj']         = $pessoa['nuCnpj'];
                    $result['form']           = $pessoa['form'];
                    $result['noPessoa']       = $pessoa['noPessoa'];
                }
            } catch(\Exception $e) {
                $result['return']  = false;
                $result['message'] = $e->getMessage();
            }

            $this->_helper->json($result);
        }
    }

    public function save()
    {
        return self::_save();
    }

    public function _factoryParamsExtrasSave($params)
    {
        $arrDto = array();

        $arrDto['params'] = new Core_Dto_Search($params);

        return $arrDto;
    }

    public function searchCnpjAction()
    {
        if($this->_getParam('nuCnpj')) {
            $criteria = array('nuCnpj' => Zend_Filter::filterStatic($this->_getParam('nuCnpj'), 'Digits'));
        }

        $result = $this->getService('VwPessoaJuridica')->searchCnpj($criteria);
        $this->_helper->json($result);
    }

    public function searchMatrizFilialAction()
    {
        $result   = array('return' => false);
        $criteria = array(
            'nuCnpj' => $this->_getParam('nuCnpj')
        );

        if($this->getService('VwPessoaJuridica')->getMatrizFilial(new \Core_Dto_Search($criteria))) {
            $result['return'] = true;
        }

        $this->_helper->json($result);
    }

    public function searchRazaoSocialAction()
    {
        if($this->_getParam('noPessoa')) {
            $criteria = array('noPessoa' => mb_strtolower($this->_getParam('noPessoa'), 'utf8'));
        }

        $result = $this->getService('VwPessoaJuridica')->searchRazaoSocial($criteria);
        $this->_helper->json($result);
    }

    public function visualizarMatrizFilialAction()
    {
        $criteria = array(
            'sqAtributoTipoDocumento' => $this->getAtributoTipoDocumento()
        );

        if($this->_getParam('sqPessoa')) {
            $criteria['sqPessoa'] = $this->_getParam('sqPessoa');

            $this->view->sqPessoa  = $criteria['sqPessoa'];
            $this->view->endereco  = $this->getService('VwEndereco')->findEndereco($criteria['sqPessoa']);
            $this->view->entity    = $this->getService('VwPessoaJuridica')->findPessoaJuridica($criteria);
            $this->view->documento = $this->getService('VwDocumento')->findBy($criteria);
        }

        if($this->_getParam('nuCnpj')) {
            $criteria['nuCnpj'] = $this->_getParam('nuCnpj');
        }

        $matrizFilial = $this->getService('VwPessoaJuridica')->getMatrizFilial(new \Core_Dto_Search($criteria));

        $this->view->matrizFilial = $matrizFilial;
        $this->view->visualizar   = false;

        if($this->_getParam('visualizar')) {
            $this->view->visualizar = true;
            $this->_helper->viewRenderer->setRender('visualizarPessoaJuridica');
        }

        $this->_helper->layout()->disableLayout();
    }

    public function getAtributoTipoDocumento()
    {
        return \Sgdoce_Constants::ATRIBUTO_TIPO_DOCUMENTO;
    }

    public function gerarDocMatrizFilialAction()
    {
        $registry = \Zend_Registry::get('configs');
        $options  = array('path' => $registry['upload']['pessoaJuridica']);
        $criteria = array(
            'sqPessoa' => $this->_getParam('sqPessoa')
        );

        if(!$this->_getParam('sqPessoa')) {
            $criteria = array(
                'nuCnpj' => $this->_getParam('nuCnpj')
            );
        }

        $file = $this->getService('VwPessoaJuridica')->gerarDocMatrizFilial(new \Core_Dto_Search($criteria));

        $this->_helper->download($file, $options);
        $this->_helper->viewRenderer->setRender('visualizarMatrizFilial');
    }

    public function searchPessoaJuridicaAction() {
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();

        $dto    = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $result = $this->getService('VwPessoaJuridica')->searchPessoaJuridica($dto);

        return $this->_helper->json($result);
    }

    public function createAction()
    {
        $this->_helper->layout->setLayout('modal');
        parent::createAction();
        $this->view->campoPessoa = $this->_getParam('campoPessoa');
        $this->view->campoCnpj = $this->_getParam('campoCnpj');
        $this->view->form = $this->_getParam('form');
    }
}
