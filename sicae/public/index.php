<?php
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('PROXY_CACHE_PATH')
|| define('PROXY_CACHE_PATH', realpath(dirname(__FILE__) . '/../data/proxy_cache'));

if ('development' === APPLICATION_ENV) {
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 'On');
}

chdir(dirname(__DIR__));
require_once 'init_autoloader.php';
require_once 'init_bootstrap.php';

$application->bootstrap()
            ->run();