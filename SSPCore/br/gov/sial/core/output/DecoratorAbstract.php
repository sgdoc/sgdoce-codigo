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
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output
 * @subpackage screen
 * @name DocumentAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class DecoratorAbstract extends SIALAbstract implements Renderizable
{
    /**
     * @var string
     * */
    const T_DECORATOR_INVLAID_APP = 'o decorador informada não está disponível';

    /**
     * Cria decorator.
     *
     * @example DecoratorAbstract::factory
     * @code
     * <?php
     *     $docType  = 'html';
     *     $decStyle = 'ICMBioGreen';
     *
     *     # decorador do documento
     *     $decorator = DecoratorAbstract::factory($docType, $decStyle);
     * ?>
     * @endcode
     *
     * @param string $type
     * @param string $decStyle
     * @return Decorator
     * @throws IllegalArgumentException
     * */
    public static function factory ($type, $decStyle = 'default')
    {
        $NSApplication = __NAMESPACE__ . self::NAMESPACE_SEPARATOR
                       . 'decorator' . self::NAMESPACE_SEPARATOR
                       . 'Decorator' . strtoupper($type);


        IllegalArgumentException::throwsExceptionIfParamIsNull(
            Location::hasClassInNamespace($NSApplication), self::T_DECORATOR_INVLAID_APP
        );

        return new $NSApplication;
    }
}