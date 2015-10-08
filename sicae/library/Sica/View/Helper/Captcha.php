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
 * @name       Captcha
 * @category   View Helper
 */
class Sica_View_Helper_Captcha extends Zend_View_Helper_Abstract
{

    public function captcha()
    {
        $request = new Zend_Controller_Request_Http();
        $cookie = (int) $request->getCookie('sicaeuser');
        if ($cookie >= 3) {
            return '<div class="control-group">
                        <div class="controls captcha-img" style="text-align: center; border: 1px solid; width:285px;">
                            <img id="captcha" src="/usuario/captcha" alt="CAPTCHA Image" />
                        </div>
                        <div class="controls captcha-button">
                            <button title="Atualizar Imagem" class="btn btn-small" type="button" onclick="document.getElementById(\'captcha\').src = \'/usuario/captcha/id/\' + Math.random(); return false;">
                                <i class="icon-refresh"></i>
                            </button>
                            <object title="Ouvir" align="top" width="33" height="26" id="SecurImage_as3" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">
                                <param title="Ouvir" value="sameDomain" name="allowScriptAccess" />
                                <param title="Ouvir" value="false" name="allowFullScreen" />
                                <param title="Ouvir" value="/captcha/swf/securimage_play.swf?audio=/usuario/captcha-audio/&amp;bgColor1=#f4f4f4&amp;bgColor2=#f4f4f4&amp;iconColor=#000&amp;roundedCorner=4&amp;borderWidth=1&amp;borderColor=#d2d2d2" name="movie" />
                                <param title="Ouvir" value="high" name="quality" />
                                <param title="Ouvir" value="#f4f4f4" name="bgcolor" />
                                <embed title="Ouvir" align="" width="33" height="26" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowfullscreen="false" allowscriptaccess="sameDomain" name="SecurImage_as3" bgcolor="#ffffff" quality="high" src="/captcha/swf/securimage_play.swf?audio=/usuario/captcha-audio/&amp;bgColor1=#f4f4f4&amp;bgColor2=#f4f4f4&amp;iconColor=#000&amp;roundedCorner=4&amp;borderWidth=1&amp;borderColor=#d2d2d2" />
                            </object>
                        </div>
                    </div>
                <div class="control-group">
                    <label class="control-label" for=captcha_code>* Código de segurança</label>
                    <div class="controls">
                        <input type="text" id="captcha_code" name="captcha_code" class="input-xlarge required" maxlength="7" />
                    </div>
                </div>
            ';
        }
    }

}
