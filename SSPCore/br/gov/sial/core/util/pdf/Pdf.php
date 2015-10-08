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
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\lib\FPDF\FPDF,
    br\gov\sial\core\util\lib\FPDF\WriteHTML,
    br\gov\sial\core\util\pdf\exception\PdfException,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * <p>
 * Utilitário de manipulacao de PDF
 * Esta classe tem por finalidade suprir necessidades na geracao/manipulacao
 * de arquivos PDF.
 * </p>
 * <p>
 *    Esta classe encapsula os metodos da class FPDF e que portanto, a documentacao
 *    desta ultima devera ser consultado para sanar possiveis duvidas quanto ao uso
 *    de suas funcionalidades. Deve-se, ainda, verificar quais funcionalidades estao
 *    disponiveis para uso.
 * </p>
 * <p>
 *  Nota:A classe foi criada pelo Mario e commita pela CTI.
 * </p>
 * @package br.gov.sial.core.util.pdf
 * @subpackage pdf
 * @name Pdf
 * */
class Pdf extends SIALAbstract
{
    /**
     * chave de recuperacao do objeto
     *
     * @param string
     * */
    private $_hashKey;

    /**
     * ponteiro para classe geradora de pdf
     *
     * @var FPDF
     * */
    private $_pdf;

    /**
     * constutor
     *
     * <ul>
     *    <li>orientatopm
     *      <ul>
     *        <li>P - Retrato (Padrao)</li>
     *        <li>L - Paisagem</li>
     *     </ul>
     *    </li>
     *    <li>unit
     *      <ul>
     *        <li>pt: pontos (point)</li>
     *        <li>mm: milimetros</li>
     *        <li>cm: centrimetro</li>
     *        <li>in: polegadas</li>
     *     </ul>
     *    </li>
     *    <li>size
     *      <ul>
     *        <li>A3</li>
     *        <li>A4</li>
     *        <li>A5</li>
     *        <li>Letter</li>
     *        <li>Legal</li>
     *        <li>ou, array(float, float)</li>
     *     </ul>
     *    </li>
     * </ul>
     *
     * @example
     * <?php
     *   # cria documento com dimensoes personalizadas
     *   $pdf = new Pdf('P', 'mm', array(100, 150));
     *
     *  # cria documetno tamanho A4
     *   $pdf = new Pdf('P', 'mm', 'A4');
     * ?>
     *
     * @param string $orientation
     * @param string $unit
     * @param strin mixed $size
     * */
    public function __construct ($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        $this->_pdf = new WriteHTML();
        $this->_pdf->FPDF($orientation, $unit, $size);
    }

    /**
     * adiciona uma nova pagina ao Pdf
     *
     * <p>
     *   O primeiro <i>param</i> define a orientacao da pagina e pode variar retrato e paisagem:
     *  <ul>
     *    <li><b>P</b> Portrait</li>
     *    <l><b>L</b> Landscape</li>
     *  </ul>
     * </p>
     * <p>
     *     O segundo <i>param</i> define o tamanho da pagina.
     *  <ul>
     *    <li><b>A3</b></li>
     *    <li><b>A4</b></li>
     *    <li><b>A5</b></li>
     *    <li><b>Letter</b></li>
     *    <li><b>Legal</b></li>
     *    <li>array(float $width, flaot $height)</li>
     *  </ul>
     * <p>
     *
     * @param string|null $orientation
     * @param int|null $size
     * @return Pdf
     */
    public function addPage ($orientation = NULL, $size = NULL)
    {
        $this->_pdf->AddPage($orientation, $size);
        return $this;
    }

    /**
     * importa font TrueType, OpenType ou Type1
     *
     * @param string $family
     * @param string|null $style
     * @param string|null $file
     * @return Pdf
     * */
    public function addFont ($family, $style = NULL, $file = NULL)
    {
        $this->_pdf->AddFont($family, $style, $file);
        return $this;
    }

    /**
     * renderiza cabecalho da pagina
     *
     * <b>Nota</b>: Este metodo deve ser reescrito numa subclasse e apos sua definicao devera retornar $this;
     *
     * @code
     * <?php
     *   # improta a classe base
     *   use br\gov\sial\core\util\pdf\Pdf;
     *
     *  # define uma nova classe baseando-se na em util\Pdf;
     *  class MyPdf extends Pdf
     *  {
     *    public function header ()
     *    {
     *        # define a fonte que sera utilizada no cabecalho
     *        $this->setFont('Arial', 'B', 15);
     *
     *        # imprime o titulo da pagina ao centro
     *        $this->cell(30, 10, 'Título', 1, 0, 'C')
     *
     *         $this->ln(20);
     *
     *        return $this;
     *    }
     *  }
     *
     * @return Pdf
     * */
    public function header ()
    {
        return $this;
    }

    /**
     * renderiza rodape da pagina
     *
     * <b>Nota</b>: Este metodo deve ser reescrito numa subclasse e apos sua definicao devera retornar $this;
     *
     * @code
     * <?php
     *   # improta a classe base
     *   use br\gov\sial\core\util\pdf\Pdf;
     *
     *  # define uma nova classe baseando-se na em util\Pdf;
     *  class MyPdf extends Pdf
     *  {
     *    public function footer ()
     *    {
     *        # define a posicao do rodape
     *        $this->setY(-15);
     *
     *        # define a fonte que sera utilizada no rodape
     *        $this->setFont('Arial', 'I', 8);
     *
     *        # imprime o numero da pagina ao centro
     *        $this->cell(0, 10, 'Pagina # ' . $this->pageNo(), 0, 0, 'C');
     *
     *        return $this;
     *    }
     *  }
     *
     * @return Pdf
     * */
    public function footer ()
    {
        return $this;
    }

    /**
     * inseri uma imagem ao documento
     *
     * <b>Note</b>: Os tipos suportados são:
     * <ul>
     *    <li>JPEG
     *      <ul>
     *          <li>gray scales</li>
     *          <li>true color (24 bits)</li>
     *          <li>CMYK (32 bits)</li>
     *      </ul>
     *    </li>
     *    <li>PNG
     *      <ul>
     *          <li>gray scales on at most 8 bits (256 levels)</li>
     *          <li>indexed colors</li>
     *          <li>true colors (24 bits)</li>
     *      </ul>
     *    </li>
     *    <li>GIF</li>
     * </ul>
     * <p>
     *  É suportado transparencia para os tipos que tenham este recurso.
     * </p>
     * <p>
     *  Para GIF animados apenas o primeiro quadro será mostrado.
     * </p>
     * <p>
     *  O formato pode ser informado manual ou detectado pela extensao do arquivo.
     * </p>
     * <p>
     *  <b>Dependencia</b>: A extensao GD e' requerida ao usar o tipo <b>GIF</b>
     * </p>
     *
     * @example
     * @code
     *    $pdf->image('logo.png', 10, 10, -300);
     * @endcode
     *
     * @param string $filename
     * @param float $posX
     * @param float $posY
     * @param float $width
     * @param float $height
     * @param string $type
     * */
    public function image ($filename, $posX = NULL, $posY = NULL, $width = 0, $height = 0, $type = NULL)
    {
        $this->_pdf->Image($filename, $posX, $posY, $width, $height, $type);
        return $this;
    }

    /**
     * desenha um alinha entre dois pontos
     *
     * @param float $posXOne
     * @param float $posYOne
     * @param float $postXTwo
     * @param float $postYTwo
     * @return Pdf
     * */
    public function line ($posXOne, $posYOne, $postXTwo, $postYTwo)
    {
        $this->_pdf->Line($posXOne, $posYOne, $postXTwo, $postYTwo);
        return $this;
    }

    /**
     * Envia para o browser o resultado
     * @param string $name
     * @param string $destination
     * @return Pdf
     */
    public function outPut ($name = '', $destination = '')
    {
        $this->_pdf->OutPut($name, $destination);
        return $this;
    }

    /**
     * Abre o documento
     * @return Pdf
     */
    public function open ()
    {
        $this->_pdf->Open();
        return $this;
    }

    /**
     * Fecha o documento
     * @return Pdf
     */
    public function close ()
    {
        $this->_pdf->Close();
        return $this;
    }

    /**
     * Imprime na posição atual
     * 
     * @param string $text
     * @param string $link
     * @return Pdf
     */
    public function write ($text = '', $link = '')
    {
        $this->_pdf->Write(10, $text, $link);
        return $this;
    }

    /**
     * Imprime em uma posição especifica
     * 
     * @param int $posX
     * @param int $posY
     * @param string $text
     * @return Pdf
     */
    public function text ($posX, $posY, $text)
    {
        $this->_pdf->Text($posX, $posY, $text);
        return $this;
    }

    /**
     * desenha uma celula no documento
     *
     * @param float $width
     * @param float $height
     * @param string $text
     * @param mixed $border
     * @param integer $ln
     * @param string $align
     * @param boolean $fiil
     * @param mixed $link
     * @return Pdf
     */
    public function cell ($width,
                          $height = 0 ,
                          $text = NULL,
                          $border = 0,
                          $lnx = 0,
                          $align = 'L',
                          $fill = FALSE,
                          $link = NULL
                         )
    {
        $this->_pdf
               ->Cell($width, $height, $text, $border, $lnx, $align, $fill, $link);
        return $this;
    }

    /**
     * define a fonte que sera utilizada para imprimir o texto
     *
     * <ul>
     *    <li>family
     *      <ul>
     *        <li>Courier</li>
     *        <li>Helvetica or Arial</li>
     *        <li>Times (serif)</li>
     *        <li>Symbol (symbolic)</li>
     *        <li>ZapfDingbats (symbolic)</li>
     *      </ul>
     *   </li>
     *   <li>style
     *      <ul>
     *        <li>regular (default)</li>
     *        <li><b>B</b> Bold</li>
     *        <li><b>I</b> Italic</li>
     *        <li><b>U</b> underline</li>
     *      </ul>
     *   </li>
     *   <li>size
     *      <ul>
     *        <li>O tamanho padrao e' de 12 poits</li>
     *      </ul>
     *   </li>
     *
     * @code
     * <?php
     *    // Times regular 12
     *   $pdf->setFont('Times');
     *
     *   // Arial bold 14
     *   $pdf->setFont('Arial','B',14);
     *
     *   // Removes bold
     *   $pdf->setFont('');
     *
     *   // Times bold, italic and underlined 14
     *   $pdf->setFont('Times','BIU');
     * ?>
     * @endcode
     *
     * @param string $family
     * @param string $style
     * @param float $size
     * @return Pdf
     */
    public function setFont ($family, $style = '', $size = 0)
    {
        $this->_pdf->SetFont($family, $style, $size);
        return $this;
    }

    /**
     * define o tamanho da font em points
     *
     * @param float $size
     * @return Pdf
     * */
    public function setFontSize ($size)
    {
        $this->_pdf->SetFontSize($size);
        return $this;
    }

    /**
     * seta o valor das margens
     * 
     * @param int $left
     * @param int $top
     * @param int $right
     * @return Pdf
     */
    public function setMargins ($left, $top, $right = NULL)
    {
        $this->_pdf->SetMargins($left, $top, $right);
        return $this;
    }

    /**
     * Quebra de linha
     * 
     * @param float $heigthBreak
     * @return Pdf
     */
    public function ln ($heigthBreak = NULL)
    {
        $this->_pdf->Ln($heigthBreak);
        return $this;
    }

    /**
     * Seta a margem superior
     * 
     * @param float $margin
     * @return Pdf
     */
    public function setTopMargin ($margin)
    {
        $this->_pdf->SetTopMargin($margin);
        return $this;
    }

    /**
     * Seta a margem da esquerda
     * 
     * @param float $margin
     * @return Pdf
     */
    public function setLeftMargin ($margin)
    {
        $this->_pdf->SetLeftMargin($margin);
        return $this;
    }

    /**
     * Retorna a abscissa da posição atual
     *
     * @return float
     * */
    public function getX ()
    {
        return $this->_pdf->GetX();
    }

    /**
     * Retorna a ordenada da posição atual
     *
     * @return float
     * */
    public function getY ()
    {
        return $this->_pdf->GetY();
    }

    /**
     * define o autor do documento
     *
     * @param string $author
     * @return Pdf
     * */
    public function setAuthor ($author)
    {
        $this->_pdf->SetAuthor($author, $isUTF8 = TRUE);
        return $this;
    }

    /**
     * habiltia quebra de linha automatica
     *
     * @param boolean $enableMode
     * @param float $margin
     * @return Pdf
     * */
    public function setAutoPageBreak ($enableMode, $margin = 2.0)
    {
        $this->_pdf->SetAutoPageBreak($enableMode, $margin);
        return $this;
    }

    /**
     * define o criador do documento
     *
     * @param string $creator
     * @return Pdf
     * */
    public function setCreator ($creator)
    {
        $this->_pdf->SetCreator($creator, $isUTF8 = TRUE);
        return $this;
    }

    /**
     * posiciona o cursor na posicao Y informada, em referencia ao plano cartesiano do documento
     *
     * @param float $posY
     * @return Pdf
     * */
    public function setY ($posY)
    {
        $this->_pdf->SetY($posY);
        return $this;
    }

    /**
     * posiciona o cursor na posicao X informada, em referencia ao plano cartesiano do documento
     *
     * @param float $posX
     * @return Pdf
     * */
    public function setX ($posX)
    {
        $this->_pdf->SetX($posX);
        return $this;
    }

    /**
     * seta a margem da direita
     * 
     * @param float $margin
     * @return Pdf
     */
    public function setRightMargin ($margin)
    {
        $this->_pdf->SetRightMargin($margin);
        return $this;
    }

    /**
     * define o titulo do documento
     *
     * @param string $title
     * @return Pdf
     * */
    public function setTitle ($title)
    {
        $this->_pdf->SetTitle($title, $isUTF8 = TRUE);
        return $this;
    }

    /**
     * desenha um retangulo
     *
     * @param float $posX
     * @param float $posY
     * @param float $width
     * @param float $height
     * @param string $style
     * @return Pdf
     * */
    public function rect ($posX, $posY, $width, $height, $style = NULL)
    {
        $this->_pdf->Rect($posX, $posY, $width, $height, $style);
        return $this;
    }

    /**
     * define cor para o elemento
     *
     * @param integer $red
     * @param integer $gre
     * @param integer $blu
     * @return Pdf
     * */
    public function setDrawColor ($red, $gre, $blu)
    {
        $this->_pdf->SetDrawColor($red, $gre, $blu);
        return $this;
    }

    /**
     * define a cor do preenchimento
     *
     * @param integer $red
     * @param integer $gre
     * @param integer $blu
     * @return Pdf
     * */
    public function setFillColor ($red, $gre, $blu)
    {
        $this->_pdf->SetFillColor($red, $gre, $blu);
        return $this;
    }

    /**
     * define a cor do texto
     *
     * @param integer $red
     * @param integer $gre
     * @param integer $blu
     * @return Pdf
     * */
    public function setTextColor ($red, $gre = 0, $blu = 0)
    {
        $this->_pdf->SetTextColor($red, $gre, $blu);
        return $this;
    }

    /**
     * retorna o comprimento do texto
     *
     * @param string $text
     * @return integer
     * */
    public function getStringWidth ($text)
    {
        return $this->_pdf->GetStringWidth($text);
    }

    /**
     * define o comprimento da linha
     *
     * @param float $width
     * @return PDf
     * */
    public function setLineWidth ($width)
    {
        $this->_pdf->SetLineWidth($width);
        return $this;
    }

    /**
     * Imprime um texto com quebras de linha
     * 
     * @param float $width
     * @param float $height
     * @param string $text
     * @param integer $border
     * @param char $align
     * @param boolean $fill
     * @return Pdf
     * */
    public function multiCell ($width, $height, $text, $border = 0, $align = 'J', $fill = FALSE)
    {
        $this->_pdf->MultiCell($width, $height, $text, $border, $align, $fill);
        return $this;
    }

    /**
     * retorna o numero corrente da pagina
     *
     * @return integer
     * */
    public function pageNo ()
    {
        return $this->_pdf->pageNo();
    }

    /**
     * Este metodo sera executado automaticamente sempre que ocorrer um erro na geracao do pdf
     *
     * @param string $message
     * @return Pdf
     * */
    public function onError ($message)
    {
        $this->_pdf->Error($message);
    }
}