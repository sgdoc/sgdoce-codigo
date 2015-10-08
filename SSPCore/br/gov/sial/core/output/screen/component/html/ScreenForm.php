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
use br\gov\sial\core\output\screen\html\H1,
    br\gov\sial\core\output\screen\html\Br,
    br\gov\sial\core\output\screen\html\Div,
    br\gov\sial\core\output\screen\html\Form,
    br\gov\sial\core\output\screen\html\Anchor,
    br\gov\sial\core\output\screen\html\Paragraph,
    br\gov\sial\core\output\screen\component\html\Well,
    br\gov\sial\core\output\screen\component\html\ButtonBar;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * */
class ScreenForm extends Div
{
    /**
     * @var string
     * */
    const T_TYPE = 'html';

    /**
     * @param stdClass $param
     * */
    public function __construct (\stdClass $param)
    {
        $this->attr('id', $this->safeToggle($param, 'id', $this->genId($param)))
             ->addClass('SAFScreenForm')
             ->_SFInitComponent($param);
    }
    /**
     * @param stdClass $param
     * */
    protected final function _SFInitComponent (\stdClass $param)
    {
        // $this->_normaliseParam($param);

        $this->anchor();

        # define o titulo da tela
        $this->topTitle()->setContent($this->safeToggle($param, 'topTitle'));

        # inicializa o topbar
        $this->topbar();

        # inicializa o formulario
        $this->form();

        # inicializa o container de resultado
        $this->result();

        # inicializa a barra controle
        $this->bottombar();
    }

    /**
     * cria ancora usada para marcar topo da tela
     *
     * @param stdClass $param
     * */
    public function anchor ()
    {
        $idx = $this->id . '-anchor';
        $anchor = $this->getElementById($idx);

        if ($anchor) {
            return $anchor;
        }

        $anchor = new Anchor(NULL, NULL);
        $anchor->id = $idx;
        $this->add($anchor);

        return $this;
    }

    /**
     * @return H1
     * */
    public function topTitle ()
    {
        $idx = $this->id . '-topTitle';

        $elm = $this->getElementById($idx);

        if (!$elm) {
            $elm = H1::factory(NULL)->attr('id', $idx);
            $this->add($elm);
        }

        return $elm;
    }

    /**
     * @return Div
     * */
    public function topbar ()
    {
        $idx = $this->id . '-topbar';

        $elm = $this->getElementById($idx);

        if (!$elm) {

            $elm = Div::factory()
                      ->addClass('top-bar')
                      ->attr('id', $idx);

            $elmMsg = Div::factory()
                           ->attr('id', $idx . '-msg');

            $paragraph = Paragraph::factory()
                                  ->attr('id', $idx . '-title')
                                  ->addClass(array('requiredLegend', 'pull-left'));

            $btnBar  = Div::factory()
                          ->attr('id', $idx . '-btnbar')
                          ->addClass(array('btn-group', 'pull-right'));

            $elm->add(array($paragraph, $btnBar));

            $this->add(array($elmMsg, $elm));
        }

        return $elm;
    }

    /**
     * @return Form
     * */
    public function form ()
    {
        $idx = $this->id . '-form';

        $elm = $this->getElementById($idx);
        if (!$elm) {
            $elm = new ScreenFormForm($idx);
            $this->add($elm);
        }

        $btnbar = new ButtonBar;
        $btnbar->attr('id', $idx . '-action');
        $elm->add(array(Br::factory(), $btnbar));

        return $elm;
    }

    /**
     * @return Div
     * */
    public function result ()
    {
        $idx = $this->id . '-result';
        $elm = $this->getElementById($idx);

        if (!$elm) {
            $elm = Div::factory()->attr('id', $idx);
            $this->add($elm);
        }

        return $elm;
    }

    /**
     * @return Buttombar
     * */
    public function bottombar ()
    {
        $idx = $this->id . '-bottombar';
        $elm = $this->getElementById($idx);

        if (!$elm) {
            $elm = new ButtonBar();
            $elm->addClass('hide')
                ->attr('id', $idx);

           $this->add($elm);
        }

        return $elm;
    }

    /**
     * @param stdClass $param
     * @param string $type
     * @throws IllegalArgumentException
     * */
    public static function factory (\stdClass $param)
    {
        return new self($param);
    }
}