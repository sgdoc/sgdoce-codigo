<?php
/*
 * Copyright 2012 ICMBio
* Este arquivo é parte do programa SISICMBio
* O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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

class Core_Session_Namespace extends Zend_Session_Namespace
{
    protected $_persistObject = true;

    /**
     * __construct() - Returns an instance object bound to a particular, isolated section
     * of the session, identified by $namespace name (defaulting to 'Default').
     * The optional argument $singleInstance will prevent construction of additional
     * instance objects acting as accessors to this $namespace.
     *
     * @param string $namespace       - programmatic name of the requested namespace
     * @param bool $singleInstance    - prevent creation of additional accessor instance objects for this namespace
     * @return void
     */
    public function __construct($namespace = 'Default', $singleInstance = false)
    {
        $persistObject = NULL;

        if (func_num_args() > 2) {
            $persistObject = func_get_arg(2);
        }

        if (NULL !== $persistObject) {
            $this->_persistObject = true;
        }

        parent::__construct($namespace, $singleInstance);
    }


    /**
     * @todo
     * getIterator() - return an iteratable object for use in foreach and the like,
     * this completes the IteratorAggregate interface
     *
     * @return ArrayObject - iteratable container of the namespace contents
     */
    public function getIterator()
    {
        return new ArrayObject(parent::_namespaceGetAll($this->_namespace));
    }

    /**
     * __get() - method to get a variable in this object's current namespace
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @return mixed
     */
    public function & __get($name)
    {
        if ($this->_persistObject) {

            $data = parent::_namespaceGet($this->_namespace);

            if (is_object($data)) {
                $method = 'get' . $name;
                if (method_exists($data, $method)) {
                    return $data->$method();
                } else if (isset($data->$name)) {
                    return $data->$name;
                }
            }
        }

        return parent::__get($name);
    }

    /**
     * @see Zend_Session_Namespace::applySet()
     */
    public function __set($name, $value)
    {
        if ($this->_persistObject) {

            if (isset(self::$_namespaceLocks[$this->_namespace])) {
                /**
                 * @see Zend_Session_Exception
                 */
                require_once 'Zend/Session/Exception.php';
                throw new Zend_Session_Exception('This session/namespace has been marked as read-only.');
            }

            if ($name === '') {
                /**
                 * @see Zend_Session_Exception
                 */
                require_once 'Zend/Session/Exception.php';
                throw new Zend_Session_Exception("The '$name' key must be a non-empty string");
            }

            if (parent::$_writable === false) {
                /**
                 * @see Zend_Session_Exception
                 */
                require_once 'Zend/Session/Exception.php';
                throw new Zend_Session_Exception(parent::_THROW_NOT_WRITABLE_MSG);
            }

            if (!isset($_SESSION[$this->_namespace])) {
                $_SESSION[$this->_namespace] = new stdClass();
            }

            if (!is_object($_SESSION[$this->_namespace])) {
                $_SESSION[$this->_namespace] = new stdClass();
            }

            $_SESSION[$this->_namespace]->$name = $value;
            return;
        }

        parent::__set($name, $value);
    }

    /**
     * @see Zend_Session_Namespace::applySet()
     */
    public function applySet($callback)
    {
        if ($this->_persistObject) {
            $argList    = func_get_args();
            $argList[0] = $_SESSION[$this->_namespace];
            $result     = call_user_func_array($callback, $argList);
            if (!is_object($result)) {
                /**
                 * @see Zend_Session_Exception
                 */
                require_once 'Zend/Session/Exception.php';
                throw new Zend_Session_Exception('Result must be an array. Got: ' . gettype($result));
            }

            $_SESSION[$this->_namespace] = $result;
            return $result;
        }

        return parent::applySet($callback);
    }

    /**
     * __isset() - determine if a variable in this object's namespace is set
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @return bool
     */
    public function __isset($name)
    {
        if ($this->_persistObject) {
            $data = parent::_namespaceGet($this->_namespace);

            if (is_object($data)) {
                return isset($data->$name);
            }

            return FALSE;
        }

        return parent::__isset($name);
    }


    /**
     * __unset() - unset a variable in this object's namespace.
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @return true
     */
    public function __unset($name)
    {
        if ($this->_persistObject) {
            $data = parent::_namespaceGet($this->_namespace);

            if (is_object($data)) {
                if (isset($data->$name)) {
                    return $data->$name;
                }
            }
        }

        return parent::__unset($name);
    }
}
