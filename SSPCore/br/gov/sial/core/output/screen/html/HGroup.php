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
use br\gov\sial\core\Component,
    br\gov\sial\core\output\screen\ISection,
    br\gov\sial\core\output\screen\ElementContainerAbstract,
    br\gov\sial\core\output\screen\exception\ElementException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen
 * @subpackage html
 * @name HGroup
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class HGroup extends ElementContainerAbstract implements ISection
{
    /**
     * @var string
     * */
    const T_TAG = 'hgroup';

    /**
     * @param HAbstract[] $element
     * */
    public function __construct ($element = NULL)
    {
        $elements = is_array($element) ? $element : array($element);
        foreach ($elements as $elm) {
            if (NULL !== $elm) {
                $this->add($elm);
            }
        }
    }

    /**
     * @param HAbstract $hElm
     * */
    public function add ($component)
    {
        ElementException::throwsExceptionIfParamIsNull($component instanceof HAbstract, 'apenas objetos HAbstract são aceitos');
        return parent::add($component);
    }
}