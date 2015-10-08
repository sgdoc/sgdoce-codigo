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

namespace br\gov\sial\core\persist\ldap;
use br\gov\sial\core\util\ErrorHandler,
    br\gov\sial\core\persist\ldap\Config,
    br\gov\sial\core\persist\PersistConfig,
    br\gov\sial\core\persist\ldap\ResultSet,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\Connect as ParentConnect,
    br\gov\sial\core\persist\exception\PersistException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage ldap
 * @name Connect
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class Connect extends ParentConnect
{
    /**
     * @var string
     * */
    const CONNECT_OPERATION_FAIL = 'Falha na operação: %s';

    /**
     * @var string
     * */
    const CONNECT_LDAP_KEY_REQUIRED = 'KeyLdap não foi setada';

    /**
     * @var string
     * */
    const CONNECT_MANDATORY_AUTH = 'Autenticação requerida';

    /**
     * @var string
     * */
    const CONNECT_TRANSACTION_UNSUPPORTED = 'Esse adapter não tem suporte a transactions';

    /**
     *
     * Armazena o filtro de pesquisa para o LDAP
     * @var string
     * */
    private $_ldapFilter;

    /**
     *
     * Armazena a parâmetros para consulta do LDAP
     * @var string[]
     * */
    private $_ldapParam;

    /**
     *
     * Armazena a autenticação na consulta do LDAP
     * @var string[]
     * */
    private $_ldapAuth;

    /**
     * @var Config
     * */
    private $_config;

    /**
     * @var string
     * */
    private $_directory;

    /**
     * @var string
     * */
    private $_sourceDn;

    /**
     * @var string
     * */
    private $_ldapKey;

    /**
     * @var string
     * */
    private $_ldapMethod;

    /**
     * Construtor.
     *
     * @param PersistConfig $config
     * @throws PersistException
     * */
    public function __construct (Config $config)
    {
        parent::__construct($config);

        $this->_directory = $config->get('directory');
        $this->_sourceDn  = $config->get('source');
    }

    /**
     * Método que efetua a conexão com o serivdor Ldap.
     *
     * @param persistConfig $config
     * @throws PersistException
     * @see Parent::_connect()
     * */

    protected function _connect(PersistConfig $config)
    {
        try {
            $resource = ldap_connect($config->getDSN());

            $this->_config = $config;

            # Seto parametros de acordo com a versÃ£o do LDAP

            ldap_set_option($resource, LDAP_OPT_PROTOCOL_VERSION, $config->get('version'));

            # Seta se as referÃªncias segue a biblioteca LDAP e retornadas por servidores LDAP.
            ldap_set_option($resource, LDAP_OPT_REFERRALS, 0);

            return $resource;

        } catch (\Exception $exc) {
            throw new PersistException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * {@inheritdoc}, tais como: Delete, Update, Insert, Create, etc.
     *
     * @return ResultSet
     * @throws PersistException
     * */
    public function update ()
    {
        $updateFunction = $this->_ldapMethod;
        $this->$updateFunction();
    }

    /**
     * Efetua o bind para o super usuario
     *
     * @throws PersistException
     * */
    private function _adminAuthenticate ()
    {
        $username = $this->_config->get('username');
        $password = $this->_config->get('password');

        $erro = new ErrorHandler;
        $erro->setError();
        $this->_ldapAuth = ldap_bind($this->_resource, $username, $password);
        if (!$this->_ldapAuth) {
            $username = "{$this->_ldapKey}={$username}";
            $username = $this->_locateDn($username);
            $this->_ldapAuth = ldap_bind($this->_resource, $username, $password);
        }
        restore_error_handler();

        PersistException::throwsExceptionIfParamIsNull($this->_ldapAuth,
            sprintf(self::CONNECT_OPERATION_FAIL, $erro->getErroStr()));
    }

    /**
     * Adiciona usuários no LDAP
     *
     * @throws PersistException
     * */
    private function _addInLdap ()
    {

        # Agora que o Super Admin está autenticado efetuo a adicao do novo usuario
        # E tenho a OBRIGATORIEDADE de setar no VO a key do Ldap
        $arrAdd = array();

        if (NULL != $this->_directory) {
            $directory = "{$this->_directory},";
            $arrAdd['objectClass'][0] = "inetOrgPerson";
            $arrAdd['objectClass'][1] = "posixAccount";
            $arrAdd['objectClass'][2] = "SambaSamAccount";
        } else {
            $directory = '';
            $arrAdd['objectClass'][0] = "user";
            $arrAdd['objectClass'][1] = "person";
            $arrAdd['objectClass'][2] = "organizationalPerson";
        }

        $dnAdd = $this->_ldapFilter['dn'] . $directory . $this->_sourceDn;

        unset($this->_ldapFilter['dn']);
        $this->_ldapFilter = array_merge($this->_ldapFilter, $arrAdd);

        # Autentico o Admin para efetuar operacao
        $this->_adminAuthenticate();

        $erro = new ErrorHandler;
        $erro->setError();

        if(!ldap_add($this->_resource, $dnAdd, array_filter($this->_ldapFilter))) {
            throw new PersistException(sprintf(self::CONNECT_OPERATION_FAIL, $erro->getErroStr()));
        }

        restore_error_handler();
    }

    /**
     * Prepara os parâmetros para o CRUD
     *
     * @param mixed[] $params
     * @throws PersistException
     * @return string DomainName
     * */
    private function _prepareCrud (&$params)
    {
        if (isset($params['keyLdap'])) {
            $keyLdap = $params['keyLdap'];
            unset($params['keyLdap']);
            $query = $keyLdap .'='. $params[$keyLdap];
            $dnAdd  = $this->_locateDn($query);
            unset($params[$keyLdap]);
        } else {
            throw new PersistException(self::CONNECT_LDAP_KEY_REQUIRED);
        }
        return $dnAdd;
    }

    /**
     * Modifica usuários no LDAP
     *
     * @throws PersistException
     * */
    private function _updateInLdap ()
    {
        # Autentico o Admin para efetuar operacao
        $this->_adminAuthenticate();

        $dnUpdate = $this->_renameInLdap();

        unset($this->_ldapFilter['dn']);
        unset($this->_ldapFilter['cn']);

        $error = new ErrorHandler;
        $error->setError();
        if(!ldap_modify($this->_resource, $dnUpdate, array_filter($this->_ldapFilter))) {
            throw new PersistException(sprintf(self::CONNECT_OPERATION_FAIL, $error->getErroStr()));
        }
        restore_error_handler();
    }

    /**
     * Move ou renomea um registro no LDAP
     *
     * @throws PersistException
     * */
    private function _renameInLdap ()
    {
        # Autentico o Admin para efetuar operacao
        $this->_adminAuthenticate();

        # Agora que o Super Admin está autenticado efetuo a adicao do novo usuario
        # E tenho a OBRIGATORIEDADE de setar no VO a key do Ldap
        $userSearch = "{$this->_ldapKey}={$this->_ldapFilter[$this->_ldapKey]}";
        $dnLdap     = $this->_locateDn($userSearch);

        $cn = $this->_ldapFilter['cn'];
        $dn = $this->_ldapFilter['dn'];
        if (empty($dn) || empty($cn)) {
            return $dnLdap;
        }

        $newRdn    = "CN={$cn}";
        $newParent = str_replace("{$newRdn},", "", $dn) . $this->_source;
        $newDnLdap = "{$newRdn},{$newParent}";

        if (strtoupper($newDnLdap) == strtoupper($dnLdap)) {
            return $dnLdap;
        }

        $error = new ErrorHandler;
        $error->setError();
        if($newRdn && !ldap_rename($this->_resource, $dnLdap, $newRdn, $newParent, true)) {
            throw new PersistException(sprintf(self::CONNECT_OPERATION_FAIL, $error->getErroStr()));
        }
        restore_error_handler();

        return $newDnLdap;

    }

    private function _locateNewDn()
    {
        $newDn  = $this->_ldapFilter['dn'];
        $newRdn = "CN={$this->_ldapFilter['cn']}";
        return str_replace("{$newRdn},", "", $newDn) . $this->_source;
    }

    /**
    * Remove usuários no LDAP
     *
     * @throws PersistException
    * */
    private function _delInLdap ()
    {
        # Autentico o Admin para efetuar operacao
        $this->_adminAuthenticate();

        $arrDel = $this->_ldapParam;

        if (isset($arrDel['keyLdap'])) {
            $keyLdap = $arrDel['keyLdap'];
            $dnDel = $keyLdap . '=' . $arrDel[$keyLdap] .',' .
                                 $this->_config->get('directory').','.
                                 $this->_config->get('source');
        }

        $erro = new ErrorHandler;
        $erro->setError();

        if(!ldap_delete($this->_resource, $dnDel)) {
            throw new PersistException(sprintf(self::CONNECT_OPERATION_FAIL, $erro->getErroStr()));
        }

        restore_error_handler();
    }

    /**
     * {@inheritdoc}
     *
     * @throws PersistException
     * @return ResultSet
     * */
    public function retrieve ()
    {
        $this->_adminAuthenticate();

        PersistException::throwsExceptionIfParamIsNull($this->_ldapAuth, self::CONNECT_MANDATORY_AUTH);

        $tmpSource = $this->_hasDirectory() ? "{$this->_directory},{$this->_source}" : $this->_source;

        return new ResultSet($this, ldap_search($this->_resource, $tmpSource, $this->_getFilter(), $this->_ldapParam));
    }

    /**
     * Retorna os parâmetros passados para o LDAP
     *
     * @return string[]
     * */
    public function getParams ()
    {
        return $this->_ldapParam;
    }

    private function _getFilter()
    {
        if (isset($this->_ldapFilter[$this->_ldapKey]) && !empty($this->_ldapFilter[$this->_ldapKey])){
            return "{$this->_ldapKey}={$this->_ldapFilter[$this->_ldapKey]}";
        }

        $userSearch = "(&";
            foreach ($this->_ldapFilter as $key => $value) {
                if (!empty($value)) {
                $userSearch .= "({$key}={$value}*)";
            }
        }
        $userSearch .= ")";

        return $userSearch;
    }

    /**
     * Realiza Bind.
     *
     * @param string[] $query
     * @return \br\gov\sial\core\persist\ldap\Connect
     * @throws PersistException
     */
    private function _bind ()
    {

        $userSearch = "{$this->_ldapKey}={$this->_ldapFilter[$this->_ldapKey]}";
        $dnUser     = $this->_locateDn($userSearch);
        $password   = isset($this->_ldapFilter['userPassword']) ? $this->_ldapFilter['userPassword']
                                                                : $this->_ldapFilter['password'];

        $erro = new ErrorHandler;
        $erro->setError();
        $this->_ldapAuth = ldap_bind($this->_resource, $dnUser, $password);
        if (!$this->_ldapAuth) {
            throw new PersistException(sprintf(self::CONNECT_OPERATION_FAIL, $erro->getErroStr()));
        }
        restore_error_handler();

        return $this;
    }

    /**
     * Efetua busca na arvore do LDAP e retorna o DN
     *
     * @param string $query - Objeto a ser pesquisado Ex.: uid=xxxxxxxxxxx
     * @return string DomainName
     * */
    private function _locateDn ($query)
    {
        $locDn = $this->_hasDirectory() ? "{$this->_directory},{$this->_source}" : $this->_sourceDn;
        $error = new ErrorHandler;
        $error->setError();
        $result = $this->_locateDnInner($query, $locDn);
        if(!$result) {
            $this->_adminAuthenticate();
            $result = $this->_locateDnInner($query, $locDn);
        }
        restore_error_handler();
        return $result['count'] ? $result[0]['dn'] : NULL;
    }

    /**
     * Metodo auxiliar para o _locateDn
     *
     * @param string $query - Objeto a ser pesquisado Ex.: uid=xxxxxxxxxxx
     * @param string $locDn - Objeto a ser pesquisado Ex.: dc=xxxx,dc=xxx
     * @return mixed[]
     *
     * */
    private function _locateDnInner ($query, $locDn)
    {
        return ldap_get_entries($this->_resource, ldap_search($this->_resource, $locDn, $query,  array('*')));
    }

    /**
     * Prepara o comando que será executado no repositório.
     *
     * @param [string|string[] ]$query
     * @param stdClass[] $params
     * @return Connect
     * */
    public function prepare (ValueObjectAbstract $voQuery, $params)
    {
        $attrs = (array) $voQuery->annotation()->load()->attrs;

        $sial_queryFields = array();
        foreach ($attrs as $field) {
            if (($attr = self::getIfDefined($field, 'ldap'))) {
                $this->_ldapParam[] = $field->ldap;
                $getMethod = $field->get;
                $this->_ldapFilter[$field->ldap] = $voQuery->$getMethod();
            }

            if (($attr = self::getIfDefined($field, 'keyLdap'))) {
                $this->_ldapKey = $field->keyLdap;
            }
        }

        $this->_ldapMethod = $params;

        return $this;
    }

    /**
     * {@inheritdoc}
     * */
    public function hasTransactionRunning ()
    {
        return FALSE;
    }

    /**
     * Inicializa transação.
     *
     * @return Persist
     * @throws PersistException
     * */
    public function transaction ()
    {
        throw new PersistException(self::CONNECT_TRANSACTION_UNSUPPORTED);
    }

     /**
     * Grava todos os dados pendentes e finaliza transação em curso.
     *
     * @return Persist
     * @throws PersistException
     * */
    public function commit ()
    {
        throw new PersistException(self::CONNECT_TRANSACTION_UNSUPPORTED);
    }

    /**
     * Descarta toda as operações pendentes e desfaz a transação.
     *
     * @return Persist
     * @throws PersistException
     * */
    public function rollback ()
    {
        throw new PersistException(self::CONNECT_TRANSACTION_UNSUPPORTED);
    }

    /**
     * Fábrica de conectores para ldap.
     *
     * @param PersistConfig $config
     * @return Connect
     * @throws PersistException
     * */
    public static function factory (PersistConfig $config)
    {
        $namespace  = __NAMESPACE__ . self::NAMESPACE_SEPARATOR . 'Connect';
        return new $namespace($config);
    }

    /**
     * Retorna a conexão.
     *
     * @return resource
     * */
    public function getResource ()
    {
        return $this->_resource;
    }

    private function _hasDirectory ()
    {
        return (boolean) $this->_directory;
    }
}