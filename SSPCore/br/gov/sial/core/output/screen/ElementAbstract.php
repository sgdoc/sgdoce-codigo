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
namespace br\gov\sial\core\output\screen;
use br\gov\sial\core\Component,
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\Renderizable,
    br\gov\sial\core\output\screen\exception\ElementException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output
 * @subpackage screen
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class ElementAbstract extends SIALAbstract implements Renderizable, Component
{
    /**
     * @var string
     * */
    const T_TAG = 'unknow';

    /**
     * @var integer
     * */
    public static $_seed = 0;

    /**
     * @var string
     * */
    private $_content = NULL;

    /**
     * @var string[]
     * */
    private $_class = array();

    /**
     * @var string
     * */
    private $_property = array();

    /**
     * @param mixed $content
     * */
    public function __construct ($content = NULL)
    {
        if (NULL != $content) {
            $this->_content = $content instanceof $this ? $content->render() : $content;
        }
    }

    /**
     * (fake: public static)
     *
     * @override
     * @throws IllegalArgumentException
     * */
    public function __get ($name)
    {

        $attr = "_{$name}";

        if (property_exists($this, $attr)) {
            return $this->$attr;

        } elseif ($this->propertyExists($name)) {
            return $this->_property[$name];

        } elseif ('id' == $name) {
            return $this->propertyExists($name);
        }

        # se nenhuma das opcoes acima for atendidas,
        # uma illegalArgExc sera lancada
        parent::__get($name);
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * atalho para definicao de propriedades
     *
     * @override
     * @param string $name
     * @param string $value
     * @todo implementar relacao de propriedades aceitas para cada elemento
     * */
    public function __set ($name, $value)
    {
        if ('class' == strtolower($name)) {
            $this->addClass($value);
            return;
        }

        $this->attr($name, $value);
    }

    /**
     * @param string $content
     * @return ElementAbstract
     * @throws ElementException
     * */
    public function setContent ($content)
    {
        $this->_content = $content;

        return $this;
    }

    /**
     * aplica um conjunto de propriedades ao objeto
     *
     * @param stdClass $properties
     * @return ElementAbstract
     * */
    public function setProperties (\stdClass $properties)
    {
        foreach ($properties as $key => $val) {
            $this->attr($key, $val);
        }

        return $this;
    }

    /**
     * adiciona classe ao elemento
     *
     * @param string $class
     * @return ElementAbstract
     * */
    public function addClass ($class)
    {
        $arrClass = (array) $class;

        foreach ($arrClass as $cls) {
            $this->_class[] = $cls;
        }

        return $this;
    }

    /**
     * @return string
     * */
    public function content ()
    {
        return $this->_content;
    }

    /**
     * @param string $name
     * @param string $value
     * @return ElementAbstract
     * */
    public function attr ($name, $value)
    {
        $this->_property[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     * */
    public function getAttr ($name)
    {
        if ($this->propertyExists($name)) {
            return $this->_property[$name];
        }

        return NULL;
    }

    /**
     * desabilita o elemento
     *
     * @return ElementAbstract
     * */
    public function disable ()
    {
        $this->attr('disabled', 'disabled');

        return $this;
    }

    /**
     * marca o elemento como somente leitura
     *
     * @return ElementAbstract
     * */
    public function readonly ()
    {
        $this->attr('readonly', 'readonly');

        return $this;
    }

    /**
     * marca o elemento como somente leitura
     *
     * @return ElementAbstract
     * */
    public function autocomplete ($status)
    {
        $this->attr('autocomplete', $status ? 'on' : 'false');

        return $this;
    }

    /**
     * @return string
     * */
    public function render ()
    {
        return sprintf('<%1$s%2$s />', $this::T_TAG, $this->renderProperty());
    }

    /**
     * css class render
     *
     * @return string
     * */
    public function renderClass ()
    {
        $class = NULL;

        if (!empty($this->_class)) {
            $class = sprintf(' class="%s"', implode(' ', $this->_class));
        }

        return $class;
    }

    /**
     * element property
     *
     * @return string
     * */
    public function renderProperty ()
    {
        $properties = '';

        foreach ($this->_property as $key => $val) {
            $properties .= " {$key}=\"{$val}\"";
        }

        return $this->renderClass() . $properties;
    }

    /**
     * @return stirng
     * */
    public function __toString ()
    {
        return $this->render();
    }

    /**
     * @return ElementAbstract
     * */
    public static function factory ()
    {
        $class = get_called_class();

        return new $class;
    }

    /**
     * Verifica se a propriedade foi setada nesse elemento
     * @param string $property
     * @return boolean
     */
    public function propertyExists ($property)
    {
        return array_key_exists($property, $this->_property);
    }

    /**
     * Verifica se o elemento possui determinado classe
     * @param string $class
     * @return boolean
     */
    public function hasClass ($class)
    {
        return in_array($class, $this->_class);
    }

   /**
    * @code
    * <?php
    *
    * # chamada pelo contexto de objeto
    * $this->genId();
    *
    * # chamada pelo contexto da classe
    * ElementAbstract::genId();
    *
    * ?>
    * @endcode
    *
    * @param string[] $config
    * @return string
    */
   public function genId (\stdClass $config = NULL)
   {
       $ident = parent::safeToggle($config, 'id', NULL);

       if (!isset($config->id)) {
           $ident = 'rndID_' . self::$_seed++;
       }

       return $ident;
   }
}