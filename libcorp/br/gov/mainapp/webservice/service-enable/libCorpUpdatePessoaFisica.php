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
 * Módulo WebService - LIBCorp - PessoaFisicaBusiness::updatePessoaFisica
 *
 * @package br.gov.icmbio.webservice
 * @subpackage services-available
 * @name libCorpUpdatePessoaFisica
 * @author Fabio Lima <fabioolima@gmail.com>
 * @since 2012-06-20
 * @version $Id$
 */

use br\gov\mainapp\application\libcorp\pessoa\valueObject\PessoaValueObject,
    br\gov\mainapp\application\libcorp\pessoaFisica\mvcb\business\PessoaFisicaBusiness,
    br\gov\mainapp\application\libcorp\pessoaFisica\valueObject\PessoaFisicaValueObject,
    br\gov\mainapp\webservice\util\Registry;

$ICMBioWSservice->wsdl->addComplexType(
        'ctPessoaFisicaUpdate',
        'complexType',
        'struct',
        'all',
        '',
        array('sqPessoa'         => array('name' => 'sqPessoa'         ,'type' => 'xsd:integer' ),
               'noPessoa'         => array('name' => 'noPessoa'         ,'type' => 'xsd:string'  ),
               'nuCpf'            => array('name' => 'nuCpf'            ,'type' => 'xsd:string'  ),
               'noProfissao'      => array('name' => 'noProfissao'      ,'type' => 'xsd:string'  ),
               'dtNascimento'     => array('name' => 'dtNascimento'     ,'type' => 'xsd:string'  ),
               'nuCurriculoLates' => array('name' => 'nuCurriculoLates' ,'type' => 'xsd:string'  ),
               'sgSexo'           => array('name' => 'sgSexo'           ,'type' => 'xsd:string'  ),
               'noPai'            => array('name' => 'noPai'            ,'type' => 'xsd:string'  ),
               'noMae'            => array('name' => 'noMae'            ,'type' => 'xsd:string'  ),
               'sqEstadoCivil'    => array('name' => 'sqEstadoCivil'    ,'type' => 'xsd:integer' ),
               'sqNaturalidade'   => array('name' => 'sqNaturalidade'   ,'type' => 'xsd:integer' ),
               'sqNacionalidade'  => array('name' => 'sqNacionalidade'  ,'type' => 'xsd:integer' ),
               'stRegistroAtivo' => array('name' => 'stRegistroAtivo' ,'type' => 'xsd:boolean'   )
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
        "libCorpUpdatePessoaFisica",
        array('ctPessoaFisicaUpdate' => 'tns:ctPessoaFisicaUpdate', 'userCredential' => 'tns:userCredential'),
        array('return' => 'xsd:string'),
        $serviceUrl,
        FALSE,
        'rpc',
        'encoded',
        'Servi&ccedil;o respons&aacute;vel por atualizar Pessoa Fisica'
);

function libCorpUpdatePessoaFisica ($ctPessoaFisicaUpdate, $userCredential = NULL)
{
    Registry::factory()->setCredential($userCredential);

    $xmlResult = '<result><status>%s</status><errocode>%s</errocode><response>%s</response></result>';
    try{
        $voPessoa       = PessoaValueObject::factory()->loadData($ctPessoaFisicaUpdate);
        $voPessoaFisica = PessoaFisicaValueObject::factory()->loadData($ctPessoaFisicaUpdate);
        $voPessoaFisica = PessoaFisicaBusiness::factory()->updatePessoaFisica($voPessoa,$voPessoaFisica);
        return sprintf($xmlResult, 'success', '00000',  $voPessoaFisica->toXml());
    } catch(\Exception $excp) {
        return sprintf($xmlResult, 'failure', $excp->getCode(), $excp->getMessage());
    }
}
