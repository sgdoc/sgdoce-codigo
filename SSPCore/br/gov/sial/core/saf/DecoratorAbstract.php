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
namespace br\gov\sial\core\saf;
use br\gov\sial\core\saf\ISAF,
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\Renderizable;

/**
 * @package br.gov.sial.core
 * @subpackage saf
 * @author j. augusto <augustowebd@gmail.com>
 * */
abstract class DecoratorAbstract extends SIALAbstract implements Renderizable, ISAF
{
    /**
     * @var ISAF
     * */
    protected $_component = NULL;

    /**
     * @param ISAF $component
     * */
    public function __construct (ISAF $component)
    {
        $this->_component = $component;
    }

    /**
     * {@inheritdoc}
     * */
    public function add ($element, $param = NULL, $place = 'body')
    {
        $param = $param ?: new \stdClass;

        if (is_string($element) && method_exists($this, $element)) {

            /*
             * a chamada '$this->$element' remete diretamente para classe filha
             * */
            $element = $this->$element($param);
        }

        $this->_component->add($element, $param, $place);

        return $this;
    }

    /**
     * return string
     * */
    public function render ()
    {
        return $this->_component->render();
    }

    /**
     * @return string
     * */
    public function __toString ()
    {
        return $this->render();
    }

    /**
     * Compõe o elemento target. Se target e element são strings, sua instâncias são criadas
     *
     * @param type $element
     * @param type $param
     * @return \br\gov\sial\core\output\screen\ElementAbstract
     */
    public function appendTo ($target, $element, $targetParams = NULL, $elementParams = NULL)
    {
        return $this->_component->appendTo($target, $element, $targetParams, $elementParams);
    }

    /**
     * Cria um elemento e retorna sua instância
     *
     * @param \br\gov\sial\core\output\screen\ElementAbstract | string $target
     * @param \br\gov\sial\core\output\screen\ElementAbstract | string $element
     * @param mixed[] $targetParams  - opcional
     * @param mixed[] $elementParams - opcional
     * @return \br\gov\sial\core\output\screen\ElementAbstract
     */
    public function create ($element, $param = NULL)
    {
        return $this->_component->create($element, $param);
    }

    /**
     * Procura recursivamente um elemento com o id informado
     *
     * @param string $id
     * @return ElementAbstract
     */
    public function getElementById ($id)
    {
        return $this->_component->getElementById($id);
    }

    /**
     * Procura recursivamente todos os elementos que possuem a classe informada
     *
     * @param string $class
     * @return ElementAbstract[]
     */
    public function getElementsByClass ($class)
    {
        return $this->_component->getElementsByClass($class);
    }

    /**
     * Procura recursivamente todos os elementos que possuem o atributo name informado
     *
     * @param string $class
     * @return ElementAbstract[]
     */
    public function getElementsByName ($name)
    {
        return $this->_component->getElementsByName($name);
    }
}