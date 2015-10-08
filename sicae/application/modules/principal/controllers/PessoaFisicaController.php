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
class Principal_PessoaFisicaController extends \Core_Controller_Action_CrudDto
{

    protected $_messageCreate = 'MN126';
    protected $_messageEdit = 'MN126';

    /** @var Principal\Service\PessoaFisica */
    protected $_service = 'PessoaFisica';

    /** @var array */
    protected $_optionsDtoEntity = array(
        'entity' => '\Sica\Model\Entity\PessoaFisica',
        'mapping' => array(
            'sqMunicipio' => '\Sica\Model\Entity\Municipio',
            'sqPais' => '\Sica\Model\Entity\Pais',
            'sqEstadoCivil' => '\Sica\Model\Entity\EstadoCivil',
            'sqPessoa' => '\Sica\Model\Entity\Pessoa'
        )
    );

    /**
     * Inicializa operacoes iniciais
     */
    public function init()
    {
        parent::init();
        $this->getCombos();
    }

    /**
     * Recupera combos
     */
    public function getCombos()
    {
        $cmb['sqEstadoCivil'] = $this->getService('EstadoCivil')->getComboDefault();
        $cmb['sqEstado'] = $this->getService('Estado')->getComboDefault(array(), array('noEstado' => 'ASC'));

        $cmb['sqMunicipio'] = $this->getService('Endereco')->comboMunicipio(0);
        $cmb['sqPais'] = $this->getService('Pais')->getCombo();

        $this->view->aba = $this->_getParam('aba');
        $this->view->cmb = $cmb;

        $this->view->perfil = $this->getService('Pessoa')->find($this->getUser());
        $this->view->noPessoa = $this->_getParam('noPessoa');
    }

    /**
     * Recupera o usuario logado
     * @return type
     */
    public function getUser()
    {
        return Core_Integration_Sica_User::getPersonId();
    }

    /**
     * Salva a pessoa fisica
     */
    public function _save()
    {
        $sqPessoa = parent::_save();
        $this->_addMessageSave();

        switch ($this->_getParam('aba')) {
            case 1:
                $this->_redirect("/principal/pessoa-fisica/edit/id/{$sqPessoa}/");
                break;
            case "":
                $this->_redirect("/principal/pessoa/");
                break;

            default:
                $this->_redirect("/principal/pessoa-fisica/edit/id/{$sqPessoa}/aba/" . $this->_getParam('aba'));
                break;
        }
    }

    /**
     * Cria outro dtos
     * @param type $data
     * @return type
     */
    public function _factoryParamsExtrasSave($data)
    {
        if($data['nacionalidade'] == 1){
            $data['sqPais'] = 0;
        }else{
            $data['sqMunicipio'] = 0;
        }

        $cfgPessoa['entity'] = '\Sica\Model\Entity\Pessoa';
        $arrDto['sqPessoa'] = Core_Dto::factoryFromData($data, 'entity', $cfgPessoa);

        $cfgMunicipio['entity'] = '\Sica\Model\Entity\Municipio';
        $cfgMunicipio['mapping']['sqEstado'] = '\Sica\Model\Entity\Estado';
        $arrDto['sqMunicipio'] = Core_Dto::factoryFromData($data, 'entity', $cfgMunicipio);

        $cfgPais['entity'] = '\Sica\Model\Entity\Pais';
        $arrDto['sqPais'] = Core_Dto::factoryFromData($data, 'entity', $cfgPais);

        $cfgCadastroSemCpf['entity'] = '\Sica\Model\Entity\CadastroSemCpf';
        $arrData = array(
            'dtInclusao' => Zend_Date::now()->get('dd/MM/YYYY'),
            'txJustificativa' => $this->_getParam('txJustificativa'),
        );
        $arrDto['sqCadastroSemCpf'] = Core_Dto::factoryFromData($arrData, 'entity', $cfgCadastroSemCpf);
        $arrDto['stRegistroAtivo'] = array('stRegistroAtivo' => $data['stRegistroAtivo']);

        $cfgIntegracaoPessoaInfoconv['entity'] = '\Sica\Model\Entity\IntegracaoPessoaInfoconv';

        $sqPessoaAutora  = $this->_getParam('sqIntegracaoPessoaInfoconv_sqPessoaAutora');
        if (!$sqPessoaAutora) {
            $sqPessoaAutora = \Core_Integration_Sica_User::getPersonId();
        }

        $entPessoaAutora = Core_Dto::factoryFromData(array('sqPessoa'=>$sqPessoaAutora), 'entity', $cfgPessoa);

        $arrDataIntegracaoPI = array(
            'dtIntegracao'    => $this->_getParam('sqIntegracaoPessoaInfoconv_dtIntegracao'),
            'txJustificativa' => $this->_getParam('sqIntegracaoPessoaInfoconv_txJustificativa'),
            'sqPessoaAutora'  => $entPessoaAutora,
        );
        $arrDto['sqIntegracaoPessoaInfoconv'] = Core_Dto::factoryFromData($arrDataIntegracaoPI, 'entity', $cfgIntegracaoPessoaInfoconv);

        return $arrDto;
    }

    /**
     * Recupera dados para visualizacao
     */
    public function viewAction()
    {
        parent::editAction();

        $this->render('view');
        $this->_helper->layout()->disableLayout();
    }

    /**
     * Recupera dados complementares (CPF, Telefone ...) de um responsável
     *
     * @return void
     */
    public function getDataInstitucionalAction()
    {
        $responsavel = $this->_getParam('id');
        $data = $this->getService()->findDataInstitucional($responsavel);

        if ($data && strlen($data['nuTelefone']) == 8) {
            $data['nuTelefone'] = substr($data['nuTelefone'], 0, 4) . '-' . substr($data['nuTelefone'], 4, 4);
        }

        $this->_helper->parseJson()->sendJson(NULL, array(), $data);
    }

    /**
     * Edicao da pessoa fisica
     */
    public function editAction()
    {
        parent::editAction();

        $sqEstado = $this->view->data->getSqMunicipio()->getSqEstado()->getSqEstado();
        $this->view->cmb['sqMunicipio'] = $this->getService('Endereco')->comboMunicipio($sqEstado);
    }

}