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
namespace br\gov\sial\core\output\decorator;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\output\screen\html\HR,
    br\gov\sial\core\output\screen\html\Br,
    br\gov\sial\core\output\screen\html\LI,
    br\gov\sial\core\output\screen\html\UL,
    br\gov\sial\core\output\screen\html\Div,
    br\gov\sial\core\output\screen\html\Img,
    br\gov\sial\core\output\screen\html\Span,
    br\gov\sial\core\output\screen\html\Link,
    br\gov\sial\core\output\screen\html\Base,
    br\gov\sial\core\output\screen\html\Meta,
    br\gov\sial\core\output\screen\html\Text,
    br\gov\sial\core\output\screen\html\Label,
    br\gov\sial\core\output\screen\html\Input,
    br\gov\sial\core\output\screen\html\Title,
    br\gov\sial\core\output\screen\html\Button,
    br\gov\sial\core\output\screen\html\Select,
    br\gov\sial\core\output\screen\html\Anchor,
    br\gov\sial\core\output\screen\html\Strong,
    br\gov\sial\core\output\screen\html\Comment,
    br\gov\sial\core\output\screen\html\Fieldset,
    br\gov\sial\core\output\screen\html\TextArea,
    br\gov\sial\core\output\screen\html\Paragraph,
    br\gov\sial\core\output\screen\html\Javascript,
    br\gov\sial\core\output\screen\ElementAbstract,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\output\screen\component\GridAbstract,
    br\gov\sial\core\output\screen\component\GridDataSourceArray;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output
 * @subpackage screen
 * @name DocumentAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class DecoratorHTML extends SIALAbstract
{
    /**
     *
     * @var integer
     */
    public static $_seed = 0;

    /**
     * Define um conteúdo para o decorator.
     *
     * @param ElementAbstract $elm
     * @param stdClass $config
     * */
    public function setContent (ElementAbstract $elm, \stdClass &$config)
    {
        $class = end(explode('\\', get_class($elm)));
        switch ($class) {
            case 'Title':
            case 'Comment':
                    $elm->setContent($config->content);
                break;
            default:
               $elm->content = $config->content;
        }

        unset($config->content);
    }

    /**
     * @param stdClass $config
     * @return Title
     * */
    public function title (\stdClass $config)
    {
        return new Title($config->content);
    }

    /**
     * @param stdClass $config
     * @return Meta
     * */
    public function meta (\stdClass $config)
    {
        return new Meta;
    }

    /**
     * @param stdClass $config
     * @return Comment
     * */
    public function comment (\stdClass $config)
    {
        return new Comment;
    }

    /**
     * @param stdClass $config
     * @return Link
     * */
    public function link (\stdClass $config)
    {
        return new Link($config->href);
    }

    /**
     * @param stdClass $config
     * @return Base
     * */
    public function base (\stdClass $config)
    {
        return new Base($config->href);
    }

    /**
     * Cria referência para documento javascript
     *
     * @param stdClass $config
     * @return Javascript
     * */
    public function javascript (\stdClass $config)
    {
        return new Javascript($config->src);
    }

    /**
     * Cria menu.
     *
     * @param stdClass
     * @return Div
     * */
    public function menuNavbar (\stdClass $config)
    {
        # navbar
        $mainDiv = new Div;
        $mainDiv->addClass(array('navbar', 'navbar-fixed-top'));

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
        $divCollapse->add($ulNav);

        foreach ($config->options as $key => $val) {

            $li = new LI;
            $ulNav->add($li);

            if ('__DIVIDER_VERTICAL__' == $val) {
                $li->addClass('divider-vertical');

            } else {

                $li->addClass('dropdown');
                $anchor = new Anchor($key, '#');
                $anchor->attr('data-toggle', 'dropdown')
                       ->addClass('dropdown-toggle');
                $li->add($anchor);

                $strong = new Strong;
                $strong->addClass('caret');
                $anchor->add($strong);

                $ul = new UL;
                $ul->addClass('dropdown-menu');
                $li->add($ul);

                foreach ($val as $idx => $link) {
                    $anchor = new Anchor($link['text'], $link['href']);
                    $liSub = new LI;
                    $liSub->add($anchor);
                    $ul->add($liSub);
                }
            }
        }

        return $mainDiv;
    }

    /**
     * @param stdClass $config
     * @return Div
     * */
    public function topbar (\stdClass $config)
    {
        $topbar = new Div;
        $topbar->addClass('top-bar');

        if (isset($config->title)) {
            $paragraph = new Paragraph(new Text($config->title));
            $paragraph->addClass('requiredLegend pull-left');
            $topbar->add($paragraph);
        }

        if (isset($config->content)) {
            $topbar->add($config->content);
        }

        return $topbar;
    }

    /**
     * @param stdClass $config
     * @return Checkbox
     * */
    public function checkGroup (\stdClass $config)
    {
        return $this->groupRadioAndCheck('checkbox', $config);
    }

    /**
     * @param stdClass $config
     * @return Radio
     * */
    public function radioGroup (\stdClass $config)
    {
        return $this->groupRadioAndCheck('radio', $config);
    }

    /**
     * @param stdClass $config
     * @return Select
     * */
    public function combo (\stdClass $config)
    {
        $combo = new Select($config->name, $config->data);

        if (isset($config->multiple)) {
            $combo->multiple = 'multiple';
        }

        return $combo;
    }

    /**
     * @return Br
     * */
    public function br ()
    {
        return new Br;
    }

    /**
     * @return hr
     * */
    public function hr ()
    {
        return new HR;
    }

    /**
     * @param stdClass $config
     * @return Input
     * */
    public function input (\stdClass $config)
    {
        $input = new Input($config->name);
        $input->id = isset($config->id) ? $config->id : 'compoundField_' . $config->name . '_' . self::$_seed++;
        $input->placeholder = isset($config->placeholder) ? $config->placeholder : '';
        return $input;
    }

    /**
     * @param stdClass $config
     * @return TextArea
     * */
    public function textarea (\stdClass $config)
    {
        $textarea              = new TextArea($config->name, isset($config->value) ? $config->value : NULL);
        $textarea->placeholder = isset($config->placeholder) ? $config->placeholder : NULL;
        return $textarea;
    }

    /**
     * @param stdClass
     * @return Img
     * */
    public function img (\stdClass $config)
    {
        return new Img($config->src, isset($img->alt) ? $img->alt : NULL);
    }

    /**
     * @param stdClass $config
     * @return Button
     * */
    public function button (\stdClass $config)
    {
        $button = new Button($config->label, $config->name);
        $button->id = $this->genId($config);
        $button->addClass('btn');
        return $button;
    }

    /**
     * @param stdClass $config
     * @return Div
     * */
    public function compoundField (\stdClass $config)
    {
        $compound  = new Div;

        if (isset($config->preText)) {
            $compound->addClass('input-prepend');
            $span = new Span(new Text($config->preText));
            $span->addClass('add-on');
            $compound->add($span);
        }

        $compound->add($this->input($config));

        if (isset($config->posText)) {
            $compound->addClass('input-append');
            $span = new Span(new Text($config->posText));
            $span->addClass('add-on');
            $compound->add($span);
        }

        return $compound;
    }

    /**
     * @param stdClass $config
     * @return Div
     * */
    public function inputButton (\stdClass $config)
    {
        $inputButton = new Div;
        $inputButton->addClass('input-append');
        $inputButton->add($this->input($config));
        $inputButton->add($this->button($config));

        return $inputButton;
    }

    /**
     * Grupo de Radio button ou Checkbox.
     *
     * @param string $type
     * @param stdClass $config
     * @return Fieldset
     * */
    public function groupRadioAndCheck ($type, \stdClass $config)
    {
        $fiedset = new Fieldset(isset($config->title) ? $config->title : NULL );
        $content = NULL;

        if(!isset($config->content)){
            $config->content = array();
        }

        foreach ($config->data as $elm) {
            $elm = (object) $elm;

            $elmId = $this->genId($config);

            $label = new Label($elm->text, $elmId);

            $radio = new Input($config->name, $type);
            $radio->value = $elm->value;
            $radio->id    = $elmId;

            if (isset($elm->checked)) {
                $radio->checked = 'checked';
            }

            $label->add($radio);
            $fiedset->add($label);
        }

        return $fiedset;
    }

    /**
     * Barra de botões de ações.
     *
     * @param stdClass $config
     * @return Div
     * @throws IllegalArgumentException
     * */
    public function buttonbar (\stdClass $config)
    {
        $elemnts = array(
            'first'     => array ('label' => 'primeiro', 'name' => 'first',    'icon' => 'icon-fast-backward' ),
            'prev'      => array ('label' => 'anterior', 'name' => 'previous', 'icon' => 'icon-chevron-left' ),
            'next'      => array ('label' => 'próximo',  'name' => 'next',     'icon' => 'icon-chevron-right'),
            'last'      => array ('label' => 'último',   'name' => 'last',     'icon' => 'icon-fast-forward'),
            'save'      => array ('label' => 'salvar',   'name' => 'save',     'icon' => 'icon-download-alt'),
            'edit'      => array ('label' => 'alterar',  'name' => 'edit',     'icon' => 'icon-pencil'),
            'complete'  => array ('label' => 'concluir', 'name' => 'complete', 'icon' => 'icon-ok'),
            'abort'     => array ('label' => 'anterior', 'name' => 'about',    'icon' => 'icon-remove'),
            'cancel'    => array ('label' => 'cancelar', 'name' => 'cancel',   'icon' => 'icon-ban-circle'),
            'delete'    => array ('label' => 'excluir',  'name' => 'delete',   'icon' => 'icon-trash')
        );

        $buttonbar = new Div;
        $buttonbar->addClass('form-actions');

        foreach ($config->elements as $elm) {
            IllegalArgumentException::throwsExceptionIfParamIsNull(isset($elemnts[$elm]), 'botão indisponível');

            $property = (object) $elemnts[$elm];
            $button = $this->button($property);
            $span = new Span;
            $span->addClass($property->icon);

            $button->add($span);
            $buttonbar->add($button);
        }

        return $buttonbar;
    }

    /**
     * @param stdClass $config
     * @return Grid
     * */
    public function grid (\stdClass $config)
    {
        return GridAbstract::factory($config->title, $config->columns, new GridDataSourceArray($config->data))->build();
    }

    /**
     * Gerador de id para os elementos do decorator.
     * 
     * @param stdClass config
     * @return string
     * */
    public function genId (\stdClass $config)
    {
        return isset($config->id) ? $config->id : 'compoundField_' . $config->name . '_' . self::$_seed++;
    }
}