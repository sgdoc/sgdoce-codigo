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
 * Classe para consumo dos serviços do SICA
 */

/**
 * @package     Core
 * @subpackage  Integration
 * @subpackage  Sica
 * @name        User
 * @category    Integration Client
 */
class Core_Integration_Sica_User extends Core_Integration_Abstract_Client
{
    private static $_userSession = null;
    private static $_systemsSession = null;

    /**
     * Retorna o login do usuário atual
     */
    public static function getUserId()
    {
        $user = static::get();
        if (isset($user->sqUsuario)) {
            return $user->sqUsuario;
        }
    }

    public static function getPersonId()
    {
        $user = static::get();
        if (isset($user->sqPessoa)) {
            return $user->sqPessoa;
        }
    }

    public static function getUserName()
    {
        $user = static::get();
        if (isset($user->noPessoa)) {
            return $user->noPessoa;
        }
    }

    public static function getUserNoProfile()
    {
        $user = static::get();
        if (isset($user->noPerfil)) {
            return $user->noPerfil;
        }
    }

    public static function getUserUnitName()
    {
        $user = static::get();
        if (isset($user->noUnidadeOrg)) {
            return $user->noUnidadeOrg;
        }
    }

    public static function getUserUnit()
    {
        $user = static::get();
        if (isset($user->sqUnidadeOrg)) {
            return $user->sqUnidadeOrg;
        }
    }

    public static function getUserSystem()
    {
        $user = static::get();
        if (isset($user->sqSistema)) {
            return $user->sqSistema;
        }
    }

    public static function getUserProfile()
    {
        $user = static::get();
        if (isset($user->sqPerfil)) {
            return $user->sqPerfil;
        }
    }

    public static function getUserData()
    {

    }

    public static function destroy()
    {
        $session = static::has();
        if ($session){
            Zend_Session::namespaceUnset('USER');
        }
    }

    /**
     * Utilizado pelo Core_View_Helper_SystemMenu
     */
    public static function getUserSystemMenu()
    {
        $session  = static::get();
        if (!isset($session->MenuExterno)) {
            return array();
        }
        return $session->MenuExterno;
    }

    public static function getSgSystemActive()
    {
        return strtoupper(\Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getOption('appname'));
    }

    public static function getUserProfileExternal()
    {
        $user = static::get();
        if (isset($user->inPerfilExterno)) {
            return $user->inPerfilExterno;
        }
    }

    public static function getUserAllProfile()
    {
        $user = static::get();
        if (isset($user->allProfile)) {
            return $user->allProfile;
        }
    }

    public static function has()
    {
        Zend_Session::start(true);
        return Zend_Session::namespaceIsset('USER');
    }

    public static function get()
    {
        if (self::$_userSession === null) {
            return static::has() ? Core_Session::namespaceGet('USER') : new stdClass();
        } else {
            return self::$_userSession;
        }
    }

    public static function set( $userSession = null )
    {
        self::$_userSession = $userSession;
    }

    public static function setSystems( $systemsSession = null )
    {
        self::$_systemsSession = $systemsSession;
    }

    public static function getInfoSystems()
    {
        if (self::$_systemsSession === null) {
            Zend_Session::start(true);
            $keys = Core_Session::getIterator()->getArrayCopy();
            $data = NULL;
            foreach ($keys as $key) {
                $namespace = Core_Session::namespaceGet($key);
                if (is_object($namespace) && isset($namespace->sistemas)) {
                    $data = $namespace->sistemas;
                }

                if (is_array($namespace) && array_key_exists('sistemas', $namespace)) {
                    $data = $namespace['sistemas'];
                }
            }

            if ($data) {
                return $data;
            }

            return array();
        } else {
            return self::$_systemsSession;
        }
    }

    public static function getInfoSystem($system = null)
    {
        if (null === $system) {
            $user = static::get();
            if (!isset($user->sqSistema)) {
                return null;
            }
            $system = $user->sqSistema;
        }

        $systems = static::getInfoSystems();

        if (isset($systems[$system])) {
            return $systems[$system];
        }

        return null;
    }

    public static function getUserCredential()
    {
        $infoSystem = self::getInfoSystem();
        $userCredential = array(
                        'sqUsuario'       => self::getUserId(),
                        'sgSistema'       => $infoSystem['sgSistema'],
                        'inPerfilExterno' => self::getUserProfileExternal()
        );
        return $userCredential;
    }

}