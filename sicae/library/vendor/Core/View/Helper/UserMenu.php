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
class Core_View_Helper_UserMenu extends Zend_View_Helper_Abstract
{

    public function userMenu()
    {
        $listSystems = $this->view->listSystemMenu();
        $html = '
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="javascript: window.location = window.location.href.indexOf(\'iframe\') > 0 ? window.location.href : window.location.protocol + \'//\' + window.location.hostname;">' . $this->view->logoSystem() . '</a>
                    <div class="nav-collapse">
                        <ul class="nav">
                            <li class="divider-vertical"></li>
                            <li><a class="active" href="javascript: window.location = window.location.href.indexOf(\'iframe\') > 0 ? window.location.href : window.location.protocol + \'//\' + window.location.hostname;">Início</a></li>
                            <li class="divider-vertical"></li>';
        if ($listSystems) {
            $html .= '<li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Sistemas <b class="caret"></b></a>
                                  ' . $listSystems . '
                      </li><li class="divider-vertical"></li>';
        }

        $html .= '</ul>
                       <img class="brandRight" src="/assets/img/icons/marcaICMBio.png" width="120" height="70" alt="ICMBio">
                            ' . $this->view->menuProfileSystem() . '
                        </ul>
                    </div>
                </div>
            </div>
        </div>';

        return $html;
    }

}