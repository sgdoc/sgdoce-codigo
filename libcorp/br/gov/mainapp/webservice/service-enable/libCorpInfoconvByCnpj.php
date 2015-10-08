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
    \br\gov\mainapp\application\infoconv\searchCnpj\valueObject\SearchCnpjValueObject,
    \br\gov\mainapp\webservice\util\Registry;

/**
 * SISICMBio
 *
 * Módulo WebService - Infoconv - ConfirmDocBusiness::sourceCnpj
 *
 * @package br.gov.icmbio.webservice
 * @subpackage services-available
 * @name libCorpInfoconvByCnpj
 * @author Carlos Eduardo <carlos.santos.terceirizado@icmbio.gov.br>
 * @since 2015-05-12
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
        'ctInfoconvByCNPJ',
        'complexType',
        'struct',
        'all',
        '',
        array('cnpj'  => array('name' => 'nuCnpj' ,'type' => 'xsd:string'))
);

$ICMBioWSservice->register(
        "libCorpInfoconvByCnpj",
        array('ctInfoconvByCNPJ'  => 'tns:ctInfoconvByCNPJ', 'userCredential' => 'tns:userCredential'),
        array('return' => "xsd:string"),
        $serviceUrl,
        FALSE,
        FALSE,
        FALSE,
        'Obtem os dados obtidos pelo infoconv pelo CNPJ'
);

/**
 *
 * @param type $params
 * @param type $userCredential
 * @return json
 */
function libCorpInfoconvByCnpj( $ctInfoconvByCNPJ, $userCredential )
{
    $xmlResult = '<result><status>%s</status><errocode>%s</errocode><response>%s</response></result>';
    try{
        Registry::factory()->setCredential($userCredential);

        $nuDoc = isset($ctInfoconvByCPF['doc']) ? $ctInfoconvByCPF['doc'] : NULL;
        $nuCnpj = isset($ctInfoconvByCNPJ['cnpj']) ? $ctInfoconvByCNPJ['cnpj'] : $nuDoc;

        $voInfoconv = SearchCnpjValueObject::factory()->setDoc($nuCnpj);

        $result  = ConfirmDocBusiness::factory(NULL, 'libcorp')->sourceCnpj( $voInfoconv );

        foreach($result as $key=>$value) {
            unset($result[$key]);
            $newKey = preg_replace('/\s/i', '_', strtolower($key));
            $result[$newKey] = $value;
        }

        return sprintf($xmlResult, 'success', '00000', Registry::factory()->arrayToXml($result));
    } catch(\Exception $excp) {
        return sprintf($xmlResult, 'failure', $excp->getCode(), $excp->getMessage());
    }
    die();
}