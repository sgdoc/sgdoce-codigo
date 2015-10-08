<?php

/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

namespace Robot;

/**
 * Debugador do robô
 *
 * @package     Robot
 * @name        Debug
 * @version     1.0.0
 *
 * @author Sósthenes Neto <sosthenes.neto.terceirizado@icmbio.gov.br>
 */
class Debug
{

    const DEBUG = 'DEBUG';
    const INFO = 'INFO';
    const WARN = 'WARN';
    const LOG = 'LOG';
    const ERROR = 'ERROR';

    const STEP_IMAGE = 'MigrationImage';
    const STEP_MERGE = 'MergePdf';
    const STEP_IMAGE_REQUESTED = 'MigrationImageRequested';

    private static $_canDebug = false;

    private static $_step = array(
        'MergePdf' => 'MERGE_',
        'MigrationImage' => 'IMAGEM_',
        'MigrationImageRequested' => 'IMAGE_REQUESTED_'
    );

    /**
     * @param boolean $bool = null
     */
    public static function initializeDebugNoise($bool = null)
    {
        self::$_canDebug = (boolean) $bool;
    }

    /**
     * @param mixed $debug
     * @param string $label
     */
    public static function log($debug = PHP_EOL, $label = self::DEBUG, $step = NULL)
    {
        $date = self::_getDate();
        $log = '';
        if (PHP_EOL === $debug) {
            $log = $debug;
        } else {
            $labels = array(
                self::DEBUG,
                self::INFO,
                self::WARN,
                self::LOG,
                self::ERROR
            );
            if (in_array( $label, $labels ) && is_string( $debug )) {
                $log .= sprintf( '%s [%5s] %s%s', $date, $label, $debug, PHP_EOL );
            } else {
                $log .= sprintf( '%s [%s]%s', $date, $label, PHP_EOL );
                $log .= var_export( $debug, true );
                $log .= PHP_EOL;
            }
            if (self::$_canDebug === true) {
                echo $log;
            }
        }
        error_log( $log, 3, self::_getLogFilename($step) );
    }

    /**
     * @return string
     */
    private static function _getLogFilename($step = NULL)
    {
        $label  = '';
        $labels = array(
                self::STEP_IMAGE,
                self::STEP_MERGE,
                self::STEP_IMAGE_REQUESTED
            );

        if (in_array( $step, $labels )) {
            $label = self::$_step[$step];
        }

        $filename =
            APPLICATION_PATH    . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'data' .
            DIRECTORY_SEPARATOR . 'logs' .
            DIRECTORY_SEPARATOR . 'robot' .
            DIRECTORY_SEPARATOR . 'Robot_' . $label . date( 'Ymd' ) . '.log';

        return $filename;
    }

    /**
     * @return string
     */
    private static function _getDate()
    {
        #With milliseconds
        $microtime = microtime( true );
        $micro = sprintf( "%06d", ($microtime - floor( $microtime )) * 1000000 );
        $datetime = new \DateTime( date( "Y-m-d H:i:s.{$micro} T", $microtime ) );
        $date = $datetime->format( "d/m/Y H:i:s.u T" );
        #Without milliseconds (\Zend_Date())
//        $date = \Zend_Date::now()->toString( 'dd/MM/yyyy HH:mm:ss z' );
        #Without milliseconds (date())
//        $date = date( 'd/m/Y H:i:s T' );
        return $date;
    }

}
