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
    br\gov\mainapp\application\libcorp\tipoDocumento\mvcb\business\TipoDocumentoBusiness;

/**
 * SISICMBio
 *
 * Módulo WebService - LIBCorp - TipoDocumentoBusiness::find
 *
 * @package br.gov.icmbio.webservice
 * @subpackage services-available
 * @name libCorpTipoDocumentoById
 * @author Fabio Lima <fabioolima@gmail.com>
 * @since 2012-06-04
 * @version $Id$
 */

$ICMBioWSservice->register(
        "libCorpTipoDocumentoById",
        array('sqTipoDocumento' => 'xsd:integer'),
        array('return'          => "xsd:string"),
        $serviceUrl,
        FALSE,
        FALSE,
        FALSE,
        'Servi&ccedil;o respons&aacute;vel por efetuar pesquisa de Tipos de Documentos por identificador'
);

function libCorpTipoDocumentoById ($sqTipoDocumento)
{
    try{
        $result  = TipoDocumentoBusiness::factory(NULL, 'libcorp')->find($sqTipoDocumento);
        $result  = !$result->isEmpty() ? $result->toXml() : NULL;

        return  sprintf('<%1$s>%2$s</%1$s>', 'result', $result);
    } catch(\Exception $excp) {
        throw new IOException($excp->getMessage());
    }
}