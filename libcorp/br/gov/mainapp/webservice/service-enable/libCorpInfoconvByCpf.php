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

use \br\gov\sial\core\exception\SIALException,
    \br\gov\mainapp\application\infoconv\confirmDoc\mvcb\business\ConfirmDocBusiness,
    \br\gov\mainapp\application\infoconv\confirmDoc\valueObject\ConfirmDocValueObject,
    \br\gov\mainapp\webservice\util\Registry,
    \br\gov\sial\core\mvcb\business\exception\BusinessException;

/**
 * SISICMBio
 *
 * Módulo WebService - Infoconv - ConfirmDocBusiness::sourceCpf
 *
 * @package br.gov.icmbio.webservice
 * @subpackage services-available
 * @name libCorpInfoconvByCpf
 * @author Carlos Eduardo <carlos.santos.terceirizado@icmbio.gov.br>
 * @since 2015-05-08
 * @version $Id$
 */

$ICMBioWSservice->wsdl->addComplexType(
        'userCredential',
        'complexType',
        'struct',
        'all',
        '',
        array( 'sqUsuario'       => array('name' => 'sqUsuario'        ,'type' => 'xsd:integer'),
               'sgSistema'       => array('name' => 'sgSistema'        ,'type' => 'xsd:string'),
               'inPerfilExterno' => array('name' => 'inPerfilExterno'  ,'type' => 'xsd:boolean')
        )
);

$ICMBioWSservice->wsdl->addComplexType(
        'ctInfoconvByCPF',
        'complexType',
        'struct',
        'all',
        '',
        array('cpf'  => array('name' => 'nuCpf' ,'type' => 'xsd:string'))
);

$ICMBioWSservice->register(
        "libCorpInfoconvByCpf",
        array('ctInfoconvByCPF'  => 'tns:ctInfoconvByCPF', 'userCredential' => 'tns:userCredential'),
        array('return' => "xsd:string"),
        $serviceUrl,
        FALSE,
        FALSE,
        FALSE,
        'Obtem os dados obtidos pelo infoconv pelo CPF'
);

/**
 * libCorpInfoconvByCpf
 * @param type $params
 * @param type $userCredential
 * @return json
 */
function libCorpInfoconvByCpf( $ctInfoconvByCPF, $userCredential )
{

    $xmlResult = '<result><status>%s</status><errocode>%s</errocode><response>%s</response></result>';
    try{
        Registry::factory()->setCredential($userCredential);

        $nuDoc = isset($ctInfoconvByCPF['doc']) ? $ctInfoconvByCPF['doc'] : NULL;
        $nuCpf = isset($ctInfoconvByCPF['cpf']) ? $ctInfoconvByCPF['cpf'] : $nuDoc;

        BusinessException::throwsExceptionIfParamIsNull(!empty($nuCpf), "O campo CPF é de preenchimento obrigatório.");

        $voInfoconv = ConfirmDocValueObject::factory()->setDoc($nuCpf);

        $result = ConfirmDocBusiness::factory(NULL, 'libcorp')->sourceCpf($voInfoconv);

        foreach($result as $key=>$value) {
            unset($result[$key]);
            $newKey = preg_replace('/\s/i', '_', strtolower($key));
            $result[$newKey] = $value;
        }

        return sprintf($xmlResult, 'success', '00000', Registry::factory()->arrayToXml($result));
    } catch(\Exception $excp) {
        return sprintf($xmlResult, 'failure', $excp->getCode(), $excp->getMessage());
    }
}