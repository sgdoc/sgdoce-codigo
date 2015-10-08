<?php
require_once __DIR__ . '/DocumentoControllerExtensao.php';
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
 * Classe para Controller de Artefato
 *
 * @package    Artefato
 * @category   Controller
 * @name       Documento
 * @version    1.0.0
 */
class Artefato_DocumentoController extends DocumentoControllerExtensao
{

    protected $_messageEdit = 'MN044'; #mensagem de inclusao

    /**
     * @var string
     */
    protected $_service = 'Documento';
    protected $_optionsDtoEntity = array(
        'entity' => 'Sgdoce\Model\Entity\Artefato',
        'mapping' => array(
            'sqFecho' => 'Sgdoce\Model\Entity\Fecho',
            'sqTipoDocumento' => 'Sgdoce\Model\Entity\TipoDocumento',
            'sqLoteEtiqueta' => 'Sgdoce\Model\Entity\EtiquetaNupSiorg',
            'nuDigital' => array('nuEtiqueta' => 'Sgdoce\Model\Entity\EtiquetaNupSiorg'),
            'sqPessoaRecebimento' => 'Sgdoce\Model\Entity\VwPessoa',
        )
    );

    /**
     * View principal da controller
     * @return redireciona para createAction
     */
    public function indexAction ()
    {
        // redireciona
        $this->view->tipoEtiquetaFisica = \Core_Configuration::getSgdoceTipoEtiquetaFisica();
        $this->view->tipoEtiquetaEletronica = \Core_Configuration::getSgdoceTipoEtiquetaEletronica();
    }

    public function createAction ()
    {
        parent::createAction();

        $tipoDigital = $this->getRequest()->getParam('tipoDigital', 2);
        $nuDigital   = $this->getRequest()->getParam('nuDigital', null);

        $this->getCombo();

        $this->view->isLoteEletronico = false;
        if ($tipoDigital == \Core_Configuration::getSgdoceTipoEtiquetaFisica()) {
            $dto = Core_Dto::factoryFromData(array(
                        'nuDigital' => $nuDigital), 'search');
            $eDigital = $this->getService()->getDigitalNupSiorg($dto);
            $this->view->eDigital = $eDigital;
            $this->view->data->setInEletronico(false);
        } else {
            $this->view->data->setInEletronico(true);
            $this->view->isLoteEletronico = true;
        }

        $this->view->tipoDigital = $tipoDigital;

        $dto = Core_Dto::factoryFromData(array('nuDigital' => $nuDigital), 'search');


        $eDigital = $this->getService()->getDigitalNupSiorg($dto);
        //utilizado para saber qual tipo de procendencia deve ser desabilitado na tela
        $nuNupSiorg = $eDigital->getNuNupSiorg(true);
        if ($nuNupSiorg) {
            $disabledProcedencia = 'chekProcedenciaExterno';
        } else {
            $disabledProcedencia = 'chekProcedenciaInterno';
        }

        $this->view->disabledProcedencia = $disabledProcedencia;
        $this->view->isSIC  = false;
        $this->view->CGU    = null;
        $this->view->docSIC = null;

        $this->view->arrWithoutSignature = $this->getService()->getTipoDocumentoSemAssinatura();
        $this->view->arrWithDuplicityCheck = $this->getService()->getTipoDocumentoComValidacaoDuplicidade();

        $this->render('form');
    }

    public function sicAction ()
    {
        parent::createAction();

        $this->getCombo();
        $dto = Core_Dto::factoryFromData(array(
            'sqPessoaCorporativo' => \Core_Configuration::getSgdoceUnidadeCgu(),
            'sqTipoPessoa'=>\Core_Configuration::getCorpTipoPessoaUnidadeExt()),
        'search');

        $this->view->data->setInEletronico(true);
        $this->view->isLoteEletronico    = true;
        $this->view->disabledProcedencia = null;
        $this->view->tipoDigital         = \Core_Configuration::getSgdoceTipoEtiquetaEletronica();
        $this->view->isSIC  = true;
        $this->view->CGU    = $this->getService('Pessoa')->getPessoa($dto);
        $this->view->docSIC = $this->getService('TipoDocumento')
                                    ->find(\Core_Configuration::getSgdoceTipoDocumentoSic());
        $this->render('form');
    }

    public function saveAction ()
    {
        try {
            # mesmo que seja função, salva na coluna de cargo 
            if (!$this->getRequest()->getParam('noCargoEncaminhado') && $this->getRequest()->getParam('noFuncaoEncaminhado')) {
                $this->getRequest()->setPost('noCargoEncaminhado', $this->getRequest()->getParam('noFuncaoEncaminhado'));
            }
            $tipoArtefato = $this->getRequest()->getParam('tipoArtefato', 1);
            $caixa        = $this->getRequest()->getParam('caixa','caixaMigracao');
            $controller   = $this->getRequest()->getParam('controllerBack','area-trabalho');

            $this->_redirect = array(
                'module' => 'artefato',
                'controller' =>$controller,
                'action'=> 'index',
                'params'=>array('tipoArtefato'=>$tipoArtefato, 'caixa'=>$caixa)
            );

            parent::saveAction();
        } catch (\Core_Exception_ServiceLayer_Verification $e) {
            $this->_helper->json(array('error' => true, 'errorType' => 'Alerta', 'msg' => $e->getMessage()));
        } catch (\Exception $e) {
            $this->_helper->json(array('error' => true, 'errorType' => 'Erro', 'msg' => $e->getMessage()));
        }
    }

    public function validaDigitalAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $msg = null;
        if ($this->_getParam('tipoDigital') == 1) {
            $status = $this->_checkDigitalFisica();

            if ($status === 1) {
                $this->_helper->json(array(
                    'error' => true,
                    'msg' => \Core_Registry::getMessage()->translate('MN150')
                ));
            }

            // E2. Digital já cadastrada
            if ($status === 2) {
                $this->_helper->json(array(
                    'error' => true,
                    'msg' => Core_Registry::getMessage()->translate('MN007') //Número da digital já cadastrado
                ));
            }

            $dto = Core_Dto::factoryFromData(array(
                        'nuDigital' => $this->_getParam('nuDigitalValida')), 'search');

            $eDigital = $this->getService()->getDigitalNupSiorg($dto);
            $nuNupSiorg = $eDigital->getNuNupSiorg(true);
            if ($nuNupSiorg) {
                $msg = sprintf(\Core_Registry::getMessage()->translate('MN148'), $nuNupSiorg);
            } else {
                $msg = \Core_Registry::getMessage()->translate('MN149');
            }
        } else {

            #@TODO: retirar quando o cadastro de documento eletrônico for liberado
            $this->_helper->json(array(
                'error' => true,
                'msg' => \Core_Registry::getMessage()->translate('MN151')
            ));

            $status = $this->_checkDigitalEletronica();
            if ($status === 1) {
                $this->_helper->json(array(
                    'error' => true,
                    'msg' => \Core_Registry::getMessage()->translate('MN152')
                ));
            }
        }

        $this->_helper->json(array('new' => true, 'msg' => $msg));
    }

    public function editAction ()
    {
        $params = $this->_getAllParams();
        $dtoSearch = \Core_Dto::factoryFromData(array('sqArtefato' => $params['id']), 'search');
        $isMigracao = $this->getService("Artefato")->isMigracao($dtoSearch);
        $this->view->isMigracao = $isMigracao;

        parent::editAction();

        $url = $this->getRequest()
                    ->getParam('back', false);

        $this->view->urlBack        = false;
        $this->view->controllerBack = null;
        $this->view->caixa          = null;

        if( $url ) {
            $url = str_replace(".", "/", $url);
            $this->view->urlBack = $url;
            $url = substr($url, 1);
            $params = explode("/", $url);
            $this->view->controllerBack = next($params);
            $this->view->caixa          = end($params);
        }

        $this->editActionExtension();

        $this->view->arrWithoutSignature = $this->getService()->getTipoDocumentoSemAssinatura();
        $this->view->arrWithDuplicityCheck = $this->getService()->getTipoDocumentoComValidacaoDuplicidade();

        if( $this->view->data->getSqTipoDocumento() ) {
            $sqTipoDocumento = $this->view->data->getSqTipoDocumento()->getSqTipoDocumento();
            $this->view->isDocWVDuplicity = (in_array($sqTipoDocumento, $this->view->arrWithDuplicityCheck));
        }
    }

    public function validateAction ()
    {
        try {
            $sqUnidadeOrg = \Core_Integration_Sica_User::getUserUnit();
            $tipoDigital = $this->_getParam('tipoDigital');

            //eletrônica
            if ($tipoDigital == 2) {

                //Buscar o nr da digital eletrônica
                $inEletronico = true;
                /* O nr de etiqueta sera gerado ao final do cadastro */

                $nuDigital = null;
                $sqLoteEtiqueta = null;
            } else {
                $status = $this->_checkDigitalFisica();

                if ($status === 2) {
                    throw new Core_Exception(
                    Core_Registry::getMessage()->translate('MN007'));
                }

                $nuDigital = $this->_getParam('nuDigitalValida');
                //quebra a digital em Nr e ano
                $arrDigital = \Sgdoce_Util::normalizeDigital($nuDigital);
                $sqLoteEtiqueta = $this->getService()
                        ->recuperaNumeroLoteEtiqueta(
                        Core_Dto::factoryFromData(array(
                            'nuDigital' => $arrDigital['nuDigital'],
                            'nuAno' => $arrDigital['nuAno'],
                            'sqUnidadeOrg' => $sqUnidadeOrg
                                ), 'search'
                        )
                );
                if (null === $sqLoteEtiqueta) {
                    throw new \Core_Exception_ServiceLayer(
                    Core_Registry::getMessage()->translate('MN153'));
                }
            }

            $this->_helper->json(true);
        } catch (\Exception $e) {
            $this->_helper->json(array(
                'error' => true,
                'msg' => $e->getMessage()
            ));
        }
    }

    public function deleteArtefatoAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $entity = $this->getService('Artefato')->find($this->_getParam('id'));
        $sqAssunto = $entity->getSqTipoArtefatoAssunto()->getSqAssunto();

        if (empty($sqAssunto)) {
            $this->getService('Artefato')->delete($this->_getParam('id'));
            $this->getService('Artefato')->finish();
        }
        return true;
    }

    public function deleteAnexoArtefatoAction ()
    {
        $entityAnexoArtefato = $this->getService('AnexoArtefatoVinculo')->find($this->_getParam('id'));
        $entityArtefatoVinculo = $this->getService('ArtefatoVinculo')->find($entityAnexoArtefato->getSqArtefatoVinculo()->getSqArtefatoVinculo());

        $this->getService('AnexoArtefatoVinculo')->delete($this->_getParam('id'));
        $this->getService('AnexoArtefatoVinculo')->finish();

        $this->getService('ArtefatoVinculo')->delete($entityAnexoArtefato->getSqArtefatoVinculo()->getSqArtefatoVinculo());
        $this->getService('ArtefatoVinculo')->finish();

        $this->getService('Artefato')->delete($entityArtefatoVinculo->getSqArtefatoFilho()->getSqArtefato());
        $this->getService('Artefato')->finish();

        if (file_exists($entityAnexoArtefato->getDeCaminhoAnexo())) {
            unlink($entityAnexoArtefato->getDeCaminhoAnexo());
        }

        return TRUE;
    }

    public function searchPessoaAction ()
    {
        // verificando tipo de registro
        if ($this->_getParam('nuCpf')) {
            $criteria = array('nuCPFDestinatario' => Zend_Filter::filterStatic(
                        $this->_getParam('nuCpf'), 'Digits'));
        }
        if ($this->_getParam('nuCnpj')) {
            $criteria = array('nuCPFDestinatario' => Zend_Filter::filterStatic(
                        $this->_getParam('nuCnpj'), 'Digits'));
        }
        if ($this->_getParam('nuPassaporte')) {
            $criteria = array('nuCPFDestinatario' => Zend_Filter::filterStatic(
                        $this->_getParam('nuPassaporte'), 'Digits'));
        }
        $criteria['sqTipoPessoa'] = $this->_getParam('sqTipoPessoa');
        // tratando parametros
        $dtoSearch = Core_Dto::factoryFromData($criteria, 'search');
        // retornando valor
        $this->getHelper('json')->sendJson($this->getService('VwPessoa')->getPessoaDados($dtoSearch));
    }

    public function modalMaterialAction ()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();
        $this->view->allParams = $params;
        $this->view->sqArtefato = $params['sqArtefato'];
        $this->view->listTipoAnexo = $this->getService('TipoAnexo')->listItems();
        $this->view->nuDigital = $this->getService('Artefato')->createNumeroDigital();
    }

    public function listMaterialAction ()
    {
        // desabilitando layout
        $this->getHelper('layout')->disableLayout();
        // valores da grid
        $configGrid = array('ta.noTipoAnexo', 'af.nuDigital', 'aav.noTituloAnexo');
        // setando parametros
        $params = $this->_getAllParams();
        $params['sqTipoVinculo'] = \Core_Configuration::getSgdoceTipoVinculoArtefatoApoio();
        // retornando valor pra grid
        $this->view->grid = new Core_Grid($configGrid);
        $params = $this->view->grid->mapper($params);
        // tratando parametros
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        // retornando valores pra view
        $this->view->result = $this->getService()->listGridMaterialApoio($this->view->dto);
    }

    public function listVincularDocumentoAction ()
    {
        $this->getHelper('layout')->disableLayout();
        $params = $this->_getAllParams();
        $params['sqTipoVinculo'] = \Core_Configuration::getSgdoceTipoVinculoArtefatoReferencia();
        $this->view->grid = new Core_Grid(array('af.nuArtefato',
            'af.nuDigital',
            'ta.noTipoArtefato',
            'ps.noPessoa')
        );
        $params = $this->view->grid->mapper($params);
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getService()->listGridVinculacao($this->view->dto);
    }

    public function modalVinculacaoAction ()
    {
        $params = $this->_getAllParams();
        $params['sqArtefato'] = $this->_getParam('sqArtefato', '');
        // herdando action
        parent::createAction();
        // desabilitando layout
        $this->_helper->layout->disableLayout();
        // retornando valores pra view
        $this->view->sqArtefato = $params['sqArtefato'];
        $this->view->listTipoArtefato = $this->getService('TipoArtefato')->listItems('documento');
        $this->view->nuDigital = $this->_getParam('nuDigital');
    }

    public function materialAction ()
    {
        $params = $this->_getAllParams();
        $params['tipoVinculo'] = \Core_Configuration::getSgdoceTipoVinculoArtefatoApoio();

        $dto = Core_Dto::factoryFromData($params, 'search');

        //Salva o Artefato Material de Apoio
        $material = $this->getService()->saveArtefato($dto);

        //Salva o ArtefatoVinculo entre o Artefato e o material
        $params['sqArtefatoVinculo'] = $this->getService()->addArtefatoVinculo($dto);
        $dto = Core_Dto::factoryFromData($params, 'search');

        //Salva o AnexoArtefato
        $json = $this->getService()->addAnexoArtefatoVinculo($dto);
        $this->getHelper('json')->sendJson($json);
    }

    public function addDocumentoEletronicoAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $params['tipoVinculo'] = \Core_Configuration::getSgdoceTipoVinculoArtefatoReferencia();
        $dto = Core_Dto::factoryFromData($params, 'search');


        if ($dto->getNuArtefatoVinculacao()) {
            $artefato = $this->getService('Artefato')->findBy(array('nuArtefato' => $dto->getNuArtefatoVinculacao()));
        } else {
            $artefato = $this->getService('Artefato')->findBy(array('nuDigital' => $dto->getNuDigital()));
        }

        $criteria = array('sqArtefatoPai' => $dto->getSqArtefato()
            , 'sqArtefatoFilho' => $artefato[0]->getSqArtefato()
            , 'sqTipoVinculoArtefato' => $params['tipoVinculo']
            , 'dtRemocaoVinculo' => NULL);

        $result = $this->getService('ArtefatoVinculo')->findBy($criteria);

        if (count($result) > 0) {
            $this->_helper->json(array('sucess' => 'false'));
        } else {
            $json = $this->getService('Documento')->addDocumentoEletronico($dto);
            $this->_helper->json(array('sucess' => 'true'));
        }
    }

    public function comboDescricaoPrioridadeAction ()
    {
        // desabilitando layout e evitando rendereizacao da action
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $sqPrioridade = $this->_getParam('sqPrioridade')?:0;
        $arrDescricao = $this->getService('TipoPrioridade')->comboDescricaoPrioridade($sqPrioridade,false);
        $this->getHelper('json')->sendJson($arrDescricao);
    }

    public function searchTipoDocumentoAction ()
    {
        // desabilitando layout e evitando rendereizacao da action
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $this->getHelper('json')->sendJson($this->getService()->tipoDocumento($params));
    }

    public function autoCompleteVinculacaoAction ()
    {
        $params = $this->_getAllParams();

        if (isset($params['nuArtefato'])) {
            $params['nuArtefato'] = str_replace('!', '/', $params['nuArtefato']);
        }
        $dto = Core_Dto::factoryFromData($params, 'search');

        $entity = $this->getService('Dossie')->findAutoComplete($dto);

        if (count($entity)) {

            $dados = $this->getService()->returnAutoCompleteVinculacao($entity);

            $return = array(
                'sqTipoArtefato' => $dados['tipoArtefato'],
                'sqTipoDocumento' => $dados['sqTipoDocumento'],
                'noTipoDocumento' => $dados['noTipoDocumento'],
                'noPessoa' => $dados['sqPessoa'],
                'sqPessoa' => $dados['Pessoa'],
                'nuArtefato' => $entity[0]->getNuArtefato(),
                'nuDigital' => ($dto->getSqTipoArtefatoVinculacao() != \Core_Configuration::getSgdoceTipoArtefatoProcesso()) ? $entity[0]->getNuDigital()->getNuEtiqueta() : null
            );
        } else {
            $return = array();
        }
        $this->_helper->json($return);
    }

    public function findOrigemAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params['nuDigital'] = $this->_getParam('nuDigital');
        if ($this->_getParam('nuArtefato')) {
            $params['nuArtefato'] = $this->_getParam('nuArtefato');
        }
        $dto = new Core_Dto_Mapping($params, array_keys($params));
        $entity = $this->getService()->findArtefatoByNuDigitalOrNuArtefato($dto);

        if (count($entity)) {
            $noPessoa = NULL;
            if ($dto->getNuDigital()) {
                $noPessoa = $this->getService('PessoaArtefato')->searchPessoaOrigem($entity[0]);
            }
            $return = array('sqTipoArtefato' => $entity[0]->getSqTipoDocumento()->getSqTipoDocumento(),
                'noPessoa' => $noPessoa[1] ? $noPessoa[1] : NULL,
                'nuDigital' => $entity[0]->getNuDigital());
        } else {
            $return = array();
        }
        $this->_helper->json($return);
    }

    public function verificaDocumentoAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->_getAllParams();
        $data = array();
        $data['nuArtefato'] = $params['nuArtefato'];
        $data['sqTipoDocumento'] = $params['sqTipoDocumento'];

        $params['sqPessoaCorporativo'] = null;
        if (isset($params['sqPessoaIcmbio']) && $params['sqPessoaIcmbio']) {
            $params['sqPessoaCorporativo'] = $params['sqPessoaIcmbio'];
        } else if (isset($params['sqPessoaOrigem']) && $params['sqPessoaOrigem']) {
            $params['sqPessoaCorporativo'] = $params['sqPessoaOrigem'];
        }

        $dto = Core_Dto::factoryFromData($params, 'search');

        $listTipoDocumentoCVD = $this->getService()->getTipoDocumentoComValidacaoDuplicidade();
        
        if( $dto->getStSemNumero() == 'true'
            && $dto->getStProcedencia() == 'true'
            && in_array($data['sqTipoDocumento'], $listTipoDocumentoCVD) ) {
            $this->_helper->json(array('sucess' => 'false', 'msg' => 'MN159'));
        }

        $pessoaSgdoce = $this->getService('Pessoa')->findPessoaBySqCorporativo($dto);
        $resul = $this->getService('Artefato')->findBy($data);

        if ($params['divEdit'] == '0') {
            if (count($resul) > 0 && $dto->getStNumeroAutomatico() == 'false' ) {
                for ($i = 0; $i < count($resul); $i++) {
                    $pessoaArtefato = $this->getService('PessoaArtefato')
                            ->findOneBy(array('sqArtefato' => $resul[$i]->getSqArtefato(),
                        'sqPessoaSgdoce' => $pessoaSgdoce,
                        'sqPessoaFuncao' => \Core_Configuration::getSgdocePessoaFuncaoOrigem())
                    );
                    if (count($pessoaArtefato) > 0) {
                        $this->_helper->json(array('sucess' => 'false', 'msg' => 'MN158'));
                        continue;
                    }
                }
                $this->_helper->json(array('sucess' => 'true'));
            } else {
                $this->_helper->json(array('sucess' => 'true'));
            }
        }

        $this->_helper->json(array('sucess' => 'true'));
    }

    public function verificaDuplicidadeAction ()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params, 'search');

        $result = $this->getService()->verificaDuplicidade($dto);

        $return['success'] = FALSE;
        if ($result) {
            $return['success'] = TRUE;
        }
        return $this->_helper->json($return);
    }

    /**
     * Metódo que recupera a pessoa
     * @return json
     */
    public function searchPessoaUnidadeAction ()
    {
        $params = $this->_getAllParams();
        $this->_helper->layout->disableLayout();

        //recuperar somente as pessoas da unidade do usuario logado
        $params['sqUnidadeOrg'] = Core_Integration_Sica_User::getUserUnit();

        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
        $service = $this->getService('VwPessoa')->searchPessoaUnidade($dtoSearch, 30);

        $this->_helper->json($service);
    }

    public function listAnexoSicAction ()
    {
        // desabilitando layout
        $this->getHelper('layout')->disableLayout();
        // setando parametros
        $params = $this->_getAllParams();
        // retornando valor pra grid
        $this->view->grid = new Core_Grid(array('asic.noArquivoReal', 'asic.dtCadastro'));

        $params = $this->view->grid->mapper($params);

        // tratando parametros
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        // retornando valores pra view
        $this->view->result = $this->getService()->listGridAnexoSic($this->view->dto);
    }

    public function deleteAnexoSicAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        try {
            $retorno = array('error'=>false, 'msg'=> \Core_Registry::getMessage()->translate('MD003'));

            $dto = \Core_Dto::factoryFromData(array(
                'sqAnexoSic' => $this->getRequest()->getParam('id')),
            'search');

            $this->getService()->deleteAnexoSic($dto);

        } catch (\Exception $e) {
            $retorno['error'] = true;
            $retorno['msg'  ] = 'Erro interno da aplicação. Tente novamente mais tarde. '. $e->getMessage();
        }
        $this->_helper->json($retorno);
    }

    /**
     *
     * @return integer
     */
    private function _checkDigitalFisica ()
    {
        $etiqueta = $this->_getParam('nuDigitalValida');
        $digitalNormalized = \Sgdoce_Util::normalizeDigital($etiqueta);
        $tipoDigital = $this->_getParam('tipoDigital');

        $params = array();
        $params['nuEtiqueta'] = $etiqueta;
        $params['nuSequencialDigital'] = $digitalNormalized['nuDigital'];
        $params['nuAno'] = $digitalNormalized['nuAno'];
        $params['sqTipoEtiqueta'] = (integer) $tipoDigital;
        $params['sqUnidadeOrg'] = Core_Integration_Sica_User::getUserUnit();

        $dtoSearch = Core_Dto::factoryFromData($params, 'search');

        if (!$this->getService()->verificaLiberacaoDigital($dtoSearch)) {
            return 1;
        }

        // E2. Digita já cadastrada
        if ($this->getService()->verificaDigitalEmUso($dtoSearch)) {
            return 2;
        }

        return 0;
    }

    /**
     *
     * @return integer
     */
    private function _checkDigitalEletronica ()
    {
        $tipoDigital = $this->_getParam('tipoDigital');

        $params = array();
        $params['nuAno'] = date('Y');
        $params['sqTipoEtiqueta'] = (integer) $tipoDigital;
        $params['sqUnidadeOrg'] = \Core_Integration_Sica_User::getUserUnit();

        $dtoSearch = Core_Dto::factoryFromData($params, 'search');

        Zend_Wildfire_Plugin_FirePhp::send($dtoSearch, 'dtoSearch');

        if (!$this->getService()->verificaLiberacaoDigitalEletronica($dtoSearch)) {
            return 1;
        }
        return 0;
    }

}
