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
use br\gov\sial\core\output\screen\ElementAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen
 * @subpackage html
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Input extends ElementAbstract
{
    /**
     * @var string
     * */
    const T_TAG = 'input';

    /**
     * @var string
     * */
    const T_TAG_DEFAULT_TYPE = 'text';

    /**
     * @var string
     * */
    const T_INPUT_REQUIRED_CLASS = 'required';

    /**
     * @var string
     * */
    const T_INPUT_REQUIRED_TITLE = 'campo obrigatório';

    /**
     * @var string
     * */
    const T_INPUT_REQUIRED_MASK = '*';

    /**
     * @param string $name
     * @param string $type
     * */
    public function __construct ($name, $type = self::T_TAG_DEFAULT_TYPE)
    {
        $this->attr('name', $name)
             ->attr('type', $type);
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $value
     * @return Input
     * */
    public static function factory ($name, $type = self::T_TAG_DEFAULT_TYPE, $defaultValue = NULL)
    {
        $input = new self($name, $type);
        $input->value = $defaultValue;
        return $input;
    }

    /**
     * @param string $value
     * @return Input
     * */
    public function value ($value)
    {
        $this->attr('value', $value);
        return $this;
    }
}