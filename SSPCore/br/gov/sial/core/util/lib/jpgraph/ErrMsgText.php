<?php
namespace br\gov\sial\core\util\lib\jpgraph;

class ErrMsgText {
    private $lt=NULL;
    function __construct() {
        GLOBAL $__jpg_err_locale;
        $file = 'lang/'.$__jpg_err_locale.'.inc.php';

        // If the chosen locale doesn't exist try english
        if( !file_exists(dirname(__FILE__).'/'.$file) ) {
            $__jpg_err_locale = 'en';
        }

        $file = 'lang/'.$__jpg_err_locale.'.inc.php';
        if( !file_exists(dirname(__FILE__).'/'.$file) ) {
            die('Chosen locale file ("'.$file.'") for error messages does not exist or is not readable for the PHP process. Please make sure that the file exists and that the file permissions are such that the PHP process is allowed to read this file.');
        }
        require($file);
        $this->lt = $_jpg_messages;
    }

    function Get($errnbr,$a1=null,$a2=null,$a3=null,$a4=null,$a5=null) {
        GLOBAL $__jpg_err_locale;
        if( !isset($this->lt[$errnbr]) ) {
            return 'Internal error: The specified error message ('.$errnbr.') does not exist in the chosen locale ('.$__jpg_err_locale.')';
        }
        $ea = $this->lt[$errnbr];
        $j=0;
        if( $a1 !== null ) {
            $argv[$j++] = $a1;
            if( $a2 !== null ) {
                $argv[$j++] = $a2;
                if( $a3 !== null ) {
                    $argv[$j++] = $a3;
                    if( $a4 !== null ) {
                        $argv[$j++] = $a4;
                        if( $a5 !== null ) {
                            $argv[$j++] = $a5;
                        }
                    }
                }
            }
        }
        $numargs = $j;
        if( $ea[1] != $numargs ) {
            // Error message argument count do not match.
            // Just return the error message without arguments.
            return $ea[0];
        }
        switch( $numargs ) {
            case 1:
                $msg = sprintf($ea[0],$argv[0]);
                break;
            case 2:
                $msg = sprintf($ea[0],$argv[0],$argv[1]);
                break;
            case 3:
                $msg = sprintf($ea[0],$argv[0],$argv[1],$argv[2]);
                break;
            case 4:
                $msg = sprintf($ea[0],$argv[0],$argv[1],$argv[2],$argv[3]);
                break;
            case 5:
                $msg = sprintf($ea[0],$argv[0],$argv[1],$argv[2],$argv[3],$argv[4]);
                break;
            case 0:
            default:
                $msg = sprintf($ea[0]);
                break;
        }
        return $msg;
    }
}
