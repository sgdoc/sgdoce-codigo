<?php
/**
* Copyright 2012 do ICMBio
* Este arquivo é parte do programa SISICMBio
* O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
* dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
* (FSF); na versão 2 da Licença.
* Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA;
* sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR.
* Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
* Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
* junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
* endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
* 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA
*/
namespace Sgdoce\Model\Repository;

use Sgdoce\Model\Repository\ArtefatoVinculo as ArtefatoVinculoRepo;

/**
* SISICMBio
*
* Classe para Repository de ArtefatoImagem
*
* @package Model
* @subpackage Repository
* @name ArtefatoImagem
* @version 1.0.0
* @since 2015-01-30
*/
class ArtefatoImagem extends \Core_Model_Repository_Base
{

    public function findDocumentoSemImagem ($limit=100)
    {

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato', 'sqArtefato', 'integer');

        /**
         * somente documento que não possua vinculo com ninguem
         */
        $strQuery = sprintf(
                            'SELECT DISTINCT t.*
                               FROM(SELECT a.sq_artefato
                                      FROM artefato AS a
                                 LEFT JOIN (
                                            SELECT DISTINCT ON (ai.sq_artefato)
                                                   ai.sq_artefato,
                                                   ai.sq_artefato_imagem
                                              FROM artefato_imagem ai
                                             ORDER BY ai.sq_artefato, ai.dt_operacao DESC, ai.sq_artefato_imagem DESC
                                 ) uia ON a.sq_artefato = uia.sq_artefato
                                 LEFT JOIN tipo_artefato_assunto     AS taa USING(sq_tipo_artefato_assunto)
                                 LEFT JOIN artefato_processo         AS ap  ON a.sq_artefato = ap.sq_artefato
                                 LEFT JOIN tmp_artefato_migration    as tmp ON a.sq_artefato = tmp.sq_artefato
                                     WHERE taa.sq_tipo_artefato = %1$d
                                       AND ap.sq_artefato         IS NULL
                                       AND uia.sq_artefato_imagem IS NULL
                                       AND tmp.sq_artefato        IS NULL
                                   ) t
                               JOIN vw_imagem_sgdoc_fisico AS isf USING(sq_artefato)
                           ORDER BY sq_artefato DESC
                           LIMIT %2$d'

              , \Core_Configuration::getSgdoceTipoArtefatoDocumento()
              , $limit
            );

        return
        $this->_em
             ->createNativeQuery($strQuery, $rsm)->useResultCache(false)->getArrayResult();
    }
}