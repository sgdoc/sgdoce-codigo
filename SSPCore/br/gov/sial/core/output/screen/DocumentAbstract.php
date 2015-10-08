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
abstract class DocumentAbstract extends ElementContainerAbstract implements Rootable
{
    /**
     * @var string
     * */
    const T_INVALID_TYPE = 'O tipo de documento informado não suportado';

    /**
     * @param string $type = 'html'
     * @throws IllegalArgumentException
     * */
    public static function factory ($type = 'html')
    {
        $namespace = __NAMESPACE__ . self::NAMESPACE_SEPARATOR .
                      $type        . self::NAMESPACE_SEPARATOR .
                      'Document';
        IllegalArgumentException::throwsExceptionIfParamIsNull(Location::hasClassInNamespace($namespace), self::T_INVALID_TYPE);
        return new $namespace($type);
    }
}