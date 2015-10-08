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
namespace br\gov\sial\core\mvcb\view\skeleton;
use br\gov\sial\core\Renderizable,
    br\gov\sial\core\SIALAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.mvcb.view
 * @subpackage skeleton
 * @name Language
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Language extends SIALAbstract implements Renderizable
{
    /**
     * @var string
     * */
    private $_name;

    /**
     * @var Command[]
     * */
    private $_commands;

    /**
     * Construtor.
     *
     * @param string $name
     * */
    public function __construct ($name)
    {
        $this->_name = $name;
    }

    /**
     * Recupera o nome da linguagem.
     *
     * @return string
     * */
    public function name ()
    {
        return $this->_name;
    }

    /**
     * Traduz o comando.
     *
     * @param Element $content
     * @return Language
     * */
    public function translate (Element $element)
    {
        $this->_commands[] = Command::factory($this, $element);
        return $this;
    }

    /**
     * Renderiza o conteúdo.
     *
     * @return string
     * */
    public function render ()
    {
        $content = '';
        foreach ($this->_commands as $tag) {
            $content .= $tag->render();
        }
        return $content;
    }
}