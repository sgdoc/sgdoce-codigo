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
 * Classe para Controller de Etiqueta
 *
 * @package    Etiqueta
 * @category   Controller
 * @name       GerarEtiqueta
 * @version    1.0.0
 */
class Etiqueta_GerarEtiquetaController extends \Core_Controller_Action_Crud
{

    /**
     * Variavel para receber o nome da service
     * @var    string
     * @access protected
     * @name   $_service
     */
    protected $_service = 'GerarEtiqueta';

    /**
     *
     * @var boolean
     */
    private $_isUserSgi = false;

    public function init ()
    {
        parent::init();
        $dto = \Core_Dto::factoryFromData((array) $this->_getUser(), 'search');
        $this->_isUserSgi = $this->getService('VwUsuario')->isUserSgi($dto);

        $this->view->isUserSgi = $this->_isUserSgi;
        $this->view->sqUnidadeLogada = $dto->getSqUnidadeOrg();
    }

    /**
     * Ação inicial de Modelos de Minutas
     */
    public function indexAction ()
    {
        parent::indexAction();
        $this->view->arrTipoEtiqueta = $this->getService('TipoEtiqueta')->listItems();
    }

    /**
     * Ação inicial de Modelos de Minutas
     */
    public function createAction ()
    {
        parent::createAction();
        $this->view->arrTipoEtiqueta = $this->getService('TipoEtiqueta')->listItems();
        $this->view->sqTipoEtiquetaEletronica = Core_Configuration::getSgdoceTipoEtiquetaEletronica();
        $this->view->arrQtdeEtiqueta = $this->getService('QuantidadeEtiqueta')->listItems();
    }

    public function validatePrintAction ()
    {
        $dto = \Core_Dto::factoryFromData($this->getRequest()->getPost(), 'search');
        $result = $this->getService('LoteEtiqueta')
                ->getUserPrintedDigital($dto);

        $this->_helper->json($result);
    }

    public function printAction ()
    {
        // desabilitando layout
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $etiquetaPdf = new Sgdoce_EtiquetaPdf();
        $sqLoteEtiqueta = $this->getRequest()->getPost('lote');
        $dto = Core_Dto::factoryFromData(array('sqLoteEtiqueta' => $sqLoteEtiqueta), 'search');

        $entityLote = $this->getService('LoteEtiqueta')->find($sqLoteEtiqueta);

        $arrDigitais = $this->getService()->listEtiquetaImprimir($dto);

        $textoEtiquetaKey = 'textoEtiqueta';
        $etiquetaPdf->setEtiquetaComNUP($entityLote->getInLoteComNupSiorg());
        if ($entityLote->getInLoteComNupSiorg()) {
            $textoEtiquetaKey = 'textoEtiquetaComNup';
        }

        $textoEtiqueta = Zend_Controller_Front::getInstance()->getParam('bootstrap')
                ->getOption($textoEtiquetaKey);

        if ($arrDigitais) {
            $this->getService('LoteEtiqueta')->saveUserPrintedDigital(
                    $sqLoteEtiqueta, \Core_Integration_Sica_User::getUserId());
        }

        $etiquetaPdf->setTextoEtiqueta($textoEtiqueta)
                ->setDigitais($arrDigitais)
                ->generate();
    }

    public function processoAction ()
    {
        // desabilitando layout
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $sqArtefato = $this->getRequest()->getParam('id', false);

        $strLogo = current(explode('application', __FILE__))
                . 'public' . DIRECTORY_SEPARATOR
                . ltrim('/img/brasao.png', DIRECTORY_SEPARATOR);

        $etiquetaPdf = new Sgdoce_EtiquetaProcessoPdf();

        $textoEtiquetaKey = 'textoEtiquetaProcesso';

        $listTextoEtiquetaProcesso = Zend_Controller_Front::getInstance()->getParam('bootstrap')
                ->getOption($textoEtiquetaKey);

        $artefato = $this->getService('Artefato')->find($sqArtefato);

        $dto = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
        $artefatoPrincipal = $this->getService('ArtefatoVinculo')->getFirstPiece($dto);
        $documentoAutuado = null;
        if( $artefatoPrincipal && $artefatoPrincipal->getSqArtefatoFilho() ) {
            $documentoAutuado = $artefatoPrincipal->getSqArtefatoFilho();
        }
        $etiquetaPdf->setDocumentoAutuado($documentoAutuado);

        if (!$sqArtefato || is_null($artefato)) {
            throw new \Zend_Controller_Action_Exception('Requisição inválida.', 500);
        }

        $nuArtefato = $artefato->getNuArtefato();
        $nuArtefatoNumber = str_replace(array(".", ",", "-", "/"), "", $nuArtefato);

        // Define máscara.
        switch (strlen($nuArtefatoNumber)) {
            case 15:
                $etiquetaPdf->setMaskNuArtefato(\Artefato\Service\Processo::T_MASK_15_DIGITS);
                break;
            case 17:
                $etiquetaPdf->setMaskNuArtefato(\Artefato\Service\Processo::T_MASK_17_DIGITS);
                break;
            case 21:
                $etiquetaPdf->setMaskNuArtefato(\Artefato\Service\Processo::T_MASK_21_DIGITS);
                break;
            default:
                $etiquetaPdf->setMaskNuArtefato('digital');
        }

        $coAmbitoProcesso = null;

        if ($artefato->getSqArtefatoProcesso()) {
            $coAmbitoProcesso = $artefato->getSqArtefatoProcesso()->getCoAmbitoProcesso();
        }

        $etiquetaPdf->setCoAmbitoProcesso($coAmbitoProcesso);
        $dtAutuacao = $artefato->getDtArtefato();
        $dtAutuacao = new \Zend_Date($dtAutuacao);
        $dto = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
        $listInteressados = $this->getService('PessoaInterassadaArtefato')->getPessoaInteressadaArtefato($dto);

        $listInteressadosNomes = array();
        foreach ($listInteressados as $interessado) {
            $listInteressadosNomes[] = $interessado['noPessoa'];
        }
        $txInteressado = implode(",", $listInteressadosNomes);

        $txAssunto = $artefato->getSqTipoArtefatoAssunto()->getSqAssunto()->getTxAssunto();
        $txAssuntoComplementar = $artefato->getTxAssuntoComplementar();

        $etiquetaPdf->setFlLogo($strLogo);
        $etiquetaPdf->setListTxHeader($listTextoEtiquetaProcesso);
        $etiquetaPdf->setNuArtefato($nuArtefato);
        $etiquetaPdf->setDtAutuacao($dtAutuacao->get("dd/MM/yyyy"));
        $etiquetaPdf->setTxInteressado($txInteressado);
        $etiquetaPdf->setTxAssunto($txAssunto);  
        $etiquetaPdf->setTxAssuntoComplementar($txAssuntoComplementar);

        $etiquetaPdf->generate();
    }

    /**
     * retorna dados da grid
     * @param array $params
     * @return array
     */
    public function getResultList ($params)
    {
        //garante quando o usuario não for SGI o filtro somente da unidade da pessoa logada
        if (!$this->_isUserSgi) {
            $params['sqUnidadeOrg'] = Core_Integration_Sica_User::getUserUnit();
        }
        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
        return $this->getService('LoteEtiqueta')->listGrid($dtoSearch);
    }

    /**
     * metodo que ordena grid
     * @return array
     */
    public function getConfigList ()
    {
        $array = array(
            'columns' => array(
                array('alias' => 'le.sqLoteEtiqueta'),
                array('alias' => 'te.noTipoEtiqueta'),
                array('alias' => 'uo.noUnidadeOrg'),
                array('alias' => 'le.nuInicial'),
                array('alias' => 'le.nuFinal'),
                array('alias' => 'le.inLoteComNupSiorg'),
                array('alias' => 'edl.nuQuantidadeDisponivel'),
            )
        );

        return $array;
    }

    /**
     * Retorna as unidades organizacionais cadastrados em formato json
     * @return void
     */
    public function searchUnidadeOrgAction ()
    {
        $result = $this->getService('VwUnidadeOrg')
                ->searchUnidadesOrganizacionais($this->_getAllParams());

        $this->_helper->json($result);
    }

}
