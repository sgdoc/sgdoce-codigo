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
 * Description of Adapter
 *
 */
class Sica_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
    /**
     * @const integer
     */
    const LDAP_MAX_PWD_LAST_SET_DAYS = 30;

    /**
     * @const string
     */
    const LDAP_CONST_NT_STATUS_PASSWORD_EXPIRED = 'NT_STATUS_PASSWORD_EXPIRED';

    /**
     * @const string
     */
    const LDAP_CONST_CODE_CANT_CONTACT_SERVER = 81;

    /**
     *
     * @var Model_User
     */
    protected $_user;

    /**
     * @var boolean
     */
    protected $_appendData;

    /**
     * @var string
     */
    protected $_identity;

    /**
     * @var password
     */
    protected $_credential;

    /**
     *
     * @var Sica\Model\Repository\Usuario
     */
    protected $_repository;

    /**
     * $_authenticateResultInfo
     *
     * @var array
     */
    protected $_authenticateResultInfo = null;

    /**
     *
     * @var boolean
     */
    protected $_ldap;

    /**
     *
     * @var boolean
     */
    protected $_secondaryHost = FALSE;

    public function __construct($repository, $identity = null, $credential = null)
    {
        $this->_setRepository($repository);

        if (null !== $identity) {
            $this->setIdentity($identity);
        }

        if (null !== $credential) {
            $this->setCredential($credential);
        }
    }

    public function setIdentity($value)
    {
        $this->_identity = $value;
        return $this;
    }

    public function setCredential($credential)
    {
        $this->_credential = $credential;
        return $this;
    }

    public function getCredential()
    {
        return $this->_credential;
    }

    public function getIdentity()
    {
        return $this->_identity;
    }

    public function getLdap()
    {
        if (NULL === $this->_ldap) {
            $config = Zend_Registry::get('configs');
            $this->setLdap($config['authenticate']['ldap']);
        }

        return $this->_ldap;
    }

    public function setLdap($ldap)
    {
        $this->_ldap = $ldap;
        return $this;
    }

    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $ldap = $this->getLdap();

        $retorno = FALSE;

        if ($ldap == TRUE) {
            $this->_user = $this->_repository->authenticate($this->getIdentity());
            if ($this->_authenticateValidateResultSet()) {
                $retorno = $this->autenticateLdap();
            }
        } else {
            $this->_user = $this->_repository->authenticate($this->getIdentity(), $this->getCredential());
            $retorno = $this->_authenticateValidateResultSet();
        }

        if (!$retorno) {
            return $this->result();
        }

        $this->_authenticateResultInfo['code']     = Zend_Auth_Result::SUCCESS;
        $this->_authenticateResultInfo['messages'] = '';

        return $this->result();
    }

    /**
     * Factory for Zend_Auth_Result
     *
     * @param integer    The Result code, see Zend_Auth_Result
     * @param mixed      The Message, can be a string or array
     * @return Zend_Auth_Result
     */
    protected function result()
    {
        $dataUser = array();
        $messages = array($this->_authenticateResultInfo['messages']);

        if ($this->_authenticateResultInfo['code'] > 0) {
            $dataUser = array(
                'sqUsuario'        => $this->_user['sqUsuario'],
                'noUsuario'        => $this->_user['noPessoa'],
                'noPessoa'         => $this->_user['noPessoa'],
                'sqPessoa'         => $this->_user['sqPessoa'],
                'ativo'            => isset($this->_user['stAtivo'])
                                      ? $this->_user['stAtivo']
                                      : $this->_user['stRegistroAtivo'],
                'stRegistroAtivo'  => $this->_user['stRegistroAtivo'],
                'inPerfilExterno'  => $this->_user['inPerfilExterno']
            );

            if ($this->_appendData) {
                 $dataUser += $this->_getDataExtras();
            }
        }

        return new Zend_Auth_Result(
                $this->_authenticateResultInfo['code'],
                $dataUser,
                $messages
        );
    }

    public function appendDataExtras($flag)
    {
        $this->_appendData = (bool) $flag;
        return $this;
    }

    protected function _getDataExtras()
    {
        return array(
            'sqTipoPessoa'     => $this->_user['sqTipoPessoa'],
            'sqEstadoCivil'    => $this->_user['sqEstadoCivil'],
            'noMae'            => $this->_user['noMae'],
            'noPai'            => $this->_user['noPai'],
            'sgSexo'           => $this->_user['sgSexo'],
            'nuCpf'            => $this->_user['nuCpf'],
            'nuCurriculoLates' => $this->_user['nuCurriculoLates'],
            'noProfissao'      => $this->_user['noProfissao'],
            'dtNascimento'     => is_object($this->_user['dtNascimento']) ?
                                            $this->_user['dtNascimento']->get('yyyy-MM-dd HH:mm:ss') :
                                            '',
        );
    }

    protected function autenticateLdap()
    {
        try {
            $container = Core_Registry::getContainers();
            $ldap = $container['ldap']->getPersist();

            $config = \Zend_Registry::get('configs');
            $samAccountNameQuery = "samAccountName={$this->getIdentity()}";

            /**
             * Modifica o host para o servidor secundário.
             */
            if ($this->_secondaryHost &&
                isset($config['resources']['container']['ldap']['host']['secondary'])) {

                $options = $ldap->getOptions();
                $options['host'] = $config['resources']['container']['ldap']['host']['secondary'];
                $ldap = new Zend_Ldap($options);
            }

            $admUsr = $config['authenticate']['username'];
            $admPwd = $config['authenticate']['password'];
            $ldap->bind($admUsr, $admPwd);

            $userLdapCount = $ldap->count($samAccountNameQuery);
            if ($userLdapCount <= 0) {
                throw new \Sica_Auth_Exception('MN175');
            }

            $userLdap = current($ldap->search($samAccountNameQuery)->toArray());
            $pwdLastSetLDAPTimestamp = isset($userLdap['pwdlastset'][0])? $userLdap['pwdlastset'][0] : 0;
            $pwdLastSetLDAPTimestamp_div = bcdiv($pwdLastSetLDAPTimestamp, '10000000');
            $pwdLastSetLDAPTimestamp_sub = bcsub($pwdLastSetLDAPTimestamp_div, '11644473600');
            $pwdLastSetDate = new \Zend_Date($pwdLastSetLDAPTimestamp_sub, \Zend_Date::TIMESTAMP);

            $measureTime = new \Zend_Measure_Time(
                \Zend_Date::now()->sub($pwdLastSetDate)->toValue(),
                \Zend_Measure_Time::SECOND
            );
            $measureTime->convertTo(\Zend_Measure_Time::DAY);
            $daysLeftToChangePwd = ceil($measureTime->getValue());


            if ($daysLeftToChangePwd >= self::LDAP_MAX_PWD_LAST_SET_DAYS) {
                throw new \Sica_Auth_Exception('EXPIRED_PWD_MSG');
            }

            $ldap->bind($this->getIdentity(), $this->getCredential());

            return TRUE;
        } catch(\Sica_Auth_Exception $authExc) {
            $this->_authenticateResultInfo['code']     = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $this->_authenticateResultInfo['messages'] = $authExc->getMessage();
            return false;
        } catch(\Zend_Ldap_Exception $ldapExc) {
            $ldapCode = $ldapExc->getCode();
            $message = sprintf('[SICA-e] LDAP Error in %s: "%s"', __METHOD__, $ldapExc->getMessage());
            error_log( $message );
            $message = sprintf('[Erro no LDAP] %s', $ldapExc->getMessage());

            /**
             * Se não foi possível contactar o servidor LDAP e se não
             * for uma tentativa de autenticação no servidor secundário.
             */
            if ($ldapCode == self::LDAP_CONST_CODE_CANT_CONTACT_SERVER && !$this->_secondaryHost) {
                #Tentativa de autenticação no servidor secundário.
                $this->_secondaryHost = TRUE;
                return $this->autenticateLdap();
            }

            if ($ldapCode > 0) {
                $message = sprintf('LDAP0x%02x', $ldapCode);
            }
            if (false !== strpos($ldapExc->getMessage(), self::LDAP_CONST_NT_STATUS_PASSWORD_EXPIRED)) {
                $message = 'EXPIRED_PWD_MSG';
            }
            $this->_authenticateResultInfo['code']     = Zend_Auth_Result::FAILURE_UNCATEGORIZED;
            $this->_authenticateResultInfo['messages'] = $message;
            return false;
        }
    }

    protected function _setRepository($repository)
    {
        $this->_repository = $repository;
        return $this;
    }

    protected function _authenticateValidateResultSet()
    {
        if (count($this->_user) < 1) {
            $this->_authenticateResultInfo['code']     = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $this->_authenticateResultInfo['messages'] = 'MN079';
            return FALSE;
        } else if (!$this->_user['stAtivo'] || !$this->_user['stRegistroAtivo']) {
            $this->_authenticateResultInfo['code']     = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $this->_authenticateResultInfo['messages'] = 'MN003';
            return FALSE;
        }

        if($this->_ldap == FALSE) {
            if(md5($this->getCredential()) !== $this->_user['txSenha']) {
                $this->_authenticateResultInfo['code']     = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
                $this->_authenticateResultInfo['messages'] = 'MN008';
                return FALSE;
            }
        }

        return TRUE;
    }

}