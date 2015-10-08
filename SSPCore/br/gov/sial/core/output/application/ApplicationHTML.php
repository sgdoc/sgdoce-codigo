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
namespace br\gov\sial\core\output\application;
use br\gov\sial\core\output\screen\html\Base,
    br\gov\sial\core\output\screen\html\Meta,
    br\gov\sial\core\output\screen\html\Link,
    br\gov\sial\core\output\DecoratorAbstract,
    br\gov\sial\core\output\screen\html\Title,
    br\gov\sial\core\output\ApplicationAbstract,
    br\gov\sial\core\output\screen\html\Comment,
    br\gov\sial\core\output\screen\ElementAbstract,
    br\gov\sial\core\output\screen\html\Javascript,
    br\gov\sial\core\output\screen\DocumentAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output
 * @subpackage application
 * @name Application
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class ApplicationHTML extends ApplicationAbstract
{
    /**
     * Construtor.
     *
     * @param DecoratorAbstract $decorator
     * @throws IllegalArgumentException
     * */
    public function __construct (DecoratorAbstract $decorator = NULL)
    {
        $this->_type      = 'html';
        $this->_document  = DocumentAbstract::factory($this->_type);
        $this->_decorator = $decorator;
    }

    /**
     * Renderiza o ApplicationHTML.
     *
     * @return string
     * */
    public function render ()
    {
        return $this->_document->render();
    }

    /**
     * @inheritdoc
     * */
    public function add ($elType, $config = NULL, $area = 'body')
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull('head' == $area || 'body' == $area, self::T_APPLICATION_INVALID_AREA);

        $decorator = $this->_decorator;

        # designa a criacao do elemento
        $setup  = (object) $config;
        $element = $decorator->$elType($setup);

        # @todo remover este foreach e coloca-lo diretamente no decorator
        foreach ($setup as $idx => $val) {

            if ('data' == $idx) {
                continue;
            }

            if ('grid'       == $elType ||
                'img'        == $elType ||
                'textarea'   == $elType ||
                'menuNavbar' == $elType ||
                'buttonbar' == $elType ||
                'radioGroup' == $elType ||
                'fieldset' == $elType) {
                continue;
            }

            if ('content' == $idx) {
                $decorator->setContent($element, $setup);
                continue;
            }

            $element->$idx = $val;
        }

        $this->_document->$area->add($element);

        return $this;
    }
}