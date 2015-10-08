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
namespace br\gov\sial\core\util\validate;
use br\gov\sial\core\SIALAbstract;

/**
 * SIAL
 *
 * validacao de valores
 *
 * @package br.gov.sial.core.util
 * @subpackage validate
 * @name Validate
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class Validate extends SIALAbstract implements Validatable
{
    /**
     * intercepta a chamda aos atalhos de validadores como: isEmail, isString, etc.
     * via de regra um validador devera esta agrupado neste pacote e seu nome composto pela parte seguinte ao prefixo
     * 'is', assim, devera existe uma classe de nome 'String' para o validador isString ou para para Integer para o
     * validador isInteger e assim por diante. Nao encotnrar esta classe implicara no retorno false da verificacao.
     *
     * @param string $method
     * @param mixed[] $param
     * @return string
     * <code>
     * <?php
     *    # validacacao modo estatico
     *    # verifica se a string informa e do tipo e-mail
     *
     *    # e-mail que sera validado
     *    $suspicious = 'username@fooserver.com.br';
     *
     *    if (Validate::isEmail($suspicious)) {
     *        ... email valido ...
     *    } else {
     *        ... email invalido ...
     *    }
     *
     *    # validacao usando objeto validate
     *    $validator = new br\gov\icmbio\sial\util\validate\Email();
     *
     *    if ($validator->isValid($suspicious)) {
     *        ... email valido ...
     *    } else {
     *        ... email invalido ...
     *    }
     *
     *   # valida se um numero arbitrario esta contido na escala informada
     *     if (Validate::isBetween(array(17, array('min' => 10, 'max' => '50')))) {
     *      ... o numero esta contido ...
     *   }
     *
     *   # verifica se o numero e igual ou esta contido na escala informada
     *     if (Validate::isBetween(array(17, array('min' => 17, 'max' => '50', 'inclusive' => true)))) {
     *      ... o numero esta contido ...
     *   } else {
     *       ... o numero nao esta contigo ...
     *   }
     *  ?>
     * </code>
     * */
    public static function __callStatic ($method, array $param = array())
    {
        $nsValidator = self::_parse(substr($method, 2));
        if (empty($nsValidator)) {
            return FALSE;
        }

        $validate = new $nsValidator;
        return $validate->isValid(1 === sizeof($param) ? current($param) : $param);
    }

    /**
     * retorna o namespace completo da classe caso esta exista ou uma strig vazia caso contrario
     *
     * @param string $class
     * @return string
     * */
    private static function _parse ($class)
    {
        $namespace = __NAMESPACE__ . self::NAMESPACE_SEPARATOR . $class;
        $filename  = self::realpathFromNamespace($namespace) . '.php';

        if (is_file($filename)) {
            require_once $filename;
            if (class_exists($namespace)) {
                return $namespace;
            }
        // @codeCoverageIgnoreStart
        }
        // @codeCoverageIgnoreEnd
        return '';
    }
}