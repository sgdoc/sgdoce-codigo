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
    br\gov\sial\core\output\screen\html\Div,
    br\gov\sial\core\output\screen\html\Anchor,
    br\gov\sial\core\output\screen\component\BrandbarAbstract;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * */
class Brandbar extends BrandbarAbstract implements IBuild
{
    /**
     * Classe CSS para o componente brandbar
     * @var string
     */
    const T_BRANDBAR_DEFAULT_CSS = 'navbar';

    /**
     * Construtor de brandbar
     * @param \stdClass $param
     */
    public function __construct ($param)
    {
        $this->_brandbar = new Div;
        $this->_brandbar->addClass(self::T_BRANDBAR_DEFAULT_CSS);
        $this->_brandbar->add($this->setElements($param));
    }

    /**
     * Retorna uma lista dos elementos que compõe o componente brandbar
     * @param \stdClass $param
     * @return \br\gov\sial\core\output\screen\html\UL
     */
    private function setElements ($param)
    {
        $ul = new UL;
        $ul->addClass('nav');

        foreach ($this->safeToggle($param, 'links', array()) as $link) {
            $li = new LI;
            $li->add(new Anchor($link['text'], $link['href']));
            $ul->add($li);
        }

        return $ul;
    }

    /**
     * @return BrandbarAbstract
     */
    public function build ()
    {
        return $this;
    }
}