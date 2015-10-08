<?php

namespace br\gov\sial\core\util\lib\OdsPhpGenerator;

use \ZipArchive,
    \DOMDocument;

/* -
 * Copyright (c) 2009 Laurent VUIBERT
 * License : GNU Lesser General Public License v3
 */

class odsCoveredTableCell extends odsTableCell
{

    public function __construct ()
    {

    }

    public function getContent (ods $ods, DOMDocument $dom)
    {
        $table_table_cell = $dom->createElement('table:covered-table-cell');
        $this->cellOpts($table_table_cell);
        return $table_table_cell;
    }

}