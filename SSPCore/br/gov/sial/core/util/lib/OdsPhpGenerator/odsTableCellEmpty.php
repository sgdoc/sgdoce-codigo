<?php

namespace br\gov\sial\core\util\lib\OdsPhpGenerator;

use \ZipArchive,
    \DOMDocument;

/* -
 * Copyright (c) 2009 Laurent VUIBERT
 * License : GNU Lesser General Public License v3
 */

class odsTableCellEmpty extends odsTableCell
{

    public function __construct (odsStyleTableCell $odsStyleTableCell = null)
    {
        $this->styleName = $odsStyleTableCell;
    }

    public function getContent (ods $ods, DOMDocument $dom)
    {
        return odsTableCell::getContent($ods, $dom);
    }

}