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
    br\gov\sial\core\output\screen\html\Span,
    br\gov\sial\core\output\screen\html\Text,
    br\gov\sial\core\output\screen\html\Anchor,
    br\gov\sial\core\output\screen\component\BreadcrumbAbstract;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * */
class Breadcrumb extends BreadcrumbAbstract implements IBuild
{
    /**
     * @param object @param
     * */
    public function __construct ($param)
    {
        $this->_breadcrumb = new UL();
        $divider = new Span(' / ');

        $this->_breadcrumb->addClass('breadcrumb');
        $divider->addClass('divider');
        $itens = $this->safeToggle($param, 'item', array());

        foreach ($itens as $item) {
            $this->_listItem = new LI();

            if (end($itens) == $item) {
                $link = new Text($item['text']);
                $this->_listItem->add($link)->addClass('active');
            } else {
                 $link = new Anchor($item['text'], $item['href']);
                 $this->_listItem->add($link)->add($divider);
            }

            $this->_breadcrumb->add($this->_listItem);
        }
    }

    /**
     * @return BreadcrumbAbstract
     */
    public function build ()
    {
        return $this;
    }
}