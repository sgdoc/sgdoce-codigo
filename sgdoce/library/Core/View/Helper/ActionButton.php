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
 * @name       ActionButton
 * @category   View Helper
 * @todo       review
 */
class Core_View_Helper_ActionButton extends Zend_View_Helper_Abstract
{
    /**
     * Cria um botão que ou executa uma ação JS ou envia a uma URL
     * @param string $title título do botão
     * @param string $target callback ou url alvo
     * @param array $params parâmetros identificados (key=>val)
     * @param string $icon nome do ícone a ser apresentado
     * @param string $label texto a ser apresentado no botão
     * @param array $tagAttrs quaisquer outros atributos a serem inseridos na tag
     * @param string $targetType tipo do target (js ou href)
     */
    public function actionButton($title, $target, $params = array(), $icon = null, $label = null, $tagAttrs = array(), $targetType = 'js')
    {
        if ($targetType == 'js') {
            $target  = 'javascript:'.$target.'(\''.implode('\',\'', $params).'\')';
        } else {
            $lastStrPos = strlen($target) - 1;
            if ($target[$lastStrPos] != '/') {
                $target .= '/';
            }

            foreach ($params as $key => $val) {
                $target .= $key . '/' . $val . '/';
            }
        }

        if ($icon) {
            $icon = 'icon-'.$icon;
            $label = '';
        }

        $attrs = '';
        foreach ($tagAttrs as $key=>$val) {
            $attrs .= $key.'="'.$val.'" ';
        }

        return "<a class=\"btn btn-mini\" title=\"$title\" href=\"$target\" $attrs><span class=\"$icon\">$label</span> </a>\n";
    }
}