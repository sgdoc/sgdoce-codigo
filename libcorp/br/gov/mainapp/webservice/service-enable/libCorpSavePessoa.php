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
 * Módulo WebService - LIBCorp - PessoaBusiness::savePessoaFisica
 *
 * @package br.gov.icmbio.webservice
 * @subpackage services-available
 * @name libCorpSavePessoaFisica
 * @author Fabio Lima <fabioolima@gmail.com>
 * @since 2014-12-17
 */
use br\gov\mainapp\application\libcorp\pessoa\valueObject\PessoaValueObject,
    br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness,
    br\gov\mainapp\webservice\util\Registry;

const XML_RESULT = '<result><status>%s</status><errocode>%s</errocode><response>%s</response></result>';

$ICMBioWSservice->wsdl->addComplexType(
        'ctPessoaSave',
        'complexType',
        'struct',
        'all',
        '',
        array('noPessoa'        => array('name' => 'noPessoa'         ,'type' => 'xsd:string' ),
              'stRegistroAtivo'  => array('name' => 'stRegistroAtivo'  ,'type' => 'xsd:boolean'),
              'stUsuarioExterno' => array('name' => 'stUsuarioExterno' ,'type' => 'xsd:boolean'),
              'sqTipoPessoa'     => array('name' => 'sqTipoPessoa'     ,'type' => 'xsd:integer')
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
        "libCorpSavePessoa",
        array('ctPessoaSave' => 'tns:ctPessoaSave', 'userCredential' => 'tns:userCredential'),
        array('return' => 'xsd:string'),
        $serviceUrl,
        FALSE,
        'rpc',
        'encoded',
        'Servi&ccedil;o respons&aacute;vel por cadastrar Pessoa'
);

function libCorpSavePessoa ($ctPessoaSave, $userCredential = NULL)
{
    try{
        Registry::factory()->setCredential($userCredential);

        $voPessoa = PessoaValueObject::factory()->loadData($ctPessoaSave);
        PessoaBusiness::factory(NULL, 'libcorp')->save($voPessoa);

        return sprintf(XML_RESULT, 'success', '00000', $voPessoa->toXml());
    } catch(\Exception $excp) {
        return sprintf(XML_RESULT, 'failure', $excp->getCode(), $excp->getMessage());
    } 
}
