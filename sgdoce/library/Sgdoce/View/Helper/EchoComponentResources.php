<?php
/**
 * Copyright 2012 do ICMBio
 *
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

/**
 * @package    Sgdoce
 * @subpackage View
 * @subpackage Helper
 * @name       EchoComponentResources
 * @category   View Helper
 */
class Sgdoce_View_Helper_EchoComponentResources extends Zend_View_Helper_Abstract
{
    /**
     * @var string
     */
    const JS_PREFIX = 'javascript:';

    /**
     * Print the component resources
     * @param  array|string $data
     * @return \void
     */
    public function echoComponentResources ($data)
    {
        $components = array();

        if (is_array($data)){
            $components = $data;
        } else {
            array_push($components, $data);
        }

        foreach ($components as $component) {

            $resources = '_component' . ucfirst($component);

            if (method_exists($this, $resources)) {
                echo $this->$resources();
            } else {
                throw new \Exception(
                    __METHOD__ . ': "' .  $component . '" não implementado'
                );
            }
        }
    }

    /**
     * @return string
     */
    private function _componentPdfViewer ()
    {
        $resources = '';
        $resources .= $this->_stylesheet($this->_urlLocal('components/pdf-viewer/pdf-viewer.css'));
        $resources .= $this->_javascript($this->_urlLocal('library/pdf.js/components/compatibility.js'));
        $resources .= $this->_javascript($this->_urlLocal('library/pdf.js/pdf.js'));
        $resources .= $this->_javascript(self::JS_PREFIX . 'PDFJS.workerSrc="/js/library/pdf.js/pdf.worker.js";');
        $resources .= $this->_javascript($this->_urlLocal('components/pdf-viewer.js'));
        return $resources;
    }

    /**
     * @return string
     */
    private function _componentUploader ()
    {
        $resources = '';
        $resources .= $this->_stylesheet($this->_urlCDN('common/plupload/jquery.plupload.queue.min.css'));
        $resources .= $this->_stylesheet($this->_urlCDN('component/uploader/uploader.css'));
        $resources .= $this->_javascript($this->_urlCDN('common/plupload/plupload.full.min.js'));
        $resources .= $this->_javascript($this->_urlCDN('common/plupload/jquery.plupload.queue.min.js'));
        $resources .= $this->_javascript($this->_urlCDN('common/plupload/i18n/pt-br.min.js'));
       // $resources .= $this->_javascript($this->_urlCDN('common/plupload/plupload.browserplus.min.js'));
        $resources .= $this->_javascript($this->_urlCDN('component/uploader/uploader.js'));
        return $resources;
    }

    /**
     * @return string
     */
    private function _componentTreeview ()
    {
        $resources = '';
        $resources .= $this->_stylesheet($this->_urlLocal('jquery.treeview.css'));
        $resources .= $this->_javascript($this->_urlLocal('library/jquery.treeview.js'));
        return $resources;
    }

    /**
     * @return string
     */
    private function _componentQuickCompleter ()
    {
        $resources = '';
        $resources .= $this->_javascript($this->_urlCDN('component/quick-completer/quick-completer.min.js'));
        $resources .= $this->_javascript($this->_urlCDN('component/quick-completer/quick-completer-config.min.js'));
        return $resources;
    }

    /**
     * @param string $stylesheet
     * @return string
     */
    private function _stylesheet ($stylesheet)
    {
        return $this->view->headLink()->setStylesheet($stylesheet);
    }

    /**
     * @param string $javascript
     * @return string
     */
    private function _javascript ($javascript)
    {
        if (strpos($javascript, self::JS_PREFIX) === 0) {
            $script = str_replace(self::JS_PREFIX,'',$javascript);
            return $this->view->headScript()->setScript($script);
        }
        return $this->view->headScript()->setFile($javascript);
    }

    /**
     * @param string $url
     * @return string
     */
    private function _urlCDN ($url)
    {
        return $this->view->baseUrlCDN($url);
    }

    /**
     * @param string $url
     * @return string
     */
    private function _urlLocal ($url)
    {
        $type = preg_filter('@^.*\.(.*)$@', '$1', $url);
        return $this->view->assetUrl($url, array('address' => $type));
    }
}
