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
 * Classe para Controller de Vinculo
 *
 * @package    Artefato
 * @category   Controller
 * @name       Vinculo
 * @version    0.0.1
 * */
class Artefato_VolumeController extends \Core_Controller_Action_CrudDto
{

    /**
     * @var string
     */
    protected $_service = 'ProcessoVolume';


    /**
     * @var string
     */
    protected $_redirect = array(
        'module'     => 'artefato',
        'controller' => 'area-trabalho',
        'action'     => 'index'
    );

    /**
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => 'Sgdoce\Model\Entity\ProcessoVolume',
        'mapping' => array(
            'sqArtefato'                 => 'Sgdoce\Model\Entity\Artefato',
            'sqPessoaAssinaturaAbertura' => array('sqPessoa'     => 'Sgdoce\Model\Entity\VwPessoa'),
            'sqCargoAssinaturaAbertura'  => array('sqCargo'      => 'Sgdoce\Model\Entity\VwCargo'),
            'sqFuncaoAssinaturaAbertura' => array('sqFuncao'     => 'Sgdoce\Model\Entity\VwFuncao'),
            'sqPessoaAbertura'           => array('sqPessoa'     => 'Sgdoce\Model\Entity\VwPessoa'),
            'sqUnidadeOrgAbertura'       => array('sqUnidadeOrg' => 'Sgdoce\Model\Entity\VwUnidadeOrg')
    ));

    /**
     * quantidade limite de caracteres na grid
     *
     * @var integer
     * */
    const T_ARTEFATO_VOLUME_LIMIT_COMMENT_GRID = 250;

    /**
     * @return void
     */
    public function indexAction()
    {
        $this->view->pageTitle = "Abrir / encerrar Volume.";
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_Crud::combo()
     */
    public function combos()
    {
        $this->view->arrCargos = $this->getService('VwCargo')->comboCargo();
        $this->view->arrFuncao = $this->getService('Funcao')->comboFuncao();
    }

    /**
     * @return type
     */
    public function formAction()
    {
        $this->_helper->layout->disableLayout();
        $this->view->invalido = false;
        $this->combos();
        $this->view->pageTitle = "Abrir volume.";
        $sqArtefato = $this->getRequest()->getParam('id', 0);

        if( $sqArtefato ) {
            $artefato = $this->getService()->getArtefatoProcesso($sqArtefato);
            if($artefato){
                if( $this->getService()->hasVolumeAberto($sqArtefato) ) {
                    $this->view->formTitle = 'Encerrar Volume';
                    $this->view->btnAction = 'Encerrar Volume';

                    if( !$this->getService()->validaEncerramentoVolume($sqArtefato) ) {
                        return $this->_redirectActionDefault('index');
                    }

                    $volume = $this->getService()->getLastVolumeAberto($sqArtefato);
                    $this->view->volume = $volume;
                    $this->view->form = 'encerrar.phtml';
                } else {
                    $this->view->formTitle = 'Abrir Volume';
                    $this->view->btnAction = 'Abrir Volume';
                    $volume = $this->getService()->getLastVolumeEncerrado($sqArtefato);

                    $this->view->nuVolume = 1;
                    $this->view->nuFolhaInicial = 1;
                    $this->view->artefato = $artefato;

                    if( $volume ) {
                        $this->view->nuVolume = $volume->getNuVolume() + 1;
                        $this->view->nuFolhaInicial = $volume->getNuFolhaFinal() + 1;
                        $this->view->dtAberturaAnterior = $volume->getDtEncerramento();
                    }

                    $this->view->form = 'abrir.phtml';
                }
            } else {
                $this->view->invalido = true;
            }
        } else {
            $this->render('adicionar');
        }

    }

    /**
     * @return void
     */
    public function abrirAction()
    {
        $this->combos();
        $this->view->pageTitle = "Abrir volume.";
        $sqArtefato = $this->getRequest()->getParam('id', 0);

        if( $this->getService()->validaAberturaVolume($sqArtefato) ) {
            return $this->_redirectActionDefault('index');
        }

        $artefato = $this->getService('Artefato')->find($sqArtefato);
        $volume = $this->getService()->getLastVolumeEncerrado($sqArtefato);

        $this->view->nuVolume = 1;
        $this->view->nuFolhaInicial = 1;
        $this->view->artefato = $artefato;

        if( $volume ) {
            $this->view->nuVolume = $volume->getNuVolume() + 1;
            $this->view->nuFolhaInicial = $volume->getNuFolhaFinal() + 1;
            $this->view->dtAberturaAnterior = $volume->getDtEncerramento();
        } else {
            return $this->_redirectActionDefault('index');
        }
    }

    /**
     * @return void
     */
    public function encerrarAction()
    {
        $this->combos();
        $this->view->pageTitle = "Encerrar Volume.";
        $sqArtefato = $this->getRequest()->getParam('id', false);

        if( !$this->getService()->validaEncerramentoVolume($sqArtefato) ) {
            return $this->_redirectActionDefault('index');
        }

        if(!$sqArtefato) {
            return $this->_redirectActionDefault('index');
        }

        $volume = $this->getService()->getLastVolumeAberto($sqArtefato);
        $this->view->volume = $volume;
    }

    /**
     * @return type
     * @throws RuntimeException
     */
    public function saveAction()
    {
        if (!$this->_request->isPost()) {
            throw new RuntimeException('A requisição deve ser POST');
        }
        $isValid = true;
        if ( $this->getRequest()->getParam('stProcessoVolume', false ) ) {

            $this->getRequest()->setPost('sqPessoaAbertura', \Core_Integration_Sica_User::getPersonId());
            $this->getRequest()->setPost('sqUnidadeOrgAbertura', (integer)\Core_Integration_Sica_User::getUserUnit());

        } else {
            $sqArtefato = (integer)$this->getRequest()->getParam('id');
            $volume = $this->getService()->getLastVolumeAberto($sqArtefato);
            $params = $this->_getAllParams();
            $configs = \Core_Registry::get('configs');

            if( ($params['nuFolhaFinal'] - $params['nuFolhaInicial']) <= (integer)$configs['volume']['maxPagePerVolume'] ){
                $sqPessoaAbertura = ($volume->getSqPessoaAbertura()) ?
                                        $volume->getSqPessoaAbertura()->getSqPessoa() :
                                        0;

                $sqUnidadeOrgAbertura = ($volume->getSqUnidadeOrgAbertura()) ?
                                            $volume->getSqUnidadeOrgAbertura()->getSqUnidadeOrg() :
                                            0;

                $this->getRequest()->setPost('sqVolume', $volume->getSqVolume());
                $this->getRequest()->setPost('sqPessoaAbertura', $sqPessoaAbertura);
                $this->getRequest()->setPost('sqUnidadeOrgAbertura', $sqUnidadeOrgAbertura);

                if( $volume->getSqPessoaAssinaturaAbertura() && $volume->getSqCargoAssinaturaAbertura() ) {
                    $this->getRequest()->setPost('sqPessoaAssinaturaAbertura', $volume->getSqPessoaAssinaturaAbertura()->getSqPessoa());
                    $this->getRequest()->setPost('sqCargoAssinaturaAbertura',  $volume->getSqCargoAssinaturaAbertura()->getSqCargo());
                }

                if( $volume->getSqPessoaAssinaturaAbertura() && $volume->getSqFuncaoAssinaturaAbertura() ) {
                    $this->getRequest()->setPost('sqPessoaAssinaturaAbertura', $volume->getSqPessoaAssinaturaAbertura()->getSqPessoa());
                    $this->getRequest()->setPost('sqFuncaoAssinaturaAbertura', $volume->getSqFuncaoAssinaturaAbertura()->getSqFuncao());
                }

                $this->getRequest()->setPost('sqPessoaEncerramento', \Core_Integration_Sica_User::getPersonId());
                $this->getRequest()->setPost('sqUnidadeOrgEncerramento', (integer)\Core_Integration_Sica_User::getUserUnit());

                $this->_optionsDtoEntity['mapping'] = array_merge($this->_optionsDtoEntity['mapping'], array(
                    'sqPessoaAssinaturaEncerramento' => array('sqPessoa'     => 'Sgdoce\Model\Entity\VwPessoa'),
                    'sqCargoAssinaturaEncerramento'  => array('sqCargo'      => 'Sgdoce\Model\Entity\VwCargo'),
                    'sqFuncaoAssinaturaEncerramento' => array('sqFuncao'     => 'Sgdoce\Model\Entity\VwFuncao'),
                    'sqPessoaEncerramento'           => array('sqPessoa'     => 'Sgdoce\Model\Entity\VwPessoa'),
                    'sqUnidadeOrgEncerramento'       => array('sqUnidadeOrg' => 'Sgdoce\Model\Entity\VwUnidadeOrg')
                ));
            } else {
                $isValid = false;
                $this->getMessaging()->addErrorMessage('Volume não pode ter mais de 200 páginas.', 'User');
            }
        }

        if( $isValid ) {
            $entity = $this->_save();
            $this->getService()->finish();
            $this->_addMessageSave();
            return $this->_redirect("/artefato/volume/termo/id/" . $entity->getSqVolume());
        }
        $this->getMessaging()->dispatchPackets();
        $sqTipoArtefato = \Core_Configuration::getSgdoceTipoArtefatoProcesso();
        return $this->_redirect("/artefato/area-trabalho/index/tipoArtefato/{$sqTipoArtefato}/caixa/minhaCaixa");
    }

    /**
     * @return void
     */
    public function inputsAction()
    {
        $params = $this->getRequest()->getParams();

    }
    /**
     * Termo de Abertura / Encerramento de Volume.
     *
     * @return void
     */
    public function termoAction()
    {
        $sqVolume   = $this->_getParam('id', false);
        $stAbertura = $this->_getParam('abertura', false);

        if( !$sqVolume ) { $this->_redirect(); }

        $objEntity = $this->getService()->find($sqVolume);

        $termo = null;

        if( !$stAbertura && $objEntity->getDtEncerramento() ) {
            $termo = 'termo-encerramento';
        } else {
            $termo = 'termo-abertura';
        }

        $sufix = str_shuffle($sqVolume.time());
        $fname = sprintf($termo . '-%d.pdf', $sufix);
        $path  = APPLICATION_PATH . '/modules/artefato/views/scripts/volume';
        $noUnidadeOrg = $this->_helper->changeCase->toupper(\Core_Integration_Sica_User::getUserUnitName());

        $params = array( 'objVolume' => $objEntity, 'noUnidadeOrg' => $noUnidadeOrg );

        return $this->_helper
                    ->termo
                    ->setParams($params)
                    ->setDateFormatPrint("dd 'dia(s) do mês de ' MMMM 'de' yyyy")
                    ->gerar($termo, $fname, $path);
    }

    /**
     * @return void
     */
    public function gridAction()
    {
        $this->getHelper('layout')->disableLayout();
        $allParams = $this->_getAllParams();

        if (!isset($allParams['id'])) {
            $this->_redirect('/artefato/area-trabalho');
        }

        $this->view->entityArtefato = $this->getService('Artefato')->find($allParams['id']);

        if (isset($allParams['back'])) {
            $this->view->backUrl = str_replace('.','/',$allParams['back']);
        }else{
            $this->view->backUrl = '/artefato/area-trabalho/index';
        }
        $this->getMessaging()->dispatchPackets();
    }

    /**
     * Solicita à service a execução da pesquisa
     * Este metodo é infocado por delegação pela superclass
     *
     * @return #descorbir
     * */
    public function getResultList (\Core_Dto_Search $dtoSearch)
    {
        $sqArtefato              = $this->_getParam('sqArtefato');
        $dtoSearch->sqArtefato   = $sqArtefato;
        $dtoSearch->limitComment = self::T_ARTEFATO_VOLUME_LIMIT_COMMENT_GRID;

        return $this->getService()->listGrid($dtoSearch);
    }

    /**
     * @return array
     */
    public function getConfigList()
    {
        return array();
    }

    public function listAction()
    {

        $dto = Core_Dto::factoryFromData($this->_getAllParams(), 'search');

        $sqArtefato = $dto->getSqArtefato();

        $this->view->lastVolume         = $this->getService()->getLastVolume($sqArtefato);
        $this->view->hasTramiteEfetivo  = $this->getService('Artefato')->hasTramiteEfetivo($dto);

        $this->view->limitComment       = self::T_ARTEFATO_VOLUME_LIMIT_COMMENT_GRID;
        $this->view->hasPermission      = $this->getService()->checkPermisionArtefato($sqArtefato);
        $this->view->notTheOnlyVolume = $this->getService()->checkIfVolumeCanBeDeleted($sqArtefato);

        $this->view->hasDemandaVolumeDeProcesso = $this->getServiceLocator()
                                                       ->getService('Solicitacao')
                                                       ->hasDemandaAbertaByAssuntoPessoaResponsavel(
                                                            \Core_Dto::factoryFromData(
                                                            array(
                                                                'sqArtefato'               => $sqArtefato,
                                                                'sqTipoAssuntoSolicitacao' => \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoVolumeDeProcesso()
                                                            ),
                                                            'search'));

        parent::listAction();
        $this->getMessaging()->dispatchPackets();
    }

    public function editAction ()
    {
        try
        {
            $this->getHelper('layout')->disableLayout();

            $data     = $this->_getAllParams();
            $sqVolume = (integer) $data['id'];

            if (!$sqVolume) {
                throw new Exception(\Core_Registry::getMessage()->translate('MN132'));
            }

            $row = $this->getService()->find($sqVolume);

            $this->view->sqArtefato     = $row->getSqArtefato()->getSqArtefato();
            $this->view->nuArtefato     = $row->getSqArtefato()->getNuArtefato();
            $this->view->sqVolume       = $row->getSqVolume();
            $this->view->nuVolume       = $row->getNuVolume();
            $this->view->nuFolhaInicial = $row->getNuFolhaInicial();
            $this->view->dtAbertura     = $row->getDtAbertura()->toString('dd/MM/yyyy');

            $this->view->noPessoaAssinaturaAbertura = $this->view->sqPessoaAssinaturaAbertura = NULL;
            if ($row->getSqPessoaAssinaturaAbertura()) {
                $dtoPessAssAbertura = \Core_Dto::factoryFromData(
                        array('sqPessoaCorporativo' => $row->getSqPessoaAssinaturaAbertura()->getSqPessoa()),
                        'search'
                );

                $nuCpfAbertura  = \Zend_Filter::filterStatic(
                        $this->getServiceLocator()->getService('VwPessoa')->returnCpfCnpjPassaporte($dtoPessAssAbertura),
                        'MaskNumber',
                        array('cpf'),
                        array('Core_Filter')
                );

                $this->view->noPessoaAssinaturaAbertura = $nuCpfAbertura.' - '.$row->getSqPessoaAssinaturaAbertura()->getNoPessoa();
                $this->view->sqPessoaAssinaturaAbertura = $row->getSqPessoaAssinaturaAbertura()->getSqPessoa();
            }

            $this->view->sqPessoaAbertura           = $row->getSqPessoaAbertura()->getSqPessoa();
            $this->view->sqUnidadeOrgAbertura       = $row->getSqUnidadeOrgAbertura()->getSqUnidadeOrg();

            $this->view->sqCargoAssinaturaAbertura  = $this->view->sqFuncaoAssinaturaAbertura = NULL;
            if ($row->getSqCargoAssinaturaAbertura()) {
                $this->view->sqCargoAssinaturaAbertura  = $row->getSqCargoAssinaturaAbertura()->getSqCargo();
            }
            if ($row->getSqFuncaoAssinaturaAbertura()) {
                $this->view->sqFuncaoAssinaturaAbertura = $row->getSqFuncaoAssinaturaAbertura()->getSqFuncao();
            }

            $this->view->isVolumeAberto = TRUE;
            if ($row->getNuFolhaFinal()) {
                $this->view->nuFolhaFinal   = $row->getNuFolhaFinal();
                $this->view->isVolumeAberto = FALSE;
                $this->view->dtEncerramento = ($row->getDtEncerramento()) ? $row->getDtEncerramento()->toString('dd/MM/yyyy') : $row->getDtEncerramento();

                $this->view->noPessoaAssinaturaEncerramento = $this->view->sqPessoaAssinaturaEncerramento = NULL;
                if ($row->getSqPessoaAssinaturaEncerramento()) {
                    $dtoPessAssEncerramento = \Core_Dto::factoryFromData(
                            array('sqPessoaCorporativo' => $row->getSqPessoaAssinaturaEncerramento()->getSqPessoa()),
                            'search'
                    );

                    $nuCpfEncerramento  = \Zend_Filter::filterStatic(
                            $this->getServiceLocator()->getService('VwPessoa')->returnCpfCnpjPassaporte($dtoPessAssEncerramento),
                            'MaskNumber',
                            array('cpf'),
                            array('Core_Filter')
                    );

                    $this->view->noPessoaAssinaturaEncerramento = $nuCpfEncerramento.' - '.$row->getSqPessoaAssinaturaEncerramento()->getNoPessoa();
                    $this->view->sqPessoaAssinaturaEncerramento = $row->getSqPessoaAssinaturaEncerramento()->getSqPessoa();
                }
                $this->view->sqPessoaEncerramento           = $row->getSqPessoaEncerramento()->getSqPessoa();
                $this->view->sqUnidadeOrgEncerramento       = $row->getSqUnidadeOrgEncerramento()->getSqUnidadeOrg();

                $this->view->sqCargoAssinaturaEncerramento = $this->view->sqFuncaoAssinaturaEncerramento = NULL;
                if ($row->getSqCargoAssinaturaEncerramento()) {
                    $this->view->sqCargoAssinaturaEncerramento = $row->getSqCargoAssinaturaEncerramento()->getSqCargo();
                }
                if ($row->getSqFuncaoAssinaturaEncerramento()) {
                    $this->view->sqFuncaoAssinaturaEncerramento = $row->getSqFuncaoAssinaturaEncerramento()->getSqFuncao();
                }
            }

            $datasMaxMin = $this->getService()->getDatasMaxMin($sqVolume);

            $this->view->dataMin = $datasMaxMin['dtEncerramento'];
            $this->view->dataMax = $datasMaxMin['dtAbertura'];

            $this->combos();

        } catch (\Exception $exc) {
            $this->getMessaging()->addErrorMessage($exc->getMessage());
        }
    }

    public function updateAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        try {
            if (!$this->_request->isPost()) {
                throw new RuntimeException('A requisição deve ser POST');
            }

            $data = $this->_getAllParams();

            $this->getService()->update($data);
            $this->getMessaging()->addSuccessMessage('MD002');
            $this->getMessaging()->dispatchPackets();

        } catch (\Exception $exc) {
            $this->_helper->json(array(
                    "status" => FALSE,
                    "message" => $exc->getMessage()
            ));
        }
    }

    public function deleteVolumeAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        try {
            $data     = $this->_getAllParams();
            $sqVolume = (integer) $data['id'];

            if (!$sqVolume) {
                throw new Exception(\Core_Registry::getMessage()->translate('MN132'));
            }

            # delega a exclusao para superclasse
            $service = $this->getService();
            $service->preDelete($sqVolume);
            $service->delete($sqVolume);
            $service->finish();

            $this->getMessaging()->addSuccessMessage('MD003');
            $this->getMessaging()->dispatchPackets();

            $this->_helper->json(array(
                    'status'  => TRUE,
                    'message' => \Core_Registry::getMessage()->translate('MN045')
            ));

        } catch (\Exception $exc) {
            $this->_helper->json(array(
                  'status'  => FALSE,
                  'message' => $exc->getMessage()
            ));
        }
    }

    public function detailAction()
    {
        try {
            $this->getHelper('layout')->disableLayout();

            $data     = $this->_getAllParams();
            $sqVolume = (integer) $data['id'];

            if (!$sqVolume) {
                throw new Exception(\Core_Registry::getMessage()->translate('MN025'));
            }

            $row = $this->getService()->find($sqVolume);

            $this->view->sqArtefato     = $row->getSqArtefato()->getSqArtefato();
            $this->view->nuArtefato     = $row->getSqArtefato()->getNuArtefato();
            $this->view->nuVolume       = $row->getNuVolume();
            $this->view->nuFolhaInicial = $row->getNuFolhaInicial();
            $this->view->nuFolhaFinal   = $row->getNuFolhaFinal();
            $this->view->dtAbertura     = $row->getDtAbertura();
            $this->view->dtEncerramento = $row->getDtEncerramento();

        } catch (\Exception $exc) {
            $this->getMessaging()->addErrorMessage($exc->getMessage());
        }
    }
}