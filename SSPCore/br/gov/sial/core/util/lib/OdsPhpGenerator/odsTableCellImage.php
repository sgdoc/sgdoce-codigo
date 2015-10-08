<?php

namespace br\gov\sial\core\util\lib\OdsPhpGenerator;

use \ZipArchive,
    \DOMDocument;

/* -
 * Copyright (c) 2009 Laurent VUIBERT
 * License : GNU Lesser General Public License v3
 */

class odsTableCellImage extends odsTableCell
{

    private $file;
    private $width;
    private $heigth;
    private $zIndex;
    private $x;
    private $y;

    public function __construct ($file, odsStyleGraphic $odsStyleGraphic = null)
    {
        $this->styleName = $odsStyleGraphic;
        $this->file = $file;
        $im = imagecreatefromstring(file_get_contents($file));
        $this->width = (imagesx($im) * 0.035276875) . "cm";
        $this->height = (imagesy($im) * 0.035276875) . "cm";
        imagedestroy($im);

        $this->zIndex = "0";
        $this->x = "0cm";
        $this->y = "0cm";
    }

    public function setWidth ($width)
    {
        $this->width = $width;
    }

    public function setHeight ($heigth)
    {
        $this->heigth = $heigth;
    }

    public function setZIndex ($zIndex)
    {
        $this->zIndex = $zIndex;
    }

    public function setX ($x)
    {
        $this->$x = $x;
    }

    public function setY ($y)
    {
        $this->$y = $y;
    }

    public function getContent (ods $ods, DOMDocument $dom)
    {
        if ($this->styleName)
            $style = $this->styleName;
        else
            $style = new odsStyleGraphic("gr1");

        $ods->addTmpStyles($style);

        $table_table_cell = $dom->createElement('table:table-cell');
        $this->cellOpts($table_table_cell);

        $draw_frame = $dom->createElement('draw:frame');
        //$draw_frame->setAttribute("table:end-cell-address", "Feuille1.AA85");
        //$draw_frame->setAttribute("table:end-x", "1.27cm");
        //$draw_frame->setAttribute("table:end-y", "0.472cm");
        $draw_frame->setAttribute("draw:z-index", $this->zIndex);
        $draw_frame->setAttribute("draw:name", "Images " . md5(time() . rand()));
        $draw_frame->setAttribute("draw:style-name", $style->getName());
        $draw_frame->setAttribute("draw:text-style-name", "P1");
        $draw_frame->setAttribute("svg:width", $this->width);
        $draw_frame->setAttribute("svg:height", $this->height);
        $draw_frame->setAttribute("svg:x", $this->x);
        $draw_frame->setAttribute("svg:y", $this->y);
        $table_table_cell->appendChild($draw_frame);

        $draw_image = $dom->createElement('draw:image');
        $draw_image->setAttribute("xlink:href", $ods->addTmpPictures($this->file));
        $draw_image->setAttribute("xlink:type", "simple");
        $draw_image->setAttribute("xlink:show", "embed");
        $draw_image->setAttribute("xlink:actuate", "onLoad");
        $draw_frame->appendChild($draw_image);

        $text_p = $dom->createElement('text:p');
        $draw_image->appendChild($text_p);

        return $table_table_cell;
    }

}