<?php

/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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

/**
 * @package    Sica
 * @subpackage View
 * @subpackage Helper
 * @name       Sistemas
 * @category   View Helper
 */
class Sica_View_Helper_Sistemas extends Zend_View_Helper_Abstract
{

    public function sistemas($sistemas)
    {
        if (!$sistemas) {
            $msg = \Core_Registry::getMessage()->_('MN170');

            return '<div class="alert alert-error campos-obrigatorios"><button class="close" data-dismiss="alert">×</button>' . $msg . '</div>';
        }

        $htmlize = function ($text) {
            return nl2br(htmlentities($text,ENT_QUOTES,'UTF-8'));
        };

        $html = '';
        $i = 0;
        foreach ($sistemas as $k => $sistema) {
            $firstOfRowClass = ($i++ % 4 === 0) ? ' welcome-system-first' : '';
            $html .= '<div class="span3 thumbnail welcome-system'. $firstOfRowClass .'"';
            $html .= '     rel="popover" data-placement="top" data-trigger="hover"';
            $html .= '     data-content="' . $htmlize($sistema['txDescricao']) . '"';
            $html .= '     title="' . $htmlize($sistema['sgSistema']) . '"';
            $html .= '     data-text-find-me="' . $htmlize($sistema['sgSistema'] . $sistema['noSistema'] . $sistema['txDescricao']) . '">';
            $html .= '    <div class="caption">';
            $html .= '       <h2>' . $htmlize($sistema['sgSistema']) . '</h2>';
            $html .= '       <p>' . $htmlize($sistema['noSistema']) . '</p>';
            $html .= '       <button type="button" class="btn btn-primary" data-loading-text="Abrindo…" data-sistema="' . $sistema['sqSistema'] . '">Acessar <i class="icon icon-white icon-share-alt"></i></button>';
            $html .= '    </div>';
            $html .= '</div>';
        }

        $html .= '<script type="text/javascript" src="' . $this->view->assetUrl('sica/sistema/home.js') . '"></script>';

        return $html;
    }

}