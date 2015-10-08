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
namespace br\gov\sial\core\mvcb\view\skeleton\html\command;
use br\gov\sial\core\mvcb\view\skeleton\html\Command;

/**
 * SIAL
 *
 * @package br.gov.sial.core.mvcb.view.skeleton.html
 * @subpackage command
 * @name Select
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Select extends Command
{
    /**
     * Define tag de abertura do comando.
     *
     * @return string
     * */
    public function open ()
    {
        $options = $this->popProperty('options');
        $defaultValue = $this->popProperty('value');
        return '<select' . $this->getProperties() . '>' . $this->options($options, $defaultValue);
    }

    /**
     * Monta options
     *
     * @param stdClass[] $options
     * @param mixed $defaultValue
     * @return string
     * */
    public function options (array $options = NULL, $defaultValue)
    {
        $options = (array) $options;
        $content = '';
        foreach ($options as $option) {
            // @codeCoverageIgnoreStart
            $content .= sprintf(
            // @codeCoverageIgnoreEnd
                '%s%s<option value="%s"%s>%s</option>'
                , PHP_EOL
                , "\t"
                , $option->value
                , $option->value === $defaultValue ? ' selected' : ''
                , $option->text
            );
        }
        return $content;
    }

    /**
     * @return string
     * */
    public function close ()
    {
        return PHP_EOL. parent::close();
    }
}