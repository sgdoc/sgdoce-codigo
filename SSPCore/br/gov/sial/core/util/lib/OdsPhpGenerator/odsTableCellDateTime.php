<?php

namespace br\gov\sial\core\util\lib\OdsPhpGenerator;

use \ZipArchive,
    \DOMDocument;

/* -
 * Copyright (c) 2009 Laurent VUIBERT
 * License : GNU Lesser General Public License v3
 */

class odsTableCellDateTime extends odsTableCell
{

    private $dateTime;
    private $format;

    public function __construct ($dateTime, $format = "MMDDYYHHMMAMPM", $language = null, odsStyleGraphic $odsStyleCellDate = null)
    {
        $this->dateTime = $dateTime;
        $this->format = $format;
        $this->language = $language;
        $this->styleName = $odsStyleCellDate;
    }

    public function getContent (ods $ods, DOMDocument $dom)
    {
        switch ($this->format) {
            case 'MMDDYYHHMMSSAMPM':
                $ods->addTmpStyles(new odsStyleDateTimeMMDDYYHHMMSSAMPM($this->language));
                break;
            case 'MMDDYYHHMMAMPM':
                $ods->addTmpStyles(new odsStyleDateTimeMMDDYYHHMMAMPM($this->language));
                break;
            case 'DDMMYYHHMMSS':
                $ods->addTmpStyles(new odsStyleDateTimeDDMMYYHHMMSS($this->language));
                break;
            case 'DDMMYYHHMM':
                $ods->addTmpStyles(new odsStyleDateTimeDDMMYYHHMM($this->language));
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
                $style->setStyleDataName('DateTime-' . $this->format);
                $ods->addTmpStyles($style);
            }
            $table_table_cell->setAttribute("table:style-name", $style->getName());
        } else {
            $style = $ods->getStyleByName("ce1-" . $this->format);
            if (!$style) {
                $style = clone $ods->getStyleByName("ce1");
                $style->setName("ce1-" . $this->format);
                $style->setStyleDataName('DateTime-' . $this->format);
                $ods->addTmpStyles($style);
            }
            $table_table_cell->setAttribute("table:style-name", $style->getName());
        }

        $table_table_cell->setAttribute("office:value-type", "date");
        $table_table_cell->setAttribute("office:date-value", $this->dateTime);
        return $table_table_cell;
    }

}