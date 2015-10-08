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
namespace br\gov\sial\core\output\screen\component;
use br\gov\sial\core\output\screen\html\UL,
    br\gov\sial\core\output\screen\html\Div,
    br\gov\sial\core\output\screen\ElementAbstract,
    br\gov\sial\core\output\screen\component\ComponentAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen
 * @subpackage component
 * @name GridPaginationAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class GridPaginationAbstract extends ComponentAbstract
{
    /**
     * @var GridAbstract
     * */
    protected $_grid;

    /**
     * @var integer
     * */
    protected $_numRegisterPerPage = 10;

    /**
     * @var boolean
     * */
    protected $_showPaginationLength = TRUE;

    /**
     * @param GridAbstract $grid
     * */
    public function __construct (GridAbstract $grid)
    {
        $this->_grid = $grid;
    }

    /**
     * @return ElementAbstract
     * */
    public function footer ()
    {
        $wrapper = new Div;
        $wrapper->addClass('row-fluid');

        $wHalfOne = new Div;
        $wHalfOne->addClass('span6');
        $wHalfTwo = clone wHalfOne;
        $wrapper->add(array($wHalfOne, $wHalfTwo));

        $wInfo = new Div;
        $wInfo->addClass('dataTables_info')
              ->attr('id', $this->name() . '_info');
        $wHalfOne->add($wInfo);

        $divPag = new Div;
        $divPag->addClass(array('dataTables_paginate', 'paging_bootstrap', 'pagination'));
        $wHalfTwo->add($divPag);

        $ul = new UL();

        return $wrapper;
    }
}