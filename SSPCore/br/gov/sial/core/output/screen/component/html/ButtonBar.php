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
    br\gov\sial\core\output\screen\html\Button,
    br\gov\sial\core\output\screen\html\Anchor,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\output\screen\component\ButtonBarAbstract;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * */
class ButtonBar extends ButtonBarAbstract implements IBuild
{
    /**
     * Relação de botões disponíveis
     *
     * @var array
     * */
    private $_elements = array(
                'first'    => array('element' => 'button', 'label' => ' Primeiro ', 'name' => 'first', 'icon' => 'icon-fast-backward', 'type' => 'button'),
                'prev'     => array('element' => 'button', 'label' => ' Anterior', 'name' => 'previous', 'icon' => 'icon-chevron-left', 'type' => 'button'),
                'next'     => array('element' => 'button', 'label' => ' Próxima', 'name' => 'next', 'icon' => 'icon-chevron-right', 'type' => 'button'),
                'last'     => array('element' => 'button', 'label' => ' Último ', 'name' => 'last', 'icon' => 'icon-fast-forward', 'type' => 'button'),
                'save'     => array('element' => 'button', 'label' => ' Salvar', 'name' => 'save', 'icon' => '', 'type' => 'button'),
                'edit'     => array('element' => 'button', 'label' => ' Alterar', 'name' => 'edit', 'icon' => 'icon-pencil', 'type' => 'button'),
                'abort'    => array('element' => 'button', 'label' => ' Anterior', 'name' => 'about', 'icon' => 'icon-remove', 'type' => 'button'),
                'cancel'   => array('element' => 'button', 'label' => ' Cancelar', 'name' => 'cancel', 'icon' => 'icon-remove', 'type' => 'button'),
                'back'     => array('element' => 'button', 'label' => ' Voltar', 'name' => 'back', 'icon' => 'icon-chevron-left', 'type' => 'button'),
                'delete'   => array('element' => 'button', 'label' => ' Excluir', 'name' => 'delete', 'icon' => 'icon-trash', 'type' => 'button'),
                'complete' => array('element' => 'button', 'label' => ' Concluir', 'name' => 'complete', 'icon' => '', 'type' => 'button'),
                'print'    => array('element' => 'button', 'label' => ' Imprimir', 'name' => 'print', 'icon' => 'icon-print', 'type' => 'button'),
                'submit'   => array('element' => 'button', 'label' => ' Enviar', 'name' => 'submit'),
                'forgot'   => array('element' => 'button', 'label' => ' Lembrar', 'name' => 'remember', 'type' => 'button'),
                'search'   => array('element' => 'button', 'label' => ' Pesquisar', 'name' => 'search', 'icon' => 'icon-search', 'type' => 'button'),
                'plus'     => array('element' => 'button', 'label' => ' Adicionar', 'name' => 'plus', 'icon' => 'icon-plus-sign', 'type' => 'button'),
                'less'     => array('element' => 'button', 'label' => ' Remover', 'name' => 'less', 'icon' => 'icon-less-sign', 'type' => 'button'),
                'reset'    => array('element' => 'button', 'label' => ' Limpar', 'name' => 'reset', 'type' => 'reset'),
                'add'      => array('element' => 'anchor', 'label' => ' Cadastrar', 'name' => 'add', 'type' => 'button'),
                'filter'   => array('element' => 'anchor', 'label' => ' Filtros', 'name' => 'filter', 'type' => 'button'),
                'import'   => array('element' => 'anchor', 'label' => ' Importar', 'name' => 'import', 'type' => 'button')
            );

    /**
     * @param \stdClass $param
     * */
    public function __construct (\stdClass $param = NULL)
    {
        $this->_buttonBar = new Div;
        $this->_buttonBar->addClass(array('form-actions'));
        $this->_addElements($param);
    }

    /**
     * @param string|string[] $class
     * @return ButtonBar
     * */
    public function addClass ($class)
    {
        $this->_buttonBar->addClass((array) $class);

        return $this;
    }

    /**
     * habilita o uso do botao informado, o botao precisa esta registrado em _elements
     * antes de ser habilidade
     *
     * @code
     * <?php
     *     # instancia class
     *     $buttonBar = new ButtonBar;
     *
     *     # habilita apenas um botao
     *     $buttonBar->enable('submit');
     *
     *     # habilita varios botoes
     *     $buttonBar->enable(array('submit', 'reset'));
     * ?>
     * @endcode
     * @param string|string[] $btn
     * */
    public function enable ($btn)
    {
        $options = array();
        foreach ((array) $btn as $elm) {
            $options['options'][] = $elm;
        }

        $this->_addElements((object) $options);

        return $this;
    }

    /**
     * Adiciona os elementos que irão compor o buttonBar
     *
     * @param stdClass $param
     * @return ButtonBar
     * @throws IllegalArgumentException
     * */
    private function _addElements (\stdClass $param = NULL)
    {
        foreach ($this->safeToggle($param, 'options', new \stdClass) as $elm) {

            if (is_object($elm)) {
                $type = $elm->type;
                unset($elm->type);
            } else {
                $type = $elm;
            }

            IllegalArgumentException::throwsExceptionIfParamIsNull(isset($this->_elements[$type]), self::T_BUTTONBARABSTRACT_INVALID_BUTTON);

            $property = (object) $this->_elements[$type];

            if (is_object($elm)) {
                foreach ($elm as $prop => $value) {
                    $property->$prop = $value;
                }
            }

            switch ($property->element) {
                case 'button':
                    $button = new Button($property->label, $property->name);
                    break;
                case 'anchor':
                    $href   = (isset($property->href)) ? $property->href : '#';
                    $button = new Anchor($property->label, $href);
                    break;
            }

            $button->id = $this->genId($property);
            $button->addClass(array('btn', 'btn-' . $property->name));

            if (isset($property->type)) {
                $button->attr('type', $property->type);
            }

            if (isset($property->icon)) {
                $span = new Span;
                $span->addClass($property->icon);
                $button->add($span);
            }

            if (isset($param->primary) && $param->primary == $property->name) {
               $button->addClass('btn-primary');

                if (isset($property->icon)) {
                   $span->addClass('icon-white');
                }
            }

            $this->_buttonBar->add($button)->add(new Text('&nbsp;'));
        }

        return $this;
    }

    /**
     *
     * @return \br\gov\sial\core\output\screen\component\html\ButtonBar
     * */
    public function build ()
    {
        return $this;
    }
}