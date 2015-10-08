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
 * Módulo WebService - LIBCorp - PessoaJuridicaBusiness::savePessoaJuridica
 *
 * @package br.gov.icmbio.webservice
 * @subpackage services-available
 * @name libCorpSavePessoaJuridica
 * @author Fabio Lima <fabioolima@gmail.com>
 * @since 2012-06-20
 * @version $Id$
 */
use br\gov\mainapp\application\libcorp\pessoa\valueObject\PessoaValueObject,
    br\gov\mainapp\application\libcorp\pessoaJuridica\mvcb\business\PessoaJuridicaBusiness,
    br\gov\mainapp\application\libcorp\pessoaJuridica\valueObject\PessoaJuridicaValueObject,
    br\gov\mainapp\webservice\util\Registry;

$ICMBioWSservice->wsdl->addComplexType(
    'ctSavePessoaJuridica',
    'complexType',
    'struct',
    'all',
    '',
    array('noPessoa'              => array('name' => 'noPessoa'              ,'type' => 'xsd:string'  ),
          'dtAbertura'            => array('name' => 'dtAbertura'            ,'type' => 'xsd:string'  ),
          'sgEmpresa'             => array('name' => 'sgEmpresa'             ,'type' => 'xsd:string'  ),
          'noFantasia'            => array('name' => 'noFantasia'            ,'type' => 'xsd:string'  ),
          'nuCnpj'                => array('name' => 'nuCnpj'                ,'type' => 'xsd:string'  ),
          'stRegistroAtivo'       => array('name' => 'stRegistroAtivo'       ,'type' => 'xsd:boolean' ),
          'sqNaturezaJuridica'    => array('name' => 'sqNaturezaJuridica'    ,'type' => 'xsd:integer' ),
          'sqPessoaHierarquia'    => array('name' => 'sqPessoaHierarquia'    ,'type' => 'xsd:integer' ),
          'inTipoEstabelecimento' => array('name' => 'inTipoEstabelecimento' ,'type' => 'xsd:integer' ),
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
        "libCorpSavePessoaJuridica",
        array('ctSavePessoaJuridica' => 'tns:ctSavePessoaJuridica', 'userCredential' => 'tns:userCredential'),
        array('return'               => 'xsd:string'),
        $serviceUrl,
        FALSE,
        'rpc',
        'encoded',
        'Servi&ccedil;o respons&aacute;vel por cadastrar Pessoa Juridica'
);

function libCorpSavePessoaJuridica ($ctSavePessoaJuridica, $userCredential = NULL)
{
    Registry::factory()->setCredential($userCredential);

    $xmlResult = '<result><status>%s</status><errocode>%s</errocode><response>%s</response></result>';
    try{
        if (!isset($ctSavePessoaJuridica['inTipoEstabelecimento'])) {
            $ctSavePessoaJuridica['inTipoEstabelecimento'] = TRUE;
        }

        $voPessoa         = PessoaValueObject::factory()->loadData($ctSavePessoaJuridica);
        $voPessoaJuridica = PessoaJuridicaValueObject::factory()->loadData($ctSavePessoaJuridica);
        $voPessoaJuridica = PessoaJuridicaBusiness::factory(NULL, 'libcorp')->savePessoaJuridica($voPessoa,$voPessoaJuridica);

        return sprintf($xmlResult, 'success', '00000', $voPessoaJuridica->toXml());
    } catch(\Exception $excp) {
        return sprintf($xmlResult, 'failure', $excp->getCode(), $excp->getMessage());
    }
}
