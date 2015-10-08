<?php
require_once __DIR__ . "/ArtefatoController.php";
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * SISICMBio
 *
 * Classe Controller de Visualizar Caixa de Minuta
 *
 * @package      Minuta
 * @subpackage  Controller
 * @name         VisualizarCaixaMinuta
 * @version      1.0.0
 * @since        2012-01-10
 */
class Artefato_VisualizarCaixaMinutaController extends Artefato_ArtefatoController
{
    /**
    * Variavel para receber o nome da service
    * @var    string
    * @access protected
    * @name   $_service
    */
    protected $_service = 'VisualizarCaixaMinuta';

    /**
    * Obtém dados de perfil do usuario
    * @return stdClass
    */
    public function getUser()
    {
        return Core_Integration_Sica_User::get();
    }


    /**
     * Action inicial do Crud. Normalmente, apresenta o formulário que será utilizado para listagem
     */
    public function indexAction()
    {
        $profissional = $this->getService('MinutaEletronica')->findUnidadeExercicio($this->getPersonId());
        $sqUnidadeExercicio = !empty($profissional) ? $profissional->getSqUnidadeExercicio() : null;
        $this->view->view     = $this->_getParam('view');
        $this->view->sqPessoa = \Core_Integration_Sica_User::getPersonId();
        $this->view->semUnidadeExercicio = FALSE;
        if (empty($sqUnidadeExercicio)){
            $this->view->semUnidadeExercicio = TRUE;
        }
        parent::indexAction();
    }

    /**
     * Método que obtém os dados para grid
     * @param \Core_Dto_Search $dtoSearch
     */
    public function getResultList(\Core_Dto_Search $dtoSearch)
    {
        $view = $this->_getParam('view');
        $sqPessoa = $this->_getParam('sqPessoa');
        $this->view->feriados = $this->getService()->getFeriados();
        $dtoSearch->setView = $view;
        $dtoSearch->setPessoa = $sqPessoa;
        $res = $this->getService()->getGrid($dtoSearch);
        return $res;
    }

    /**
     *
     * Método que configura os dados da grid
     */
    public function getConfigList()
    {
        return array('columns' => array(0 => array('alias' => 'vcm.dataCriacao'),
                                        1 => array('alias' => 'vcm.tipo'),    2 => array('alias' => 'vcm.origem'),
                                        3 => array('alias' => 'vcm.assunto'), 4 => array('alias' => 'vcm.autor'),
                                        5 => array('alias' => 'vcm.prazo'),   6 => array('alias' => 'vcm.status')));
    }

    /**
     * Método que encaminha minutas para acompanhamento
     * @return NULL
     */
    public function acompanharMinutaAction()
    {
        $sqArtefato = $this->_getParam('sqArtefato');
        $res = NULL;
        foreach($sqArtefato as $dados)
        {
            $dados = array('sqArtefato' => $dados);
            $dtoEntity = $this->dtoOptionHistoricoArtefato($dados);
            $data = $this->getService()->findCaixaMinuta($dtoEntity);
            $params = NULL;
            switch($data){
                case TRUE:
                    $params['sqStatusArtefato']     = $data['sqStatusArtefato'];
                    $params['sqUnidadeOrg']         = $this->getUser()->sqUnidadeOrg;
                    $params['sqArtefato']           = $data['sqArtefato'];
                    $params['sqPessoa']             = $this->getUser()->sqPessoa;
                    $params['sqOcorrencia']         = \Core_Configuration::getSqOcorrenciaAcompanharMinuta();
                break;
            }
            $res = $this->alteraHistoricoArtefato($params);
        }
        switch($res){
            case TRUE:
                $this->getMessaging()->addSuccessMessage('MN013');
                return $this->_redirectActionDefault('index/view/' . $this->_getParam('view') );
            break;
        }
        return NULL;
    }

    /**
     * Método que desacompanha minutas
     * @return NULL
     */
    public function desacompanharMinutaAction()
    {
        $paramArtefato = $this->_getParam('sqArtefato');
        $res = NULL;
        foreach($paramArtefato as $dados) {
            $dados = array('sqArtefato' => $dados);
            $dtoEntity = $this->dtoOptionHistoricoArtefato($dados);
            $data = $this->getService()->findCaixaMinuta($dtoEntity);
            $dtoEntityN = $this->dtoOptionHistoricoArtefato($data);
            $result = $this->getPenultimateHistArt($dtoEntityN);
            $params = NULL;
            switch($data){
                case TRUE:
                    $params['sqStatusArtefato']     = $result['sqStatusArtefato'];
                    $params['sqUnidadeOrg']         = $this->getUser()->sqUnidadeOrg;
                    $params['sqArtefato']           = $result['sqArtefato'];
                    $params['sqPessoa']             = $this->getUser()->sqPessoa;
                    $params['sqOcorrencia']         = \Core_Configuration::getSqOcorrenciaDesacompanharMinuta();
                break;
            }
            $res = $this->alteraHistoricoArtefato($params);
        }
        switch($res){
            case TRUE:
                $this->getMessaging()->addSuccessMessage('MN013');
                return $this->_redirectActionDefault('index/view/' . $this->_getParam('view') );
            break;
        }
        return NULL;
    }

    /**
     *
     * Método que encaminha minutas para análise
     * @return NULL
     */
    public function encaminharMinutaAnaliseAction()
    {
        $params = $this->_getAllParams();
        if($this->getRequest()->isPost()){
            $sqArtefato = $this->_getParam('sqArtefato');
            $res = NULL;
            foreach($sqArtefato as $dados){
                $dados = array('sqArtefato' => $dados);
                $dtoEntity = $this->dtoOptionHistoricoArtefato($dados);
                $data = $this->getService()->findCaixaMinuta($dtoEntity);
                $params = NULL;
                switch($data){
                    case TRUE:
                        $params['sqStatusArtefato']     = \Core_Configuration::getSgdoceStatusRecebida();
                        $params['sqUnidadeOrg']         = $this->getUser()->sqUnidadeOrg;
                        $params['sqArtefato']           = $data['sqArtefato'];
                        $params['sqPessoa']             = $this->_getParam('sqPessoa');
                        $params['sqOcorrencia']         = $this->_getParam('sqOcorrencia');
                        break;
                }
                $res = $this->alteraHistoricoArtefato($params);
            }
            switch($res){
                case TRUE:
                    $this->getMessaging()->addSuccessMessage('MN013');
                    return $this->_redirectActionDefault('index/view/' . $this->_getParam('view') );
                break;
            }
            return NULL;
        }

        $this->_helper->layout->disableLayout();
    }

    /**
     * Método que encaminha minutas para assinatura
     * @return NULL
     */
    public function encaminharMinutaAssinaturaAction()
    {
        $params = $this->_getAllParams();
        if($this->getRequest()->isPost()){
            $paramArtefato = $this->_getParam('sqArtefato');
            $res = NULL;
            foreach ($paramArtefato as $dados) {
                $dados = array('sqArtefato' => $dados);
                $dtoEntity = $this->dtoOptionHistoricoArtefato($dados);
                $data = $this->getService()->findCaixaMinuta($dtoEntity);
                $params = NULL;
                switch($data){
                    case TRUE:
                        $params['sqStatusArtefato']     = \Core_Configuration::getSgdoceStatusRecebida();
                        $params['sqUnidadeOrg']         = $this->_getParam('sqUnidadeOrgCorp');
                        $params['sqArtefato']           = $data['sqArtefato'];
                        $params['sqPessoa']             = $this->_getParam('sqPessoa');
                        $params['sqOcorrencia']         = $this->_getParam('sqOcorrencia');
                        break;
                }

                $assinantes = $this->getService('Artefato')->findOneBy($dados)->getSqPessoaAssinanteArtefato();
                if ($assinantes->count() == 0) {
                    $res = $this->alteraHistoricoArtefato($params);
                } else {
                    $podeAssinar = $this->getService('Artefato')->verificaPermissaoAssinatura($params);
                    if ($podeAssinar) {
                        $res = $this->alteraHistoricoArtefato($params);
                    } else {
                        $res = FALSE;
                    }
                }
            }
            switch($res){
                case TRUE:
                    $this->getMessaging()->addSuccessMessage('MN013');
                    return $this->_redirectActionDefault('index/view/' . $this->_getParam('view') );
                case FALSE:
                    return $this->_redirectActionDefault('index/view/' . $this->_getParam('view') );
                break;
            }
            return NULL;
        }
        $this->view->sqArtefato = !empty($params['sqArtefato']) ? $params['sqArtefato'] : '';
        $this->_helper->layout->disableLayout();
    }

    /**
     * Método que devolve minutas
     * @return NULL
     */
    public function devolverMinutaAction()
    {
        $data = $this->_getAllParams();
        if($this->getRequest()->isPost()){
            $params['sqStatusArtefato']     = \Core_Configuration::getSgdoceStatusDevolvida();
            $params['sqUnidadeOrg']         = $data['sqUnidadeOrg'];
            $params['sqArtefato']           = $data['sqArtefato'];
            $params['sqPessoa']             = $data['sqPessoa'];
            $params['sqOcorrencia']         = \Core_Configuration::getSqOcorrenciaDevolver();
            $params['txJustificativa']      = $data['txJustificativa'];
            $res = $this->alteraHistoricoArtefato($params);
            switch($res){
                case TRUE:
                    $this->getMessaging()->addSuccessMessage('MN108');
                    return $this->_redirect('/artefato/visualizar-caixa-minuta');
                break;
            }
            return NULL;
        }

        $dtoOption = array('entity'  => 'Sgdoce\Model\Entity\HistoricoArtefato',
                                    'mapping' => array('sqArtefato' => 'Sgdoce\Model\Entity\Artefato',
                                                        'sqPessoa' => 'Sgdoce\Model\Entity\VwPessoa'));
        $dtoEntity = Core_Dto::factoryFromData($data, 'entity', $dtoOption);
        $getDataHistoricoEnvioAnterior = $this->getDataHistoricoEnvioAnterior($dtoEntity);
        $this->view->dataHistoricoEnvioAnterior = $getDataHistoricoEnvioAnterior;
        $this->_helper->layout->disableLayout();
    }

    /**
     * Método que exclui minutas
     * @return NULL
     */
    public function excluirMinutaAction()
    {
        $params['sqStatusArtefato'] = \Core_Configuration::getSgdoceStatusExcluida();
        $params['sqUnidadeOrg']     = $this->getUser()->sqUnidadeOrg;
        $params['sqArtefato']       = $this->_getParam('sqArtefato');
        $params['sqPessoa']         = $this->getUser()->sqPessoa;
        $params['sqOcorrencia']     = \Core_Configuration::getSqOcorrenciaExcluirMinuta();
        $res = $this->alteraHistoricoArtefato($params);
        switch($res){
            case TRUE:
                $this->getMessaging()->addSuccessMessage('MN045');
                return $this->_redirectActionDefault('index/view/' . $this->_getParam('view') );
            break;
        }
        return NULL;
    }

    /**
     * Método que assina a minuta
     */
    public function assinarMinutaAction()
    {
        $artefato = $this->getService('Artefato')->find($this->getRequest()->getParam('sqArtefato'));
        //O artefato deve continuar com o mesmo status
        $ultimNumeroArtefato = $this->getService('HistoricoArtefato')->findBy(
                array('sqArtefato' => $artefato->getSqArtefato()),
                array('sqHistoricoArtefato' => 'DESC')
        );

        $params['sqStatusArtefato'] = $ultimNumeroArtefato[0]->getSqStatusArtefato()->getSqStatusArtefato();
        $params['sqUnidadeOrg']     = \Core_Integration_Sica_User::getUserUnit();
        $params['sqArtefato']       = $this->_getParam('sqArtefato');
        $params['sqPessoa']         = \Core_Integration_Sica_User::getPersonId();
        $params['sqOcorrencia']     = \Core_Configuration::getSqOcorrenciaAssinarMinuta();
        $params['nuDigital']        = '';
        $params['sqTipoArtefato']   = \Core_Configuration::getSgdoceTipoArtefatoDocumento();
        $params['sqTipoDocumento']  = $artefato->getSqTipoDocumento()->getSqTipoDocumento();

        //registra assinatura
        $this->getService('Artefato')->registrarAssinatura($artefato, $params);
        $assinado = $this->getService('Artefato')->verificaArtefatoAssinado($artefato);

        if ($assinado) {
            $this->gerarDocumento($params);
        }
        $result = $this->alteraHistoricoArtefato($params);

        switch($result) {
            case TRUE:
                if ($assinado) {
                    $params['sqOcorrencia'] = \Core_Configuration::getSgdoceSqOcorrenciaCadastrar();
                    $this->alteraHistoricoArtefato($params);

                    //Gera as vias do documento
                    $sqModeloDocumento = $artefato->getSqArtefatoMinuta()->getSqModeloDocumento()->getSqModeloDocumento();
                    if ($this->isVariasVias($sqModeloDocumento)) {
                        $this->getService('Artefato')->gerarVias($artefato, $params);
                    }

                    $this->getMessaging()->addSuccessMessage('MN085');
                    return $this->_redirect("/artefato/area-trabalho/index/view/{$this->_getParam('view')}");
                } else {
                    return $this->_redirect("/artefato/visualizar-caixa-minuta/index/view/{$this->_getParam('view')}");
                }
        }

        return NULL;
    }

    /**
     * Método que gera o documento a partir da minuta
     * @param array $params
     * @param array $params
     * @return object
     */
    private function gerarDocumento($params)
    {
        $params['sqStatusArtefato'] = \Core_Configuration::getSgdoceStatusAssinada();
        $params['nuDigital']        = $this->getService('Artefato')->createNumeroDigital();

        // recuperar o ultimo sequencia por unidade e tipo de documento
        $ultimNumeroArtefato = $this->getService('Artefato')->recuperaProximoNumeroArtefato($params);
        $numeroSequencial = $ultimNumeroArtefato->getNuSequencial();
        $sequencial = str_pad($numeroSequencial + 1, 4, '0', STR_PAD_LEFT) . '/' . date('Y');
        $params['nuArtefato'] = $sequencial;

        $searchDto = Core_Dto::factoryFromData($params, 'search');
        $this->getService('Artefato')->transformarMinutaDocumentoEletronico($searchDto);

        //atualiza o numero sequencial para o numero utilizado
        $this->getService('Artefato')->atualizarSequencial($ultimNumeroArtefato);
        $this->alteraHistoricoArtefato($params);

        return TRUE;
    }


    /**
     * Método que verifica se o artefato é varias vias ou via única
     * @param integer $sqModeloDocumento
     * @return boolean
     */
    private function isVariasVias($sqModeloDocumento)
    {
        $campos  = $this->getService()->getCamposModelo($sqModeloDocumento);
        foreach ($campos as $campo) {
            if ($campo['noCampo'] == 'Várias vias?') {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
    * Método que transforma array em objeto relacionado ao historico do artefato
    * @param array $params
    * @return object
    */
    public function dtoOptionHistoricoArtefato($params)
    {
        $dtoOption = array('entity'  => 'Sgdoce\Model\Entity\HistoricoArtefato',
        'mapping' => array('sqStatusArtefato' => 'Sgdoce\Model\Entity\StatusArtefato',
                                                 'sqPessoa' => 'Sgdoce\Model\Entity\VwPessoa',
                                                 'sqOcorrencia' => 'Sgdoce\Model\Entity\Ocorrencia',
                                                 'sqArtefato' => 'Sgdoce\Model\Entity\Artefato'));

        $dtoEntity = Core_Dto::factoryFromData($params, 'entity', $dtoOption);
        return $dtoEntity;
    }

    /**
    * Método que obtém dados do envio anterior no histórico do artefato
    * @param  object $dtoEntityN
    * @return array
    */
    public function getDataHistoricoEnvioAnterior($dtoEntity)
    {
        return $this->getService()->getDataHistoricoEnvioAnterior($dtoEntity);
    }

    /**
    * Obtém dados do penúltimo histórico do artefato
    * @param  object $dtoEntityN
    * @return array
    */
    public function getPenultimateHistArt($dtoEntityN)
    {
        return $this->getService()->getPenultimateHistArt($dtoEntityN);
    }

    /**
    * Método que salva o novo histórico do artefato
    *
    * @return boolean
    */
    public function alteraHistoricoArtefato($params = NULL)
    {
        if($params) {
            $dtoOption = array(
                'entity'  => 'Sgdoce\Model\Entity\HistoricoArtefato',
                'mapping' => array(
                    'sqStatusArtefato' => 'Sgdoce\Model\Entity\StatusArtefato',
                    'sqUnidadeOrg'     => 'Sgdoce\Model\Entity\VwUnidadeOrg',
                    'sqArtefato'       => 'Sgdoce\Model\Entity\Artefato',
                    'sqPessoa'         => 'Sgdoce\Model\Entity\VwPessoa',
                    'sqOcorrencia'     => 'Sgdoce\Model\Entity\Ocorrencia'
                )
            );

            $params['dtOcorrencia'] = new \Zend_Date();

            $dtoEntity = Core_Dto::factoryFromData($params, 'entity', $dtoOption);
            $result    = $this->getService()->saveHistorico($dtoEntity);

            return $result;
        }

        return false;
    }

    /**
    * Método que pesquisa as unidades das pessoas vinculadas as funcionalidades de minuta
    */
    public function searchUnidadeOrgsAction()
    {
        $params = $this->_getAllParams();
        $res = $service = $this->getService()->searchUnidadeOrgs($params);
        $this->getHelper('json')->sendJson($res);
    }

    /**
    * Método que pesquisa as pessoas vinculadas as funcionalidades de minuta
    */
    public function searchPessoasAction()
    {
        $params = $this->_getAllParams();
        $params['sqPessoaLogada'] = $this->getUser()->sqPessoa;
        $dto = Core_Dto::factoryFromData($params, 'search');
        $res = $service = $this->getService()->searchPessoas($dto);
        $this->getHelper('json')->sendJson($res);
    }
}