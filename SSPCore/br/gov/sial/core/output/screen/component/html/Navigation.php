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
use br\gov\sial\core\output\screen\IBuild,
    br\gov\sial\core\output\screen\html\UL,
    br\gov\sial\core\output\screen\html\LI,
    br\gov\sial\core\output\screen\html\Anchor,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\output\screen\component\NavigationAbstract;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * @name Navigation
 * */
class Navigation extends NavigationAbstract implements IBuild
{

    public function __construct ($param)
    {
        $this->isValidType($param->type);

        $this->_nav = new UL();
        $this->_nav->addClass('nav')
                   ->addClass('nav-' . $param->type);

        foreach ($param->item as $item) {
            $this->_item = new LI();

            # Menu Dropdown
            if (isset($item['dropdown'])) {
                $divider = new \br\gov\sial\core\output\screen\html\Strong();
                $divider->addClass('caret');

                $link = new Anchor($item['text'] . '&nbsp;' . $divider, $item['href']);
                $link->addClass('dropdown-toggle')
                     ->attr('data-toggle', 'dropdown');

                $this->_item->add($link);
                $this->_item->addClass('dropdown');

                $this->_dropdown = new UL();
                $this->_dropdown->addClass('dropdown-menu');

                foreach ($item['dropdown'] as $subItem) {
                    $li = new LI();
                    $li->add(new Anchor($subItem['text'], $subItem['href']));
                    $this->_dropdown->add($li);
                }

                $this->_item->add($this->_dropdown);
            } else {
                $this->_item->add(new Anchor($item['text'], $item['href']));
            }

            if ($item['text'] == $param->active) {
                $this->_item->addClass('active');
            }

            $this->_nav->add($this->_item);
        }
    }

    private function isValidType ($type)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull($type == 'tabs' || $type == 'lists' || $type == 'pills', self::T_NAVIGATIONABSTRACT_INVALID_TYPE);
    }

    public function build ()
    {
        return $this;
    }
}