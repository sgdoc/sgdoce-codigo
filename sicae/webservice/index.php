<?php

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

if ('development' === APPLICATION_ENV) {
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 'On');
}

chdir(dirname(__DIR__));
require_once 'init_autoloader.php';
require_once 'init_bootstrap.php';
require_once 'init.php';

$soap = new SoapServer(__DIR__ . '/wsdl/service.wsdl');
$soap->setClass('Services');
$soap->handle();