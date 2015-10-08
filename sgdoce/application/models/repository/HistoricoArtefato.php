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
 * Classe para Repository de HistoricoArtefato
 *
 * @package      Model
 * @subpackage   Repository
 * @name         HistoricoArtefato
 * @version      1.0.0
 * @since        2012-11-20
 */
class HistoricoArtefato extends \Core_Model_Repository_Base
{

    /**
     * Constante para receber o valor zero
     * @var    integer
     * @name   ZER
     */
    const ZER = 0;

    /**
     * Constante para receber o valor um
     * @var    integer
     * @name   UNIC
     */
    const UNIC = 1;

    /**
     * Variavel para receber a entidade pai referente a esta classe
     * @var    string
     * @access private
     * @name   $_enName
     */
    private $_enName = 'app:HistoricoArtefato';

    /**
     * Obtém o penultimo historico de uma minuta
     * @param \Core_Dto_Entity $dto
     * @return array
     */
    public function getPenultimateHistArt (\Core_Dto_Entity $dto)
    {

        $queryBuilder = $this->_em->createQueryBuilder()
                ->select('ha.sqHistoricoArtefato, a.sqArtefato, sa.sqStatusArtefato, p.sqPessoa,
                                        o.sqOcorrencia ,un.sqUnidadeOrg')
                ->from('app:HistoricoArtefato', 'ha')
                ->innerJoin('ha.sqArtefato', 'a')
                ->innerJoin('ha.sqStatusArtefato', 'sa')
                ->innerJoin('ha.sqUnidadeOrg', 'un')
                ->innerJoin('ha.sqPessoa', 'p')
                ->innerJoin('ha.sqOcorrencia', 'o')
                ->andWhere('ha.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $dto->getSqArtefato()->getSqArtefato())
                ->andWhere('ha.sqHistoricoArtefato < :sqHistoricoArtefato')
                ->setParameter('sqHistoricoArtefato', $dto->getSqHistoricoArtefato())
                ->orderBy('ha.sqHistoricoArtefato', 'DESC')
                ->setMaxResults(self::UNIC)
                ->getQuery()
                ->execute();

        $result = NULL;
        if (!empty($queryBuilder)) {
            $result = $queryBuilder[self::ZER];
        }

        return $result;
    }

    /**
     * Obtém dados anterior da minuta
     * @param \Core_Dto_Entity $dto
     * @return array
     */
    public function getDataHistoricoEnvioAnterior (\Core_Dto_Entity $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
                ->select('p.sqPessoa, p.noPessoa, ha.sqHistoricoArtefato, a.sqArtefato, uo.sqUnidadeOrg')
                ->from($this->_enName, 'ha')
                ->innerJoin('ha.sqPessoa', 'p')
                ->innerJoin('ha.sqArtefato', 'a')
                ->innerJoin('ha.sqUnidadeOrg', 'uo')
                ->andWhere('ha.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $dto->getSqArtefato()->getSqArtefato())
                ->andWhere('p.sqPessoa <> :sqPessoa')
                ->setParameter('sqPessoa', $dto->getSqPessoa()->getSqPessoa())
                ->orderBy('ha.sqHistoricoArtefato', 'DESC')
                ->setMaxResults(self::UNIC)
                ->getQuery()
                ->execute();

        $result = NULL;
        if (!empty($queryBuilder)) {
            $result = $queryBuilder[self::ZER];
        }

        return $result;
    }

    /**
     * método que retorna dados para grid
     * @param \Core_Dto_Abstract $dto
     * @return \Doctrine\ORM\NativeQuery
     */
    public function listGridHistorico (\Core_Dto_Search $dto)
    {

        $configs = \Core_Registry::get('configs');
        $dataEntradaProducao = $configs['dataEntradaProducao'];

        $strSql = "WITH
                    artef AS (
                        select {$dto->getSqArtefato()} as sq_artefato
                    ),
                    hist AS (
                        SELECT  tx_descricao_operacao AS tx_operacao
                               ,to_char(dt_ocorrencia,'dd/mm/yyyy HH24:MI:SS') AS dt_operacao
                               ,dt_ocorrencia AS dt_ordenacao
                               ,p.no_pessoa
                               ,uo.sg_unidade_org AS no_unidade_org
                          FROM historico_artefato ha
                          JOIN artef             ON ha.sq_artefato = artef.sq_artefato
                          JOIN vw_pessoa       p USING(sq_pessoa)
                          JOIN vw_unidade_org uo ON ha.sq_unidade_org = uo.sq_pessoa
                         WHERE ha.dt_ocorrencia >= :dataEntradaProducao
                    ),
                    tramite AS (
                        SELECT  CASE WHEN ta.sq_pessoa_destino_interno IS NULL THEN
                                    'Tramitado para ' || pesDest.no_pessoa ||' em ' || to_char(ta.dt_tramite,'dd/mm/yyyy HH24:MI:SS')
                                ELSE
                                    'Tramitado para ' || pesDest.no_pessoa || ' [' || pesDestInt.no_pessoa || ']' || ' em ' || to_char(ta.dt_tramite,'dd/mm/yyyy HH24:MI:SS')
                                END AS tx_operacao
                               ,to_char(ta.dt_tramite,'dd/mm/yyyy HH24:MI:SS') AS dt_operacao
                               ,ta.dt_tramite AS dt_ordenacao
                               ,pes.no_pessoa
                               ,uo.sg_unidade_org AS no_unidade_org
                          FROM tramite_artefato ta
                          JOIN artef                ON artef.sq_artefato            = ta.sq_artefato
                          JOIN vw_pessoa        pes ON pes.sq_pessoa                = ta.sq_pessoa_tramite
                          JOIN vw_unidade_org    uo ON uo.sq_pessoa                 = ta.sq_unidade_org_tramite
                          JOIN vw_pessoa    pesDest ON ta.sq_pessoa_destino         = pesDest.sq_pessoa
                     LEFT JOIN vw_pessoa pesDestInt ON ta.sq_pessoa_destino_interno = pesDestInt.sq_pessoa
                         WHERE ta.dt_tramite is not null
                           AND ta.nu_tramite <> 1
                           AND ta.sq_status_tramite IN (1,2,3)
                           AND ta.dt_tramite >= :dataEntradaProducao
                    ),
                    recebimento AS(
                        SELECT 'Trâmite recebido em ' || to_char(ta.dt_recebimento,'dd/mm/yyyy HH24:MI:SS') AS tx_operacao
                               ,to_char(ta.dt_recebimento,'dd/mm/yyyy HH24:MI:SS') AS dt_operacao
                               ,ta.dt_recebimento AS dt_ordenacao
                               ,pes.no_pessoa
                               ,uo.sg_unidade_org AS no_unidade_org
                          FROM tramite_artefato ta
                          JOIN artef             ON artef.sq_artefato = ta.sq_artefato
                          JOIN vw_pessoa     pes ON pes.sq_pessoa = ta.sq_pessoa_recebimento
                          JOIN vw_unidade_org uo ON uo.sq_pessoa = ta.sq_pessoa_destino
                         WHERE ta.dt_recebimento IS NOT NULL
                           AND ta.nu_tramite <> 1
                           AND ta.dt_tramite >= :dataEntradaProducao
                    ),
                    cancelamento AS (
                        SELECT
                               'Trâmite cancelado em ' || to_char(ta.dt_cancelamento,'dd/mm/yyyy HH24:MI:SS') AS tx_operacao
                               ,to_char(ta.dt_cancelamento,'dd/mm/yyyy HH24:MI:SS') AS dt_operacao
                               ,ta.dt_cancelamento AS dt_ordenacao
                               ,pes.no_pessoa
                               ,uo.sg_unidade_org AS no_unidade_org
                          FROM tramite_artefato ta
                          JOIN artef             ON artef.sq_artefato = ta.sq_artefato
                          JOIN vw_pessoa     pes ON pes.sq_pessoa = ta.sq_pessoa_tramite
                          JOIN vw_unidade_org uo ON uo.sq_pessoa = ta.sq_unidade_org_tramite
                         WHERE ta.dt_cancelamento IS NOT NULL
                           AND ta.dt_tramite >= :dataEntradaProducao
                    ),
                    devolucao AS(
                        SELECT
                               'Trâmite devolvido em ' || to_char(ta.dt_devolucao,'dd/mm/yyyy HH24:MI:SS') AS tx_operacao
                               ,to_char(ta.dt_devolucao,'dd/mm/yyyy HH24:MI:SS') AS dt_operacao
                               ,ta.dt_devolucao AS dt_ordenacao
                               ,pes.no_pessoa
                               ,uo.sg_unidade_org AS no_unidade_org
                          FROM tramite_artefato ta
                          JOIN artef             ON artef.sq_artefato = ta.sq_artefato
                          JOIN vw_pessoa     pes ON pes.sq_pessoa = ta.sq_pessoa_tramite
                          JOIN vw_unidade_org uo ON uo.sq_pessoa = ta.sq_unidade_org_tramite
                         WHERE ta.dt_devolucao IS NOT NULL
                           AND ta.dt_tramite >= :dataEntradaProducao
                    )

            SELECT
                COUNT(dt_operacao) over() as total_record, *
              FROM(
                    SELECT * FROM hist
                    UNION ALL
                    SELECT * FROM tramite
                    UNION ALL
                    SELECT * FROM recebimento
                    UNION ALL
                    SELECT * FROM cancelamento
                    UNION ALL
                    SELECT * FROM devolucao
                ) t
                    ORDER BY dt_ordenacao DESC";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('total_record'    , 'totalRecord' , 'integer');
        $rsm->addScalarResult('tx_operacao'     , 'txOperacao'  , 'string');
        $rsm->addScalarResult('no_pessoa'       , 'noPessoa'    , 'string');
        $rsm->addScalarResult('no_unidade_org'  , 'noUnidadeOrg', 'string');
        $rsm->addScalarResult('dt_operacao'     , 'dtOperacao'  , 'zenddate');

        $nativeQuery = $this->_em->createNativeQuery($strSql, $rsm);
        $nativeQuery->setParameter('dataEntradaProducao', $dataEntradaProducao);

        $nativeQuery->useResultCache(false);

        return $nativeQuery;
    }

    /**
     * método que retorna dados para grid de historico dos dados do sistema antigo
     *
     * @param \Core_Dto_Abstract $dto
     * @return \Doctrine\ORM\NativeQuery
     */
    public function listGridHistoricoFisico (\Core_Dto_Search $dto)
    {
        $strSql = "SELECT *
                     FROM vw_historico_sgdoc_fisico
                    WHERE sq_artefato = :sqArtefato
                 ORDER BY dt_operacao DESC";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('tx_operacao'     , 'txOperacao'  , 'string');
        $rsm->addScalarResult('no_pessoa'       , 'noPessoa'    , 'string');
        $rsm->addScalarResult('no_unidade_org'  , 'noUnidadeOrg', 'string');
        $rsm->addScalarResult('dt_operacao'     , 'dtOperacao'  , 'zenddate');

        $nativeQuery = $this->_em->createNativeQuery($strSql, $rsm);
        $nativeQuery->setParameter('sqArtefato', $dto->getSqArtefato());
        $nativeQuery->useResultCache(false);

        return $nativeQuery;
    }

    public function listAllHistorico (\Core_Dto_Search $dto)
    {
        return $this->listGridHistorico($dto)->execute();
    }

}
