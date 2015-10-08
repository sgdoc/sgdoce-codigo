<?php
/**
 * Copyright 2012 do ICMBio
 *
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

class Core_Auth_Chain
{
    /**
     * Singleton instance
     *
     * @var Zend_Auth
     */
    protected static $_instance = NULL;

    protected $_adapters = array();

    protected $_resultInvalid = array();

    /**
     * Returns an instance of Zend_Auth
     *
     * Singleton pattern implementation
     *
     * @return Zend_Auth Provides a fluent interface
     */
    public static function getInstance()
    {
        if (NULL === static::$_instance) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

   /**
     * Singleton pattern implementation makes "new" unavailable
     *
     * @return void
     */
    protected function __construct()
    {}

    /**
     * Singleton pattern implementation makes "clone" unavailable
     *
     * @return void
     */
    protected function __clone()
    {}

    public function addAuth(Zend_Auth_Adapter_Interface $auth, $stackIndex = NULL)
    {
        if (NULL === $stackIndex) {
            $this->_adapters[] = $auth;
            return $this;
        }

        if (!is_int($stackIndex)) {
            throw new InvalidArgumentException('StackIndex deve ser inteiro.');
        }

        $this->_adapters[$stackIndex] = $auth;
        return $this;
    }

    public function authenticate()
    {
        if ($this->hasIdentity()) {
            $this->clearIdentity();
        }

        ksort($this->_adapters);
        $result = NULL;
        foreach ($this->_adapters as $stackIndex => $adapter) {
            $result = $adapter->authenticate();

            if ($result->isValid()) {
                $this->getStorage()->write($result->getIdentity());
            }

            $this->_resultInvalid[get_class($adapter)] = $result;
        }

        return $result;
    }

    public function getResultInvalid($adapterClassName)
    {
        if (isset($this->_resultInvalid[$adapterClassName])) {
            return $this->_resultInvalid[$adapterClassName];
        }

        return null;
    }

    /**
     * Returns the persistent storage handler
     *
     * Session storage is used by default unless a different storage adapter has been set.
     *
     * @return Zend_Auth_Storage_Interface
     */
    public function getStorage()
    {
        if (null === $this->_storage) {
            /**
             * @see Zend_Auth_Storage_Session
             */
            require_once 'Zend/Auth/Storage/Session.php';
            $this->setStorage(new Zend_Auth_Storage_Session());
        }

        return $this->_storage;
    }

    /**
     * Sets the persistent storage handler
     *
     * @param  Zend_Auth_Storage_Interface $storage
     * @return Zend_Auth Provides a fluent interface
     */
    public function setStorage(Zend_Auth_Storage_Interface $storage)
    {
        $this->_storage = $storage;
        return $this;
    }

/**
     * Returns true if and only if an identity is available from storage
     *
     * @return boolean
     */
    public function hasIdentity()
    {
        return !$this->getStorage()->isEmpty();
    }

    /**
     * Returns the identity from storage or null if no identity is available
     *
     * @return mixed|null
     */
    public function getIdentity()
    {
        $storage = $this->getStorage();

        if ($storage->isEmpty()) {
            return null;
        }

        return $storage->read();
    }

    /**
     * Clears the identity from persistent storage
     *
     * @return void
     */
    public function clearIdentity()
    {
        $this->getStorage()->clear();
    }

}
