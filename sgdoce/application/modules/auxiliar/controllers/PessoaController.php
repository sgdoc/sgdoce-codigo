<?php

/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */

/**
 * SISICMBio
 *
 * Classe Controller Index
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         Pessoa
 * @version      1.0.0
 * @since        2012-08-21
 */
class Auxiliar_PessoaController extends \Core_Controller_Action_CrudDto
{

    /** @var Principal\Service\Pessoa */
    protected $_service = 'VwPessoa';

    /**
     * Inicializa operacoes iniciais
     */
    public function init()
    {
        parent::init();

        $this->view->sqTipoPessoa = $this->_getParam('sqTipoPessoa');
        $this->getCombos();
    }

    /**
     * Configura a lista com os campos a apresentar na grid
     * @return array
     */
    public function getConfigList()
    {
        $configArray = array();
        $configArray['columns'][0]['alias'] = 'p.noPessoa';
        $configArray['columns'][1]['alias'] = 'pf.nuCpf';
        $configArray['columns'][2]['alias'] = 'pf.dtNascimento';
        $configArray['columns'][3]['alias'] = 'pf.sgSexo';
        $configArray['columns'][4]['alias'] = 'p.sqPessoa';
        $configArray['columns'][5]['alias'] = 'p.stRegistroAtivo';

        if ($this->_getParam('sqTipoPessoa') == \Core_Configuration::getCorpTipoPessoaJuridica()) {
            $configArray = array();
            $configArray['columns'][0]['alias'] = 'pj.noFantasia';
            $configArray['columns'][1]['alias'] = 'pj.nuCnpj';
            $configArray['columns'][2]['alias'] = 'p.noPessoa';
            $configArray['columns'][3]['alias'] = 'p.sqPessoa';
            $configArray['columns'][4]['alias'] = 'p.stRegistroAtivo';
        }

        return $configArray;
    }

    /**
     * Recupera lista de combos
     */
    public function getCombos()
    {
        $sqTipoPessoa = array(\Core_Configuration::getCorpTipoPessoaFisica());
        $cmb['sqTipoPessoa'] = $this->getService('VwTipoPessoa')->getCombo($sqTipoPessoa);

        $this->view->cmb = $cmb;
    }

    /**
     * Realiza consulta de existencia de cpf ou cnpj.
     */
    public function searchCpfCnpjAction()
    {
        if ($this->_getParam('nuCpf')) {
            $criteria = array('nuCpf' => Zend_Filter::filterStatic($this->_getParam('nuCpf'), 'Digits'));
        } else {
            $criteria = array('nuCnpj' => Zend_Filter::filterStatic($this->_getParam('nuCnpj'), 'Digits'));
        }

        $result = $this->getService()->searchCpfCnpj($criteria);
        $this->_helper->json($result);
    }

    /**
     * Metodo generico que salva webservice
     */
    public function saveFormWebServiceAction()
    {
        $repository = $this->_getParam('repository');
        $method     = $this->_getParam('method');
        $arrValues  = $this->_getParam('params');
        $filters    = $this->_getParam('filters');

        $result = $this->getWebService($repository, $method, $arrValues, $filters);

        if ($result) {
            $msg = 'MN013';

            if (strstr($method, 'AtivarPessoa')) {
                $msg = 'MN131';
            }

            if (strstr($method, 'InativarPessoa')) {
                $msg = 'MN131';
            }

            if (strstr($method, 'Update')) {
                $msg = 'MN013';
            }

            if (strstr($method, 'Delete')) {
                $msg = 'MN045';
            }
        } else {
            $msg = 'Erro na operação.';
        }

        $this->_helper->parseJson()->sendJson($msg);
    }

    /**
     * Salva webservice
     * @param type $repository
     * @param type $method
     * @param type $arrValues
     * @param type $filters
     * @return type
     */
    public function getWebService($repository, $method, $arrValues, $filters)
    {
        return $this->getService()->saveFormWebService($repository, $method, $arrValues, $filters);
    }

    /**
     * Verificação e autorização de chefe de setor
     */
    public function autorizarJustificativaAction()
    {
        $result = array(
            'result' => true,
            'sqPessoaResponsavel' => $this->_getParam('sqResponsavel')
        );

        if (!$this->_getParam('isChefe')){
            try {
                $this->_setParam(
                    'nuCpfResponsavel',
                    \Zend_Filter::filterStatic($this->_getParam('nuCpfResponsavel'), 'Digits')
                );
                $dto = new Core_Dto_Search($this->_getAllParams());

                $this->getService()->autorizarJustificativa($dto);
            } catch(\Core_Exception_ServiceLayer_Verification $e) {
                $result['result']   = false;
                $result['mensagem'] = $e->getMessage();
            }

        }

        $this->_helper->json($result);
    }
}