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
namespace br\gov\sial\core\util\graph\elements;
use br\gov\sial\core\util\lib\jpgraph\BarPlot,
    br\gov\sial\core\util\graph\GraphicElement;

/**
 * SIAL
 *
 * Utilitário de Geração de Graficos
 *
 * @package br.gov.sial.core.util.graph
 * @subpackage elements
 * @name BarElement
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class BarElement extends GraphicElement
{
    private $_element   = NULL;

    /**
     * Elementos de BarPlot
     * @param integer[] $data
     */
    public function __construct($data)
    {
        $this->_element = new BarPlot($data);
    }

    /**
     * Preenche a cor das colunas
     * @example BarElement::fillCollor
     * @code
     *     # criado o elemento do grafico e informado os dados do grafico
     *     $graphEl$graphEl = GraphicElement::factory(GraphicElement::BARGRAPH, array(12,8,35,3));
     *     # preenche a cor das colunas
     *     $graphEl->fillCollor('orange');
     * @endcode
     * @param string $color
     * @return BarElement
     */
    public function fillCollor ($color)
    {
        $this->_element->setFillColor($color);
        return $this;
    }

    /**
     * Preenche o alinhamento do grafico
     * @example BarElement::alignment
     * @code
     *     # criado o elemento do grafico e informado os dados do grafico
     *     $graphEl$graphEl = GraphicElement::factory(GraphicElement::BARGRAPH, array(12,8,35,3));
     *     # alinhamento das colunas
     *     $graphEl->alignment('center');
     * @endcode
     * @param string $align
     * @return BarElement
     */
    public function alignment ($align)
    {
        $this->_element->SetAlign($align);
        return $this;
    }

    /**
     * Define a posição das barras do grafico
     * @example BarElement::valuePos
     * @code
     *     # criado o elemento do grafico e informado os dados do grafico
     *     $graphEl$graphEl = GraphicElement::factory(GraphicElement::BARGRAPH, array(12,8,35,3));
     *     # posicao
     *     $graphEl->valuePos('center');
     * @endcode
     * @param string $align
     * @return BarElement
     */
    public function valuePos ($position)
    {
        $this->_element->SetValuePos($position);
        return $this;
    }

    /**
     * Define o tamanho das colunas
     * @example BarElement::width
     * @code
     *     # criado o elemento do grafico e informado os dados do grafico
     *     $graphEl$graphEl = GraphicElement::factory(GraphicElement::BARGRAPH, array(12,8,35,3));
     *     # tamanho
     *     $graphEl->width(25);
     * @endcode
     * @param integer $align
     * @return BarElement
     */
    public function width ($width)
    {
        $this->_element->SetWidth($width);
        return $this;
    }

    /**
     * Retorna o grafico a ser renderizado
     * @return br\gov\sial\core\util\lib\jpgraph\BarPlot
     */
    public function getGraph ()
    {
        return $this->_element;
    }

    /**
     * Fabrica de Grafico de Barras
     * @param integer[] $data
     * @return BarElement
     */
    public static function factory ($data)
    {
        return new self($data);
    }
}