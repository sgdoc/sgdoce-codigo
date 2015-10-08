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
namespace br\gov\sial\core\mvcb\view\skeleton\html;
use br\gov\sial\core\mvcb\view\skeleton\Command as SCommand;

/**
 * Command HTML
 *
 * @package br.gov.sial.core.mvcb.view.skeleton
 * @subpackage html
 * @name Skeleton
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class Command extends SCommand
{
    /**
     * Armazena o nome do comando.
     *
     * @var string
     * */
    protected $_tag = NULL;

    /**
     * Define tag de abertura do comando.
     *
     * @return string
     * */
    public function open ()
    {
        return '<' . $this->tag() . $this->getProperties() . '>';
    }

    /**
     * Define tag de fechamento do comando.
     *
     * @return string
     * */
    public function close ()
    {
        return '</' . $this->tag() . '>' . PHP_EOL;
    }

    /**
     * Este método existe para evitar a obrigatoriedade de definir uma propriedade
     * para cada Command com o nome da tag HTML que o mesmo irá gerar.
     *
     * @return string
     * */
    public function tag ()
    {
        if (NULL === $this->_tag) {
            $this->_tag = strtolower(end(preg_split('/\\\\/', get_class($this))));
        }
        return $this->_tag;
    }

    /**
     * @return string
     *
     * @internal este metodo trabalhar em conjunto com um decorator para definir se uma determinada propriedade poderah
     * @internal ser transformado numa class css, por exemplo, que eh caso da propriedade 'nullable' que dever ser
     * @internal trocada pela class 'sial-required-field' que vai trabalhar em conjunto com jquery ;)
     * */
    public function getProperties ()
    {

        # analisa se existe propriedade que serao tratadas como css
        CSSAdapter::analise($this, $this->_properties);
        $properties = NULL;
        foreach ($this->_properties as $property => $value) {

            $properties .= " {$property}=\"{$value}\"";
        }
        return $properties;
    }

    /**
     * Renderizador padrão para HTML
     *
     * @return string
     * */
    public function render ()
    {
        $content  = '';
        $content .= $this->open();
        foreach ($this->_children as $son) {
            $content .= $son->render();
        }
        $content .= $this->close();
        return $content;
    }
}