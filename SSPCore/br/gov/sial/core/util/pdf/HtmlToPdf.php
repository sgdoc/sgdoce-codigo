<?php
/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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

namespace br\gov\sial\core\util\pdf;
use br\gov\sial\core\SIALAbstract;

class HtmlToPdf
{
    /**
     * @var char
     * */
    const T_DOCPDF_FORCE_DOWNLOAD = 'D';

    /**
     * @var char
     * */
    const T_DOCPDF_RETURN_CONTENT = 'S';
    /**
     * @var char
     * */
    const T_DOCPDF_ORIENTATION_PORTRAIT = 'P';
    /**
     * @var char
     * */
    const T_DOCPDF_ORIENTATION_LANDSCAPE = 'L';
    /**
     * @var string
     * */
    const T_DOCPDF_TEMPLATE_NOT_FOUND = 'Template indisponível';

    private $_data;

    /**
     * @var string
     * */
    private $_encode = NULL;
    /**
     * @var string
     */
    private $_orientation = NULL;
     /**
     * @var string
     * */
    private $_templateDir = NULL;

    /**
     * @param string $encode
     * */
    public function __construct ($orientation=self::T_DOCPDF_ORIENTATION_PORTRAIT,$encode = 'UTF-8')
    {
        $this->_orientation = $orientation;
        $this->_encode = $encode;
    }

    /**
     * @param string $name <nome do documento gerado>
     * @param string $template
     * @param stdClass $data
     * @param boolean $option
     * @return Printable
     * @throws Exception
     * */
    public function document ($name, $template, $data = NULL, $option = FALSE)
    {
        $data = (object) $data;

        $this->_data = $data;

        $this->_data->_title = $name;
        $this->_data->_orientationPortrait = $this->_orientation === self::T_DOCPDF_ORIENTATION_PORTRAIT;

        $this->applyCharset();

        $content = $this->getTemplate($template);

        $this->applyVar($data, $content);

        $data->now = date('d/m/Y');

        return $this->documentWithContent($name, $content, $option);
    }

    public function documentWithContent($name, $content, $option)
    {
        $html2pdf = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR. "lib/html2pdf/html2pdf.class.php";

        require_once($html2pdf);

        if (TRUE === $option) {
            $option = self::T_DOCPDF_FORCE_DOWNLOAD;
        }

        $html2pdf = new \HTML2PDF( $this->_orientation ,'A4','pt', true, $this->_encode, 2);

        $html2pdf->WriteHTML($content);

        if ($name) {
            if (substr($name,-4)!=='.pdf') {
                $name .= '.pdf';
            }
        }

        if (self::T_DOCPDF_RETURN_CONTENT == $option) {
            $name = NULL;
        }

        $content = $html2pdf->Output($name, $option);

        if (self::T_DOCPDF_FORCE_DOWNLOAD === $option) {
            die;
        }

        return $content;
    }

    public function applyCharset ()
    {
        if (PHP_SAPI !== 'cli') {
            header('Content-type: text/html; charset=' . $this->_encode);
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', FALSE);
            header('Pragma: no-cache');
        }
    }

    /**
     * @param string $templateDir
     */
    public function setTemplateDir ($templateDir)
    {
        $this->_templateDir = $templateDir;
    }

    /**
     * @return string
     */
    public function getTemplateDir ()
    {
        if (empty($this->_templateDir)) {
            $this->setTemplateDir(__DIR__ . DIRECTORY_SEPARATOR . 'template');
        }
        return $this->_templateDir;
    }

    /**
     * @return string
     * @throws Exception
     * */
    public function getTemplate ($filepath)
    {
        if (!is_file($filepath)) {
            throw new Exception (self::T_DOCPDF_TEMPLATE_NOT_FOUND);
        }

        ob_start();
            require $filepath;
            $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    public function applyVar ($data, &$content)
    {
        foreach ($data as $key => $value) {

            /* adiciona suporte a tipos nao escalar */
            if (!is_scalar($value)) {
                // $this->_data[$key] = $value;
                continue;
            }

            $content = str_replace("{{$key}}", $value, $content);
        }
    }

    /**
     * Parametros usados da emissão do template
     *
     * return object
     */
    private function layout()
    {
        return $this->_data;
    }
}