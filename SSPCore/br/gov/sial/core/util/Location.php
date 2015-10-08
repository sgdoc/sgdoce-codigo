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
namespace br\gov\sial\core\util;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\exception\SIALException;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Location extends SIALAbstract
{
    /**
     * retorna o caminho da aplicacao (sandbox)
     *
     * @return string
     * */
    public static function sandbox ()
    {
        # modo seguro de include de arquivo
        $pwd = end(debug_backtrace());
        return dirname(dirname($pwd['file']));
    }

    /**
     * retorna TRUE se o namespace informa existir
     *
     * @param string $namespace
     * @returm boolean
     * */
    public static function hasNamespace ($namespace)
    {
        $namespace = self::realpathFromNamespace($namespace);

        if (is_dir($namespace)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * retorna TRUE se a classe existir no namespace
     *
     * @param string $namespace
     * @return boolean
     * */
    public static function hasClassInNamespace ($namespace)
    {
        $namespace = $namespace = self::realpathFromNamespace($namespace) . '.php';

        if (is_file($namespace)) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * retorna se cominho completo da classe (filha de SIALAbstract) informado
     *
     * @param SIALAbstract|string $target
     * @return string
     * @throws SIALException
     * */
    public static function realpathFromNamespace ($target)
    {
        if (is_object($target)) {
            return self::_objectPath($target);
        }

        if (is_string($target)) {
            return self::toggle(
                self::_existentPath($target),
                self::_prospection($target)
            );
        }

        throw new SIALException('O namespace informado não é suportado');
    }

    private static function _objectPath ($target)
    {
        $refl = new \ReflectionClass(__CLASS__);
        $pathinfo = pathinfo($refl->getFileName());
        return $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['filename'];
    }

    /**
     * retorna o caminho do arquivo/diretório existente ou que se supunha existir
     *
     * @param  string $targer
     * @return string
     * */
    private static function _existentPath ($target)
    {
        $target = ltrim($target, self::NAMESPACE_SEPARATOR);
        $include = array_reverse(array_flip(explode(PATH_SEPARATOR, get_include_path())));

        foreach ($include as $path => $key) {
            $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $fullpath = str_replace(self::NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, $path . $target);

            /* o namespace poderá apontar para uma classe ou para um pacote */
            if (is_file($fullpath . '.php') && is_dir($fullpath)) {
                return $fullpath;
            }
        }

        return NULL;
    }

    /**
     * retorna o caminho de namespace que ainda não existe
     *
     * @param  string $target
     * @return string
     */
    private static function _prospection ($target)
    {
        $target = parent::NAMESPACE_SEPARATOR == $target[0] ? substr($target, 1) : $target;
        $sep = current(explode(parent::NAMESPACE_SEPARATOR, $target));

        $dirbase   = __DIR__;

        # redefine o diretorio caso nao se trate de uma classe do SIAL
        # quando a classe nao sera do SIAL, quando ela for da aplicacao,
        # ou seja, o namespace nao iniciara com 'br\gov\sial'
        if (! preg_match('/\\\?(br\\\gov\\\sial)/', $target)) {
            $backtrace = debug_backtrace();
            $dirbase   = end($backtrace);
            $dirbase   = current(explode($sep, $dirbase['file']));
        }

        $dirbase  = (string) current(preg_split("/\/{$sep}\//", $dirbase)) . DIRECTORY_SEPARATOR . $target;
        $dirbase  = str_replace(parent::NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, $dirbase);
        return str_replace(str_repeat(DIRECTORY_SEPARATOR, 2), DIRECTORY_SEPARATOR, $dirbase);
    }
}
