<?php

/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

namespace Sgdoce\Model\Repository;

/**
 * SISICMBio
 *
 * Classe para Repository de LoteEtiqueta
 *
 * @package      Model
 * @subpackage   Repository
 * @name         LoteEtiqueta
 * @since        2014-10-21
 */
class LoteEtiqueta extends \Core_Model_Repository_Base
{
    /**
     * Realiza busca para grid
     *
     * @param \Core_Dto_Search $search
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGrid(\Core_Dto_Search $search)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $query = $queryBuilder->select(
                                'te.noTipoEtiqueta',
                                'le.sqLoteEtiqueta',
                                'le.nuInicial',
                                'le.nuFinal',
                                'le.nuAno',
                                'te.sqTipoEtiqueta',
                                'uo.noUnidadeOrg',
                                'uo.sqUnidadeOrg',
                                'le.inLoteComNupSiorg',
                                'CASE WHEN le.dtCriacao IS NULL THEN 0 ELSE edl.nuQuantidadeDisponivel END nuQuantidadeDisponivel',
                                'le.dtCriacao'
                            )
                        ->from('app:LoteEtiqueta', 'le')
                        ->innerJoin('le.sqTipoEtiqueta', 'te')
                        ->innerJoin('le.sqUnidadeOrg', 'uo')
                        ->innerJoin('le.sqEtiquetaDisponivelLote', 'edl');

        if ($search->getSqTipoEtiqueta()) {
            $query->andWhere($queryBuilder->expr()->eq('le.sqTipoEtiqueta', ':sqTipoEtiqueta'))
                  ->setParameter('sqTipoEtiqueta', $search->getSqTipoEtiqueta());
        }

        if ($search->getSqUnidadeOrg()) {
            $query->andWhere($queryBuilder->expr()->eq('le.sqUnidadeOrg', ':sqUnidadeOrg'))
                  ->setParameter('sqUnidadeOrg', $search->getSqUnidadeOrg());
        }

        /**
         * values:
         * '' ou 'T' => todos
         * '0' => sem nup
         * '1' => com nup
         */
        $inLoteComNupSiorg = $search->getInLoteComNupSiorg();
        if(is_numeric($inLoteComNupSiorg)){
            if((boolean) $inLoteComNupSiorg){
                $query->andWhere('le.inLoteComNupSiorg = TRUE' );
            }else{
                $query->andWhere('le.inLoteComNupSiorg = FALSE' );
            }
        }
        
        $query->orderBy('le.sqLoteEtiqueta','DESC');

        return $query;
    }

    /**
     *
     * @param \Core_Dto_Search $search
     * @return array
     */
    public function getUltimoLotePessoaUnidadeOrg(\Core_Dto_Search $search)
    {
        $query = $this->_em->createQueryBuilder();
        $query
            ->select('le.sqLoteEtiqueta',
                    'le.nuInicial',
                    'le.nuFinal',
                    'le.nuInicialNupSiorg',
                    'le.nuFinalNupSiorg',
                    'uo.noUnidadeOrg',
                    'edl.nuQuantidadeDisponivel',
                    'te.noTipoEtiqueta')
            ->from('app:LoteEtiqueta', 'le')
            ->innerJoin('le.sqUnidadeOrg', 'uo')
            ->innerJoin('le.sqTipoEtiqueta', 'te')
            ->innerJoin('le.sqEtiquetaDisponivelLote', 'edl')
            ->andWhere($query->expr()->eq('le.nuAno', ':nuAno'))
                //lotes com dt_criacao nullo são lotes da migração
            ->andWhere($query->expr()->isNotNull('le.dtCriacao'))

            ->orderBy('le.sqLoteEtiqueta','DESC')
            ->setParameter('nuAno', $search->getNuAno())
            ->setMaxResults(1); //limit

        $searchApi = $search->getApi();

        if ($search->getSqUnidadeOrg()) {
            if ($search->getSqUnidadeOrg() instanceof \Sgdoce\Model\Entity\VwUnidadeOrg) {
                $sqUnidadeOrg = $search->getSqUnidadeOrg()->getSqUnidadeOrg();
            }else{
                $sqUnidadeOrg = $search->getSqUnidadeOrg();
            }
            $query->andWhere($query->expr()->eq('le.sqUnidadeOrg',':sqUnidadeOrg'))
                  ->setParameter('sqUnidadeOrg', $sqUnidadeOrg);
        }

        if ($search->getSqTipoEtiqueta()) {
            if ($search->getSqTipoEtiqueta() instanceof \Sgdoce\Model\Entity\TipoEtiqueta) {
                $sqTipoEtiqueta = $search->getSqTipoEtiqueta()->getSqTipoEtiqueta();
            }else{
                $sqTipoEtiqueta = $search->getSqTipoEtiqueta();
            }
            $query->andWhere($query->expr()->eq('le.sqTipoEtiqueta',':sqTipoEtiqueta'))
                  ->setParameter('sqTipoEtiqueta', $sqTipoEtiqueta);
        }

        if (isset($searchApi['inLoteComNupSiorg'])) {
            $inLoteComNup = $search->getInLoteComNupSiorg();
            if($inLoteComNup){
                $query->andWhere('le.inLoteComNupSiorg = TRUE');
            }else{
                $query->andWhere('le.inLoteComNupSiorg = FALSE');
            }
        }

        return $query->getQuery()->execute();
    }

    public function listEtiquetaImprimir(\Core_Dto_Search $dto)
    {
        $sql = "SELECT * FROM(
                    SELECT ens.nu_etiqueta
                           ,ens.nu_nup_siorg
                           ,ens.sq_lote_etiqueta
                           ,le.nu_ano
                           ,le.in_lote_com_nup_siorg
                      FROM etiqueta_nup_siorg AS ens
                      JOIN lote_etiqueta AS le USING(sq_lote_etiqueta)
                     WHERE ens.sq_lote_etiqueta = :sqLoteEtiqueta
                    EXCEPT
                    SELECT etiqueta_nup_siorg.nu_etiqueta
                           ,etiqueta_nup_siorg.nu_nup_siorg
                           ,etiqueta_nup_siorg.sq_lote_etiqueta
                           ,lote_etiqueta.nu_ano
                           ,lote_etiqueta.in_lote_com_nup_siorg
                      FROM etiqueta_nup_siorg
                      JOIN lote_etiqueta USING(sq_lote_etiqueta)
                      JOIN etiquetas_uso
                        ON etiquetas_uso.sq_lote_etiqueta = etiqueta_nup_siorg.sq_lote_etiqueta
                       AND etiquetas_uso.nu_etiqueta = etiqueta_nup_siorg.nu_etiqueta
                     WHERE etiqueta_nup_siorg.sq_lote_etiqueta = :sqLoteEtiqueta
                ) tb
                ORDER BY nu_etiqueta";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('nu_etiqueta'          , 'nuEtiqueta');
        $rsm->addScalarResult('nu_nup_siorg'         , 'nuNupSiorg');
        $rsm->addScalarResult('nu_ano'               , 'nuAno');
        $rsm->addScalarResult('sq_lote_etiqueta'     , 'sqLoteEtiqueta');
        $rsm->addScalarResult('in_lote_com_nup_siorg', 'inLoteComNupSiorg');

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqLoteEtiqueta', $dto->getSqLoteEtiqueta());

        return $query->getScalarResult();
    }

    public function listEtiquetasPorNumero(\Core_Dto_Search $objDTOSearch, $limit = 30)
    {
    	if($objDTOSearch->getNuEtiqueta() == ""){
            return array();
    	}

        $nupCondition = "le.in_lote_com_nup_siorg";

        if (!$objDTOSearch->getInLoteComNupSiorg()) {
            $nupCondition = "NOT le.in_lote_com_nup_siorg";
        }

    	$strSql = "SELECT DISTINCT formata_numero_digital(eu.nu_etiqueta) as nu_etiqueta, ens.nu_nup_siorg
                     FROM sgdoce.etiquetas_uso AS eu
                     JOIN sgdoce.etiqueta_nup_siorg AS ens
                       ON ens.sq_lote_etiqueta = eu.sq_lote_etiqueta
                      AND ens.nu_etiqueta = eu.nu_etiqueta
                     JOIN sgdoce.lote_etiqueta AS le ON le.sq_lote_etiqueta = eu.sq_lote_etiqueta
                     JOIN sgdoce.tipo_etiqueta AS te ON te.sq_tipo_etiqueta = le.sq_tipo_etiqueta
                    WHERE te.sq_tipo_etiqueta = " . \Core_Configuration::getSgdoceTipoEtiquetaFisica() . "
                      AND formata_numero_digital(eu.nu_etiqueta) ILIKE '%" . $objDTOSearch->getNuEtiqueta() . "%'
                      AND {$nupCondition}
                    ORDER BY nu_etiqueta";
                      
        if( $limit > 0 ) {
            $strSql .= " LIMIT ". $limit;
        }

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('nu_etiqueta' , 'nuEtiqueta');
        $rsm->addScalarResult('nu_nup_siorg', 'nuNupSiorg');
        
        $objQuery = $this->_em->createNativeQuery($strSql, $rsm);

        return $objQuery->getScalarResult();

    }

    public function getNextDigitalNumber($sqLoteEtiqueta)
    {
        $sql = sprintf("WITH etiquetas_lote AS(
                            SELECT et.sq_lote_etiqueta,
                                   et.nu_ano,
                                   LPAD(et.nu_ano::VARCHAR||LPAD(et.nu_etiqueta::VARCHAR,7,'0'),11,'0')::BIGINT AS nu_etiqueta
                            FROM (
                                SELECT l.sq_lote_etiqueta,
                                       l.nu_ano,
                                       generate_series(nu_inicial, nu_final) nu_etiqueta
                                FROM sgdoce.lote_etiqueta l
                                JOIN sgdoce.etiqueta_nup_siorg AS ens USING(sq_lote_etiqueta)
                               WHERE l.sq_lote_etiqueta = %d
                       ) AS et
                    )
                    SELECT l.sq_lote_etiqueta,
                           l.nu_ano,
                           min(l.nu_etiqueta) AS nu_etiqueta
                    FROM etiquetas_lote l
                    LEFT JOIN sgdoce.etiquetas_uso AS eu
                      ON eu.sq_lote_etiqueta = l.sq_lote_etiqueta
                     AND eu.nu_etiqueta = l.nu_etiqueta
                   WHERE eu.sq_lote_etiqueta IS NULL
                   GROUP BY l.sq_lote_etiqueta, l.nu_ano", $sqLoteEtiqueta);

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('nu_etiqueta', 'nuEtiqueta');
        $rsm->addScalarResult('nu_ano', 'nuAno');
        $rsm->addScalarResult('sq_lote_etiqueta', 'sqLoteEtiqueta');

        $query = $this->_em->createNativeQuery($sql, $rsm);

        return $query->getScalarResult();
    }

    public function recuperaNumeroLoteEtiquetaEletronica(\Core_Dto_Search $search)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('le.sqLoteEtiqueta')
            ->from('app:LoteEtiqueta', 'le')
            ->where($qb->expr()->eq('le.sqUnidadeOrg', ':sqUnidadeOrg'))
            ->andWhere($qb->expr()->eq('le.nuAno',':nuAno'))
            ->andWhere($qb->expr()->eq('le.sqTipoEtiqueta',':sqTipoEtiqueta'))
            ->setParameter('sqUnidadeOrg'  , $search->getSqUnidadeOrg())
            ->setParameter('nuAno'         , $search->getNuAno())
            ->setParameter('sqTipoEtiqueta', \Core_Configuration::getSgdoceTipoEtiquetaEletronica());

        $lote = $qb->getQuery()->getArrayResult();

        $sqLoteEtiqueta = null;
        if ($lote) {
            $sqLoteEtiqueta = $lote[0]['sqLoteEtiqueta'];
        }

        return $sqLoteEtiqueta;
    }

    public function recuperaNumeroLoteEtiqueta(\Core_Dto_Search $search)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('le.sqLoteEtiqueta')
            ->from('app:LoteEtiqueta', 'le')
            ->where($qb->expr()->eq('le.sqUnidadeOrg', ':sqUnidadeOrg'))
            ->andWhere($qb->expr()->eq('le.nuAno',':nuAno'))
            ->andWhere($qb->expr()->between(':nuDigital', 'le.nuInicial', 'le.nuFinal'))
            ->andWhere($qb->expr()->eq('le.sqTipoEtiqueta',':sqTipoEtiqueta'))
            ->setParameter('nuDigital'     , $search->getNuDigital())
            ->setParameter('sqUnidadeOrg'  , $search->getSqUnidadeOrg())
            ->setParameter('nuAno'         , $search->getNuAno())
            ->setParameter('sqTipoEtiqueta', \Core_Configuration::getSgdoceTipoEtiquetaFisica());

        $lote = $qb->getQuery()->getArrayResult();

        $sqLoteEtiqueta = null;
        if ($lote) {
            $sqLoteEtiqueta = $lote[0]['sqLoteEtiqueta'];
        }

        return $sqLoteEtiqueta;
    }

    /**
     *
     *
     * @param \Core_Dto_Search $search
     * @return boolean
     */
    public function verificaLiberacaoDigital(\Core_Dto_Search $search )
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('le.sqLoteEtiqueta')
            ->from('app:LoteEtiqueta', 'le')
            ->where($qb->expr()->eq('le.sqUnidadeOrg', ':sqUnidadeOrg'))
            ->andWhere($qb->expr()->eq('le.nuAno',':nuAno'))
            ->andWhere($qb->expr()->between(':nuSequencialDigital', 'le.nuInicial', 'le.nuFinal'))
            ->andWhere($qb->expr()->eq('le.sqTipoEtiqueta',':sqTipoEtiqueta'))
            ->orderBy('le.sqLoteEtiqueta','DESC')

            ->setParameter('nuSequencialDigital', $search->getNuSequencialDigital())
            ->setParameter('sqUnidadeOrg'       , $search->getSqUnidadeOrg())
            ->setParameter('nuAno'              , $search->getNuAno())
            ->setParameter('sqTipoEtiqueta'     , \Core_Configuration::getSgdoceTipoEtiquetaFisica());

        return count($qb->getQuery()->execute()) > 0;
    }

    /**
     *
     *
     * @param \Core_Dto_Search $search
     * @return boolean
     */
    public function verificaLiberacaoDigitalEletronica(\Core_Dto_Search $search )
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('le.sqLoteEtiqueta','edl.nuQuantidadeDisponivel')
            ->from('app:LoteEtiqueta', 'le')
            ->innerJoin('le.sqEtiquetaDisponivelLote', 'edl')
            ->where($qb->expr()->eq('le.sqUnidadeOrg', ':sqUnidadeOrg'))
            ->andWhere($qb->expr()->eq('le.nuAno',':nuAno'))
            ->andWhere($qb->expr()->eq('le.sqTipoEtiqueta',\Core_Configuration::getSgdoceTipoEtiquetaEletronica()))
            ->orderBy('le.sqLoteEtiqueta','ASC') //ordena ASC para pegar possiveis lotes antigos primeiro

            ->setParameter('sqUnidadeOrg', $search->getSqUnidadeOrg())
            ->setParameter('nuAno'       , $search->getNuAno());

        $return    = false;
        $arrResult = $qb->getQuery()->execute();

        foreach($arrResult as $lote) {
            //existe um lote
            if ($lote['nuQuantidadeDisponivel'] > 0) {
                $return = true;
                break;
            }
        }

        return $return;
    }

    public function listSeries(\Core_Dto_Search $search)
    {
        $sql = "WITH etiquetas_lote AS(
                        SELECT et.sq_lote_etiqueta
                               ,et.nu_ano
                               ,cast(LPAD(CAST(et.nu_ano as VARCHAR)||LPAD(CAST(et.nu_etiqueta as VARCHAR),7,'0'),11,'0') as BIGINT) AS nu_etiqueta

                               ,CASE WHEN et.seq_nup_siorg IS NOT NULL THEN
                                    et.sq_unidade_siorg || LPAD(CAST(et.seq_nup_siorg as VARCHAR),8,'0') || et.nu_ano
                                ELSE
                                    NULL
                                END AS nu_nup_siorg_sem_dv
                        FROM (
                            SELECT l.sq_lote_etiqueta
                                   ,l.nu_ano
                                   ,generate_series(nu_inicial, nu_final) nu_etiqueta
                                   ,CASE WHEN l.in_lote_com_nup_siorg THEN
                                        generate_series(nu_inicial_nup_siorg, nu_final_nup_siorg)
                                    ELSE
                                        NULL
                                    END AS seq_nup_siorg
                                   ,LPAD(:coSiorg,7,'0') AS sq_unidade_siorg

                            FROM sgdoce.lote_etiqueta l
                           WHERE l.sq_lote_etiqueta = :sqLoteEtiqueta
                   ) AS et
                )
                SELECT *
                  FROM etiquetas_lote l
                 ORDER BY nu_etiqueta";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('sq_lote_etiqueta'   , 'sqLoteEtiqueta');
        $rsm->addScalarResult('nu_ano'             , 'nuAno');
        $rsm->addScalarResult('nu_etiqueta'        , 'nuEtiqueta');
        $rsm->addScalarResult('nu_nup_siorg_sem_dv', 'nuNupSiorgSemDv');

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqLoteEtiqueta', $search->getSqLoteEtiqueta());
        $query->setParameter('coSiorg', $search->getCoSiorg());

        return $query->getScalarResult();
    }
}
