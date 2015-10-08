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
 * Classe para Controller de RestaurarEtiqueta
 *
 * @package    Etiqueta
 * @category   Controller
 * @name       Etiqueta_GerarEtiquetaController
 * @version    1.0.0
 */

/**
 * Classe para Controller de RestaurarEtiqueta
 *
 * @package    Etiqueta
 * @category   Controller
 * @name       Etiqueta_GerarEtiquetaController
 * @version    1.0.0
 */
class Etiqueta_RestaurarEtiquetaController extends \Core_Controller_Action_Crud
{

    /**
     * Variavel para receber o nome da service
     * @var    string
     * @access protected
     * @name   $_service
     */
    protected $_service = 'GerarEtiqueta';

    /**
     * Ação inicial de restauração de etiquetas.
     */
    public function indexAction()
    {}

    public function formAction()
    {
        $this->getHelper('layout')->disableLayout();
    }

    public function printAction()
    {
        // desabilitando layout
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        if($this->getRequest()->isPost()) {

            $arrDigitais         = $this->getRequest()->getParam('nuEtiquetas', array());
            $arrDigitaisPrint    = array();
            $arrDigitaisValidate = array();

            $withNUP = false;
            foreach ($arrDigitais as $key=>$value) {
                $aux1 = explode(' - ', $value);
                $arrDigitaisValidate[$key] = trim($aux1[0]);
                if (count($aux1) > 1) {
                    if($withNUP === false) $withNUP = true;

                    $arrDigitaisPrint[$key] = array(
                        'nuEtiqueta' => $arrDigitaisValidate[$key],
                        'nuNupSiorg' => preg_replace('/\D+/', '', $aux1[1])
                        );
                }else{
                    $arrDigitaisPrint[$key]['nuEtiqueta'] = $arrDigitaisValidate[$key];
                }
            }

            if( $this->getService()->isValidNuEtiqueta($arrDigitaisValidate) ) {
                $etiquetaPdf = new Sgdoce_EtiquetaPdf();

                $textoEtiquetaKey = 'textoEtiqueta';
                if ($withNUP) {
                    $textoEtiquetaKey = 'textoEtiquetaComNup';
                }
                $textoEtiqueta  = Zend_Controller_Front::getInstance()->getParam('bootstrap')
                        ->getOption($textoEtiquetaKey);

                $etiquetaPdf->setTextoEtiqueta($textoEtiqueta)
                            ->setDigitais($arrDigitaisPrint)
                            ->setEtiquetaComNUP($withNUP)
                            ->generate();
            }
        }

        echo "Número da digital inválida.";
    }

    public function listaNumeroEtiquetasAction()
    {
    	$inLoteComNupSiorg = $this->getRequest()->getParam("extraParam", '');
    	$nuEtiqueta        = preg_replace("/[^a-zA-Z0-9]+/", "", $this->getRequest()->getParam("query", ""));
    	$arrParams         = array('nuEtiqueta' => $nuEtiqueta,
                                   'inLoteComNupSiorg' => (boolean) $inLoteComNupSiorg);

        $arrDigitais = array();
        if ($inLoteComNupSiorg !== ''){
            $objCDto     = \Core_Dto::factoryFromData($arrParams, 'search');
            $arrDigitais = $this->getService()->listEtiquetaPorNumero($objCDto,$arrParams['inLoteComNupSiorg']);
        }
    	$this->_helper->json($arrDigitais);
    }
}