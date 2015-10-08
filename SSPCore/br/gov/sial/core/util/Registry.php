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
    br\gov\sial\core\exception\SIALException,
    br\gov\sial\core\exception\IllegalArgumentException;

// @codeCoverageIgnoreStart
// require_once 'Zend/Registry.php';
// @codeCoverageIgnoreEnd

/**
 * Repositorio de elementos alcançáveis em qualquer camada da aplicacao
 *
 * Esta classe tem por única e exclusivo objetivo permitir o armazenamento temporário
 * de valores definidos numa camada e que poderão serem recuperados noutra.
 * <br />
 * <i>Em sua versão 0.0.1, esta classe encapsula a classe Zend_Registry</i>
 * <ul>
 *    <li>Como registrar elementos:
 *        <ul>
 *            <li><b>string</b>: Registry::<i>set</i>('string', 'element value');</li>
 *            <li><b>array</b>: Registry::<i>set</i>('array', array('el#1','el#2', 'el#3'));</li>
 *            <li><b>objeto</b>: Registry::<i>set</i>('object', new stdClass());</li>
 *        </ul>
 *    </li>
 *    <li>Como recuperar elementos registrados:
 *        <ul>
 *            <li><b>string</b>: Registry::<i>get</i>('string');</li>
 *            <li><b>array</b>: Registry::<i>get</i>('array');</li>
 *            <li><b>objeto</b>: Registry::<i>get</i>('object');</li>
 *        </ul>
 *    </li>
 * </ul>
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @final
 * @name Registry
 * @author J. Augusto <augustowebd@gmail.com>
 * */
final class Registry extends SIALAbstract
{
    /**
     * @var string
     * */
    const T_REGISTRY_STR_NO_ENTRY_REGISTERED = 'Registry::%s não disponível/registrada';

    /**
     * @param Registry
     * */
    private static $_instance = NULL;

    /**
     * @var \ArrayObject
     * */
    private $_data = NULL;

    /**
     * construtor
     * */
    private function __construct ()
    {
    }

    /**
     * retorna TRUE se $index estiver registrado
     *
     * @param string $index
     * @return bool
     * */
    public static function isRegistered ($index)
    {
        if (NULL === self::$_instance) {
            return FALSE;
        }

        $instance = self::factory();
        return array_key_exists($index, $instance->_data);
    }

    /**
     * recupera o conteudo registrado pelo metodo <i>Registry::set()</i>
     *
     * @code
     * <?php
     *  # registra um valor no Registry
     *  Registry::set('strKey', 'someValue');
     *
     *  # recuper ao valor registrado
     *  echo Registry::get('strKey');
     *
     *  // output: someValue
     * ?>
     * @endcode
     * @param string $index
     * @return mixed
     * @throws \br\gov\sial\core\exception\SIALException
     * */
    public static function get ($index)
    {
        SIALException::throwsExceptionIfParamIsNull(
            self::isRegistered($index),
            sprintf(self::T_REGISTRY_STR_NO_ENTRY_REGISTERED, $index)
        );

        $instance = self::factory();
        return $instance->_data->offsetGet($index);
    }

    /**
     * registra um novo elemento para posterior recuperacao
     * o primeiro param será usado para identificar o conteúdo armazendo, necessario
     * para posterior recuperação.
     *
     * @code
     * <?php
     *  # registra um valor no Registry
     *  Registry::set('strKey', 'someValue');
     * ?>
     * @endcode
     * @param string $index
     * @param mixed $value
     * */
    public static function set ($index, $value)
    {
        $instance = self::factory();
        $instance->_data->offsetSet($index, $value);
    }

    /**
     * fábrica de objetos Registry
     *
     * @param mixed[] $content
     * @param integer $flags
     * @return Registry
     * */
    public static function factory (array $content = array(), $flags = \ArrayObject::ARRAY_AS_PROPS)
    {
        if (NULL === self::$_instance) {
            self::$_instance = new self;
            self::$_instance->_data = new \ArrayObject($content, $flags);
        }

        return self::$_instance;
    }
}