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
class Core_View_Helper_MenuProfileHome extends Zend_View_Helper_Abstract
{

    public function MenuProfileHome()
    {
        $html = '<ul class="nav pull-right dropdown-perfil">
                <li class="divider-vertical visible-desktop"></li>
                <li class="dropdown pull-right">
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <img src="/assets/img/icons/icon-perfil.png">
                </a>
                <ul class="dropdown-menu">';

        $html .= '<li><span>Nome: ' . $this->view->userName() . '</span></li>';
        $html .= '<li class="divider"></li>';

        if (\Core_Integration_Sica_User::getUserProfileExternal()) {
            $html .= $this->view->usuarioExterno()->optionAlterarCadastro(TRUE);
        }

        $html .= '<li><a data-toggle="modal" data-backdrop="static" data-keyboard="false" '
                . 'href="#modal-alterar-senha" id="btn-alterar-senha">Alterar Senha</a>'
                . '</li>';

        $html .= '<li><a href="' . $this->view->usuario()->urlLogout() . '">Sair</a></li>';
        $html .= '</ul></li><li class="divider-vertical visible-desktop"></li></ul>';

        return $html;
    }

}

