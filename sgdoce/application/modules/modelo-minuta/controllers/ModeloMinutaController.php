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
 * Classe para Controller de ModeloMinuta
 *
 * @package    Minuta
 * @category   Controller
 * @name       ModeloMinuta
 * @version    1.0.0
 */

use Doctrine\DBAL\Query\QueryBuilder;

class ModeloMinuta_ModeloMinutaController extends \Core_Controller_Action_CrudDto
{
    /**
     * Variavel para receber o nome da service
     * @var    string
     * @access protected
     * @name   $_service
     */
    protected $_service = 'ModeloMinuta';

    /**
     * Variavel para receber a entidade referente a esta classe
     * @var    string
     * @access protected
     * @name   $_optionsDtoEntity
     */
    protected $_optionsDtoEntity = array(
            'entity'     => 'Sgdoce\Model\Entity\ModeloDocumento',
            'mapping'    => array(
                'sqTipoDocumento'            => 'Sgdoce\Model\Entity\TipoDocumento',
                'sqGrauAcesso'               => 'Sgdoce\Model\Entity\GrauAcesso',
                'sqPosicaoTipoDocumento'     => 'Sgdoce\Model\Entity\PosicaoTipoDocumento',
                'sqAssunto'                  => 'Sgdoce\Model\Entity\Assunto',
                'sqPosicaoData'              => 'Sgdoce\Model\Entity\PosicaoData',
                'sqCabecalho'                => 'Sgdoce\Model\Entity\Cabecalho'
            )
    );

    /**
     * Configura os parametros extra para o save
     * @return array
     */
    protected function _factoryParamsExtrasSave($data)
    {
        if ($data['sqPosicaoData']  === NULL){
            $data['sqPosicaoData'] = 1;
        }
        if ($data['sqPadraoModeloDocumentoCam1']){
            $data['sqPadraoModeloDocumentoCam'][] = $data['sqPadraoModeloDocumentoCam1'];
        }
        if ($data['sqPadraoModeloDocumentoCam2']){
            $data['sqPadraoModeloDocumentoCam'][] = $data['sqPadraoModeloDocumentoCam2'];
        }
        if ($data['sqPadraoModeloDocumentoCam3']){
            $data['sqPadraoModeloDocumentoCam'][] = $data['sqPadraoModeloDocumentoCam3'];
        }
        if ($data['sqPadraoModeloDocumentoCam4']){
            $data['sqPadraoModeloDocumentoCam'][] = $data['sqPadraoModeloDocumentoCam4'];
        }
        $result = array_unique($data['sqPadraoModeloDocumentoCam']);
        $arrayDto = array();
        $dtoOption = array(
                'entity'  => 'Sgdoce\Model\Entity\ModeloDocumentoCampo',
                'mapping' => array('sqPadraoModeloDocumentoCam'
                        => 'Sgdoce\Model\Entity\PadraoModeloDocumentoCampo')
        );

        foreach ($result as $value) {
            $arrayDto[] = Core_Dto::factoryFromData(
                    array('sqPadraoModeloDocumentoCam'
                            => $value),
                    'entity', $dtoOption
            );
        }

        return array($arrayDto, $data);
    }

    /**
     * Valida o Documento de Minuta
     * @return json
     */
    public function validaDocumentoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $dtoEntity = Core_Dto::factoryFromData($this->_getAllParams(), 'entity',
                array('entity'=> 'Sgdoce\Model\Entity\ModeloDocumento',
               'mapping' => array('sqTipoDocumento'=> 'Sgdoce\Model\Entity\TipoDocumento'
                                   ,'sqAssunto'=> 'Sgdoce\Model\Entity\Assunto')));
        $return = $this->getService()->validaDocumento($dtoEntity);
        $this->_helper->json($return);
    }
    /**
     * Ação da criação do Modelos de Minutas
     */
    public function createAction()
    {
        parent::createAction();
        $sqPadraoModeloDocumento     = $this->_getParam('sqPadraoModeloDocumento');

        if ($this->_getParam('sqTipoDocumento')){
            $this->view->sqTipoDocumento = $this->getService('TipoDocumento')->find(
                                           $this->_getParam('sqTipoDocumento'));
        }
        if ($this->_getParam('sqAssunto')){
            $this->view->sqAssunto = $this->getService('Assunto')->find($this->_getParam('sqAssunto'));
        }
        $sqModeloDocumento           = $this->_getParam('id');
        $dtoSearch = Core_Dto::factoryFromData(array('sqPadraoModeloDocumento' => $sqPadraoModeloDocumento
                                                     ,'sqModeloDocumento' => $sqModeloDocumento), 'search');

        $this->view->sqPadraoModeloDocumento = $sqPadraoModeloDocumento;
        $this->view->itens = $this->getService('PadraoModeloDocumento')->listItensPadraoModeloDoc();
        $this->view->itensGrauAcesso = $this->getService('GrauAcesso')->listItensGrauAcesso();
        $this->view->itensPosicaoTipoDoc = $this->getService('PosicaoTipoDocumento')->listItensPosicaoTipoDoc();
        $this->view->itensPosicaoData = $this->getService('PosicaoData')->listItensPosicaoData();
        $this->view->campos = $this->getService('PadraoModeloDocumentoCampo')
                                   ->listItensPadraoModeloDocCampos($dtoSearch);
    }

    /**
     * Ação inicial de Modelos de Minutas
     */
    public function indexAction()
    {
        parent::indexAction();
        $this->view->itens = $this->getService('PadraoModeloDocumento')->listItensPadraoModeloDoc();
    }

    /**
     * Ação de edit de Modelos de Minutas
     */
    public function editAction()
    {
        parent::editAction();
        $sqPadraoModeloDocumento = $this->_getParam('sqPadraoModeloDocumento');
        $sqModeloDocumento       = $this->_getParam('id');
        $dtoSearch = Core_Dto::factoryFromData(array('sqPadraoModeloDocumento' => $sqPadraoModeloDocumento
                ,'sqModeloDocumento' => $sqModeloDocumento), 'search');
        $this->view->sqPadraoModeloDocumento = $sqPadraoModeloDocumento;
        $this->view->itens = $this->getService('PadraoModeloDocumento')->listItensPadraoModeloDoc();
        $this->view->itensCabecalho = $this->getService('Cabecalho')->listItensCabecalho();
        $this->view->itensGrauAcesso = $this->getService('GrauAcesso')->listItensGrauAcesso();
        $this->view->itensPosicaoTipoDoc = $this->getService('PosicaoTipoDocumento')->listItensPosicaoTipoDoc();
        $this->view->itensPosicaoData = $this->getService('PosicaoData')->listItensPosicaoData();
        $this->view->campos = $this->getService('PadraoModeloDocumentoCampo')
                                   ->listItensPadraoModeloDocCampos($dtoSearch);
    }

    public function saveAction() {
        if ($this->_getParam('id')) {
            $dtoSearch = Core_Dto::factoryFromData(array('sqModeloDocumento' => $this->_getParam('id')), 'search');
            $this->getService()->deleteModelo($dtoSearch);
        }
        parent::saveAction();

    }

    /**
     * Retorna o download com o Modelo de Minuta
     */
    public function downloadModeloAction()
    {
        $codigo   = $this->_getParam('codigo');

        $registry = \Zend_Registry::get('configs');
        $options  = array('path' => $registry['folder']['modeloMinuta']);

        $dtoEntity = Core_Dto::factoryFromData(array('sqModeloDocumento' => $codigo), 'entity',
                                               array('entity'=> 'Sgdoce\Model\Entity\ModeloDocumento'));

        $entityModelo = $this->getService()->find($codigo);

        $entityArray = $this->getService()->findModelo($dtoEntity);

        $dtoSearch = Core_Dto::factoryFromData(
                        array('sqPadraoModeloDocumento' => $entityArray['sqPadraoModeloDocumento']
                        ,'sqModeloDocumento' => $entityArray['sqModeloDocumento']), 'search');

        $arrayGrupo = $this->getService('PadraoModeloDocumentoCampo')
                        ->listItensPadraoModeloDocCampos($dtoSearch);

        $file = "{$entityArray['noPadraoModeloDocumento']}.pdf";

        \Core_Doc_Factory::setFilePath(APPLICATION_PATH . '/modules/modelo-minuta/views/scripts/modelo-minuta');

        switch ($entityArray['sqPadraoModeloDocumento']){
            case 1:
                    \Core_Doc_Factory::write('padraoAta', array('data' => $arrayGrupo ,
                                                                'entity' => $entityModelo), $options['path'], $file);
                    break;
            case 2:
                    \Core_Doc_Factory::write('padraoGeral', array('data' => $arrayGrupo,
                                                                  'entity' => $entityModelo), $options['path'], $file);
                    break;
            case 3:
                    \Core_Doc_Factory::write('padraoOficio', array('data' => $arrayGrupo,
                                                                   'entity' => $entityModelo), $options['path'], $file);
                    break;
        }

        $this->_helper->download($file, $options);
    }

    /**
     * Ordena a grid
     * @return array
     */
    public function getConfigList()
    {
        $array = array(
                'columns' => array(
                        0 => array(
                                'alias' => 'pmd.noPadraoModeloDocumento'
                        ),
                        1 => array(
                                'alias' => 'td.noTipoDocumento'
                        ),
                        2 => array(
                                'alias' => 'a.txAssunto'
                        )
                    )
                );

        return $array;
    }

    /**
     * Exclusão lógica
     * @return array
     */
    public function deleteAction()
    {
        $dtoSearch = Core_Dto::factoryFromData(array('sqModeloDocumento' => $this->_getParam('id')), 'search');
        $this->getService()->deleteModelo($dtoSearch);
        $this->getMessaging()->addSuccessMessage($this->_getMessageTranslate('MD003'));
        return $this->_redirectActionDefault('index');
    }
}