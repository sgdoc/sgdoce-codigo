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
namespace br\gov\sial\core\mvcb\view;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\mvcb\view\exception\HelperException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.mvcb
 * @subpackage view
 * @name Helper
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Helper extends SIALAbstract
{
    /**
     * @var string
     * */
    const T_HELPER_STR_HELPER_UNAVAILABLE = "O Helper '%s' não foi encontrado.";

    /**
     * Armazenamento de namespaces dos helpers.
     *
     * @var string[]
     * */
    private static $_helperNamespace = array();

    /**
     * Intercepta chamada para executar o helper solicitado.
     *
     * @param string $helper
     * @param string[] $args
     * @throws IOException
     * @see br\gov\sial\core.SIALAbstract::__call()
     */
    public function __call ($helper, $args)
    {
        $namespace = self::_namespace(ucfirst($helper));
        return call_user_func_array(array(new $namespace, $helper), $args);
    }

    /**
     * Retorna true se existir o helper informado.
     *
     * @param string $helper
     * @return boolean
     * */
    public function has ($helper)
    {
        try {
            self::_namespace(ucfirst($helper));
        } catch (HelperException $hexc) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Registra local de armazendo de helper.
     *
     * @param string $namespace
     * */
    public static function registerNamespace ($namespace)
    {
        $namespace = self::NAMESPACE_SEPARATOR == substr($namespace, -1)
                   ? $namespace : $namespace . self::NAMESPACE_SEPARATOR;

        self::$_helperNamespace[] = $namespace;
    }

    /**
     * Retorna o nome completo do helper.
     *
     * @param string $helper
     * @throws HelperException
     * */
    private static function _namespace ($helper)
    {
        $fileHelper = "{$helper}.php";

        foreach (self::$_helperNamespace as $namespace) {
            $filename = self::realpathFromNamespace($namespace) . $fileHelper;

            if (is_file($filename)) {
                return $namespace . $helper;
            }
        }

        HelperException::throwsExceptionIfParamIsNull(FALSE, sprintf(self::T_HELPER_STR_HELPER_UNAVAILABLE, $helper));
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd
}