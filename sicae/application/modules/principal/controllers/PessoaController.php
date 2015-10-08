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

use Principal\Service\Pessoa;

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
class Principal_PessoaController extends Sica_Controller_Action
{

    /** @var Principal\Service\Pessoa */
    protected $_service = 'Pessoa';

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

        switch ($this->_getParam('sqTipoPessoa')) {
            case Pessoa::SQ_TIPO_PESSOA_FISICA:
                $configArray['columns'][0]['alias'] = 'p.noPessoa';
                $configArray['columns'][1]['alias'] = 'pf.nuCpf';
                $configArray['columns'][2]['alias'] = 'pf.dtNascimento';
                $configArray['columns'][3]['alias'] = 'pf.sgSexo';
                $configArray['columns'][4]['alias'] = 'p.sqPessoa';
                $configArray['columns'][5]['alias'] = 'p.stRegistroAtivo';

                break;

            case Pessoa::SQ_TIPO_PESSOA_JURIDICA:
                $configArray['columns'][0]['alias'] = 'pj.noFantasia';
                $configArray['columns'][1]['alias'] = 'pj.nuCnpj';
                $configArray['columns'][2]['alias'] = 'p.noPessoa';
                $configArray['columns'][3]['alias'] = 'p.sqPessoa';
                $configArray['columns'][4]['alias'] = 'p.stRegistroAtivo';

                break;

            default:
                $configArray['columns'][0]['alias'] = 'p.sqPessoa';
                $configArray['columns'][1]['alias'] = 'p.noPessoa';
                $configArray['columns'][2]['alias'] = 'pj.nuCnpj';
                $configArray['columns'][3]['alias'] = 'p.sqPessoa';
                $configArray['columns'][4]['alias'] = 'p.stRegistroAtivo';

                break;
        }

        return $configArray;
    }

    /**
     * Recupera lista de combos
     */
    public function getCombos()
    {
        $cmb['sqTipoPessoa'] = $this->getService('TipoPessoa')->getCombo(array(
            Pessoa::SQ_TIPO_PESSOA_FISICA,
            Pessoa::SQ_TIPO_PESSOA_JURIDICA
        ));
        $cmb['sqTipoPessoa'][3] = 'Sem classificação';

        $cmb['sqNaturezaJuridica'] = $this->getService('NaturezaJuridica')
                ->getComboDefault(array(
            'sqNaturezaJuridica' => array(100, 200, 300, 400, 500)), array('noNaturezaJuridica' => 'ASC')
        );

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
     * Realiza com autocomplete de pessoa fisica e juridica
     */
    public function searchPessoaAction()
    {
        $validate = $this->_getParam('validate', FALSE);

        $config = array(
            'sqTipoPessoa' => $this->_getParam('extraParam'),
            'noPessoaFisica' => $this->_getParam('query'),
            'nuCpf' => $this->_getParam(''),
            'nuCnpj' => $this->_getParam('')
        );

        if ($this->_getParam('extraParam') == Pessoa::SQ_TIPO_PESSOA_JURIDICA) {
            $config['noPessoaJuridica'] = $this->_getParam('query');
        }

        $params = Core_Dto::factoryFromData($config, 'search');
        $result = $this->getService()->searchPessoa($params);

        if (0 === count($result) && $validate) {
            $result = array(
                '__NO_CLICK__' => Core_Registry::getMessage()->_('MN016')
            );
        }

        $this->_helper->json($result);
    }

    /**
     * Realiza consulta por cpf e nome
     */
    public function searchCpfAction()
    {
        $cpf = \Zend_Filter::filterStatic($this->_getParam('query'), 'Digits');
        $params = array('sqTipoPessoa' => Pessoa::SQ_TIPO_PESSOA_FISICA);
        $params['cpf'] = $cpf;

        $this->_helper->json($this->getService()->searchCpf(new \Core_Dto_Mapping($params)));
    }

    /**
     * Redireciona o usuario para tela de pessoa fisica ou juridica
     */
    public function rotaAction()
    {
        $sqTipoPessoa = array(Pessoa::SQ_TIPO_PESSOA_FISICA, Pessoa::SQ_TIPO_PESSOA_JURIDICA);

        $cmb['sqTipoPessoa'] = $this->getService('TipoPessoa')->getCombo($sqTipoPessoa);
        $this->view->cmb = $cmb;
    }

    public function toogleStatusPessoaJuridica($id)
    {
        $arrPessoa = array();
        $pessoaJuridica = $this->getService('PessoaJuridica')->find($id);
        $arrPessoa['sqPessoa'] = $pessoaJuridica->getSqPessoa()->getSqPessoa();
        $arrPessoa['noPessoa'] = $pessoaJuridica->getSqPessoa()->getNoPessoa();
        $arrPessoa['noFantasia'] = $pessoaJuridica->getNoFantasia();
        $arrPessoa['nuCnpj'] = $pessoaJuridica->getNuCnpj();
        $arrPessoa['sgEmpresa'] = $pessoaJuridica->getSgEmpresa();
        $arrPessoa['dtAbertura'] = $pessoaJuridica->getDtAbertura();
        $arrPessoa['inTipoEstabelecimento'] = $pessoaJuridica->getInTipoEstabelecimento();
        $arrPessoa['sqNaturezaJuridica'] = $pessoaJuridica->getSqPessoa()->getSqNaturezaJuridica()->getSqNaturezaJuridica();

        return $arrPessoa;
    }

    /**
     * Metodo generico que salva webservice
     */
    public function saveFormWebServiceAction()
    {
        $repository = $this->_getParam('repository');
        $method = $this->_getParam('method');
        $arrValues = $this->_getParam('params');
        $filters = $this->_getParam('filters');

        $arrPessoa = array();
        if ($method == 'inativarPessoaJuridica'){
            $method = 'libCorpUpdatePessoaJuridica';
            $arrPessoa = $this->toogleStatusPessoaJuridica($arrValues[0]['value']);
            $arrPessoa['stRegistroAtivo'] = $arrValues[3]['value'];
            $filters['skyp'] = true;
        }

        $data = !empty($arrPessoa) ? $arrPessoa : $arrValues;
        $result = $this->getWebService($repository, $method, $data, $filters);

        $toogleStatus = FALSE;
        foreach ($this->_getParam('params') as $value) {
            if ($value['name'] == 'toogleStatus') {
                $toogleStatus = TRUE;
                $msgToogleStatus = $value['value'];
                break;
            }
        }

        if ($result) {
            switch (TRUE) {
                case $toogleStatus:
                    $msg = $msgToogleStatus;

                    break;

                case strstr($method, 'InativarPessoa'):
                case strstr($method, 'AtivarPessoa'):
                    $msg = 'MN131';

                    break;

                case strstr($method, 'Update'):
                    $msg = 'MN004';

                    break;

                case strstr($method, 'Delete'):
                    $msg = 'MN131';

                    break;

                default:
                    $msg = 'MN126';
                    break;
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
     * Dados a serem utilizados para geração do PDF
     *
     * @see    Sica_Controller_Action::getDataPdf()
     * @return array
     */
    public function getDataPdf()
    {
        $this->_pdfName = 'Lista.pdf';

        if ($this->_getParam('sqTipoPessoa') == Pessoa::SQ_TIPO_PESSOA_JURIDICA) {
            $this->_pdfName = 'Lista de Pessoa Jurídica.pdf';
        }

        $dtoSearch = new Core_Dto_Search($this->_getAllParams());

        $arrPJ = array();
        $result = $this->getService()->listGrid($dtoSearch);

        foreach ($result['data'] as $value) {
            $result['arrPJ'][$value['sqPessoa']] = $this->getService()->find($value['sqPessoa']);

            $criteria = array('sqPessoa' => $value['sqPessoa']);
            $result['arrVinculo'][$value['sqPessoa']] = $this->getService('PessoaVinculo')->findBy($criteria);
        }

        return $result;
    }

}