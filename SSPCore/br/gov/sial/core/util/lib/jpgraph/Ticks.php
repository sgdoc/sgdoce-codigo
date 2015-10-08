<?php
namespace br\gov\sial\core\util\lib\jpgraph;
//===================================================
// CLASS Ticks
// Description: Abstract base class for drawing linear and logarithmic
// tick marks on axis
//===================================================
class Ticks {
    public $label_formatstr='';   // C-style format string to use for labels
    public $label_formfunc='';
    public $label_dateformatstr='';
    public $direction=1; // Should ticks be in(=1) the plot area or outside (=-1)
    public $supress_last=false,$supress_tickmarks=false,$supress_minor_tickmarks=false;
    public $maj_ticks_pos = array(), $maj_ticklabels_pos = array(),
           $ticks_pos = array(), $maj_ticks_label = array();
    public $precision;

    protected $minor_abs_size=3, $major_abs_size=5;
    protected $scale;
    protected $is_set=false;
    protected $supress_zerolabel=false,$supress_first=false;
    protected $mincolor='',$majcolor='';
    protected $weight=1;
    protected $label_usedateformat=FALSE;

    function __construct($aScale) {
        $this->scale=$aScale;
        $this->precision = -1;
    }

    // Set format string for automatic labels
    function SetLabelFormat($aFormatString,$aDate=FALSE) {
        $this->label_formatstr=$aFormatString;
        $this->label_usedateformat=$aDate;
    }

    function SetLabelDateFormat($aFormatString) {
        $this->label_dateformatstr=$aFormatString;
    }

    function SetFormatCallback($aCallbackFuncName) {
        $this->label_formfunc = $aCallbackFuncName;
    }

    // Don't display the first zero label
    function SupressZeroLabel($aFlag=true) {
        $this->supress_zerolabel=$aFlag;
    }

    // Don't display minor tick marks
    function SupressMinorTickMarks($aHide=true) {
        $this->supress_minor_tickmarks=$aHide;
    }

    // Don't display major tick marks
    function SupressTickMarks($aHide=true) {
        $this->supress_tickmarks=$aHide;
    }

    // Hide the first tick mark
    function SupressFirst($aHide=true) {
        $this->supress_first=$aHide;
    }

    // Hide the last tick mark
    function SupressLast($aHide=true) {
        $this->supress_last=$aHide;
    }

    // Size (in pixels) of minor tick marks
    function GetMinTickAbsSize() {
        return $this->minor_abs_size;
    }

    // Size (in pixels) of major tick marks
    function GetMajTickAbsSize() {
        return $this->major_abs_size;
    }

    function SetSize($aMajSize,$aMinSize=3) {
        $this->major_abs_size = $aMajSize;
        $this->minor_abs_size = $aMinSize;
    }

    // Have the ticks been specified
    function IsSpecified() {
        return $this->is_set;
    }

    function SetSide($aSide) {
        $this->direction=$aSide;
    }

    // Which side of the axis should the ticks be on
    function SetDirection($aSide=SIDE_RIGHT) {
        $this->direction=$aSide;
    }

    // Set colors for major and minor tick marks
    function SetMarkColor($aMajorColor,$aMinorColor='') {
        $this->SetColor($aMajorColor,$aMinorColor);
    }

    function SetColor($aMajorColor,$aMinorColor='') {
        $this->majcolor=$aMajorColor;

        // If not specified use same as major
        if( $aMinorColor == '' ) {
            $this->mincolor=$aMajorColor;
        }
        else {
            $this->mincolor=$aMinorColor;
        }
    }

    function SetWeight($aWeight) {
        $this->weight=$aWeight;
    }

} // Class
