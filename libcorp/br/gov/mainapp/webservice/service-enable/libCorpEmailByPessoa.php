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
 * Módulo WebService - LIBCorp - EmailBusiness::findByID
 *
 * @package br.gov.icmbio.webservice
 * @subpackage services-available
 * @name libCorpEmailByPessoa
 * @author Sósthenes Neto <sosthenes.neto.terceirizado@icmbio.gov.br>
 * @since 2015-03-20
 * @version $Id$
 */
use br\gov\mainapp\application\libcorp\email\valueObject\EmailValueObject,
    br\gov\mainapp\application\libcorp\email\mvcb\business\EmailBusiness,
    br\gov\sial\core\exception\IOException;

$ICMBioWSservice->register(
        "libCorpEmailByPessoa",
        array('sqPessoa'  => 'xsd:integer'),
        array('return' => "xsd:string"),
        $serviceUrl,
        FALSE,
        FALSE,
        FALSE,
        'Recupera os emails do ID informado (EmailValueObject::sqPessoa)'
);

function libCorpEmailByPessoa($sqPessoa)
{
    try{
        $voEmail = EmailValueObject::factory()->setSqPessoa($sqPessoa);
        $tmpResult = EmailBusiness::factory()->findByParam($voEmail);

        $tmpTxt = '<result>';
        foreach ($tmpResult as $result) {
            $tmpTxt .= $result->toXml();
        }
        $tmpTxt .= '</result>';

        return $tmpTxt;
    } catch(\Exception $excp) {
        throw new IOException($excp->getMessage());
    }
}