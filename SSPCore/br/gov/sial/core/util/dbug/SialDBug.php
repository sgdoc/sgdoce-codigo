<?php
/*
 * Copyright 2012 ICMBio
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
namespace br\gov\sial\core\util\dbug;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\lib\dbug\dBug;

/**
 * SIAL
 *
 * Utilitário de Geração de Debugs
 *
 * @package br.gov.sial.core.util
 * @subpackage dbug
 * @name sialDBug
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class SialDBug extends SIALAbstract
{
    /**
     * Fábrica de objetos
     * @param mixed $obj
     * @param string|null $type
     * @param boolean $open
     * @return \br\gov\sial\core\util\lib\dbug\dBug
     */
    public static function factory ($obj, $type = NULL, $open = FALSE)
    {
        return new dBug(self::_convert($obj), $type, $open);
    }

    /**
     * converte
     * @param mixed $obj
     * @return mixed[]
     */
    private static function _convert ($obj)
    {
        $content = array();
        if (is_array($obj)) {
            $content = $obj;
        }

        if (is_object($obj)) {
            $orefObj = new \ReflectionObject($obj);
            $props = $orefObj->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);

            foreach ($props as $prop) {
                $prop->setAccessible(TRUE);
                $content[$prop->getName()] = $prop->getValue($obj);
            }

            $traces = debug_backtrace();

            foreach ($traces as $trace) {
                $content['stack'] = $trace;
            }

        }
        return $content;
    }
}