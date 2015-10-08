<?php

namespace br\gov\sial\core\util\lib\OdsPhpGenerator;

use \ZipArchive,
    \DOMDocument;

/* -
 * Copyright (c) 2009 Laurent VUIBERT
 * License : GNU Lesser General Public License v3
 */

class odsTableCellStringUrl extends odsTableCellString
{

    public function getContent (ods $ods, DOMDocument $dom)
    {
        $table_table_cell = odsTableCell::getContent($ods, $dom);
        $table_table_cell->setAttribute("office:value-type", "string");

        // text:p
        $text_p = $dom->createElement('text:p');
        $table_table_cell->appendChild($text_p);

        // text:a
        $text_a = $dom->createElement('text:a', $this->value);
        $text_a->setAttribute("xlink:href", (substr($this->value, 0, 7) == "http://" ? '' : "http://") . $this->value);
        $text_p->appendChild($text_a);
        return $table_table_cell;
    }

}