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
 * Classe para Controller de ConsultarArtefato
 *
 * @package  Artefato
 * @category Controller
 * @name     ConsultarArtefato
 * @version  1.0.0
 */
class Artefato_ConsultarArtefatoController extends \Core_Controller_Action_CrudDto
{

    /**
     * Serviço
     * @var string
     */
    protected $_service = 'VwConsultaArtefato';

    public function getPersonId()
    {
        return \Core_Integration_Sica_User::getPersonId();
    }

    /**
     * Monta as combos default para os tipos de processos
     * @return void
     */
    protected function combos()
    {
        $this->view->ambito = array('Outros', 'Federal');
        $this->view->estado = $this->getService('Estado')->comboEstado();
        $this->view->tipoPessoa = $this->getService('TipoPessoa')->getComboDefault(array());
        $this->view->temaVinculado = array('Cavernas', 'Espécies', 'Empreendimentos', 'Unidades de Conservação');
        $this->view->tipoDocumento = $this->getService('TipoDocumento')->getComboDefault(array());
        $this->view->tipoArtefato = $this->getService('TipoArtefato')->listItems(array());
        $this->view->assunto = $this->getService('Assunto')->comboAssunto(array());
    }

    /**
     * Monta as combos default para os tipos de processos
     * @return void
     */
    public function consultarArtefatoPadraoAction()
    {
        $this->_helper->viewRenderer->setRender('form-pesquisa-padrao');
        $this->view->update = $this->_getParam("update");
    }

    /**
     * Método que retorna os dados da pesquisa avançada
     */
    public function consultarArtefatoAvancadoAction()
    {
        $this->_helper->viewRenderer->setRender('form-pesquisa-avancada');
        $this->view->itemsTipoDocumento = $this->getService('TipoDocumento')->searcTipoDocumento();
        $this->view->tipoPrioridade = $this->getService('Prioridade')->listItems();
    }

    public function getNuNup()
    {
        $dtoOrigem  = Core_Dto::factoryFromData(array('sqProfissional' => $this->getPersonId()), 'search');
        $unidadeOrg = $this->getService('Dossie')->unidadeOrigemPessoa($dtoOrigem);

        if(isset($unidadeOrg) && $unidadeOrg) {
            $unidadeOrg = $unidadeOrg->getSqUnidadeExercicio();

            return $unidadeOrg->getNuNup();
        }

        return NULL;
    }
    /**
     * Método que retorna os dados a pesquisa padrão
     */
    public function listConsultarArtefatoPadraoAction()
    {
        $this->getHelper('layout')->disableLayout();

        $this->view->nuNup = $this->getNuNup();

        $this->_helper->viewRenderer->setRender('list-consultar-artefato-padrao');

        $params = $this->_getAllParams();

        $configArray = $this->getConfigListConsultaArtefatoPadrao();
        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getService()->listGridConsultaArtefatoPadrao($this->view->dto);
    }

    /**
     * Dados que retorna os dados da lista da grid avançada
     */
    public function listConsultarArtefatoAvancadoAction()
    {
        $this->getHelper('layout')->disableLayout();
        $tipoInformacao = $this->_getParam('tipoInformacao');
        if ($tipoInformacao != \Sgdoce_Constants::INFO_AVANCADA && !empty($tipoInformacao)) {
            $this->_helper->viewRenderer->setRender('list-consultar-artefato-avancado-tipo-info');
        } else {
            $this->_helper->viewRenderer->setRender('list-consultar-artefato-avancado');
        }

        $this->view->nuNup = $this->getNuNup();

        $params = $this->_getAllParams();
        $configArray = $this->getConfigListConsultaArtefatoAvancado();
        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getService()->listGridConsultaArtefatoAvancado($this->view->dto);

    }

    /**
     * Método que retorna os dados da pessoa_interessada
     * @return json
     */
    public function searchPessoaInteressadaAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params, 'search');
        $result = $this->getService('PessoaInterassadaArtefato')->searchPessoaInteressada($dto);

        return $this->_helper->json($result);
    }

    /**
     * Método que retorna os dados do título do dossie
     * @return json
     */
    public function searchTituloDossieAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params, 'search');
        $result = $this->getService()->searchTituloDossie($dto);

        return $this->_helper->json($result);
    }

    /**
     * Método que retorna os dados da referencia
     * @return json
     */
    public function searchReferenciaAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params, 'search');
        $result = $this->getService('ArtefatoVinculo')->searchReferencia($dto);

        return $this->_helper->json($result);
    }

    public function searchTipoDocumentoAction()
    {
        // desabilitando layout e evitando rendereizacao da action
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $this->getHelper('json')->sendJson($this->getService('Documento')->tipoDocumento($params));
    }

    /**
     * Método que retorna os dados de configuração da list grid padrão
     * @return array
     */
    public function getConfigListConsultaArtefatoPadrao()
    {
        return array(
            0 => array('alias' => 'vwca.noTipoArtefato'),
            1 => array('alias' => 'vwca.nuDigital'),
            2 => array('alias' => 'vwca.noTipoDocumento'),
            3 => array('alias' => 'vwca.nuArtefato'),
            4 => array('alias' => 'vwca.noPessoaOrigem'),
            5 => array('alias' => 'vwca.txAssunto'),
            6 => array('alias' => 'vwca.noPessoaInteressada'),
            7 => array('alias' => 'vwca.nuCpfCnpjPassaporteOrigem'),
            8 => array('alias' => 'vwca.dtPrazo'),
            9 => array('alias' => 'vwca.noPrioridade')
        );
    }

    /**
     * Método que retorna os dados de configuração da list grid avançada
     * @return array
     */
    public function getConfigListConsultaArtefatoAvancado()
    {

        return array(
            0 => array('alias' => 'vwca.noTipoArtefato'),
            1 => array('alias' => 'vwca.nuDigital'),
            2 => array('alias' => 'vwca.noTipoDocumento'),
            3 => array('alias' => 'vwca.nuArtefato'),
            4 => array('alias' => 'vwca.noPessoaOrigem'),
            5 => array('alias' => 'vwca.txAssunto'),
            6 => array('alias' => 'vwca.noPessoaInteressada'),
            7 => array('alias' => 'vwca.nuCpfCnpjPassaporteOrigem'),
            8 => array('alias' => 'vwca.dtPrazo'),
            9 => array('alias' => 'vwca.noPrioridade')
        );
    }

}
