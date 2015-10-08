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
use br\gov\sial\core\output\screen\ISection,
    br\gov\sial\core\output\screen\ElementContainerAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen
 * @subpackage html
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Form extends ElementContainerAbstract implements ISection
{
    /**
     *
     * @var string
     * */
    const T_TAG = 'form';

    /**
     * define que o campo será obrigatório
     *
     * @var boolean
     * */
    const T_FORM_FIELD_REQUIRED = TRUE;

    /**
     * define que o campo não será obrigatório
     *
     * @var boolean
     * */
    const T_FORM_FIELD_NOT_REQUIRED = FALSE;

    /**
     * @param string $name
     * @param string $action
     * */
    public function __construct ($name = NULL, $action = NULL)
    {
        $this->attr('enctype', 'application/x-www-form-urlencoded')
             ->attr('autocomplete', 'on')
             ->attr('method', 'get');

        if(NULL != $name) {
            $this->attr('name', $name);
        }
    }

    /**
     * @param string $name
     * @param string $action
     * @return Form
     * */
    public static function factory ($name = NULL, $action = NULL)
    {
        return new self($name, $action);
    }
}