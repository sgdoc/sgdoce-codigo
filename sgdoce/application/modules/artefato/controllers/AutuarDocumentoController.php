<?php
use Bisna\Application\Resource\Doctrine;

require_once __DIR__ . '/ArtefatoController.php';
/*
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
 * Classe para Controller de Autuar Documento.
 *
 * @package    Artefato
 * @category   Controller
 * @name       AutuarDocumento
 * @version    1.0.0
 */
class Artefato_AutuarDocumentoController extends Artefato_ArtefatoController
{
    /**
     * @var string
     */
    protected $_service = 'AutuarDocumento';

    /**
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => 'Sgdoce\Model\Entity\Artefato',
        'mapping' => array()
    );

    /**
     * View principal da controller
     * @return redireciona para createAction
     */
    public function indexAction()
    {
    }

    /**
     * @return void
     */
    public function formAction()
    {
        $url = $this->getRequest()
                    ->getParam('back', false);
        
        if( $url ) {
            $url = str_replace(".", "/", $url);
            $this->view->urlBack = $url;
            $url = substr($url, 1);
            $params = explode("/", $url);
            $this->view->controllerBack = next($params);
            $this->view->caixa          = end($params);
        }
        
        $sqArtefato = $this->getRequest()->getParam('id', false);        
        $objDigital = $this->getService()->find($sqArtefato);
        
        if( !$sqArtefato
            || $this->getService()->hasImage($sqArtefato) == false
            || $this->getService()->inAbreProcesso($sqArtefato) == false
            || $this->getService()->isUnidadeProtocolizadora() == false
            || $this->getService()->isDocumentoAutuado($objDigital->getNuDigital()->getNuEtiqueta(), true) == true ) {            
            $this->_redirect($this->view->urlBack);
        }

        $dtoArtefato    = Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
        $dadosOrigem    = $this->returnDadosOrigem($dtoArtefato);

        if( $dadosOrigem[1] == 1 ){
            $this->view->dadosOrigem = $dadosOrigem;
        }
        
        $this->view->objDigital = $objDigital;
        $this->view->listInteressados = $this->getService()->listInteressados($sqArtefato);
        $this->view->urlBack    = str_replace(".", "/", $this->getRequest()->getParam('back', ""));
        $this->view->urlBackText = $url;
        $this->view->tipoPessoa = array( '' => 'Selecione...' ) + $this->getService('TipoPessoa')->getComboDefault(array());
        $this->view->arrOptPrioridade = array( '' => 'Selecione...' ) + $this->getService('Prioridade')->listItems();
        $this->view->arrOptTipoPrioridade = array( '' => 'Selecione...' ) + $this->getService('TipoPrioridade')->listItems();
        $this->view->arrOptGrauAcesso     = array( '' => 'Selecione...' ) + $this->getService('GrauAcesso')->listItensGrauAcesso();
    }

    /**
     * @return void
     */
    public function saveAction()
    {
        if (!$this->_request->isPost()) {
            throw new RuntimeException('A requisição deve ser POST');
        }
        
        try {
            $entity = $this->_save();
            $this->getService()->finish();
            $this->_addMessageSave();
        } catch( \Exception $ex ) {
            $params = $this->_getAllParams();
            
            $this->getMessaging()->addErrorMessage($ex->getMessage());
            
            $url = "/artefato/autuar-documento/form/id/";
            $url .= $params['id'];
            $url .= "/back/" . $params['back'];
            
            $this->_redirect($url);
        }
                
        $url = $this->getRequest()
                    ->getParam('back', false);
        
        if( $url ) {
            $url = str_replace(".", "/", $url);
            $urlBack = $url;
        }
        
        $this->_redirect($urlBack);
    }

    /**
     * Metódo que realiza a configuração dos extrasave
     *
     * @return array
     */
    protected function _factoryParamsExtrasSave($data)
    {
        $data = $this->getService('ProcessoEletronico')->fixNewlines($data);
        $searchDto = Core_Dto::factoryFromData($data, 'search');
        // salva o artefato_processo
        $optionsDtoEntity = array(
            'entity' => 'Sgdoce\Model\Entity\ArtefatoProcesso',
            'mapping' => array(
                'sqEstado'    => 'Sgdoce\Model\Entity\VwEstado',
                'sqMunicipio' => 'Sgdoce\Model\Entity\VwMunicipio',
                'sqArtefato'  => 'Sgdoce\Model\Entity\Artefato'
            )
        );

        $data['coAmbitoProcesso'] = 'F';
        $artefatoProcesso = Core_Dto::factoryFromData($data, 'entity', $optionsDtoEntity);

        $criteriaDigital = array('nuDigital' => $data['nuDigitalAutuado']);
        $digital = $this->getService('Artefato')->findBy($criteriaDigital);
        $digital = current($digital);

        return array($searchDto, $artefatoProcesso, $digital);
    }
}
