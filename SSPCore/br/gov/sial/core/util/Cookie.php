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
 * @final
 * @name Cookie
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
final class Cookie extends SIALAbstract
{

    /**
     * Construtor de Cookies
     * @param string $cookieName
     * @param mixed $value
     * @param integer $lifeTime
     * @param string|null $path
     * @param string|null $domain
     * @param bolean $security
     * @param boolean $httpOnly
     */
    private function __construct ($cookieName
                                 , $value
                                 , $lifeTime = 0
                                 , $path = NULL
                                 , $domain = NULL
                                 , $security = FALSE
                                 , $httpOnly = TRUE
                                 )
    {
        setcookie($cookieName, $value, $lifeTime, $path, $domain, $security, $httpOnly);
    }

    /**
     * recupera um conteudo previamente registrado
     *
     * @param string $cookieName
     * @return mixed
     * */
    public function get ($cookieName)
    {
        if (isset($_COOKIE[$cookieName])) {
            return $_COOKIE[$cookieName];
        }
        return NULL;
    }

    /**
     * seta um conteudo
     *
     * @param string $cookieName
     * @param mixed  $value
     * @return mixed
     * */
    public function set ($cookieName, $value)
    {
        if (isset($_COOKIE[$cookieName])) {
            $_COOKIE[$cookieName] = $value;
        }
        return NULL;
    }

    /**
     * Fábrica
     * @param string $cookieName
     * @param mixed $value
     * @param integer $lifeTime
     * @param string $path
     * @param string $domain
     * @param bolean $security
     * @param boolean $httpOnly
     * @return Cookie
     */
    public static function factory ($cookieName
                                   , $value
                                   , $lifeTime = 0
                                   , $path = NULL
                                   , $domain = NULL
                                   , $security = FALSE
                                   , $httpOnly = TRUE
                                   )
    {
        return new self($cookieName, $value, $lifeTime, $path, $domain, $security, $httpOnly);
    }
}