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
 * Classe Controller DadoBancarioController
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         DadoBancarioController
 * @version      1.0.0
 * @since        2012-08-21
 */
class Principal_DadoBancarioController extends \Core_Controller_Action_CrudDto
{

    /** @var Principal\Service\DadoBancario */
    protected $_service = 'DadoBancario';

    /** @var array */
    protected $_optionsDtoEntity = array(
        'entity' => '\Sica\Model\Entity\DadoBancario',
        'mapping' => array(
            'sqTipoDadoBancario' => '\Sica\Model\Entity\TipoDadoBancario',
            'sqAgencia' => '\Sica\Model\Entity\Agencia',
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

        if ($this->_getParam('id')) {
            $cmb['sqTipoDadoBancario'] = $this->getService('TipoDadoBancario')->getComboDefault();
        } else {
            $cmb['sqTipoDadoBancario'] = $this->getService('TipoDadoBancario')->getComboForSqPessoa($sqPessoa);
        }

        $cmb['sqBanco'] = $this->getService('Banco')->getComboCoBanco();

        $this->view->cmb = $cmb;
        $this->view->sqPessoa = $sqPessoa;
        $this->_helper->layout()->disableLayout();
    }

    /**
     * Configura a lista com os campos a apresentar na grid
     * @return array
     */
    public function getConfigList()
    {
        $configArray = array();
        $configArray['columns'][0]['alias'] = 'td.noTipoDadoBancario';
        $configArray['columns'][1]['alias'] = 'b.noBanco';
        $configArray['columns'][2]['alias'] = 'a.coAgencia';
        $configArray['columns'][3]['alias'] = 'a.coDigitoAgencia';
        $configArray['columns'][4]['alias'] = 'd.nuConta';
        $configArray['columns'][5]['alias'] = 'd.nuContaDv';

        return $configArray;
    }

    /**
     * Recupera auto complete de agencia 
     */
    public function searchAgenciaAction()
    {
        $result = $this->getService()->searchAgencia($this->_getAllParams());
        $this->getHelper('json')->sendJson($result);
    }

    /**
     * Recupera digito da agencia
     */
    public function searchDigitoAgenciaAction()
    {
        $result = $this->getService()->searchDigitoAgencia($this->_getAllParams());
        $this->getHelper('json')->sendJson($result);
    }

}