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
 * @name     Imagem
 * @version  1.0.0
 */
class Artefato_ImagemController extends \Core_Controller_Action_CrudDto
{

    /**
     * @var string
     */
    protected $_service = 'ArtefatoImagem';

    /**
     * Metodo que trata de upload de documentos.
     * Caso o documento seja de migração redireciona para indexMigrationAction
     */
    public function indexAction ()
    {
        $params = $this->_viewSteps();
        if (isset($params['back'])) {
            $this->view->backUrl = str_replace('.', '/', $params['back']);
        }

        $dtoSearch = \Core_Dto::factoryFromData(array('sqArtefato' => $params['id']), 'search');
        $isMigracao = $this->getService("Artefato")->isMigracao($dtoSearch);

        //redireciona para action que trata de migração
        if ($isMigracao) {
            $params = $this->getRequest()->getParams();
            unset($params['module'],$params['controller'],$params['action']);

            $url = '/artefato/imagem/index-migration';
            foreach ($params as $param=>$value) {
                $url .= '/'.$param.'/'.$value;
            }

            $this->_redirect($url);
        }

        $this->view->canAlterImage = 0;
        $this->view->canOverwrite = false;
        $this->view->canUpload =
            !$this->view->hasImage &&
            $this->getService("Artefato")->inMyDashboard($params['id']) &&
            $this->getService()->canUpload($params['id']);


        $isAllowedAlter = in_array(\Core_Integration_Sica_User::getUserProfile(), $this->getService()->getUsersAllowedAlterImage());
        $inMyDashboard  = $this->getService("Artefato")->inMyDashboard($params['id']);

        if( $isAllowedAlter && $inMyDashboard ) {
            $this->view->canUpload      = TRUE;
            $this->view->canOverwrite   = TRUE;
            $this->view->hasImage       = FALSE;
            $this->view->canAlterImage  = 1;
        }

        if ( $this->view->hasImage ) {
            $this->getMessaging()->addInfoMessage('Este artefato já possui imagem.', 'User');
            $this->_redirect( $this->view->backUrl?: 'artefato/area-trabalho/index' );
        }
    }

    /**
     * metodo que trata o upload dos artefatos de migração
     */
    public function indexMigrationAction()
    {
        $params = $this->_viewSteps();
        if (isset($params['back'])) {
            $this->view->backUrl = str_replace('.', '/', $params['back']);
        }

        $dtoSearch = \Core_Dto::factoryFromData(array('sqArtefato' => $params['id']), 'search');

        $isImagemInconsistent = $this->getService("Artefato")->isInconsistent($dtoSearch, true);

        if ( $this->view->hasImage && !$isImagemInconsistent ) {
            $this->_redirect( $this->view->backUrl?: 'artefato/area-trabalho/index' );
        }

        $this->view->canUpload =
            !$this->view->hasImage &&
                $this->getService("Artefato")->inMyDashboard($params['id']) &&
                    $this->getService()->canUpload($params['id']);

        if( $isImagemInconsistent ) {

            if($this->getService()->hasArtefatoImagemData($dtoSearch) ) {
                if( !$this->view->hasImage ) {
                    $this->view->canUpload = true;
                    $this->view->canOverwrite = false;
                } else if( $isImagemInconsistent ) {
                    $this->view->hasImage = false;
                    $this->view->canUpload = true;
                    $this->view->canOverwrite = true;
                }
            } else {
                $listSolicitacao = $this->getService()->getSolicitacaoMigracaoImagem($dtoSearch);
                if (!count($listSolicitacao)) {
                    $dtoSolicitacao = \Core_Dto::factoryFromData(array(
                        'sqPessoa' => \Core_Integration_Sica_User::getPersonId(),
                        'sqUnidadeOrg' => \Core_Integration_Sica_User::getUserUnit(),
                        'sqArtefato' => $params['id']
                    ), 'search');

                    $this->getService('VinculoMigracao')->addSolicitacaoMigracao($dtoSolicitacao);
                    $this->getMessaging()->addAlertMessage('A(s) imagem(ns) deste artefato ainda não foi(ram) processada(s), aguarde.', 'User');
                    $this->_redirect($this->view->backUrl? : 'artefato/area-trabalho/index' );
                } else {
                    $configs       = \Core_Registry::get('configs');
                    $qtdeTentativa = $configs['migration']['qtdeTentativa'];


                    $entSolicitacao = current($listSolicitacao);
                    if ($entSolicitacao->getStProcessado() ||
                            (!$entSolicitacao->getStProcessado() && ($entSolicitacao->getInTentativa() == $qtdeTentativa))) {
                        if (!$this->view->hasImage) {
                            $this->view->canUpload = true;
                            $this->view->canOverwrite = false;
                        } else {
                            $this->getMessaging()->addInfoMessage('Imagem deste artefato foi processada com sucesso, clique no visualizar imagem.', 'User');
                            $this->_redirect($this->view->backUrl? : 'artefato/area-trabalho/index' );
                        }
                    } else {
                        $this->getMessaging()->addAlertMessage('A imagem deste artefato ainda não foi processada, aguarde.', 'User');
                        $this->_redirect($this->view->backUrl? : 'artefato/area-trabalho/index' );
                    }
                }
            }
        }
        $this->render('index');
    }

    /**
     * Método para fazer alteração de imagem.
     * Obs.: somente SGI e SDOC possuem permissão
     */
    public function updateAction()
    {
        $params = $this->_getAllParams();

        if (isset($params['back'])) {
            $this->view->backUrl = str_replace('.', '/', $params['back']);
        }

        $this->view->sqArtefato = $params['id'];
        $this->view->canAlterImage = 0;
    }

    /**
     * Ação para visualização de imagem
     * Caso documento seda de migração redireciona para viewMigrationAction
     *
     * @return void
     */
    public function viewAction ()
    {
        $this->getHelper('layout')->setLayout('modal');

        $sqArtefato = $this->getRequest()->getParam('id');
        // Regras para artefatos de migração.
        $dtoSearchPai = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');

        $isMigracao = $this->getService("Artefato")->isMigracao($dtoSearchPai);


        //Se for migração redireciona para demais tratamentos
        if ($isMigracao) {
            $params = $this->getRequest()->getParams();
            unset($params['module'],$params['controller'],$params['action']);

            $url = '/artefato/imagem/view-migration';
            foreach ($params as $param=>$value) {
                $url .= '/'.$param.'/'.$value;
            }

            $this->_redirect($url);
        }

        $params = $this->_viewSteps();


        $this->view->canOverwrite = false;

        if (count($this->view->treeviewData) > 0 && !$this->view->hasImage) {
            $firstItem = current($this->view->treeviewData);
            $this->view->hasImage = (count($firstItem['filhos']) > 0);
        }

        if (!$this->view->treeviewData) {
            $this->view->hasDuplicity = true;
        }

        $this->getService('VinculoMigracao')->verificaImagemArvore($this->view->treeviewData);

        $isAllowedAlter = in_array(\Core_Integration_Sica_User::getUserProfile(), $this->getService()->getUsersAllowedAlterImage());
        $inMyDashboard  = $this->getService("Artefato")->inMyDashboard($dtoSearchPai->getSqArtefato());

        $entArtefato = $this->getService("Artefato")->find($params['id']);

        $this->view->canAlterImage = ( $isAllowedAlter && $inMyDashboard );
        $this->view->sqTipoArtefato = $entArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();
    }

    /**
     * Ação para visualização de imagem de documentos migrados
     */
    public function viewMigrationAction()
    {

        $this->getHelper('layout')->setLayout('modal');

        $sqArtefato = $this->getRequest()->getParam('id');
        // Regras para artefatos de migração.
        $dtoSearchPai = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');

        $params = $this->_viewSteps();

        $entArtefato = $this->getService("Artefato")->find($params['id']);

        if ($entArtefato->isProcesso() &&
                $this->getService("Artefato")->isMigracao($dtoSearchPai) &&
                    $this->getService("Artefato")->isInconsistent($dtoSearchPai)) {
            $firstItem = current($this->view->treeviewData);
            $primeiraPeca = current($firstItem['filhos']);

            $dtoSearch = \Core_Dto::factoryFromData(array('sqArtefato' => $primeiraPeca['sqArtefatoFilho']), 'search');
            $hasImagem = $this->getService()->hasArtefatoImagemAtiva($dtoSearch);

            if (!$hasImagem) {
                $this->view->entArtefato = $entArtefato;

                //recupera a 1ª Peça
                $entPrimeiraPeca = $this->getService('ArtefatoVinculo')->findOneBy(array(
                        'sqArtefatoPai' => $entArtefato->getSqArtefato(),
                        'sqTipoVinculoArtefato' => \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao()
                    )
                );

                //se não tem 1ª Peça não faz nada
                if (! $entPrimeiraPeca) {
                    $this->render('fail-image-not-first-piece');
                } else {
                    $listSolicitacao = $this->getService()->getSolicitacaoMigracaoImagem($dtoSearch);
                    if ($listSolicitacao) {
                        $configs = \Core_Registry::get('configs');
                        $qtdeTentativa = $configs['migration']['qtdeTentativa'];

                        $entSolicitacao = current($listSolicitacao);
                        if (!$entSolicitacao->getStProcessado() && ($entSolicitacao->getInTentativa() == $qtdeTentativa)) {
                            $this->render('fail-image-not-process');
                        } else {
                            $this->render('fail-image-processo');
                        }
                    } else {
                        $this->render('fail-image-processo');
                    }
                }
            }
        }

        $this->view->canOverwrite = false;

        if (count($this->view->treeviewData) > 0 && !$this->view->hasImage) {
            $firstItem = current($this->view->treeviewData);
            $this->view->hasImage = (count($firstItem['filhos']) > 0);
        }

        if (!$this->view->treeviewData) {
            $this->view->hasDuplicity = true;
        }

        $this->getService('VinculoMigracao')->verificaImagemArvore($this->view->treeviewData);

        // Verifica se item é de migração e se tem imagem, se não tiver, adicionar uma solicitação de migração de imagem
        if ( (isset($params['view']) && $params['view'] == 'migracao')) {

            if ($this->getService("Artefato")->isInconsistent($dtoSearchPai, true)) {
                $this->view->canOverwrite = true;
            }
            $this->view->id = $params['id'];
            $this->render('view-migracao');
        }else{
            $this->render('view');
        }

        $isAllowedAlter = in_array(\Core_Integration_Sica_User::getUserProfile(), $this->getService()->getUsersAllowedAlterImage());
        $inMyDashboard  = $this->getService("Artefato")->inMyDashboard($dtoSearchPai->getSqArtefato());

        $this->view->canAlterImage = ( $isAllowedAlter && $inMyDashboard );
        $this->view->sqTipoArtefato = $entArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();
    }

    /**
     * @return void
     */
    public function viewerAction ()
    {
        $this->getHelper('layout')->disableLayout();
        $this->_viewSteps();
    }

    /**
     * @return void
     */
    public function pdfAction ()
    {
        $filename = '';

        $tmp = $this->_getParam('tmp', '');
        $id = $this->_getParam('id', '');

        if (!empty($tmp)) {
            $mufService = $this->getService('MoveFileUpload');
            $pathTemporary = $mufService->getTemporaryPath();
            $filename = $pathTemporary . $tmp;
        } elseif (empty($id)) {
            throw new \Exception("Artefato não encontrado");
        } else {
            if (!$this->getService()->hasImage($id)) {
                throw new \Exception("Imagem do artefato não encontrada");
            }
            $filename = $this->getService()->getImagePath($id);
        }

        $url = $this->getService()->getLinkedTemporaryURL($filename);

        $this->_redirect($url);
    }

    /**
     * @return void
     */
    public function saveImageAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $result = array('success' => FALSE, 'message' => 'Erro ao salvar arquivo para o artefato.');
        try {
            $dto = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
            $this->getService()->saveImage($dto);
            $result['success'] = TRUE;
            $result['message'] = 'Imagem salva com sucesso.';
        } catch (\Core_Exception_ServiceLayer $exc) {
            $result['message'] = $exc->getMessage();
        }
        $this->_helper->json($result);
    }

    /**
     * @return array Com o parametros da requisição
     */
    private function _viewSteps ()
    {
        $params = $this->_getAllParams();
        if (!isset($params['id'])) {
            throw new \Exception("Artefato não informado");
        }
        $this->view->sqArtefato = $params['id'];
        $this->view->hasImage = $this->getService()->hasImage($params['id']);
        if ($this->view->hasImage) {
            $this->view->forbiddenAccess = $this->getService()->forbiddenAccess($params['id'], Core_Integration_Sica_User::getPersonId());
        }

        // Regras para artefatos de migração.
        $dtoSearch = \Core_Dto::factoryFromData(array('sqArtefato' => $params['id']), 'search');
        $isMigracao = $this->getService("Artefato")->isMigracao($dtoSearch);

        if ($isMigracao) {
            $dtoSearch = \Core_Dto::factoryFromData(array('sqArtefato' => $params['id']), 'search');
            # Tainá falou que era para ser assim.
            if ($this->getService('Artefato')
                            ->isInconsistent($dtoSearch)) {
                $this->view->forbiddenAccess = false;
            }
            $this->view->treeviewData = $this->getService('ArtefatoVinculo')->mostarArvoreMigracao($dtoSearch);
        } else {
            $this->view->treeviewData = $this->getService('ArtefatoVinculo')->mostarArvore($params['id']);
        }

        return $params;
    }

    public function downloadAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        try {
            $params = $this->_getAllParams();
            $arrRetorno = $this->getService()->processDownloadFileRequest($params['id']);
        } catch (\Exception $e) {
            $arrRetorno['success'] = false;
            $arrRetorno['msg'] = $e->getMessage();
            $arrRetorno['link'] = '';
        }
        $this->_helper->json($arrRetorno);
    }

    public function viewImageDownloadAction ()
    {

        $id = base64_decode($this->getRequest()->getParam('id', NULL));

        if (!$id) {
            throw new \Exception('Nenhuma informação recebida para apresentação do PDF');
        }

        $entSolicitacao = $this->getService()->findSolicitacaoDownloadImagem($id);

        if (!$entSolicitacao->getStProcessado()) {
            throw new \Exception('Esta Solicitação não foi processada');
        }
        if (!$entSolicitacao->getTxLink()) {
            throw new \Exception('Esta Solicitação foi processada, porém nenhum arquivo foi gerado.');
        }

        if (!$entSolicitacao->getDtDownload()) {
            $this->getService()->updateDtDownloadSolicitacao($entSolicitacao);
        }


        $this->_redirect($entSolicitacao->getTxLink());
    }

}