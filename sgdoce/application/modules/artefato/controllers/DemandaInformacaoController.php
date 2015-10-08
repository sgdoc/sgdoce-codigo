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
class Artefato_DemandaInformacaoController extends \Core_Controller_Action_CrudDto
{
    /**
     * Define tipo de listagem ex: Processo / Documento / Etc
     *
     * @var string
     */
    protected $_fnConfigList = null;
    /**
     * Define tipo de listagem ex: Processo / Documento / Etc
     *
     * @var string
     */
    protected $_fnListGrid = null;

    /**
     * @var string
     */
    protected $_service = 'Prazo';

    /**
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => 'Sgdoce\Model\Entity\Prazo',
        'mapping' => array(
            'sqArtefato'    => 'Sgdoce\Model\Entity\Artefato',
            'sqUnidadeOrgPessoaDestino' => array(
                'sqUnidadeOrg' => 'Sgdoce\Model\Entity\VwUnidadeOrg'
            ),
        )
    );


    /**
     * PAINEL DE DEMANDAS DE INFORMAÇÃO.
     *
     * @return void
     */
    public function indexAction()
    {
        $sqPessoaDestino = $this->getRequest()->getParam('idMeu', null);
        $this->view->sqUnidadeOrgPessoaDestino  = \Core_Integration_Sica_User::getUserUnit();
        $this->view->sqUnidadeOrgPessoaPrazo    = $this->view->sqUnidadeOrgPessoaDestino;
        $this->view->sqPessoaDestino            = $sqPessoaDestino;
        $this->view->sqPessoaPrazo              = $this->view->sqPessoaDestino;
    }

    /**
     * FORMULÁRIO DE GERAÇÃO DE DEMANDA DE INFORMAÇÃO.
     *
     * @return void
     */
    public function gerarAction()
    {
        $this->_helper->layout->disableLayout();
        $sqArtefato = $this->getRequest()->getParam('id', false);
        $sqPrazoPai = $this->getRequest()->getParam('idPai', false);

        if( $sqArtefato ) {
            $entArtefato = $this->getService('Artefato')->find($sqArtefato);
            $this->view->entArtefato = $entArtefato;
            $this->view->entPrazoPai = null;
        }

        if( $sqPrazoPai ) {
            $entPrazo   = $this->getService()->find($sqPrazoPai);
            $sqArtefato = $entPrazo->getSqArtefato()->getSqArtefato();
            $this->view->entArtefato = $entPrazo->getSqArtefato();
            $this->view->entPrazoPai = $entPrazo;
        }
    }

    /**
     * FORMULÁRIO DE RESPOSTA DE DEMANDA DE INFORMAÇÃO.
     *
     * @return void
     */
    public function respostaAction()
    {
        $this->_helper->layout->disableLayout();
        $sqPrazo = $this->getRequest()->getParam('id', false);
        if( $sqPrazo ) {
            $entPrazo =  $this->getService()
                              ->find($sqPrazo);

            $this->view->entPrazo = $entPrazo;
        }
    }

    /**
     * FORMULÁRIO DE RESPOSTA DE DEMANDA DE INFORMAÇÃO.
     *
     * @return void
     */
    public function visualizarAction()
    {
        $this->_helper->layout->disableLayout();
        $sqPrazo = $this->getRequest()->getParam('id', false);
        if( $sqPrazo ) {
            $entPrazo =  $this->getService()
                              ->find($sqPrazo);

            $this->view->entPrazo = $entPrazo;
        }
    }

    /**
     * Página de resultado da pesquisa
     *
     * @return void
     */
    public function listReceivedAction()
    {
        $this->_fnConfigList = "_getConfigListReceived";
        $this->_fnListGrid  = "listGridReceived";
        parent::listAction();
        $this->view->params = $this->_getAllParams();
    }

    /**
     * Página de resultado da pesquisa
     *
     * @return void
     */
    public function listGeneratedAction()
    {
        $this->_fnConfigList = "_getConfigListGenerated";
        $this->_fnListGrid  = "listGridGenerated";
        parent::listAction();
        $this->view->params = $this->_getAllParams();
    }


    /**
     * Método para preencher os dados da pesquisa
     *
     * @param $params Dados da requisição
     */
    public function getResultList(\Core_Dto_Search $dto)
    {
        $listGrid = $this->_fnListGrid;
        return $this->getService()->$listGrid($dto);
    }

    /**
     * Retorna array de configuração da pesquisa
     *
     * @return array
     */
    public function getConfigList()
    {
        $getConfigList = $this->_fnConfigList;
        return $this->$getConfigList();
    }

    /**
     * @return void
     */
    public function saveAction()
    {
        parent::saveAction();
        if (!$this->_request->isPost()) {
            throw new RuntimeException('A requisição deve ser POST');
        }

        $data = $this->_request->getPost();

        $this->_save();
        $this->getService()->finish();
        $this->_addMessageSave();

        $isResposta = $data['isReposta'];

        if($isResposta){
            return $this->_redirectActionDefault('index');
        } else {
            $this->_redirect("/artefato/area-trabalho/index/tipoArtefato/"
                . \Core_Configuration::getSgdoceTipoArtefatoProcesso());
        }
    }

    /**
     * @return void
     */
    protected function _getConfigListReceived()
    {
        return array('columns' => array(
                array('alias' => 'nu_artefato'),
                array('alias' => 'tx_pessoa_origem'),
                array('alias' => 'tx_pessoa_destino'),
                array('alias' => 'tx_solicitacao'),
                array('alias' => 'dt_prazo'),
        ));
    }

    /**
     * @return void
     */
    protected function _getConfigListGenerated()
    {
        return array('columns' => array(
                array('alias' => 'nu_artefato'),
                array('alias' => 'tx_pessoa_origem'),
                array('alias' => 'tx_pessoa_destino'),
                array('alias' => 'tx_solicitacao'),
                array('alias' => 'dt_prazo'),
                array('alias' => 'dt_resposta'),
                array('alias' => 'dt_resposta'),
        ));
    }

    /**
     * Metódo que realiza a configuração dos extrasave
     *
     * @return array
     */
    protected function _factoryParamsExtrasSave($data)
    {
        $searchDto = Core_Dto::factoryFromData($data, 'search');
        $entPrazo  = null;
        if( $searchDto->getSqPrazo() ) {
            $entPrazo  = $this->getService()->find($searchDto->getSqPrazo());
        }

        return array($searchDto, $entPrazo);
    }

    /**
     * @return void
     */
    public function findArtefatoRespostaAction()
    {
        $params = $this->_getAllParams();
        $result = array();

        $params['sqTipoArtefato']   = $params['extraParam'];
        $params['nuArtefato']       = $params['query'];

        $dto = \Core_Dto::factoryFromData($params, 'search');

        $result = $this->getService()->findArtefatoResposta($dto);

        $this->_helper->json($result);
    }
}