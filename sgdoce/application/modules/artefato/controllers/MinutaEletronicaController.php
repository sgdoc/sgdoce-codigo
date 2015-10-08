<?php
use Doctrine\ORM\Mapping\Entity;

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
 * Classe para Controller de Artefato
 *
 * @package    Minuta
 * @category   Controller
 * @name       Artefato
 * @version    1.0.0
 */
class Artefato_MinutaEletronicaController extends Artefato_ArtefatoController
{

    /**
     * @var string
     * @access protected
     * @name $_optionsDtoEntity
     */
    private $_idUser;
    private $_idUnidade;
    protected $_service = 'MinutaEletronica';
    protected $_folder = "rodape";
    protected $_optionsDtoEntity = array(
        'entity' => 'Sgdoce\Model\Entity\ArtefatoMinuta',
        'mapping' => array(
            'sqArtefato'  => 'Sgdoce\Model\Entity\Artefato',
            'sqMunicipio' => 'Sgdoce\Model\Entity\VwMunicipio',
            'sqModeloDocumento' => 'Sgdoce\Model\Entity\ModeloDocumento'
        )
    );
    protected $_entityTemp = "Sgdoce\Model\Entity\Artefato";
    protected $_optionsDtoGrauAcesso = array(
        'entity' => 'Sgdoce\Model\Entity\Artefato',
        'mapping' => array()
    );

    /**
     * Action inicial do Crud. Normalmente, apresenta o formulário que será utilizado para listagem
     */
    public function indexAction()
    {
        $profissional = $this->getService()->findUnidadeExercicio($this->getPersonId());
        $sqUnidadeExercicio = !empty($profissional) ? $profissional->getSqUnidadeExercicio() : null;

        $this->view->semUnidadeExercicio = FALSE;
        if (empty($sqUnidadeExercicio)){
            $this->view->semUnidadeExercicio = TRUE;
        }
        parent::indexAction();
    }

    /**
     * Metódo para recuperar o ID do usuário logado
     * @return int
     */
    public function getIdUnidade()
    {
        return Core_Integration_Sica_User::getUserUnit();
    }

    /**
     * Recupera o redicionamento correto
     * @param type $url, array $options
     * @param array $options
     */
    public function _redirect($url, array $options = array())
    {
        if ($this->_getParam('sqTipoVisualizacao') != '' && !$this->getMessaging()->getErrorMessages()) {
            parent::_redirect('/artefato/visualizar-caixa-minuta/index/view/'.
                      $this->_getParam('sqTipoVisualizacao'));
        } else {
            parent::_redirect($url, $options);
        }
    }

    /**
     * Metódo para recuperar modelo.
     * @param array $dtoSearch, array $params
     * @return array
     */
    public function hasModeloDocumentoCadastrado($dtoSearch,$params = FALSE)
    {
        return $this->getService()->hasModeloDocumentoCadastrado($dtoSearch,$params);
    }

    /**
     * Metódo para recuperar todas as combos.
     * @param array $params
     * @return TRUE
     */
    public function getCombo($params = NULL)
    {

        $this->view->dataArtefato     = date('d/m/Y');
        $params         = $this->_request->getParams();
        $tipoDocumento  = $this->getService('TipoDocumento')->find($params['sqTipoDocumento']);
        $assunto        = $this->getService('assunto')->find($params['sqAssunto']);

        $this->chekModelo($params);

        $this->view->tipoDocumento    = $tipoDocumento;
        $this->view->assunto          = $assunto;

        $params['sqUser']        = $this->getPersonId();
        $params['sqUsuario']     = $params['sqUser'];
        $params['sqPessoa']      = $params['sqUser'];
        $params['sqUnidade']     = $this->getIdUnidade();

        $dtoSearch = Core_Dto::factoryFromData($params, 'search');

        //dados do usuario logado
        $this->setDataView($dtoSearch);

        //fim dados
        if (!$this->view->rodape) {

            $params['sqPessoa']  = $this->getIdUnidade();
            $params['sqUsuario'] = $this->getIdUnidade();

            $dtoSearch           = Core_Dto::factoryFromData($params, 'search');
            //dados da unidade.

            $this->setDataView($dtoSearch);

            $rodape['sqPessoaSgdoce'] = '';
            $rodape['sqPessoaCorporativo'] = ($this->view->pessoa->getSqPessoa());
            $rodape['noPessoa']   = $this->view->pessoa->getNoPessoa();
            $rodape['coCep']      = $this->view->dadosOrigem->getSqCep();
            $rodape['txEndereco'] = $this->view->dadosOrigem->getTxEndereco();
            $rodape['nuDdd']      = $this->view->telefone->getNuDdd();
            $rodape['nuTelefone'] = $this->view->telefone->getNuTelefone();
            $rodape['txEmail']    = $this->view->email->getTxEmail();
            $this->view->rodape = $rodape;

        }

        return TRUE;
    }

    /**
     * Metódo para setar a view
     * @param array $dtoSearch
     * @return NULL
     */
    public function setDataView($dtoSearch)
    {

        $this->view->dadosOrigem      = $this->getService()->getDadosOrigem($dtoSearch);

        $this->view->pessoa           = $this->getService('pessoa')->findbyPessoaCorporativo($dtoSearch);
        $this->view->unidadeOrg       = $this->getService('VwUnidadeOrg')->getDadosUnidade($dtoSearch);
        $this->view->telefone         = $this->getService('VwTelefone')->getDadosTelefone($dtoSearch);
        $this->view->email            = $this->getService('VwEmail')->getDadosEmail($dtoSearch);

        $this->view->estado           = $this->getService('Estado')->comboEstado();
        $this->view->tratamento       = $this->getService('Tratamento')->comboTratamento();
        $this->view->grauAcesso       = $this->getService('GrauAcesso')->listItensGrauAcesso();
        $this->view->tipoPessoa       = $this->getService('TipoPessoa')->comboTipoPessoa();
        $this->view->vocativo         = $this->getService('Vocativo')->comboVocativo();
        $this->view->tipoMotivacao    = $this->getService('TipoMotivacao')->comboTipoMotivacao();
        $this->view->fecho            = $this->getService('Fecho')->comboFecho();
    }

    /**
     * Metódo para criar a view de criação e edit
     * @return NULL
     */
    public function viewEdit()
    {
        $this->view->dataPost = $this->_helper->persist->get('dataPost');
        $this->getCombo();
        $params = $this->_getAllParams();
        $params['sqArtefato'] = $params['id'];
        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $this->view->assinaturaUnica  = $this->getService('PessoaAssinanteArtefato')->getDadosAssinaturaUnica($dtoSearch);
        $this->view->dataArtefato = $this->view->data->getSqArtefato()->getDtArtefato()->get('dd/MM/yyyy');
        $this->view->unicoDestino = $this->isUnicoDestinos(
            $this->view->data->getSqArtefato()->getSqArtefatoMinuta()->getSqModeloDocumento()->getSqModelodocumento()
        );

        if (isset($this->view->assinaturaUnica[0])){
            $dtoSearchPessoa = Core_Dto::factoryFromData(
                array(
                    'sqPessoa' => $this->view->assinaturaUnica[0]['sqPessoa'],
                    'sqTipoPessoa' => \Core_Configuration::getSgdoceTipoPessoaPessoaFisica(),
                    'sqUnidadeOrg' => $this->getIdUnidade()
                ),
                'search'
            );
            $this->view->assinaturaSetor  = $this->getService('Pessoa')->getPessoaAssinatura($dtoSearchPessoa);
        }

        $this->view->assinantesUnicos = $this->getService('Pessoa')->getAssinantesUnicos();
        $this->view->dataGrauAcesso = $this->getService('GrauAcessoArtefato')->getGrauAcessoArtefato($dtoSearch);
        $this->_helper->viewRenderer->setRender('form');
    }

    /**
     * Método que verifica se o artefato é varias vias ou via única
     * @param integer $sqModeloDocumento
     * @return boolean
     */
    private function isUnicoDestinos($sqModeloDocumento)
    {
        $campos  = $this->getService('VisualizarCaixaMinuta')->getCamposModelo($sqModeloDocumento);
        foreach ($campos as $campo) {
            if ($campo['noCampo'] == 'Único Destino?') {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Metódo para criar a view de creação
     * @return TRUE
     */
    public function createAction()
    {
        parent::editAction();

        $this->viewEdit();
    }

    /**
     * Metódo para criar a view de edição
     * @return TRUE
     */
    public function editAction()
    {
        parent::editAction();

        $this->viewEdit();
    }

    /**
     * Metódo que valida antes do save.
     * @return NULL
     */
    public function saveAction()
    {
        $this->_persistDataError['dataPost'] = $this->getRequest()->getPost();

        $this->_save();
        $this->getService()->finish();
        $this->_addMessageSave();
        $this->_redirect('artefato/visualizar-caixa-minuta/index/view/3');
    }

    /**
     * Metódo para carregar a modal de destinatario externo
     * @return TRUE
     */
    public function destinatarioExternoModalAction()
    {
        $params         = $this->_request->getParams();
        $this->getCombo($params);
        $this->_helper->layout()->disableLayout();
        return TRUE;
    }

    /**
     * Metódo para carregar a modal de destinatario interno
     * @return TRUE
     */
    public function destinatarioInternoModalAction()
    {
        $params         = $this->_request->getParams();
        $this->getCombo($params);
        $this->_helper->layout()->disableLayout();
        return TRUE;
    }

    /**
     * Metódo para carregar a modal de interessado
     * @return TRUE
     */
    public function interessadoModalAction()
    {
        $params         = $this->_request->getParams();
        $this->getCombo($params);
        $this->_helper->layout()->disableLayout();
        return TRUE;
    }

    /**
     * Metódo para carregar a modal de assinatura
     * @return TRUE
     */
    public function assinaturaModalAction()
    {
        $params         = $this->_request->getParams();
        $this->getCombo($params);
        $this->_helper->layout()->disableLayout();
        return TRUE;
    }

    /**
     * Metódo que realiza a configuração dos extrasave
     * @param array $data
     * @return array
     */
    protected function _factoryParamsExtrasSave($data)
    {
        $data = $this->getService()->fixNewlines($data);
        $data['sqUnidade'] = $this->getIdUnidade();
        $dto = Core_Dto::factoryFromData($data, 'search');

        $dtoPessoa['unidade'] = $this->getService('VwUnidadeOrg')->getDadosUnidade($dto);

        $arrDto = array(
            'sqPessoa' => $this->getPersonId()
        );
        $dtoSearch = Core_Dto::factoryFromData($arrDto, 'search');
        $dtoPessoa['pessoa']  = $this->getService('pessoa')->findbyPessoaCorporativo($dtoSearch);

        $dtoPessoa['assinatura'] = '';
        if(isset($data['sqTipoAssinante'])) {
            $dtoPessoa['assinatura'] = $this->getService('PessoaAssinanteArtefato')->getDtoAssinatura($data);
        }

        $dtoPessoa['rodape'] = '';
        if(isset($data['coCepRodape'])) {
            $dtoPessoa['rodape'] = $this->getService('PessoaSgdoce')->getDtoRodape($data);
        }

        $dtoPessoa['externo']  = $this->getService()->getCampoModeloDocumento($dto);
        $dtoPessoa['artefato'] = $this->getDtoArtefato($data);

        return array($dto, $dtoPessoa);
    }

    /**
     * Metódo que recupera o Dto da Assinatura
     * @param array $data
     * @return Entity /Artefato
     */
    public function getDtoArtefato($data)
    {
        $dtoArtefato = Core_Dto::factoryFromData($data, 'entity',
            array(
                'entity'  => 'Sgdoce\Model\Entity\Artefato',
                'mapping' => array(
                    'sqTipoArtefatoAssunto' => 'Sgdoce\Model\Entity\TipoArtefatoAssunto',
                    'sqTipoDocumento'       => 'Sgdoce\Model\Entity\TipoDocumento',
                    'sqTipoPrioridade'      => 'Sgdoce\Model\Entity\TipoPrioridade',
                    'sqFecho'               => 'Sgdoce\Model\Entity\Fecho'
                )
            )
        );

        return $dtoArtefato;
    }

    /**
     * Metódo que verifica se o modelo está cadastrado.
     * @return json
     */
    public function checkModeloCadastradoAction()
    {
        $params = $this->_getAllParams();
        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $this->getHelper('json')->sendJson($this->hasModeloDocumentoCadastrado($dtoSearch,FALSE));
    }

    /**
     * Metódo que retorna os campos de acordo com o modelo.
     * @return json
     */
    public function getCampoModeloDocumentoAction()
    {
        $params = $this->_getAllParams();
        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $this->getHelper('json')->sendJson($this->getService()->getCampoModeloDocumento($dtoSearch));
    }

    /**
     * Metódo que retorna a lista os destinatarios internos.
     * @return NULL
     */
    public function listDestinatarioInternoAction()
    {
        $params = $this->_getAllParams();

        $this->_helper->layout->disableLayout();

        $params['sqPessoaFuncao'] = \Core_Configuration::getSgdocePessoaFuncaoDestinatario();
        $params['sqTipoPessoa']   = \Core_Configuration::getSgdoceTipoPessoaPessoaFisica();

        $configArray        = $this->getConfigListDestinatario();
        $this->view->grid   = new Core_Grid($configArray);
        $params             = $this->view->grid->mapper($params);
        $this->view->dto    = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getResultListDestinatario($this->view->dto);
    }

    /**
     * Metódo que retorna a lista os destinatarios externos.
     * @return NULL
     */
    public function listDestinatarioExternoAction()
    {
        $params = $this->_getAllParams();

        $this->_helper->layout->disableLayout();
        $data = array();
        $params['sqPessoaFuncao'] = \Core_Configuration::getSgdocePessoaFuncaoDestinatario();

        $configArray = $this->getConfigListDestinatario();
        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);
        $this->view->dto  = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getResultListDestinatario($this->view->dto);
    }

    /**
     * retorna dados da grid
     * @param \Core_Dto_Search $dtoSearch
     * @return array
     */
    public function getResultListDestinatario(\Core_Dto_Search $dtoSearch)
    {
        return $this->getService('pessoa')->listDestinatario($dtoSearch);
    }

    /**
     * retorna dados da grid
     * @param \Core_Dto_Search $dtoSearch
     * @return array
     */
    public function getResultListAssinatura(\Core_Dto_Search $dtoSearch)
    {
        return $this->getService('pessoa')->listAssinatura($dtoSearch);
    }

    /**
     * metodo que ordena grid
     * @return array
     */
    public function getConfigListAssinatura()
    {
        $array = array(
            'columns' => array(
                array('alias' => 'p.noPessoa'),
            )
        );

        return $array;
    }

    /**
     * metodo que ordena grid
     * @return array
     */
    public function getConfigListDestinatario()
    {
        $array = array(
            'columns' => array(
                array('alias' => 'p.noPessoa'),
            )
        );

        return $array;
    }

    /**
     * Metódo que retorna a lista das assinaturas na grid
     * @return NULL
     */
    public function listAssinaturaAction()
    {
        $params = $this->_getAllParams();

        $this->_helper->layout->disableLayout();
        $data = array();
        $configArray = $this->getConfigListAssinatura();
        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);
        $this->view->dto  = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getResultListAssinatura($this->view->dto);
    }

    /**
     * Metódo para adicionar os destinatarios na grid
     * @return NULL
     */
    public function addDestinatarioArtefatoAction()
    {
        $params     = $this->_getAllParams();
        $result     = 'true';
        $params['sqPessoaFuncao'] = \Core_Configuration::getSgdocePessoaFuncaoDestinatario();
        if(($params['sqTratamentoVocativo'] == '0') || ($params['sqTratamentoVocativo'] == '')){
            unset($params['sqTratamentoVocativo']);
        }

        $params['nuCpfCnpjPassaporte'] = str_replace('/','',str_replace('.','',
                                         str_replace('-', '', $params['nuCpfCnpjPassaporte'])));

        //busca a pessoa na tabela pessoa_sgdoce se não existir já cria.
        $params = $this->getService('Pessoa')->mountDtoPessoaSgdoce($params);

        $params['sqPessoa'] = isset($params['sqPessoaCorporativo']) ? $params['sqPessoaCorporativo'] : null;
        $dtoSearch = Core_Dto::factoryFromData($params, 'search');

        $params = $this->parametrizar($params,$dtoSearch);
        $this->abaSelecionada($params,$dtoSearch);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->_response->setBody($result);
    }

    /**
     * Metódo para salvar o artefato parcialmente
     * @return TRUE
     */
    public function saveArtefatoAction()
    {
        $sqTipoDocumento = $this->_getParam('sqTipoDocumento');
        $sqAssunto       = $this->_getParam('sqAssunto');

        $params['sqPessoaFuncao'] = \Core_Configuration::getSgdocePessoaFuncaoDestinatario();
        $dtoSearch = Core_Dto::factoryFromData(array('sqTipoDocumento' => $sqTipoDocumento ,
                                                     'sqAssunto' => $sqAssunto ), 'search');

        $res = $this->getService()->saveArtefato($dtoSearch);

        $this->_redirect('/artefato/minuta-eletronica/create/id/'.$res->getSqArtefato().'/sqTipoDocumento/'.
                         $sqTipoDocumento.'/sqAssunto/'.$sqAssunto);

        return TRUE;
    }
}