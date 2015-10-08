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

use Principal\Service\PessoaJuridica,
    Principal\Service\NaturezaJuridica;

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
class Principal_PessoaJuridicaController extends \Core_Controller_Action_CrudDto
{

    protected $_messageCreate = 'MN126';
    protected $_messageEdit = 'MN126';

    /** @var Principal\Service\PessoaJuridica */
    protected $_service = 'PessoaJuridica';

    /** @var array */
    protected $_optionsDtoEntity = array(
        'entity' => '\Sica\Model\Entity\PessoaJuridica',
        'mapping' => array(
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
        $cmb['sqNaturezaJuridicaPai'] = $this->getService('NaturezaJuridica')
                ->getComboDefault(array('sqNaturezaJuridica' => array(
                NaturezaJuridica::ADMINISTRAÇÃO_PUBLICA,
                NaturezaJuridica::ENTIDADES_EMPRESARIAIS,
                NaturezaJuridica::ENTIDADES_SEM_FINS_LUCRATIVOS,
                NaturezaJuridica::PESSOAS_FISICAS,
                NaturezaJuridica::INSTITUICOES_EXTRATERRITORIAIS
            )), array('noNaturezaJuridica' => 'ASC'));

        $cmb['inTipoEstabelecimento'] = array(
            PessoaJuridica::IN_TIPO_ESTABELECIMENTO_FILIAL => 'Filial',
            PessoaJuridica::IN_TIPO_ESTABELECIMENTO_MATRIZ => 'Matriz'
        );

        $cmb['sqNaturezaJuridica'] = array();

        $this->view->aba = $this->_getParam('aba');
        $this->view->cmb = $cmb;
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
                $this->_redirect("/principal/pessoa-juridica/edit/id/{$sqPessoa}/");
                break;
            case "":
                $this->_redirect("/principal/pessoa/");
                break;

            default:
                $this->_redirect("/principal/pessoa-juridica/edit/id/{$sqPessoa}/aba/" . $this->_getParam('aba'));
                break;
        }
    }

    public function viewAction()
    {
        self::editAction();

        $this->view->visualizar = TRUE;
        $this->render('edit');
    }

    public function editAction()
    {
        parent::editAction();

        $sqNaturezaJuridicaPai = $this->view
                ->data
                ->getSqPessoa()
                ->getSqNaturezaJuridica()
                ->getSqNaturezaJuridicaPai()
                ->getSqNaturezaJuridica();

        $criteria = array('sqNaturezaJuridicaPai' => $sqNaturezaJuridicaPai);
        $this->view->cmb['sqNaturezaJuridica'] = $this->getService('NaturezaJuridica')
                ->getComboDefault($criteria, array('noNaturezaJuridica' => 'ASC'));
    }

    /**
     * Cria outro dtos
     * @param type $data
     * @return type
     */
    public function _factoryParamsExtrasSave($data)
    {
        if (!$data['sqIntegracaoPessoaInfoconv_sqPessoaAutora']) {
            $data['sqIntegracaoPessoaInfoconv_sqPessoaAutora'] = \Core_Integration_Sica_User::getPersonId();
        }

        return array(new \Core_Dto_Mapping($data, $data));
    }

    public function findNaturezaJuridicaAction()
    {
        $this->_helper->layout->disableLayout();

        $criteria = array('sqNaturezaJuridicaPai' => $this->_getParam('sqNaturezaJuridicaPai'));
        $arrNaturezaJuridica = $this->getService('NaturezaJuridica')
                ->findBy($criteria, array('noNaturezaJuridica' => 'ASC'));

        $arrOption = array();
        foreach ($arrNaturezaJuridica as $value) {
            $arrOption[$value->getSqNaturezaJuridica()] = $value->getNoNaturezaJuridica();
        }

        $this->view->arrOptions = $arrOption;
    }

}