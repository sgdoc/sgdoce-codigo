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
namespace br\gov\sial\core\auth;
use br\gov\sial\core\util\Session,
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\Registry,
    br\gov\sial\core\valueObject\ValueObjectAbstract;

/**
 * Classe de armazenagem autenticação.
 *
 * @package br.gov.sial.core
 * @subpackage auth
 * @name AuthStorage
 * @author Cleiton Coimbra <cleiton.coimbra@gmail.com>
 * */
class AuthStorage extends SIALAbstract implements AuthStorable
{
    /**
     * Nome da chave para armazenar dos dados do usuário autenticado.
     *
     * @var string
     */
    const KEY_NAME_USER_AUTHENTICATE = 'user';

    /**
     * Nome do namespace da sessão para armazenagem dos dados de autenticação.
     *
     * @var string
     */
    const NAMESPACE_SESSION = 'security';

    /**
     * Chave do config.ini que armazena o tempo de expiração da sessão.
     *
     * @var string
     */
    const SESSION_EXPIRE = 'app.session.expire';

    /**
     * Armazena Auth.
     *
     * @param ValueObjectAbstract
     * */
    public static function setStorage (ValueObjectAbstract $valueObject)
    {
        self::_getSession()->set(self::KEY_NAME_USER_AUTHENTICATE, $valueObject);
    }

    /**
     * Recupera Auth.
     *
     * @return ValueObjectAbstract
     * */
    public static function getStorage ()
    {
        return self::_getSession()->get(self::KEY_NAME_USER_AUTHENTICATE);
    }

    /**
     * Obtém uma instância do objeto session para manipulação dos dados de autenticação.
     *
     * @return br\gov\sial\core\util\Session
     */
    private static function _getSession ()
    {
        $sessionExpire = Registry::get('bootstrap')->config(self::SESSION_EXPIRE);
        return Session::start(self::NAMESPACE_SESSION, $sessionExpire);
    }
}