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
    br\gov\sial\core\output\screen\html\Div,
    br\gov\sial\core\output\screen\html\Text,
    br\gov\sial\core\output\screen\html\Span,
    br\gov\sial\core\output\screen\html\Label,
    br\gov\sial\core\output\screen\html\Input,
    br\gov\sial\core\output\screen\component\InputLabelAbstract;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * @name ButtonBar
 * */
class InputLabel extends InputLabelAbstract implements IBuild
{
    /**
     * Construtor
     * @param \stdClass $param
     */
    public function __construct ($param)
    {
        $this->_controls = Div::factory()->addClass('controls');
        $this->_label = Label::factory($this->safeToggle($param, 'label'))->addClass('control-label');

    if (TRUE == $this->safeToggle($param, 'required')) {
        $span = new Span();
        $span->addClass(Input::T_INPUT_REQUIRED_CLASS)
             ->add(new Text(Input::T_INPUT_REQUIRED_MASK));
        $this->_label->add($span);
    }

    # @todo expandir este metodo para suportar qualquer tipo de input
    $this->_controls->add(new Input($param->name, $param->type));

    $this->_inputLabel = Div::factory()->addClass('control-group')
                                       ->add($this->_label)
                                       ->add($this->_controls);
    }

    /**
     *
     * @return \br\gov\sial\core\output\screen\component\html\InputLabel
     */
    public function build ()
    {
        return $this;
    }
}