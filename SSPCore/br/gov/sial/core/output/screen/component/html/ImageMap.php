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
namespace br\gov\sial\core\output\screen\component\html;
use br\gov\sial\core\output\screen\IBuild,
    br\gov\sial\core\output\screen\html\Div,
    br\gov\sial\core\output\screen\html\Map,
    br\gov\sial\core\output\screen\html\Area,
    br\gov\sial\core\output\screen\html\Img as Image,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\output\screen\component\ImageMapAbstract;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * @name Img
 * */
class ImageMap extends ImageMapAbstract implements IBuild
{
    public function __construct ($param)
    {
        $this->_imageMap = Div::factory();

        $this->_img = new Image($param->img);
        $this->_img->usemap = "#" . $param->usemap;

        $this->_map = new Map();
        $this->_map->name = $param->usemap;

        $this->setArea($param->area);
    }

    /**
     *
     * @param stdClass $area
     */
    private function setArea ($areas)
    {
        foreach ($areas as $area)
        {
            $this->_area = new Area();

            $this->_area->shape = $area['shape'];
            $this->_area->coords = $area['coords'];
            $this->_area->href = $area['href'];

            $this->_map->add($this->_area);
        }

        return $this->_map;
    }

    /**
     *
     * @return ImageMap
     */
    public function build ()
    {
        return $this->_imageMap->add($this->_img)
                               ->add($this->_map);
    }
}