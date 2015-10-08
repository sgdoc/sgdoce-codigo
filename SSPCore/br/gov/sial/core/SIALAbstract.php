<?php
/*
 * Copyright 2011 ICMBio
 *
 * Este arquivo é parte do programa SIAL Framework
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
namespace br\gov\sial\core;
use br\gov\sial\core\util\Location;
use br\gov\sial\core\exception\SIALException;
use br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL Framework
 *
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class SIALAbstract
{
    /**
     * habilita o modo debug
     * */
    const T_SIALABSTRACT_DEBUG_MODE = FALSE;

    /**
     * separador de namespace
     *
     * @var string
     * */
    const NAMESPACE_SEPARATOR = '\\';

    /**
     * define o charset default
     *
     * @var string
     * */
    const CHARSET = 'utf-8';

    /**
     * @var string
     * */
    const UNSUPPORTED_TYPE = 'Tipo de dado inválido';

    /**
     * @var string
     * */
    const CONTENT_DOT_EXISTS = '%s não existe';

    /**
     * @var string
     * */
    const CONTENT_UNDEFINED = '%s não está definido';

    /**
     * @var string
     * */
    const CONTENT_MUST_BE_A_OBEJCT = '%s deve ser um objeto';

    /**
     * @param string $name
     * @param mixed $arguments
     * @throws SIALException
     * */
    public function __call ($name, $arguments)
    {
        $target =  $this->getClassName();
        throw new SIALException(sprintf(self::CONTENT_DOT_EXISTS, "{$target}::{$name}()"));
    }

    /**
     * @name __get
     * @param string $name
     * @return mixed
     * @throws SIALException
     * */
    public function __get ($name)
    {
        $target = $this->getClassName();
        throw new SIALException(sprintf(self::CONTENT_UNDEFINED, "{$target}::{$name}"));
    }

    /**
     * @name __set
     * @access public
     * @param string $name
     * @param mixed $value
     * @throws SIALException
     * */
    public function __set ($name, $value)
    {
        $target =  $this->getClassName();
        throw new SIALException(sprintf(self::CONTENT_UNDEFINED, "{$target}::{$name}"));
    }

    /**
     * retorna true se o metodo existir na classe. Opcionalmente um objeto podera ser
     * informado para ser usado como contexto da verificacao.
     *
     * @name hasMethod
     * @param string $method
     * @param Object $context
     * @return boolean
     *
     * <?php
     *      # consultado existencia de metodo de classe que nao seja
     *      # filha de SIALAbstract
     *      $someObject = new SomeClass();
     *      $someMehtod = 'methodName';
     *      if (TRUE === SIALAbstract::hasMethod($someMehtod, $someObject)) {
     *         ...
     *      } else {
     *         ...
     *      }
     *
     *      # consultado existencia de metodo de classe filha de SIALAbstract
     *      $someObject = new SIALChild();
     *      $someMehtod = 'methodName';
     *      if (TRUE === $this->hasMethod($someMehtod)) {
     *         ...
     *      } else {
     *         ...
     *      }
     * ?>
     * @endcode
     * */
    public function hasMethod ($method, $context = NULL)
    {
        $context = is_object($context) ? $context : $this;
        return method_exists($context, (string) $method);
    }

    /**
     * retorna o nome da classe do objeto informado
     *
     * @name getClassName
     * @param object $refer
     * @return string
     * @throws SIALException
     * */
    public function getClassName ($refer = NULL)
    {
        if ($refer && !is_object($refer)) {
            throw new SIALException(sprintf(self::CONTENT_MUST_BE_A_OBEJCT, $refer));
        }

        # prevente static call
        $called = isset($this) ? $this : __CLASS__;
        return get_class(self::toggle($refer, $called));
    }

    /**
     * retorna uma string com o namespace da classe informada
     *
     * @param object $refer
     * @return string
     *
     * @code
     * <?php
     *      # codigo ...
     * ?>
     * @endcode
     * */
    public function getNamespace ($refer = NULL)
    {
        if ($refer && !is_object($refer)) {
            throw new SIALException(sprintf(self::CONTENT_MUST_BE_A_OBEJCT, $refer));
        }

        $namespace = explode(self::NAMESPACE_SEPARATOR, $refer ? get_class($refer) : get_called_class());
        array_pop($namespace);
        return (string) implode(self::NAMESPACE_SEPARATOR, $namespace);
    }

    /**
     * <p>retorna o segundo argumento se o primeiro for avaliado como false</p>
     *
     * @example SIALAbstract::toggle
     * @code
     *  <?php
     *      # definicao das opcoes de avaliacao
     *      $firstOption  = 'assert false';
     *      $secondOption = 'assert true';
     *
     *      SialAbstract::toggle($firstOption, $secondOption);
     * ?>
     * @endcode
     *
     * @param mixed $fParam
     * @param mixed $sParam
     * @return mixed
     * */
    public final function toggle ($fParam, $sParam)
    {
        return $fParam ?: $sParam;
    }

    /**
     * verifica de forma segura, sem lancar Notice, a existencia de uma propriedade
     * num determinado array ou objeto, podendo ainda assumir um valor padrao
     *
     * @param enum[array|object] $element
     * @param string $property
     * @param string $default
     * @return mixed
     * */
    public function safeToggle ($element, $property, $default = NULL)
    {
        $element = (object) $element;
        return isset($element->$property) ? $element->$property : $default;
    }

    /**
     * <p>Retorna TRUE se elemento informado existir no container (que podera ser um objeto ou array), opcionalmento um
     * terceiro paramentro, escalar, podera ser informado para confrontar com valor do elemento do container.</p>
     * <p>A pesquisa será realizada por chave.</p>
     * <p><strong>Este método será refatorado para outra classe.</strong></p>
     *
     * @example SIALAbstract::has
     * @code
     * <?php
     *     # container de elementos
     *     $container = array('foo', 'bar', 'foobar', 0 => 'zero');
     *
     *     # a chave 'foo' nao exist
     *     echo SIALAbstract::has($container, 'foo') ? 'foo' : 'not exists';
     *     // output: not exists
     * ?>
     * @endcode
     *
     * @param object|array $container
     * @param mixed $key
     * @param mixed $equalsTo
     * @return boolean
     * @throws SIALException
     * */
    public function has ($container, $key, $equalsTo = NULL)
    {
        self::isArrayOrObject($container);

        $type     = gettype($container);
        $content  = NULL;
        $status   = FALSE;

        if (is_array($container) &&  isset($container[$key])) {
            $content  = $container[$key];
            $status   = TRUE;

        } elseif (is_object($container) &&  isset($container->$key)) {
            $content  = $container->$key;
            $status   = TRUE;
        }

        return ((NULL === $equalsTo) ? $status : $content == $equalsTo);
    }

    /**
     * avalia se o container informado sao validos
     * retorna o tipo do container, se o mesmo for valido, ou lanca um sialException
     *
     * @code
     * <?php
     *  # elemento para teste
     *  $arr = array ('foo', 'bar');
     *
     *  echo SialAbstract::isArrayOrObject($arr);
     *  // output: array
     *
     *  $obj = new stdClass();
     *  echo SialAbstract::isArrayOrObject($obj);
     *  // output: object
     *
     *  SialAbstract::isArrayOrObject($arr['foo']);
     *  // IllegalArgumentException
     * ?>
     * @endcode
     * @param mixed $container
     * @return boolean
     * @throws SIALException
     * */
    public static function isArrayOrObject ($data)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull(
            (is_array($data) || is_object($data)), self::UNSUPPORTED_TYPE
        );

        return TRUE;
    }

    /**
     * efetua a troca do primeiro termo pelo segundo
     *
     * @example SIALAbstract::erReplace
     * @code
     * <?php
     *    # efetua a troca do primeiro termo pelo segundo na string $subject
     *
     *    # texto que sera manipulado
     *    $subject = 'Lorem ipsum dolor sit elit, consectetur adipiscing elit';
     *
     *    # termo termo de pesquisa
     *    $pattern = array('elit$' => 'Foo');
     *
     *    echo self::erReplace($pattern, $subject);
     *    // output: Lorem ipsum dolor sit elit, consectetur adipiscing Foo
     * ?>
     * @endcode
     *
     * @param string[] $pattern
     * @param string $subject
     * @return string
     * */
    public final function erReplace (array $patterns, $subject)
    {
        $patterns = (array) $patterns;

        # @todo com preg_replace não é necessário loop
        # remover este loop
        foreach ($patterns as $pattern => $replace) {
           $pattern = str_replace('\\', '\\\\', $pattern);
           $subject = preg_replace("/{$pattern}/", $replace, $subject);
        }

        return $subject;
    }

    /**
     * <p>retorna o conteudo armazenado no container (object|array) ou null se a propriedade nao existir ou nao for acessivel.</p>
     * <p>lanca <b>IllegalArgumentException</b> se o container informado for invalido</p>
     *
     *
     * @param object|array $container
     * @param string $attr
     * @return mixed
     * @throws IllegalArgumentException
     */
    public static function getIfDefined ($container, $attr)
    {
        self::isArrayOrObject($container);

        if (is_array($container) && isset($container[$attr])) {
            return $container[$attr];
        } elseif (isset($container->$attr)) {
            return $container->$attr;
        }

        return NULL;
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
        return Location::realpathFromNamespace($target);
    }

    /**
     * retorna o diretorio da classe SIALAbstract
     * @return string
     * */
    public function SIALDocs ()
    {
        return dirname(__FILE__);
    }

    /**
     * {@inheritdoc}
     *
     * @access public
     * @return string
     * */
    public function __toString ()
    {
        return get_class($this);
    }
}