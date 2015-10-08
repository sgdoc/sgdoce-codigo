<?php
require_once 'Core/Application.php';
// Create application, bootstrap, and run
$application = new Core_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

// initialize configs
$options = $application->getOptions();
unset($options['config']);
Zend_Registry::getInstance()->set('configs', $options);