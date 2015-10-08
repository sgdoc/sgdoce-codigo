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
    br\gov\sial\core\output\screen\html\Select,
    br\gov\sial\core\output\screen\component\ComboAbstract;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * @name Combo
 * */
class Combo extends ComboAbstract implements IBuild
{
    /**
     * Construtor
     * @var \stdClass $param
     */
    public function __construct (\stdClass $param)
    {
        $combo = new Select($param->name, $param->options, NULL, NULL, isset($param->selected) ? $param->selected : NULL);
        $combo->safeToggle($param, 'id');

        if (isset($param->multiple)) {
            $combo->multiple = 'multiple';
        }

        if (isset($param->attrs)) {
            $combo->setProperties($param->attrs);
        }

        if (isset($param->class)) {
            $combo->addClass($param->class);
        }

        $this->_combo = Div::factory()->addClass('controls')->add($combo);
    }

    public function build ()
    {
        return $this;
    }
}