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
 * Classe para Controller Endereco
 *
 * @package      Corporativo
 * @subpackage     Controller
 * @name         Endereco
 * @version     1.0.0
 * @since        2012-06-26
 */
class Principal_EnderecoController extends \Core_Controller_Action_CrudDto
{

    /**
     * Variavel para receber o nome da service
     * @var string
     * @access protected
     * @name $_service
     */
    protected $_service = 'Endereco';

    /** @var array */
    protected $_optionsDtoEntity = array(
        'entity' => '\Sica\Model\Entity\Endereco',
        'mapping' => array(
            'sqMunicipio' => '\Sica\Model\Entity\Municipio',
            'sqTipoEndereco' => '\Sica\Model\Entity\TipoEndereco',
            'sqPessoa' => '\Sica\Model\Entity\Pessoa'
        )
    );

    /**
     * Metodo iniciais
     */
    public function init()
    {
        parent::init();

        $sqPessoa = $this->_getParam('sqPessoa');

        $cmb['sqEstado'] = $this->getService('Estado')->getComboDefault(array(), array('noEstado' => 'ASC'));
        $cmb['sqMunicipio'] = $this->getService('Endereco')->comboMunicipio(NULL);

        if ($this->_getParam('id')) {
            $cmb['sqTipoEndereco'] = $this->getService('TipoEndereco')->getComboDefault();
        } else {
            $cmb['sqTipoEndereco'] = $this->getService('TipoEndereco')->getComboForSqPessoa($sqPessoa);
        }

        $this->view->cmb = $cmb;
        $this->view->sqPessoa = $sqPessoa;
        $this->_helper->layout()->disableLayout();
    }

    /**
     * Action para edicao
     */
    public function editAction()
    {
        parent::editAction();

        $sqEstado = $this->view->data->getSqMunicipio()->getSqEstado()->getSqEstado();
        $this->view->cmb['sqMunicipio'] = $this->getService('Endereco')->comboMunicipio($sqEstado);
    }

    /**
     * Retorna json com os Estados
     * @return json $arrEstado
     */
    public function comboEstadoAction()
    {
        $pais = $this->_getParam('pais');
        $arrEstado = $this->getService()->comboEstado($pais);

        $this->view->arrOptions = $arrEstado;
        $this->render('combo');
    }

    /**
     * Retorna json com os Municipios
     * @return json $arrMunicipio
     */
    public function comboMunicipioAction()
    {
        $estado = $this->_getParam('estado');
        $arrMunicipio = $this->getService()->comboMunicipio($estado);

        $this->view->arrOptions = $arrMunicipio;
        $this->render('combo');
    }

    /**
     * Recupera um endereco conforme cep
     */
    public function searchCepAction()
    {
        $cep = Zend_Filter::filterStatic($this->_getParam('cep', 0), 'Digits');
        $arrEndereco = $this->getService()->searchCep($cep);

        $this->_helper->json($arrEndereco);
    }

    /**
     * Configura a lista com os campos a apresentar na grid
     * @return array
     */
    public function getConfigList()
    {
        $configArray = array();
        $configArray['columns'][0]['alias'] = 'e.sqCep';
        $configArray['columns'][1]['alias'] = 'te.noTipoEndereco';
        $configArray['columns'][2]['alias'] = 'e.txEndereco';
        $configArray['columns'][3]['alias'] = 'e.nuEndereco';
        $configArray['columns'][4]['alias'] = 'e.noBairro';
        $configArray['columns'][5]['alias'] = 'm.noMunicipio';
        $configArray['columns'][6]['alias'] = 'es.noEstado';

        return $configArray;
    }

}
