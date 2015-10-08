<?php
use Bisna\Application\Resource\Doctrine;

require_once __DIR__ . '/ProcessoEletronicoController.php';
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
 * Classe para Controller de Autuar Processo
 *
 * @package  Artefato
 * @category Controller
 * @name     AutuarProcessoController
 * @version     1.0.0
 */
class Artefato_AutuarProcessoController extends Artefato_ProcessoEletronicoController
{
    /**
     * @var string
     */
    protected $_service = 'AutuarProcesso';

    protected $_optionsDtoEntity = array(
        'entity' => 'Sgdoce\Model\Entity\Artefato',
        'mapping'    => array(
                'sqTipoPrioridade'  => 'Sgdoce\Model\Entity\TipoPrioridade'
        )
    );

    public function getPersonId()
    {
        return \Core_Integration_Sica_User::getPersonId();
    }

    /**
     * Metódo que realiza o form de index
     * @return array
     */
    public function indexAction()
   {
        parent::createAction();
        parent::combos();
        $this->view->sqEstado = '';
    }

    /**
     * Metódo que realiza o form de edit
     * @return array
     */
    public function editAction()
    {
        self::formDataAction();
        $this->view->form = 'alterar';
    }

    /**
     * Metódo que realiza a configuração dos extrasave
     * @return array
     */
    protected function _factoryParamsExtrasSave($data)
    {
        $dto = Core_Dto::factoryFromData($data, 'search');

        // salva o artefato_processo
        $optionsDto = array(
                'entity' => 'Sgdoce\Model\Entity\ArtefatoProcesso',
                'mapping' => array(
                        'sqEstado' => array('sqEstado'=>'Sgdoce\Model\Entity\VwEstado'),
                        'sqMunicipio' => array('sqMunicipio'=>'Sgdoce\Model\Entity\VwMunicipio'),
                        'sqArtefato' => array('sqArtefato'=>'Sgdoce\Model\Entity\Artefato')
                )
        );

        $dtoArtefatoProcesso = Core_Dto::factoryFromData($data, 'entity', $optionsDto);
        return array($dto,$dtoArtefatoProcesso);
    }

    /**
     * Salva o artefato
     * @see Core_Controller_Action_CrudDto::_save()
     */
    public function saveArtefatoProcessoAction()
    {
        $params = $this->_request->getPost();
        if(empty($params['sqEstado'])){
            unset($params['sqEstado']);
        }
        if(empty($params['sqMunicipio'])){
            unset($params['sqMunicipio']);
        }
        $params['id'] = $this->_getParam('id');
        $dtoOrigem      = Core_Dto::factoryFromData(array('sqProfissional' => $this->getPersonId()), 'search');
        $unidadeOrg     = $this->getService('Dossie')->unidadeOrigemPessoa($dtoOrigem);
        $unidadeOrg     = $unidadeOrg->getSqUnidadeExercicio();

        $data = new \Zend_Date(\Zend_Date::now(),'yyyy/MM/dd');

        $nuSequencial = $this->getService('SequencialArtefato')->getNuSequencialProcesso();

        $nuProcesso =  str_pad($unidadeOrg->getNuNup(), 5, "0", STR_PAD_LEFT) .
            str_pad($nuSequencial, 6, "0", STR_PAD_LEFT) .
            $data->get('yyyy');

//         calcular DV
        $nuProcesso = $this->getService()->calcularDigitoVerificador($nuProcesso);

        //Recuperar assunto do documento filho e anexar ao pai
        /** @var Artefato $artefato */
        $artefato = $this->getService('Artefato')->find($params['id']);

        $this->getRequest()->setPost('nuArtefato', $nuProcesso);
        $this->getRequest()->setPost('dtArtefato', $data);
        $this->getRequest()->setPost('txAssuntoComplementar', $artefato->getTxAssuntoComplementar());

        $artefato = parent::_save();

        $params['sqArtefato'] = $artefato->getSqArtefato();
        //autuação sempre Federal
        $params['coAmbitoProcesso'] = 'F';

        // salva o artefato_processo
        $optionsDto = array(
            'entity' => 'Sgdoce\Model\Entity\ArtefatoProcesso',
            'mapping' => array(
                'sqEstado' => 'Sgdoce\Model\Entity\VwEstado',
                'sqMunicipio' => 'Sgdoce\Model\Entity\VwMunicipio',
                'sqArtefato' => 'Sgdoce\Model\Entity\Artefato'
            )
        );

        $dto = Core_Dto::factoryFromData($params, 'entity', $optionsDto);

        $optionsDtoVinculo = array(
            'entity' => 'Sgdoce\Model\Entity\ArtefatoVinculo',
            'mapping' => array(
                'sqArtefatoPai' => array('sqArtefato' => 'Sgdoce\Model\Entity\Artefato'),
                'sqArtefatoFilho' => array('sqArtefato' => 'Sgdoce\Model\Entity\Artefato'),
                'sqTipoVinculoArtefato' => 'Sgdoce\Model\Entity\TipoVinculoArtefato'
            )
        );

        $arrArtefatoVinculo = array();
        $arrArtefatoVinculo['sqArtefatoPai'] = $params['sqArtefato'];
        $arrArtefatoVinculo['sqArtefatoFilho'] = $params['id'];
        $arrArtefatoVinculo['sqTipoVinculoArtefato'] = Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao();
        $arrArtefatoVinculo['inOriginal'] = TRUE;
        $arrArtefatoVinculo['dtVinculo'] = new \Zend_Date(\Zend_Date::now(),'yyyy/MM/dd');
        $dtoArtefatoVinculo = Core_Dto::factoryFromData($arrArtefatoVinculo,'entity',$optionsDtoVinculo);
        $this->getService('ArtefatoVinculo')->saveArtefatoVinculo($dtoArtefatoVinculo);
        $this->getService()->saveArtefatoProcesso($dto);
        $this->_redirect("/artefato/autuar-processo/form-data/id/{$artefato->getSqArtefato()}/artefatopai/{$this->_getParam('id')}/view/{$this->_getParam('view')}/update/{$this->_getParam('update')}");
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_CrudDto::editAction()
     */
    public function formDataAction()
    {
        parent::combos();
        parent::editAction();
        $params = $this->_getAllParams();
        $params['sqArtefato'] = $params['id'];
        $this->view->redirect = $params['view'];
        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $artefatoPai = $this->_getParam('artefatopai');
        $sqArtefatoPai = $this->getService()->findSqArtefatoPai($dtoSearch);
        $this->view->sqArtefatoPai    = !empty($artefatoPai) ?
            $artefatoPai : $sqArtefatoPai[0]->getSqArtefatoFilho()->getSqArtefato();
        $this->view->dataPost         = $this->_helper->persist->get('dataPost');
        $this->view->data             = $this->getService()->find($this->_getParam('id'));

        $entityArtefatoProcesso       = $this->getService('ArtefatoProcesso')->find($this->_getParam('id'));
        if($entityArtefatoProcesso){
            $this->view->sqEstado         = $entityArtefatoProcesso->getSqEstado()->getSqEstado();
            $this->view->sqMunicipio      = $entityArtefatoProcesso->getSqMunicipio()->getSqMunicipio();
            $this->view->coAmbitoProcesso = $entityArtefatoProcesso->getCoAmbitoProcesso();
            $this->view->nuPaginaProcesso = $entityArtefatoProcesso->getNuPaginaProcesso();
        }

        $this->view->dadosOrigem = self::_dadosPessoaDocumento($dtoSearch, \Core_Configuration::getSgdocePessoaFuncaoOrigem());
    }

    /**
     * Metódo que verifica se o modelo está cadastrado.
     * @return json
     */
    public function saveCapaAction()
    {

        $params = $this->_getAllParams();

        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $data = $this->_request->getPost();
        $data['nuArtefato'] = str_replace('-', '', str_replace('.', '', str_replace('/', '', $data['nuArtefato'])));
        $data = $this->getService('MinutaEletronica')->fixNewlines($data);
        $data = $this->getService('Artefato')->validarDataPrazo($data);
        $this->getRequest()->setPost($data);

        // salva o artefato_vinculo
        $params['sqArtefatoPai'] = $data['artefatoPai'];
        $params['sqArtefatoFilho'] =$data['sqArtefato'];
        $params['sqTipoVinculoArtefato'] = \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao();
        $params['dtVinculo'] = new \Zend_Date();
        $params['inOriginal'] = FALSE;

        $optionsDtoVinculo = array(
                'entity' => 'Sgdoce\Model\Entity\ArtefatoVinculo',
                'mapping' => array(
                        'sqArtefatoPai' => array('sqArtefato'=>'Sgdoce\Model\Entity\Artefato'),
                        'sqArtefatoFilho' => array('sqArtefato'=>'Sgdoce\Model\Entity\Artefato'),
                        'sqTipoVinculoArtefato' => array('sqTipoVinculoArtefato'=>'Sgdoce\Model\Entity\TipoVinculoArtefato')
                )
        );

        $dtoVinculo = Core_Dto::factoryFromData($params, 'entity', $optionsDtoVinculo);
        $return     = $this->getService('ArtefatoVinculo')->findVinculo($dtoVinculo);
        if(!$return && $data['artefatoPai'] != ''){
            $params['id'] = $params['sqArtefatoPai'];
            $this->getService('ArtefatoVinculo')->save($dtoVinculo);
            $this->getService('ArtefatoVinculo')->finish($dtoVinculo);
        }

        $this->_save();
        $this->getService()->finish();
           $this->_helper->json(array('sucess' => 'true'));
    }
}