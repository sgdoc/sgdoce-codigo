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
namespace br\gov\sial\core\mvcb\view\helper;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\Request,
    br\gov\sial\core\mvcb\view\exception\HelperException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.mvcb.view
 * @subpackage helper
 * @name Helper
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class BuildUrl extends SIALAbstract
{
    /**
     * Porta padrão para montagem do link.
     *
     * @var integer
     * */
    public static $port = 80;

    /**
     * Lista de protocolos aceitos.
     *
     * @var mixed[]
     * */
    public static $portList = array(
         80 => 'http://',
         21 => 'ftp://',
        443 => 'https://'
    );

    /**
     * Gera url.
     *
     * @param string[] $urlParam
     * @param boolean $appendParam
     * @param string $url
     * @param boolean $friendlyMode
     * @return string
     * */
    public function buildUrl (array $urlParam, $appendParam = TRUE, $url = NULL, $friendlyMode = TRUE)
    {
        # monta base da url
        if (NULL === $url) {
            $urlQuery = self::getDomain($_SERVER['SERVER_PORT']);
        } else {
            $urlQuery = strpos($url, '://') ? $url : (self::$portList[self::$port]. $url);
        }
        $urlQuery .= self::makeQuery($urlParam, (boolean) $appendParam, (boolean) $friendlyMode, !$url);
        return $urlQuery;
    }

    /**
     * Retorna o domínio.
     *
     * @param integer $port
     * @return string
     * */
    public static function getDomain ($port)
    {
        $port = array_key_exists($port, self::$portList) ? $port : self::$port;
        return (self::$portList[$port] . $_SERVER['HTTP_HOST']);
    }

    /**
     * Montra a query de params.
     *
     * @param string[] $urlParams
     * @param boolean $appendParam
     * @param boolean $friendlyMode
     * @param boolean $externalLink
     * @return string
     * */
    public static function makeQuery (array $urlParams, $appendParam, $friendlyMode, $externalLink)
    {
        if (!$friendlyMode) {
            return '?' . http_build_query($urlParams);
        }

        $request = Request::factory();
        $params  = $request->getParams('get');
        $query   = '';

        # preserva a chamada ao modulo
        if ($externalLink) {
            $query = sprintf('/%s/%s/%s', $request->getModule()
                                        , $request->getFuncionality()
                                        , $request->getAction(TRUE));
        }

        foreach ($urlParams as $key => $value) {
            # previne a duplicacao de chave na url
            if (isset($params[$key]) && TRUE == $appendParam) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }
            $query .= sprintf('/%s/%s', $key, $value);
        }
        return $query;
    }
}