<?php
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

/**
 * SISICMBio
 *
 * Classe Controller de Area de Trabalho
 *
 * @package      Minuta
 * @subpackage   Controller
 * @name         AreaTrabalho
 * @version      1.0.0
 * @since        2012-01-10
 */
class Artefato_AreaTrabalhoController extends \Core_Controller_Action_CrudDto
{
    /**
    * Variavel para receber o nome da service
    * @var    string
    * @access protected
    * @name   $_service
    */
    protected $_service = 'AreaTrabalho';

    public function postDispatch()
    {
        parent::postDispatch();
        $this->getMessaging()->dispatchPackets();
    }

    public function indexAction()
    {
        $this->view->tipoArtefato   = $this->getRequest()->getParam('tipoArtefato',null);
        $this->view->caixa          = $this->getRequest()->getParam('caixa',null);
        $this->view->currentProfile = \Core_Integration_Sica_User::getUserProfile();

        if(!$this->view->tipoArtefato){
            $this->_redirect('/artefato/area-trabalho/index/tipoArtefato/'
                    . \Core_Configuration::getSgdoceTipoArtefatoDocumento()
                    .'/caixa/minhaCaixa');
        }

        //parametro enviado pela tela de tramite em casos de
        //tramite externo com impressão de "guia de tramite externo"
        $this->view->printGuia   = $this->getRequest()->getParam('guia',false);
        $this->view->enderecoGuia= $this->getRequest()->getParam('endereco','');

        $this->view->unit_box     = 1;
        $this->view->my_box       = 2;
        $this->view->external_box = 3;
        $this->view->archive_box  = 4;
    }

    /**
    * Obtém dados de perfil do usuario
    * @return stdClass
    */
    public function getUser()
    {
        return Core_Integration_Sica_User::get();
    }

    /**
    * Método que obtém os dados para grid
    * @param \Core_Dto_Search $dtoSearch
    * @return array
    */
    public function getResultList(\Core_Dto_Search $dtoSearch)
    {
        $this->view->perfil        = Core_Integration_Sica_User::getUserProfile();
        $dtoSearch->sqPessoa       = Core_Integration_Sica_User::getPersonId();
        $dtoSearch->sqUnidadeOrg   = Core_Integration_Sica_User::getUserUnit();
        $dtoSearch->sqTipoArtefato = $this->getRequest()->getParam('tipoArtefato') ?
                                     $this->getRequest()->getParam('tipoArtefato') : 1;
        $dtoSearch->search         = $this->getRequest()->getParam('search') ?
                                     $this->getRequest()->getParam('search') : NULL;

        $this->view->isUserSgi     = $this->_isUserSgi();
        $this->view->isUnidadePro  = $this->getService('AutuarDocumento')->isUnidadeProtocolizadora(false);
        $this->view->isUserPro     = ($this->view->perfil == \Core_Configuration::getSgdocePerfilProtocolo());
        $this->view->isAllowedAlter= in_array(\Core_Integration_Sica_User::getUserProfile(), $this->getService()->getUsersAllowedAlterArtefact());

        $dtoUnidadeOrg = Core_Dto::factoryFromData(array('sqUnidadeOrg' => $dtoSearch->sqUnidadeOrg), 'search');
        $dtoSearch->currentUnitHasNUP = $this->getService('VwUnidadeOrg')->hasNUP($dtoUnidadeOrg);

        $caixa = null;
        switch (true) {
            case $this->_getParam('unit_box'):
                $caixa = $this->_getParam('unit_box');
                break;
            case $this->_getParam('my_box'):
                $caixa = $this->_getParam('my_box');
                break;
            case $this->_getParam('archive_box'):
                $caixa = $this->_getParam('archive_box');
                break;
            default:
                $caixa = $this->_getParam('external_box');
                break;
        }

        $dtoSearch->caixa = $caixa;

        $res = $this->getService()->getGrid($dtoSearch);
        return $res;
    }

    /**
     *
     * Método que configura os dados da grid
     * @return array
     */
    public function getConfigList()
    {

        return array();
        //este retorno serve para ordenação da grid. como não é usado ordenação retorna array vazio
//        return array('columns' => array(
//                                        array('alias' => 'sq_Artefato'),
//                                        array('alias' => 'nu_Digital'),
//                                        array('alias' => 'dt_Cadastro'),
//                                        array('alias' => 'tx_Assunto'),
//                                        array('alias' => 'nu_Artefato'),
//                                        array('alias' => 'no_Tipo_Documento'),
//                                        array('alias' => 'no_Pessoa_Origem'),
//                                        array('alias' => 'tx_Movimentacao'),
//            ));
    }

    public function visualizarMinutaAction()
    {
        $sqArtefato     = $this->getRequest()->getParam('sqArtefato');

        // REGISTRO DE VISUALIZAÇÃO DE MINUTA NO HISTÓRICO. #HistoricoArtefato::save();
        $strMessage = $this->getServiceLocator()
                        ->getService('HistoricoArtefato')
                        ->getMessage('MH002');

        $this->_salvarHistoricoArtefato($sqArtefato,
               \Core_Configuration::getSgdoceSqOcorrenciaVisualizar(),
               $strMessage);

        if ($this->_getParam('abreProcesso')){
            $nunup = $this->getNuNup();

            $criteria = array('sqArtefatoFilho' => $sqArtefato,'sqTipoVinculoArtefato' => 7);
            $autuar = $this->getService('ArtefatoVinculo')->findBy($criteria);
            if (!$autuar && $nunup){
                $this->view->abreProcesso = TRUE;
            } else {
                $this->view->abreProcesso = FALSE;
            }
        }
        // mapeamento da entidade 'pessoa'
        $this->view->sqArtefato = $sqArtefato;
        $dtoOptionArtefato = array('entity'  => 'Sgdoce\Model\Entity\Artefato');
        // transforma o array 'artefato' em objeto
        $dtoEntityArtefato = Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'entity',
                $dtoOptionArtefato);
        $artefatoMinuta = $this->getService('ArtefatoMinuta')->find($sqArtefato);
        $dtoEntityModelo = Core_Dto::factoryFromData(array('sqModeloDocumento' =>
                $artefatoMinuta->getSqModeloDocumento()->getSqModeloDocumento()), 'entity',
                array('entity'=> 'Sgdoce\Model\Entity\ModeloDocumento'));
        $this->view->view = $this->_getParam('view');

        $data = $this->getService('VisualizarCaixaMinuta')->createDocView($dtoEntityArtefato, $dtoEntityModelo);
        $modelo = $this->getService('VisualizarCaixaMinuta')->getModeloMinuta($dtoEntityModelo);
        $this->view->data = $data;

        switch ($modelo['sqPadraoModeloDocumento']) {
            case \Core_Configuration::getSgdocePadraoModeloDocumentoAtos():
                $this->render('visualizarMinutaAtos');
                break;
            case \Core_Configuration::getSgdocePadraoModeloDocumentoGeral():
                $this->render('visualizarMinutaGeral');
                break;
            case \Core_Configuration::getSgdocePadraoModeloDocumentoOficio():
                $this->render('visualizarMinutaOficio');
                break;
        }
    }

    public function listArtefatoArquivoAction ()
    {
        // desabilitando layout
        $this->getHelper('layout')->disableLayout();
        // retornando valor pra grid
        $this->view->grid = new Core_Grid(array(
//                array('alias' => 'sqArtefato'),
//                array('alias' => 'nuDigital'),
//                array('alias' => 'dtArquivamento'),
//                array('alias' => 'txAssunto'),
//                array('alias' => 'nuArtefato'),
//                array('alias' => 'noTipoDocumento'),
//                array('alias' => 'noPessoaOrigem'),
//                array('alias' => 'txMovimentacao'),
            )
        );

        $params = $this->view->grid->mapper($this->_getAllParams());

        // tratando parametros
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->dto->caixa = 4;

        // retornando valores pra view
        $this->view->result = $this->getService()->getGridArquivo($this->view->dto);
    }

    public function listArquivoSetorialAction ()
    {
        // desabilitando layout
        $this->getHelper('layout')->disableLayout();
        // retornando valor pra grid
        $this->view->grid = new Core_Grid(array(
//                array('alias' => 'sqArtefato'),
//                array('alias' => 'nuDigital'),
//                array('alias' => 'dtArquivamento'),
//                array('alias' => 'txAssunto'),
//                array('alias' => 'nuArtefato'),
//                array('alias' => 'noTipoDocumento'),
//                array('alias' => 'noPessoaOrigem'),
//                array('alias' => 'txMovimentacao'),
            )
        );
        $params = $this->_getAllParams();
        $params['sqTipoArtefato'] = $params['tipoArtefato'];

        $params = $this->view->grid->mapper($params);
        // tratando parametros
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->dto->caixa = 5;
        // retornando valores pra view

        $this->view->result = $this->getService()->getGridArquivoSetorial($this->view->dto);
    }

    public function getPersonId()
    {
        return \Core_Integration_Sica_User::getPersonId();
    }

    public function getNuNup()
    {
        $dtoOrigem  = Core_Dto::factoryFromData(array('sqProfissional' => $this->getPersonId()), 'search');
        $unidadeOrg = $this->getService('Dossie')->unidadeOrigemPessoa($dtoOrigem);

        if ($unidadeOrg) {
            $unidadeExercicio = $unidadeOrg->getSqUnidadeExercicio();
        }

        if(!empty($unidadeExercicio)) {
            return $unidadeExercicio->getNuNup();
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

    public function boxMigracaoAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();

        $this->view->typeView       = $params['typeView'];
        $this->view->tipoArtefato   = $params['tipoArtefato'];
        $this->view->migration_box  = $params['migration_box'];

        if(isset($params['nuArtefatoSearch'])){
            $this->view->nuArtefato = $params['nuArtefatoSearch'];
        } else {
            $this->view->nuArtefato = null;
        }

        if(isset($params['urlMigracao'])){
            $this->view->urlMigracao    = $params['urlMigracao'];
        }

        if($params['typeView'] == 'list' ){
            $this->view->typeView = 'search';
            $this->render("tab-migracao-content");
        } else {
            $this->view->typeView = 'list';
            $this->render("form-search-migracao");
        }
    }
}