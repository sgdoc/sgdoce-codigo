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
 * @name       MenuTree
 * @category   View Helper
 */
class Sica_View_Helper_MenuTree extends Core_View_Helper_Abstract
{
    public function menuTree($menu, $full = 'false')
    {
        $html = '';
        $pai = null;
        $pai2 = null;

        if ($full === 'false') {
            $html .= '<tr>
                            <td><input type="radio" value="0" name="sqMenuPai"></td>
                            <td>Menu Raiz</td>
                        </tr>';
        }

        foreach($menu as $key => $value){
            if ($pai != $value['m_sqMenu']) {
               $pai = $value['m_sqMenu'];
               $html .= '<tr>
                            <td><input class="nivel-menu" id="'.$value['m_sqMenu'].'" value="'.$value['m_sqMenu'].'" type="radio" name="sqMenuPai"></td>
                            <td>'.$value['m_noMenu'].'</td>
                        </tr>';
            }

            if ($value['m_sqMenu'] == $pai && $value['m2_sqMenu'] !== null) {
                $pai2 = $value['m2_sqMenu'];
                $html .= '<tr>
                            <td><input class="nivel-menu" id="'.$value['m2_sqMenu'].'" value="'.$value['m2_sqMenu'].'" type="radio" name="sqMenuPai"></td>
                            <td><i class="icon-subnivel subnivel"></i>'.$value['m2_noMenu'].'</td>
                         </tr>';
            }

            if($full === 'true'){
                if($value['m2_sqMenu'] == $pai2 && $value['m3_sqMenu'] !== null){
                    $html .= '<tr>
                                <td><input class="nivel-menu" id="'.$value['m3_sqMenu'].'" value="'.$value['m3_sqMenu'].'" type="radio" name="sqMenuPai"></td>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-subnivel subnivel"></i>'.$value['m3_noMenu'].'</td>
                             </tr>';
                }
            }

        }
        return $html;
    }
}
