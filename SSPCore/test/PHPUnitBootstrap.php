<?php
use \br\gov\sial\core\ClassLoader;

error_reporting(E_ALL & ~E_STRICT);
ini_set('display_errors', 'On');
date_default_timezone_set('America/Sao_Paulo');

defined('NAMESPACE_SEPARATOR') ?: define('NAMESPACE_SEPARATOR', '\\');
defined('APPLICATION_ENV')     ?: define('APPLICATION_ENV', 'testing');
defined('SIAL_NS')             ?: define('SIAL_NS', '\br\gov\sial', FALSE);
defined('SIAL_CORE_NS')        ?: define('SIAL_CORE_NS', SIAL_NS . NAMESPACE_SEPARATOR . 'core', FALSE);

if (! defined('SIAL_HTDOCS')) {
    $SIAL_HTDOCS = current(explode(str_replace(NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, SIAL_NS), __DIR__));
    $SIAL_HTDOCS = rtrim($SIAL_HTDOCS, 'test');
    define('SIAL_HTDOCS', $SIAL_HTDOCS, FALSE);

    require_once sprintf("%sClassLoader.php", constant('SIAL_HTDOCS') . str_replace(':', DIRECTORY_SEPARATOR, 'br:gov:sial:core:'));

    /* local onde o SIAL está armazenado */
    ClassLoader::factory(
        constant('SIAL_CORE_NS'),
        constant('SIAL_HTDOCS')
    )->register();

    /* local onde a aplicacao de teste está armazenada */
    ClassLoader::factory('\test\application', __DIR__)->register();
}

/* registra o classloader do SIAL */

//

// # defina o caminho do phpunit.phar
// define('PHPUNIT_PATH', '/usr/local/bin/', FALSE);

// /* SIAL namespace */
// define('SIAL_NS', '\br\gov\sial', FALSE);

// // /* pasta do sial dentro de sua propria estrutura. ex: /path/br/gov/sial */
// $SIAL_HOME = dirname(__DIR__) . DIRECTORY_SEPARATOR;

// if (!defined('SIAL_HTDOCS')) {
//     $SIAL_HTDOCS = current(explode(str_replace(NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, SIAL_NS), __DIR__));
//     $SIAL_HTDOCS = rtrim($SIAL_HTDOCS, 'test');
//     define('SIAL_HTDOCS', $SIAL_HTDOCS, FALSE);
// }

// /* load class loader */
// require_once sprintf(
//     "%sClassLoader.php",
//     constant('SIAL_HTDOCS') . str_replace(':', DIRECTORY_SEPARATOR, 'br:gov:sial:core:')
// );

// /* load SIAL class */
// ClassLoader::factory(SIAL_CORE_NS, SIAL_HTDOCS)->register();

// /* load SIAL Tests */
// ClassLoader::factory('\test\br\gov\sial', $SIAL_HOME)->register();

// /* load system deps */
// require_once 'Zend/Registry.php';
// require_once PHPUNIT_PATH . 'phpunit.phar';