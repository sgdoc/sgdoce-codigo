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
 * Classe para Controller de Processo
 *
 * @package  Artefato
 * @category Controller
 * @name     Artefato
 * @version     1.0.0
 */
class ArtefatoControllerExtensao extends \Core_Controller_Action_CrudDto
{
    /**
     * Recupera os dados da sessão do usuário logado.
     * @return array
     */
    public function recuperaDadosUsuarioLogado()
    {
        // retorno de valor
        return \Core_Integration_Sica_User::get();
    }

    /**
     * Metodo resposavel por retornar a pessoa vinculada ao artefato e verifica se essa pessoa eh interna ou externa
     *
     * return array object
     */
    protected function _dadosPessoaDocumento($dto, $tipoPessoa)
    {
        $result = $this->getService('PessoaArtefato')->getPessoaArtefato($dto, $tipoPessoa);

        if ($result) {
            $arrCheck = array(
                \Core_Configuration::getSgdocePessoaFuncaoOrigem(),
                \Core_Configuration::getSgdocePessoaFuncaoDestinatario()
            );

            if (in_array($tipoPessoa, $arrCheck)) {
                $procedencia = $result[0]->getStProcedencia();
                if($procedencia){
                    $result[1] = \Sgdoce_Constants::PROCEDENCIA_INTERNA;
                }

                if(FALSE === $procedencia){
                    $result[1] = \Sgdoce_Constants::PROCEDENCIA_EXTERNA;
                }
                if(is_null($procedencia)){
                    $result[1] = NULL;
                }
            }else{
                trigger_error('Método usado apenas para dados de Pessoa ORIGEM/DESTINO', E_USER_ERROR);
            }
        }
        return $result;
    }

    /**
     * Método que retorna os dados da Origem
     * @param unknown_type $dtoArtefato
     */
    public function returnDadosOrigem($dtoArtefato)
    {
        return $this->_dadosPessoaDocumento($dtoArtefato, \Core_Configuration::getSgdocePessoaFuncaoOrigem());
    }

    public function mountDto($paramsVinculo)
    {
           $entity['entity'] =   'Sgdoce\Model\Entity\\' . $paramsVinculo['entity'];
           $entity['chave']  =   $paramsVinculo['chave'];
           $entity['view']   =   'Sgdoce\Model\Entity\\' . $paramsVinculo['view'];

           $optionsDto = array(
                'entity' => $entity['entity'],
                'mapping' => array('sqArtefato'  => 'Sgdoce\Model\Entity\Artefato'
                                    ,$entity['chave']  => array('codigo' => $entity['view'])));

           return Core_Dto::factoryFromData($paramsVinculo, 'entity', $optionsDto);
    }

    /**
     *retorna dados da grid
     */
    public function getResultListTemaTratado(\Core_Dto_Search $dtoSearch)
    {
        return $this->getService()->listGridTemaTratado($dtoSearch);
    }

    /**
     *metodo que ordena grid
     */
    public function getConfigListTemaTratado()
    {
        $array = array('columns' => array(0 => array('alias' => 'vi.nome'),
                            1 => array('alias' => 'vi.tipo')
                ));

        return $array;
    }

    /**
     * Ação da exclusao de vinculo
     */
    public function deletePecaAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $dto = Core_Dto::factoryFromData(array('sqArtefatoVinculo' => $this->_getParam('id')
                                                ,'sqArtefato' => $this->_getParam('sqArtefato')
                                                ,'inOriginal' => $this->_getParam('inOriginal')), 'search');

        $return = $this->getService('ArtefatoVinculo')->findArtefatoVinculo($dto);
        if($return){
            $this->_deleteArtefatoVinculo($dto);
            $this->_helper->json('true');
        }
        $this->_helper->json('false');
    }

    /**
     * Ação da exclusao de vinculo
     */
    public function deleteReferenciaAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $data = $this->_getParam('id');
        $dto = Core_Dto::factoryFromData(array('sqArtefatoVinculo' => $data), 'search');
        $this->_deleteArtefatoVinculo($dto);
    }

    /**
     * Executa exclusao de vinculo apartir da chave sq_artefato_vinculo
     *
     * @param Core_Dto_Search $dto
     * @return \ArtefatoControllerExtensao
     */
    private function _deleteArtefatoVinculo(Core_Dto_Search $dto)
    {
        $this->getService('Dossie')->deleteArtefatoVinculo($dto);
        return $this;
    }

    /**
     * Método que carrega a view Capa do processo
     */
    public function capaProcessoAction()
    {
        $this->getHelper('layout')->disableLayout();

        $data['sqArtefato'] = $this->_getParam("sqArtefato");
        $dtoArtefato        = Core_Dto::factoryFromData($data, 'search');

        $this->view->cabecalho   = $this->getService('Cabecalho')->find(\Core_Configuration::getSgdoceSqCabecalho_1());
        $this->view->artefato    = $this->getService('Artefato')->findVisualizarArtefato($dtoArtefato);
        $this->view->temaTratado = $this->getService('ProcessoCaverna')->listGridCapaProcesso($dtoArtefato);

        $this->view->dadosInteressado = $this->getService('PessoaInterassadaArtefato')->getPessoaInteressadaArtefato($dtoArtefato);
        $entityArtefatoProcesso       = $this->getService('ArtefatoProcesso')->find($data['sqArtefato']);
        $this->view->sqEstado         = $entityArtefatoProcesso->getSqEstado() instanceof \Sgdoce\Model\Entity\VwEstado ?
            $entityArtefatoProcesso->getSqEstado()->getSqEstado() : $entityArtefatoProcesso->getSqEstado();
        $this->view->coAmbitoProcesso = $entityArtefatoProcesso->getCoAmbitoProcesso();
        $this->view->nuPaginaProcesso = $entityArtefatoProcesso->getNuPaginaProcesso();
        $this->view->autuar           = $this->_getParam("autuar");

        $artefatoPai = $this->_getParam("artefatoPai");
        //para o autuar Digital/Tipo/Numero e Origem
        if($this->view->autuar === 'true'){
            $data['sqArtefato'] = $this->_getParam("artefatoPai");
            $dtoArtefatoPai = Core_Dto::factoryFromData($data, 'search');
            $this->view->dadosOrigem = $this->returnDadosOrigem($dtoArtefatoPai);
            $this->view->artefatoPai = $this->getService('Artefato')->findVisualizarArtefato($dtoArtefatoPai);
        }

        //para o autuar Digital/Tipo/Numero e Origem
        if(!$artefatoPai){
            $criteria = array(
                'sqArtefatoPai'       => $dtoArtefato->getSqArtefato(),
                'sqTipoVinculoArtefato' => \Core_Configuration::getSgdoceTipoVinculoArtefatoInsercao(),
                'dtRemocaoVinculo'      => NULL
            );
            $artefatoVinculo = $this->getService('ArtefatoVinculo')->findBy($criteria);

            if(count($artefatoVinculo) > 0) {
                $data['sqArtefato'] = $artefatoVinculo[0]->getSqArtefatoFilho()->getSqArtefato();
                $dtoArtefatoPai     = Core_Dto::factoryFromData($data, 'search');

                $this->view->dadosOrigem = $this->returnDadosOrigem($dtoArtefatoPai);
                $this->view->artefatoPai = $this->getService('Artefato')->findVisualizarArtefato($dtoArtefatoPai);
            }
        }
    }

    /**
     * Metódo que verifica se o modelo está cadastrado.
     * @return json
     */
    public function saveCapaAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $data = $this->_request->getPost();
        $data = $this->getService('MinutaEletronica')->fixNewlines($data);
        $data = $this->getService()->validarDataPrazo($data);
        $this->getRequest()->setPost($data);
        $this->_save();
        $this->getService()->finish();
        $this->_helper->json(array('sucess' => 'true'));
    }



    /**
     * Metódo para parametrizar
     * @param array $params, array $dtoSearch
     * @return array
     */
    public function parametrizar($params,$dtoSearch)
    {
        switch ($params['checkCorporativo']) {
            case \Sgdoce_Constants::PESSOA_CORPORATIVO: //pessoa corporativo
                $endereco = '';
                if($dtoSearch->getSqEndereco()) {
                    $endereco = $this->getService('VwEndereco')->findId($dtoSearch->getSqEndereco());
                } else {
                    $endereco = $this->getService('VwEndereco')->findEndereco($dtoSearch->getSqPessoaCorporativo());
                }

                $cep = str_replace('.','',str_replace('-', '',$endereco->getSqCep()));
                $params['txEndereco']     = $endereco->getTxEndereco();
                $params['coCep']          = $cep;
                $params['sqMunicipio']    = $endereco->getSqMunicipio()->getSqMunicipio();
                $params['sqTipoEndereco'] = $endereco->getSqTipoEndereco()->getSqTipoEndereco();
                $params['noBairro']       = $endereco->getNoBairro();
                $params['nuEndereco']     = $endereco->getNuEndereco();
                $params['txComplemento']  = $endereco->getTxComplemento();

                break;
            case \Sgdoce_Constants::PESSOA_SGDOCE: //pessoa sgdoce
                $params = $this->casePessoaSgdoce($params, $dtoSearch);
                break;
        }

        if($params['sqTipoEndereco'] == ''){
            $params['sqTipoEndereco'] = \Core_Configuration::getSgdoceTipoEnderecoResidencial();
        }

        return $params;
    }

    public function casePessoaSgdoce(array $params, $dtoSearch)
    {
        $dadosSgdoce              = $this->getService('Pessoa')->findPessoaDestinatarioArtefato($dtoSearch);

        $params['txEndereco']     = NULL;
        $params['coCep']          = NULL;
        $params['sqMunicipio']    = NULL;
        $params['sqTipoEndereco'] = NULL;
        $params['noBairro']       = NULL;
        $params['nuEndereco']     = NULL;
        $params['txComplemento']  = NULL;

        if(isset($dadosSgdoce[0]['txEndereco'])){
            $params['txEndereco'] = $dadosSgdoce[0]['txEndereco'];
        }
        if(isset($dadosSgdoce[0]['coCep'])){
            $params['coCep'] = $dadosSgdoce[0]['coCep'];
        }
        if(isset($dadosSgdoce[0]['sqMunicipio'])){
            $params['sqMunicipio'] = $dadosSgdoce[0]['sqMunicipio'];
        }
        if(isset($dadosSgdoce[0]['sqTipoEndereco'])){
            $params['sqTipoEndereco'] = $dadosSgdoce[0]['sqTipoEndereco'];
        }
        if(isset($dadosSgdoce[0]['noBairro'])){
            $params['noBairro'] = $dadosSgdoce[0]['noBairro'];
        }
        if(isset($dadosSgdoce[0]['nuEndereco'])){
            $params['nuEndereco'] = $dadosSgdoce[0]['nuEndereco'];
        }
        if(isset($dadosSgdoce[0]['txComplemento'])){
            $params['txComplemento'] = $dadosSgdoce[0]['txComplemento'];
        }

        return $params;
    }
}
