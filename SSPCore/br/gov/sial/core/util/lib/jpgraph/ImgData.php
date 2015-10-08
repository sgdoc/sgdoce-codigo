<?php
namespace br\gov\sial\core\util\lib\jpgraph;
//========================================================================
// CLASS ImgData
// Description: Base class for all image data classes that contains the
// real image data.
//========================================================================
class ImgData {
    protected $name = '';  // Each subclass gives a name
    protected $an = array();  // Data array names
    protected $colors = array(); // Available colors
    protected $index  = array(); // Index for colors
    protected $maxidx = 0 ;  // Max color index
    protected $anchor_x=0.5, $anchor_y=0.5 ;    // Where is the center of the image

    function __construct() {
        // Empty
    }

    // Create a GD image from the data and return a GD handle
    function GetImg($aMark,$aIdx) {
        $n = $this->an[$aMark];
        if( is_string($aIdx) ) {
            if( !in_array($aIdx,$this->colors) ) {
                JpGraphError::RaiseL(23001,$this->name,$aIdx);//('This marker "'.($this->name).'" does not exist in color: '.$aIdx);
            }
            $idx = $this->index[$aIdx];
        }
        elseif( !is_integer($aIdx) ||
        (is_integer($aIdx) && $aIdx > $this->maxidx ) ) {
            JpGraphError::RaiseL(23002,$this->name);//('Mark color index too large for marker "'.($this->name).'"');
        }
        else
        $idx = $aIdx ;
        return Image::CreateFromString(base64_decode($this->{$n}[$idx][1]));
    }

    function GetAnchor() {
        return array($this->anchor_x,$this->anchor_y);
    }
}


// Keep a global flag cache to reduce memory usage
$_gFlagCache=array(
1 => null,
2 => null,
3 => null,
4 => null,
);
// Only supposed to b called as statics
class FlagCache {

    static function GetFlagImgByName($aSize,$aName) {
        global $_gFlagCache;
        require_once('jpgraph_flags.php');
        if( $_gFlagCache[$aSize] === null ) {
            $_gFlagCache[$aSize] = new FlagImages($aSize);
        }
        $f = $_gFlagCache[$aSize];
        $idx = $f->GetIdxByName($aName,$aFullName);
        return $f->GetImgByIdx($idx);
    }
}
