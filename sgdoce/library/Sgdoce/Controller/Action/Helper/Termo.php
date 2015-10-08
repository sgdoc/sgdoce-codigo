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
 * Classe
 *
 * @name       Termo
 * @category   Sgdoce
 * @package    Sgdoce_Controller
 * @subpackage Sgdoce_Controller_Action_Helper
 *
 * @author     Rafael Yoo  <rafael.yoo.terceirizado@icmbio.gov.br>
 */
class Sgdoce_Controller_Action_Helper_Termo extends \Zend_Controller_Action_Helper_Abstract
{
    /**
     * caminho da logo a partir da pasta public/
     *
     * @var string
     * */
    const T_IMG_LOGO_PATH      = '/img/marcaICMBio2.png';

    /**
     * formato da data
     *
     * @var string
     * */
    protected $_dateFormatPrint = 'dd/MM/yyyy H:mm:s';

    /**
     * @var array
     */
    protected $_params = array();

    /**
     * @var string
     */
    protected $_nuArtefatoMask = "9999999.99999999/9999-99";

    /**
     * Retorna caminho real da imagem.
     *
     * @return string
     */
    protected function _getImgPath()
    {
    	return APPLICATION_PATH . DIRECTORY_SEPARATOR
                . ".." . DIRECTORY_SEPARATOR
                . 'public'
                . self::T_IMG_LOGO_PATH;
    }

    /**
     * @return array
     */
    protected function _getDefaultParams()
    {
        return array(
            'dtFormatPrint' => $this->getDateFormatPrint(),
            'logo' => $this->_getImgPath(),
            'nuArtefatoMask' => $this->getNuArtefatoMask()
        );
    }

    /**
     * @param array
     * @return Sgdoce_Controller_Action_Helper_Termo
     */
    public function setParams( $params )
    {
        $this->_params = array_merge($this->_getDefaultParams(), $params);
        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @return type
     */
    public function getDateFormatPrint() {
        return $this->_dateFormatPrint;
    }

    /**
     * @param string $dateFormatPrint
     * @return Sgdoce_Controller_Action_Helper_Termo
     */
    public function setDateFormatPrint( $dateFormatPrint )
    {
        $this->_dateFormatPrint = $dateFormatPrint;
        if (isset($this->_params['dtFormatPrint'])) {
            //atualiza o atributo
            $this->_params['dtFormatPrint'] = $dateFormatPrint;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getNuArtefatoMask() {
        return $this->_nuArtefatoMask;
    }

    /**
     * @param string $nuArtefatoMask
     * @return \Sgdoce_Controller_Action_Helper_Termo
     */
    public function setNuArtefatoMask($nuArtefatoMask)
    {
        $this->_nuArtefatoMask = $nuArtefatoMask;
        if (isset($this->_params['nuArtefatoMask'])) {
            //atualiza o atributo
            $this->_params['nuArtefatoMask'] = $nuArtefatoMask;
        }
        return $this;
    }


    /**
     *
     * @param string $phtml
     * @param array $params
     * @param string $fname
     * @param string $path
     *
     * @return binary
     */
    public function gerar( $phtml, $fname, $path )
    {
        \Core_Doc_Factory::setFilePath($path);
        return \Core_Doc_Factory::download($phtml, $this->getParams(), $fname);
    }
}
