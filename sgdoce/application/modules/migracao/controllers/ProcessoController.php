<?php
use Bisna\Application\Resource\Doctrine;

require_once __DIR__ . '/../../artefato/controllers/ArtefatoControllerExtensao.php';

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
 * Classe para Controller de Processo Eletronico
 *
 * @package  Artefato
 * @category Controller
 * @name     ProcessoEletronico
 * @version     1.0.0
 */
class Migracao_ProcessoController extends ArtefatoControllerExtensao
{
    /**
     * @var string
     */
    protected $_service = 'ProcessoMigracao';

    /**
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => 'Sgdoce\Model\Entity\Artefato',
        'mapping' => array(
            'sqTipoPrioridade' => 'Sgdoce\Model\Entity\TipoPrioridade'
    ));

    /**
     * Monta as combos default para os tipos de processos
     * @return void
     */
    public function combos( $complete = false )
    {
        $this->view->arrOptGrauAcesso   = array();
        $this->view->arrOptMunicipio    = array();
        $this->view->arrOptEstado       = $this->getService('Estado')->comboEstado();
    	  $this->view->arrOptAmbito 		  = array('F' => 'Federal','E' => 'Estadual','M' => 'Municipal','J' => 'Judicial' );

        if( $complete ) {
            $this->view->arrOptTemaVinculado 	= array('Cavernas','Espécies','Empreendimentos','Unidades de Conservação' );
            $this->view->arrOptTipoPessoa 		= array( '' => 'Selecione...' ) + $this->getService('TipoPessoa')->getComboDefault(array());
            $this->view->arrOptTipoArtefato 	= array( '' => 'Selecione...' ) + $this->getService('TipoArtefato')->listItems(array());
            $this->view->arrOptAssunto        = array( '' => 'Selecione...' ) + $this->getService('Assunto')->comboAssunto(array());
            $this->view->arrOptPrioridade 		= array( '' => 'Selecione...' ) + $this->getService('Prioridade')->listItems();
            $this->view->arrOptTipoPrioridade = array( '' => 'Selecione...' ) + $this->getService('TipoPrioridade')->listItems();
            $this->view->arrOptGrauAcesso     = array( '' => 'Selecione...' ) + $this->getService('GrauAcesso')->listItensGrauAcesso();
        }
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_Abstract::indexAction()
     */
    public function indexAction()
    {
        $this->combos();

        $this->view->objEntArtefato 	= $this->getService('Artefato')->getEntityDto();
        $this->view->objEntArtefatoProcesso = $this->getService('ArtefatoProcesso')->getEntityDto();

    	$arrParams = $this->_getAllParams();
        $objDtoParams = \Core_Dto::factoryFromData($arrParams, 'search');
        $arrParams = array_merge($arrParams, array(
            'coAmbitoProcesso' => '',
            'sqEstado'      => 0,
            'sqEstado'      => 0,
            'sqMunicipio'   => 0,
        ));
        $this->view->objDtoParams = $objDtoParams;
    }

    /**
     * @return void
     */
    public function editAction()
    {
        $objRequest = $this->getRequest();
        $sqArtefato = $objRequest->getParam('id', false);

        $url = $this->getRequest()
                    ->getParam('back', false);

        $this->view->urlBack        = false;
        $this->view->withTramite    = $this->getRequest()->getParam('tramite', 1);

        if( $url ) {
            $url = str_replace(".", "/", $url);
            $this->view->urlBack = $url;
            $url = substr($url, 1);
            $params = explode("/", $url);
            $this->view->controllerBack = next($params);
            $this->view->caixa          = end($params);
        }

        $dtoSearch = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
        if( !$this->getService("Artefato")->isInconsistent($dtoSearch, false, true) ) {
            $this->getMessaging()->addErrorMessage('Processo já corrigido!', 'User');
            $this->_redirect( $this->view->urlBack );
        }

        $this->view->isAutuacao = $objRequest->getParam('autuado', false);

        if($sqArtefato){
            $artefato = $this->getService()
                             ->find($sqArtefato);
            if( count($artefato) <= 0 ) {
                $this->_redirect("/artefato/area-trabalho/index/tipoArtefato/"
                    . \Core_Configuration::getSgdoceTipoArtefatoProcesso());
            }
        }

        $this->combos(true);
        $this->view->tipoPessoa = $this->view->arrOptTipoPessoa;

        $arrParams = $this->_getAllParams();
        $arrParams['dtArtefato'] = \Zend_Date::now();
        $objDtoParams = \Core_Dto::factoryFromData($arrParams, 'search');

        if(!$sqArtefato) {
            $this->view->pageTitle = "Cadastrar Processo";
            if( $arrParams['coAmbitoProcesso'] == 'F' ) {
                $arrParams['nuArtefato'] = preg_replace('/[^a-zA-Z0-9]/', '', $arrParams['nuArtefato']);
            } else {
                $arrParams['nuArtefato'] = preg_replace('/[^a-zA-Z0-9\.\-\/]/', '', $arrParams['nuArtefato']);
            }

            $objEntArtefato         = $this->getService('Artefato')->getEntityDto($arrParams, array(
                'entity' => 'Sgdoce\Model\Entity\Artefato',
                'mapping' => array())
            );
            $objEntArtefatoProcesso = $this->getService('ArtefatoProcesso')->getEntityDto($arrParams, array(
                'entity' => 'Sgdoce\Model\Entity\ArtefatoProcesso',
                'mapping' => array(
                    'sqEstado' => 'Sgdoce\Model\Entity\VwEstado',
                    'sqMunicipio' => 'Sgdoce\Model\Entity\VwMunicipio'
                ))
            );
        } else {
            $this->view->pageTitle = "Corrigir Processo";

            $listArtefato = $this->getService('Artefato')->findBy(array('sqArtefato' => $sqArtefato));
            $objEntArtefato = current($listArtefato);
            $listArtefatoProcesso = $this->getService('ArtefatoProcesso')->findBy(array('sqArtefato' => $sqArtefato));

            $objEntArtefatoProcesso = current($listArtefatoProcesso);
        }

        // COMBO MUNICIPIO
        if( $objEntArtefatoProcesso->getSqMunicipio() ){
            $sqEstado = $objEntArtefatoProcesso->getSqEstado()->getSqEstado();
            $this->view->arrOptMunicipio = array( '' => 'Selecione...' ) + $this->getService('VwEndereco')->comboMunicipio($sqEstado, TRUE);
        }

        $dtoArtefato                            = Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
        $this->view->dadosOrigem                = $this->returnDadosOrigem($dtoArtefato);

        if( !$this->view->dadosOrigem ) {
            $this->view->dadosOrigem = NULL;
        } elseif(!$this->view->dadosOrigem[0]->getSqPessoaSgdoce()
                                           ->getSqPessoaCorporativo()
                                           ->getSqTipoPessoa() ) {
            $this->view->dadosOrigem[1] = NULL;
        }

    	$this->view->objEntArtefato             = $objEntArtefato;
        $this->view->data                       = $objEntArtefato;
    	$this->view->objEntArtefatoProcesso 	= $objEntArtefatoProcesso;
    	$this->view->sqTipoArtefato             = \Core_Configuration::getSgdoceTipoArtefatoProcesso();
    	$this->view->objDtoParams               = $objDtoParams;

    	if( $objEntArtefato->getDtArtefato() != null ) {
            $this->view->isValidProcess = true;
    	} else {
            $this->_redirectAction('index');
    	}

        $this->view->bloqueioDigito = false;
        if( isset($arrParams['tpNuArtefato'])
            || $sqArtefato ){
            $this->view->bloqueioDigito = true;
        }
    }

    /**
     * @throws RuntimeException
     */
    public function saveAction()
    {
        if (!$this->_request->isPost()) {
            throw new RuntimeException('A requisição deve ser POST');
        }

        $this->_save();
        $this->getService()->finish();
        $this->_addMessageSave();

        $url = $this->getRequest()
                    ->getParam('back', false);

        $this->view->urlBack        = false;
        $this->view->controllerBack = null;
        $this->view->caixa          = null;

        if( $url ) {
            $url = str_replace(".", "/", $url);
            $this->view->urlBack = $url;
            $url = substr($url, 1);
        }

        $this->_redirect($url);
    }

    /**
     * MÉTODO RETORNA NACIONALIDADE DA PESSOA FÍSICA.
     * (returnNacionalidade)
     *
     * @param Sgdoce\Model\Entity\PessoaArtefato $objEntPessoaArtefato
     * @return number
     */
    public function _getNacionalidade( Sgdoce\Model\Entity\PessoaArtefato $objEntPessoaArtefato )
    {
        $nuSqPessoa	= $objEntPessoaArtefato->getSqPessoaSgdoce()->getSqPessoaCorporativo()->getSqPessoa();
        $objEntPessoa   = current($this->getService('Pessoa')->findByPessoaFisica(array( 'sqPessoaFisica' => $nuSqPessoa )));

        if( $objEntPessoa )
        {
            $nuSqNacionalidade = $objEntPessoa->getSqNacionalidade()
            ? $objEntPessoa->getSqNacionalidade()->getSqPais()
            : NULL;

            if($nuSqNacionalidade == \Sgdoce_Constants::NACIONALIDADE_BRASILEIRA || $nuSqNacionalidade == NULL) {
                return \Sgdoce_Constants::NACIONALIDADE_BRASILEIRA;
            }

            return \Sgdoce_Constants::NACIONALIDADE_ESTRANGEIRA;
        }
    }

    /**
     * Metódo que realiza a configuração dos extrasave
     *
     * @return array
     */
    protected function _factoryParamsExtrasSave($data)
    {
        $data = $this->getService()->fixNewlines($data);

        $objSearchDto = Core_Dto::factoryFromData($data, 'search');
        // salva o artefato_processo
        $arrOptArtPro = array(
            'entity' => 'Sgdoce\Model\Entity\ArtefatoProcesso',
            'mapping' => array(
                'sqEstado'    => 'Sgdoce\Model\Entity\VwEstado',
                'sqMunicipio' => 'Sgdoce\Model\Entity\VwMunicipio',
                'sqArtefato'  => 'Sgdoce\Model\Entity\Artefato'
            )
        );

        $objArtProDto = Core_Dto::factoryFromData($data, 'entity', $arrOptArtPro);

        return array($objSearchDto, $objArtProDto);
    }

    /**
     * @return void
     */
    public function visualizarCapaAction()
    {
        $params = $this->_getAllParams();

        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
    }

    /**
     * MÉTODO N/I
     *
     * @return boolean
     */
    public function findTipoArtefatoAction()
    {
        $params = $this->_getAllParams();
        $dto    = Core_Dto::factoryFromData($params, 'search');

        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $this->_helper->json($this->getService()->findTipoArtefato($dto));

        return TRUE;
    }

    /**
     * Metódo que retorna a lista os destinatarios internos.
     *
     * @return array
     */
    public function listTemaTratadoAction()
    {
        $params = $this->_getAllParams();

        $this->_helper->layout->disableLayout();
        $data = array();

        $params['sqPessoaFuncao'] = \Core_Configuration::getSgdocePessoaFuncaoDestinatario();
        $params['sqTipoPessoa']   = \Core_Configuration::getSgdoceTipoPessoaPessoaFisica();

        $configArray = $this->getConfigListTemaTratado();
        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);
        $this->view->dto  = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getResultListTemaTratado($this->view->dto);
    }

    /**
     * @return boolean
     */
    public function addTemaTratadoAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->_getAllParams();
        $paramsVinculo = $this->findTemaVinculado($params);
        $dto = $this->mountDto($paramsVinculo);

        $entity = $this->getService($paramsVinculo['entity'])->findProcesso($dto);
        if($entity){
            $this->_helper->json(array('sucess' => 'true'));
        }else{
            $this->getService($paramsVinculo['entity'])->save($dto);
            $this->getService($paramsVinculo['entity'])->finish();
            $this->_helper->json(array('sucess' => 'false'));
        }
        return TRUE;
    }

	/**
	 * @return boolean
	 */
    public function addPecaProcessoAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->_getAllParams();
        $params['dtVinculo'] = new Zend_Date();

        $optionsDto = array(
            'entity' => 'Sgdoce\Model\Entity\ArtefatoVinculo',
            'mapping' => array(
                'sqArtefatoPai' => array('sqArtefato'=>'Sgdoce\Model\Entity\Artefato'),
                'sqArtefatoFilho' => array('sqArtefato'=>'Sgdoce\Model\Entity\Artefato'),
                'sqTipoVinculoArtefato' => 'Sgdoce\Model\Entity\TipoVinculoArtefato',
            )
        );

        $dto = Core_Dto::factoryFromData($params, 'entity', $optionsDto);

        $result = $this->getService('ArtefatoVinculo')->verificaVinculoArfatoPai($dto);

        if(count($result) > 0 ){
            $this->_helper->json(array('sucess' => 'true'));
        }else{
            $this->getService('ArtefatoVinculo')->save($dto);
            $this->getService('ArtefatoVinculo')->finish();
            $this->_helper->json(array('sucess' => 'false'));
        }
        return TRUE;
    }

    /**
     * Metódo que verifica se o modelo está cadastrado.
     *
     * @return json
     */
    public function checkProcessoCadastradoAction()
    {
        $params = $this->_getAllParams();
        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $this->getHelper('json')->sendJson($this->getService()->checkProcessoCadastrado($dtoSearch));
    }

    /**
     * Retorna json com os Municipios.
     *
     * @return json $arrMunicipio
     */
    public function comboMunicipioAction()
    {
        $estado = $this->_getParam('sqEstado');
        $result =  $this->getService('VwEndereco')->comboMunicipio($estado,TRUE);
        $this->_helper->layout->disableLayout();
        $this->view->result = $result;
        return TRUE;

    }

    /**
     * @return boolean
     */
    public function termoRenumeracaoAction()
    {
        $this->_helper->layout->disableLayout();
        $data['sqArtefato'] = $this->_getParam("sqArtefato");
        $dtoArtefato = Core_Dto::factoryFromData($data, 'search');

        $entityArtefatoProcesso       = $this->getService('ArtefatoProcesso')->find($data['sqArtefato']);
        $this->view->sqEstado         = $entityArtefatoProcesso->getSqEstado()->getSqEstado();
        $this->view->sqMunicipio      = $entityArtefatoProcesso->getSqMunicipio()->getSqMunicipio();
        $this->view->coAmbitoProcesso = $entityArtefatoProcesso->getCoAmbitoProcesso();
        $this->view->nuPaginaProcesso = $entityArtefatoProcesso->getNuPaginaProcesso();
        $this->view->nuVolume         = $entityArtefatoProcesso->getNuVolume();
        $this->view->cabecalho        = $this->getService('Cabecalho')->find(\Core_Configuration::getSgdoceSqCabecalho_2());
        $this->view->artefato         = $this->getService()->findVisualizarArtefato($dtoArtefato);

        return TRUE;
    }

    /**
     * @param unknown $params
     * @return Ambigous <string, unknown>
     */
    public function findTemaVinculado($params)
    {
        $paramsVinculo['sqArtefato'] = $params['sqArtefato'];
        switch ($params['sqTemaVinculado']) {
            case 0:
                $paramsVinculo['sqCaverna']  = $params['sqNomeEspecifico'];
                $paramsVinculo['view']       = 'VwIntegracaoCanie';
                $paramsVinculo['chave']      = 'sqCaverna';
                $paramsVinculo['entity']     = 'ProcessoCaverna';
                break;
            case 1:
                $paramsVinculo['sqTaxon']      = $params['sqNomeEspecifico'];
                $paramsVinculo['view']       = 'VwIntegracaoTaxon';
                $paramsVinculo['chave']      = 'sqTaxon';
                $paramsVinculo['entity']     = 'ProcessoTaxon';
                break;
            case 2:
                $paramsVinculo['sqEmpreendimento']      = $params['sqNomeEspecifico'];
                $paramsVinculo['view']       = 'VwIntegracaoSgca';
                $paramsVinculo['chave']      = 'sqEmpreendimento';
                $paramsVinculo['entity']     = 'ProcessoEmpreendimento';
                break;
            case 3:
                $paramsVinculo['sqUnidadeOrg']      = $params['sqNomeEspecifico'];
                $paramsVinculo['view']       = 'VwIntegracaoUnidade';
                $paramsVinculo['chave']      = 'sqUnidadeOrg';
                $paramsVinculo['entity']     = 'ProcessoUnidadeOrg';
                break;
        }
        return $paramsVinculo;
    }

    /**
     * @return void
     */
    public function deleteTemaAction()
    {
        $params = $this->_getAllParams();
        $paramsVinculo = $this->findTemaVinculado($params);

        $dto = $this->mountDto($paramsVinculo);

        $entity = $this->getService($paramsVinculo['entity'])->findProcesso($dto);

        $key = 'getSq' . ucfirst($paramsVinculo['entity']);

        $result = $this->getService($paramsVinculo['entity'])->delete($entity->{$key}());
        $this->getService($paramsVinculo['entity'])->finish();

        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

    }

     /**
     * Action que realiza a pesquisa de materiais para listagem
     *
     * @return json
     */
    public function listPecaProcessoAction()
    {

        // desabilitando layout
        $this->getHelper('layout')->disableLayout();
        // valores da grid
        $configGrid = array('a.nuArtefato', 'ta.noTipoArtefato','a.nuDigital','tva.noTipoVinculoArtefato');
        // setando parametros
        $params = $this->_getAllParams();
        // retornando valor pra grid
        $this->view->grid = new Core_Grid($configGrid);
        $params = $this->view->grid->mapper($params);
        // tratando parametros
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        // retornando valores pra view
        $this->view->result = $this->getService('ArtefatoVinculo')->listGridVinculacaoPeca($this->view->dto);

    }

    /**
     * @return json
     */
    public function findArtefatoPecaProcessoAction()
    {
        $dto = Core_Dto::factoryFromData($this->_getAllParams(),'search');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $result = $this->getService()->findArtefatoPecaProcesso($dto);

        return $this->getHelper('json')->sendJson($result);
    }

    /**
     * @return boolean
     */
    public function modalTemaTratadoAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->view->temaVinculado = array('Cavernas', 'Espécies', 'Empreendimentos', 'Unidades de Conservação');
        $this->view->sqArtefato = $this->_getParam('sqArtefato');
        $this->_helper->layout->disableLayout();
    }

    /**
     * @return boolean
     */
    public function modalPecaProcessoAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->view->sqArtefato = $this->_getParam('sqArtefato');
        $this->view->sqPessoaLogada = Core_Integration_Sica_User::getPersonId();
        $this->_helper->layout->disableLayout();
    }
}
