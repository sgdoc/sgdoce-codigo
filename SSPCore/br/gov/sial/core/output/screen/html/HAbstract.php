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
    br\gov\sial\core\output\screen\ElementAbstract,
    br\gov\sial\core\output\screen\ElementContainerAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen
 * @subpackage html
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class HAbstract extends ElementContainerAbstract implements ISection
{
    const T_HABSTRACT_LEVEL_ONE  = 1;
    const T_HABSTRACT_LEVEL_TWO  = 2;
    const T_HABSTRACT_LEVEL_TREE = 3;
    const T_HABSTRACT_LEVEL_FOUR = 4;
    const T_HABSTRACT_LEVEL_FIVE = 5;
    const T_HABSTRACT_LEVEL_SIX  = 6;

    /**
     * @var string
     * */
    const T_TAG = 'h';

    /**
     * @var integer
     * */
    private $_level = 1;

    /**
     * @param integer $leval
     * @param mixed $value
     * */
    public function __construct ($value = NULL)
    {
        $this->_level = $this::H_LEVEL;
        $this->setContent(NULL == $value ? '' : $value);
    }

    /**
     * @override
     * @return string
     * */
    public function render ()
    {
        return sprintf('<%1$s%2$s>%3$s</%1$s>', $this::T_TAG . $this->_level, $this->renderProperty(), $this->content());
    }

    /**
     * @param integer $level
     * @param string $title
     * */
    public static function factory ($level = 1, $title = NULL)
    {
        $level = (integer) $level;
        if (1 > $level) {
            $level = 1;
        } elseif (6 < $level) {
            $level = 6;
        }

        $namespace = explode(self::NAMESPACE_SEPARATOR, __CLASS__);
        array_pop($namespace);
        $namespace[] = 'H' . $level;
        $namespace = implode(self::NAMESPACE_SEPARATOR, $namespace);

        return new $namespace($title);
    }
}