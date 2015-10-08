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
use br\gov\sial\core\output\screen\html\Div;
use br\gov\sial\core\output\screen\html\Text;
use br\gov\sial\core\output\screen\html\Input;
use br\gov\sial\core\output\screen\html\Style;
use br\gov\sial\core\output\screen\html\Javascript;
use br\gov\sial\core\output\screen\ElementAbstract;
use br\gov\sial\core\output\screen\component\AutoCompleteAbstract;
use br\gov\sial\core\output\screen\component\exception\ComponentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * @name AutoComplete
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class AutoComplete extends AutoCompleteAbstract
{
    /**
     * @var Div
     * */
    private $_curtain;

    /**
     * construtor
     * */
    public function __construct (\stdClass $param)
    {
        parent::__construct($param);
        $this->_autoComplete = Div::factory()->attr('id', $this->_ident);
        $this->_curtain      = Div::factory()->attr('id', $this->_ident . '-curtain')
                                             ->addClass('autocomplete');
    }

    /**
     * @param string $filter
     * @return ElementAbstract
     * */
    public function createInputFilter ()
    {
        $input = $this->_filter;

        if (!($input instanceof $this)) {
            $type = 'input';
            $name = '';

            # verifica se é uma instrucao para criacao de elemento
            if (':' == $input[0]) {

                preg_match(self::T_AUTO_COMPLETE_CREATE_PATTERN, $input, $result);

                if (count($result)) {
                    $properties         = array();
                    $result['property'] =  explode(',', $result['property']);

                    foreach ($result['property'] as $prop) {
                        list($name, $value) = explode('=', $prop);
                        $properties[$name] = $value;
                    }

                    if (!isset($properties['name'])){
                        $properties['name'] = 'autocomplete-' . $this->genId();
                    }

                    $name = $properties['name'];

                    $type = $this->safeToggle($result, 'type', 'input');

                    if ('input' == $type) {
                        $type = 'text';
                    }
                }

                $input = Input::factory($name, $type)
                              ->attr('id', $this->_ident . '-input')
                              ->attr('autocomplete', 'off')
                              ->addClass($this->_inputClass);
            }
        }

        return  $input;
    }

    /**
     * @return string
     * */
    public function getUrlJS ()
    {
        return $this->_defaultCdn . 'component/js/SAFAutoComplete.js';
    }

    /**
     * @return string
     * */
    public function getUrlCSS ()
    {
        return $this->_defaultCdn . 'component/css/SAFAutoComplete.css';
    }

    /**
     * @return AutoComplete
     * */
    public function build ()
    {
        $input = $this->createInputFilter();

        $inputInfo = Input::factory(
            $this->_ident . '-info',
            'hidden',
            str_replace('"', "'", json_encode($this->infoToJS()))
        );

        $this->_autoComplete->add($input)
                            ->add($this->_curtain)
                            ->add($inputInfo)
                            ->add(Style::factory($this->getUrlCSS(), 'screen'))
                            ->add(Javascript::factory($this->getUrlJS()));

        return $this->_autoComplete;
    }
}