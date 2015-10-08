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
class Auxiliar_EnderecoController extends \Core_Controller_Action_CrudDto
{
    protected $_messageCreate = 'MN013';
    protected $_messageEdit   = 'MN013';

    /**
     * Variavel para receber o nome da service
     * @var string
     * @access protected
     * @name $_service
     */
    protected $_service = 'VwEndereco';

    /** @var array */
    protected $_optionsDtoEntity = array(
        'entity' => '\Sgdoce\Model\Entity\VwEndereco',
        'mapping' => array(
            'sqMunicipio' => '\Sgdoce\Model\Entity\VwMunicipio',
            'sqTipoEndereco' => '\Sgdoce\Model\Entity\VwTipoEndereco',
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

        $cmb['sqEstado']    = $this->getService('VwEstado')->comboEstado();
        $cmb['sqMunicipio'] = $this->getService('VwEndereco')->comboMunicipio(NULL);

        if ($this->_getParam('id')) {
            $cmb['sqTipoEndereco'] = $this->getService('VwTipoEndereco')->getComboDefault();
        } else {
            $cmb['sqTipoEndereco'] = $this->getService('VwTipoEndereco')->getComboForSqPessoa($sqPessoa);
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

        $this->view->cmb['sqMunicipio'] = $this->getService('VwEndereco')->comboMunicipio($sqEstado);
        $this->view->enderecoSgdoce = $this->getService('EnderecoSgdoce')->getEnderecoFromCorporativo(
            $this->view->data,
            $this->_getParam('sqPessoaSgdoce')
        );
    }

    public function deleteEnderecoAction()
    {
        $params = new \Core_Dto_Search($this->_getAllParams());

        $this->getService()->delete($params);
        $this->getService('AnexoComprovante')->deleteByEnderecoSgdoce($params);
        $this->getService('EnderecoSgdoce')->delete($params->getSqEnderecoSgdoce());
        $this->getService('EnderecoSgdoce')->finish();

        $this->_helper->parseJson()->sendJson('MN045');
    }

    /**
     * Retorna json com os Estados
     * @return json $arrEstado
     */
    public function comboEstadoAction()
    {
        $pais = $this->_getParam('pais');
        $arrEstado = $this->getService()->comboEstado($pais);

        $this->_helper->json($arrEstado);
    }

    /**
     * Retorna json com os Municipios
     * @return json $arrMunicipio
     */
    public function comboMunicipioAction()
    {
        $estado = $this->_getParam('estado');
        $arrMunicipio = $this->getService()->comboMunicipio($estado);

        $this->_helper->json($arrMunicipio);
    }

    /**
     * Recupera um endereco conforme cep
     */
    public function searchCepAction()
    {
        $cep = Zend_Filter::filterStatic($this->_getParam('cep'), 'Digits');
        $arrEndereco = $this->getService()->searchCep($cep);

        $this->_helper->json($arrEndereco);
    }

    public function listAction()
    {
        $this->getHelper('layout')->disableLayout();

        $params = $this->_getAllParams();

        $this->view->dto    = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getResultList($this->view->dto);
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
        $configArray['columns'][7]['alias'] = 'e.deCaminhoArquivo';
        $configArray['columns'][8]['alias'] = 'ess.sqEnderecoSgdoce';
        $configArray['columns'][9]['alias'] = 'ac.sqAnexoComprovante';

        return $configArray;
    }

    /**
     * Realiza o save do endereco
     * @return json
     */
    public function saveAction()
    {
        $result  = $this->_save();
        $message = !empty($result['sqEndereco'])
            ? 'MN013'
            : 'Erro na operação.';

        $this->getService()->finish();

        $this->saveEnderecoSgdoce();
        $this->_helper->parseJson()->sendJson($message, array(), $result);
    }

    /**
     * Realiza o upload do arquivo
     */
    public function uploadFileEndereco($post)
    {
        $tipoPessoa = 'fisica';
        if(isset($post['sqTipoPessoa']) && $post['sqTipoPessoa']) {
            if($post['sqTipoPessoa'] == \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica()) {
                $tipoPessoa = 'juridica';
            }
        }

        $dto = new \Core_Dto_Search($post);

        $this->_redirect('/auxiliar/pessoa-' . $tipoPessoa . '/edit/id/' . $dto->getSqPessoa() . (
                (isset($post['new']) && $post['new'] == 1)
                ? '/new/true'
                : ''
        ) . '/#enderecos');
    }

    /**
     * Realiza a configuração do arquivo.
     */
    public function uploadDocumentoAction()
    {
        $sqEnderecoSgdoce = $this->saveEnderecoSgdoce();

        if($sqEnderecoSgdoce) {
            $this->getRequest()->setPost('sqEnderecoSgdoce', $sqEnderecoSgdoce);
        }

        $dto  = null;
        $post = $this->getRequest()->getPost();

        try {
            $arquivo = $this->getService()->uploadArquivo();
        } catch(\Exception $e) {
            $params = new \Core_Dto_Search($this->_getAllParams());

            $this->getService()->deleteEnderecoSgdoce($params);

            $this->uploadFileEndereco($post);
        }

        if($arquivo) {
            $post += array(
                'deCaminhoArquivo' => $arquivo
            );

            $dto    = new \Core_Dto_Search($post);
            $result = $this->getService('AnexoComprovante')->saveDocumento($dto);

            $this->_messageCreate = $result['entity']
                ? (
                    $result['isUpdate']
                        ? 'MN013'
                        : 'MN013'
                )
                : 'Erro na operação.';

            $this->_addMessageSave();
        }

        $this->uploadFileEndereco($post);
    }

    public function saveEnderecoSgdoce()
    {
        $post = $this->getRequest()->getPost();

        return $this->getService('VwEndereco')->saveEnderecoSgdoce($post);
    }

    /**
     * Realiza a configuração do dto.
     */
    protected function _factoryParamsExtrasSave($data)
    {
        $arrReturn = array();

        $arrReturn['data'] = new \Core_Dto_Search($this->_getAllParams());

        return $arrReturn;
    }

    /**
     * visualiza a imagem do endereco.
     */
    public function viewImageAction()
    {
        $this->_helper->layout()->disableLayout();

        $params = $this->_getAllParams();
        $entity = $this->getService('AnexoComprovante')->find($params['sqAnexoComprovante']);
        $this->view->sqAnexoComprovante = $entity;
    }

    /**
     * Realiza o render da imagem
     */
    public function renderImageAction()
    {
        $config = \Zend_Registry::get('configs');
        $path   = $config['upload']['endereco']['destination'];

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_getAllParams();
        $entity = $this->getService('AnexoComprovante')->find($params['id']);

        if($entity) {
            $enderecoImagem = $path . '/' . $entity->getDeCaminhoArquivo();
            $dto = new \Core_Dto_Search(
                array(
                    'resize' => true,
                    'width'  => 480,
                    'height' => 480
                )
            );

            return $this->showImage($dto, $enderecoImagem);
        }
    }

    public function showImage($dto, $enderecoImagem)
    {
        return \Artefato\Service\Imagem::showImage($dto, $enderecoImagem);
    }

}
