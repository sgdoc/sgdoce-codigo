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
class Sica_View_Helper_ComboMenu extends Zend_View_Helper_FormElement
{
    /**
     * @var array
     */
    protected $_prefix = 'label-select';

    public function comboMenu($name, $value = NULL, array $attribs = array(), array $options, $valueReference = 'sqMenu', $all = FALSE)
    {
        $html = '<select';
        $html .= $this->_htmlAttribs($attribs) . ' name="'.$name.'" id="'.$name.'">';
        $html .= "<option value=\"\" >{$this->view->translate($this->_prefix)}</option>";
        $selected = '';

        if($all){
            if ($value == '0') {
                $selected = 'selected="selected"';
            }
            $html .= "<option value=\"0\" $selected>Todos</option>";
        }

        foreach ($options as $option) {
            $selected = '';

            if (NULL !== $value && $option[$valueReference] == $value) {
                $selected = 'selected="selected"';
            }

            $label = $this->view->escape($option['noMenu']);
            $label = str_repeat('&nbsp;', $option['nuNivel'] * ($option['nuNivel'] - 1)) . $label;
            $html .= "<option value=\"{$option[$valueReference]}\" $selected>{$label}</option>";
        }

        $html .= '</select>';

        return $html;
    }
}
