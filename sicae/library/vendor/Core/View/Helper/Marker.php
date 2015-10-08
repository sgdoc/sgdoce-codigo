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
 * @package    Core
 * @subpackage View
 * @subpackage Helper
 * @name       Marker
 * @category   View Helper
 */
class Core_View_Helper_Marker extends Zend_View_Helper_Abstract
{
    const NONE      = "";
    const IMPORTANT = "important";
    const WARNING   = "warning";
    const SUCCESS   = "success";
    const INFO      = "info";
    const INVERSE   = "inverse";

    /**
     * Cria um "destaque" para um determinado label
     * @param  integer $type tipo do marcador (Core_View_Helper_Marker::NONE,
     * @param  string  $label texto a ser apresentado
     * @param  string  $class classe css
     * @return string
     */
    public function marker($text, $type = Core_View_Helper_Marker::NONE, $cssClass = NULL)
    {
        if ($cssClass) {
            $cssClass = '<span class="'.$cssClass.' '.$cssClass.'-'.$type.'">';
        } else {
            $cssClass = '<span class="label label-'.$type.'">';
        }

        return $cssClass .= $text . "</span>";
    }
}