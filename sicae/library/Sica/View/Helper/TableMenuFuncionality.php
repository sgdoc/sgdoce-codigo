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
 * @name       TableMenuFuncionality
 * @category   View Helper
 */
class Sica_View_Helper_TableMenuFuncionality extends Zend_View_Helper_Abstract
{
    public function tableMenuFuncionality(array $data = array())
    {
        $html = '';
        $noMenu = '';
        foreach ($data as $key => $func) {
            if ($noMenu != $func['noMenu']) {
                $noMenu = $func['noMenu'];

                $html .= '
                    <tr class="gradeX odd">
                        <td></td>
                        <td>
                            ' . $func['noMenu'] . '
                        </td>
                    </tr>';
            }

            $html .= '
            <tr class="gradeX odd">
                <td>
                </td>
                <td>
                    <label class="checkbox inline">
                        <input style="margin-left:15px;" type="checkbox" class="checkbox inline" name="sqFuncionalidade[]" value="' . $func['sqFuncionalidade'] . '"' . (isset($func['checked']) && $func['checked'] == "checked" ? ' checked="checked"' : '') . ' />
                        ' . $func['noFuncionalidade'] . '
                    </label>
                </td>
            </tr>';
        }

        return $html;
    }
}