<?php

namespace br\gov\sial\core\util\lib\OdsPhpGenerator;

use \ZipArchive,
    \DOMDocument;

/* -
 * Copyright (c) 2009 Laurent VUIBERT
 * License : GNU Lesser General Public License v3
 */

class odsTableCellCurrency extends odsTableCell
{

    public $value;
    public $styleName;
    public $currency;

    public function __construct ($value, $currency, $odsStyleTableCell = null)
    {
        $this->value = $value;
        $this->currency = $currency;
        $this->styleName = $odsStyleTableCell;
    }

    public function getContent (ods $ods, DOMDocument $dom)
    {
        switch ($this->currency) {
            case 'EUR':
                $ods->addTmpStyles(new odsStyleMoneyEUR());
                $ods->addTmpStyles(new odsStyleMoneyEURNeg());
                break;
            case 'USD':
                $ods->addTmpStyles(new odsStyleMoneyUSD());
                $ods->addTmpStyles(new odsStyleMoneyUSDNeg());
                break;
            case 'GBP':
                $ods->addTmpStyles(new odsStyleMoneyGBP());
                $ods->addTmpStyles(new odsStyleMoneyGBPNeg());
                break;
            default:
            //FIXME: send error;
        }

        $table_table_cell = $dom->createElement('table:table-cell');
        $this->cellOpts($table_table_cell);
        if ($this->styleName) {
            $style = $ods->getStyleByName($this->styleName->getName() . "-" . $this->currency);
            if (!$style) {
                $style = clone $this->styleName;
                $style->setName($this->styleName->getName() . "-" . $this->currency);
                $style->setStyleDataName('NCur-' . $this->currency);
                $ods->addTmpStyles($style);
            }
            $table_table_cell->setAttribute("table:style-name", $style->getName());
        } else {
            $style = $ods->getStyleByName("ce1-" . $this->currency);
            if (!$style) {
                $style = clone $ods->getStyleByName("ce1");
                $style->setName("ce1-" . $this->currency);
                $style->setStyleDataName('NCur-' . $this->currency);
                $ods->addTmpStyles($style);
            }
            $table_table_cell->setAttribute("table:style-name", $style->getName());
        }

        $table_table_cell->setAttribute("office:value-type", "currency");
        $table_table_cell->setAttribute("office:currency", $this->currency);
        $table_table_cell->setAttribute("office:value", $this->value);

        // text:p
        $text_p = $dom->createElement('text:p');
        $table_table_cell->appendChild($text_p);

        return $table_table_cell;
    }

}
