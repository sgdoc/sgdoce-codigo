<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/application'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../library',
    APPLICATION_PATH . '/../library/vendor',
    get_include_path(),
)));

$libPath    = getenv('LIB_PATH') ? getenv('LIB_PATH') : NULL;
$libVersion = getenv('LIB_VERSION') ? getenv('LIB_VERSION') : NULL;

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$lib = APPLICATION_PATH . '/../library/vendor';

if (NULL !== $libPath) {
    if (!$libVersion) {
        $libVersion = 'latest';
    }
    $autoloader->setZfPath($libPath, $libVersion);
    $lib = $autoloader->getZfPath();
}

define('LIB_PATH', $lib);