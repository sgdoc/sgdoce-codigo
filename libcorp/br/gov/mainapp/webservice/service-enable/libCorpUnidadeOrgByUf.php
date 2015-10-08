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
use br\gov\sial\core\exception\IOException,
    br\gov\mainapp\application\libcorp\estado\valueObject\EstadoValueObject,
    br\gov\mainapp\application\libcorp\unidadeOrg\mvcb\business\UnidadeOrgBusiness;

/**
 * SISICMBio
 *
 * Módulo WebService - LIBCorp - UnidadeOrgBusiness::findByUf
 *
 * @package br.gov.icmbio.webservice
 * @subpackage services-available
 * @name libCorpUnidadeOrgByUf
 * @author Fabio Lima <fabioolima@gmail.com>
 * @since 2012-05-21
 * @version $Id$
 */
$ICMBioWSservice->register(
    "libCorpUnidadeOrgByUf",
        array('sqEstado' => 'xsd:string'),
        array('return'   => "xsd:string"),
       $serviceUrl,
       FALSE,
       FALSE,
       FALSE,
       'Efetua a busca de Unidades Organizacionais por UF (EstadoValueObject::sqEstado)'
);

function libCorpUnidadeOrgByUf($sqEstado)
{
    try{
        $voEstado   = EstadoValueObject::factory()->setSqEstado($sqEstado);
        $tmpResult  = UnidadeOrgBusiness::factory(NULL, 'libcorp')->findByUf($voEstado);

        $tmpTxt     = '<result>';
        foreach ($tmpResult as $result) {
            $tmpTxt .= "<unidade_org>{$result->toXml()}</unidade_org>";
        }
        $tmpTxt .= '</result>';

        return $tmpTxt;
    } catch(\Exception $excp) {
        throw new IOException($excp->getMessage());

    }
}