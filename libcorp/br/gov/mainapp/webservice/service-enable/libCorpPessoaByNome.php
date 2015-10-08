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

use \br\gov\sial\core\exception\IOException,
    \br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness,
    \br\gov\mainapp\application\libcorp\pessoa\valueObject\PessoaValueObject;

/**
 * SISICMBio
 *
 * Módulo WebService - LIBCorp - PessoaBusiness::findByNome
 *
 * @package br.gov.icmbio.webservice
 * @subpackage services-available
 * @name libCorpPessoaByNome
 * @author Fabio Lima <fabioolima@gmail.com>
 * @since 2014-12-19
 * @version $Id$
 */

$ICMBioWSservice->register(
        "libCorpPessoaByNome",
        array('noPessoa' => 'xsd:string'),
        array('return'   => "xsd:string"),
        $serviceUrl,
        FALSE,
        FALSE,
        FALSE,
        'Retorna os dados de Pessoa por Nome (ilike) (PessoaValueObject::noPessoa)'
);

function libCorpPessoaByNome($noPessoa)
{
    try{
        $voPessoa  = PessoaValueObject::factory()->setNoPessoa($noPessoa);
        $tmpResult = PessoaBusiness::factory()->findByNome($voPessoa);

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