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
    // br\gov\sial\core\output\screen\html\H2,
    br\gov\sial\core\output\screen\html\UL,
    br\gov\sial\core\output\screen\html\LI,
    br\gov\sial\core\output\screen\html\Div,
    br\gov\sial\core\output\screen\html\Span,
    br\gov\sial\core\output\screen\html\Strong,
    br\gov\sial\core\output\screen\html\Anchor,
    br\gov\sial\core\output\screen\html\HAbstract,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\output\screen\component\MenuAbstract;
 
/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * @name Menu
 * */
class Menu extends MenuAbstract implements IBuild
{
    /**
     * Construtor
     * @param \stdClass $param
     * */
    public function __construct (\stdClass $param)
    {
        $param->type = strtolower($param->type);
 
        $this->isValidType($param->type);
 
        $this->setMenuByType($param);
    }
 
    /**
     * Constrói o menu de acordo com o tipo solicitado
     *
     * @param type $param
     * */
    private function setMenuByType ($param)
    {
        switch ($param->type)
        {
            case 'h': return $this->_buildHorizontalMenu($param);
            case 'v': return $this->_buildVerticalMenu  ($param);
        }
    }
 
    /**
     * @param stdClass {id, title, options[[text, href]]}
     * @return Menu
     * */
    private function _buildVerticalMenu ($param)
    {
        # container do menu
       $divContainer =
        Div::factory()
           ->attr(
                'id',
                $this->safeToggle($param, 'id', 'nestedAccordion')
            );
 
        $title =
        HAbstract::factory(
            2,
            Strong::factory()
                  ->setContent($this->safeToggle($param, 'title'))
        );
 
        # necessário para
       $isFirst = TRUE;
 
        # responsavel por escrever a lista de opcoes
       $writelist = function ($params) use (&$writelist, $isFirst) {
 
            $ul = UL::factory();
 
            foreach ($params as $option) {
 
                $anchor = Anchor::factory(
                    $option['text'],
                    isset($option['href']) ? trim($option['href']) : '#'
                );
 
                # remove as opcoes de texto e link
               unset(
                    $option['text'],
                    $option['href']
                );
 
                $li = LI::factory()->add($anchor);
                $ul->add($li);
 
                if (is_array($option)) {
                    $li->add($writelist($option));
                }
 
                if (!empty($option)) {
                    $anchor->addClass('trigger');
                }
            }
 
            return $ul;
        };
 
        $ULMain = $writelist($param->options);
        $ULMain->addClass('menu');
 
        $divContainer->add(array(
            $title, $ULMain
        ));
 
        $this->_menu =
        Div::factory()
           ->addClass('span3')
           ->add($divContainer);
 
        return $this;
    }
 
    /**
     * Constrói o menu horizontal
     *
     * @param \stdClass $param
     * @return ElementContainerAbstract
     * */
    private function _buildHorizontalMenu ($param)
    {
         # navbar
       $mainDiv = new Div;
        $mainDiv->addClass(array(
            'navbar', 'navbar-fixed-top'
        ));
 
        # inner
       $divInner = new Div;
        $divInner->addClass('navbar-inner');
        $mainDiv->add($divInner);
 
        # fluid
       $divFluid = new Div;
        $divFluid->addClass('container-fluid');
        $divInner->add($divFluid);
 
        # anchor
       $anchor = new Anchor;
        $anchor->attr('data-target', '.nav-collapse')
               ->attr('data-toggle', 'collapse')
               ->addClass(array('btn', 'btn-navbar'));
        $divFluid->add($anchor);
 
        # @todo a quantidade de icon-bar deve ser dinamica
       $span = new Span;
        $span->addClass('icon-bar');
        $anchor->add(array(clone $span, clone $span, $span));
 
        $divCollapse = new Div;
        $divCollapse->addClass('nav-collapse');
        $divFluid->add($divCollapse);
 
        $ulNav = new UL;
        $ulNav->addClass('nav');
 
        foreach ((object) $this->safeToggle($param, 'options', new \stdClass) as $key => $val) {
 
            $li = new LI;
            $ulNav->add($li);
 
            if ('__DIVIDER_VERTICAL__' == $val) {
                $li->addClass('divider-vertical');
            } else {
                $anchorT = new Anchor($key, '#');
 
                $strong = new Strong;
                $strong->addClass('caret');
                $anchor->add($strong);
 
                $ul = new UL;
 
                if (is_array($val) && !empty($val)) {
                    $anchorT->attr('data-toggle', 'dropdown');
                    $li->addClass('dropdown');
                    $ul->addClass('dropdown-menu');
 
                    foreach ($val as $link) {
                        $anchor = new Anchor($link['text'], $link['href']);
                        $liSub = new LI;
                        $liSub->add($anchor);
                        $ul->add($liSub);
                    }
                } else {
                    $anchorT->addClass('active');
                }
 
                $li->add($anchorT);
                $li->add($ul);
            }
        }
 
        $divCollapse->add($ulNav);
 
 
        $this->_menu = $mainDiv;
 
        return $this;
    }
 
 
    /**
     * Verifica se o tipo do menu solicitado é válido.
     * @param string $type
     */
    private function isValidType ($type)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull($type == 'h' | $type == 'v', self::T_MENUABSTRACT_INVALID_TYPE);
    }
}