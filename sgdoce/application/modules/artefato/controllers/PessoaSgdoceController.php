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
 * Classe para Controller de Artefato Pessoa SGDOCE
 *
 * @package    PessoaSgdoce
 * @category   Controller
 * @name       PessoaSgdoce
 * @version    1.0.0
 */
class Artefato_PessoaSgdoceController extends \Core_Controller_Action_CrudDto
{

    /**
     * @var string
     */
    protected $_service = 'Pessoa';

    /**
     * Metódo que recupera a pessoa
     * @return json
     */
    public function searchPessoaInternaAction()
    {
        $params = $this->_getAllParams();
        $this->_helper->layout->disableLayout();
        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
        $service = $this->getService()->searchPessoaInterna($dtoSearch, 30);
        $this->_helper->json($service);
    }

    /**
     * Metódo que recupera a pessoa
     * @return json
     */
    public function searchPessoaExternaAction()
    {
        $this->_helper->layout->disableLayout();
        $dtoSearch = \Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $service = $this->getService()->searchPessoaExterna($dtoSearch, 30);
        $this->_helper->json($service);
    }

    /**
     * Método criado para subistituir o método getPessoaDadosAction.
     * Não foi removido o outro método para não impactar em funcionalidades que não conheço.
     */
    public function getDadosPessoaAction()
    {
        $params = $this->_getAllParams();
        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
        if($dtoSearch->getSqInteressado()&& $dtoSearch->getSqInteressado() === 'interno') {
            $service = $this->getService()->getPessoaInternoDados($dtoSearch);
            $this->_helper->json($service);
        } else {
            $service = $this->getService()->getDadosPessoa($dtoSearch);
            $this->_helper->json($service);
        }
    }

    /**
     * Metódo que recupera os dados da pessoa
     * @return json
     */
    public function getPessoaDadosAction()
    {
        $params = $this->_getAllParams();

        $params['sqTipoEndereco'] = 1;
        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');

        if($dtoSearch->getSqInteressado()&& $dtoSearch->getSqInteressado() === 'interno') {
            $service = $this->getService()->getPessoaInternoDados($dtoSearch);
            $this->_helper->json($service);
        } else {
            $service = $this->getService()->getPessoaDados($dtoSearch);

            $this->_helper->json($service);
        }
    }

    /**
     * Metódo que recupera assinatura da pessoa
     * @return json
     */
    public function getPessoaAssinaturaAction()
    {
        $params = $this->_getAllParams();
        $params['sqPessoa'] = $this->getIdUser();
        $params['sqUnidadeOrg'] = $this->getIdUnidade();
        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
        $service = $this->getService()->getPessoaAssinatura($dtoSearch);
        $this->_helper->json($service);
    }

    /**
     * Metódo que recupera os dados do rodape
     * @return json
     */
    public function getPessoaDadosRodapeAction()
    {
        $params = $this->_getAllParams();
        $params['sqTipoEndereco'] = 1;
        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
        $service = $this->getService()->getPessoaDadosRodape($dtoSearch);
        $this->_helper->json($service);
    }

    /**
     * retorna dados da grid
     * @param \Core_Dto_Search $dtoSearch
     * @return array
     */
    public function getResultListInteressado(\Core_Dto_Search $dtoSearch)
    {
        return $this->getService()->listGridInteressado($dtoSearch);
    }

    /**
     * metodo que ordena grid
     * @return array
     */
    public function getConfigListInteressado()
    {
        $array = array('columns' => array(0 => array('alias' => 'p.noPessoa'),
                1 => array('alias' => 'p.nuCpfCnpjPassaporte')));
        return $array;
    }

    /**
     * Action que realiza a pesquisa
     * @return NULL
     */
    public function listInteressadoAction()
    {
        $this->getHelper('layout')->disableLayout();
        $params = $this->_getAllParams();
        $configArray = $this->getConfigListInteressado();
        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getResultListInteressado($this->view->dto);
    }

    /**
     * Action que realiza a exclusão da assinatura
     * @return array
     */
    public function deleteAssinaturaAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $dtoPessoaAssinante = Core_Dto::factoryFromData($this->_getAllParams(), 'entity',
                array('entity' => 'Sgdoce\Model\Entity\PessoaAssinanteArtefato',
                'mapping' => array(
                    'sqArtefato' => 'Sgdoce\Model\Entity\Artefato'
                    , 'sqPessoaUnidadeOrg' => 'Sgdoce\Model\Entity\PessoaUnidadeOrg')));

        $this->getService()->deleteAssinatura($dtoPessoaAssinante);
    }

    /**
     * Action que realiza a exclusão da assinatura
     * @return array
     */
    public function deleteInteressadoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $dtoPessoaArtefato = Core_Dto::factoryFromData($this->_getAllParams(), 'entity',
                array('entity' => 'Sgdoce\Model\Entity\PessoaInteressadaArtefato',
                'mapping' => array(
                    'sqArtefato' => 'Sgdoce\Model\Entity\Artefato'
                    , 'sqPessoaSgdoce' => 'Sgdoce\Model\Entity\PessoaSgdoce')));

        return $this->getService()->deleteInteressado($dtoPessoaArtefato);
    }

    /**
     * Action que realiza a exclusão da assinatura
     * @return array
     */
    public function deleteDestinatarioAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $params['sqPessoaFuncao'] = \Core_Configuration::getSgdocePessoaFuncaoDestinatario();
        $dtoPessoaArtefato = Core_Dto::factoryFromData($params, 'entity',
                array('entity' => 'Sgdoce\Model\Entity\PessoaArtefato',
                        'mapping' => array(
                                'sqArtefato' => 'Sgdoce\Model\Entity\Artefato',
                                'sqPessoaFuncao' => 'Sgdoce\Model\Entity\PessoaFuncao',
                                'sqPessoaSgdoce' => 'Sgdoce\Model\Entity\PessoaSgdoce')));

        $this->getService()->deleteDestinatario($dtoPessoaArtefato);
    }

    /**
     * Action que realiza a pesquisa a funcao da pessoa
     * @return json
     */
    public function searchPessoaFuncaoAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();
        $service = $this->getService('PessoaFuncao')->listPessoaFuncao($params);
        $this->_helper->json($service);
    }

    /**
     * Action que realiza a pesquisa os dados da pessoa
     * @return json
     */
    public function searchDadosPessoaAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();
        $service = $this->getService()->searchDadosPessoa($params);
        $this->_helper->json($service);
    }

    /**
     * Recupera informacoes da pessoa solicitada
     * @return json
     */
    public function recuperaDadosPessoaAction()
    {
        // desabilitando layout e renderizacao
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->_getAllParams();
        $params['sqTipoEndereco'] = 1;
        // verificando qual tipo de consulta sera realizada

        if($params['sqTipoPessoa'] == 3){
            unset($params['extraParam']);
            $params['sqUnidade'] = $params['sqPessoa'];
            $dtoSearch = Core_Dto::factoryFromData($params, 'search');
            $service = $this->getService('VwUnidadeOrg')->getUnidadeOrigem($dtoSearch);
        }
        if($params['nuCPFDestinatario']){
            $params['sqNacionalidade'] = $params['sqTipoPessoa'];
            $dtoSearch = Core_Dto::factoryFromData($params, 'search');
            $return = $this->getService('VwPessoa')->getPessoaDados($dtoSearch);
        } else {
            // tratando parametros, realizando a consulta
            $dtoSearch = Core_Dto::factoryFromData($params, 'search');
            $return = $this->getService()->getPessoaDados($dtoSearch);
        }

        // retornando o resultado
        $this->_helper->json($return);
    }

    public function getPessoaAction()
    {
        $params = $this->_getAllParams();
        $dto = Core_Dto::factoryFromData($params,'search');
        $return = $this->getService()->getPessoa($dto);

        if ($params['sqTipoPessoa'] == 1) {
            $result['nuCpf'] = Zend_Filter::filterStatic(
                $return->getNuCpf(),
                'MaskNumber',
                array('999.999.999-99'),
                array('Core_Filter')
            );
        } else {
            $result['nuCnpj'] = Zend_Filter::filterStatic(
                $return->getNuCnpj(),
                'MaskNumber',
                array('cnpj'),
                array('Core_Filter')
            );
        }
        $this->_helper->json($result);
    }
}