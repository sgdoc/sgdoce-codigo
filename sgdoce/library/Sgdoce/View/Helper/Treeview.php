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
 * @package    Sgdoce
 * @subpackage View
 * @subpackage Helper
 * @name       Treeview
 * @category   View Helper
 */
class Sgdoce_View_Helper_Treeview extends Zend_View_Helper_Abstract
{
    /**
     * @param  array  $data
     * @param  int    $selected
     * @param  array  $config
     * @return string
     */
    public function treeview (array $data, $selected, array $config)
    {
        $actions = <<<HTML
<div class="btn-group dropup">
    <button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">
        <i class="icon-cog"></i>
    </button>
    <ul class="dropdown-menu">
        <li>
            <a href="#" title="Recolhe todos os itens">
                <i class="icon-minus"></i>
                Recolher todos
            </a>
        </li>
        <li>
            <a href="#" title="Expande todos os itens">
                <i class="icon-plus"></i>
                Expandir todos
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="#" title="Abre os fechados e fecha os abertos">
                <i class="icon-refresh"></i>
                Inverter abertos e fechados
            </a>
        </li>
    </ul>
</div>
HTML;
        $html = '<div class="treeheader"></div>';
        $html .= '<div id="sidetreecontrol">';
        $html .= $config['hasActions']? $actions : '';
        $html .= '</div>';
        $html .= $this->_tree($config, $data, $selected);
        return $html;
    }

    /**
     * @param  array   $config
     * @param  array   $data
     * @param  int     $selected
     * @param  boolean $isRoot
     * @return string
     */
    private function _tree (array $config, array $data, $selected, $isRoot = true)
    {
        $container = '';
        $lines = '';

        foreach (array_filter((array) $data) as $key => $value) {

            $itemClass = sprintf(
                'treeviewItem treeviewItem-%s%s %s', 
                $key,
                isset($config['classKey'])? " {$value[$config['classKey']]}" : '',
                $key == $selected ? 'active selected' : ''
            );

            $lines .= sprintf('<li class="%s">', ($isRoot ? ' parent' : ' child'));
            $lines .= $key == $selected ? '<em><strong>' : '';

            if (isset($config['iconType']) && $value[$config['iconType']] == \Core_Configuration::getSgdoceTipoVinculoArtefatoApensacao()) {
                $lines .= '<i class="icon-apenso" title="Apenso"></i>';
            }
            if (isset($config['iconType']) && $value[$config['iconType']] == \Core_Configuration::getSgdoceTipoVinculoArtefatoAnexacao()) {
                $lines .= '<i class="icon-anexo" title="Anexo"></i>';
            }
                
            if ($config['hasLink']) {
                $lines .= sprintf(
                    '<a class="%s" href="%s">%s</a>',
                    $itemClass,
                    str_replace('id',$key,$config['linkHref']),
                    $value[$config['displayKey']]
                );
            } else {
                $lines .= sprintf(
                    '<output class="%s">%s</output>',
                    $itemClass,
                    $value[$config['displayKey']]
                );
            }

            $lines .= $key == $selected ? '</strong></em>' : '';
            $lines .=  $this->_tree($config, $value[$config['childrenKey']], $selected, false);
            $lines .=  '</li>';
        }

        if ($lines) {
            $container .= sprintf(
                '<ul%s>%s</ul>',
                $isRoot ? sprintf(' id="%s"', $config['id']) : '',
                $lines
            );
        }

        return $container;
    }
}