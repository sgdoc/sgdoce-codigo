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
    br\gov\sial\core\util\graph\Graphic,
    br\gov\sial\core\util\graph\GraphicElement;

/**
 * SIAL
 *
 * @package br.gov.sial.core.mvcb.view
 * @subpackage helper
 * @name GraphGenerator
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class GraphGenerator extends SIALAbstract
{
    /**
     * Helper para gerar o Grafico.
     *
     * @param string $graphType
     * @param integer[] $data
     * @param integer[] $size
     * @param string[] $titles
     * @param string $bgColor
     * @param string $mgColor
     */
    public function graphGenerator ($graphType,
                                    $data,
                                    $size,
                                    $titles = array('','','',''),
                                    $bgColor = 'white',
                                    $mgColor = '#DFDECB'
                                   )
    {
        $graph = new Graphic($size[0], $size[1]);
        ob_start();

        $graph->setScale(Graphic::INTLIN)
              ->title($titles[0])
              ->subtitle($titles[1])
              ->xtitle($titles[2])
              ->ytitle($titles[3])
              ->bgColor($bgColor)
              ->mgColor($mgColor)
              ->insert(GraphicElement::factory($graphType, $data))
              ->render();
        # Utilizado devido o fato de que uma imagem esta sendo gerada e qualquer texto exibido irá corromper a imagem
        die;
    }
}