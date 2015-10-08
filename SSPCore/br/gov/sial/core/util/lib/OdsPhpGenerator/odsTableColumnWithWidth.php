<?php

namespace br\gov\sial\core\util\lib\OdsPhpGenerator;

use \ZipArchive,
    \DOMDocument;

/* -
 * Copyright (c) 2009 Laurent VUIBERT
 * License : GNU Lesser General Public License v3
 */


class odsTableColumnWithWidth extends odsTableColumn
{

    public function __construct ($width)
    {
        $styleColumn = new odsStyleTableColumn();
        $styleColumn->setColumnWidth($width);
        parent::__construct($styleColumn);
    }

}