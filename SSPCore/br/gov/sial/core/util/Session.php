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
use br\gov\sial\core\SIALAbstract;
/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @name Session
 * @author J. Augusto <augustowebd@gmail.com>
 * */
final class Session extends SIALAbstract
{
    /**
     * @var Session
     * */
    private static $_instance = NULL;

    /**
     * chave de acesso a sessao
     *
     * @var string
     * */
    public $_ssKey = NULL;

    /**
     * armazena namespace para uso na regeracao de chave
     *
     * @var string
     * */
    private $_namespace;

    /**
     * construtor
     *
     * @param string $key
     * @param string $namespace
     * @param integer $lifeTime
     * @param boolean $security
     * @param boolean $httpOnly
     * */
    private function __construct ($key, $namespace = NULL, $lifeTime = NULL, $security = FALSE, $httpOnly = TRUE)
    {
        $this->_ssKey     = $key;
        $this->_namespace = self::ensureNamespace ($namespace);

        if (!isset($_SESSION)) {
            session_start();
        }

        session_set_cookie_params($lifeTime, NULL, $namespace, $security, $httpOnly);
    }

    /**
     * @deprecated
     * */
    public function del ($key)
    {
        return $this->delete($key);
    }

    /**
     * Recupera um objeto de uma sessão já existente
     * onde o primeiro parametro é o nome da sessão e o segundo o elemento que deseja retornar
     * @param string $name
     * @param string $elemnt
     * @return mixed
     */
    public function getLiveSession ($name, $elemnt = NULL)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        session_name($name);

        return $elemnt && isset($_SESSION[$elemnt]) ? $_SESSION[$elemnt] : NULL;
    }

    /**
     * retorna o identificador da sessao
     *
     * @return string
     * */
    public function getId()
    {
        return $this->_ssKey;
    }

    /**
     * registra conteudo na sessao. Para o armazenamento de objeto devera' ser tomado o cuidado de serialziar
     * antes de armazenar devendo tomar os cuidados necessarios na deserializacao
     *
     * @param string $key
     * @param mixed $value
     * @return Session
     * */
    public function set ($key, $value)
    {
        $_SESSION[$this->_ssKey][$key] = $value;
        return $this;
    }

    /**
     * recupera um conteudo previamente registrado
     *
     * @param string $key
     * @return mixed
     * */
    public function get ($key)
    {
        if (isset($_SESSION[$this->_ssKey][$key])) {
            return $_SESSION[$this->_ssKey][$key];
        }
        return NULL;
    }

    /**
     * remove uma propriedade da session
     *
     * @param string $key
     * @return Session
     * */
    public function delete ($key)
    {
        if (isset($_SESSION[$this->_ssKey][$key])) {
            unset($_SESSION[$this->_ssKey][$key]);
        };
        return $this;
    }

    /**
     * forca a geracao de um novo identificador para sessao
     *
     * @return Session
     * */
    public function regenerationIdent ()
    {
        $actualData = isset($_SESSION[$this->_ssKey]) ? $_SESSION[$this->_ssKey] : array();
        session_regenerate_id(TRUE);
        $this->_ssKey = session_id();
        $_SESSION[$this->_ssKey] = $actualData;
        return $this;
    }

    /**
     * destroi a sessao
     * */
    public function destroy ()
    {
        unset($_SESSION[$this->_ssKey]);
        $this->regenerationIdent();
        $this->_ssKey = NULL;
    }

    /**
     * gerador de id
     *
     * @param string $namespace
     * @return string
     * */
    private static function generateId ($namespace)
    {
        return md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $namespace);
    }

    /**
     * garantir namespace
     *
     * @param string
     * @return string
     * */
    private static function ensureNamespace ($namespace)
    {
        return empty($namespace) ? md5(time()) : $namespace;
    }

    /**
     * cria session para o namespace informado
     *
     * <ul>
     * 	<li><b>$namespace</b> define o namespace de acesso aos dados da sessao criada</li>
     * 	<li><b>$life</b> define o tempo de vida da sessão. O valor padrão é de uma hora (3600 segundos)</li>
     * 	<li><b>$security</b> se definido diferente de FALSE define que a sessão só será válida em ambiente seguro, HTTPS</li>
     * 	<li><b>$httpOnly</b> se avaliado como TRUE define que a sessão só será válida em ambiente WEB</li>
     * </ul>
     *
     * @param string $namespace
     * @param integer $lifeTime
     * @param boolean $security
     * @param boolean $httpOnly
     * @return Session
     * */
    public static function start ($namespace = NULL, $lifeTime = 3600, $security = FALSE, $httpOnly = TRUE)
    {
        $key = self::generateId($namespace);
        if (!isset(self::$_instance[$key])) {
            self::$_instance[$key] = new self($key, $namespace, $lifeTime, $security, $httpOnly);
        }
        return self::$_instance[$key];
    }
}