<?php

namespace br\gov\sial\core\util\lib\OdsPhpGenerator;

use \ZipArchive,
    \DOMDocument;

/* -
 * Copyright (c) 2009 Laurent VUIBERT
 * License : GNU Lesser General Public License v3
 */

class odsTableCellDate extends odsTableCell
{

    private $date;
    private $format;
    private $language;

    public function __construct ($date, $format = "MMDDYYYY", $language = null, odsStyleGraphic $odsStyleCellDate = null)
    {
        $this->date = $date;
        $this->format = $format;
        $this->language = $language;
        $this->styleName = $odsStyleCellDate;
    }

    public function getContent (ods $ods, DOMDocument $dom)
    {
        switch ($this->format) {
            case 'DDMMYYYY':
                $ods->addTmpStyles(new odsStyleDateDDMMYYYY($this->language));
                break;
            case 'DDMMYY':
                $ods->addTmpStyles($style = new odsStyleDateDDMMYY($this->language));
                break;
            case 'MMDDYYYY':
                $ods->addTmpStyles($style = new odsStyleDateMMDDYYYY($this->language));
                break;
            case 'MMDDYY':
                $ods->addTmpStyles($style = new odsStyleDateMMDDYY($this->language));
                break;
            case 'DMMMYYYY':
                $ods->addTmpStyles($style = new odsStyleDateDMMMYYYY($this->language));
                break;
            case 'DMMMYY':
                $ods->addTmpStyles($style = new odsStyleDateDMMMYY($this->language));
                break;
            case 'DMMMMYYYY':
                $ods->addTmpStyles($style = new odsStyleDateDMMMMYYYY($this->language));
                break;
            case 'DMMMMYY':
                $ods->addTmpStyles($style = new odsStyleDateDMMMMYY($this->language));
                break;
            case 'MMMDYYYY':
                $ods->addTmpStyles($style = new odsStyleDateMMMDYYYY($this->language));
                break;
            case 'MMMDYY':
                $ods->addTmpStyles($style = new odsStyleDateMMMDYY($this->language));
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
                $style->setStyleDataName('Date-' . $this->format);
                $ods->addTmpStyles($style);
            }
            $table_table_cell->setAttribute("table:style-name", $style->getName());
        } else {
            $style = $ods->getStyleByName("ce1-" . $this->format);
            if (!$style) {
                $style = clone $ods->getStyleByName("ce1");
                $style->setName("ce1-" . $this->format);
                $style->setStyleDataName('Date-' . $this->format);
                $ods->addTmpStyles($style);
            }
            $table_table_cell->setAttribute("table:style-name", $style->getName());
        }

        $table_table_cell->setAttribute("office:value-type", "date");
        $table_table_cell->setAttribute("office:date-value", $this->date);
        return $table_table_cell;
    }

}