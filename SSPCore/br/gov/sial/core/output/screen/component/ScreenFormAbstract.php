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
namespace br\gov\sial\core\output\screen\component;
use br\gov\sial\core\util\Location,
    br\gov\sial\core\output\screen\IBuild,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\output\screen\component\ComponentAbstract;

/**
 * @package br.gov.sial.core.output.screen
 * @subpackage component
 * */
abstract class ScreenFormAbstract extends ComponentAbstract
{
    /**
     * @var string
     * */
    const T_SCREENFORMABSTRACT_COMPONENT_UNAVAILABLE = 'O componente informado não está disponível';

    /**
     * @param stdClass $param
     * @param string $type
     * @throws IllegalArgumentException
     * */
    public static function factory (\stdClass $param, $type)
    {
        $namespace = __NAMESPACE__ . self::NAMESPACE_SEPARATOR . $type . self::NAMESPACE_SEPARATOR . 'ScreenForm';
        IllegalArgumentException::throwsExceptionIfParamIsNull(Location::hasClassInNamespace($namespace), self::T_SCREENFORMABSTRACT_COMPONENT_UNAVAILABLE);
        return $namespace::factory($param);
    }
}
