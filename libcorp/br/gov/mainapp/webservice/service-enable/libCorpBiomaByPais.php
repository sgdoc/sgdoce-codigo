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
    \br\gov\mainapp\application\libcorp\pais\valueObject\PaisValueObject,
    \br\gov\mainapp\application\libcorp\bioma\mvcb\business\BiomaBusiness;

/**
 * SISICMBio
 *
 * Módulo WebService - LIBCorp - BiomaBusiness::findByPais
 *
 * @package br.gov.icmbio.webservice
 * @subpackage services-available
 * @name libCorpBiomaByEstado
 * @author Fabio Lima <fabioolima@gmail.com>
 * @since 2015-01-02
 * @version $Id$
 */

$ICMBioWSservice->register(
        "libCorpBiomaByPais",
        array('sqPais' => 'xsd:integer'),
        array('return'   => "xsd:string"),
        $serviceUrl,
        FALSE,
        FALSE,
        FALSE,
        'Servi&ccedil;o respons&aacute;vel por efetuar pesquisa de Bioma por País'
);

function libCorpBiomaByPais($sqPais)
{
    try{
        $voPais = PaisValueObject::factory()->setSqPais($sqPais);
        $tmpResult = BiomaBusiness::factory()->findByPais($voPais);

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