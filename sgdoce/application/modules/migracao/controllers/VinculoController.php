<?php
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
 *
 *
 * @package    Migracao
 * @category   Controller
 * @name       Vinculo
 * @version    1.0.0
 */
class Migracao_VinculoController extends \Core_Controller_Action_CrudDto
{
    /**
     * @var string
     */
    protected $_service = 'VinculoMigracao';

    protected $_optionsDtoEntity = array(
        'entity' => '',
        'mapping' => array()
    );

    /**
     * @return redireciona para createAction
     */
    public function indexAction()
    {
        $sqArtefato = $this->_getParam('id');

        $url = $this->getRequest()->getParam('back', false);

        if( $url ) {
            $url = str_replace(".", "/", $url);
            $this->view->urlBack = $url;
            $url = substr($url, 1);
            $params = explode("/", $url);
            $this->view->controllerBack = next($params);
            $this->view->caixa          = end($params);
        }

        $dto = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
        $arResult = $this->getService()->getChilds($dto);

        if( $arResult ) {
            $this->view->listVinculos = $arResult['list'];

            $isProcesso = $this->getService('ArtefatoProcesso')->isProcesso($sqArtefato, false);
            $this->view->tipoArtefato = ($isProcesso) ? \Core_Configuration::getSgdoceTipoArtefatoProcesso() : \Core_Configuration::getSgdoceTipoArtefatoDocumento() ;
        } else {
            $this->_redirect( $this->view->urlBack );
        }

        if( $arResult['isOk'] ) {
            $sqArtefatoPai = $arResult['sqArtefatoPai'];
            $this->getService()->setMigracaoConcluida($sqArtefatoPai);
            $dtox = Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefatoPai), 'search');
            $areaTrabalho = $this->getService('AreaTrabalho')->findArtefato($dtox);

            $msg = 'Migração concluída com sucesso';
            $caixa = "minhaCaixa";

            if ($areaTrabalho) {

                $this->getMessaging()->addSuccessMessage($msg, 'User');
                $this->getMessaging()->dispatchPackets();

                $this->_redirect( 'artefato/area-trabalho/index/tipoArtefato/' . $this->view->tipoArtefato . '/caixa/' . $caixa );

            } else {
                $sucesso = FALSE;
                if( $this->getService('CaixaArtefato')->isArquivado($dtox) ) {
                    $caixa = "caixaArquivo";
                    $msg .= ", o artefato se encontra arquivado.";
                    $sucesso = TRUE;
                }

                if( $this->getService('VwUltimoTramiteArtefato')->isTramiteExterno($dtox) ){
                    $caixa = "caixaExterna";
                    $sucesso = TRUE;
                }

                if ($sucesso) {
                    $this->getMessaging()->addSuccessMessage($msg, 'User');
                    $this->getMessaging()->dispatchPackets();

                    $this->_redirect( 'artefato/area-trabalho/index/tipoArtefato/' . $this->view->tipoArtefato . '/caixa/' . $caixa );
                } else {
                    $this->getMessaging()->addErrorMessage(\Core_Registry::getMessage()->translate('MN178'), 'User');
                    $this->getMessaging()->dispatchPackets();
                }
            }

            //@TODO: procurar por getArquivado e getIsTramiteExterno pois usuando a função essas informações não vem


            //ANTES ERA FEITO ASSIM quando fazia find na vw_area_trablaho
//            if ($entArtefato) {
//
//                if( $entArtefato->getArquivado() ) {
//                    $caixa = "caixaArquivo";
//                    $msg .= ", o artefato se encontra arquivado.";
//                }
//
//                if( $entArtefato->getIsTramiteExterno() ){
//                    $caixa = "caixaExterna";
//                }
//
//                $this->getMessaging()->addSuccessMessage($msg, 'User');
//                $this->getMessaging()->dispatchPackets();
//
//                $this->_redirect( 'artefato/area-trabalho/index/tipoArtefato/' . $this->view->tipoArtefato . '/caixa/' . $caixa );
//            } else {
//                $this->getMessaging()->addErrorMessage(\Core_Registry::getMessage()->translate('MN178'), 'User');
//                $this->getMessaging()->dispatchPackets();
//            }
        }

        $this->view->urlBack        = false;
        $this->view->controllerBack = null;
        $this->view->caixa          = null;
        $this->view->id             = $sqArtefato;

        $this->view->maskNumber = new Core_Filter_MaskNumber();
    }

    /**
     * @return boolean
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $dto    = Core_Dto::factoryFromData($params, 'search');
        $delete = $this->getService()->deleteAnexos($params['sqAnexoArtefato']);
        $this->_helper->json($delete);
        return TRUE;
    }

    /**
     * @return void
     */
    public function confirmarImagemAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $data = $this->_getAllParams();

        $sqArtefato = (integer) $data['id'];

        try {
            # delega a exclusao para superclasse
            $service = $this->getService('DocumentoMigracao');
            $service->setHasImage($sqArtefato);

            $this->getMessaging()->addSuccessMessage("Imagem confirmada com sucesso!",'User');
            $this->getMessaging()->dispatchPackets();

            $this->_helper->json(array(
                "status" => TRUE,
                "message" => "Imagem confirmada com sucesso!"
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
    public function searchPessoaFisicaAction()
    {
        $params = $this->_getAllParams();
        $this->_helper->layout->disableLayout();
        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
        $service = $this->getService()->searchPessoaFisica($dtoSearch);
        $this->_helper->json($service);
    }
}
