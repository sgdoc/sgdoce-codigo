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
class Core_View_Helper_SystemMenu extends Zend_View_Helper_Abstract
{

    public function systemMenu()
    {
        $menu = Core_Integration_Sica_User::getUserSystemMenu();

        $html = '<div id="nestedAccordion">';
        $html .= '<h2><strong>Menu</strong></h2>';
        $html .= '<ul id="acc1" class="menu">';

        $sqMenuFilho = 0;
        $sqMenuNeto = 0;

        /*descomentar os trechos de $printDivider para printar divisões entre os 1º niveis*/
        $printDivider = false;
        foreach ($menu as $menuCadastro) {
            if ($menuCadastro['Acao']) {
                if($printDivider){
                    $html .= '<li class="divider"></li>';
                }
                $html .= '<li>';
                $html .= '<a class="trigger" href="/' . ltrim($menuCadastro['Acao'], '/') . '">';
                $html .= $menuCadastro['MenuPai']['noMenu'];
                $html .= '</a>';
                $html .= '</li>';
                $html .= '<li class="divider"></li>';
                $printDivider = false;
            } else {

                if($printDivider){
                    $html .= '<li class="divider"></li>';
                }
                $printDivider = true;
                $html .= '<li>';

                /*ADICIONANDO LINK COM class trigger para representar o icone sem ação*/
                $html .= '<a class="trigger" href="javascript:void(0);">';
                $html .= $menuCadastro['MenuPai']['noMenu'];
                $html .= '</a>';

                foreach ($menuCadastro['MenuFilho'] as $menuFilho) {
                    if ($menuFilho['MenuFilho']['sqMenu'] != $sqMenuFilho) {
                        if ($menuFilho['Acao']) {
                            $html .= '<ul>';
                            $html .= '<li>';
                            $html .= '<a href="/' . ltrim($menuFilho['Acao'], '/') . '">';
                            $html .= $menuFilho['MenuFilho']['noMenu'];
                            $html .= '</a>';
                            $html .= '</li>';
                            $html .= '</ul>';
                        } else {

                            $html .= '<ul>';
                            $html .= '<li>';
                            $html .= '<a class="trigger" href="javascript:void(0);">';
                            $html .= $menuFilho['MenuFilho']['noMenu'];
                            $html .= '</a>';

                            foreach ($menuCadastro['MenuNeto'] as $menuNeto) {
                                if ($sqMenuNeto != $menuNeto['MenuNeto']['sqMenu']) {
                                    if ($menuNeto['Acao']){
                                        $html .= '<ul>';
                                        $html .= '<li>';
                                        $html .= '<a href="/' . ltrim($menuNeto['Acao'], '/') . '">';
                                        $html .= $menuNeto['MenuNeto']['noMenu'];
                                        $html .= '</a>';
                                        $html .= '</li>';
                                        $html .= '</ul>';
                                    } else {
                                        $html .= '<ul>';
                                        $html .= '<li>';
                                        $html .= $menuNeto['MenuNeto']['noMenu'];
                                        if (!empty($menuCadastro['MenuBisNeto'])){
                                            foreach ($menuCadastro['MenuBisNeto'] as $menuBisNeto){
                                                if ($menuBisNeto != $menuBisNeto['MenuBisNeto']['sqMenu']){
                                                    $html .= '<ul>';
                                                    $html .= '<li>';
                                                    $html .= '<a href="/' . ltrim($menuBisNeto['Acao'], '/') . '">';
                                                    $html .= $menuBisNeto['MenuBisNeto']['noMenu'];
                                                    $html .= '</a>';
                                                    $html .= '</li>';
                                                    $html .= '</ul>';
                                                }
                                            }

                                        }
                                        $html .= '</li>';
                                        $html .= '</ul>';
                                    }

                                    $sqMenuNeto = $menuNeto['MenuNeto']['sqMenu'];
                                }
                            }

                            $html .= '</li>';
                            $html .= '</ul>';
                        }

                        $sqMenuFilho = $menuFilho['MenuFilho']['sqMenu'];
                    }
                }

                $html .= '</li>';
            }
        }

        $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }

}

