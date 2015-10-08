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
namespace br\gov\sial\core\output\screen\html;
use br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen
 * @subpackage html
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Select extends Input
{
    /**
     * @var string
     * */
    const T_TAG = 'select';

    /**
     * @var string
     * */
    const T_SELECT_PARAM_TEXT_REQUERID = 'o param $text é obrigatório quando o param $value for informado';

    /**
     * @var string
     * */
    protected $_selectedIndex;

    /**
     * @param string[]
     * */
    protected $_data;

    /**
     * nome da propriedade que sera usada como o 'value' do 'option'
     *
     * @var string
     * */
    protected $_value;

    /**
     * nome da propriedade que sera usada como o 'text' do 'option'
     *
     * @var string
     * */
    protected $_text;

    /**
     * o primeiro param define o nome do combo, o segundo é usado como fonte de dados, o terceiro define a propriedade
     * que sera utilizada para recupera o conteudo usado como valor do combo, o quarto para definir o valor do combo,
     * o quinto se definido ira usado como valor default do combo.
     *
     * Nota: Se o 'value' for informado 'text' torna-se-á abrigatório
     *
     * @param string $name
     * @param mixed $data
     * @param string $selectedIndex
     * */
    public function __construct ($name, $data = NULL, $value = NULL, $text = NULL, $selectedIndex = NULL)
    {
//        IllegalArgumentException::throwsExceptionIfParamIsNull(!($value && !$text), self::T_SELECT_PARAM_TEXT_REQUERID);
        $this->_data  = $data;
        $this->_text  = $text  ?: 'text';
        $this->_value = $value ?: 'value';
        $this->_selectedIndex = $selectedIndex;
        $this->attr('name', $name);
    }

    /**
     * @return string
     * */
    public function render ()
    {
        $options = '';
        $data = $this->safeToggle($this, '_data', array());

        foreach ($data as $idx => $val) {

            # isso garante que o usuario possa informar um array
            $val = (object) $val;

            # @todo refatorar a parte que trata dos valores para um metodo proprio
            $value = $idx;
            $text  = $val;

            if ($this->_value) {
                $pVal = $this->_value;
                $value = $val->$pVal;

                $txt   = $this->_text;
                $text  = isset($val->$txt) ? $val->$txt : NULL;
            }

            $extraData = NULL;
            if (isset($val->extraData)) {
                foreach ($val->extraData as $extraKey => $extraVal) {
                    $dataName = sprintf('data-%s', $extraKey);
                    $extraData .= sprintf('%s="%s" ', $dataName, $extraVal);
                }
            }

            $options .= sprintf(
                '<option value="%s"%s%s>%s</option>'
                , $value
                , $value == $this->_selectedIndex ? ' selected="true"' : NULL
                , $extraData
                , $text
                );
        }

        return sprintf('<%1$s%2$s>%3$s</%1$s>', $this::T_TAG, $this->renderProperty(), $options);
    }
}