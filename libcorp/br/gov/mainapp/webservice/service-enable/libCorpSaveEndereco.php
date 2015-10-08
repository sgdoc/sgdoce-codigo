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
 * Módulo WebService - LIBCorp - EnderecoBusiness::save
 *
 * @package br.gov.icmbio.webservice
 * @subpackage services-available
 * @name libCorpSaveEndereco
 * @author Fabio Lima <fabioolima@gmail.com>
 * @since 2014-12-19
 * @version $Id$
 */
use br\gov\mainapp\application\libcorp\endereco\mvcb\business\EnderecoBusiness,
    br\gov\mainapp\application\libcorp\endereco\valueObject\EnderecoValueObject,
    br\gov\mainapp\webservice\util\Registry;

$ICMBioWSservice->wsdl->addComplexType(
        'endereco',
        'complexType',
        'struct',
        'all',
        '',
        array('sqMunicipio'       => array('name' => 'sqMunicipio'       ,'type' => 'xsd:integer'),
              'sqTipoEndereco'    => array('name' => 'sqTipoEndereco'    ,'type' => 'xsd:integer'),
              'sqCep'             => array('name' => 'sqCep'             ,'type' => 'xsd:integer'),
              'sqPessoa'          => array('name' => 'sqPessoa'          ,'type' => 'xsd:integer'),
              'noBairro'          => array('name' => 'noBairro'          ,'type' => 'xsd:string'),
              'txEndereco'        => array('name' => 'txEndereco'        ,'type' => 'xsd:string'),
              'nuEndereco'        => array('name' => 'nuEndereco'        ,'type' => 'xsd:string'),
              'txComplemento'     => array('name' => 'txComplemento'     ,'type' => 'xsd:string'),
              'inCorrespondencia' => array('name' => 'inCorrespondencia' ,'type' => 'xsd:boolean')
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
        "libCorpSaveEndereco",
        array('endereco' => 'tns:endereco', 'userCredential' => 'tns:userCredential'),
        array('return'   => 'xsd:string'),
        $serviceUrl,
        FALSE,
        'rpc',
        'encoded',
        'Servi&ccedil;o respons&aacute;vel por cadastrar Endereços'
);

function libCorpSaveEndereco($endereco, $userCredential = NULL)
{
    $xmlResult = '<result><status>%s</status><errocode>%s</errocode><response>%s</response></result>';
    try{
        Registry::factory()->setCredential($userCredential);

        $voEndereco = EnderecoValueObject::factory()->loadData($endereco);
        $voEndereco = EnderecoBusiness::factory()->save($voEndereco);

        return sprintf($xmlResult,
                       'success'
                       ,'000000' 
                       ,$voEndereco->toXml()
                      );
    } catch(\Exception $excp) {
        return sprintf($xmlResult,
                       'failure'
                       ,$excp->getCode()
                       ,$excp->getMessage()
                      );
    }
}
