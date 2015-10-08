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
 * Classe Controller de Histórico de Despachos
 *
 * @package      Minuta
 * @subpackage   Controller
 * @name         DespachoInterlocutorio
 * @version      1.0.0
 * @since        2014-11-26
 */
class Artefato_DespachoInterlocutorioController extends \Core_Controller_Action_CrudDto
{
    /**
     * Variavel para receber o nome da service
     * @var    string
     * @access protected
     * @name   $_service
     */
    protected $_service = 'DespachoInterlocutorio';

    /**
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => '\Sgdoce\Model\Entity\DespachoInterlocutorio',
        'mapping' => array(
            'sqArtefato' => 'Sgdoce\Model\Entity\Artefato',
            'sqUnidadeDestino' => 'Sgdoce\Model\Entity\VwUnidadeOrg',
            'sqUnidadeAssinatura' => 'Sgdoce\Model\Entity\VwUnidadeOrg',
            'sqPessoaAssinatura' => 'Sgdoce\Model\Entity\VwPessoa',
            'sqPessoaOperacao' => 'Sgdoce\Model\Entity\VwPessoa',
        )
    );

    /**
     * quantidade limite de caracteres na grid
     *
     * @var integer
     * */
    const T_ARTEFATO_DESPACHO_INTERLOCUTORIO_LIMIT_COMMENT_GRID = 50;

    /**
     * formato da data
     *
     * @var string
     * */
    const T_ARTEFATO_DESPACHO_INTERLOCUTORIO_DATE_TIME_PT_BR = 'dd/MM/yyyy H:i:s';

    /**
     * caminho da logo a partir da pasta public/
     *
     * @var string
     * */
    const T_ARTEFATO_DESPACHO_INTERLOCUTORIO_IMG_LOGO_PATH = '/img/marcaICMBio.png';

    /**
     * Action inicial do Crud. Normalmente, apresenta o formulário que será utilizado para listagem
     */
    public function indexAction()
    {
        $this->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();

        if (!isset($allParams['id'])) {
            $this->_redirect('/artefato/area-trabalho');
        }

        $sqArtefato = $allParams['id'];
        $this->_checkPermissaoArtefato($sqArtefato);
        $this->view->entityArtefato = $this->getService('Artefato')->find($sqArtefato);

        if (isset($allParams['back'])) {
            $this->view->backUrl = str_replace('.','/',$allParams['back']);
        }else{
            $this->view->backUrl = '/artefato/area-trabalho/index';
        }

        $this->getMessaging()->dispatchPackets();
    }

    /**
     * Método que obtém os dados para grid
     * @param \Core_Dto_Search $dtoSearch
     * @return array
     */
    public function getResultList(\Core_Dto_Search $dtoSearch)
    {
        $dtoSearch->sqArtefato = $this->_getParam('sqArtefato');
        $dtoSearch->limitComment = self::T_ARTEFATO_DESPACHO_INTERLOCUTORIO_LIMIT_COMMENT_GRID;
        $dtoSearch->hasPermission = $this->_checkPermissaoArtefato($dtoSearch->getSqArtefato());
        $dtoSearch->ultimoTramite = $this->getService('TramiteArtefato')->getLastTramite($dtoSearch->getSqArtefato());
        $dtoSearch->demandaAberta = $this->getService('Solicitacao')->getSolicitacaoAberta ($dtoSearch);

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
    }

    public function createAction()
    {
        $this->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();

        if (!isset($allParams['id'])) {
            $this->_redirect('/artefato/area-trabalho');
        }

        if (false === $this->_checkPermissaoArtefato($allParams['id'])) {
            $this->_forward('index',null,null,array('id'=>$allParams['id']));
        }

        $helper      = new Sgdoce_View_Helper_NuArtefato();
        $entArtefato = $this->getService('Artefato')->find($allParams['id']);

        parent::createAction();

        $this->view->arrCargo            = $this->getService('Cargo')->comboCargo();
        $this->view->arrFuncao           = $this->getService('Funcao')->comboFuncao();
        $this->view->sqArtefato          = $allParams['id'];
        $this->view->nuArtefato          = $helper->nuArtefato($entArtefato);
        $this->view->sqUnidadeAssinatura = \Core_Integration_Sica_User::getUserUnit();
    }

    public function detailAction()
    {
        try {
            $this->getHelper('layout')->disableLayout();
            $data = $this->_getAllParams();
            $sqDespacho = (integer) $data['id'];

            if (!$sqDespacho) {
                throw new Exception(\Core_Registry::getMessage()->translate('MN025'));
            }

            $this->view->backToModal = (boolean) $data['backToModal'];
            $this->view->data        = $this->getService()->find($sqDespacho);
        } catch (\Exception $exc) {
            $this->getMessaging()->addErrorMessage($exc->getMessage());
        }
    }

    public function printAction()
    {
        $this->_helper->layout()->disableLayout();
        $data = $this->_getAllParams();
        $sqArtefato = (integer) $data['id'];

        if (!$sqArtefato) {
            throw new Exception(\Core_Registry::getMessage()->translate('MN132'));
        }

        $entityArtefato = $this->getService('Artefato')->find($sqArtefato);

        $data = $this->getService()->findBy(
            array('sqArtefato' => $sqArtefato), array('dtDespacho' => 'DESC')
        );

        $options = array(
            'fname' => sprintf('Despacho-%d.pdf', $sqArtefato),
            'path' => APPLICATION_PATH . '/modules/artefato/views/scripts/despacho-interlocutorio/'
        );

        \Core_Doc_Factory::setFilePath($options['path']);

        $params = array(
            'data' => $data,
            'entityArtefato' => $entityArtefato,
            'dtFormatPrint' => self::T_ARTEFATO_DESPACHO_INTERLOCUTORIO_DATE_TIME_PT_BR,
        );

        \Core_Doc_Factory::download('print', $params, $options['fname']);
    }

    public function printDespachoAction()
    {

        $this->_helper->layout()->disableLayout();
        $data = $this->_getAllParams();
        $id = (integer) $data['id'];

        if (!$id) {
            throw new Exception(\Core_Registry::getMessage()->translate('MN132'));
        }


        $data = $this->getService()->findBy(
                array('sqDespachoInterlocutorio' => $id)
        );

        $args = $this->getService()->find($id);

        $sqArtefato = $args->getSqArtefato()->getSqArtefato();

//        $entityArtefato = $this->getService('VwConsultaArtefato')->findBy(
//                array('sqArtefato' => $sqArtefato)
//        );
        $entityArtefato = $this->getService('Artefato')->find($sqArtefato);

        $options = array(
            'fname' => sprintf('Despacho-%d.pdf', $id),
            'path' => APPLICATION_PATH . '/modules/artefato/views/scripts/despacho-interlocutorio/'
        );

        \Core_Doc_Factory::setFilePath($options['path']);


        \Core_Doc_Factory::download(
                'print-despacho', 
                array(
                        'data' => $data,
                        'entityArtefato' => $entityArtefato,
                        'dtFormatPrint' => self::T_ARTEFATO_DESPACHO_INTERLOCUTORIO_DATE_TIME_PT_BR,
                ), 
                $options['fname']
        );
    }

    public function editAction()
    {
        try {
            $this->_helper->layout()->disableLayout();
            $data = $this->_getAllParams();
            $sqDespacho = (integer) $data['id'];

            if (!$sqDespacho) {
                throw new Exception(\Core_Registry::getMessage()->translate('MN132'));
            }
            $this->view->backUrl = isset($data['backUrl']) ? $data['backUrl'] : '';
            $this->view->data    = $this->getService()->find($sqDespacho);
        } catch (\Exception $exc) {
            $this->getMessaging()->addErrorMessage($exc->getMessage());
        }
    }

    public function deleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $data = $this->_getAllParams();

        $sqDespacho = (integer) $data['id'];

        try {
            # delega a exclusao para superclasse
            $service = $this->getService();
            $service->preDelete($sqDespacho);
            $service->delete($sqDespacho);
            $service->finish();

            $this->getMessaging()->addSuccessMessage('MD003','User');
            $this->getMessaging()->dispatchPackets();

            $this->_helper->json(array(
                "status" => TRUE,
                "message" => \Core_Registry::getMessage()->translate('MN045')
            ));
        } catch (\Exception $exc) {
            $this->_helper->json(array(
                "status" => FALSE,
                "message" => $exc->getMessage()
            ));
        }
    }

    /**
     * Metódo que recupera a pessoa
     * @return json
     */
    public function searchPessoaUnidadeAction()
    {
        $this->_helper->layout->disableLayout();
        
        $params    = $this->_getAllParams();
        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
        $service   = $this->getService('VwPessoaFisica')->searchPessoaFisica($dtoSearch);
        
        $this->_helper->json($service);
    }

    private function _checkPermissaoArtefato($sqArtefato)
    {
        $this->view->hasPermission = $this->getService()->checkPermisionArtefato($sqArtefato);
        return $this->view->hasPermission;
    }

    public function saveAction()
    {
        try {

            //$this->_redirect = 'index/id/' . $this->getRequest()->getParam('sqArtefato');

            parent::saveAction();
        } catch (Exception $e) {
            $this->_helper->json(array(
                'status' => false,
                "message" => $e->getMessage()
            ));
        }
    }

    /**
     * recupera os dados do termo interlocutorio
     *
     * @param string $query
     * @return json
     */
    public function autoCompleteGetFromTermoAction ()
    {
        try{
            $params = $this->_getAllParams();
            $this->_helper->layout->disableLayout();

            $result = $this->getService()->find((integer) $params['sqDespacho']);

            $status = FALSE;
            $data = NULL;

            if ($result) {

                $status = !$status;
                
                $sqCargoAssinatura = ($result->getSqCargoAssinatura()) ? $result->getSqCargoAssinatura()->getSqCargo() : 0;
                $noCargoAssinatura = ($result->getSqCargoAssinatura()) ? $result->getSqCargoAssinatura()->getNoCargo() : 0;
                $sqFuncaoAssinatura= ($result->getSqFuncaoAssinatura()) ? $result->getSqFuncaoAssinatura()->getSqFuncao() : 0;
                $stCargoFuncao     = ($sqCargoAssinatura) ? 1 : 2;
                
                
                $data = array(
                    'sqDespachoInterlocutorio' => $result->getSqDespachoInterlocutorio(),
                    'sqArtefato'               => $result->getSqArtefato()->getSqArtefato(),
                    'sqUnidadeAssinatura'      => $result->getSqUnidadeAssinatura()->getSqUnidadeOrg(),
                    'sqPessoaAssinatura'       => $result->getSqPessoaAssinatura()->getSqPessoa(),
                    'sqCargoAssinatura'        => $sqCargoAssinatura,
                    'sqUnidadeDestino'         => $result->getSqUnidadeDestino()->getSqUnidadeOrg(),
                    'sqPessoaOperacao'         => $result->getSqPessoaOperacao()->getSqPessoa(),
                    'noUnidadeAssinatura'      => $result->getSqUnidadeAssinatura()->getNoUnidadeOrg(),
                    'noCargoAssinatura'        => $noCargoAssinatura,
                    'noUnidadeDestino'         => $result->getSqUnidadeDestino()->getNoUnidadeOrg(),
                    'dtDespacho'               => $result->getDtDespacho()->toString('dd/MM/yyyy'),
                    'noPessoaOperacao'         => $result->getSqPessoaOperacao()->getNoPessoa(),
                    'sqFuncaoAssinatura'       => $sqFuncaoAssinatura,
                    'stCargoFuncao'            => $stCargoFuncao
                );
            }

            $this->_helper->json(array(
                'status' => $status,
                'data' => $data
            ));

        } catch (\Exception $e) {
            $this->_helper->json(array('status' => FALSE,  'data' => NULL));
        }
    }

    protected function _factoryParamsExtrasSave($data)
    {
        // tratando parametro
        $dto = Core_Dto::factoryFromData($data, 'search');

        return array($dto);
    }
}
