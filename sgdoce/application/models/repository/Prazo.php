<?php
/*
 * Copyright 2012 ICMBio
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
namespace Sgdoce\Model\Repository;

/**
 * SISICMBio
 *
 * Classe para Repository de Prazo
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Prazo
 * @version      1.0.0
 * @since        2015-02-20
 */
class Prazo extends \Core_Model_Repository_Base
{

    /**
     * Consulta de processos.
     *
     * @return QueryBuilder
     */
    public function listGrid ($dto)
    {
        $listCondition = array(
            'getSqPessoaPrazoGerada' => array(
                "=" => array(
                    "AND" => 'pr.sq_pessoa_prazo'
                )
            ),
            'getSqUnidadeOrgPessoaPrazoGerada' => array(
                "=" => array(
                    "AND" => 'pr.sq_unidade_org_pessoa_prazo'
                )
            ),
            'getSqPessoaDestinoRecebida' => array(
                "=" => array(
                    "AND" => 'pr.sq_pessoa_destino'
                )
            ),
            'getSqUnidadeOrgPessoaDestinoRecebida' => array(
                "=" => array(
                    "AND" => 'pr.sq_unidade_org_pessoa_destino'
                )
            ),
            'getStRespostaIsNull' => array(
                "IS" => array(
                    "AND" => 'pr.dt_resposta'
                )
            ),
            'getSearch' => array(
                "ilike" =>
                array( 'OR' => array(
                        'po.no_pessoa',
                        'pd.no_pessoa',
                        'pa.no_pessoa',
                        'pr.tx_solicitacao',
                        'pr.tx_resposta',
                        'ar.nu_artefato',
                        'cast(ar.nu_digital as text)',
                    )
                )
            ),
        );

        $where = $this->getEntityManager()
                      ->getRepository('app:Artefato')
                      ->getCriteriaText($listCondition, $dto);

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_prazo', 'sqPrazo', 'integer');
        $rsm->addScalarResult('sq_artefato', 'sqArtefato', 'integer');
        $rsm->addScalarResult('nu_artefato', 'nuArtefato', 'string');
        $rsm->addScalarResult('nu_digital', 'nuDigital', 'string');
        $rsm->addScalarResult('tx_pessoa_origem', 'txPessoaOrigem', 'string');
        $rsm->addScalarResult('tx_unidade_org_origem', 'txUnidadeOrgOrigem', 'string');
        $rsm->addScalarResult('tx_pessoa_destino', 'txPessoaDestino', 'string');
        $rsm->addScalarResult('tx_unidade_org_destino', 'txUnidadeOrgDestino', 'string');
        $rsm->addScalarResult('tx_pessoa_resposta', 'txPessoaResposta', 'string');
        $rsm->addScalarResult('tx_unidade_org_resposta', 'txUnidadeOrgResposta', 'string');
        $rsm->addScalarResult('sq_artefato_resposta', 'sqArtefatoResposta', 'string');
        $rsm->addScalarResult('nu_artefato_resposta', 'nuArtefatoResposta', 'string');
        $rsm->addScalarResult('tx_solicitacao', 'txSolicitacao', 'string');
        $rsm->addScalarResult('tx_resposta', 'txResposta', 'string');
        $rsm->addScalarResult('dt_prazo', 'dtPrazo', 'string');
        $rsm->addScalarResult('dt_resposta', 'dtResposta', 'string');
        $rsm->addScalarResult('dt_cadastro', 'dtCadastro', 'string');
        $rsm->addScalarResult('nao_respondidos', 'naoRespondidos', 'integer');

        $sql = "WITH RECURSIVE prazos(sq_prazo, sq_prazo_pai, dt_cadastro, dt_resposta, sq_pessoa_resposta, sq_unidade_org_pessoa_resposta) AS (
                    SELECT sq_prazo, sq_prazo_pai, dt_cadastro, dt_resposta, sq_pessoa_resposta, sq_unidade_org_pessoa_resposta FROM sgdoce.prazo
                UNION ALL
                    SELECT pf.sq_prazo, pf.sq_prazo_pai, pf.dt_cadastro, pf.dt_resposta, pf.sq_pessoa_resposta, pf.sq_unidade_org_pessoa_resposta
                    FROM prazos pp
                    JOIN sgdoce.prazo pf ON pf.sq_prazo_pai = pp.sq_prazo
                )
                SELECT  pr.sq_prazo, ar.sq_artefato, ar.nu_artefato,
                    ar.nu_digital, COALESCE(po.no_pessoa, ' - ') AS tx_pessoa_origem, od.sg_unidade_org AS tx_unidade_org_origem,
                    COALESCE(pd.no_pessoa, ' - ') AS tx_pessoa_destino, COALESCE(ud.sg_unidade_org, ' - ') AS tx_unidade_org_destino,
                    COALESCE(pa.no_pessoa, ' - ') AS tx_pessoa_resposta, COALESCE(rd.sg_unidade_org, ' - ') AS tx_unidade_org_resposta,
                    aa.sq_artefato AS sq_artefato_resposta, aa.nu_artefato AS nu_artefato_resposta,
                    pr.tx_solicitacao, pr.dt_prazo, pr.dt_cadastro, pr.dt_resposta, pr.tx_resposta, COUNT(pf.sq_prazo) as nao_respondidos
                FROM sgdoce.prazo pr
                JOIN sgdoce.artefato ar
                  ON ar.sq_artefato = pr.sq_artefato
                -- PROCESSO
                -- ORIGEM
                LEFT JOIN corporativo.vw_pessoa po
                  ON pr.sq_pessoa_prazo = po.sq_pessoa
                LEFT JOIN corporativo.vw_unidade_org od
                  ON pr.sq_unidade_org_pessoa_prazo = od.sq_pessoa
                -- DESTINO
                LEFT JOIN corporativo.vw_pessoa pd
                  ON pr.sq_pessoa_destino = pd.sq_pessoa
                LEFT JOIN corporativo.vw_unidade_org ud
                  ON pr.sq_unidade_org_pessoa_destino = ud.sq_pessoa
                -- RESPOSTA
                LEFT JOIN corporativo.vw_pessoa pa
                  ON pr.sq_pessoa_destino = pa.sq_pessoa
                LEFT JOIN corporativo.vw_unidade_org rd
                  ON pr.sq_unidade_org_pessoa_destino = rd.sq_pessoa
                LEFT JOIN sgdoce.artefato aa
                  ON pr.sq_artefato_resposta = aa.sq_artefato
                -- FILHOS
                LEFT JOIN prazos pf ON pr.sq_prazo = pf.sq_prazo_pai AND pf.dt_resposta IS NULL
                  %s
                GROUP BY pr.sq_prazo, ar.sq_artefato, ar.nu_artefato,
                    ar.nu_digital, po.no_pessoa, od.sg_unidade_org,
                    pd.no_pessoa, ud.sg_unidade_org, pa.no_pessoa, rd.sg_unidade_org,
                    aa.sq_artefato, aa.nu_artefato,  pr.tx_solicitacao, pr.dt_prazo,
                    pr.dt_cadastro, pr.dt_resposta, pr.tx_resposta";


        if( $where != "" ) {
            $where = "WHERE " . $where;
        }

        $sql = sprintf($sql, $where);

        return $this->_em->createNativeQuery($sql, $rsm);
    }
}