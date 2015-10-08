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
/**
 * CSRF
 */
class Core_Secure_Token implements Zend_Validate_Interface
{
    /**
     * Actual hash used.
     *
     * @var mixed
     */
    protected $_hash;

    /**
     * @var string
     */
    protected $_identifier;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * TTL for CSRF token
     * @var int
     */
    protected $_timeout = 300;

    /**
     * Constructor
     *
     * Creates session namespace for CSRF token, and adds validator for CSRF
     * token.
     *
     * @return void
     */
    public function __construct($identifier = null, $timeout = null)
    {
        if (null !== $identifier) {
            $this->setIdentifier($identifier);
        }

        if (null !== $timeout) {
             $this->setTimeout($timeout);
        }
    }

    /**
     * Set session object
     *
     * @param  Zend_Session_Namespace $session
     * @return Core_Secure_Token
     */
    public function setSession(Zend_Session_Namespace $session)
    {
        $this->_session = $session;
        return $this;
    }

    /**
     * Get session object
     *
     * Instantiate session object if none currently exists
     *
     * @return Zend_Session_Namespace
     */
    public function getSession()
    {
        if (null === $this->_session) {
            $this->_session = new Zend_Session_Namespace($this->getSessionName());
        }
        return $this->_session;
    }

    /**
     * @return Zend_Validate_Identical
     */
    protected function _getTokenValidator()
    {
        $session = $this->getSession();
        $previousHash = null;
        if (isset($session->token)) {
            $previousHash = $session->token;
        }

        return new Zend_Validate_Identical($previousHash);
    }

    /**
     * Salt for CSRF token
     *
     * @param  string $identifier
     * @return Zend_Form_Element_Hash
     */
    public function setIdentifier($identifier)
    {
        $this->_identifier = (string) $identifier;
        return $this;
    }

    /**
     * Retrieve salt for CSRF token
     *
     * @return string
     */
    public function getIdentifier()
    {
        if (null === $this->_identifier) {
            throw new RuntimeException('É necessário atribuir o identificador.');
        }
        return $this->_identifier;
    }

    /**
     * Retrieve CSRF token
     *
     * If no CSRF token currently exists, generates one.
     *
     * @return string
     */
    public function getToken()
    {
        if (null === $this->_hash) {
            $this->_generateToken();
            $this->_initTokenSession();
        }
        return $this->_hash;
    }

    /**
     * Get session namespace for CSRF token
     *
     * Generates a session namespace based on salt, element name, and class.
     *
     * @return string
     */
    public function getSessionName()
    {
        return __CLASS__ . '_' . $this->getIdentifier();
    }

    /**
     * Set timeout for CSRF session token
     *
     * @param  int $ttl
     * @return Zend_Form_Element_Hash
     */
    public function setTimeout($ttl)
    {
        $this->_timeout = (int) $ttl;
        return $this;
    }

    /**
     * Get CSRF session token timeout
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->_timeout;
    }

    /**
     * Initialize CSRF token in session
     *
     * @return void
     */
    protected function _initTokenSession()
    {
        $session = $this->getSession();
        $session->setExpirationHops(1, null, true);
        $session->setExpirationSeconds($this->getTimeout());
        $session->token = $this->getToken();
    }

    /**
     * Generate CSRF token
     *
     * Generates CSRF token and stores both in {@link $_hash} and element
     * value.
     *
     * @return void
     */
    protected function _generateToken()
    {
        $this->_hash = md5(
            mt_rand(1,1000000)
            .  $this->getIdentifier()
            .  mt_rand(1,1000000)
        );
    }

    /**
     * @param  string $value
     * @return bool
     */
    public function isValid($value)
    {
        return $this->_getTokenValidator()->isValid($value);
    }

    /**
     * @return null
     */
    public function getMessages()
    {
        return null;
    }
}
