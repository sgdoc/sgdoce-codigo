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
class Auxiliar_DocumentoController extends \Core_Controller_Action_CrudDto
{
    protected $_messageCreate = 'MN013';
    protected $_messageEdit   = 'MN013';

    /** @var Principal\Service\Documento */
    protected $_service = 'VwDocumento';

    /** @var array */
    protected $_optionsDtoEntity = array(
        'entity' => 'Sgdoce\Model\Entity\VwDocumento',
        'mapping' => array(
            'sqTipoDocumento' => 'Sgdoce\Model\Entity\VwTipoDocumento',
            'sqPessoa' => 'Sgdoce\Model\Entity\VwPessoa'
        )
    );

    /**
     * Metodo iniciais
     */
    public function init()
    {
        $lista    = array();
        $entity   = null;
        $sqPessoa = $this->_getParam('sqPessoa');
        $sqPessoaSgdoce = NULL;
        if ($this->_getParam('sqPessoaSgdoce')) {
            $sqPessoaSgdoce = $this->_getParam('sqPessoaSgdoce');
        }
        if($sqPessoa) {
            $entity = $this->getService('VwPessoa')->find($sqPessoa);
            if (!$sqPessoaSgdoce) {
                $sqPessoaSgdoce = $this->getService('PessoaSgdoce')->findPessoaBySqCorporativo(
                    new \Core_Dto_Search(array(
                        'sqPessoaCorporativo' => $sqPessoa
                    ))
                );
            }

            $this->view->sqPessoaSgdoce = is_object($sqPessoaSgdoce) ?
                $sqPessoaSgdoce->getSqPessoaSgdoce() : $sqPessoaSgdoce;
        }

        if(isset($entity) && $entity) {
            $lista['sqTipoDocumento'] = $this->_getParam('id')
                ? $this->getService('VwTipoDocumento')->getComboSgdoce()
                : $this->getService('VwTipoDocumento')->getComboForSqPessoa($entity->getSqPessoa());
        } else {
            $lista['sqTipoDocumento'] = $this->getService('VwTipoDocumento')->getComboSgdoce();
        }

        $lista['sqEstado']        = $this->getService('VwEndereco')->comboEstado(null, true);
        $lista['tipoDocumento']   = $this->getService('VwTipoDocumento')->listAll();

        $this->view->lista    = $lista;
        $this->view->sqPessoa = $sqPessoa;
    }

    public function createAction()
    {
        parent::createAction();

        $this->_helper->layout()->disableLayout();
    }

    public function validate($data)
    {
        $validate = array(
            1 => array(1 => array('50',true), 2 => array('50',true), 3 => array(null,true), 4 => array(null,true)),
            2 => array(5 => array('50',true), 8 => array('50',true), 9 => array('50',true)),
            4 => array(14 => array('50',true), 15 => array('50',true)),
            5 => array(
                16 => array('50', true), 17 => array(null,true), 18 => array(null,true), 20 => array('5',false),
                21 => array(null, false), 22 =>array(null, true)
            ),
            6 => array(23 => array('50',true))
        );

        $valid = true;
        if (!empty($validate[$data['sqTipoDocumento']])){
            $lengths = $validate[$data['sqTipoDocumento']];
            foreach ($data as $key => $value){
                if (is_array($value)){
                    if (array_key_exists($value['sqAtributoTipoDocumento'],$lengths)){
                        $field = $lengths[$value['sqAtributoTipoDocumento']];
                        if ($field[1] && empty($value['txValor'])){
                            $valid = false;
                        }
                        if (is_string($field[0]) && strlen($value['txValor']) > $field[0]){
                            $valid = false;
                        }
                    }
                }
            }
        }

        return $valid;
    }

    /**
     * Salva dados de documentos
     */
    public function saveAction()
    {
        $message  = null;
        $isUpdate = false;
        $result   = false;
        $post     = $this->getRequest()->getPost();

        $valido = $this->validate($post);
        if ($valido){
            foreach($post as $key => $value) {
                if (is_array($value)) {
                    if (array_key_exists('sqDocumento', $value)) {
                        $isUpdate = true;
                        $method   = 'libCorpUpdateDocumento';
                    } else {
                        $method = 'libCorpSaveDocumento';
                    }

                    if(!empty($value['txValor'])) {
                        $result = $this->saveWs($method, $value,null);
                    } else {
                        unset($post[$key]);
                    }
                }
            }
        }

        $message = $valido ? 'Erro na operação.' : 'Campos de preenchimento obrigatório não preenchido.';

        if($result) {
            $message = 'MN013';
        }

        $this->_helper->parseJson()->sendJson($message, array(), $result);
    }

    /**
     * Realiza o Upload do Arquivo
     */
    public function uploadFile($post, $isNew)
    {
        $tipoPessoa = 'fisica';
        if(isset($post['sqTipoPessoa']) && $post['sqTipoPessoa']) {
            if($post['sqTipoPessoa'] == 2) {
                $tipoPessoa = 'juridica';
            }
        }

        $appendNew = '';
        if(
            $isNew
            || (isset($post['new']) && $post['new'] == 1)
        ) {
            $appendNew = '/new/true';
        }
        $this->_redirect('/auxiliar/pessoa-' . $tipoPessoa . '/edit/id/' . $post['sqPessoa'] . '/#documentos');
    }

    /**
     * Configura o upload
     */
    public function uploadDocumentoAction($isNew = false)
    {
        $result  = true;
        $post    = $this->getRequest()->getPost();
        $arquivo = null;

        try {
            $arquivo = $this->getService()->uploadArquivo();
        } catch(Exception $e) {
            $this->uploadFile($post, $isNew);
        }

        if($arquivo) {
            $post += array(
                'deCaminhoImagem' => $arquivo
            );

            $mapping = new Core_Dto_Search($post);
            $result  = $this->getService('AnexoComprovanteDocumento')->saveDocumento($mapping);
        }

        $this->_messageCreate = $result
            ? (
                $result == 'update'
                    ? 'MN013'
                    : 'MN013'
            )
            : 'Erro na operação.';

        $this->_addMessageSave();
        $this->uploadFile($post, $isNew);
    }

    /**
     * Realiza o save do primeiro documento
     */
    public function savePrimeiroDocumentoAction()
    {
        $message  = null;
        $isUpdate = false;
        $result   = false;
        $post     = $this->getRequest()->getPost();

        foreach($post as $key => $value) {
            if (is_array($value)) {
                if (array_key_exists('sqDocumento', $value)) {
                    $isUpdate = true;
                    $method   = 'libCorpUpdateDocumento';
                } else {
                    $method = 'libCorpSaveDocumento';
                }

                if(isset($value['txValor']) && $value['txValor']) {
                    $result = $this->saveWs($method, $value);
                } else {
                    unset($post[$key]);
                }
            }
        }

        $this->uploadDocumentoAction(true);
    }

    /**
     * Realiza o delte do documento
     */
    public function deleteAction()
    {
        $result = null;
        $params = $this->_getAllParams();

        $dto           = Core_Dto::factoryFromData($params, 'search');
        $servicePessoa = $this->getService('VwPessoa')->findPessoa($dto);
        $criteria      = array(
            'sqPessoa' => $servicePessoa->getSqPessoa()
        );

        $this->getService('AnexoComprovanteDocumento')->deleteAnexoComprovanteDocumento($dto);
        $serviceDocumentos = $this->getService('VwDocumento')->findBy($criteria);

        if(count($serviceDocumentos) > 2) {
            $documento = $this->getService('VwDocumento')->getDocumento($dto);

            if(isset($documento) && $documento) {
                foreach($documento as $atributoDocumento) {
                    $criteria['sqAtributoTipoDocumento'] = $atributoDocumento->getSqAtributoTipoDocumento()
                        ->getSqAtributoTipoDocumento();

                    $result = $this->saveWs('libCorpDeleteDocumento', $criteria,$atributoDocumento);
                }
            }
        }

        $this->_helper->parseJson()->sendJson($result ? 'MN045' : 'MN137');
    }

    /**
     * Visualiza o documento
     */
    public function viewImageAction()
    {
        $this->_helper->layout()->disableLayout();

        $params = $this->_getAllParams();
        $entity = $this->getService('AnexoComprovanteDocumento')->findOneBy(array(
            'sqAnexoComprovanteDocumento' => $params['sqAnexoComprovanteDocumento']
        ));
        $this->view->sqAnexoComprovanteDocumento = $entity;
    }

    /**
     * Realiza o render da imagem
     */
    public function renderImageAction()
    {
        $config = \Zend_Registry::get('configs');
        $path   = $config['upload']['documento']['destination'];

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_getAllParams();
        $entity = $this->getService('AnexoComprovanteDocumento')->findBy(array(
            'sqAnexoComprovanteDocumento' => $params['id']
        ));

        $enderecoImagem = $path . '/' . $entity[0]->getDeCaminhoImagem();
        $dto = new \Core_Dto_Search(
            array(
                'resize' => true,
                'width'  => 480,
                'height' => 480
            )
        );

        return $this->showImage($dto, $enderecoImagem);
    }

    public function showImage($dto, $enderecoImagem)
    {
        return \Artefato\Service\Imagem::showImage($dto, $enderecoImagem);
    }

    /**
     * Salva webservice
     * @param type $method
     * @param type $arrValues
     * @return type
     */
    public function saveWs($method, $arrValues,$dadosLogger)
    {
        return $this->getService()->saveWs('app:VwDocumento', $method, $arrValues,$dadosLogger);
    }

    /**
     * Action para edição
     */
    public function editAction()
    {
        $criteria = array(
            'sqPessoa' => $this->view->sqPessoa
        );

        $arrDocumentos = $this->getService('VwDocumento')->findBy($criteria);

        $this->view->sqTipoDocumento = $this->_getParam('sqTipoDocumento');
        $this->view->arrDoc          = $arrDocumentos;

        $this->_helper->layout()->disableLayout();
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
        $configArray['columns'][2]['alias'] = 'acd.deCaminhoImagem';

        return $configArray;
    }

}