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
 * Classe para Controller de Comentario
 *
 * @category Controller
 * @package  Auxiliar
 * @name     Comentario
 * @version  1.0.0
 */
class Artefato_ComentarioController extends \Core_Controller_Action_Crud
{
    /**
     * caminho da logo a partir da pasta public/
     *
     * @var string
     * */
    const T_ARTEFATO_COMENTARIO_IMG_LOGO_PATH      = '/img/marcaICMBio.png';

    /**
     * formato da data
     *
     * @var string
     * */
    const T_ARTEFATO_COMENTARIO_DATE_TIME_PT_BR    = 'dd/MM/yyyy H:mm:s';

    /**
     * quantidade limite de caracteres na grid
     *
     * @var integer
     * */
    const T_ARTEFATO_COMENTARIO_LIMIT_COMMENT_GRID = 250;

    /**
     * @var string
     */
    protected $_service = 'Comentario';

    public function indexAction()
    {
        $this->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();

        if(!isset($allParams['id'])){
            $this->_redirect('/artefato/area-trabalho');
        }

        if (isset($allParams['back'])) {
            $this->view->backUrl = str_replace('.','/',$allParams['back']);
        }

        $this->_checkPermissaoArtefato($allParams['id']);

        $this->view->entArtefato = $this->getService('Artefato')->find($allParams['id']) ;
        $this->render('grid');
    }

    public function formAction()
    {
        $this->getHelper('layout')->disableLayout();
        $data = $this->_getAllParams();

        $sqArtefato = (integer) $data['sqArtefato'];

        if (!$sqArtefato) {
            $this->getMessaging()->addAlertMessage(Core_Registry::getMessage()->translate('MN132'),'User');
            $this->getMessaging()->dispatchPackets();
            $this->_forward('index',null,null,array('id'=>$sqArtefato));
        }

        if (false === $this->_checkPermissaoArtefato($sqArtefato)) {
            $this->getMessaging()->addAlertMessage(Core_Registry::getMessage()->translate('MN156'),'User');
            $this->getMessaging()->dispatchPackets();
            $this->_forward('index',null,null,array('id'=>$sqArtefato));
        }

        $this->view->backUrl = isset($data['backUrl']) ? $data['backUrl'] : '';
        $this->view->sqArtefato = $sqArtefato;
    }

    public function updateAction ()
    {
        try {
            $this->getHelper('layout')->disableLayout();
            $data = $this->_getAllParams();
            $sqComentario = (integer) $data['sqComentario'];

            if (!$sqComentario) {
                throw new Exception(\Core_Registry::getMessage()->translate('MN132'));
            }

            $row = $this->getService()->find($sqComentario);

            # registrar os dados na view de alteracao
            $this->view->sqComentarioArtefato = $row->getSqComentarioArtefato();
            $this->view->sqArtefato           = $row->getSqArtefato()->getSqArtefato();
            $this->view->txComentario         = $row->getTxComentario();
            $this->view->dtComentario         = $row->getDtComentario()->get(Zend_Date::DATETIME_MEDIUM);

            $this->view->backUrl              = isset($data['backUrl']) ? $data['backUrl'] : '';

        } catch (\Exception $exc) {
            $this->getMessaging()->addErrorMessage($exc->getMessage());
        }
    }

    public function deleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $data = $this->_getAllParams();
        $sqComentario = (integer) $data['id'];
        $sqArtefato   = (integer) $data['sqArtefato'];

        try{

            if (false === $this->_checkPermissaoArtefato($sqArtefato)) {
                throw new Core_Exception_ServiceLayer_Verification(\Core_Registry::getMessage()->translate('MN156'));
            }

            # delega a exclusao para superclasse
            $service = $this->getService();
            $service->delete($sqComentario);
            $service->finish();

            $this->getMessaging()->addSuccessMessage('MD003','User');
            $this->getMessaging()->dispatchPackets();

            $this->_helper->json(array(
                    "status"  => TRUE,
                    "message" => ''
            ));

        } catch (\Exception $exc) {
            $this->_helper->json(array(
                  "status"  => FALSE,
                  "message" => $exc->getMessage()
            ));
        }
    }

    public function detailAction ()
    {
        try {
            $this->getHelper('layout')->disableLayout();
            $data = $this->_getAllParams();
            $sqComentario = (integer) $data['sqComentario'];

            if (!$sqComentario) {
                throw new Exception(\Core_Registry::getMessage()->translate('MN132'));
            }
            
            $this->view->backToModal = (boolean) $data['backToModal'];

            $row = $this->getService()->find($sqComentario);

            # registrar os dados na view de alteracao
            $this->view->sqComentarioArtefato = $row->getSqComentarioArtefato();
            $this->view->dtArtefato           = $row->getDtComentario()->toString(self::T_ARTEFATO_COMENTARIO_DATE_TIME_PT_BR);
            $this->view->noPessoa             = $row->getSqPessoa()->getNoPessoa();
            $this->view->noUnidadeOrg         = $row->getSqUnidadeOrg()->getNoUnidadeOrg();
            $this->view->sqArtefato           = $row->getSqArtefato()->getSqArtefato();
            $this->view->txComentario         = $row->getTxComentario();

        } catch (\Exception $exc) {
            $this->getMessaging()->addErrorMessage($exc->getMessage());
        }
    }

    public function listAction()
    {
        $this->view->limitComment = self::T_ARTEFATO_COMENTARIO_LIMIT_COMMENT_GRID;
        $this->view->hasPermission = $this->_checkPermissaoArtefato($this->getRequest()->getParam('sqArtefato'));
        $this->view->demandaAberta = $this->getService('Solicitacao')
                                          ->getSolicitacaoAberta (\Core_Dto::factoryFromData($this->getRequest()->getParams(), 'search'));
        parent::listAction();
    }

    public function printAction ()
    {
        $this->_helper->layout()->disableLayout();
        $data       = $this->_getAllParams();
        $sqArtefato = (integer) $data['sqArtefato'];

        if (! $sqArtefato) {
            throw new Exception(\Core_Registry::getMessage()->translate('MN132'));
        }

        $digitalOrNumProcesso = NULL;
        $artefato = $this->getService('Artefato')->find($sqArtefato);

        if ($artefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato() == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
            $digitalOrNumProcesso = $this->getService('Processo')->formataProcessoAmbitoFederal($artefato);
        } else {
            $digitalOrNumProcesso = $artefato->getNuDigital()->getNuEtiqueta();
            $digitalOrNumProcesso = (strlen($digitalOrNumProcesso) < 7) ? str_pad($digitalOrNumProcesso, 7,'0',STR_PAD_LEFT) : $digitalOrNumProcesso;
        }

        $data = $this->getService()->findBy(
            array('sqArtefato' => $sqArtefato),
            array('dtComentario' => 'DESC')
        );

        $options  = array(
           'fname' => sprintf('Comentario-%d.pdf', $sqArtefato),
           'path'  => APPLICATION_PATH . '/modules/artefato/views/scripts/comentario'
        );

        $logo = current(explode('application', __FILE__))
              . 'public' . DIRECTORY_SEPARATOR
              . ltrim(self::T_ARTEFATO_COMENTARIO_IMG_LOGO_PATH, DIRECTORY_SEPARATOR);

        \Core_Doc_Factory::setFilePath($options['path']);

        \Core_Doc_Factory::download(
             'print', /* .phtml */
              array(
                   'data'          => $data,
                   'logo'          => $logo,
                   'nuArtefato'    => $digitalOrNumProcesso,
                   'dtFormatPrint' => self::T_ARTEFATO_COMENTARIO_DATE_TIME_PT_BR,
              ),
              $options['fname']
        );
    }

    /**
     * Solicita à service a execução da pesquisa
     * Este metodo é infocado por delegação pela superclass
     *
     * @return #descorbir
     * */
    public function getResultList ($params)
    {
        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
        return $this->getService()->listGrid($dtoSearch);
    }

    /**
     * @return array
     */
    public function getConfigList()
    {
//        $array = array('columns' =>
//            array(
//                array('alias' => 'coma.dtComentario'),
//                array('alias' => 'coma.txComentario'),
//                array('alias' => 'coma.sqPessoa'    ),
//                array('alias' => 'coma.sqUnidade'   ),
//                array('alias' => 'coma.dtComentario'),
//                array('alias' => 'uta.dtTramite'    ),
//            )
//        );
//
//        return $array;
        return array();
    }

    public function registerAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $data   = $this->_getAllParams();

        $entity = Core_Dto::factoryFromData(
                array(), // se informar os dados enviados diretamente pelo post, gerará um
                         // um erro no carregamento de ArtefatoEntity
                'entity',
                array('entity' => '\Sgdoce\Model\Entity\ComentarioArtefato')
        );

        $entity->setTxComentario($data['txComentario']);

        /*
         * quando for salvar nao havera sqComentarioArtefato, mas obrigatoriamente,
         * deverá constar o sqArtefato, na
         * */
        if (isset($data['sqArtefato'])) {
            $entity->setSqArtefato( $this->getService('Artefato')->find($data['sqArtefato']) );
        }

        if (isset($data['sqComentarioArtefato'])) {
            $entity->setSqComentarioArtefato($data['sqComentarioArtefato']);
            $entity->setDtComentario(new \Zend_Date($data['dtComentario']));
        }

        $back = '';
        if (isset($data['backUrl']) && $data['backUrl']) {
            $back = '/back/' . $data['backUrl'];
        }

        try{
            $this->getService()->register($entity);

            $this->_redirect('artefato/comentario/index/id/' . $data['sqArtefato'] . $back);
        } catch (\Exception $exc) {

            $this->getMessaging()->addErrorMessage($exc->getMessage(),'User');
            $this->getMessaging()->dispatchPackets();
            $this->_redirect('artefato/comentario/index/id/' . $data['sqArtefato'] . $back);
        }
    }

    private function _checkPermissaoArtefato($sqArtefato)
    {
        $this->view->hasPermission = $this->getService()->checkPermissaoArtefato($sqArtefato);
        return $this->view->hasPermission;
    }
}