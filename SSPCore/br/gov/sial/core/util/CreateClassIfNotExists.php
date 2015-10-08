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
    br\gov\sial\core\exception\IOException;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'SIALAbstract.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'exception' . DIRECTORY_SEPARATOR . 'IOException.php';


/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @name CreateClassIfNotExists
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class CreateClassIfNotExists extends SIALAbstract
{
    /**
     * @var string
     * */
    const T_CREATECLASSIFNOTEXISTS_ERROR_ON_CREATE_CLASS = 'não foi possível criar a classe informada';

    /**
     * Cria classe se ela não existe
     * 
     * @param string $namespace
     * @param string $class
     * @param string $parent
     * @param string $implements
     * @param string $license
     * */
    public static function create ($namespace, $class, $parent = NUL, $implements = NULL, $license = NULL)
    {
        $NSTemp    = $namespace;
        $namespace = self::NAMESPACE_SEPARATOR == substr($namespace, -1) ? substr($namespace, 0, -1) : $namespace;
        $namespace = $namespace . self::NAMESPACE_SEPARATOR . $class;
        $fullpath  = Location::realpathFromNamespace($namespace) . '.php';


        if (TRUE == is_file($fullpath)) {
            return;
        }

        $dirname = dirname($fullpath);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0700, TRUE);
        }


        $content = sprintf(
            '<?php%5$s%1$s%5$s%2$s%5$s%5$s%3$s%5$s%4$s'
            , self::classLicense()
            , self::classLocation($NSTemp)
            , self::classDoc($NSTemp, $class)
            , self::classDef($class, $parent, $implements)
            , PHP_EOL
        );


        IOException::throwsExceptionIfParamIsNull(
            file_put_contents($fullpath , $content, LOCK_EX | FILE_TEXT), 
            self::T_CREATECLASSIFNOTEXISTS_ERROR_ON_CREATE_CLASS
        );      
    }

    /**
     * Retorna a licença padrão
     * 
     * @param string|null $license
     * @return string
     * */
    public static function classLicense ($license = NULL)
    {
        if (NULL == $license) {
            preg_match_all('/(?P<license>\/\*.*\*\/)[\s\n]*namespace/ms', file_get_contents(__FILE__), $output);

            # verifica se o bloco de licensa pode ser recuperado
            if (count($output['license'])) {
                $license = current($output['license']);
            }

        }

        return $license;
    }

    /**
     * retorna o namespace da classe
     * 
     * @param string $namespace
     * @return string
     * */
    public static function classLocation ($namespace)
    {
        $namespace = self::NAMESPACE_SEPARATOR == $namespace[0] ? substr($namespace, 1) : $namespace;
        return sprintf('namespace %s;', $namespace);
    }

    /**
     * Retorna o campo de documentação da classe
     * 
     * @param string $namespace
     * @param string $class
     * @return string
     * */
    public static function classDoc ($namespace, $class)
    {
        $namespace  = explode(self::NAMESPACE_SEPARATOR, $namespace);
        $subpackage = array_pop($namespace);
        $package    = implode(self::NAMESPACE_SEPARATOR, $namespace);
        
        $classDoc  = '/**'                          . PHP_EOL
                   . ' * SIAL'                      . PHP_EOL
                   . ' *'                           . PHP_EOL
                   . ' * @package '                 . $package    . PHP_EOL
                   . ' * @subpackage '              . $subpackage . PHP_EOL
                   . ' * @name ' . $class           . PHP_EOL
                   . ' * @author SIAL Generator'    . PHP_EOL
                   . ' * */';

        return $classDoc;
    }

    /**
     * Retorna a declaração da classe
     * 
     * @param string $class
     * @param string|null $parent
     * @param string|null $implements
     * @return string
     * */
    public static function classDef ($class, $parent = NULL, $implements = NULL)
    {
        $parant     = $parent ? sprintf(' extends %s', $parent) : NULL;
        $implements = $implements ? sprintf(' implements %s', $implements) : NULL;
        return sprintf('class %1$s%2$s%3$s%4$s{%4$s}', $class, $parant, $implements, PHP_EOL);
    }
}