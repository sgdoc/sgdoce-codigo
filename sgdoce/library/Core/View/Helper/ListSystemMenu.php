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
 * @name       Menu
 * @category   View Helper
 */
class Core_View_Helper_ListSystemMenu extends Zend_View_Helper_Abstract
{

    public function listSystemMenu()
    {
        $menuSistema = Core_Integration_Sica_User::getInfoSystems();
        $urlSica = rtrim(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('urlSica'), '/');

        $listaSistema = '<ul class="dropdown-menu">';
        $total = 1;

        foreach ($menuSistema as $key => $sistema) {
            if (\Core_Integration_Sica_User::getUserSystem() == $sistema['sqSistema']) {
                continue;
            }

            $listaSistema .= '<li>'
                . '<a href="javascript:Sistemas.verifica(\''. $urlSica.'\','.$sistema['sqSistema'] . ');">' . $sistema['sgSistema']
                . '</a></li>';

            if ($total == 5) {
                break;
            }

            $total++;
        }

        if ((count($menuSistema) -1) > 4) {
            $listaSistema .= '<li class="divider"></li>';
            $listaSistema .= '<li><a href="' . $urlSica . '/index/home">Todos</a></li>';
        }

        $listaSistema .= '</ul>';

        if (1 === count($menuSistema)) {
            $listaSistema = '';
        }

        return $listaSistema;
    }

}