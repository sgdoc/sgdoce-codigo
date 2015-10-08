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
    br\gov\sial\core\output\screen\html\H3,
    br\gov\sial\core\output\screen\html\Div,
    br\gov\sial\core\output\screen\html\Button,
    br\gov\sial\core\output\screen\html\Paragraph,
    br\gov\sial\core\output\screen\component\ModalAbstract;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * @name Modal
 * http://twitter.github.com/bootstrap/javascript.html#modals
 * */
class Modal extends ModalAbstract implements IBuild
{
    /**
     * Construtor
     * @param \stdClass $param
     */
    public function __construct (\stdClass $param)
    {
        $this->_modal = Div::factory()->attr('id', $param->id)
                                      ->addClass(self::T_MODALABSTRACT_DEFAULT_CSS)
                                      ->addClass('hide')
                                      ->addClass('fade')
                                      ->attr('tabindex', '-1')
                                      ->attr('role', 'dialog')
                                      ->attr('aria-hidden', 'true');

        if (isset($param->large) && $param->large === TRUE) {
            $this->_modal->addClass('modalFull');
        }

        $this->setTitle($param)
             ->setBody($param)
             ->setFooter($param);
    }

    /**
     * Adiciona o título do modal
     * @param stdClass $param
     * @return \br\gov\sial\core\output\screen\component\html\Modal
     */
    private function setTitle ($param)
    {
        $header = H3::factory()->setContent($param->title)->attr('id', 'myModalLabel');
        $close = Button::factory('&times;')->addClass('close')
                                           ->attr('aria-hidden', 'true')
                                           ->attr('data-dismiss', 'modal')
                                           ->attr('type', 'button');

        $this->_title = Div::factory()->addClass('modal-header')
                                      ->add($close)
                                      ->add($header);

        return $this;
    }

    /**
     * Monta o corpo do modal
     * @param stdClass $param
     * @return \br\gov\sial\core\output\screen\component\html\Modal
     */
    private function setBody ($param)
    {
        $content = Paragraph::factory()->add($param->body);
        $this->_body = Div::factory()->addClass('modal-body')
                                     ->add($content);

        return $this;
    }

    /**
     * Monta o rodapé do modal
     * @param type $param
     * @return \br\gov\sial\core\output\screen\component\html\Modal
     */
    private function setFooter ($param)
    {
        $content = Button::factory('Fechar')->addClass('btn')
                                            ->attr('type', 'button')
                                            ->attr('data-dismiss', 'modal');

        $this->_footer = Div::factory()->addClass('modal-footer')
                                       ->add($content);

        if (isset($param->footer)) {
            $this->_footer->add($param->footer);
        }

        return $this;
    }

    /**
     *
     * @param type $name
     * @param type $value
     * @return type
     * @throws IllegalArgumentException
     */
    public function __set ($name, $value)
    {
        $dic = array('options' => array('backdrop', 'keyboard', 'show', 'remote'),
                     'events' => array('show', 'shown', 'hide', 'hidden'));

        if (in_array($dic['options'], $name)) {
            return $this->options('data-' . $name, $value);
        }

        if (in_array($dic['events'], $name)) {
            return $this->events($name);
        }

        # Lança excetion caso a opção ou evento não seja suportada.
        parent::__set($name, $value);
    }

    /**
     * Opções para controle do componente
     * @param string $name
     * @param string $value
     * @return Modal
     */
    public function options ($name, $value)
    {
        return $this->_modal->$name = $value;
    }

    /**
     * Eventos disponíveis para o componente
     * @param string $name
     * @return Modal
     */
    public function events ($name)
    {
        return $this->_modal->addClass($name);
    }

    /**
     * @return Modal
     */
    public function build ()
    {
        return $this->_modal->add($this->_title)
                            ->add($this->_body)
                            ->add($this->_footer);
    }
}