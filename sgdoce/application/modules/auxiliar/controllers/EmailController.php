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
 * Classe Controller Email
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         Email
 * @version      1.0.0
 * @since        2012-08-21
 */
class Auxiliar_EmailController extends \Core_Controller_Action_CrudDto
{

    /** @var Principal\Service\Email */
    protected $_service = 'VwEmail';

    /** @var array */
    protected $_optionsDtoEntity = array(
        'entity' => '\Sgdoce\Model\Entity\VwEmail',
        'mapping' => array(
            'sqTipoEmail' => '\Sgdoce\Model\Entity\VwTipoEmail',
            'sqPessoa' => '\Sgdoce\Model\Entity\VwPessoa'
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
            $cmb['sqTipoEmail'] = $this->getService('VwTipoEmail')->getComboDefault();
        } else {
            $cmb['sqTipoEmail'] = $this->getService('VwTipoEmail')->getComboForSqPessoa($sqPessoa);
        }

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
        $configArray['columns'][0]['alias'] = 'te.noTipoEmail';
        $configArray['columns'][1]['alias'] = 'e.txEmail';

        return $configArray;
    }

}