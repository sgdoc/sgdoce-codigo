<?php
require_once __DIR__ . '/PessoaSgdoceController.php';

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
 * Classe para Controller de Artefato
 *
 * @package    Artefato
 * @category   Controller
 * @name       Pessoa
 * @version    1.0.0
 */
class Artefato_PessoaController extends Artefato_PessoaSgdoceController
{

    /**
     * @var string
     * @access protected
     * @name $_optionsDtoEntity
     */
    protected $_service = 'Pessoa';
    protected $_optionsDtoEntity = array(
        'entity' => 'Sgdoce\Model\Entity\PessoaArtefato',
        'mapping' => array(
            'sqArtefato' => 'Sgdoce\Model\Entity\Artefato'
            , 'sqPessoaFuncao' => 'Sgdoce\Model\Entity\PessoaFuncao'
        )
    );

    /**
     * Metódo que recupera o id do usuário
     * @return int
     */
    public function getIdUser()
    {
        return \Core_Integration_Sica_User::getPersonId();
    }

    /**
     * Metódo que recupera o id da unidade
     * @return int
     */
    public function getIdUnidade()
    {
        return Core_Integration_Sica_User::getUserUnit();
    }

    /**
     * Metódo que recupera a pessoa
     * @return json
     */
    public function searchPessoaAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();

        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');

        $result = $this->getService('VwPessoa')->autocomplete($dtoSearch, 30); #30 é o limit

        if (0 === count($result) && (isset($params['save']))) {
            $msg = '';
            switch ($params['extraParam']) {
                case \Core_Configuration::getCorpTipoPessoaFisica():
                    $msg = 'MN132F';
                    break;
                case \Core_Configuration::getCorpTipoPessoaJuridica():
                    $msg = 'MN132J';
                    break;
            }
            if($msg){
                $result = array('__NO_CLICK__' => Core_Registry::getMessage()->_($msg)
                );
            }
        }
        $this->_helper->json($result);
    }

    /**
     * Metódo para preenchimento do autocomplete
     *
     * @return json
     */
    public function autocompleteAction()
    {
        $this->_helper->layout->disableLayout();

        $dtoSearch = \Core_Dto::factoryFromData($this->_getAllParams(), 'search');

        $result = $this->getService('VwPessoa')->autocomplete($dtoSearch, 30); #30 é o limit

        $this->_helper->json($result);
    }

    /**
     * Action que recupera o cargo de uma pessoa (interna)
     * @return json
     */
    public function recuperaCargoPessoaAction()
    {
        $this->_helper->layout->disableLayout();
        $dtoSearch = \Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $service = $this->getService()->searchDadosProfissinal($dtoSearch);
        $arrReturn = array('noCargo' => $service->getSqCargo()->getNoCargo());
        $this->_helper->json($arrReturn);
    }

    /**
     * Metódo que recupera a pessoa
     * @return json
     */
    public function searchPessoaDestinoAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();

        if($params['extraParam'] == \Core_Configuration::getSgdoceTipoPessoaMinisterioPublico()){
            unset($params['extraParam']);
            $service = $this->getService('VwUnidadeOrg')->searchUnidadesOrganizacionais($this->_getAllParams());
        } else {
            unset($params['extraParam']);
            $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
            $service = $this->getService()->searchPessoaInterna($dtoSearch);
        }

        $this->_helper->json($service);
    }

    /**
     * Metódo que recupera a unidadeOrg interna
     * @return json
     */
    public function searchUnidadeInternaAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();
//        $dtoSearch = \Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $service = $this->getService('VwUnidadeOrg')->searchUnidadesOrganizacionais($params);

        $this->_helper->json($service);
    }

    /**
     * Metódo que recupera a pessoa
     * @return json
     */
    public function pessoaUnidadeInternaAction()
    {
        $this->_helper->layout->disableLayout();
        $dtoSearch = \Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        if($dtoSearch->getExtraParam() == \Core_Configuration::getSgdoceTipoPessoaPessoaFisica()){
            $service = $this->getService()->searchInterna($dtoSearch);
        } else {
            $service = $this->getService('VwUnidadeOrg')->searchUnidadesOrganizacionais($this->_getAllParams());
        }
        $this->_helper->json($service);
    }

    /**
     * Metódo que recupera a pessoa
     * @return json
     */
    public function funcionarioUnidadeSetorAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();

        if(!empty($params['tipoConsulta']) &&
            $params['tipoConsulta'] == \Core_Configuration::getSgdoceTipoPessoaPessoaFisica()){
            $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
            $entitySetor = $this->getService('Pessoa')->recuperarInformacaoProfissional($dtoSearch);
            $params['sqUnidadeExercicio'] = $entitySetor;
        } else {
            $params['sqUnidadeExercicio'] = !empty($params['extraParam']) ? $params['extraParam'] : NULL;
        }

        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
        $service = $this->getService('Pessoa')->searchPessoaPorSetorOuUnidade($dtoSearch);

        $this->_helper->json($service);
    }

    /**
     * Metódo que valida dados.
     * @return json
     */
    public function validaDadosAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params, 'search');

//      verificar se já tem  a pessoa para este artefato no sgdoce.
        $result = $this->getService('PessoaInterassadaArtefato')->findPessoaInteressada($dto);

        if ($result) {
            $this->_response->setBody('true');
        } else {
            $this->getHelper('json')->sendJson($this->getService()->validaDados($dto));
        }
    }

    /**
     * Metódo que valida dados.
     * @return json
     */
    public function validaPessoaAssinaturaAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params, 'search');

//      verificar se já tem  a pessoa para este artefato no sgdoce.
        $result = $this->getService('PessoaAssinanteArtefato')->findAssinaturaArtefato($dto);
        if (count($result) > 0) {
            $this->_response->setBody('true');
        } else {
            $this->_response->setBody('false');
        }
    }

    /**
     * Metódo que valida dados.
     * @return json
     */
    public function validaPessoaDestinatarioAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params, 'search');

        //      verificar se já tem  a pessoa para este artefato no sgdoce.
        $result = $this->getService('PessoaArtefato')->findPessoaArtefato($dto);

        if (count($result) > 0) {
            $this->_response->setBody('true');
        } else {
            $this->_response->setBody('false');
        }
    }

    public function addInteressadoAction()
    {
        $params = $this->_getAllParams();
        $result = 'true';

        if (empty($params['sqTipoPessoa'])) {
            $dtoUnidSearch = Core_Dto::factoryFromData($params, 'search');

            if ($params['unidFuncionario'] == 'unidade') {
                $arrUnid = $this->getService('Processo')->searchVwUnidadeOrg($dtoUnidSearch);
            } else {
                $arrUnid = $this->getService('Processo')->searchFuncionarioIcmbio($dtoUnidSearch);
            }

            $params['sqTipoPessoa'] = (isset($arrUnid['sqTipoPessoa'])) ? $arrUnid['sqTipoPessoa']: null;
        }

        $dtoPessoaSgdoce = Core_Dto::factoryFromData($params, 'entity',
                array('entity' => 'Sgdoce\Model\Entity\PessoaSgdoce',
                'mapping' => array(
                    'sqTipoPessoa' => 'Sgdoce\Model\Entity\VwTipoPessoa'
                    , 'sqPessoaCorporativo' => array('sqPessoa' => 'Sgdoce\Model\Entity\VwPessoa'))));


        $sqPessoaSgdoce = $this->getService()->findPessoaSgdoce($dtoPessoaSgdoce);
        if (!$sqPessoaSgdoce) {
        	$data['sqPessoaCorporativo'] = $dtoPessoaSgdoce->getSqPessoaCorporativo()->getSqPessoa();
        	$dtoPessoaSearch = Core_Dto::factoryFromData($data, 'search');
        	$dtoPessoaSgdoce->setNuCpfCnpjPassaporte($this->getService('VwPessoa')->returnCpfCnpjPassaporte($dtoPessoaSearch));
            $return = $this->getService('MinutaEletronica')->saveDestinatario($dtoPessoaSgdoce);
            $sqPessoaSgdoce = $return->getSqPessoaSgdoce();
        }

        $params['sqPessoaSgdoce'] = $sqPessoaSgdoce;

        $dtoPessoaArtefato = Core_Dto::factoryFromData($params, 'entity',
                array('entity' => 'Sgdoce\Model\Entity\PessoaInteressadaArtefato',
                'mapping' => array(
                    'sqArtefato' => 'Sgdoce\Model\Entity\Artefato'
                  , 'sqPessoaSgdoce' => 'Sgdoce\Model\Entity\PessoaSgdoce')));


        $criteria = array('sqPessoaSgdoce' => $dtoPessoaArtefato->getSqPessoaSgdoce()->getSqPessoaSgdoce()
        				  ,'sqArtefato' => $dtoPessoaArtefato->getSqArtefato()->getSqArtefato());

        $returnP = $this->getService('PessoaInterassadaArtefato')->findBy($criteria);

        if($returnP){
        	$this->_helper->json(array('sucess' => 'false'));
        }
        $res = $this->getService('PessoaInterassadaArtefato')->savePessoaInteressada($dtoPessoaArtefato);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->_helper->json(array('sucess' => 'true'));
//         $this->_response->setBody($result);
    }

    public function searchUnidade($params)
    {
        $params['sqPessoa'] =  $params['sqPessoaCorporativo'];

        $dto = Core_Dto::factoryFromData($params, 'search');
        $resultArtefato = $this->getService('PessoaArtefato')->findPessoaArtefato($dto);

        if (count($resultArtefato) > 0){
            return 'false';
        }

        $params = $this->getService('Pessoa')->mountDtoPessoaSgdoce($params);
        $dto = Core_Dto::factoryFromData($params, 'search');
        $params['checkCorporativo'] = '1';
        $params['sqEndereco'] = '';
        $params = $this->getService('EnderecoSgdoce')->saveExtraDadosPessoa($params,$dto);
        $dtoPessoaArtefato = Core_Dto::factoryFromData($params, 'entity',
                array('entity' => 'Sgdoce\Model\Entity\PessoaArtefato',
                        'mapping' => array(
                                'sqArtefato' => 'Sgdoce\Model\Entity\Artefato'
                                ,'sqPessoaFuncao' => 'Sgdoce\Model\Entity\PessoaFuncao'
                                ,'sqEnderecoSgdoce' => 'Sgdoce\Model\Entity\EnderecoSgdoce'
                                ,'sqTelefoneSgdoce' => 'Sgdoce\Model\Entity\TelefoneSgdoce'
                                ,'sqEmailSgdoce' => 'Sgdoce\Model\Entity\EmailSgdoce'
                                ,'sqPessoaSgdoce' => 'Sgdoce\Model\Entity\PessoaSgdoce')));

        $resultArtefato = $this->getService('PessoaArtefato')->savePessoaArtefato($dtoPessoaArtefato);
    }

    public function addDestinatarioInternoAction()
    {
        $params = $this->_getAllParams();
        $result = 'true';
        $params['sqPessoaFuncao'] = \Core_Configuration::getSgdocePessoaFuncaoDestinatario();
        if ($params['sqTipoUnidadeOrg'] != '' &&
            $params['sqUnidadeOrg'] == '' && $params['sqPessoaCorporativo'] == '') {
            $params['sqTipoEndereco'] = \Core_Configuration::getSgdoceTipoEnderecoResidencial();
            $dtoSearch = Core_Dto::factoryFromData($params, 'search');
            $service = $this->getService('TipoUnidadeOrg')->searchUnidadeOrgPorTipo($dtoSearch);
            $params['sqTipoPessoa'] = \Core_Configuration::getSgdoceTipoPessoaPessoaFisica();

            foreach ($service as $key => $value) {
                $params['sqPessoaCorporativo'] = $value['sqUnidadeOrg'];
                $params['noPessoa'] = $value['noUnidadeOrg'];

                $result = $this->searchUnidade($params);

            }
            //se preencher o tipo e a unidade
        } else if ($params['sqTipoUnidadeOrg'] != '' &&
            $params['sqUnidadeOrg'] != '' && $params['sqPessoaCorporativo'] == '') {
            $params['sqTipoPessoa'] = \Core_Configuration::getSgdoceTipoPessoaPessoaFisica();
            $params['sqTipoEndereco'] = \Core_Configuration::getSgdoceTipoEnderecoResidencial();
            $params['sqPessoaCorporativo'] = $params['sqUnidadeOrg'];
            $params['noPessoa']            = $params['noUnidadeOrg'];

            $result = $this->searchUnidade($params);

        } else {
            $result = $this->searchUnidade($params);
        }

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->_response->setBody($result);
    }

    public function searchCargoCorporativoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $this->getHelper('json')->sendJson($this->getService('Pessoa')->searchCargoCorporativo($dtoSearch));
    }

    public function searchNomeCargoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $this->getHelper('json')->sendJson($this->getService('Pessoa')->searchNomeCargo($dtoSearch));
    }

    public function cargoPessoaInternaAction()
    {
        $params = $this->_getAllParams();
        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        // primeiro passo, vamos recuperar a unidade
        $this->getHelper('json')->sendJson($this->getService('PessoaSgdoce')->searchPessoaProfissinal($dtoSearch));
    }

    public function recuperaPessoaJuridicaPorCnpjAction()
    {
        $params = $this->_getAllParams();
        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $this->getHelper('json')->sendJson($this->getService()->searchPessoaJuridicaPorCnpj($dtoSearch));
    }

    public function recuperaQtdInteressadosArtefatoAction()
    {
    	$sqArtefato = $this->_getParam('sqArtefato');
    	$dtoSearch = Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
    	$this->getHelper('json')->sendJson($this->getService('PessoaInterassadaArtefato')->countInteressadosArtefato($dtoSearch));
    }
}