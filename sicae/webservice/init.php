<?php
$application->bootstrap('doctrine');
$application->bootstrap('serviceLocator');
$application->getBootstrap()
            ->setContainer(Zend_Registry::getInstance());

Core_Configuration::setEntityName('Sica\Model\Entity\Configuracao');
Core_Configuration::getInstance();
require_once 'Services.php';
