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
 * @name       MenuTableTree
 * @category   View Helper
 */
class Sica_View_Helper_MenuTableTree extends Core_View_Helper_Abstract
{
    /**
     * Monta a tabela para a listadem de menu em forma de "árvore"
     * @param $grid array
     * @return string
     */
    public function menuTableTree($grid, $ordenar, $message)
    {
        $table   = '';
        $grids   = $this->_buildRecursiveGrid($grid);
        $qtdGrid = count($grids);

        if ($qtdGrid) {
            foreach ($grids as $key => $grid) {
                $dataGrid   = $grid['itens'][$key];
                $countFilho = count($grid['sub-itens']);
                $table .= $this->_buildTr($dataGrid, $ordenar, $key, $qtdGrid, $countFilho);

                if ($countFilho) {
                    $table .= $this->_buildTrFilho($grid['sub-itens'], $ordenar);
                }
            }
        } else {
            $table .= '<tr class="gradeX odd">';
            $table .= '<td class="mainMenu" colspan="4">'.$message.'</td>';
            $table .= '</tr>';
        }
        return $table;
    }

    /**
     * (non-PHPdoc)
     */
    protected function _buildTr($dataGrid, $ordenar, $key, $qtdGrid, $qtdFilho)
    {
        $qtdFuncionalidade = (int) $dataGrid['nuQuantidadeFuncionalidade'];
        $status      = ($dataGrid['stRegistroAtivo']) ? 'iconAtivarDesativar icon-inativado'
                                                      : 'iconAtivarDesativar icon-ativado';
        $hint        = ($dataGrid['stRegistroAtivo']) ? 'Inativar' : 'Reativar';
        $labelStatus = ($dataGrid['stRegistroAtivo']) ? 'Ativo'    : 'Inativo';

        $label       = $this->view->escape($dataGrid['noMenu']);
        $label       = ($dataGrid['nuNivel'] > 1) ? '<i class="icon-subnivel subnivel"></i>' . $label : $label;
        $label       = str_repeat('&nbsp;', $dataGrid['nuNivel'] * ($dataGrid['nuNivel'] - 1)) . $label;
        $label      .= '<input type="hidden" class="grid-filho" value="'.$qtdFilho.'" />';

        $ordenacao   = ($ordenar == 'true') ? '<td>'.$this->_ordenacao($dataGrid, $key, $qtdGrid).'</td>' : null;

        $tr = '<tr class="gradeX odd">';
        $tr.= '<td class="mainMenu">' . $label . '</td>';
        $tr.= $ordenacao;
        $tr.= '<td>';
        $tr.= $labelStatus;
        $tr.= '</td>';
        $tr.= '<td>';
        $tr.= '<div class="btn-group">';
        #btn "Alterar"
        $tr.= '<a title="Alterar"
                  href="'.$this->view->url(array('action' => 'edit', 'id' => $dataGrid['sqMenu'])).'"
                  class="btn btn-mini editMenu"><i class="icon-pencil"></i></a>';
        #btn "Inativar/Ativar"

        if ($this->_checkAcl(array('action'=>'switch-status'))) {
            $tr.= '<button type="button" title='.$hint.' id="'.$dataGrid['sqMenu'].'"
                           status="'.(int)$dataGrid['stRegistroAtivo'].'"
                           class="btn btn-mini status"><i class="'.$status.'"></i></button>';
        }

        if ($qtdFuncionalidade + $qtdFilho === 0) {
            #btn "Remover"
            $tr.= '<button type="button" title="Remover"
                           data-url="'.$this->view->url(array('action' => 'delete', 'id' => $dataGrid['sqMenu'])).'"
                           class="btn btn-mini deleteMenu"><i class="icon-trash"></i></button>';
        }
        $tr.= '<input type="hidden" class="grid-filho" value="'.$qtdFilho.'" />';
        $tr.= '</div>';
        $tr.= '</td>';
        $tr.= '</tr>';

        return $tr;
    }

    /**
     * (non-PHPdoc)
     */
    protected function _buildTrFilho($grids, $ordenar, $sqMenuPai = NULL)
    {
        $dataGrids  = $grids['itens'];
        $subGrids   = $grids['sub-itens'];
        $countFilho = count($subGrids);
        $tr         = array();
        $qtdGrid    = 0;
        $key        = 1;

        foreach ($dataGrids as $dataGrid) {
            if (NULL === $sqMenuPai || $sqMenuPai == $dataGrid['sqMenuPai']) {
                $qtdGrid++;
            }
        }

        foreach ($dataGrids as $dataGrid) {
            $arTr = array();
            if (NULL === $sqMenuPai || $sqMenuPai == $dataGrid['sqMenuPai']) {
                if ($countFilho) {
                    $arTr = $this->_buildTrFilho($subGrids, $ordenar, $dataGrid['sqMenu']);
                }

                $tr[$dataGrid['sqMenu']] = $this->_buildTr($dataGrid, $ordenar, $key, $qtdGrid, count($arTr));

                if (count($arTr)) {
                    foreach ($arTr as $k => $value) {
                        $tr[$k] = $value;
                    }
                }

                $key++;
            }
        }
        return $sqMenuPai ? $tr : implode('', $tr);
    }

    /**
     * (non-PHPdoc)
     */
    protected function _ordenacao($grid, $key, $total)
    {
        $retorno   = '<div class="btn-group">';
        $nextKey   = $key + 1;
        $beforeKey = $key - 1;

        if ($nextKey <= $total && $total > 1) {
            $retorno .= '<button type="button" title="Descer" nivel="' . $grid['nuNivel'] . '" class="btn btn-mini btnDown"
                            menu="'.$grid['sqMenu'].'"><i class="icon-chevron-down"></i></button>';
        }

        if ($beforeKey > 0) {
            $retorno .= '<button type="button" title="Subir" nivel="' . $grid['nuNivel'] . '" class="btn btn-mini btnUp"
                            menu="'.$grid['sqMenu'].'"><i class="icon-chevron-up"></i></button>';
        }

        return $retorno . "</div>";
    }

    /**
     * (non-PHPdoc)
     */
    private function _buildRecursiveGrid($grids)
    {
        $menus = array();
        foreach ($grids as $grid) {
            $ordenacao = $grid["ordenacao"];
            $menus[$ordenacao[0]]  = $grid['nuNivel'] == 1 ? array('itens'     => array($ordenacao[0] => $grid),
                                                                   'sub-itens' => array())
                                                           : array_merge_recursive($menus[$ordenacao[0]],
                                                                                   $this->_recursiveArray($grid));
        }

        return $menus;
    }

    /**
     * (non-PHPdoc)
     */
    protected function _recursiveArray($valueMenu)
    {
        $i = 0;
        $ar_menu = array();
        $arrayIndex = array_reverse(range(1, $valueMenu['nuNivel']));
        foreach ($arrayIndex as $key => $value) {
            $ar_menu = ($i == 0) ? array('itens'     => array($value => $valueMenu),
                             'sub-itens' => array())
                     : array('itens'     => array(),
                             'sub-itens' => $ar_menu);
            $i++;
        }
        return $ar_menu;
    }
}