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
class Migracao_DocumentoController extends \Core_Controller_Action_CrudDto
{
    /**
     * @var string
     */
    protected $_service = 'DocumentoMigracao';

    /**
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => 'Sgdoce\Model\Entity\Artefato',
        'mapping' => array(
            'sqFecho' => 'Sgdoce\Model\Entity\Fecho',
            'sqTipoDocumento' => 'Sgdoce\Model\Entity\TipoDocumento',
            'sqLoteEtiqueta' => 'Sgdoce\Model\Entity\EtiquetaNupSiorg',
            'nuDigital' => array('nuEtiqueta' => 'Sgdoce\Model\Entity\EtiquetaNupSiorg'),
//            'sqPessoaRecebimento' => 'Sgdoce\Model\Entity\VwPessoa',
        )
    );

    /**
     * @return void
     */
    public function editAction()
    {
        $id = $this->_getParam('id');

        $url = $this->getRequest()
                    ->getParam('back', false);

        $this->view->urlBack        = FALSE;
        $this->view->controllerBack = NULL;
        $this->view->caixa          = NULL;

        if( $url ) {
            $url = str_replace(".", "/", $url);
            $this->view->urlBack = $url;
            $url = substr($url, 1);
            $params = explode("/", $url);
            $this->view->controllerBack = next($params);
            $this->view->caixa          = end($params);
        }

        $dtoSearch = \Core_Dto::factoryFromData(array('sqArtefato' => $id), 'search');
        if( !$this->getService("Artefato")->isInconsistent($dtoSearch, FALSE, TRUE) ) {
            $this->getMessaging()->addErrorMessage('Documento já corrigido!', 'User');
            $this->_redirect( $this->view->urlBack );
        }

        parent::editAction();

        $this->editActionExtension();

        $this->view->arrWithoutSignature = $this->getService('Documento')->getTipoDocumentoSemAssinatura();
        $this->view->arrWithDuplicityCheck = $this->getService('Documento')->getTipoDocumentoComValidacaoDuplicidade();

        if( $this->view->data->getSqTipoDocumento() ) {
            $sqTipoDocumento = $this->view->data->getSqTipoDocumento()->getSqTipoDocumento();
            $this->view->isDocWVDuplicity = in_array($sqTipoDocumento, $this->view->arrWithDuplicityCheck);
        }

        $this->getMessaging()->dispatchPackets();
    }


    public function saveAction ()
    {
        try {
            parent::saveAction();
        } catch (\Core_Exception_ServiceLayer_Verification $e) {
            $this->_helper->json(array('error' => true, 'errorType' => 'Alerta', 'msg' => $e->getMessage()));
        } catch (\Exception $e) {
            $this->_helper->json(array('error' => true, 'errorType' => 'Erro', 'msg' => $e->getMessage()));
        }
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
        }
        return $result;
    }

    /**
     * Retorna lista combos para Documento Eltronico
     *
     * @return void
     */
    public function getCombo()
    {
        $this->view->assunto = $this->getService('Assunto')->comboAssunto();
        $this->view->grauAcesso = $this->getService('GrauAcesso')->listItensGrauAcesso();
        $this->view->tipoPessoa = $this->getService('TipoPessoa')->comboTipoPessoa();
        $this->view->tipoArtefato = $this->getService('TipoArtefato')->listItems();
        $this->view->tipoDocumento = $this->getService('TipoDoc')->listItems();
        $this->view->tipoPrioridade = $this->getService('Prioridade')->listItems();

        /*carrega cargos para o cadastro de documento no padrão array('no_cargo'=>'no_cargo')*/
        $this->view->cargo = $this->getService('VwCargo')->comboCargoCadastroDocumento();
    }

    /**
     * @return array
     */
    public function getUser()
    {
        // retorno de valor
        return \Core_Integration_Sica_User::get();
    }

    /**
     * @param array $dados
     * @return mixed
     */
    public function returnNacionalidade($dados)
    {
        $sqPessoa = $dados[0]->getSqPessoaSgdoce()->getSqPessoaCorporativo()->getSqPessoa();
        $pessoaFisica = $this->getService('Pessoa')->findByPessoaFisica(array('sqPessoaFisica' => $sqPessoa));
        if ($pessoaFisica) {
            $sqNacionalidade = $pessoaFisica[0]->getSqNacionalidade() ? $pessoaFisica[0]->getSqNacionalidade()->getSqPais() : NULL;

            if ($sqNacionalidade == \Sgdoce_Constants::NACIONALIDADE_BRASILEIRA || $sqNacionalidade == NULL) {
                return \Sgdoce_Constants::NACIONALIDADE_BRASILEIRA;
            }
            return \Sgdoce_Constants::NACIONALIDADE_ESTRANGEIRA;
        }
    }

    /**
     * @param mixed $data
     * @return array
     */
    protected function _factoryParamsExtrasSave($data)
    {
        // tratando parametro
        $dto = Core_Dto::factoryFromData($data, 'search');

        $entityPrioridade = Core_Dto::factoryFromData($data, 'entity', array(
            'entity' => 'Sgdoce\Model\Entity\TipoPrioridade',
            'mapping' => array('sqPrioridade' => 'Sgdoce\Model\Entity\Prioridade')
        ));

        $entityGrauArtefato = Core_Dto::factoryFromData($data, 'entity', array(
            'entity' => 'Sgdoce\Model\Entity\GrauAcessoArtefato',
            'mapping' => array(
                'sqGrauAcesso' => 'Sgdoce\Model\Entity\GrauAcesso',
                'sqArtefato' => 'Sgdoce\Model\Entity\Artefato'
        )));

        return array($dto, $entityPrioridade, $entityGrauArtefato);
    }

    /**
     * @return void
     */
    protected function editActionExtension()
    {
        $params = $this->_getAllParams();

        $dtoCheckEdit = Core_Dto::factoryFromData(array(
                            'sqArtefato' => $params['id'],
                            'sqPessoa' => \Core_Integration_Sica_User::getPersonId()),
                        'search');

        //verifica se o artefato pode ser editado
//        if (!$this->getService('AreaTrabalho')->canEditArtefact($dtoCheckEdit)) {
//            $this->getMessaging()->addErrorMessage(
//                    sprintf(\Core_Registry::getMessage()->translate('MN154'),
//                            $this->view->data->getNuDigital()->getNuEtiqueta()));
//            $this->_redirect('/artefato/area-trabalho');
//        }

        $params['sqArtefato'] = $params['id'];
        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $this->view->redirect = $params['view'];
        $this->view->user = $this->getUser();
        $this->getCombo();

        $this->view->isLoteEletronico = false;
        $this->view->eDigital = $this->view->data->getNuDigital();

        if( $this->view->data->getSqTipoDocumento()  ) {
            $this->view->isSIC = $this->view->data->getSqTipoDocumento()->getSqTipoDocumento() == \Core_Configuration::getSgdoceTipoDocumentoSic();
        } else {
            $this->view->isSIC = false;
        }

        $this->view->docSIC   = $this->view->data->getSqTipoDocumento();

        $dtoSgdoceFisico = \Core_Dto::factoryFromData(array(
            'nuDigital' => $this->view->data->getNuDigital()->getNuEtiqueta(),
            'sqArtefato' => $this->view->data->getSqArtefato()
        ), 'search');

        $this->view->dataSgdocFisico = $this->getService()->getDataSgdocFisico($dtoSgdoceFisico);
        $this->view->dataSgdocFisico = current($this->view->dataSgdocFisico);

        $this->view->listProcedenciaFisico = array(
            'I' => 'Interno',
            'E' => 'Externo'
        );

        $this->view->nuInteressados = $this->getService('PessoaInterassadaArtefato')->countInteressadosArtefatoValido($dtoSgdoceFisico);

        $dto = Core_Dto::factoryFromData(array(
            'sqPessoaCorporativo' => \Core_Configuration::getSgdoceUnidadeCgu(),
            'sqTipoPessoa'=>\Core_Configuration::getCorpTipoPessoaUnidadeExt()),
        'search');
        $this->view->CGU = (!$this->view->isSIC) ? null : $this->getService('Pessoa')->getPessoa($dto);

        // retorno de valor para a view
        $this->view->tipoNavegacao = $dtoSearch->getA();
        $this->view->dataGrauAcesso = $this->getService('GrauAcessoArtefato')
                ->getGrauAcessoArtefato($dtoSearch);
        $criteria = array(
            'sqArtefato' => $params['sqArtefato'],
            'sqPessoaFuncao' => \Core_Configuration::getSgdocePessoaFuncaoAssinatura()
        );
        $this->view->assinanteArtefatoExterno = $this->getService('PessoaArtefato')->findOneBy($criteria);
        $this->view->assinanteArtefatoInterno = $this->getService('PessoaAssinanteArtefato')
                ->getAssinanteArtefato($dtoSearch);
        $this->view->dadosOrigem = self::_dadosPessoaDocumento($dtoSearch, \Core_Configuration::getSgdocePessoaFuncaoOrigem());
        $this->view->dadosDestino = self::_dadosPessoaDocumento($dtoSearch, \Core_Configuration::getSgdocePessoaFuncaoDestinatario());
        $this->view->result = $this->getService('AnexoArtefato')->listGridAnexos($dtoSearch);

        if ($this->view->dadosDestino) {
            $sqTipoPessoa = $this->view->dadosDestino[0]->getSqPessoaSgdoce()->getSqTipoPessoa()->getSqTipoPessoa();
            if ($sqTipoPessoa == \Core_Configuration::getCorpTipoPessoaFisica()) {
                $this->view->nacionalidadeDestino = $this->returnNacionalidade($this->view->dadosDestino);
            }
        }

        if ($this->view->dadosOrigem) {
            $sqTipoPessoa = $this->view->dadosOrigem[0]->getSqPessoaSgdoce()->getSqTipoPessoa()->getSqTipoPessoa();
            if ($sqTipoPessoa == \Core_Configuration::getCorpTipoPessoaFisica()) {
                $this->view->nacionalidadeOrigem = $this->returnNacionalidade($this->view->dadosOrigem);
            }
        }

        if ($this->view->data->getSqTipoArtefatoAssunto()->getSqTipoArtefatoAssunto()) {
            $this->_messageEdit = 'MN043'; #mensagem de alteracao
        }

        if(!$this->view->isSIC){
            $procedenciaInterno = (isset($this->view->dadosOrigem[1])) ? $this->view->dadosOrigem[1] : NULL; //1 = interno  -- 3 = externo
            if ($procedenciaInterno == 1) {
                $disabledProcedencia = 'chekProcedenciaExterno';
            } else {
                $disabledProcedencia = 'chekProcedenciaInterno';
            }

            $this->view->disabledProcedencia = $disabledProcedencia;
        }
    }
}