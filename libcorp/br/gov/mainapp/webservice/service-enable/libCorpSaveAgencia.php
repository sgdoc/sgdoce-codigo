<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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

/**
 * SISICMBio
 *
 * Módulo WebService - LIBCorp - AgenciaBusiness::save
 *
 * @package br.gov.icmbio.webservice
 * @subpackage services-available
 * @name libCorpSaveAgencia
 * @author Fabio Lima <fabioolima@gmail.com>
 * @since 2014-12-19
 * @version $Id$
 */

use br\gov\mainapp\application\libcorp\agencia\mvcb\business\AgenciaBusiness,
    br\gov\mainapp\application\libcorp\agencia\valueObject\AgenciaValueObject,
    br\gov\mainapp\webservice\util\Registry;

$ICMBioWSservice->wsdl->addComplexType(
        'addAgencia',
        'complexType',
        'struct',
        'all',
        '',
        array('noAgencia'       => array('name' => 'noAgencia'       ,'type' => 'xsd:string'),
              'coAgencia'       => array('name' => 'coAgencia'       ,'type' => 'xsd:string'),
              'sqBanco'         => array('name' => 'sqBanco'         ,'type' => 'xsd:integer'),
              'coDigitoAgencia' => array('name' => 'coDigitoAgencia' ,'type' => 'xsd:integer')
        )
);

$ICMBioWSservice->wsdl->addComplexType(
        'userCredential',
        'complexType',
        'struct',
        'all',
        '',
        array('sqUsuario'       => array('name' => 'sqUsuario'        ,'type' => 'xsd:integer'),
               'sgSistema'       => array('name' => 'sgSistema'        ,'type' => 'xsd:string'),
               'inPerfilExterno' => array('name' => 'inPerfilExterno'  ,'type' => 'xsd:boolean')
        )
);

$ICMBioWSservice->register(
        "libCorpSaveAgencia",
        array('addAgencia'  => 'tns:addAgencia', 'userCredential' => 'tns:userCredential'),
        array('return' => 'xsd:string'),
        $serviceUrl,
        FALSE,
        'rpc',
        'encoded',
        'Servi&ccedil;o respons&aacute;vel por cadastrar Agencia'
);

function libCorpSaveAgencia($addAgencia, $userCredential = NULL)
{
    $xmlResult = '<result><status>%s</status><errocode>%s</errocode><response>%s</response></result>';
    try{
        Registry::factory()->setCredential($userCredential);

        $voAgencia = AgenciaValueObject::factory()->loadData($addAgencia);
        $voAgencia = AgenciaBusiness::factory()->saveAgencia($voAgencia);

        return sprintf($xmlResult,
                        'success',
                        '00000',
                        $voAgencia->toXml()
                       );
    } catch(\Exception $excp) {
        return sprintf($xmlResult,
                        'failure',
                        $excp->getCode(),
                        $excp->getMessage()
                       );
    }

}
