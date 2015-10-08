<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

$_caminhoVendorZend =  realpath(dirname(__FILE__) )."/../vendor/zendframework/zendframework1/library/";
$_caminhoZendFramework = "/usr/local/zend/var/libraries/Zend_Framework_1/default/library";

if (file_exists($_caminhoVendorZend)){
    $aIncludePath = explode(PATH_SEPARATOR, get_include_path());
    $indice = array_search($_caminhoZendFramework,$aIncludePath);
    if ($indice>=0){
        $aIncludePath[$indice] = $_caminhoVendorZend;
    }
    set_include_path(implode(PATH_SEPARATOR, $aIncludePath));
}

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../library',
    get_include_path(),
)));

$libPath    = getenv('LIB_PATH') ? getenv('LIB_PATH') : NULL;
$libVersion = getenv('LIB_VERSION') ? getenv('LIB_VERSION') : NULL;

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$lib = APPLICATION_PATH . '/../library';

if (NULL !== $libPath) {
    if (!$libVersion) {
        $libVersion = 'latest';
    }
    $autoloader->setZfPath($libPath, $libVersion);
    $lib = $autoloader->getZfPath();
}

define('LIB_PATH', $lib);

require_once 'Core/Application.php';
// Create application, bootstrap, and run
$application = new Core_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()
            ->run();

function dump ($obj, $exit = TRUE, $outputRaw = TRUE)
{
    $trace = array();
    $backtrace = debug_backtrace();

    $totalCall = sizeof($backtrace);
    for ($i = 0; $i < $totalCall; $i++) {
        if (!$i) {
            $trace[$i] = ' +';
        } elseif ($i+1 == $totalCall) {
            $trace[$i] = ' \\';
        } else {
            $trace[$i] = ' |';
        }
        $trace[$i] .= str_repeat('-', $totalCall - $i);
        $trace[$i] .= "> {$backtrace[$i]['file']}::{$backtrace[$i]['line']}\n";
    }

    $eol = "\n";
    if (TRUE == $outputRaw) {
        header('Content-Type: text/plain; charset=UTF-8');
    } else {
        header('Content-Type: text/html; charset=UTF-8');
        echo "<pre>";
        $eol = '<br />';
    }
    echo $eol, '[CALL STACK]',$eol;
    foreach ($trace as $indice => $value) {
        echo $value;
    }

    echo $eol,'[VALUE]', $eol;
    print_r($obj);
    echo $eol;

    if ($exit) {
        die;
    }
}

function dumpd($obj, $exit = 1)
{
	echo '<pre>';
	\Doctrine\Common\Util\Debug::dump($obj);
	if ($exit) die;
	echo "</pre>";
}