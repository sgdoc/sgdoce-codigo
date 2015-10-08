<?php

$result = array(
    'sqSistema'    => NULL,
    'sgSistema'    => NULL,
    'noSistema'    => NULL,
    'txSistema'    => NULL,
    'nuCpf'        => NULL,
    'noUsuario'    => NULL,
    'sqPerfil'     => NULL,
    'noPerfil'     => NULL,
    'sqUnidadeOrg' => NULL,
    'noUnidadeOrg' => NULL,
    'error'        => NULL, 
);

try {

    // Define application environment
    defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


    chdir(dirname(__DIR__));
    require_once 'init_autoloader.php';
    require_once 'init_bootstrap.php';

    // initialize configs
    $options = $application->bootstrap('session')->getOptions();

    $domains = array();
    if (isset($options['resources']['container']['crossDomain']['allowed'])) {
        $domains = $options['resources']['container']['crossDomain']['allowed'];
    }

    if (isset($_SERVER['HTTP_ORIGIN'])) {
        foreach ($domains as $value) {
            if ($_SERVER['HTTP_ORIGIN'] == $value) {
                header('Access-Control-Allow-Origin: ' . $value);
                break;
            }
        }
    }

    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Max-Age: 86400');

    header('Content-Type: application/json');

    $userSessionKey = 'USER';
    if (isset($_SESSION[$userSessionKey])) {
        $session = $_SESSION[$userSessionKey];

        if (isset($session->sqSistema) && isset($session->sistemas)) {
            $result['sqSistema'] = $session->sqSistema;
            $result['noSistema'] = isset($session->sistemas[$session->sqSistema]['noSistema']) ?
                $session->sistemas[$session->sqSistema]['noSistema'] : NULL;
            $result['sgSistema'] = isset($session->sistemas[$session->sqSistema]['sgSistema']) ?
                $session->sistemas[$session->sqSistema]['sgSistema'] : NULL;
            $result['txSistema'] = isset($session->sistemas[$session->sqSistema]['txDescricao']) ?
                $session->sistemas[$session->sqSistema]['txDescricao'] : NULL;
        }

        if (isset($session->nuCpf) && isset($session->noUsuario)) {
            $result['nuCpf']     = $session->nuCpf;
            $result['noUsuario'] = $session->noUsuario;
        }

        if (isset($session->sqUnidadeOrg) && isset($session->noUnidadeOrg)) {
            $result['sqUnidadeOrg'] = $session->sqUnidadeOrg;
            $result['noUnidadeOrg'] = $session->noUnidadeOrg;
        }

        if (isset($session->sqPerfil) && isset($session->noPerfil)) {
            $result['sqPerfil'] = $session->sqPerfil;
            $result['noPerfil'] = $session->noPerfil;
        }
    } else {
        throw new \Exception('Não foi possível recuperar a sessão do usuário logado no SICA-e, tente efetuar o login novamente.');
    }

} catch (\Exception $exc) {
    $result['error'] = $exc->getMessage();
}

echo json_encode($result);