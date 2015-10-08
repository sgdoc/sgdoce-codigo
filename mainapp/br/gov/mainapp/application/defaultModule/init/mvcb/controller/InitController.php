<?php
/*
 * Copyright 2011 appdemo
 * */
namespace br\gov\mainapp\application\defaultModule\init\mvcb\controller;
use br\gov\sial\core\SIALApplication,
    br\gov\mainapp\library\mvcb\controller\ControllerAbstract;

/**
 * APPDemo
 *
 * @package com.appdemo.application.defaultModule.init.mvcb
 * @subpackage controller
 * @name InitController
 * @author j. augusto <augustowebd@gmail.com>
 * @since 2012-10-23
 * */
class InitController extends ControllerAbstract
{
    /**
     * Dominio default dos sistemas do ICMBio
     */
    const DEFAUL_DOMAIN = '.sisicmbio.icmbio.gov.br';

    /* Entrada principal do Sistema */
    public function defaultAction ()
    {
        # obtenho o VHost para efetuar o roteamento
        $systemName = $_SERVER['HTTP_HOST'];
        $systemName = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $systemName;

        # tenho o nome do sistema.
        $sysName    = array_pop(explode('.',basename($systemName,self::DEFAUL_DOMAIN)));
        header("Location: {$sysName}");
    }
}
