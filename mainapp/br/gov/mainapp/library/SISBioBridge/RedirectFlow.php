<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SISBio
 * O SISBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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
 * depende do modulu apache
 *
 * @author J. Augusto <augustowebd@gmail.com>
 * @version 1.0.0
 * @date 2014-09-01
 * @depends Apache::mod_rewrite
 * */
class RedirectFlow
{
    /**
     * token utilizado para a origem da requisicao, se trocar este identificadir
     * antes deve ser atentado para os tokens utilizados na classe do SIAL Request
     *
     * @var char
     * */
    const T_TOKEN_IDENT_FLAG = 'q';

    /**
     * nome do sistema sistema na url
     *
     * @var string
     * */
    const T_REDIRECT_FLOW_SISBIO_NAME = 'sisbio';

    /**
     * @var stirng
     * */
    private $_sourceURL;

    /**
     * @var stirng
     * */
    private $_sourceToken;

    /**
     * @var stirng
     * */
    private $_targetURL;

    /**
     * @var stirng
     * */
    private $_targetToken;

    /**
     * @var stirng
     * */
    private $_paramns;

    /**
     * @var stirng
     * */
    private static $_SCHEMA = array(
        80  => 'http',
        443 => 'https'
    );

    /**
     * @var string $target
     * @var string $source
     * */
    public function __construct ($target, $source = NULL)
    {
        $this->from($source);

        $this->target($target);
    }

    /**
     * define a url origem
     *
     * @param string
     * @return stdClass
     * */
    public function from ($url = NULL)
    {
        $url  = $url ?: $_SERVER['SERVER_NAME'];

        $info = self::_getURLInfo($url);

        $this->_sourceURL   = $info->domain;

        $this->_sourceToken = self::_encodeToken($this->_sourceURL);
    }

    /**
     * define a URL destino
     *
     * @param string $url
     * */
    public function target ($url)
    {
        # se o protocolo destino nao for informado, será
        # usado o mesmo de origem
        $info = self::_getURLInfo($url);

        $this->_paramns = $info->params;

        $this->_targetURL = $info->protocol . '://'
                          . $info->domain;

        $this->_targetToken = self::_encodeToken($info->domain);
    }

    /**
     * se necessario, redireciona a requisicao
     * */
    public function forward ()
    {
        if (!$this->_canGoThere()) {
            return;
        }

        $params = rtrim($this->_paramns, '/');

        $pReplaceToken = sprintf('/\/%s\/[^=]+=/', self::T_TOKEN_IDENT_FLAG);

        $params = preg_replace($pReplaceToken, '', $params)
                . sprintf('/%s/%s', self::T_TOKEN_IDENT_FLAG, $this->_sourceToken);

        $target = $this->_targetURL . $params;

        header('Location: ' . $target, TRUE, 307); die;
    }

    /*
     * avalia se a requisicao pode ser redirecionada
     * */
    private function _canGoThere ()
    {
        # so permite continuar avaliando o redirecionamento se
        # o sistema (modulo do sisicmbio) for o sisbio
        $pattern = sprintf('/\/?(%s(?:fsw)?)/i', self::T_REDIRECT_FLOW_SISBIO_NAME);
        if (!preg_match($pattern, $this->_paramns, $result)) {
            return;
        }

        # verifica se a url possui o marcador de origem, definida pelo token
        $pattern = sprintf('/%s\/(?P<token>[^$]+)$/', self::T_TOKEN_IDENT_FLAG);
        preg_match($pattern, $this->_paramns, $result);

        # se o token ainda nao tiver sido definido significa que a requisicao parte
        # da classe que esta avaliando a possibilidade de redirecionar a requisicao
        if (!isset($result['token'])) {
            return TRUE;
        }

        # outra possibilidade de redirecionar a requisicao
        # eh quando os tokens forem diferentes
        return $result['token'] != $this->_targetToken;
    }

    /*
     * extra as informações da url informada
     *
     * @param string $url
     * @return object
     * */
    private static function _getURLInfo ($url)
    {
        $url = trim($url);

        preg_match(
            '/^(?P<protocol>https?:\/\/)?(?P<domain>[^\/]+)(?P<params>\/[^$]+)?$/',
            $url, $result
        );

        # se nao for 80 ou 443 nao eh suportado
        if (!isset(self::$_SCHEMA[$_SERVER['SERVER_PORT']])) {
            throw new Exception('__INTEGRACAO NAO SUPORTADA__');
        }

        return (object) array(
            # basea o protocolo pelo ambiente, devido ao proxy reverso
            // 'protocol' => $result['protocol'] ?: self::$_SCHEMA[$_SERVER['SERVER_PORT']],
            // 'protocol' => $result['protocol'] ?: self::$_SCHEMA[$_SERVER['SERVER_PORT']],
            'protocol' => (strpos($_SERVER['APPLICATION_ENV'], 'dev') === 0) ? 'http' : 'https',
            'domain'   => $result['domain'],
            'params'   => $_SERVER['REQUEST_URI'],
        );
    }

    /*
     * cifra o token informado
     *
     * @param string $token
     * @return string
     * */
    private static function _encodeToken ($token)
    {
        return base64_encode(md5($token));
    }
}