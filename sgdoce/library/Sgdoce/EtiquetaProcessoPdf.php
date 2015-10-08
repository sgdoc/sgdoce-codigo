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

class Sgdoce_EtiquetaProcessoPdf {

    private $_count;
    /**
     *
     * @var string
     */
    private $_pdfOutputName = 'EtiquetaProcesso.pdf';
    
    /**
     * Tipo de saida do PDF gerado
     * @var char values ['I', 'D', 'F', 'S']
     */
    private $_pdfOutputType = 'I';
    
    /**
     * Objeto pdf a ser manipulado
     * @var PDF_Code128
     */
    private $_pdf;

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
    
    
    protected $_nuEtiqueta;
    
    protected $_nuArtefato;
    
    protected $_coAmbitoProcesso;
    
    protected $_dtAutuacao;
    
    protected $_txInteressado;
    
    protected $_txAssunto;
    
    protected $_txAssuntoComplementar;
    
    protected $_flLogo;
    
    protected $_listTxHeader;
    
    protected $_documentoAutuado;



    /**
     * @return void
     */
    public function __construct()
    {
        $this->_count = 0;
        $this->_maskNumber = new Core_Filter_MaskNumber();
    }
    
    /**
     * 
     */
    public function setMaskNuArtefato( $mask )
    {
        //Mascara para NUP siorg
        $this->_maskNumber->setMask($mask);        
    }
    
    /**
     * @return void
     */
    public function setNuEtiqueta( $nuEtiqueta )
    {        
        $this->_nuEtiqueta = $nuEtiqueta;
        return $this;
    }
    
    /**
     * @return 
     */
    public function getNuEtiqueta()
    {
        return $this->_nuEtiqueta;
    }
    
    
    /**
     * @return void
     */
    public function setNuArtefato($nuArtefato)
    {        
        $this->_nuArtefato = $nuArtefato;
        return $this;
    }
    
    /**
     * @return 
     */
    public function getNuArtefato()
    {
        return $this->_nuArtefato;
    }
    
    /**
     * @return void
     */
    public function setDtAutuacao( $dtAutuacao )
    {        
        $this->_dtAutuacao = $dtAutuacao;
        return $this;
    }
    
    /**
     * @return 
     */
    public function getDtAutuacao()
    {
        return $this->_dtAutuacao;
    }
    
    
    /**
     * @return void
     */
    public function setTxInteressado($txInteressado)
    {        
        $this->_txInteressado = $txInteressado;
        return $this;
    }
    
    /**
     * @return 
     */
    public function getTxInteressado()
    {
        return $this->_txInteressado;
    }
    
    /**
     * @return void
     */
    public function setTxAssunto( $txAssunto )
    {        
        $this->_txAssunto = $txAssunto;
        return $this;
    }
    
    /**
     * @return 
     */
    public function getTxAssunto()
    {
        return $this->_txAssunto;
    }

    /**
     * @return void
     */
    public function setTxAssuntoComplementar( $txAssuntoComplementar )
    {        
        $this->_txAssuntoComplementar = $txAssuntoComplementar;
        return $this;
    }
    
    /**
     * @return 
     */
    public function getTxAssuntoComplementar()
    {
        return $this->_txAssuntoComplementar;
    }
    
    /**
     * @return void
     */
    public function setFlLogo( $flLogo )
    {        
        $this->_flLogo = $flLogo;
        return $this;
    }
    
    /**
     * @return 
     */
    public function getFlLogo()
    {
        return $this->_flLogo;
    }
    
    /**
     * @return void
     */
    public function setListTxHeader( $listTxHeader )
    {        
        $this->_listTxHeader = $listTxHeader;
        return $this;
    }
    
    /**
     * @return 
     */
    public function getListTxHeader()
    {
        return $this->_listTxHeader;
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
    
    function getCoAmbitoProcesso() {
        return $this->_coAmbitoProcesso;
    }

    function setCoAmbitoProcesso($coAmbitoProcesso) {
        $this->_coAmbitoProcesso = $coAmbitoProcesso;
        return $this;
    }
    
    function getDocumentoAutuado() {
        return $this->_documentoAutuado;
    }

    function setDocumentoAutuado($documentoAutuado) {
        $this->_documentoAutuado = $documentoAutuado;
        return $this;
    }

        
    /**
     * @return void
     */
    private function _prepararPdf() {
        $this->_pdf = new PDF_Code128('P', 'cm', array(15,15));
        $this->_pdf->SetMargins(0, 0, 0, 0);
        $this->_pdf->SetTitle('Folha de Etiquetas de Processo');
        $this->_pdf->SetFont("Arial", "", 7);
        $this->_pdf->Open();
    }

    /**
     * @return void
     * @param integer $linha
     */
    private function _gerarLinha()
    {        
        $yRec       = 1.6;        
        $xRec       = 1.6;
        
        $yImagem    = $yRec + 0.3;
        $xImagem    = $xRec + 0.3;
        
        $yHeader    = $yRec + 0.5;
        $xHeader    = $xRec + 1.8;
        
        $yTexto     = $yRec + 1.5;    
        $xTexto     = $xRec + 0.3;
        
        $hRec = 6;
        
        // Linhas para interessados, cada caracter ocupa em média 1.51cm
        // A etiqueta possui 11.4cm para escrita na horizontal, que seria igual a 75 caracteres.
        $listLinhasInteressados = array();
        $llinhaInteressados     = 0;
        $lchar                  = 75;
        $lstop                  = false;
        
        $this->_txInteressado   = "Interessados: " . $this->_txInteressado;
        
        if( strlen($this->_txInteressado) > $lchar ) {            
            if($llinhaInteressados == count($listLinhasInteressados)) {
                $lchar = 75;
                $lstop = true;
            }
            $listInteressados = explode(",", $this->_txInteressado);
            
            $txInteressadoLinha = "";
            foreach($listInteressados as $key => $txInteressado) {                
                $txInteressadoLinha .= $txInteressado;
                $endValue           = end($listInteressados);
                
                if(strlen($txInteressadoLinha) < $lchar){                    
                    if( $lstop && $endValue != $txInteressado ) {
                        $txInteressadoLinha .= " e outros";
                    }
                    $listLinhasInteressados[] = $txInteressadoLinha;
                    $txInteressadoLinha = "";
                } else {
                    if( !$lstop && $endValue != $txInteressado  ) {
                        $txInteressadoLinha .= ", ";
                    } else {                        
                        $listWordsInteressado = explode(' ', $txInteressadoLinha); 
                        $txLinhaDoInteressadoLen = "";
                        $txLinhaDoInteressadoTex = "";
                        foreach( $listWordsInteressado as $key => $word ) {
                            $txLinhaDoInteressadoLen .= $word . " ";
                            if( strlen($txLinhaDoInteressadoLen) < $lchar ) {
                                $txLinhaDoInteressadoTex .= $word . " ";
                                if( $listWordsInteressado[$key] == end($listWordsInteressado) ) {
                                    $txLinhaDoInteressadoTex = rtrim($txLinhaDoInteressadoTex);
                                    $listLinhasInteressados[] = $txLinhaDoInteressadoTex;    
                                    $txLinhaDoInteressadoTex = "";
                                    $txLinhaDoInteressadoLen = "";                             
                    }
                            } else {
                                $listLinhasInteressados[] = $txLinhaDoInteressadoTex;
                                $txLinhaDoInteressadoTex = "";
                                $txLinhaDoInteressadoLen = "";                                
                }
                        }
                    }
                }
                if($lstop){
                    break;
                }
            }
        } else {
            $listLinhasInteressados[] = $this->_txInteressado;
        }
        // linhas para assunto complementar
        $listLinhasAssuntoCompl = array();
        $llinhaAssunto          = 1;
        $lchar                  = 75;
        $lstop                  = false;
        $txAssuntoComplLabel    = "Assunto Complementar: ";
        
        if( strlen($this->_txAssuntoComplementar) > 0 ) {
            $this->_txAssuntoComplementar   = mb_strtoupper($this->_txAssuntoComplementar, 'UTF-8');
            $this->_txAssuntoComplementar   = preg_replace('/\s+/', " ", $this->_txAssuntoComplementar);        
            $this->_txAssuntoComplementar   = $txAssuntoComplLabel . $this->_txAssuntoComplementar;                
        }
        if( strlen($this->_txAssuntoComplementar) > $lchar ) {
            $listWords = explode(" ", $this->_txAssuntoComplementar);          
            $totalWords = count($listWords) - 1;
            $linhaAssunto = "";
            foreach( $listWords as $key => $word ){ 
                $lenlinha = strlen($linhaAssunto);
                $lenword  = strlen($word);
                $endvalue = end($listWords);  
                $lcharC   = $lchar;
                
                if( $key == 0 ) {
                    $lcharC = $lchar - strlen($txAssuntoComplLabel);
                }
                
                if( ($lenlinha + $lenword) <= $lcharC ){
                    $linhaAssunto .= " " . $word;
                } else {      
                    $listLinhasAssuntoCompl[] = utf8_decode($linhaAssunto);      
                    $linhaAssunto = " " . $word;
                }
                
                if( $key == $totalWords && $endvalue == $word ) {
                    $listLinhasAssuntoCompl[] = utf8_decode($linhaAssunto);
                }
            }
        } else if(strlen($this->_txAssuntoComplementar) > 0 ) {
            $listLinhasAssuntoCompl[] = utf8_decode($this->_txAssuntoComplementar);
        }
        
        // POSICIONAMENTO DO QUADRO
        $totYTex = $yTexto + (0.35 * (12 + count($listLinhasInteressados) + count($listLinhasAssuntoCompl)));
        
        $diffHRec = ($totYTex - ($hRec + $yRec))/2;
        $yHeader -= (($totYTex - ($hRec + $yRec))/2);
        $yRec    -= $diffHRec + 0.15;
        $xImagem -= (($totYTex - ($hRec + $yRec))/2) - 0.2;
        $yTexto  = ($yHeader + (count($this->_listTxHeader) * 0.35));
        $this->_pdf->Image($this->_flLogo, $yImagem, $xImagem, 1);
        
        foreach($this->_listTxHeader as $txHeader){
            $this->_pdf->SetFont('Arial', 'B', 8);
            $this->_pdf->Text($xHeader, $yHeader, utf8_decode($txHeader));
            $yHeader += 0.35;
        }        
        
        if( !is_null($this->getCoAmbitoProcesso()) 
            && $this->getCoAmbitoProcesso() != 'F' ){
            $nuEtiqueta = $this->_nuArtefato;
        } else {
            $nuEtiqueta = $this->_maskNumber->filter($this->_nuArtefato);
        }
        
        $this->_pdf->SetFont("Arial", "B", 8);
        $yTexto += 0.50;
        $labelNrProcesso = utf8_decode("Número do Processo: ");
        $this->_pdf->Text($xTexto, $yTexto, $labelNrProcesso . $nuEtiqueta);
        $yTexto += 0.35;
        $this->_pdf->Text($xTexto, $yTexto, utf8_decode("Autuação: ") . $this->_dtAutuacao);
        
        foreach( $listLinhasInteressados as $key => $txLinhaInteressado ){
            if($txLinhaInteressado != end($listLinhasInteressados)){
                $txLinhaInteressado .= " ";
            } else {
                $txLinhaInteressado .= ".";
            }
            $yTexto += 0.35;
            $this->_pdf->Text($xTexto, $yTexto, utf8_decode($txLinhaInteressado));
        }
        $yTexto += 0.35;
        $this->_pdf->Text($xTexto, $yTexto, "Assunto: " . utf8_decode($this->_txAssunto));
        $this->_pdf->SetFont("Arial", "B", 7);
        if( count($listLinhasAssuntoCompl) ) {
            foreach( $listLinhasAssuntoCompl as $txLinhaAssuntoCompl ){
                $yTexto += 0.35;
                $this->_pdf->Text($xTexto, $yTexto, trim($txLinhaAssuntoCompl));
            }
        }
        // Dados da Digital     
        $this->_pdf->SetFont("Arial", "B", 8);
        $yTexto += 0.35;
        $digital = $this->getDocumentoAutuado();
        if( $digital instanceof \Sgdoce\Model\Entity\Artefato ) {
            $nuDigital = $digital->getNuDigital()->getNuEtiqueta();
            if( strlen($nuDigital) <= 7 ){
                $nuDigital = str_pad($nuDigital, 7, "0", STR_PAD_LEFT);
            }
            $nuDigitalLinha = "Digital: " . $nuDigital;
            $tipoDocumentoLinha = " Tipo: " . $digital->getSqTipoDocumento()->getNoTipoDocumento();
            $nuArtefatoLinha = "Número: " . $digital->getNuArtefato();
            
            if( strlen( $nuDigitalLinha . $tipoDocumentoLinha . $nuArtefatoLinha ) > 68 ) {                                
                $text = $nuDigitalLinha . 
                        $tipoDocumentoLinha;
                $text = utf8_decode($text);
                $this->_pdf->Text($xTexto, $yTexto, $text); 
                $yTexto += 0.35;       
                $text = $nuArtefatoLinha;
                $text = utf8_decode($text);
                $this->_pdf->Text($xTexto, $yTexto, $text); 
            } else {
                $text = $nuDigitalLinha . 
                        $tipoDocumentoLinha .
                        " - " . $nuArtefatoLinha;
                $text = utf8_decode($text);
                $this->_pdf->Text($xTexto, $yTexto, $text);        
            }                
            
        }
        $yTexto += 0.2;
        $this->_pdf->Code128($xTexto + 1.8, $yTexto, $this->_nuArtefato, 7.9, 0.9);
        $yTexto += 1.25;
        $this->_pdf->SetFont("Arial", "", 9);
        $this->_pdf->Text($xTexto + 3.8, $yTexto, $nuEtiqueta);
        
        $this->_pdf->Rect($xRec, $yRec, 11.8, ($hRec + ($diffHRec * 0.6)));
    }

    /**
     * @return void
     */
    public function generate()
    {
        $this->_prepararPdf();
        $this->_pdf->AddPage();
        $this->_gerarLinha();
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