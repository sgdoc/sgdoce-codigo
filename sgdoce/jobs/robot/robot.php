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
#entrando na pasta do arquivo...
chdir( realpath( dirname( __FILE__ ) ) );
ignore_user_abort( false );

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

$arrRegisterNamespace = array();
$arrRegisterNamespace['application_robot'] = 'Robot';
foreach ($arrRegisterNamespace as $namespace) {
    $autoloader->registerNamespace( $namespace );
}

/** BEGIN::SGDOCE stuff * */
defined( 'APPLICATION_PATH' ) || define( 'APPLICATION_PATH', realpath( dirname( __FILE__ ) . '/../../application' ) );
defined( 'PROXY_CACHE_PATH' ) || define( 'PROXY_CACHE_PATH', realpath( dirname(__FILE__) . '/../data/proxy_cache' ) );


// Ensure library/ is on include_path
set_include_path( implode( PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../library',
    get_include_path(),
) ) );
$libPath = getenv( 'LIB_PATH' ) ? getenv( 'LIB_PATH' ) : NULL;
$libVersion = getenv( 'LIB_VERSION' ) ? getenv( 'LIB_VERSION' ) : NULL;
$lib = APPLICATION_PATH . '/../library';
if (NULL !== $libPath) {
    if (!$libVersion) {
        $libVersion = 'latest';
    }
    $autoloader->setZfPath( $libPath, $libVersion );
    $lib = $autoloader->getZfPath();
}
define( 'LIB_PATH', $lib );
/** END::SGDOCE stuff * */
$main = \Robot\Main::getInstance();
try {
    register_shutdown_function( array($main, 'shutdown') );
    $main->argsManipulator();

    /** BEGIN::SGDOCE stuff * */
    require_once 'Core/Application.php';
    // Create application, bootstrap, and run
    $application = new Core_Application(
        APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'
    );
    $application->bootstrap();
    /** END::SGDOCE stuff * */

    $main->bootstrap()
         ->run();
} catch (Exception $exception) {
    if ($main instanceof Robot\Main) {
        $main->setExpection( $exception );
        exit( 1 );
    }

    if ($exception instanceof Zend_Console_Getopt_Exception) {
        exit( $exception->getMessage() . PHP_EOL . $exception->getUsageMessage() );
    }
    exit( $exception->getMessage() );
}

exit( 0 );
