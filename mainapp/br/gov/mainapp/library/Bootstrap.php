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
namespace br\gov\mainapp\library;
use br\gov\sial\core\util\Registry,
    br\gov\sial\core\mvcb\controller\exception\ControllerException;

# inclusao do componente de redirecionamento
$RFlowPath = constant('__MAINAPPDOCS__')
           . str_replace(':', DIRECTORY_SEPARATOR, ':br:gov:mainapp:library:SISBioBridge:');

$RFlowPath .= 'RedirectFlow.php';
require_once $RFlowPath;

/**
 * SIAL
 *
 * @package com\appdemo
 * @subpackage library
 * @name Bootstrap
 * @author SIAL Generator
 * */
class Bootstrap extends \br\gov\sial\core\BootstrapAbstract
{
    public function controller ()
    {
        try {
            return parent::controller();
        } catch (ControllerException $cExc) {

            $flow = new \RedirectFlow(
                Registry::get('bootstrap')
                        ->config()
                        ->get('app.sisbio.redirectflow.target')
            );

            # o controle se pode ou nao enviar eh feito no
            # proprio componente RedirectFlow
            $flow->forward();
        }
    }
}