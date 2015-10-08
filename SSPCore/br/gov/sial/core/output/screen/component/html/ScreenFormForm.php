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
    br\gov\sial\core\output\screen\html\Form,
    br\gov\sial\core\output\screen\html\Text,
    br\gov\sial\core\output\screen\html\Label,
    br\gov\sial\core\output\screen\html\Legend,
    br\gov\sial\core\output\screen\html\Fieldset,
    br\gov\sial\core\output\screen\ElementAbstract;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * */
class ScreenFormForm extends Form
{
    /**
     * @var string
     * */
    const T_SCRENFORMFORM_REQUIRED_TOKEN = '* ';

    /**
     * @var boolean
     * */
    const T_SCREEN_FORM_RETURN_WADD_GENERATED_FIELD = TRUE;

    /**
     * @var Fieldset
     * */
    protected $_container;

    /**
     * @param stdClass $param
     * */
    public function __construct ($idx)
    {
        parent::__construct();
        $this->id = $idx;

        $this->_container = Fieldset::factory()->addClass(array('static-assets', 'form-horizontal'));
        $this->_container->id = $this->id . '-body';

        $legend = Legend::factory();
        $legend->id = $this->_container->id . '-legend';
        $this->_container->add($legend);
        $this->add($this->_container);
    }

    public function container ()
    {
        return $this->_container;
    }

    /**
     * @return ElementAbstract
     * */
    public function __get ($idx)
    {
        /* libera o corpo do formulario, onde reside os elms */
        if ('body' == $idx) {
            return $this->_container;
        }

        return parent::__get($idx);
    }

    /**
     * @param string $legend
     * @return ScreenFormForm
     * */
    public function setLegend ($legend)
    {
        $this->_container->legend->setContent($legend);
        return $this;
    }

    /**
     * remove legenda
     *
     * @return ScreenFormForm
     * */
    public function removeLegend ()
    {
        $this->_container->removeLegend();
        return $this;
    }

    /**
     * wizard para inclusao de elementos no form
     *
     * @param string $txLabel
     * @param ElementAbstract|string $element
     * @param boolean $isRequired
     * @param string $posLegend
     * @param boolean
     * @return ScreenFormForm
     * */
    public function wAdd
    (
        $txLabel,
        $element,
        $isRequired = FALSE,
        $posLegend = NULL,
        $returnCreatedElem = FALSE
    )
    {
        $divCtlGroup = Div::factory()->addClass('control-group');
        $divCtrls    = Div::factory()->addClass('controls');

        if (!($element instanceof ElementAbstract)) {
            $element = Text::factory($element);
        }

        $for    = $element->getAttr('id') ?: $element->getAttr('name');
        $label  = Label::factory(NULL, $for)->addClass('control-label');
        $divCtlGroup->add(array($label, $divCtrls));

        if (TRUE == $isRequired) {

            $element->addClass('required');

            $span = Span::factory()
                        ->addClass('required')
                        ->setContent(self::T_SCRENFORMFORM_REQUIRED_TOKEN);

            $label->add($span);
        }

        $label->add(Text::factory($txLabel));

        $divCtrls->add($element);

        if (NULL != $posLegend) {

            if (!($posLegend instanceof ElementAbstract)) {
                $posLegend = Text::factory(' ' . $posLegend);
            }

            $divCtrls->add($posLegend);
        }

        if ($returnCreatedElem) {
            return $divCtlGroup;
        }

        $this->_container->add($divCtlGroup);

        return $this;
    }
}

