<?php

namespace br\gov\sial\core\util\lib\OdsPhpGenerator;

use \ZipArchive,
    \DOMDocument;

/* -
 * Copyright (c) 2009 Laurent VUIBERT
 * License : GNU Lesser General Public License v3
 */

class odsTableCellFloat extends odsTableCell
{

    public $value;
    public $styleName;

    public function __construct ($value, odsStyleTableCell $odsStyleTableCell = null)
    {
        $this->value = $value;
        $this->styleName = $odsStyleTableCell;
    }

    public function getContent (ods $ods, DOMDocument $dom)
    {
        $table_table_cell = odsTableCell::getContent($ods, $dom);
        $table_table_cell->setAttribute("office:value-type", "float");
        $table_table_cell->setAttribute("office:value", $this->value);

        // text:p
        $text_p = $dom->createElement('text:p', $this->value);
        $table_table_cell->appendChild($text_p);
        return $table_table_cell;
    }

}