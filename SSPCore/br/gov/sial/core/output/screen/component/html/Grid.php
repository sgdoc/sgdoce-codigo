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
namespace br\gov\sial\core\output\screen\component\html;
use br\gov\sial\core\output\screen\html\Div,
    br\gov\sial\core\output\screen\html\Span,
    br\gov\sial\core\output\screen\html\Text,
    br\gov\sial\core\output\screen\html\Table,
    br\gov\sial\core\output\screen\html\Input,
    br\gov\sial\core\output\screen\html\Label,
    br\gov\sial\core\output\screen\html\Style,
    br\gov\sial\core\output\screen\html\Anchor,
    br\gov\sial\core\output\screen\html\Button,
    br\gov\sial\core\output\screen\html\Select,
    br\gov\sial\core\output\screen\html\TableRow,
    br\gov\sial\core\output\screen\html\TableBody,
    br\gov\sial\core\output\screen\html\TableData,
    br\gov\sial\core\output\screen\html\TableHead,
    br\gov\sial\core\output\screen\ElementAbstract,
    br\gov\sial\core\output\screen\html\Javascript,
    br\gov\sial\core\output\screen\html\TableFooter,
    br\gov\sial\core\output\screen\component\GridAbstract,
    br\gov\sial\core\output\screen\component\html\grid\GridColumn,
    br\gov\sial\core\output\screen\component\GridDataSourceAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * @name Grid
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Grid extends GridAbstract
{
    /**
     * @var boolean
     * */
    protected $_hasPagination = TRUE;
    
    /**
     * @var boolean
     * */
    protected $_disablePagination = FALSE;

    /**
     * @var boolean
     * */
    protected $_loadDataOnReady = TRUE;

    /**
     * @param string $name
     * @param $columns
     * @param GridDataSourceAbstract $dataSource
     * */
    public function __construct ($name, $columns, GridDataSourceAbstract $dataSource, $extraParam = NULL, $cdn = NULL)
    {
        $this->_name         = trim($name);
        $this->_columns      = $columns;
        $this->_dataSource   = $dataSource;

        $this->_grid = new Div;
        $this->_grid->attr('role', 'grid')
                    ->attr('id', 'table-' . $name . '_wrapper')
                    ->addClass(array('dataTables_wrapper', 'form-inline', 'table-hover', 'grid'))
                    ;

        $this->_gridRowSet = new Table;
        $this->_gridRowSet->attr('id', 'table-' . $name)
                    ->attr('style', '')
                    ->attr('aria-describedby', 'table-' . $name . '_info');

        $this->_gridRowSet->addClass(array('table', 'table-striped', 'table-bordered', 'dataTable'));

        # agenda gatilho de inicializacao
        # @todo criar forma de passar param adicional
        # @todo criar metodo especifico para este fim, igual o usado para column
        $this->_grid->add(new Text(sprintf('<script>$(document).ready(function () {$("#%1$s").SAFGrid({gridID: "%1$s"}, %2$s, %3$s);});</script>', $this->_grid->id, json_encode($extraParam), json_encode($cdn))));

        # registra informacoes sobre as colunas manipuladas
        $this->registerColumnInfo($columns);
    }

    /**
     * define odominio, cdn, onde o JS da grid estará armazenado
     *
     * <strong>NOTA</strong>: A estrutura de pastas será sempre __DOMAIN__/component/js/grid.js<br />
     * Onde: __DOMAIN__ representa o dominio informando a este metodo.
     *
     * @param string $domain
     * @return Grid
     * */
    public function setUrlJS ($domain)
    {
        $this->setUrlCSS($domain);

        $domain = ('/' == substr($domain, -1) ? $domain : $domain . '/') . 'component/js/SAFGrid.js';
        $this->_grid->add(Javascript::factory($domain));
        return $this;
    }

    /**
     * define odominio, cdn, onde o CSS da grid estará armazenado
     *
     * <strong>NOTA</strong>: A estrutura de pastas será sempre __DOMAIN__/component/css/grid.css<br />
     * Onde: __DOMAIN__ representa o dominio informando a este metodo.
     *
     * @param string $domain
     * @return Grid
     * */
    public function setUrlCSS ($domain)
    {
        $domain = ('/' == substr($domain, -1) ? $domain : $domain . '/') . 'component/css/SAFGrid.css';
        $this->_grid->add(Style::factory($domain));
        return $this;
    }

    /**
     * define que quando a grid for completamente carregada, no lado do cliente,
     * a mesma ira buscar os dados via httprequest baseando-se nos valores
     * definidos por meio do metodo self::registerHttpInfo
     *
     * @return Grid
     * */
    public function loadDataOnReady ($status = TRUE)
    {
        $this->_loadDataOnReady = (boolean) $status;
        return $this;
    }

    /**
     * registra dados para recuperacao dos dados remotamente
     *
     * @param json_string $info
     * @return Grid
     * */
    public function registerHttpInfo ($info)
    {
        $this->_grid->add(Input::factory('grid-http-info', 'hidden', str_replace('"', "'", $info)));
        return $this;
    }

    /**
     * registra dados das colunas que serao manipuladas
     *
     * @param string[] $columns
     * @return Grid
     * */
    public function registerColumnInfo ($columns)
    {
      $cInfo['columns']      = $columns;
      $cInfo['hasCountLine'] = $this->_hasCountLine;
      $this->_grid->add(Input::factory('grid-column-info', 'hidden', str_replace('"', "'", json_encode($cInfo))));
    }

    /**
     * @return Grid
     * */
    public function attachPagination ()
    {
        $this->_hasPagination = TRUE;
        return $this;
    }

    /**
     * @return Grid
     * */
    public function detachPagination ()
    {
        $this->_hasPagination = FALSE;
        return $this;
    }

    /**
     * @return GridAbstract
     * */
    public function build ()
    {
        $this->_grid->add($this->_hasPagination ? array( $this->selectorCount(),  $this->_gridRowSet, $this->navbar()) : $this->_gridRowSet);
        $this->header();
        $this->body();
        return $this;
    }
    
    /**
     * @return Grid
     * */
    public function disablePagination()
    {
        $this->_disablePagination = TRUE;
        return $this;
    }

    /**
     * @return Div
     * */
    public function navbar ()
    {
        $div      = Div::factory()->addClass(array('row-fluid', 'hide'));
        if (!$this->_disablePagination) {
            $frstDiv6 = Div::factory()->addClass('span6');
            $scndDiv6 = clone $frstDiv6;
            $div->add(array($frstDiv6, $scndDiv6));
    
            $divInfo = Div::factory()
                          ->attr('id', $this->_name . '_info')
                          ->addClass('dataTables_info')
                          ->add(new Text('_x até _y de _z registros'));
    
            $frstDiv6->add($divInfo);
    
            $divPaginate = Div::factory()->addClass(array('dataTables_paginate', 'paging_bootstrap', 'pagination', 'pagination-right'));
            $scndDiv6->add($divPaginate);
    
            # controle de navegacao, indica a pagina atual em exibicao
            $div->add(Input::factory('currentpage', $type = 'hidden', 1));
        }
        # informa que a grid devera efetuar carga inicial logo apos o carregamento do documento
        $div->add(Input::factory($this->_grid->id . 'LoadDataOnReady', $type = 'hidden', $this->_loadDataOnReady));

        return $div;
    }

    /**
     * criar select que possibilita definir a quantidade registros por pagina
     *
     * @return Div
     * */
    public function selectorCount ()
    {
        if ($this->_disablePagination) {
            return Div::factory()->addClass(array('row-fluid', 'grid-select-length-bar'));
        }
        
        $select = new Select($this->_name . '_length', $this->_selectorCount);
        $select->addClass('span2');

        $divLength = Div::factory()
                ->attr('id', $this->_name . '_length')
                ->addClass(array('dataTables_length', 'span6'))
                ->add(array(new Label('Registros por página&nbsp;'), $select));

        $loadingText = 'Carregando registros' .
        '<span class="threeLittleDots">.</span>' .
        '<span class="threeLittleDots">.</span>' .
        '<span class="threeLittleDots">.</span>';
        $divLoader = Div::factory()
                     ->attr('id', $this->_name . '_processing')
                     ->addClass(array('dataTables_processing', 'span6'))
                     ->setContent(new Label($loadingText));

        return Div::factory()
                  ->add(array($divLoader, $divLength))
                  ->addClass(array('row-fluid', 'grid-select-length-bar'));
    }

    /**
     * @return Grid
     * */
    public function header ()
    {
        $this->_gridRowSet->thead = new TableHead;
        $headerRow = new TableRow;
        $headerRow->attr('role', 'row');

        if ($this->_hasCountLine) {
            array_unshift($this->_columns, array('label' => '#', 'dindex' => '__GRID_COLUMN_COUNTLINE__'));
        }

        if ($this->_canEdit || $this->_canDetail || $this->_canDelete || $this->_canChangeStatus) {
            $this->_columns[] = array(
              'label'  => 'Ações',
              'dindex' => '__GRID_COLUMN_HANDLE__',
              'attr'   => GridColumn::T_GRIDCOLUMN_STR_COLUMN_ACTION
            );
        }

        # repassa informacao para o js sobre a coluna de acao
        $this->_gridRowSet->thead->add(Input::factory('rowPerm', 'hidden', $this->controllButtonJSon()));

        foreach ($this->_columns as $column) {

            if (isset($column['hide']) && TRUE === $column['hide']) {
                continue;
            }

            $tmpSort = $this->safeToggle($column, 'sorter');

            $column  = new GridColumn($this, $column);
            $column->setSorter($tmpSort);
            $headerRow->add($column->build());
        }

        $this->_gridRowSet->thead->add($headerRow);

        return $this;
    }

    /**
     * @return Grid
     * */
    public function body ()
    {
        $count = 1;
        $this->_gridRowSet->tbody = new TableBody;


        # content
        foreach ($this->_dataSource as $register) {

            # grid's row
            $row    = new TableRow;
            $rowKey = NULL;
            $this->_gridRowSet->tbody->add($row);

            # column
            foreach ($this->_columns as $col) {

                $content = NULL;

                if ('__GRID_COLUMN_COUNTLINE__' == $col['dindex']) {
                    $content = $count++;
                } elseif ('__GRID_COLUMN_HANDLE__' == $col['dindex']) {
                    $content = $this->controllButton($col);
                    $content->attr('id', isset($register['rowKey']) ? $register['rowKey'] : 'rnd' . rand(1, PHP_INT_MAX));
                } elseif('rowKey' == $col['dindex']) {
                    continue;
                } else {
                    $content = $register[$col['dindex']];
                }

                # callback server
                if (isset($col['callback'])) {
                    $cback = $col['callback'];
                    $cback($content);
                }

                $row->add(new TableData($content));
            }
        }

        return $this;
    }

    /**
     * @return string[]
     * */
    public function rowKey ()
    {

      # row key
      $rowKey = array();

      # procura pela rowKey
      foreach ($this->_columns as $column) {

        if (isset($column['rowKey']) && $column['rowKey']) {
          $rowKey[] = $column['dindex'];
        }
      }

      return $rowKey;
    }

    /**
     * retorna representacao em string das permissoes do usuario
     *
     * @return string
     * */
    public function controllButtonJSon ()
    {
        $permission = array();
        $permission['rowKey'] = $this->rowKey();

        foreach ($this->permission() as $perm) {
            $permission['allow'][] = $perm;
        }

        return str_replace('"', "'", json_encode($permission));
    }

    /**
     * retorna as permissoes disponiveis
     *
     * @return string
     * */
    public function permission ()
    {
        $control = array();

        if ($this->_canEdit) {
            $control[] = 'edit';
        }

        if ($this->_canDetail) {
            $control[] = 'detail';
        }

        if ($this->_canChangeStatus) {
            $control[] = 'status';
        }

        if ($this->_canDelete) {
            $control[] = 'delete';
        }

        if ($this->_canPrint) {
            $control[] = 'print';
        }

        return $control;
    }

    /**
     * @return ElementAbstract
     * */
    public function controllButton ($col)
    {
        $btnGroup = new Div;
        $btnGroup->addClass(array('btn-group', 'grid-column-btn-group'))
                 ->attr('id', $col);

        if ($this->_canEdit) {
            $anchor = new Anchor;
            $anchor->addClass('edit');
            $span = new Span;
            $span->addClass('icon-pencil');

            $anchor->setContent($span);
            $anchor->addClass(array('btn', 'btn-mini'));
            $btnGroup->add($anchor);
        }

        if ($this->_canDetail) {
            $anchor = new Anchor;
            $anchor->addClass('detail');
            $span = new Span;
            $span->addClass('icon-eye-open');

            $anchor->setContent($span);
            $anchor->addClass(array('btn', 'btn-mini'));
            $btnGroup->add($anchor);
        }

        if ($this->_canChangeStatus) {
            $anchor = new Anchor;
            $anchor->addClass('status');
            $span = new Span;
            $span->addClass('icon-off');

            $anchor->setContent($span);
            $anchor->addClass(array('btn', 'btn-mini'));
            $btnGroup->add($anchor);
        }

        if ($this->_canDelete) {
            $anchor = new Anchor;
            $anchor->addClass('delete');
            $span = new Span;
            $span->addClass('icon-trash');

            $anchor->setContent($span);
            $anchor->addClass(array('btn', 'btn-mini'));
            $btnGroup->add($anchor);
        }

        if ($this->_canPrint) {
            $anchor = new Anchor;
            $anchor->addClass('print');
            $span = new Span;
            $span->addClass('icon-print');

            $anchor->setContent($span);
            $anchor->addClass(array('btn', 'btn-mini'));
            $btnGroup->add($anchor);
        }

        if ($this->_canExonerate) {
            $anchor = new Anchor;
            $anchor->addClass('exonerate');
            $span = new Span;
            $span->addClass('icon-exonerate');

            $anchor->setContent($span);
            $anchor->addClass(array('btn', 'btn-mini'));
            $btnGroup->add($anchor);
        }

        return $btnGroup;
    }
}