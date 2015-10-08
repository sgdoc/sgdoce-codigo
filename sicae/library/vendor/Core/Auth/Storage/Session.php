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

/**
 * @category   Core
 * @package    Core_Auth
 * @subpackage Storage
 */
class Core_Auth_Storage_Session implements Zend_Auth_Storage_Interface
{
    const NAMESPACE_DEFAULT = 'USER';

    /**
     * Object to proxy $_SESSION storage
     *
     * @var Zend_Session_Abstract
     */
    protected $_session;

    /**
     * Returns the session namespace
     *
     * @return string
     */
    protected function _getNamespace()
    {
        return $this->getSession()->getNamespace();
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !Core_Session::namespaceIsset($this->_getNamespace());
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return mixed
     */
    public function read()
    {
        return Core_Session::namespaceGet($this->_getNamespace());
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @param  mixed $contents
     * @return void
     */
    public function write($contents)
    {
        if (!is_array($contents)) {
            $contents = array($contents => $contents);
        }

        foreach ($contents as $key => $value) {
            $key = (string)$key;
            $this->getSession()->{$key} = $value;
        }
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return void
     */
    public function clear()
    {
        return Core_Session::namespaceUnset($this->_getNamespace());
    }

    public function setSession(Zend_Session_Namespace $session)
    {
        $this->_session  = $session;
    }

    public function getSession()
    {
        if (NULL === $this->_session) {
            $this->setSession(new Core_Session_Namespace(static::NAMESPACE_DEFAULT, FALSE, TRUE));
        }

        return $this->_session;
    }
}
