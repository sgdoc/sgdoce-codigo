<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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
namespace br\gov\sial\core\output\screen\component\html\grid;
use br\gov\sial\core\output\screen\html\Abbr,
    br\gov\sial\core\output\screen\html\Label,
    br\gov\sial\core\output\screen\html\TableHeaderCell,
    br\gov\sial\core\output\screen\component\GridAbstract,
    br\gov\sial\core\output\screen\component\GridColumnAbstract;

/**
 * SIAL
 *
 * Componente
 *
 * @package br.gov.sial.core.output.screen.component.html
 * @subpackage grid
 * @name GridColumn
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class GridColumn extends GridColumnAbstract
{
    /**
     * @var string
     * */
    const T_GRIDCOLUMN_STR_COLUMN_ACTION = 1;

    /**
     * @var GridAbstract
     * */
    private $_grid;

    /**
     * @var int
     * */
    private $_attr;

    /**
     * @var string
     * */
    private $_customConfig;

    /**
     * @param GridAbstract $grig
     * @param string[] $config
     * */
    public function __construct (GridAbstract $grid, array $config)
    {
        parent::__construct($config);
        $this->_element = new TableHeaderCell;
        $this->_grid = $grid->name();
        $this->_attr = $this->safeToggle($config, 'attr', 0);
        $this->_customConfig = $this->safeToggle($config, 'customConfig', array());
    }

    /**
     * @return GridColumn
     * */
    public function build ()
    {
        /* sempre havera um label, mesmo que vazio */
        $label = new Label($this->_label ?: '');

        $this->_element->addClass('header')
                       ->attr('role', 'columnheader')
                       ->attr('order', 'asc')
                       ->attr('aria-controls', $this->_grid);

        if (self::T_GRIDCOLUMN_STR_COLUMN_ACTION == $this->_attr) {
            $this->_element->addClass('grid-column-action');
        }

        if ($this->_sorter) {
            $this->_element->addClass(array('grid_sorter', $this->_dindex));
        }

        if ($this->_legend) {
            $this->_element->attr('aria-label', $this->_legend);
        }

        foreach ($this->_customConfig as $attr => $value) {
            $this->_element->attr($attr, $value);
        }

        $this->_element->add($label);

        return $this;
    }

    /**
     * @return string
     * */
    public function render ()
    {
        return $this->_element->render();
    }
}