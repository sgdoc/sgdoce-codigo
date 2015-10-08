<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */
use br\gov\sial\core\SIALApplication;

error_reporting(TRUE);
ini_set("display_errors", TRUE);
ini_set('error_reporting', E_ALL);

# define o caminho completo para o SSPCore. Por determinação da infra, ficou
# acertado que o caminho do SIALCore no servidor é o definido abaixo
$SSPCoreDir = '/var/www/html/SSPCore';

if (!is_dir($SSPCoreDir)) {
    # mostrar tela de erro ne inclusao do SIAL Core
    echo "Não foi possível localizar o SSPCore\n";
    die;
}

# configura o include_path
set_include_path(
    get_include_path() . PATH_SEPARATOR .
    rtrim($SSPCoreDir, '/') . '/br/gov/sial/core'
);

# importa a classe de aplicacao
require_once 'SIALApplication.php';

# ATENCAO: Esta definição deve ser feita antes de invocar
# o arquivo config.ini.
# variavel que informa ao config.ini a pasta
if (!defined('__MAINAPPDOCS__')) {
    $sep = str_replace(':', DIRECTORY_SEPARATOR, ':br:gov:mainapp');
    define('__MAINAPPDOCS__', current(explode($sep, __DIR__)), FALSE);
}

# define o ambiente de execução e o caminho do arquivo de configuração
$environment   = getenv('CLI_ENV') ? : 'development';
$appConfigFile = dirname(__DIR__) . DIRECTORY_SEPARATOR
               . 'application'    . DIRECTORY_SEPARATOR
               . 'config'         . DIRECTORY_SEPARATOR
               . 'config.ini';

# Cria a aplicacao
$app = SIALApplication::factory($environment, $appConfigFile);

# confifura exibicao de erros por ambiente
error_reporting(
    $app->config()->get('php.environment.ini.error_level')
);

ini_set(
    "display_errors",
    $app->config()->get('php.environment.ini.display_errors')
);

# configura o timezone a ser utilizado
date_default_timezone_set(
    $app->config()->get('php.environment.fnc.date_default_timezone_set')
);