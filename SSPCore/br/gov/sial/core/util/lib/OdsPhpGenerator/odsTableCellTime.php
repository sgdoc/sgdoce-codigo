<?php

namespace br\gov\sial\core\util\lib\OdsPhpGenerator;

use \ZipArchive,
    \DOMDocument;

/* -
 * Copyright (c) 2009 Laurent VUIBERT
 * License : GNU Lesser General Public License v3
 */

class odsTableCellTime extends odsTableCell
{

    private $time;
    private $format;

    public function __construct ($time, $format = "HHMM", odsStyleGraphic $odsStyleCellDate = null)
    {
        $this->time = $time;
        $this->format = $format;
        $this->styleName = $odsStyleCellDate;
    }

    public function getContent (ods $ods, DOMDocument $dom)
    {
        switch ($this->format) {
            case 'HHMMSS':
                $ods->addTmpStyles(new odsStyleTimeHHMMSS());
                break;
            case 'HHMM':
                $ods->addTmpStyles(new odsStyleTimeHHMM());
                break;
            case 'HHMMSSAMPM':
                $ods->addTmpStyles(new odsStyleTimeHHMMSSAMPM());
                break;
            case 'HHMMAMPM':
                $ods->addTmpStyles(new odsStyleTimeHHMMAMPM());
                break;
            default:
            //FIXME: send error;
        }

        $table_table_cell = $dom->createElement('table:table-cell');
        $this->cellOpts($table_table_cell);

        if ($this->styleName) {
            $style = $ods->getStyleByName($this->styleName->getName() . "-" . $this->format);
            if (!$style) {
                $style = clone $this->styleName;
                $style->setName($this->styleName->getName() . "-" . $this->format);
                $style->setStyleDataName('Time-' . $this->format);
                $ods->addTmpStyles($style);
            }
            $table_table_cell->setAttribute("table:style-name", $style->getName());
        } else {
            $style = $ods->getStyleByName("ce1-" . $this->format);
            if (!$style) {
                $style = clone $ods->getStyleByName("ce1");
                $style->setName("ce1-" . $this->format);
                $style->setStyleDataName('Time-' . $this->format);
                $ods->addTmpStyles($style);
            }
            $table_table_cell->setAttribute("table:style-name", $style->getName());
        }

        $table_table_cell->setAttribute("office:value-type", "time");
        $table_table_cell->setAttribute("office:time-value", $this->time);
        return $table_table_cell;
    }

}