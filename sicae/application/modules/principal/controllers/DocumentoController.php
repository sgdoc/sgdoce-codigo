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
 * Classe Controller DocumentoController
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         DocumentoController
 * @version      1.0.0
 * @since        2012-08-21
 */
class Principal_DocumentoController extends \Core_Controller_Action_CrudDto
{

    /** @var Principal\Service\Documento */
    protected $_service = 'Documento';

    /** @var array */
    protected $_optionsDtoEntity = array(
        'entity' => '\Sica\Model\Entity\Documento',
        'mapping' => array(
            'sqAtributoTipoDocumento' => '\Sica\Model\Entity\AtributoTipoDocumento',
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
            $cmb['sqTipoDocumento'] = $this->getService('TipoDocumento')->getComboDefault();
        } else {
            $cmb['sqTipoDocumento'] = $this->getService('TipoDocumento')->getComboForSqPessoa($sqPessoa);
        }

        $cmb['sqEstado'] = $this->getService('Endereco')->comboEstado(NULL, TRUE);
        $cmb['tpDocumento'] = $this->getService('TipoDocumento')->findAll();

        $this->view->cmb = $cmb;
        $this->view->sqPessoa = $sqPessoa;
        $this->_helper->layout()->disableLayout();
    }

    /**
     * Salva dados de documentos
     */
    public function saveAction()
    {
        $result = FALSE;

        foreach ($this->getRequest()->getPost() as $arrValues) {
            if (is_array($arrValues)) {
                if (array_key_exists('sqDocumento', $arrValues)) {
                    $method = 'libCorpUpdateDocumento';
                } else {
                    $method = 'libCorpSaveDocumento';
                }

                $result = $this->saveWs($method, $arrValues,NULL);
            }
        }

        $this->_helper->parseJson()->sendJson(
                $result ? strstr($method, 'Update') ? 'MN004' : 'MN126'  : 'Erro na operação.'
        );
    }

    /**
     * Deleta dados de documentos
     */
    public function deleteAction()
    {
        $result = FALSE;

        $criteria = array('sqPessoa' => $this->_getParam('sqPessoa'));
        $arrDocumentos = $this->getService('Documento')->findBy($criteria);

        foreach ($arrDocumentos as $doc) {

            if ($doc->getSqAtributoTipoDocumento()
                            ->getSqTipoDocumento()
                            ->getSqTipoDocumento() == $this->_getParam('sqTipoDocumento')) {

                $criteria['sqAtributoTipoDocumento'] = $doc->getSqAtributoTipoDocumento()->getSqAtributoTipoDocumento();
                $method = 'libCorpDeleteDocumento';

                $result = $this->saveWs($method, $criteria,$doc);
            }
        }

        $this->_helper->parseJson()->sendJson($result ? 'MN131' : 'Erro na operação.');
    }

    /**
     * Salva webservice
     * @param type $method
     * @param type $arrValues
     * @return type 
     */
    public function saveWs($method, $arrValues,$dadosLogger)
    {
        return $this->getService('Pessoa')->saveLibCorp('app:Documento', $method, $arrValues,$dadosLogger);
    }

    /**
     * Action para edição
     */
    public function editAction()
    {
        $criteria = array('sqPessoa' => $this->_getParam('sqPessoa'));
        $arrDocumentos = $this->getService('Documento')->findBy($criteria);

        $this->view->sqTipoDocumento = $this->_getParam('sqTipoDocumento');
        $this->view->arrDoc = $arrDocumentos;
    }

    /**
     * Configura a lista com os campos a apresentar na grid
     * @return array
     */
    public function getConfigList()
    {
        $configArray = array();
        $configArray['columns'][0]['alias'] = 'td.noTipoDocumento';
        $configArray['columns'][1]['alias'] = 'd.txValor';

        return $configArray;
    }

}