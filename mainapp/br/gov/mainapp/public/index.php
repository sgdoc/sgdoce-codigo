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

# define o caminho completo para o SSPCore. Por determinação da infra, ficou
# acertado que o caminho do SIALCore no servidor é o definido abaixo
$SSPCoreDir = '/var/www/html/SSPCore';

if (! is_dir($SSPCoreDir)) {
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
if (! defined('__MAINAPPDOCS__')) {
    $sep = str_replace(':', DIRECTORY_SEPARATOR, ':br:gov:mainapp');
    define('__MAINAPPDOCS__', current(explode($sep, __DIR__)), FALSE);
}

# define o ambiente de execução e o caminho do arquivo de configuração
$environment   = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER["APPLICATION_ENV"] : 'development';
$appConfigPath = dirname(__DIR__) . DIRECTORY_SEPARATOR
               . 'application'    . DIRECTORY_SEPARATOR
               . 'config'         . DIRECTORY_SEPARATOR
               ;

# Cria a aplicacao
$app = SIALApplication::factory(
    $environment,
    $appConfigPath
);

# Cria a referencia para a Controller
$ctrl = $app->controller();

 # Define o comportamento a ser executado antes da aplicacao iniciar
 # bom local criar links para banco, webservice.
$app

#eventos globais das controllers
 ->addEvent($ctrl::T_EVENT_CONTROLLER_ABSTRACT_SHOW_MENU,
    function () use ($ctrl) {
        #onshowMenu
        $ctrl->getMenu();
    }
 )

 ->addEvent($ctrl::T_EVENT_CONTROLLER_ABSTRACT_RENDER_JSON,
    function ($jsonData) use ($app) {
        #onRenderJson
        if (is_null($jsonData)) {
            $jsonData = array(
                'result' => array(),
                'status' => 'success'
            );
        }
        $app->set('_jsonData', $jsonData)
            ->render('/application/layout/json');
    }
 )

 # define o que sera executado quando o ambiente da aplicacao estiver pronta para ser executada
 ->addEvent(
    # agenda evento para ser executado quando a aplicacao
    # tiver carregado todos as dependecias
    SIALApplication::T_EVENT_APPLICATION_ON_READY,

    # caso seja necessario usar uma referencia de application
    # faca como mostrado abaixo, passando a referencia do nome
    # do objeto de aplicacao com o construtor 'use'
    function () use ($app, $ctrl) {
        try{
        $action = $app->bootstrap->getAction();

        $ctrl->$action();
        } catch (\Exception $e) {
            dump($e);
        }
    }
 )

  # constroi a app
 ->build()
 ->show();

/**
 * exibe o conteudo do objeto informado bem como a pilha
 * de execução até o ponto de chamada desta função
 *
 * @param  mixed
 * @param  boolean
 * @param  boolean
 */
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
        if (!headers_sent()){
            header('Content-Type: text/plain; charset=UTF-8');
        }
    } else {
        if (!headers_sent()){
            header('Content-Type: text/html; charset=UTF-8');
        }
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