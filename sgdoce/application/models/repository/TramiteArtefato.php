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

use \Artefato\Service\TramiteArtefato as TramiteArtefatoService;

/**
 * SISICMBio
 *
 * Classe para Repository de TramiteArtefato
 *
 * @package      Model
 * @subpackage   Repository
 * @name         TramiteArtefato
 * @version      1.0.0
 * @since        2012-11-20
 */
class TramiteArtefato extends \Core_Model_Repository_Base
{

    /**
     * Retorna o proximo numero de tramite
     *
     * @param \Core_Dto_Search $dto
     * @return integer
     */
    public function getNextTramiteNumber (\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em
                ->createQueryBuilder()
                ->select('max(ta.nuTramite) AS nuTramite')
                ->from('app:TramiteArtefato', 'ta')
                ->andWhere('ta.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $dto->getSqArtefato());

        $result = $queryBuilder->getQuery()->getSingleResult();
        if ($result) {
            $nuTramite = current($result);
            return ++$nuTramite;
        }

        return 1; //1º tramite
    }

    /**
     *
     * @param \Core_Dto_Abstract $dto
     * @return \Doctrine\ORM\NativeQuery
     */
    public function listTramiteExternoComRastreamento (\Core_Dto_Abstract $dto)
    {

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('no_pessoa_destino', 'noPessoaDestino', 'string');
        $rsm->addScalarResult('no_tipo_rastreamento', 'noTipoRastreamento', 'string');
        $rsm->addScalarResult('tx_codigo_rastreamento', 'txCodigoRastreamento', 'string');
        $rsm->addScalarResult('dt_envio', 'dtEnvio', 'zenddate');
        $rsm->addScalarResult('no_remetente', 'noRemetente', 'string');

        $strQuery = sprintf(
                'SELECT p.no_pessoa AS no_pessoa_destino
                        ,trc.no_tipo_rastreamento_correio AS no_tipo_rastreamento
                        ,ta.tx_codigo_rastreamento
                        ,ta.dt_tramite AS dt_envio
                        ,pes_origem.no_pessoa || \' [\' || uo_origem.no_pessoa || \']\' AS no_remetente
                   FROM tramite_artefato ta
              LEFT JOIN vw_unidade_org             uo ON ta.sq_pessoa_destino = uo.sq_pessoa
              LEFT JOIN vw_pessoa                   p ON ta.sq_pessoa_destino = p.sq_pessoa
              LEFT JOIN tipo_rastreamento_correio trc ON ta.sq_tipo_rastreamento = trc.sq_tipo_rastreamento_correio
              LEFT JOIN vw_unidade_org      uo_origem ON ta.sq_unidade_org_tramite = uo_origem.sq_pessoa
              LEFT JOIN vw_pessoa          pes_origem ON ta.sq_pessoa_tramite = pes_origem.sq_pessoa
                  WHERE uo.sq_pessoa IS NULL
                    AND ta.sq_artefato = %1$d
                    AND ta.tx_codigo_rastreamento IS NOT NULL'
                , $dto->getSqArtefato()
        );

        return $this->_em->createNativeQuery($strQuery, $rsm)->useResultCache(false);
    }

    /**
     * Verifica se o artefato possui tramite para a pessoa logada
     *
     * @param \Core_Dto_Search $dto
     * @return boolean
     */
    public function canViewSigiloso (\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('count(ta.sqTramiteArtefato) as qtdTramite')
                ->from($this->_entityName, 'ta')
                ->where($queryBuilder->expr()->eq('ta.sqArtefato', ':sqArtefato'))
                ->andWhere($queryBuilder->expr()->eq('ta.sqPessoaRecebimento', $dto->getSqPessoa()))
                ->andWhere($queryBuilder->expr()->neq('ta.sqStatusTramite', \Core_Configuration::getSgdoceStatusTramiteCancelado()))
                ;
        $qb = $queryBuilder;
        $qb->setParameter('sqArtefato', $dto->getSqArtefato());

        $qtdTramiteArtefato = $qb->getQuery()->getSingleScalarResult();

        //se o artefato da vez não possui tramite para a pessoa em questão
        //busca o artefato principal da arvore para verificar se possui tramite para a pessoa logada
        if (!$qtdTramiteArtefato) {
            $sqArtefatoPrincipal = $this->_em->getRepository('app:ArtefatoVinculo')->getSqArtefatoPrincipal($dto);
            if ($sqArtefatoPrincipal) {
                $qtdTramiteArtefato = $qb->setParameter('sqArtefato', $sqArtefatoPrincipal)
                                         ->getQuery()
                                         ->getSingleScalarResult();
            }
        }

        return ($qtdTramiteArtefato > 0);
    }

    public function hasTramiteEfetivo (\Core_Dto_Search $dto)
    {
        $sql = "SELECT (count(sq_tramite_artefato) > 1) has_tramite_efetivo
                  FROM tramite_artefato
                 WHERE nu_tramite <> :nuTramite
                   AND sq_status_tramite < :sqStatusTramite
                   AND NOT st_ultimo_tramite
                   AND sq_artefato = :sqArtefato";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('has_tramite_efetivo', 'hasTramiteEfetivo', 'boolean');

        $nq = $this->_em->createNativeQuery($sql, $rsm)->useResultCache(false);
        $nq->setParameters(array(
            'nuTramite'       => TramiteArtefatoService::FIRST_TRAMITE_NUMBER,
            'sqStatusTramite' => \Core_Configuration::getSgdoceStatusTramiteCancelado() ,
            'sqArtefato'      => $dto->getSqArtefato()
        ));

        return $nq->getSingleScalarResult();
    }
}
