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
 * @name       AlterarSenha
 * @category   View Helper
 */
class Core_View_Helper_AlterarSenha extends Zend_View_Helper_Url
{
    public function alterarSenha()
    {
        $urlSistema = rtrim(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('urlSica'),'/');
        $html = '<div class="modal hide" id="alterar">
                    <div class="modal-header">
                        <a class="close" data-dismiss="modal">×</a>
                        <h3>Alterar Minha Senha</h3>
                    </div>
                    <div class="modal-body">
                        <iframe
                        width="100%"
                        height="460px"
                        scrolling="no"
                        marginheight="0"
                        marginwidth="0"
                        style="background-color: transparent" frameborder="0"
                        src="'.$urlSistema.'/usuario/change-pass-iframe">
                                        </iframe>
                    </div>
                </div>';

        return $html;
    }

}