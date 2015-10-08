<?php

use Bisna\Service\ServiceLocator;

use Dossie\Service\Dossie;
use Sgdoce\Model\Repository\Artefato;
use Doctrine\ORM\Query\ParameterTypeInferer;
require_once __DIR__ . '/ArtefatoController.php';

/*
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
 * Classe para Controller de Dossie
 *
 * @package    Artefato
 * @category   ControllerTIPO_VINCULO_DOCUMENTO
 * @name       Dossie
 * @version    1.0.0
*/

class Artefato_DossieController extends Artefato_ArtefatoController
{

    /**
     * Variavel para receber o nome da service
     * @var    string
     * @access protected
     * @name   $_service
     */
    protected $_service = 'Dossie';

    /**
     * Variavel para receber a entidade referente a esta classe
     * @var    string
     * @access protected
     * @name   $_optionsDtoEntity
     */
    protected $_optionsDtoEntity = array(
            'entity'     => 'Sgdoce\Model\Entity\Artefato',
            'mapping'    => array(
                    'sqTipoDocumento'  => 'Sgdoce\Model\Entity\TipoDocumento'
            )
    );

    /**
     * Solicita pesquisa a repository para apresentar dados do TipoMarco na Grid
     * @param object $request
     * @param object $response
     * @param array $invokeArgs
     */
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response,
            array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
    }

    /**
     * Ação inicial de criação de dossies
     */
    public function indexAction()
    {
        parent::indexAction();
        $this->view->usuario = $this->getPersonId();
    }

    public function getPersonId()
    {
        return \Core_Integration_Sica_User::getPersonId();
    }
    /**
     * Ação da criação de Dossies
     */
    public function createAction()
    {
        parent::createAction();

        if(!$this->_hasParam('nuDigital')) {
            $this->_redirect('/artefato/dossie/');
        } else {
            $newNuDigital  = $this->getService('Artefato')->createNumeroDigital();
            $coreDtoSearch = Core_Dto::factoryFromData(array('nuDigital' => $newNuDigital), 'search');
            $res           = $this->getService('Documento')->saveArtefato($coreDtoSearch);

            $this->_redirectAction('edit/id/'.$res->getSqArtefato().'/view/1');
        }
    }

    /**
     * Save - Crud
     */
    protected function _save()
    {
        $params = $this->_getAllParams();
        $result = $this->getService('Artefato')->findBy(array('nuDigital' => $params['nuDigital']));
        $params['sqArtefato'] = $result[0]->getSqArtefato();
        $params['sqTipoDocumento'] = $params['sqTipoDocumento_hidden'];
        $params = $this->getService('MinutaEletronica')->fixNewlines($params);
        $dto = new Core_Dto_Search($params,array_keys($params));
        $this->getService()->alterarArtefato($dto);
    }

    /**
     * Ação da exclusao de vinculo
     */
    public function deleteAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $data = $this->_getParam('id');
        $dto = Core_Dto::factoryFromData(array('sqArtefatoVinculo' => $data), 'search');
        $this->getService()->deleteArtefatoVinculo($dto);
    }

    public function deleteDossieAction()
    {
        $entity = $this->getService('Artefato')->find($this->_getParam('id'));
        $sqAssunto = $entity->getSqTipoArtefatoAssunto()->getSqAssunto();

        if (empty($sqAssunto)) {
            $this->getService('Artefato')->delete($this->_getParam('id'));
            $this->getService('Artefato')->finish();
        }
        return true;
    }

    /**
     *cria modal de documentos
     */
    public function modalDocumentosAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();
    }

    /**
     * Responsavel por cadastrar vinculo de documentos
     * @return json
     */
    public function documentosAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $params['tipoVinculo'] = $params['sqTipoVinculoArtefato'];
        $dto = Core_Dto::factoryFromData($params, 'search');

        $artefato = $this->getService('Artefato')->findBy(array('nuDigital' => $dto->getNuDigital()));
        $criteria = array('sqArtefatoPai' => $dto->getSqArtefato()
                ,'sqArtefatoFilho' => $artefato[0]->getSqArtefato()
                ,'sqTipoVinculoArtefato' => $params['tipoVinculo']
                ,'inOriginal' => $params['inOriginal']
                ,'dtRemocaoVinculo' => NULL);

        $result = $this->getService('ArtefatoVinculo')->findBy($criteria);

        if(count($result) > 0 ){
            $this->_helper->json(array('sucess' => 'false'));
        }else{
            $json = $this->getService('Documento')->addDocumentoEletronico($dto);
            $this->_helper->json(array('sucess' => 'true'));
        }
    }

    /**
     * Action que realiza a pesquisa
     */
    public function listVinculacaoAction()
    {
        $this->getHelper('layout')->disableLayout();
        $params = $this->_getAllParams();
        $params['sqTipoVinculo'] = \Core_Configuration::getSgdoceTipoVinculoArtefatoVinculo();
        $configArray = array( 'a.nuArtefato', 'ta.sqTipoArtefato', 'a.nuDigital', 'ps.noPessoa');
        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getResultListVinculacao($this->view->dto);
    }

    /**
     * Action que realiza a pesquisa
     */
    public function listDocumentosAction()
    {
        $this->getHelper('layout')->disableLayout();
        $params = $this->_getAllParams();
        $params['sqTipoVinculo'] = \Core_Configuration::getSgdoceTipoVinculoArtefatoVinculo();
        $configArray = array( 'av.inOriginal', 'a.nuDigital');
        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getResultListDocumentos($this->view->dto);
    }

    /**
     *retorna dados da grid
    */
    public function getResultListDocumentos(\Core_Dto_Search $dtoSearch)
    {
        return $this->getService('Dossie')->listGridDocumentos($dtoSearch);
    }

    /**
     *retorna dados da grid
     */
    protected function _factoryParamsExtrasSave($data)
    {
        if(!$data['sqGrauAcesso']){
            $data['sqGrauAcesso'] = \Core_Configuration::getSgdoceGrauAcessoPublico();
        }

        $entityGrauArtefato = Core_Dto::factoryFromData($data, 'entity',
                array('entity' => 'Sgdoce\Model\Entity\GrauAcessoArtefato',
                        'mapping' => array(
                                'sqGrauAcesso' => 'Sgdoce\Model\Entity\GrauAcesso',
                                'sqArtefato' => 'Sgdoce\Model\Entity\Artefato')));

        $sqAssunto = new Core_Dto_Mapping($data , array_keys($data));
        return array($sqAssunto, $entityGrauArtefato);
    }

    /**
     * Edit - Crud
     */
    public function editAction()
    {
        parent::editAction();
        $params = $this->_getAllParams();

        /**
         * Artefato
        */
        $dtoSearch = Core_Dto::factoryFromData(array('sqArtefato' => $params['id']), 'search');
        $dtoArtefato = $this->getService('Artefato')->find($dtoSearch->getSqArtefato());
        /**
         * PessoaSgdoce
        */
        $dtoPessoaSgdoce = Core_Dto::factoryFromData(array('sqPessoaCorporativo' => $this->getPersonId()), 'search');
        $sqPessoaSgdoce = $this->getService('PessoaSgdoce')->findPessoaBySqCorporativo($dtoPessoaSgdoce);

        /**
         * PessoaUnidadeOrg
        */
        $pessoaUnidadeOrg = '';
        if($sqPessoaSgdoce){
            $pessoaUnidadeOrg = $this->getService('PessoaUnidadeOrg')->findUnidSgdoce($sqPessoaSgdoce);
            if(!$pessoaUnidadeOrg){
                $pessoaUnidadeOrg = $this->getServiceLocator()->getService('Documento')->hasPessoaUnidadeOrg($sqPessoaSgdoce);
            }
        }
        /**
         * PessoaAssinanteArtefato
        */
        $this->view->pessoaAssinante = $this->getService('PessoaAssinanteArtefato')
        ->getAssinanteArtefato($dtoSearch);

        $dadosOrigem = self::_dadosPessoaDocumento($dtoSearch, \Core_Configuration::getSgdocePessoaFuncaoOrigem());

        $dtoOrigem = Core_Dto::factoryFromData(array('sqProfissional' => $this->getPersonId()), 'search');
        $unidadeOrg = $this->getService()->unidadeOrigemPessoa($dtoOrigem);

        if(!count($dadosOrigem)){
            $this->view->sqUnidadeOrg = $unidadeOrg->getSqUnidadeExercicio()->getSqUnidadeOrg();
            $this->view->noUnidadeOrg = $unidadeOrg->getSqUnidadeExercicio()->getNoUnidadeOrg();
        }else{
            $this->view->sqUnidadeOrg = $dadosOrigem[0]->getSqPessoaSgdoce()->getSqPessoaCorporativo()->getSqPessoa();
            $this->view->noUnidadeOrg = $dadosOrigem[0]->getSqPessoaSgdoce()->getNoPessoa();
        }

        $this->view->redirect = $params['view'];
        $this->view->data = $dtoArtefato;
        $this->view->pessoaUnidadeOrg = $pessoaUnidadeOrg;
        $this->view->usuario = $this->getPersonId();
        $this->view->itensTipoDocumento = $this->getService('TipoDoc')->listItems();
        $this->view->itensGrauAcesso = $this->getService('GrauAcesso')->listItensGrauAcesso();
        $this->view->grauAcessoArtefato = $this->getService('GrauAcessoArtefato')->getGrauAcessoArtefato($dtoSearch);
        $this->view->itensAssunto = $this->getService('Assunto')->comboAssunto();
    }

    /**
     * Action da Form
     */
    public function formAction()
    {
        $params = $this->_getAllParams();
        $this->view->usuario = $this->getPersonId();
    }

    /**
     * Validacao se a digital já é existente
     */
    public function validarDigitalAction()
    {
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData(array('sqPessoaCorporativo' => $this->getPersonId()) , 'search');
        $entity = $this->getService()->validarPessoa($dto);
        $dto = Core_Dto::factoryFromData(array('nuDigital' => $params['nuDigital']), 'search');
        $sqArtefato = $this->getService()->findByNuDigital($dto);
        if ($sqArtefato) {
            $this->_helper->json(1);
        } else {
            $this->_redirectAction('create/nuDigital/0');
        }
    }

    /**
     * método que faz pesquisa no banco para preencher o autocomplete
     * @return json
     */
    public function findAssinaturaAction()
    {
        $dto = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $res = $service = $this->getService()->findAssinatura($dto);
        $this->_helper->json($res);
    }

    /**
     * método que faz pesquisa no banco para preencher o autocomplete
     * @return json
     */
    public function searchUnidadeAction()
    {
        $term = $this->_getParam('query');
        $res = $this->getService()->searchUnidadeInterna($term);
        $this->_helper->json($res);
    }

    /**
     * método que faz pesquisa no banco para preencher o autocomplete
     * @return json
     */
    public function findNumeroArtefatoAction()
    {
        $dtoSearch = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $result =  $this->getService()->findNumeroArtefato($dtoSearch);
        $this->_helper->json($result);
    }

    /**
     * método que faz pesquisa no banco para preencher o autocomplete
     * @return json
     */
    public function findNumeroDigitalAction()
    {
        $params = $this->_getAllParams();
        $term = $params['query'];
        $params['usuario'] = Core_Integration_Sica_User::getPersonId();
        $params['sqTipoArtefato'] = $params['extraParam'];
        $dto = Core_Dto::factoryFromData($params, 'search');
        $res = $this->getService()->findNumeroDigital($term, $dto, 10);
        $this->_helper->json($res);
    }

    /**
     * método para excluir dossie
     */
    public function autoCompleteVinculacaoAction()
    {
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params, 'search');
        $entity = $this->getService()->findAutoComplete($dto);

        $dtoPessoaSgdoce = Core_Dto::factoryFromData(array('sqPessoaCorporativo' => $this->getPersonId()), 'search');
        $sqPessoaSgdoce = $this->getService('PessoaSgdoce')->findPessoaBySqCorporativo($dtoPessoaSgdoce);

        $this->_helper->json(array('sqTipoArtefato'  => $entity[0]->getSqTipoDocumento()->getSqTipoDocumento() ,
                'noPessoa'        => $sqPessoaSgdoce,
                'nuDigital'       => $entity[0]->getNuDigital(),
                'noTitulo'        => $entity[0]->getSqArtefatoDossie()->getNoTitulo()));
    }

    public function verificaDuplicidadeAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params,'search');

        $result = $this->getService()->verificaDuplicidade($dto);

        $return['success'] = FALSE;
        if ($result) {
            $return['success'] = TRUE;
        }
        return $this->_helper->json($return);
    }
}