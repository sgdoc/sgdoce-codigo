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
namespace br\gov\sial\core\output;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\Renderizable,
    br\gov\sial\core\util\Location,
    br\gov\sial\core\output\screen\DocumentAbstract,
    br\gov\sial\core\output\screen\DecoratorAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage output
 * @name ApplicationAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class ApplicationAbstract extends SIALAbstract implements Renderizable
{
    /**
     * @var string
     * */
    const T_APPLICATION_INVLAID_APP = 'o tipo de aplicação informada não está disponível';

    /**
     * @var string
     * */
    const T_APPLICATION_INVALID_AREA = 'a area informada é inválida. aceito apenas: "head" ou "body"';

    /**
     * @var string
     * */
    protected $_type;

    /**
     * @var DecoratorAbstract
     * */
    protected $_decorator = NULL;

    /**
     * @var DocumentAbstract
     * */
    protected $_document = array();

    /**
     * Adiciona elemento ao documento.
     *
     * O primeiro param define o tipo de elemento que sera adicionado, como por exemplo label, campo input, select
     * div e etc., o segundo param, objeto|array , define todas as propriedades do elemento, o terceiro param define
     * a area de inclusao, existem apenas duas opcoes: head e body.
     *
     * @code
     * <?php
     *     # adiciona referencia externa
     *     $app->add('javascript, array('href', 'http....', 'head');
     *
     *     # adiciona um  label a app
     *     $app->add('label', array('for' => 'idElement', 'text' => 'nome do usuario'));
     *
     *     # adiciona um campo
     *     $app->add('input', array('name', 'elementName', 'value' => 'foo'));
     *
     *     # adiciona objeto
     *     $app->add(new Input('field_name'));
     * ?>
     * @endcode
     *
     * @param string $elType
     * @param stdClass $config
     * @param enum[head, body] $area
     * @return ElementAbstract
     * */
    public abstract function add ($elType, $config = NULL, $area = 'body');

    /**
     * @return string
     * */
    public function __toString ()
    {
        return $this->render();
    }

    /**
     * Cria aplicação.
     *
     * @example ApplicationAbstract::factory
     * @code
     * <?php
     *     $decStyle = 'ICMBioGreen';
     *     $docType  = 'html';
     *
     *     # decorador do documento
     *     $decorator = DecoratorAbstract::factory($docType, $decStyle);
     *
     *     # criar aplicacao simples sem decorator
     *     $app = ApplicationAbstract::factory($docType, $decorator);
     * ?>
     * @endcode
     *
     * @param string $type
     * @param DecoratorAbstract $decorator
     * @return Application
     * @throws IllegalArgumentException
     * */
    public static function factory ($type, $decorator = NULL)
    {
        $NSApplication = __NAMESPACE__ . self::NAMESPACE_SEPARATOR
                       . 'application' . self::NAMESPACE_SEPARATOR
                       . 'Application' . strtoupper($type);

        IllegalArgumentException::throwsExceptionIfParamIsNull(
            Location::hasClassInNamespace($NSApplication), self::T_APPLICATION_INVLAID_APP
        );

        $application = new $NSApplication;
        $application->_decorator = $decorator;
        return $application;
    }
}