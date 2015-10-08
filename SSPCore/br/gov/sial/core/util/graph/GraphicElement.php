<?php
/*
 * Copyright 2012 ICMBio
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
namespace br\gov\sial\core\util\graph;
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR .
                        'lib' . DIRECTORY_SEPARATOR . 'jpgraph'
                              . DIRECTORY_SEPARATOR . 'Init.php';
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * Utilitário de Geração de Graficos
 *
 * @package br.gov.imcbio.sial.util
 * @subpackage graph
 * @name GraphicElement
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
abstract class GraphicElement extends SIALAbstract
{
    /**
     * @var string
     */
    const BARGRAPH   = 'bargraph';

    /**
     * @var string
     */
    const LINEGRAPH  = 'linegraph';

    /**
     * Tipos de Graficos aceitos
     * @var string[]
     */
    private static $_graphAllowed = array(
                                    "bargraph"  => "BarElement",
                                    "linegraph" => "LineElement"
                                  );

    /**
     * Elementos de Grafico
     * @var objeto
     */
    private static $_graphEl      = NULL;

    /**
     * Valida os tipos de graficos aceitos
     * @param string $graph
     * @return boolean
     */
    private function _validateGraph ($graph)
    {
        return array_key_exists($graph, self::$_graphAllowed);
    }

    /**
     * (non-PHPdoc)
     * @see br\gov\sial\core\SIALAbstract::__call()
     */
    public function __call($name, array $arguments = array())
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull(self::$_graphEl->hasMethod($name),
                                                               "Método '<i>{$name}</i>' não existe");
        // @codeCoverageIgnoreStart
        self::$_graphEl->$name($arguments);
        return $this;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Fabrica de Elementos
     * @param string $graphType
     * @param array $data
     * @return GraphicElement
     */
    public static function factory ($graphType, $data)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull(self::_validateGraph($graphType),
                                                               "Tipo de Gráfico não suportado");

        $class = __NAMESPACE__ . self::NAMESPACE_SEPARATOR . 'elements' .
                                 self::NAMESPACE_SEPARATOR . self::$_graphAllowed[$graphType];

        self::$_graphEl = new $class($data);
        return self::$_graphEl;
    }
}