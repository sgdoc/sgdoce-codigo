<?php
use Bisna\Application\Resource\Doctrine;

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
 * Classe para Controller de Autuar Documento.
 *
 * @package    Artefato
 * @category   Controller
 * @name       DemandaInformacao
 * @version    1.0.0
 */
class Artefato_SolicitacaoController extends \Core_Controller_Action_CrudDto
{
    /**
     * @var string
     */
    protected $_service = 'Solicitacao';

    /**
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => 'Sgdoce\Model\Entity\Solicitacao',
        'mapping' => array(
            'sqTipoAssuntoSolicitacao' => 'Sgdoce\Model\Entity\TipoAssuntoSolicitacao'
        )
    );

    protected $_fncConfigList   = "";

    /**
     * PAINEL DO GERENCIAR SOLICITAÇÕES.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->caixa = $this->getRequest()->getParam('caixa', 'collapseOne');
        $this->getMessaging()->dispatchPackets();
    }

    /**
     * @return void
     */
    public function pesquisaAction()
    {
    }

    /**
     *
     * @return void
     */
    public function triarAction()
    {
        $this->_helper->layout->disableLayout();
        $sqSolicitacao = $this->getRequest()->getParam('id', false);
        $this->view->entSolicitacao = false;

        if( $sqSolicitacao ) {
            $entSolicitacao = $this->getService()
                                   ->find($sqSolicitacao);

            $this->_emailSolicitante($entSolicitacao);

//            $arrEmailSolicitante = $entSolicitacao->getSqPessoa()->getSqEmail();
//
//
//            if (count($arrEmailSolicitante) > 0) {
//                $hasEmailInstitucional = false;
//                //verifica se tem email institucional
//                foreach ($arrEmailSolicitante as $key => $entVwEmail) {
//                    if ($entVwEmail->getSqTipoEmail()->getSqTipoEmail() == Core_Configuration::getCorpTipoEmailInstitucional()) {
//                        $hasEmailInstitucional = true;
//                        $entVwEmailSolicitante = $entVwEmail;
//                        break;
//                    }
//                }
//                //se não tem pega o primeiro email
//                if (!$hasEmailInstitucional) {
//                    $entVwEmailSolicitante = $arrEmailSolicitante[0];
//                }
//            }else{
//                $entVwEmailSolicitante = new \Sgdoce\Model\Entity\VwEmail;
//            }

            $this->view->entSolicitacao        = $entSolicitacao;
//            $this->view->entVwEmailSolicitante = $entVwEmailSolicitante;

            $dto = \Core_Dto::factoryFromData(array(
                'sqPerfil' => \Core_Configuration::getSgdocePerfilSgi()
            ), 'search');

            $this->view->listAtendentes = $this->getService('VwUsuario')
                                               ->comboPorPerfil($dto);
        }
    }

    /**
     * @return void
     */
    public function triarMultAction()
    {
        $this->_helper->layout->disableLayout();

        $sqSolicitacao = $this->getRequest()->getParam('id', array());
        $this->view->arrSqSolicitacao = $sqSolicitacao;

        $dto = \Core_Dto::factoryFromData(array(
                'sqPerfil' => \Core_Configuration::getSgdocePerfilSgi()
        ), 'search');

        $this->view->listAtendentes = $this->getService('VwUsuario')->comboPorPerfil($dto);
    }

    /**
     *
     * @return void
     */
    public function devolverAction()
    {
        $this->_helper->layout->disableLayout();

        $sqSolicitacao = $this->getRequest()->getParam('id', array());
        $this->view->arrSqSolicitacao = $sqSolicitacao;
    }

    /**
     *
     * @return void
     */
    public function finalizarAction()
    {
        $this->_helper->layout->disableLayout();
        $sqSolicitacao = $this->getRequest()->getParam('id', false);
        $this->view->entSolicitacao = false;

        if( $sqSolicitacao ) {
            $entSolicitacao = $this->getService()
                                   ->find($sqSolicitacao);

            $this->_emailSolicitante($entSolicitacao);
            $this->view->entSolicitacao = $entSolicitacao;
            $this->view->dataUltimoTramite = $this->getService('StatusSolicitacao')
                                                  ->getUltimoStatusSolicitacao($sqSolicitacao);

            $this->view->visualizar     = false;
        }
    }

    /**
     * tenta recuperar o email institucional caso nao encontra recupera o primeiro da lista
     *
     * @param \Sgdoce\Model\Entity\Solicitacao $entSolicitacao
     * @return \Sgdoce\Model\Entity\VwEmail
     */
    private function _emailSolicitante(\Sgdoce\Model\Entity\Solicitacao $entSolicitacao)
    {
        $arrEmailSolicitante = $entSolicitacao->getSqPessoa()->getSqEmail();

        if (count($arrEmailSolicitante) > 0) {
            $hasEmailInstitucional = false;
            //verifica se tem email institucional
            foreach ($arrEmailSolicitante as $key => $entVwEmail) {
                if ($entVwEmail->getSqTipoEmail()->getSqTipoEmail() == Core_Configuration::getCorpTipoEmailInstitucional()) {
                    $hasEmailInstitucional = true;
                    $entVwEmailSolicitante = $entVwEmail;
                    break;
                }
            }
            //se não tem pega o primeiro email
            if (!$hasEmailInstitucional) {
                $entVwEmailSolicitante = $arrEmailSolicitante[0];
            }
        }else{
            $entVwEmailSolicitante = new \Sgdoce\Model\Entity\VwEmail;
        }
        return $this->view->entVwEmailSolicitante = $entVwEmailSolicitante;
    }

    /**
     *
     * @return void
     */
    public function visualizarAction()
    {
        $this->_helper->layout->disableLayout();
        $sqSolicitacao = $this->getRequest()->getParam('id', false);
        $this->view->entSolicitacao = false;

        if( $sqSolicitacao ) {
            $entSolicitacao = $this->getService()
                                   ->find($sqSolicitacao);

            $this->_emailSolicitante($entSolicitacao);
            $this->view->entSolicitacao = $entSolicitacao;
            $dataUltimoTramite = $this->getService('StatusSolicitacao')
                                      ->getStatusPorTipo($sqSolicitacao, \Core_Configuration::getSgdoceTipoStatusSolicitacaoEmAndamento());
            $this->view->dataUltimoTramite = current($dataUltimoTramite);
            $this->view->dataUltimoTriagem = $this->getService('StatusSolicitacao')
                                                  ->getUltimoStatusSolicitacao($sqSolicitacao);

            $this->view->visualizar = true;
        }
    }
    /**
     * @return void
     */
    public function createAction ()
    {
        parent::createAction();

        $dto = \Core_Dto::factoryFromData(array(
            'inTipoParaArtefato' => TRUE,
            'query' => ''
        ), 'search');

        $this->view->comboTipoAssuntoSolicitacao = $this->getService()->comboTipoAssuntoSolicitacao($dto);
    }

    /**
     * @return void
     */
    public function saveAction ()
    {
        $sqArtefato = $this->getRequest()
                       ->getParam('sqArtefato', false);
        if($sqArtefato){
            $this->_optionsDtoEntity['mapping']['sqArtefato'] = 'Sgdoce\Model\Entity\Artefato';
        }

        if (!$this->_request->isPost()) {
            throw new RuntimeException('A requisição deve ser POST');
        }

        $this->_save();
        $this->getService()->finish();
        $this->_addMessageSave();
        return $this->_redirectActionDefault('pesquisa');
    }

    /**
     * @return void
     */
    public function saveStatusAction()
    {
        $controller = $this->getRequest()
                           ->getControllerName();
        $action     = 'index';
        $caixa      = '';

        try {
            $this->_helper->layout->disableLayout();

            $params = $this->_getAllParams();

            $arrSqSolicitacao = $params['sqSolicitacao'];
            unset($params['sqSolicitacao']);
            if(is_numeric($arrSqSolicitacao)){
                $arrSqSolicitacao = array($arrSqSolicitacao);
            }

            foreach( $arrSqSolicitacao as $sqSolicitacao ) {
                $params['sqSolicitacao'] = $sqSolicitacao;
                $dto = \Core_Dto::factoryFromData($params, 'search');
                $this->getService('StatusSolicitacao')
                     ->newStatusSolicitacao($dto);
                unset($dto);
            }

            $dto = \Core_Dto::factoryFromData($params, 'search');

        } catch( \Exception $e ) {
            $this->getMessaging()->addErrorMessage($e->getMessage(), 'User');
            $this->getMessaging()->dispatchPackets();
        }

        if ($dto->getSqTipoStatusSolicitacao() == \Core_Configuration::getSgdoceTipoStatusSolicitacaoDevolvidaTriagem() ||
            $dto->getSqTipoStatusSolicitacao() == \Core_Configuration::getSgdoceTipoStatusSolicitacaoAberta())
        {
            $caixa = 'collapseOne';
        } else {
            $caixa = 'collapseTwo';
        }

        $this->_redirect = array(
            'controller' => $controller,
            'action'     => $action,
            'params'     => array('caixa' => $caixa)
        );

        return $this->_redirectActionDefault('index');
    }

    public function excluirImagemAction()
    {
        $this->_helper->layout->disableLayout();

        $params = $this->_getAllParams();

        $entSolicitacao = $this->getService()->find($params['id']);

        if( $this->getRequest()->isPost() ){

            $entArtefato = $entSolicitacao->getSqArtefato();
            $entArtefatoImagem = $this->getService('ArtefatoImagem')->excluirImagem($entArtefato, $params['txObservacao']);

            $controller = $this->getRequest()
                                ->getControllerName();
            $action     = 'index';
            $caixa      = '';

            $dto = \Core_Dto::factoryFromData($params, 'search');

            /*switch( $dto->getSqTipoStatusSolicitacao() ){
                case \Core_Configuration::getSgdoceTipoStatusSolicitacaoFinalizada():
                    $caixa = 'collapseTree';
                    break;
                case \Core_Configuration::getSgdoceTipoStatusSolicitacaoEmAndamento():
                    $caixa = 'collapseTwo';
                    break;
                case \Core_Configuration::getSgdoceTipoStatusSolicitacaoDevolvidaTriagem():
                case \Core_Configuration::getSgdoceTipoStatusSolicitacaoAberta():
                    $caixa = 'collapseOne';
                    break;
                default:
                    $caixa = 'collapseTwo';
            }*/

            if ($dto->getSqTipoStatusSolicitacao() == \Core_Configuration::getSgdoceTipoStatusSolicitacaoDevolvidaTriagem() ||
                $dto->getSqTipoStatusSolicitacao() == \Core_Configuration::getSgdoceTipoStatusSolicitacaoAberta())
            {
                $caixa = 'collapseOne';
            } else {
                $caixa = 'collapseTwo';
            }

            $this->_redirect = array(
                'controller' => $controller,
                'action'     => $action,
                'params'     => array('caixa' => $caixa)
            );

            return $this->_redirectActionDefault('index');
        }

        if( $entSolicitacao ) {

            $entArtefato = $entSolicitacao->getSqArtefato();

            $nuArtefato = ($entArtefato->getNuDigital())
                            ? str_pad( $entArtefato->getNuDigital()->getNuEtiqueta(), 7, '0', STR_PAD_LEFT )
                            : $this->getService('Processo')
                                   ->formataProcessoAmbitoFederal($entArtefato);

            $this->view->txDefaultTxObservacao = "Exclusão de imagem do artefato: "
                    . $nuArtefato .
                    " conforme demanda: #" . $entSolicitacao->getSqSolicitacao();

            $this->view->entArtefato = $entArtefato;
        }
    }

    /**
     *
     */
    public function listGerenciarAction()
    {
        $this->view->noAction = $this->_getParam('noAction', false);
        $this->view->withCbox = $this->_getParam('wtCheckbox', false);
        $this->_fncConfigList = "getConfigListGerenciar";
        $this->listAction();
    }

    /**
     *
     */
    public function listGerenciarMinhaAction()
    {
        $this->view->withCbox = true;
        $this->_fncConfigList = "getConfigListGerenciarMinha";
        $this->view->withSituacao = false;
        $this->listAction();
        $this->render('list-gerenciar');
    }

    /**
     *
     */
    public function listHistoricoAction()
    {

        // desabilitando layout
        $this->getHelper('layout')->disableLayout();
        // retornando valor pra grid
        $this->view->noAction = $this->_getParam('noAction', false);
        $this->view->withCbox = false;
        $this->view->withSituacao = true;

        $this->view->grid = new Core_Grid(array(
                array('alias' => 'sq_solicitacao'),
                array('alias' => 'dt_solicitacao'),
                array('alias' => 'no_tipo_status_solicitacao'),
                array('alias' => 'no_pessoa_abertura'),
                array('alias' => 'no_unidade_abertura'),
                array('alias' => 'nu_artefato'),
                array('alias' => 'no_tipo_assunto_solicitacao'),
                array('alias' => 'ds_solicitacao'),
        ));

        $params = $this->_getAllParams();

        $params = $this->view->grid->mapper($params);
        // tratando parametros
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');

        $this->view->params = $params;

        $this->view->result = $this->getService()->listGridHistorico($this->view->dto);

//        try {
//
//            $this->view->noAction = $this->_getParam('noAction', false);
//            $this->view->withCbox = false;
//            $this->view->withSituacao = true;
//            $this->_fncConfigList = "getConfigListGerenciarHistorico";
//            $this->listAction();
//            $this->render('list-gerenciar');
//        } catch (Exception $ex) {
//
//            echo '<pre>';
//            print_r($ex->getMessage());
//            die('</pre><b>File:</b> ' . __FILE__ . '</br><b>Line: </b>' . __LINE__);
//
//        }
    }

    /**
     *
     */
    public function listPesquisarAction()
    {
        $this->_fncConfigList = "getConfigListPesquisar";
        $this->listAction();
    }

    /**
     * @return void
     */
    public function listAction ()
    {
        $params = $this->_getAllParams();
        if( isset($params['dtSolicitacao'])
            && $params['dtSolicitacao'] != '' ) {
            $this->getRequest()
                 ->setParam('dtInicial', $params['dtSolicitacao']);
            $this->getRequest()
                 ->setParam('dtFinal', $params['dtSolicitacao']);
        }
        parent::listAction();
        $this->view->params = $params;
    }

    /**
     * @return void
     */
    public function searchTipoAssuntoSolicitacaoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();

        $params['inTipoParaArtefato'] = null;

        if (isset($params['extraParam'])) {
            $params['inTipoParaArtefato'] = ($params['extraParam'] > 0 ) ? '1' : '0';
        }
        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $this->getHelper('json')->sendJson($this->getService()->comboTipoAssuntoSolicitacao($dtoSearch));
    }

    /**
     */
    public function searchArtefatoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        if ( isset($params['extraParam'])
             && $params['extraParam'] != '' ) {
            $params['sqTipoArtefato'] = $params['extraParam'];
        } else {
            $params['sqTipoArtefato'] = \Core_Configuration::getSgdoceTipoArtefatoDocumento();
        }

        $params['sqUnidadeOrg'] = \Core_Integration_Sica_User::getUserUnit();
        $params['sqPessoa']     = \Core_Integration_Sica_User::getPersonId();

        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $this->getHelper('json')->sendJson($this->getService()->comboArtefato($dtoSearch));
    }

    /**
     * Metódo que realiza a configuração dos extrasave
     *
     * @return array
     */
    protected function _factoryParamsExtrasSave($data)
    {
        $dto = Core_Dto::factoryFromData($data, 'search');
        return array($dto);
    }

    /**
     * Método para preencher os dados da pesquisa
     *
     * @param $params Dados da requisição
     */
    public function getResultList(\Core_Dto_Search $dto)
    {
        return $this->getService()->listGrid($dto);
    }

    /**
     * Retorna array de configuração da pesquisa
     *
     * @return array
     */
    public function getConfigList()
    {
        $getConfigList = $this->_fncConfigList;
        return $this->$getConfigList();
    }

    /**
     * @return array
     */
    public function getConfigListPesquisar()
    {
        return array(/*'columns' => array(
                array('alias' => 'sq_solicitacao'),
                array('alias' => 'nu_artefato'),
                array('alias' => 'dt_solicitacao'),
                array('alias' => 'no_tipo_assunto_solicitacao'),
                array('alias' => 'ds_solicitacao'),
                array('alias' => 'no_pessoa_atendimento'),
                array('alias' => 'dt_operacao'),
                array('alias' => 'no_tipo_status_solicitacao'),
        )*/);
    }

    /**
     * @return array
     */
    public function getConfigListGerenciar()
    {
        return array('columns' => array(
                array('alias' => 'sq_solicitacao'),
                array('alias' => 'dt_solicitacao'),
                array('alias' => 'no_pessoa_abertura'),
                array('alias' => 'no_unidade_abertura'),
                array('alias' => 'nu_artefato'),
                array('alias' => 'no_tipo_assunto_solicitacao'),
                array('alias' => 'ds_solicitacao'),
        ));
    }

    /**
     * @return array
     */
    public function getConfigListGerenciarMinha()
    {
        return array('columns' => array(
                array('alias' => 'sq_solicitacao'),
                array('alias' => 'sq_solicitacao'),
                array('alias' => 'dt_solicitacao'),
                array('alias' => 'no_pessoa_abertura'),
                array('alias' => 'no_unidade_abertura'),
                array('alias' => 'nu_artefato'),
                array('alias' => 'no_tipo_assunto_solicitacao'),
                array('alias' => 'ds_solicitacao'),
        ));
    }

    /**
     * @return array
     */
    public function getConfigListGerenciarHistorico()
    {
        return array('columns' => array(
                array('alias' => 'sq_solicitacao'),
                array('alias' => 'dt_solicitacao'),
                array('alias' => 'no_tipo_status_solicitacao'),
                array('alias' => 'no_pessoa_abertura'),
                array('alias' => 'no_unidade_abertura'),
                array('alias' => 'nu_artefato'),
                array('alias' => 'no_tipo_assunto_solicitacao'),
                array('alias' => 'ds_solicitacao'),
        ));
    }

    public function comboTipoSolicitacaoAssuntoAction ()
    {
        // desabilitando layout e evitando rendereizacao da action
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->_getAllParams();

        if(empty($params['inTipoParaArtefato'])){
            $params['inTipoParaArtefato'] = '0';
        }

        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $this->getHelper('json')->sendJson($this->getService()->comboTipoAssuntoSolicitacao($dtoSearch));
    }

}