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
class Core_View_Helper_MenuProfileSystem extends Zend_View_Helper_Abstract
{

    public function MenuProfileSystem()
    {
        $urlSica    = rtrim(\Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('urlSica'), '/');
        $infoSystem = \Core_Integration_Sica_User::getInfoSystem();
        $urlHelp    = array_key_exists('txUrlHelp', (array) $infoSystem) ? $infoSystem['txUrlHelp'] : '#';

        $html = '<ul class="nav pull-right dropdown-perfil">
                <li class="divider-vertical visible-desktop"></li>
                <li class="dropdown pull-right">
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <img src="/img/icons/icon-perfil.png">
                </a>
                <ul class="dropdown-menu">';

        $html .= '<li><span>Nome: ' . $this->view->userName() . '</span></li>';
        $html .= '<li><span>Perfil: ' . \Core_Integration_Sica_User::getUserNoProfile() . '</span></li>';

        if (\Core_Integration_Sica_User::getUserProfileExternal()) {
            $linkHelp = '';
            $html .= '<li class="divider"></li>';

            if (count(\Core_Integration_Sica_User::getUserAllProfile()) > 1) {
                $html .= $this->view->usuarioExterno()->optionSelecionarPerfil();
            }

            $html .= $this->view->usuarioExterno()->optionAlterarCadastro();
        } else {
            $linkHelp = '<li><a href="' . $urlHelp . '" target="_blank">Ajuda</a></li>';
            $html .= $this->view->usuario()->optionUnidadeOrg();
            $html .= '<li class="divider"></li>';

            if (count(\Core_Integration_Sica_User::getUserAllProfile()) > 1) {
                $html .= $this->view->usuario()->optionSelecionarPerfil();
            }
        }

        $html .= '<li><a href="' . $urlSica . '/index/home/change/password">Alterar Senha</a></li>';

        $html .= $linkHelp;
        $html .= '<li><a href="' .$urlSica . '/usuario/logout">Sair</a></li>';
        $html .= '</ul></li><li class="divider-vertical visible-desktop"></li>';

        return $html;
    }

}

