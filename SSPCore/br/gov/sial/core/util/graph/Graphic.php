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
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\lib\jpgraph\Init,
    br\gov\sial\core\util\lib\jpgraph\Graph,
    br\gov\sial\core\util\lib\jpgraph\BarPlot,
    br\gov\sial\core\util\lib\jpgraph\AccBarPlot,
    br\gov\sial\core\util\graph\elements\BarElement,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * Utilitário de Geração de Graficos
 *
 * @package br.gov.imcbio.sial.util
 * @subpackage graph
 * @name Graphic
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class Graphic extends SIALAbstract
{
    /**
     * Quantidades de parametros aceitos para margem
     * @var integer
     */
    const MARGINPARAMS = 4;

    /**
     * Constantes de Escalas aceitas
     * @var string
     */
    const INTLIN       = 'intlin';

    /**
     * Constantes de Escalas aceitas
     * @var string
     */
    const TEXTINT      = 'textint';

    /**
     * Constantes de Escalas aceitas
     * @var string
     */
    const LOGLOG       = 'loglog';

    /**
     * Constantes de Escalas aceitas
     * @var string
     */
    const LINLOG       = 'linlog';

    /**
     * Constantes de Escalas aceitas
     * @var string
     */
    const LINLIN       = 'linlin';

    /**
     * @var objeto
     */
    private $_graph = NULL;


    /**
     * @var objeto
     */
    private $_accumulate = NULL;


    public function __construct($sizex, $sizey)
    {
        $this->_graph = new Graph($sizex, $sizey);
    }

    /**
     * (non-PHPdoc)
     * @see br\gov\sial\core\SIALAbstract::__call()
     */
    public function __call($name, array $arguments = array())
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull($this->hasMethod($name),
                                                               "Método '<i>{$name}</i>' não existe");
        // @codeCoverageIgnoreStart
        $this->$name($arguments);
        return $this;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Adiciona o Plot ao Container do Grafico
     * @param unknown_type $plot
     */
    public function insert ($plot)
    {
        $this->_accumulate = $plot instanceof BarElement ? new AccBarPlot(array($plot->getGraph()))
                                                          : $plot->getGraph();

        $this->_graph->Add($this->_accumulate);
        return $this;
    }

    /**
     * Configura as margens do Conteiner do grafico
     * @param integer[] $params
     */
    public function margin ($params)
    {
        if (self::MARGINPARAMS == sizeof($params)) {
        return $this->_graph->img->setMargin($params[0],$params[1],$params[2],$params[3]);
        }
        return $this;
    }

    /**
     * Configura o Titulo do Grafico
     * @param string $title
     */
    public function title ($title)
    {
        $this->_graph->title->Set($title);
        return $this;
    }

    /**
     *
     * Configura o Titulo do Grafico (Eixo X)
     * @param string $title
     */
    public function xTitle ($title)
    {
        $this->_graph->xaxis->title->Set($title);
        return $this;
    }

    /**
     * Configura o Titulo do Grafico (Eixo Y)
     * @param string $title
     */
    public function yTitle ($title)
    {
        $this->_graph->yaxis->title->Set($title);
        return $this;
    }

    public function xAxisLabels ($arrLabels)
    {
        $this->_graph->xaxis->SetTickLabels($arrLabels);
        return $this;
    }

    /**
     * Configura o Subtitulo do Grafico
     * @param string $subtitle
     */
    public function subtitle ($subtitle)
    {
        $this->_graph->subtitle->Set($subtitle);
        return $this;
    }

    /**
     * Renderiza o Grafico
     */
    public function render ($option = NULL)
    {
        $this->_graph->Stroke($option);
    }

    /**
     * Configura a escala do grafico a ser utilizado
     * @param string $scale
     * @param integer $aYMin
     * @param integer $aYMax
     * @param integer $aXMin
     * @param integer $aXMax
     * @return br\gov\sial\core\util\graph\Graphic
     */
    public function setScale ($scale, $aYMin=1, $aYMax=1, $aXMin=1, $aXMax=1)
    {
        $this->_graph->SetScale($scale, $aYMin, $aYMax, $aXMin, $aXMax);
        return $this;
    }

    /**
     * Configura a cor para o fundo do Grafico
     * @param string $color
     */
    public function bgColor ($color)
    {
        $this->_graph->SetColor($color);
        return $this;
    }

    /**
     * Configura a cor para as margens do Grafico
     * @param string $color
     */
    public function mgColor ($color)
    {
        $this->_graph->SetMarginColor($color);
        return $this;
    }

    /**
     * Fabrica da Graphic
     * @param integer $sizex
     * @param integer $sizey
     */
    public static function factory ($sizex, $sizey)
    {
        return new self($sizex, $sizey);
    }
}