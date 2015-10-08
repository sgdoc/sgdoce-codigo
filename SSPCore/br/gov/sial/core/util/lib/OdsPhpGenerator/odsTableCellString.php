<?php

namespace br\gov\sial\core\util\lib\OdsPhpGenerator;

use \ZipArchive,
    \DOMDocument;

/* -
 * Copyright (c) 2009 Laurent VUIBERT
 * License : GNU Lesser General Public License v3
 */

class odsTableCellString extends odsTableCellStringHttp
{

    public function __construct ($value, odsStyleTableCell $odsStyleTableCell = null)
    {
        $this->value = str_replace('&', '&amp;', $value);
        $this->styleName = $odsStyleTableCell;
    }

}
