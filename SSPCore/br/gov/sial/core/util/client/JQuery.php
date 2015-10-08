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
namespace br\gov\sial\core\util\client;
use br\gov\sial\core\Factorable,
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * Config
 *
 * @package br.gov.imcbio.sial.util
 * @subpackage client
 * @name JQuery
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class JQuery extends SIALAbstract implements Factorable, \Serializable
{
    /**
     * @var br\gov\sial\core\util\client\JQuery
     * */
    private static $_instance = NULL;

    /**
     * serializa o elemento (array) informado convertendo-o em uma string, preservando o nome de cada uma das chaves do
     * <br />array e aplicando a funcao urlencode para cada um dos valores
     *
     * @param string[] $element
     * @return string
     * */
    public function serialize ($content)
    {
        $content = (array) $content;
        $tmpSerialized = '';
        foreach ($content as $key => $value) {
            $tmpSerialized .= $key . '=' . urlencode($value) . '&';
        }
        return substr($tmpSerialized, 0, -1);
    }

    /**
     * desserializa a string num array criando uma entrada para cada valor
     *
     * @param string
     * @return string[]
     * */
    public function unserialize ($content)
    {
        $tmpResult = array();
        $content   = (string) $content;
        $content = explode('&', $content);
        foreach ($content as $element) {
            if (NULL == $element) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }
            list($key, $value) = explode('=', $element, 2);
            $tmpResult[$key] = urldecode($value);
        }
        return $tmpResult;
    }

    /**
     * desserializa o conteudo serializado pela metodo jQuery::serializeArray
     *
     * @param string[]
     * @return string[]
     * @throws IllegalArgumentException
     * */
    public function unserializeArray ($content)
    {
        $content = (array) $content;
        # nota de programacao:
        # - neste processo nao eh necesasrio aplicar a funcao urldecode porque neste tipo de serializacao o formulario
        # eh enviado via post de forma similar ao submit normal, assim, no transporte dos dados ja' realizado o decode
        $tmpResult = array();
        foreach ($content as $element) {
            $tmpResult[$element['name']] = $element['value'];
        }
        return $tmpResult;
    }

    /**
     * fábrica de objetos
     * @return \br\gov\sial\core\util\client\JQuery
     * */
    public static function factory ()
    {
        if (NULL == self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
}