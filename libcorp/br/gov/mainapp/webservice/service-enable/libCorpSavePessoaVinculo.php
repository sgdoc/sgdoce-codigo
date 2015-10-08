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
 * Módulo WebService - LIBCorp - PessoaVinculoBusiness::save
 *
 * @package br.gov.icmbio.webservice
 * @subpackage services-available
 * @name libCorpSavePessoaVinculo
 * @author Fabio Lima <fabioolima@gmail.com>
 * @since 2014-12-19
 * @version $Id$
 */

use br\gov\mainapp\application\libcorp\pessoaVinculo\mvcb\business\PessoaVinculoBusiness,
    br\gov\mainapp\application\libcorp\pessoaVinculo\valueObject\PessoaVinculoValueObject,
    br\gov\mainapp\webservice\util\Registry;

$ICMBioWSservice->wsdl->addComplexType(
        'pessoaVinculo',
        'complexType',
        'struct',
        'all',
        '',
        array('sqPessoa'               => array('name' => 'sqPessoa'               ,'type' => 'xsd:integer'),
              'noCargo'                => array('name' => 'noCargo'                ,'type' => 'xsd:string'),
              'sqPessoaRelacionamento' => array('name' => 'sqPessoaRelacionamento' ,'type' => 'xsd:integer'),
              'sqTipoVinculo'          => array('name' => 'sqTipoVinculo'          ,'type' => 'xsd:integer'),
              'dtInicioVinculo'        => array('name' => 'dtInicioVinculo'        ,'type' => 'xsd:string'),
              'dtFimVinculo'           => array('name' => 'dtFimVinculo'           ,'type' => 'xsd:string'),
              'stRegistroAtivo'        => array('name' => 'stRegistroAtivo'        ,'type' => 'xsd:boolean')
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
        "libCorpSavePessoaVinculo",
        array('pessoaVinculo' => 'tns:pessoaVinculo', 'userCredential' => 'tns:userCredential'),
        array('return'        => 'xsd:string'),
        $serviceUrl,
        FALSE,
        'rpc',
        'encoded',
        'Servi&ccedil;o respons&aacute;vel por cadastrar PessoaVinculo'
);

function libCorpSavePessoaVinculo($pessoaVinculo, $userCredential = NULL)
{
    $xmlResult = '<result><status>%s</status><errocode>%s</errocode><response>%s</response></result>';
    try{
        Registry::factory()->setCredential($userCredential);

        $voPessoaVinculo = PessoaVinculoValueObject::factory()->loadData($pessoaVinculo);
        $voPessoaVinculo = PessoaVinculoBusiness::factory()->save($voPessoaVinculo);

        return sprintf($xmlResult,
                        'success',
                        '00000',
                        $voPessoaVinculo->toXml()
                       );
    } catch(\Exception $excp) {
        return sprintf($xmlResult,
                        'failure',
                        $excp->getCode(),
                        $excp->getMessage()
                       );
    }
}
