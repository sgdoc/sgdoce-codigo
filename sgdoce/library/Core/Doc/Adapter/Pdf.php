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
 * Adapter para geração de Pdfs
 *
 * @package      Core
 * @subpackage   Doc
 * @subpackage   Adapter
 * @name         Abstract
 * @category     Adapter
 */
class Core_Doc_Adapter_Pdf extends Core_Doc_Adapter_Abstract
{
    public function convertHtmlToFormat($orientation='P')
    {
        require_once 'html2pdf/html2pdf.class.php';
        $html2pdf = new HTML2PDF($orientation,'A4','fr');
        $html2pdf->setDefaultFont('times');
        $html2pdf->WriteHTML($this->docTemplate->render());

        $this->doc = $html2pdf->Output(NULL, 'S');
    }
}