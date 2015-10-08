<?php

use Doctrine\ORM\Mapping\Entity;

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
 * Classe para Controller de Artefato
 *
 * @package    Artefato
 * @category   Controller
 * @name       Documento
 * @version    1.0.0
 */
class DocumentoControllerExtensao extends Artefato_ArtefatoController
{

    /**
     * Retorna lista combos para Documento Eltronico
     */
    public function getCombo()
    {
        $this->view->assunto = $this->getService('Assunto')->comboAssunto();
        $this->view->grauAcesso = $this->getService('GrauAcesso')->listItensGrauAcesso();
        $this->view->tipoPessoa = $this->getService('TipoPessoa')->comboTipoPessoa();
        $this->view->tipoArtefato = $this->getService('TipoArtefato')->listItems();
        $this->view->tipoDocumento = $this->getService('TipoDoc')->listItems();
        $this->view->tipoPrioridade = $this->getService('Prioridade')->listItems();

        # carrega cargos para o cadastro de documento no padrão array('no_cargo'=>'no_cargo')
        $this->view->cargo  = $this->getService('VwCargo')->comboCargoCadastroDocumento();
        # carrega funções para o cadastro de documento no padrão array('no_funcao'=>'no_funcao')
        $this->view->funcao = $this->getService('Funcao')->comboFuncaoCadastroDocumento();
    }

    public function getUser()
    {
        // retorno de valor
        return \Core_Integration_Sica_User::get();
    }

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
        
        $entityArtefatoAnterior = null;        
        if( $dto->getSqArtefato() ) {
            $entityArtefatoAnterior = $this->getService('Artefato')->find($dto->getSqArtefato());
        }

        return array($dto, $entityPrioridade, $entityGrauArtefato, $entityArtefatoAnterior);
    }

    protected function editActionExtension()
    {
        $params = $this->_getAllParams();

        $dtoCheckEdit = Core_Dto::factoryFromData(array(
                            'sqArtefato' => $params['id'],
                            'sqPessoa' => \Core_Integration_Sica_User::getPersonId()),
                        'search');

        //verifica se o artefato pode ser editado
        if (!$this->getService('AreaTrabalho')->canEditArtefact($dtoCheckEdit)) {
            $this->getMessaging()->addErrorMessage(
                    sprintf(\Core_Registry::getMessage()->translate('MN154'),
                            $this->view->data->getNuDigital()->getNuEtiqueta()));
            $this->_redirect('/artefato/area-trabalho');
        }

        $params['sqArtefato'] = $params['id'];
        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $this->view->redirect = $params['view'];
        $this->view->user = $this->getUser();
        $this->getCombo();

        $this->view->isLoteEletronico = false;
        $this->view->eDigital = $this->view->data->getNuDigital();
        $this->view->isSIC    = $this->view->data->getSqTipoDocumento()->getSqTipoDocumento() == \Core_Configuration::getSgdoceTipoDocumentoSic();
        $this->view->docSIC   = $this->view->data->getSqTipoDocumento();

        $dto = Core_Dto::factoryFromData(array(
            'sqPessoaCorporativo' => \Core_Configuration::getSgdoceUnidadeCgu(),
            'sqTipoPessoa'=>\Core_Configuration::getCorpTipoPessoaUnidadeExt()),
        'search');
        $this->view->CGU = (!$this->view->isSIC) ? null : $this->getService('Pessoa')->getPessoa($dto);

        if(null === $this->view->data->getSqLoteEtiqueta()){
            $this->view->isLoteEletronico = true;
        }else{
            $sqTipoEtiqueta = $this->view->data->getNuDigital()->getSqLoteEtiqueta()->getSqTipoEtiqueta()->getSqTipoEtiqueta();
            if($sqTipoEtiqueta == \Core_Configuration::getSgdoceTipoEtiquetaEletronica()){
                $this->view->isLoteEletronico = true;
            }
        }

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
            $procedenciaInterno = $this->view->dadosOrigem[1]; //1 = interno  -- 3 = externo
            if ($procedenciaInterno == 1) {
                $disabledProcedencia = 'chekProcedenciaExterno';
            } else {
                $disabledProcedencia = 'chekProcedenciaInterno';
            }

            $this->view->disabledProcedencia = $disabledProcedencia;
        }
        
    }
}
