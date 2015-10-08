<?php
require_once 'Fpdf/fpdf.php';
require_once 'Barcode/code128.php';

/**
 * Copyright 2014 do ICMBio
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

class Sgdoce_EtiquetaPdf {

    private $_count;
    /**
     *
     * @var string
     */
    private $_pdfOutputName = 'Etiquetas.pdf';
    /**
     * Quantidade de etiquetas por página
     * @var Integer
     */
    private $_etiquetas = 65;
    /**
     * Quantidade de paginas
     * @var Integer
     */
    private $_paginas = 0;
    /**
     * Digitais a serem impressas
     * @var array
     */
    private $_digitais = array();
    /**
     * Tipo de saida do PDF gerado
     * @var char values ['I', 'D', 'F', 'S']
     */
    private $_pdfOutputType = 'I';
    /**
     * Texto a ser impresso acima do codigo de barras
     * @var string
     */
    private $_textoEtiqueta = null;
    /**
     * Objeto pdf a ser manipulado
     * @var PDF_Code128
     */
    private $_pdf;

    /**
     * Identificador de etiqueta com nup siorg
     *
     * @var boolean
     */
    private $_etiquetaComNUP = false;

    /**
     *
     * @var \Core_Filter_MaskNumber
     */
    private $_maskNumber;

    /**
     *
     * @var integer
     */
    private $angle=0;

    /**
     *
     * @var float
     */
    private $M_PI = 3.14159;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->_count = 0;
        $this->_maskNumber = new Core_Filter_MaskNumber();
        //Mascara para NUP siorg
        $this->_maskNumber->setMask('9999999.99999999/9999-99');

    }

    public function getTextoEtiqueta()
    {
        return $this->_textoEtiqueta;
    }

    public function setTextoEtiqueta($textoEtiqueta)
    {
        $this->_textoEtiqueta = trim($textoEtiqueta);
        return $this;
    }

    public function getPdfOutputType()
    {
        return $this->_pdfOutputType;
    }

    public function setPdfOutputType($pdfOutputType = 'I')
    {
        $this->_pdfOutputType = $pdfOutputType;
        return $this;
    }

    /**
     * @return void
     */
    public function setDigitais(array $arrDigital)
    {
        $this->_setPaginas(count($arrDigital));
        $this->_digitais = $arrDigital;

        return $this;
    }

    public function getEtiquetaComNUP ()
    {
        return $this->_etiquetaComNUP;
    }

    public function setEtiquetaComNUP ($etiquetaComNUP = false)
    {
        $this->_etiquetaComNUP = $etiquetaComNUP;
        return $this;
    }


    /**
     * @return void
     */
    private function _setPaginas($registros)
    {
        $this->_paginas = ceil($registros / $this->_etiquetas);
    }

    /**
     * @return void
     */
    private function _prepararPdf() {
        $this->_pdf = new PDF_Code128('P', 'cm', 'A4');
        $this->_pdf->SetMargins(0, 0, 0, 0);
        $this->_pdf->SetTitle('Folha de Etiquetas');
        $this->_pdf->SetFont("Arial", "", 7);
        $this->_pdf->Open();
    }

    /**
     * @return void
     * @param integer $linha
     */
    private function _gerarLinhas($linha)
    {
        switch ($linha) {
            case 1:
                $yImagem = 1.8;
                $yTexto = $yImagem - 0.1;
                break;
            case 2:
                $yImagem = 3.8;
                $yTexto = $yImagem - 0.1;
                break;
            case 3:
                $yImagem = 6.0;
                $yTexto = $yImagem - 0.1;
                break;
            case 4:
                $yImagem = 8.1;
                $yTexto = $yImagem - 0.1;
                break;
            case 5:
                $yImagem = 10.3;
                $yTexto = $yImagem - 0.1;
                break;
            case 6:
                $yImagem = 12.3;
                $yTexto = $yImagem - 0.1;
                break;
            case 7:
                $yImagem = 14.5;
                $yTexto = $yImagem - 0.1;
                break;
            case 8:
                $yImagem = 16.5;
                $yTexto = $yImagem - 0.1;
                break;
            case 9:
                $yImagem = 18.7;
                $yTexto = $yImagem - 0.1;
                break;
            case 10:
                $yImagem = 20.8;
                $yTexto = $yImagem - 0.1;
                break;
            case 11:
                $yImagem = 22.9;
                $yTexto = $yImagem - 0.1;
                break;
            case 12:
                $yImagem = 25.0;
                $yTexto = $yImagem - 0.1;
                break;
            case 13:
                $yImagem = 27.2;
                $yTexto = $yImagem - 0.1;
                break;
        }

        $xImagem = 0.9;
        $xTexto = 1.1;

        for ($ws = 1; $ws <= 5; $ws++) {
            if (isset($this->_digitais[$this->_count])) {
                $nuEtiqueta = $this->_digitais[$this->_count]['nuEtiqueta'];
                if ($ws != 5) {

                    $this->_pdf->Text($xTexto + 0.3, $yTexto, $this->_textoEtiqueta);
                    $this->_pdf->Code128($xImagem, $yImagem, $nuEtiqueta, 3, 0.9);
                    $this->_pdf->Text($xTexto + 0.3, ($yTexto + 1.3), $nuEtiqueta);

                    $this->_count++;

                    if ($ws != 4) {
                        if ($ws == 1) {
                            $xImagem = $xImagem + 4.2;
                            $xTexto  = $xTexto  + 4.2;
                        } else {
                            if ($ws != 2) {
                                $xImagem = $xImagem + 4.1;
                                $xTexto  = $xTexto  + 4.1;
                            } else {
                                $xImagem = $xImagem + 3.8;
                                $xTexto  = $xTexto  + 3.8;
                            }
                        }
                    }
                } else {

                    $xImagem = $xImagem + 4.0;
                    $xTexto = $xTexto + 4.3;
                    $yImagem = $yImagem;
                    $yTexto = $yTexto;

                    $this->_pdf->Text($xTexto, $yTexto, $this->_textoEtiqueta);
                    $this->_pdf->Code128($xImagem, $yImagem, $nuEtiqueta, 3, 0.9);
                    $this->_pdf->Text($xTexto, ($yTexto + 1.3), $nuEtiqueta);

                    $this->_count++;
                }
            }
        }
    }

    /**
     * @return void
     * @param integer $linha
     */
    private function _gerarLinhasComNUP($linha)
    {
        switch ($linha) {
            case 1:
                $yImagem = 1.8;
                $yTexto = $yImagem - 0.1;
                break;
            case 2:
                $yImagem = 3.8;
                $yTexto = $yImagem - 0.1;
                break;
            case 3:
                $yImagem = 6.0;
                $yTexto = $yImagem - 0.1;
                break;
            case 4:
                $yImagem = 8.1;
                $yTexto = $yImagem - 0.1;
                break;
            case 5:
                $yImagem = 10.3;
                $yTexto = $yImagem - 0.1;
                break;
            case 6:
                $yImagem = 12.3;
                $yTexto = $yImagem - 0.1;
                break;
            case 7:
                $yImagem = 14.5;
                $yTexto = $yImagem - 0.1;
                break;
            case 8:
                $yImagem = 16.5;
                $yTexto = $yImagem - 0.1;
                break;
            case 9:
                $yImagem = 18.7;
                $yTexto = $yImagem - 0.1;
                break;
            case 10:
                $yImagem = 20.8;
                $yTexto = $yImagem - 0.1;
                break;
            case 11:
                $yImagem = 22.9;
                $yTexto = $yImagem - 0.1;
                break;
            case 12:
                $yImagem = 25.0;
                $yTexto = $yImagem - 0.1;
                break;
            case 13:
                $yImagem = 27.2;
                $yTexto = $yImagem - 0.1;
                break;
        }

        $xImagemNup         = 0.9;
        $xImagemEtiqueta    = 1.9;
        $xTextoNup          = 0.8;
        $xTextoEtiqueta     = 2.35;
        $xTextoRotated      = 0.65;
        $widthBarcodeDigital= 2;
        $widthBarcodeNup    = 3;
        $heigthBarcode      = 0.4;

        for ($ws = 1; $ws <= 5; $ws++) {
            if (isset($this->_digitais[$this->_count])) {
                $nuEtiqueta = $this->_digitais[$this->_count]['nuEtiqueta'];
                $nuNUP = $this->_digitais[$this->_count]['nuNupSiorg'];
                $nuNUPMask = $this->_maskNumber->filter($nuNUP);

                if ($ws != 5) {
                    $this->_rotatedText($xTextoRotated, $yTexto+1.2, $this->_textoEtiqueta, 90);

                    $this->_pdf->Text($xTextoNup, $yTexto, $nuNUPMask);
                    $this->_pdf->Code128($xImagemNup, $yImagem, $nuNUP, $widthBarcodeNup, $heigthBarcode);

                    $this->_pdf->Code128($xImagemEtiqueta, $yImagem+0.5, $nuEtiqueta, $widthBarcodeDigital, $heigthBarcode);
                    $this->_pdf->Text($xTextoEtiqueta, $yTexto+1.2, $nuEtiqueta);

                    $this->_count++;

                    if ($ws != 4) {
                        if ($ws == 1) {
                            $xImagemNup     = $xImagemNup       + 4.1;
                            $xImagemEtiqueta= $xImagemEtiqueta  + 4.1;
                            $xTextoNup      = $xTextoNup        + 4.1;
                            $xTextoEtiqueta = $xTextoEtiqueta   + 4.1;
                            $xTextoRotated  = $xTextoRotated    + 4.1;
                        } else {
                            if ($ws != 2) {
                                $xImagemNup     = $xImagemNup       + 4.1;
                                $xImagemEtiqueta= $xImagemEtiqueta  + 4.1;
                                $xTextoNup      = $xTextoNup        + 4.1;
                                $xTextoEtiqueta = $xTextoEtiqueta   + 4.1;
                                $xTextoRotated  = $xTextoRotated    + 4.1;
                            } else {
                                //3º Coluna de etiqueta
                                $xImagemNup     = $xImagemNup       + 4.1;
                                $xImagemEtiqueta= $xImagemEtiqueta  + 4.1;
                                $xTextoNup      = $xTextoNup        + 4.1;
                                $xTextoEtiqueta = $xTextoEtiqueta   + 4.1;
                                $xTextoRotated  = $xTextoRotated    + 4.1;
                            }
                        }
                    }
                } else {

                    $xImagemNup     = $xImagemNup       + 4.1;
                    $xImagemEtiqueta= $xImagemEtiqueta  + 4.1;
                    $xTextoNup      = $xTextoNup        + 4.1;
                    $xTextoEtiqueta = $xTextoEtiqueta   + 4.1;
                    $xTextoRotated  = $xTextoRotated    + 4.1;
                    $yImagem        = $yImagem;

                    $this->_rotatedText($xTextoRotated, $yTexto+1.2, $this->_textoEtiqueta,90);

                    $this->_pdf->Text($xTextoNup, $yTexto, $nuNUPMask);
                    $this->_pdf->Code128($xImagemNup, $yImagem, $nuNUP, $widthBarcodeNup, $heigthBarcode);

                    $this->_pdf->Code128($xImagemEtiqueta, $yImagem+0.5, $nuEtiqueta, $widthBarcodeDigital, $heigthBarcode);
                    $this->_pdf->Text($xTextoEtiqueta, $yTexto+1.2, $nuEtiqueta);

                    $this->_count++;
                }
            }
        }
    }
    /**
     * @return void
     */
    public function generate()
    {
        $this->_prepararPdf();
        for ($i = 1; $i <= $this->_paginas; $i++) {
            $this->_pdf->AddPage();
            for ($x = 1; $x <= 13; $x++) {
                if ($this->getEtiquetaComNUP()) {
                    $this->_gerarLinhasComNup($x);
                }else{
                    $this->_gerarLinhas($x);
                }
            }
        }
        $this->_pdf->Output($this->_pdfOutputName, $this->_pdfOutputType);
    }

    private function _rotate($angle,$x=-1,$y=-1)
    {
        if($x==-1)
            $x=$this->_pdf->x;
        if($y==-1)
            $y=$this->_pdf->y;
        if($this->_pdf->angle!=0)
            $this->_pdf->_out('Q');
            $this->_pdf->angle=$angle;
        if($angle!=0)
        {
            $angle*=$this->M_PI /180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->_pdf->k;
            $cy=($this->_pdf->h-$y)*$this->_pdf->k;
            $this->_pdf->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }

    private function _endpage()
    {
        if($this->angle!=0)
        {
            $this->angle=0;
            $this->_pdf->_out('Q');
        }
        $this->_pdf->_endpage();
    }

    private function _rotatedText($x,$y,$txt,$angle)
    {
        //Text rotated around its origin
        $this->_rotate($angle,$x,$y);
        $this->_pdf->Text($x,$y,$txt);
        $this->_rotate(0);
    }

}