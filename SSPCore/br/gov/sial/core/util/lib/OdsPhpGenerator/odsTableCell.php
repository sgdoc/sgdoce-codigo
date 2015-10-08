<?php

namespace br\gov\sial\core\util\lib\OdsPhpGenerator;

use \ZipArchive,
    \DOMDocument;

/* -
 * Copyright (c) 2009 Laurent VUIBERT
 * License : GNU Lesser General Public License v3
 */

abstract class odsTableCell
{

    protected $styleName;
    protected $numberColumnsSpanned;
    protected $numberRowsSpanned;
    protected $formula;
    protected $numberColumnsRepeated;

    abstract protected function __construct ();

    protected function getContent (ods $ods, DOMDocument $dom)
    {
        $table_table_cell = $dom->createElement('table:table-cell');
        if ($this->styleName) {
            $ods->addTmpStyles($this->styleName);
            $table_table_cell->setAttribute("table:style-name", $this->styleName->getName());
        }
        $this->cellOpts($table_table_cell);
        return $table_table_cell;
    }

    protected function cellOpts ($table_table_cell)
    {
        if ($this->numberColumnsSpanned)
            $table_table_cell->setAttribute("table:number-columns-spanned", $this->numberColumnsSpanned);
        if ($this->numberRowsSpanned)
            $table_table_cell->setAttribute("table:number-rows-spanned", $this->numberRowsSpanned);
        if ($this->formula)
            $table_table_cell->setAttribute("table:formula", "of:=" . $this->formula);
        if ($this->numberColumnsRepeated)
            $table_table_cell->setAttribute("table:number-columns-repeated", $this->numberColumnsRepeated);
    }

    public function setNumberColumnsSpanned ($numberColumnsSpanned)
    {
        $this->numberColumnsSpanned = $numberColumnsSpanned;
    }

    public function getNumberColumnsSpanned ()
    {
        if (!$this->numberColumnsSpanned)
            return 1;
        return $this->numberColumnsSpanned;
    }

    public function setNumberRowsSpanned ($numberRowsSpanned)
    {
        $this->numberRowsSpanned = $numberRowsSpanned;
    }

    public function setFormula ($formula)
    {
        $this->formula = $formula;
    }

    public function setNumberColumnsRepeated ($numberColumnsRepeated)
    {
        $this->numberColumnsRepeated = $numberColumnsRepeated;
    }

}