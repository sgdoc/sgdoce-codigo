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
class Core_View_Helper_UsuarioExterno extends Zend_View_Helper_Abstract
{

    public function UsuarioExterno()
    {
        return $this;
    }

    public function optionAlterarCadastro($home = FALSE)
    {
        $urlSica = rtrim(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('urlSica'), '/');
        $sqUsuario = \Core_Integration_Sica_User::getUserId();

        $urlAlter = $urlSica . '/principal/usuario-externo/edit/id/' . $sqUsuario;

        if ($home) {
            $urlAlter = $urlSica . '/principal/usuario-externo/edit/id/' . $sqUsuario . '/page/home';
        }

        $html = '<li><a href="' . $urlAlter . '">Alterar Cadastro</a></li>';

        return $html;
    }

    public function optionSelecionarPerfil()
    {
        $html = '<li><a data-toggle="modal" data-keyboard="false" data-backdrop="static" href="#modal-usuario-perfil">Selecionar Perfil</a></li>';

        return $html;
    }

    public function modalSelecionarPerfil()
    {
        $urlSica = rtrim(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('urlSica'), '/') . '/';

        $html = '<div class="modal hide" id="modal-usuario-perfil">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">×</button>
                        <h3>Perfil</h3>
                    </div>
                    <form id="form-user-profile"
                          method="post"
                          name="form-user-profile"
                          action="' . $this->view->urlCurrent(array('controller' => 'usuario-perfil', 'action' => 'user-profile')) . '"
                          class="form-horizontal">
                        <div class="modal-body">
                            <fieldset>
                                <input type="hidden" name="systemId" id="systemId" value="' . \Core_Integration_Sica_User::getUserSystem() . '" />
                                <div class="control-group">
                                    <label class="control-label"><span class="required">*</span> Perfil</label>
                                    <div class="controls">
                                        <select name="sqPerfilUsuarioExterno" class="required" id="sqPerfilUsuarioExterno">';
        $html .= "<option value=\"\">Selecione uma opção</option>";
        foreach ((array) \Core_Integration_Sica_User::getUserAllProfile() as $perfil) {
            $html .= "<option value=\"{$perfil['sqPerfil']}\">";
            $html .= $perfil['noPerfil'] . '</option>';
        }

        $html .='</select>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary">Concluir</button>
                            <a href="#" class="btn" data-dismiss="modal">
                            <i class="icon-remove"></i>
                            Cancelar</a>
                        </div>
                    </form>
                </div>';

        $html .= '<script src="'
                . $urlSica . $this->view->assetUrl('sica/usuario-externo/sistemas.js')
                . '" type="text/javascript"></script>';

        return $html;
    }

}