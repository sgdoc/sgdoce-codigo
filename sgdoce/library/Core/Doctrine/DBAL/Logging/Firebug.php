<?php

/**
 * Bgy Library
 *
 * LICENSE
 *
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 *
 * @category Bgy
 * @package Bgy\DBAL
 * @subpackage Logging
 * @author Boris Guéry <guery.b@gmail.com>
 * @license http://sam.zoy.org/wtfpl/COPYING
 * @link http://borisguery.github.com/bgylibrary
 */

namespace Core\Doctrine\DBAL\Logging;

use \Zend_Wildfire_Plugin_FirePhp as FirePhp,
    \Zend_Wildfire_Plugin_FirePhp_TableMessage as FirePhp_TableMessage;

require_once 'Doctrine/DBAL/Logging/SQLLogger.php';

class Firebug implements \Doctrine\DBAL\Logging\SQLLogger
{
    /**
     * Total duration in seconds to emit warning
     * @var int
     */
    const DURATION_LIMIT = 2;

    /**
     * Log of critical queries
     * @var \Zend_Log
     */
    private $_criticalQueriesLogger = null;

    /**
     * The original label for this profiler.
     * @var string
     */
    protected $_label = null;

    /**
     * The label template for this profiler
     * @var string
     */
    protected $_label_template = '%label% (%totalCount% @ %totalDuration% sec)';

    /**
     * The message envelope holding the profiling summary
     * @var Zend_Wildfire_Plugin_FirePhp_TableMessage
     */
    protected $_message = null;

    /**
     * The total time taken for all profiled queries.
     * @var float
     */
    protected $_totalElapsedTime = 0;

    /**
     * Current query
     * @var array
     */
    protected $_currentQuery = array();

    /**
     * Query count
     * @var integer
     */
    protected $_queryCount = 0;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->_label = 'SQLs SGDOC-e';
        $this->_message = new FirePhp_TableMessage('Doctrine2 Queries');
        $this->_message->setBuffered(true);
        $this->_message->setHeader(array('Time', 'Event', 'Parameters'));
        $this->_message->setOption('includeLineNumbers', false);

        $this->_setCriticalQueriesLogger();

        FirePhp::getInstance()->send($this->_message);
    }

    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->_currentQuery['sql'        ] = $sql;
        $this->_currentQuery['parameters' ] = $params;
        $this->_currentQuery['types'      ] = $types;
        $this->_currentQuery['executionMS'] = microtime(true);
    }

    public function stopQuery()
    {
        $elapsedTime = microtime(true) - $this->_currentQuery['executionMS'];
        $this->_totalElapsedTime += $elapsedTime;
        ++$this->_queryCount;
        $labelTime = round($elapsedTime, 5);
        $this->_message->addRow(
                array(
                    $labelTime,
                    $this->_currentQuery['sql'],
                    $this->_currentQuery['parameters'],
                )
        );
        $this->_updateMessageLabel();

        if (!is_null($this->_criticalQueriesLogger)) {
            if ($labelTime >= self::DURATION_LIMIT) {
                $query = $this->_currentQuery['sql'];
                $params = $this->_currentQuery['parameters'];
                $log = sprintf( PHP_EOL . 'TIME: %f' . PHP_EOL, $labelTime );
                if (count($params) > 0) {
                    $queryWithParams = '';
                    $queryPieces = explode('?', $query );
                    foreach ($queryPieces as $key => $queryPiece) {
                        if (isset($params[$key])) {
                            if (is_numeric($params[$key]) || is_bool($params[$key])) {
                                $queryWithParams .= $params[$key];
                            } else {
                                $params[$key] = str_replace("'","''",$params[$key]);
                                $queryWithParams .= "'{$params[$key]}'";
                            }
                        }
                        $queryWithParams .= $queryPiece;
                    }
                    $log .= sprintf( 'QUERY (WITH BOUND PARAMETERS):' . PHP_EOL . '%s' . PHP_EOL . PHP_EOL .
                                     'QUERY (ORIGINAL):' . PHP_EOL . '%s' . PHP_EOL . PHP_EOL .
                                     'BOUND PARAMETERS:' . PHP_EOL . '%s',
                                     $queryWithParams,
                                     $query,
                                     print_r($params,true));
                } else {
                    $log .= sprintf( 'QUERY:'.PHP_EOL.'%s', $query );
                }

                $request = \Zend_Controller_Front::getInstance()->getRequest();
                $log .= PHP_EOL . 'ROUTE: ';

                $log .= $request->getModuleName();
                $log .= '/'. $request->getControllerName();
                $log .= '/'. $request->getActionName() . PHP_EOL;

                $log .= PHP_EOL . PHP_EOL .
                        '_______________________________________________________________________________';

                $this->_criticalQueriesLogger->log( $log, \Zend_Log::DEBUG );
            }
        }
    }

    /**
     * Update the label of the message holding the profile info.
     *
     * @return void
     */
    protected function _updateMessageLabel()
    {
        if (!$this->_message) {
            return;
        }
        $search = array('%label%', '%totalCount%', '%totalDuration%');
        $replacements = array(
            $this->_label,
            $this->_queryCount,
            (string) round($this->_totalElapsedTime, 5)
        );
        $label = str_replace($search, $replacements, $this->_label_template);
        $this->_message->setLabel($label);

        $this->queries[] = $this->_currentQuery;
    }

    /**
     * Initialize the Critical Queries Logger
     * @return void
     */
    private function _setCriticalQueriesLogger()
    {
        if(PHP_SAPI !== 'cli') {
            $logPath = APPLICATION_PATH . '/../data/logs/sql/criticalQueries.log';
            $writer = new \Zend_Log_Writer_Stream($logPath, 'r+');
            $writer->setFormatter( new \Zend_Log_Formatter_Simple() );
            $this->_criticalQueriesLogger = new \Zend_Log( $writer );
        }
    }

}