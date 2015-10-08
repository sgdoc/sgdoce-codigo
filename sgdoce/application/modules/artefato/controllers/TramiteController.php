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
 * Classe para Controller de Imagem
 *
 * @package  Artefato
 * @category Controller
 * @name     Tramite
 * @version  1.0.0
 */
class Artefato_TramiteController extends \Core_Controller_Action_CrudDto
{

    /**
     * caminho da logo a partir da pasta public/
     *
     * @var string
     * */
    const IMG_LOGO_PATH = '/img/marcaICMBio.png';

    /**
     * Serviço
     * @var string
     */
    protected $_service  = 'TramiteArtefato';
    protected $_redirect = '/artefato/area-trabalho/index';

    public function indexAction()
    {
        $this->_checkRedirect();

        $arrSqArtefato = $this->getRequest()->getParam('sqArtefato');
        $arrMsg        = $this->getService()->checkCanTramitarArtefato($arrSqArtefato);

        if($arrMsg){
            foreach($arrMsg as $msg){
                $this->getMessaging()->addInfoMessage($msg);
            }
            $this->_redirectActionDefault('index');
        }

        $this->view->tipoArtefatoBack = $this->getRequest()->getParam('tipoArtefato',1);
        $this->view->arrArtefato      = $this->getService()->getArtefatoToTramite($arrSqArtefato);
        $this->view->tipoPessoa       = $this->getService('TipoPessoa')->comboTipoPessoa();
        $this->view->tipoRastreamento = $this->getService('TipoRastreamentoCorreio')
                                             ->getComboDefault(array(),array('noTipoRastreamentoCorreio'=> 'ASC'));

        $backUrl = $this->getRequest()->getParam('back',false);
        if ($backUrl) {
            $this->view->backUrl = str_replace('.','/',$backUrl);
        }else{
            $this->view->backUrl = '/artefato/area-trabalho/index/tipoArtefato/' . $this->view->tipoArtefatoBack;
        }

        //se tiver mais de um artefato para tramitar
        //garantir que não há artefato sigiloso
        if (count($this->view->arrArtefato) > 1) {
            $sigiloso = FALSE;
            $arrArtefatoSigiloso = array();
            foreach ($this->view->arrArtefato as $sqArtefato => $values) {
                if ($values['hasVinculoSigiloso']) {
                    $sigiloso = TRUE;
                    $arrArtefatoSigiloso[] = $nuArtefato = $this->view->nuArtefato($values['entity']);
                }
            }
            $acao = 'tramitado';
            if ($sigiloso) {
                $message = 'MN187';
                if(count($arrArtefatoSigiloso) > 1){
                    $message = 'MN188';
                }

                $this->getMessaging()->addInfoMessage(
                        sprintf(
                                \Core_Registry::getMessage()->translate($message),
                                implode(', ', $arrArtefatoSigiloso),
                                $acao
                        ), 'User');
                $this->getMessaging()->dispatchPackets();
                $this->_redirect($this->view->backUrl);
            }
        }
    }

    public function validateSigiloAction ()
    {


        $mixSqArtefato = $this->getRequest()->getParam('sqArtefato');
        $back = (boolean)$this->getRequest()->getParam('back','0');

        if( !is_array($mixSqArtefato) ){
            $mixSqArtefato = array((integer)$mixSqArtefato);
        }

        $type  = 'Sucesso';
        $msg   = '';
        $error = false;
        $arrArtefatoSigiloso = array();
        try {
            foreach($mixSqArtefato as $sqArtefato){
                $entArtefato = $this->getService('Artefato')->find($sqArtefato);
                $hasVinculoSigiloso = $this->getServiceLocator()
                                           ->getService('ArtefatoVinculo')
                                           ->hasVinculoSigiloso ($entArtefato->getSqArtefato());
                if ($hasVinculoSigiloso) {
                    $arrArtefatoSigiloso[] = $nuArtefato = $this->view->nuArtefato($entArtefato);
                }
            }
            $acao = 'tramitado';
            if($back){
                $acao = 'retornado';
            }

            if ($arrArtefatoSigiloso) {
                $message = 'MN187';
                if(count($arrArtefatoSigiloso) > 1){
                    $message = 'MN188';
                }
                throw new \Core_Exception(
                        sprintf(
                                \Core_Registry::getMessage()->translate($message),
                                implode(', ', $arrArtefatoSigiloso),
                                $acao
                        ));
            }

        } catch( Exception $exception ) {
            $error = true;
            $type  = 'Alerta';
            $msg  .= $exception->getMessage();
        }

        $this->_helper->json(array(
            'error' => $error,
            'type' => $type,
            'msg' => $msg
        ));
    }

    public function validateExternalBackSigiloAction ()
    {
        $sqArtefato = $this->getRequest()->getParam('sqArtefato');

        $hasVinculoSigiloso = $this->getServiceLocator()
                                   ->getService('ArtefatoVinculo')
                                   ->hasVinculoSigiloso ($sqArtefato);

        $this->_helper->json(array('hasVinculoSigiloso' => $hasVinculoSigiloso));
    }

    public function saveAction ()
    {
        $params = $this->_getAllParams();

        $this->getService()->processTramite($params);

        if ($params['tipo_tramite'] == 2 && $params['stImprimeGuia']) {
            $txEndereco = $params['txEndereco'];
            $objZSN = new \Zend_Session_Namespace("TramiteTxEndereco");
            $objZSN->txEndereco = $txEndereco;
            $this->_redirect .= "/tipoArtefato/{$params['tipoArtefato']}/guia/". implode('|',$params['sqArtefato']);
        }
        $this->_redirect($this->_redirect);
    }

    /**
     * @return void
     */
    public function printGuiaAction()
    {
        $this->_helper->layout()->disableLayout();
        $params        = $this->_getAllParams();
        $arrSqArtefato = explode('|', $params['data']) ;

        //recupera o ultimo tramite de um dos documentos tramitados pois são todos iguais
        $entityUltimoTramite = $this->getService('VwUltimoTramiteArtefato')->find($arrSqArtefato[0]);

        //@TODO:   TESTAR PRA VER SE DA PRA PEGAR O SQ_ENDERECO DA $entityUltimoTramite
        $enderecoDestino = NULL;

        /*
        $entEndereco = $this->getService('VwEndereco')->find($params['endereco']);

        $enderecoDestino = '';
        if ($entEndereco) {
            $nuEndereco       = $entEndereco->getNuEndereco();
            $enderecoDestino  = $entEndereco->getNoBairro();
            $enderecoDestino .= ', ' . $entEndereco->getTxEndereco();
            $enderecoDestino .= ', ' . (is_null($nuEndereco) ? 'S/N': 'Nº ' . $nuEndereco );
            $enderecoDestino .= ', ' . $entEndereco->getTxComplemento();

            $enderecoDestino = rtrim(trim($enderecoDestino),',');
        }*/

        $objZSN = new \Zend_Session_Namespace("TramiteTxEndereco");
        if( isset($objZSN->txEndereco) ) {
            $enderecoDestino = $objZSN->txEndereco;
            $objZSN->unsetAll();
        }

        $data = $this->getService()->getArtefatoGuia($arrSqArtefato);

        $options = array(
            'fname' => sprintf('Guia-%d.pdf', date('YmdHis')),
            'path' => APPLICATION_PATH . '/modules/artefato/views/scripts/tramite/'
        );

        $logo = current(explode('application', __FILE__))
                . 'public' . DIRECTORY_SEPARATOR
                . ltrim(self::IMG_LOGO_PATH, DIRECTORY_SEPARATOR);

        \Core_Doc_Factory::setFilePath($options['path']);

        $viewParams = array(
            'data' => $data,
            'logo' => $logo,
            'entityUTA' => $entityUltimoTramite,
            'endereco' => $enderecoDestino,
            'maskNumber' => new \Core_Filter_MaskNumber(),
        );

        \Core_Doc_Factory::download('print-guia', $viewParams, $options['fname']);

    }

    public function rescueAction ()
    {
        try {
            $type  = 'Sucesso';
            $msg   = \Core_Registry::getMessage()->translate('MN013');
            $error = FALSE;
            $params = $this->_getAllParams();
            $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
            $this->getService()->rescue($dtoSearch);
        } catch( \Exception $exception ) {
            $error = TRUE;
            $type  = 'Alerta';
            $msg  .= $exception->getMessage();
        }

        $this->_helper->json(array(
            'error' => $error,
            'type' => $type,
            'msg' => $msg
        ));
    }

    public function receiveAction()
    {
        $this->_checkRedirect();

        $mixSqArtefato = $this->getRequest()->getParam('sqArtefato');

        if( !is_array($mixSqArtefato) ){
            $mixSqArtefato = array((integer)$mixSqArtefato);
        }

        $type  = 'Sucesso';
        $msg   = '';
        $error = false;
        $total = count($mixSqArtefato);
        $ops   = 0;
        $opf   = 0;
        $msgExcp  = '';

        foreach($mixSqArtefato as $sqArtefato){
            try {
                $this->getService()->receive($sqArtefato);
                $ops++;
            } catch( \Exception $exception ) {
                $error = true;
                $type  = 'Alerta';
                $msgExcp  .= $exception->getMessage() . "<br />";
                $opf++;
            }
        }
        // Tratamento para mensagens
        if( $total == 1 ) {
            if ($msgExcp) {
                $msg = $msgExcp;
            }else{
                $msg = \Core_Registry::getMessage()->translate('MN164');
            }
        } else {
            if( $total == $ops ) {
                $msg = \Core_Registry::getMessage()->translate('MN195');
            } else if( $ops > 0 ) {
                $msg = sprintf(\Core_Registry::getMessage()->translate('MN196'), $ops);
            }

            if( $opf > 0 ) {
                $msg .= ($ops > 0) ? "<br />" : "";
                $msg .= $msgExcp;
            }
        }

        $this->_helper->json(array(
            'error' => $error,
            'type' => $type,
            'msg' => $msg
        ));
    }

    /**
     * @return void
     */
    public function cancelAction()
    {
        $this->_checkRedirect();

        $mixSqArtefato = $this->getRequest()->getParam('sqArtefato');

        if( !is_array($mixSqArtefato) ){
            $mixSqArtefato = array((integer)$mixSqArtefato);
        }

        $type  = 'Sucesso';
        $msg   = '';
        $error = false;
        $total = count($mixSqArtefato);
        $ops   = 0;
        $opf   = 0;
        $msgExcp  = '';

        foreach($mixSqArtefato as $sqArtefato){
            try {
                $this->getService()->cancel($sqArtefato);
                $ops++;
            } catch( \Exception $exception ) {
                $error = true;
                $type  = 'Alerta';
                $msgExcp  .= $exception->getMessage() . "<br />";
                $opf++;
            }
        }
        // Tratamento para mensagens
        if( $total == 1 ) {
            if ($msgExcp) {
                $msg = $msgExcp;
            }else{
                $msg = \Core_Registry::getMessage()->translate('MN159');
            }
        } else {
            if( $total == $ops ) {
                $msg = \Core_Registry::getMessage()->translate('MN192');
            } else if( $ops > 0 ) {
                $msg = sprintf(\Core_Registry::getMessage()->translate('MN193'), $ops);
            }

            if( $opf > 0 ) {
                $msg .= ($ops > 0) ? "<br />" : "";
                $msg .= $msgExcp;
            }
        }

        $this->_helper->json(array(
            'error' => $error,
            'type' => $type,
            'msg' => $msg
        ));
    }

    public function returnAction()
    {
        $this->_checkRedirect();

        $mixSqArtefato = $this->getRequest()->getParam('sqArtefato');

        if( !is_array($mixSqArtefato) ){
            $mixSqArtefato = array((integer)$mixSqArtefato);
        }

        $type  = 'Sucesso';
        $msg   = '';
        $error = false;
        $total = count($mixSqArtefato);
        $ops   = 0;
        $opf   = 0;
        $msgExcp  = '';

        foreach($mixSqArtefato as $sqArtefato){
            try {
                $this->getService()->goBack($sqArtefato);
                $ops++;
            } catch( \Exception $exception ) {
                $error = true;
                $type  = 'Alerta';
                $msgExcp  .= $exception->getMessage() . "<br />";
                $opf++;
            }
        }
        // Tratamento para mensagens
        if( $total == 1 ) {
             if ($msgExcp) {
                $msg = $msgExcp;
            }else{
                $msg = \Core_Registry::getMessage()->translate('MN160');
            }
        } else {
            if( $total == $ops ) {
                $msg = \Core_Registry::getMessage()->translate('MN197');
            } else if( $ops > 0 ) {
                $msg = sprintf(\Core_Registry::getMessage()->translate('MN198'), $ops);
            }

            if( $opf > 0 ) {
                $msg .= ($ops > 0) ? "<br />" : "";
                $msg .= $msgExcp;
            }
        }

        $this->_helper->json(array(
            'error' => $error,
            'type' => $type,
            'msg' => $msg
        ));
    }

    public function returnSigilosoAction()
    {
        $this->_checkRedirect();

        $params = $this->_getAllParams();

        $type  = 'Sucesso';
        $msg   = '';
        $error = false;

        try {
            $this->getService()->goBack($params['sqArtefato'], $params['sqPessoaDestino'], $params['sqPessoaDestinoInterno']);
            $msg = $this->getServiceLocator()
                               ->getService('HistoricoArtefato')
                               ->getMessage('MN160');
            $msg .= " <br />";
        } catch( \Exception $exception ) {
            $error = true;
            $type  = 'Alerta';
            $msg  .= $exception->getMessage();
        }

        $this->_helper->json(array(
            'error' => $error,
            'type' => $type,
            'msg' => $msg
        ));
    }

    public function returnModalAction()
    {
        $this->getHelper('layout')->disableLayout();
        $entityArtefato = $this->getService('Artefato')->find($this->_getParam('id'));

        $this->view->entityArtefato = $entityArtefato;

        $this->render('modal-return');
    }

    /**
     * Retorna as unidades organizacionais cadastrados em formato json
     * @return void
     */
    public function searchUnidadeOrgAction()
    {
        $result =  $this->getService('VwUnidadeOrg')
                        ->searchUnidadesOrganizacionais($this->_getAllParams());
        $this->_helper->json($result);
    }

    /**
     * Metódo que recupera a pessoa
     * @return json
     */
    public function funcionarioUnidadeSetorAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();
        if (!$params['extraParam']) {
            $this->_helper->json(array());
        }else{
            $params['sqUnidadeExercicio'] = $this->getService('VwPessoa')->find($params['extraParam']);
            $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
            $result = $this->getService('Pessoa')->searchPessoaPorSetorOuUnidade($dtoSearch);
            $this->_helper->json($result);
        }
    }

    public function modalRastreamentoAction()
    {
        $this->getHelper('layout')->disableLayout();
        $params = $this->_getAllParams();

        $entityArtefato = $this->getService('Artefato')->find($params['sqArtefato']);
        $this->view->entityArtefato = $entityArtefato;
    }

    public function listRastreamentoAction()
    {
        // desabilitando layout
        $this->getHelper('layout')->disableLayout();
        // retornando valor pra grid
        $this->view->grid = new Core_Grid(
            array('columns' => array(
                 array('alias' => 'no_pessoa_destino')
                ,array('alias' => 'no_tipo_rastreamento')
                ,array('alias' => 'tx_codigo_rastreamento')
                ,array('alias' => 'dt_envio')
                ,array('alias' => 'no_remetente')
            ),
        ));

        $params = $this->view->grid->mapper($this->_getAllParams());

        // tratando parametros
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        // retornando valores pra view
        $this->view->result = $this->getService()->listTramiteExternoComRastreamento($this->view->dto);
    }

    private function _checkRedirect()
    {
        $params = $this->_getAllParams();

        if(!isset($params['sqArtefato'])){
            $this->_redirect('/artefato/area-trabalho');
        }
    }

    /**
     * SALVA HISTÓRICO AREA DE TRABALHO.
     *
     * @param integer $sqArtefato
     * @param integer $sqOcorrencia
     * @param string $strMessage
     *
     * @return Sgdoce\Model\Entity\HistoricoArtefato
     */
    protected function _salvarHistoricoArtefato($sqArtefato, $sqOcorrencia, $strMessage)
    {
        $arrOptEntity = array(
            'entity'    => 'Sgdoce\Model\Entity\HistoricoArtefato',
            'mapping'   => array(
                'sqArtefato'    => 'Sgdoce\Model\Entity\Artefato',
                'sqOcorrencia'  => 'Sgdoce\Model\Entity\Ocorrencia',
            )
        );

        $arrData = array(
            'sqArtefato'    => $sqArtefato,
            'sqOcorrencia'  => $sqOcorrencia,
            'txDescricaoOperacao' => $strMessage
        );

        $entHistoricoArtefato = \Core_Dto::factoryFromData($arrData, 'entity', $arrOptEntity);

        return $this->getServiceLocator()
                    ->getService('HistoricoArtefato')
                    ->save($entHistoricoArtefato);
    }

    public function getEnderecosByPessoaAction ()
    {
        $arrEndereco = $this->getService()->getEnderecosByPessoa(
            Core_Dto::factoryFromData($this->_getAllParams(), 'search')
        );

        $this->_helper->json($arrEndereco);

    }
}
