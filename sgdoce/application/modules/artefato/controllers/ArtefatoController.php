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

require_once __DIR__ . '/ArtefatoControllerExtensao.php';

/**
 * Classe para Controller de Processo
 *
 * @package  Artefato
 * @category Controller
 * @name     Artefato
 * @version     1.0.0
 */
class Artefato_ArtefatoController extends \ArtefatoControllerExtensao
{
    /**
     * Constante para receber o valor do padrao modelo documento atos
     * @var integer
     * @name   SQ_PADRAO_MODELO_DOCUMENTO_ATOS
     */
    const SQ_PADRAO_MODELO_DOCUMENTO_ATOS   = 1;

    /**
     * Constante para receber o valor do padrao modelo documento geral
     * @var integer
     * @name   SQ_PADRAO_MODELO_DOCUMENTO_GERAL
     */
    const SQ_PADRAO_MODELO_DOCUMENTO_GERAL  = 2;

    /**
     * Constante para receber o valor do padrao modelo documento oficio
     * @var integer
     * @name   SQ_PADRAO_MODELO_DOCUMENTO_OFICIO
     */
    const SQ_PADRAO_MODELO_DOCUMENTO_OFICIO = 3;

    /**
     * Serviço
     * @var string
     */
    protected $_service = 'Artefato';

    /**
     * Monta as combos default para os tipos de processos
     * @return void
     */
    public function combos()
    {
        $this->view->ambito = array('F' => 'Federal','E'=> 'Estadual','M'=>'Municipal','J'=>'Judicial');
        $this->view->estado = $this->getService('Estado')->comboEstado();
        $this->view->tipoPessoa = $this->getService('TipoPessoa')->getComboDefault(array());
        $this->view->temaVinculado = array('Cavernas', 'Espécies', 'Empreendimentos', 'Unidades de Conservação');
        $this->view->tipoDocumento = $this->getService('TipoDocumento')->getComboDefault(array());
        $this->view->tipoArtefato = $this->getService('TipoArtefato')->listItems(array());
        $this->view->assunto = $this->getService('Assunto')->comboAssunto(array());
        $this->view->prioridade = $this->getService('Prioridade')->listItems();
        $this->view->tipoPrioridade = $this->getService('TipoPrioridade')->listItems();
        $this->view->municipio = $this->getService('VwEndereco')->comboMunicipio(NULL,TRUE);
    }

    /**
     * Método efetua a busca das unidades organizacionais pertencentes ao ICMBio
     * @return void
     */
    public function unidadeOrgIcmbioAction()
    {
        $dtoUnidSearch = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $arrUnid = $this->getService('processo')->unidadeOrgIcmbio($dtoUnidSearch);
        $this->getHelper('json')->sendJson($arrUnid);
        
        $this->getRequest()->getParams();
    }

    public function deleteAction()
    {
        // desabilitando layout
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $dtoSearch = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $sqArtefato = $dtoSearch->getSqArtefato();

        //verifica apenas se o artefato possui vinculos ativos
        $artefatoVinculo = $this->getService('ArtefatoVinculo')->verificaVinculoArfato($dtoSearch);

        $artefatoProcesso = $this->getService('ArtefatoProcesso')->find($sqArtefato);
        if($artefatoProcesso){
            $session = new Core_Session_Namespace('Sequencial');
            $sequencial = $session->oldNuSequencial;
            $this->getService('ArtefatoProcesso')->delete($sqArtefato);
            if (!empty($sequencial)){
                $this->getService('SequencialArtefato')->saveSequencialProcesso($sequencial);
            }
            unset($session);
            $this->getService('ArtefatoProcesso')->finish();
        }

        if(count($artefatoVinculo) <= 0){
            //excluir os vinculos como pai ja desativados
            $criteria = array('sqArtefatoPai' => $sqArtefato);
            $artefatoVinculoPai = $this->getService('ArtefatoVinculo')->findBy($criteria);
            if(count($artefatoVinculoPai) > 0){
                $this->_excluirVinculoArtefato($artefatoVinculoPai);
            }
            //exclui os vinculos como filho ja desativados
            $criteria = array('sqArtefatoFilho' => $sqArtefato);
            $artefatoVinculoFilho = $this->getService('ArtefatoVinculo')->findBy($criteria);

            if(count($artefatoVinculoFilho) > 0){
                $this->_excluirVinculoArtefato($artefatoVinculoFilho);
            }

            $criteria = array('sqArtefato' => $sqArtefato);
            $anexoArtefato = $this->getService('AnexoArtefato')->findBy($criteria);
            foreach ($anexoArtefato as $key => $value) {
                $this->getService('AnexoArtefato')->delete($value->getSqAnexoArtefato());

                //Excluir Imagem
                if (file_exists($value->getDeCaminhoArquivo()))
                    unlink($value->getDeCaminhoArquivo());
            }
            $this->getService('AnexoArtefato')->finish();

            $this->getService('Motivacao')->deleteTodaMotivacao($sqArtefato);
            $this->getService('PessoaAssinanteArtefato')->deleteTodasAssinatura($sqArtefato);
            $this->getService('PessoaInterassadaArtefato')->deleteTodosInteressado($sqArtefato);

            $entity = $this->getService('ArtefatoDossie')->deleteDossie($sqArtefato);
            $entity = $this->getService('ArtefatoProcesso')->delete($sqArtefato);
            $entity = $this->getService('ArtefatoMinuta')->delete($sqArtefato);


            $entityArtefato = $this->getService('Artefato')->find($sqArtefato);

            $criteria = array(
                'nuEtiqueta' => $entityArtefato->getNuDigital()->getNuEtiqueta(),
                'sqLoteEtiqueta' => $entityArtefato->getNuDigital()->getSqLoteEtiqueta()->getSqLoteEtiqueta()
            );

            $this->getService('Artefato')->delete($sqArtefato);
            $this->getService('EtiquetasUso')->delete($criteria);

            $this->getService('EtiquetasUso')->finish();
            $this->getService('Artefato')->finish();

            return $this->_helper->json('true');
        }else{
            return $this->_helper->json('false');
        }
    }

    /**
     * método que exclui os vinculos do artefato
     * @return bollean
     */
    private function _excluirVinculoArtefato ($artefatoVinculo) {
        foreach ($artefatoVinculo as $key => $value) {
            $criteria = array('sqArtefatoVinculo' =>$value->getSqArtefatoVinculo());
            $anexoArtefatoVinculo = $this->getService('AnexoArtefatoVinculo')->findBy($criteria);
            foreach ($anexoArtefatoVinculo as $key => $value1) {
                //Excluir Imagem
                if (file_exists($value1->getDeCaminhoAnexo()))
                    unlink($value1->getDeCaminhoAnexo());

                $this->getService('AnexoArtefatoVinculo')->delete($value1->getSqAnexoArtefatoVinculo());
            }

            $this->getService('ArtefatoVinculo')->delete($value->getSqArtefatoVinculo());

            //Excluir Artefato filho quando tipo vinculo = "SGDOCE_TIPO_VINCULO_ARTEFATO_APOIO"
            if($value->getSqTipoVinculoArtefato()->getSqTipoVinculoArtefato() == \Core_Configuration::getSgdoceTipoVinculoArtefatoApoio()){
                $this->getService('Artefato')->delete($value->getSqArtefatoFilho()->getSqArtefato());
            }
        }
        $this->getService('AnexoArtefatoVinculo')->finish();
        $this->getService('ArtefatoVinculo')->finish();

        return TRUE;
    }

    /**
     * método que faz pesquisa no banco para preencher o autocomplete
     * @return json
     */
    public function findNumeroDigitalAction()
    {
        // desabilitando layout
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params, 'search');
        $res = $service = $this->getService()->findNumeroDigital($dto);
        $this->_helper->json($res);
    }

    /**
     * método que faz pesquisa no banco para preencher o autocomplete
     * @return json
     */
    public function searchTextoComplementarAction()
    {
        // desabilitando layout
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $dto = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $res = $service = $this->getService()->findTextoComplemetar($dto);
        $this->_helper->json($res);
    }

    /**
     * Método efetua a busca dos funcionários pertencentes ao ICMBio
     * @return void
     */
    public function funcionarioIcmbioAction()
    {
        $dtoFuncSearch = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $arrFunc = $this->getService('processo')->funcionarioIcmbio($dtoFuncSearch);
        $this->getHelper('json')->sendJson($arrFunc);
    }

    public function listInteressadosAction()
    {
        $this->getHelper('layout')->disableLayout();
        $params = $this->_getAllParams();
        $params['sqPessoaFuncao'] = \Core_Configuration::getSgdocePessoaFuncaoInteressado();

        $configArray = array(
            'columns' => array(
                array(
                    'alias' => 'p.noPessoa',
                    'alias' => 'p.nuCpfCnpjPassaporte',
                ),
            )
        );

        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);

        $this->view->dto = Core_Dto::factoryFromData($params, 'search');

        $this->view->result = $this->getService('Pessoa')->listGridInteressado($this->view->dto);
    }

    public function modalInteressadoAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->view->tipoPessoa = $this->getService('TipoPessoa')->getComboDefault(array());
        $this->view->sqArtefato = $this->_getParam('sqArtefato');
    }

    public function listMaterialAction()
    {
        $this->getHelper('layout')->disableLayout();
        $params = $this->_getAllParams();
        $params['sqPessoaFuncao'] = \Core_Configuration::getSgdocePessoaFuncaoInteressado();
        $configArray = array(
                'columns' => array(
                        array(
                                'alias' => 'p.noPessoa'
                        ),
                )
        );

        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);

        $this->view->dto = Core_Dto::factoryFromData($params, 'search');

        $this->view->result = $this->getService('Pessoa')->listGridInteressado($this->view->dto);
    }

    public function listVincularDocumentoAction()
    {
        $this->getHelper('layout')->disableLayout();
        $params = $this->_getAllParams();
        $params['sqPessoaFuncao'] = \Core_Configuration::getSgdocePessoaFuncaoInteressado();
        $configArray = array(
                'columns' => array(
                        array(
                                'alias' => 'p.noPessoa',
                                'alias' => 'a.sqArtefato'
                        ),
                )
        );

        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getService('Pessoa')->listGridDocumento($this->view->dto);
    }

    public function deleteInteressadoAction()
    {
        $this->getService('Pessoa')->delete($this->_getParam('id'));
        $this->getService()->finish();
        $this->_helper->json(array());
    }

    public function searchPessoaInteressadaAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params, 'search');
        $result = $this->getService('PessoaInterassadaArtefato')->searchPessoaInteressada($dto);

        return $this->_helper->json($result);
    }

    public function canieCavernaAutoCompleteAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params, 'search');
        $service = $this->getService('VwIntegracaoSistema')->canieCavernaAutoComplete($dto);
        $this->_helper->json($service);
    }

    /**
     * Metódo para salvar destinatario de acordo com o tipo.
     */
    public function abaSelecionada($params,$dtoSearch)
    {
        //pessoaArtefato
        switch ($params['tipoPessoaAba']) {
            case '1':
                $params = $this->getService('EnderecoSgdoce')->saveExtraDadosPessoa($params,$dtoSearch);
                if(!isset($params['sqTratamentoVocativo'])){
                    if(strlen($params['sqTratamento']) >= 1){
                        $entityTratamento = $this->getService('TratamentoVocativo')->findBy(array(
                                'sqTratamento' => $params['sqTratamento'], 'sqVocativo' => NULL
                        ));
                        if(count($entityTratamento) >= 1){
                            $params['sqTratamentoVocativo'] = $entityTratamento[0]->getSqTratamentoVocativo();
                        }
                    }
                }

                $dtoPessoaArtefato = Core_Dto::factoryFromData($params,
                        'entity', array('entity'=> 'Sgdoce\Model\Entity\PessoaArtefato',
                                'mapping' => array(
                                        'sqArtefato'            => 'Sgdoce\Model\Entity\Artefato'
                                        ,'sqPessoaSgdoce'       => 'Sgdoce\Model\Entity\PessoaSgdoce'
                                        ,'sqEnderecoSgdoce'     => 'Sgdoce\Model\Entity\EnderecoSgdoce'
                                        ,'sqTelefoneSgdoce'     => 'Sgdoce\Model\Entity\TelefoneSgdoce'
                                        ,'sqEmailSgdoce'        => 'Sgdoce\Model\Entity\EmailSgdoce'
                                        ,'sqPessoaFuncao'       => 'Sgdoce\Model\Entity\PessoaFuncao'
                                        ,'sqTratamentoVocativo' => 'Sgdoce\Model\Entity\TratamentoVocativo')));

                $res = $this->getService('PessoaArtefato')->savePessoaArtefato($dtoPessoaArtefato);
                break;
            case '2':
                $dtoPessoaArtefato = Core_Dto::factoryFromData($params, 'entity',
                array('entity' => 'Sgdoce\Model\Entity\PessoaInteressadaArtefato',
                'mapping' => array(
                'sqArtefato' => 'Sgdoce\Model\Entity\Artefato'
                        , 'sqPessoaSgdoce' => 'Sgdoce\Model\Entity\PessoaSgdoce')));

                $res = $this->getService('PessoaInterassadaArtefato')->savePessoaInteressada($dtoPessoaArtefato);
                break;
            case '3':
                $params = $this->getService('PessoaUnidadeOrg')->mountDtoUnidadeSgdoce($params,$dtoSearch);
                $pessoaAssinanteCargo = $this->getService('PessoaAssinanteArtefato')->findCargoAssinante($dtoSearch);

                $params['noCargoAssinante'] = '';
                if($pessoaAssinanteCargo){
                    $params['noCargoAssinante'] = $pessoaAssinanteCargo->getSqCargo()->getNoCargo();
                }

                $dtoPessoaAssinatura = Core_Dto::factoryFromData($params,
                        'entity', array('entity'=> 'Sgdoce\Model\Entity\PessoaAssinanteArtefato',
                                'mapping' => array(
                                        'sqArtefato'            => 'Sgdoce\Model\Entity\Artefato'
                                        ,'sqPessoaUnidadeOrg'       => 'Sgdoce\Model\Entity\PessoaUnidadeOrg'
                                        ,'sqTipoAssinante'      => 'Sgdoce\Model\Entity\TipoAssinante')));

                $dto = '';

                $res = $this->getService('PessoaAssinanteArtefato')->savePessoaAssinatura($dtoPessoaAssinatura,$dto);

                if(isset($params['sqTipoMotivacao']) && ($params['sqTipoMotivacao'] != '') ){
                    $this->getService('Motivacao')->saveAssinatura($params);
                }
                break;
        }
    }

    /**
     * Método que prepara valores para inserir no arquivo pdf da minuta para visualização
     */
    public function visualizarMinutaAction()
    {
        $this->_helper->layout()->feriados = $this->getService()->getFeriados();
        $sqArtefato     = $this->_getParam('sqArtefato');
        $viewAssinatura = $this->_getParam('viewFor');
        $registry = \Zend_Registry::get('configs');
        $options  = array('path' => $registry['folder']['visualizaMinuta']);
        // mapeamento da entidade 'pessoa'
        $dtoOptionArtefato = array('entity'  => 'Sgdoce\Model\Entity\Artefato');
        // transforma o array 'artefato' em objeto
        $dtoEntityArtefato = Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'entity',
                $dtoOptionArtefato);
        $artefatoMinuta = $this->getService('ArtefatoMinuta')->find($sqArtefato);
        $dtoEntityModelo = Core_Dto::factoryFromData(array('sqModeloDocumento' =>
                $artefatoMinuta->getSqModeloDocumento()->getSqModeloDocumento()), 'entity',
                array('entity'=> 'Sgdoce\Model\Entity\ModeloDocumento'));
        $this->view->view = $this->_getParam('view');

        try {
            if ($viewAssinatura) {
                $data = $this->getService()->createDocView($dtoEntityArtefato, $dtoEntityModelo);
            } else {
                $data = $this->getService()->createDocPdf($dtoEntityArtefato, $dtoEntityModelo);
            }
            if (!$viewAssinatura) {
                $this->_helper->download($data, $options);
            } else {
                $modelo = $this->getService()->getModeloMinuta($dtoEntityModelo);
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
        } catch (\HTML2PDF_exception $exc) {
            $this->getMessaging()->addErrorMessage('Favor retirar os caracteres "<" e ">" dos dados informados no
                    artefato.');
            return $this->_redirectActionDefault('index/view/' . $this->_getParam('view'));
        } catch (\Exception $exc) {
            $this->getMessaging()->addErrorMessage($exc->getMessage());
            return $this->_redirectActionDefault('index/view/' . $this->_getParam('view'));
        }
    }

        /**
        * Realiza o render da imagem
        */
        public function renderImageAction()
        {
            $config = \Zend_Registry::get('configs');
            $path   = $config['upload']['rodape']['destination'];

            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $params = $this->_getAllParams();

            $entity = $this->getService('Artefato')->findBy(array(
                'sqArtefato' => $params['sqArtefato']
            ));
            $enderecoImagem = $path . '/' . $entity[0]->getDeImagemRodape();
            $dto = new \Core_Dto_Search(
                array(
                    'resize' => true,
                    'width'  => 120,
                    'height' => 120
                )
            );

            return $this->showImage($dto, $enderecoImagem);
        }

        public function showImage($dto, $enderecoImagem)
        {
            return \Artefato\Service\Imagem::showImage($dto, $enderecoImagem);
        }

    /**
     * Metódo para recuperar o modelo.
     */
    public function chekModelo ($params)
    {
        if(isset($params['view'])){
            $this->view->tipoVisualizacao    = $params['view'];
        }
        if(isset($params['id'])){

            $params['sqArtefato'] = $params['id'];
            //retorna grau de acesso pre definido no modelo de documento
            $dtoSearchModelo = Core_Dto::factoryFromData($params, 'search');

            $modeloDocumento = $this->hasModeloDocumentoCadastrado($dtoSearchModelo,TRUE);

            if(!$modeloDocumento){
                $artefatoMinuta  = $this->getService('ArtefatoMinuta')->findBy(array(
                    'sqArtefato'=> $dtoSearchModelo->getSqArtefato()
                ));
                $modeloDocumento['sqModeloDocumento'] = $artefatoMinuta[0]->getSqModeloDocumento()
                ->getSqModeloDocumento();
            }
            $dtoResult = Core_Dto::factoryFromData(array('sqModeloDocumento' => $modeloDocumento['sqModeloDocumento']), 'search');

            $this->view->grau = $this->getService('ModeloMinuta')->getGrauAcesso($dtoResult);
            //rodape tabela pessoa
            $this->view->rodape  = $this->getService('Pessoa')->getPessoaArtefatoRodape($dtoSearchModelo);
        }
    }

    /**
     * Retorna o download com o Modelo de Minuta
     */
    public function downloadMaterialAction()
    {
        $codigo   = $this->_getParam('codigo');
        $anexo = $this->getService('AnexoArtefatoVinculo')->find($codigo);
        $path  = explode('/',$anexo->getDeCaminhoAnexo());
        $registry = \Zend_Registry::get('configs');
        $options  = array('path' => $registry['upload']['material']);
        $file = "{$path['4']}";
        $this->_helper->download($file, $options);
    }
    
    public function podeEditarArtefatoAction()
    {
        $params = $this->_getAllParams();        
        $sqArtefato = $params['id'];
        
        $dtoCheckEdit = Core_Dto::factoryFromData(array(
                            'sqArtefato' => $params['id'],
                            'sqPessoa' => \Core_Integration_Sica_User::getPersonId()),
                        'search');
        
        $return['canEdit'] = TRUE;
        $return['msg'] = '';
        $entArtefato = $this->getService()->find($dtoCheckEdit->getSqArtefato());        
        
        //verifica se o artefato pode ser editado
        if (!$this->getService('AreaTrabalho')->canEditArtefact($dtoCheckEdit, $entArtefato->isProcesso())) {            
            $return['canEdit'] = FALSE;
            if( $this->_isUserSgi() ){
                $return['msg'] = \Core_Registry::getMessage()->translate('MN183');            
            } else {
                $return['msg'] = \Core_Registry::getMessage()->translate('MN182');            
            }
        }
        return $this->_helper->json($return);
    }
}
