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
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\Renderizable,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * Command
 *
 * @package br.gov.sial.core.mvcb.view
 * @subpackage skeleton
 * @name Command
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class Command extends SIALAbstract implements Renderizable
{
    /**
     * @var string
     * */
    const UNAVAILABLE_COMMAND = "<i>Command</i> '<u>%s/%s</u>' indisponível";

    /**
     * @var Command[]
     * */
    protected $_children = array();

    /**
     * @var stdClass
     * */
    protected $_properties = array();

    /**
     * @var stdClass
     */
    protected $_text = array();

    /**
     * Construtor.
     *
     * @param Language $laguage
     * @param Element $element
     * */
    public function __construct (Language $laguage, Element $element)
    {
        $this->_properties = $element->getProperties();
        foreach ($element->children() as $child) {
            $this->_children[] = self::factory($laguage, $child);
        }
    }

    /**
     * Retorna TRUE se a propriedade existir na relação informada.
     *
     * @param string $attr
     * @return boolean
     * */
    public function hasProperty ($attr)
    {
        return isset($this->_properties->$attr);
    }

    /**
     * Retorna a propriedade se a mesma existir sem remove-la da relação
     *
     * @param string $attr
     * @return string
     * */
    public function property ($attr)
    {
        return $this->hasProperty($attr) ? $this->_properties->$attr : '';
    }

    /**
     * Renomeia o nome de uma propriedade se a mesma existir.
     *
     * @param string $old
     * @param string $new
     * */
    public function renameProperty ($old, $new)
    {
        if ($this->hasProperty($old)) {
            $this->_properties->$new = $this->popProperty($old);
        }
    }

    /**
     * Retorna a propriedade se a mesma existir removendo-a da relação
     *
     * @param string $attr
     * @return string
     * */
    public function popProperty ($attr)
    {
        $content = NULL;
        if (TRUE == $this->hasProperty($attr)) {
            $content = $this->property($attr);
            unset($this->_properties->$attr);
        }
        return $content;
    }

    /**
     * Define tag de abertura do command.
     *
     * @return string
     * @codeCoverageIgnoreStart
     * */
    public abstract function open ();
    // @codeCoverageIgnoreEnd

    /**
     * Define tag de fechamento do command
     *
     * @return string
     * @codeCoverageIgnoreStart
     * */
    public abstract function close ();
    // @codeCoverageIgnoreEnd

    /**
     * Fábrica de Command.
     *
     * @param Language $language
     * @param Element $element
     * @return Command
     * @throws IllegalArgumentException
     * */
    public static function factory (Language $laguage, Element $element)
    {
        # formata nome da linguagem e do command para uso
        $language = strtolower($laguage->name());
        $command  = ucfirst($element->type());

        # monta o namespace do commando alvo levando em consideracao a
        # linguagem inforamda
        $namespace = sprintf('br\gov\sial\core\mvcb\view\skeleton\%s\command\%s', $language, $command);

        $message = sprintf(self::UNAVAILABLE_COMMAND, $language, $command);
        IllegalArgumentException::throwsExceptionIfParamIsNull(self::isAvailable($namespace), $message);
        return new $namespace($laguage, $element);
    }

    /**
     * Verifica se um determinado Command esta disponível.
     *
     * @param string $namespace
     * @return boolean
     * */
    public function isAvailable ($namespace)
    {
        $namespace = self::realpathFromNamespace($namespace);
        return is_file($namespace . '.php');
    }
}