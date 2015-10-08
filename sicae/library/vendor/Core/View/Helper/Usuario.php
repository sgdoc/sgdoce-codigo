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
class Core_View_Helper_Usuario extends Zend_View_Helper_Abstract
{

    public function Usuario()
    {
        return $this;
    }

    public function urlLogout()
    {
        $controller = 'usuario';

        if (\Core_Integration_Sica_User::getUserProfileExternal()) {
            $controller = 'usuario-externo';
        }

        $urlSica = rtrim(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('urlSica'), '/');
        return $urlSica . $this->view->url(array('controller' => $controller, 'action' => 'logout'));
    }

    public function optionUnidadeOrg()
    {
        $noUnidadeOrg = trim(\Core_Integration_Sica_User::getUserUnitName());
        $li = '<li><span>Unidade: ' . $noUnidadeOrg . '</span></li>';

        $maxChars = 28;
        if (strlen($noUnidadeOrg) > $maxChars) {
            $li = '<li><span  data-content="' . $noUnidadeOrg . '"';
            $li .= 'data-placement="left" data-trigger="hover" rel="popover" ';
            $li .= 'data-original-title="Unidade Organizacional"> ';
            $li .= 'Unidade: ' . trim(substr($noUnidadeOrg, 0, $maxChars)) . '&hellip;</span></li>';
        }

        return $li;
    }

    public function optionSelecionarPerfil()
    {
        $sistema = \Core_Integration_Sica_User::getInfoSystem();

        return '<li><a '
                . 'href="javascript:Sistemas.verifica(' . $sistema['sqSistema'] . ');">'
                . 'Selecionar Perfil</a></li>';
    }

    public function modalSelecionarPerfil()
    {
        $urlSica = rtrim(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('urlSica'), '/') . '/';
        $html = '<div class="modal hide" id="modal-usuario-perfil">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">×</button>
                        <h3>Unidade Organizacional / Perfil</h3>
                    </div>
                    <div class="modal-body">
                        <form id="form-user-profile" method="post" name="form-user-profile" class="form-horizontal">
                            <span class="help-block">Existem duas ou mais unidades organizacionais e/ou perfis cadastrados. Selecione a unidade e o perfil desejado.</span>
                            <br/>
                            <input type="hidden" name="systemId" id="systemId" value="" />
                            <div id="search-container" class="control-group hide">
                                <div class="controls">
                                    <div class="input-append">
                                        <input class="span3" type="text" value="" placeholder="Procurar uma unidade" name="search-text"><button id="btn-search" class="btn" type="button">
                                            <i class="icon icon-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label"><span class="required">*</span> Unidade</label>
                                <div class="controls" style="width:250px">
                                    <select name="feijoadaUnit" class="required" id="usersList" style="width: 250px;"></select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label"><span class="required">*</span> Perfil</label>
                                <div class="controls">
                                    <select name="feijoadaProfile" id="feijoadaProfile" class="required" disabled="disabled">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="btn-access" type="button">Acessar <i class="icon icon-white icon-share-alt"></i></button>
                        <a href="#" class="btn" data-dismiss="modal" id="btnCancelar">
                        <i class="icon-remove"></i>
                        Cancelar</a>
                    </div>
                </div>';

        $html .= '<script src="'
                . $urlSica . $this->view->assetUrl('sica/usuario-perfil/usuario-perfil.js')
                . '" type="text/javascript"></script>';

        $html .= '<script src="'
                . $urlSica . $this->view->assetUrl('sica/principal/sistemas.js')
                . '" type="text/javascript"></script>';

        return $html;
    }

    public function modalAlterarSenha()
    {
        $urlSica = rtrim(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('urlSica'), '/') . '/';
        $html = '<div class="modal hide fade" id="modal-alterar-senha">
                    <div class="modal-header">
                        <a class="close" data-dismiss="modal">×</a>
                        <h3>Alterar Minha Senha</h3>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" id="form-altera-senha"
                              name="form-altera-senha" method="post">
                            <fieldset>
                                <div class="control-group error-pass">
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="input01"><span class="required">* </span>Senha Atual</label>
                                    <div class="controls">
                                        <input type="password" name="txSenha"
                                               class="input-xlarge required validatePassword"
                                               maxlength="32" id="txSenha">
                                        <span class="help-block">Digite a senha que você quer trocar</span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="input01"><span class="required">* </span>Nova Senha</label>
                                    <div class="controls">
                                        <input type="password" name="txSenhaNova"
                                               class="input-xlarge required validatePassword" maxlength="32" id="txSenhaNova">
                                        <span class="help-block">Digite sua nova senha</span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="input01"><span class="required">* </span>Confirmação Nova Senha</label>
                                    <div class="controls">
                                        <input type="password" name="txSenhaNovaConfirm"
                                               class="input-xlarge required validatePassword" maxlength="32"
                                               id="txSenhaNovaConfirm">
                                        <span class="help-block">Digite sua nova senha outra vez</span>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnAlteraSenha" class="btn btn-primary">Concluir</button>
                        <button type="button" class="btn" id="btnCancelar" data-dismiss="modal">
                        <i class="icon-remove"></i>
                        Cancelar</button>
                    </div>
                </div>';

        if (\Core_Integration_Sica_User::getUserProfileExternal()) {
            $url = 'sica/usuario-externo/usuario-externo.js';
        } else {
            $url = 'sica/usuario/usuario.js';
        }

        $html .= '<script src="'
                . $urlSica . $this->view->assetUrl($url)
                . '" type="text/javascript"></script>';

        return $html;
    }

}