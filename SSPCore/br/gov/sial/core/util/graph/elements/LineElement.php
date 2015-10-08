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
use br\gov\sial\core\util\graph\GraphicElement,
    br\gov\sial\core\util\lib\jpgraph\LinePlot;

/**
 * SIAL
 *
 * Utilitário de Geração de Graficos
 *
 * @package br.gov.sial.core.util.graph
 * @subpackage elements
 * @name LineElement
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class LineElement extends GraphicElement
{
    private $_element   = NULL;

    /**
     * Elementos de LinePlot
     * @param integer[] $data
     */
    public function __construct($data)
    {
        $this->_element = new LinePlot($data);
    }

    /**
     * Retorna o grafico a ser renderizado
     */
    public function getGraph ()
    {
        return $this->_element;
    }

    /**
     * Fabrica de Grafico de Barras
     * @param integer[] $data
     */
    public static function factory ($data)
    {
        return new self($data);
    }
}