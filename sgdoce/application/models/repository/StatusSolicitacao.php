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
 * Classe para Repository de StatusSolicitacao
 *
 * @package      Model
 * @subpackage   Repository
 * @name         StatusSolicitacao
 * @version      1.0.0
 * @since        2012-11-20
 */
class StatusSolicitacao extends \Core_Model_Repository_Base
{
    /**
     * @param integer $sqSolicitacao
     */
    public function getUltimoStatusSolicitacao( $sqSolicitacao )
    {
        $sql = "SELECT uss.sq_solicitacao, uss.sq_pessoa_triagem,
                       uss.sq_pessoa_responsavel,
                       uss.tx_comentario, to_char(uss.dt_operacao, 'DD/MM/YYYY') as dt_operacao,
                       pt.no_pessoa as no_pessoa_triagem,
                       pr.no_pessoa as no_pessoa_responsavel,
                       ps.no_pessoa as no_pessoa_solicitacao,
                       uss.sq_tipo_status_solicitacao,
                       uss.no_tipo_status_solicitacao,
                       em.tx_email
                  FROM sgdoce.vw_ultimo_status_solicitacao uss
                  JOIN sgdoce.solicitacao s USING(sq_solicitacao)
                  JOIN corporativo.vw_pessoa ps ON s.sq_pessoa = ps.sq_pessoa
                  LEFT JOIN corporativo.vw_pessoa pt ON uss.sq_pessoa_triagem = pt.sq_pessoa
                  LEFT JOIN corporativo.vw_pessoa pr ON uss.sq_pessoa_responsavel = pr.sq_pessoa
                  LEFT JOIN corporativo.vw_email em
                  ON em.sq_pessoa = s.sq_pessoa AND em.sq_tipo_email = " . \Core_Configuration::getCorpTipoEmailInstitucional() . "
                 WHERE uss.sq_solicitacao = :sqSolicitacao";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('sq_solicitacao', 'sqSolicitacao');
        $rsm->addScalarResult('sq_pessoa_triagem', 'sqPessoaTriagem');
        $rsm->addScalarResult('sq_pessoa_responsavel', 'sqPessoaResponsavel');
        $rsm->addScalarResult('tx_comentario', 'txComentario');
        $rsm->addScalarResult('dt_operacao', 'dtOperacao');
        $rsm->addScalarResult('no_pessoa_triagem', 'noPessoaTriagem');
        $rsm->addScalarResult('no_pessoa_responsavel', 'noPessoaResponsavel');
        $rsm->addScalarResult('no_pessoa_solicitacao', 'noPessoaSolicitacao');
        $rsm->addScalarResult('sq_tipo_status_solicitacao', 'sqTipoStatusSolicitacao');
        $rsm->addScalarResult('no_tipo_status_solicitacao', 'noTipoStatusSolicitacao');
        $rsm->addScalarResult('tx_email', 'txEmail');

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqSolicitacao', $sqSolicitacao);

        return $query->getSingleResult();
    }

    /**
     * @param integer $sqSolicitacao
     */
    public function getStatusPorTipo( $sqSolicitacao, $sqTipoStatusSolicitacao )
    {
        $sql = "SELECT  row_number() over() as rownum,
                        s.sq_solicitacao,
                        ss.sq_pessoa_triagem,
                        ss.sq_pessoa_responsavel,
                        ss.tx_comentario,
                        to_char(ss.dt_operacao, 'DD/MM/YYYY') as dt_operacao,
                        pt.no_pessoa as no_pessoa_triagem,
                        pr.no_pessoa as no_pessoa_responsavel,
                        ps.no_pessoa as no_pessoa_solicitacao,
                        ss.sq_tipo_status_solicitacao,
                        em.tx_email
                FROM sgdoce.solicitacao s
                JOIN sgdoce.status_solicitacao ss USING(sq_solicitacao)
                JOIN corporativo.vw_pessoa ps ON s.sq_pessoa = ps.sq_pessoa
                LEFT JOIN corporativo.vw_pessoa pt ON ss.sq_pessoa_triagem = pt.sq_pessoa
                LEFT JOIN corporativo.vw_pessoa pr ON ss.sq_pessoa_responsavel = pr.sq_pessoa
                LEFT JOIN corporativo.vw_email em
                ON em.sq_pessoa = s.sq_pessoa AND em.sq_tipo_email = " . \Core_Configuration::getCorpTipoEmailInstitucional() . "
                WHERE ss.sq_solicitacao = :sqSolicitacao AND ss.sq_tipo_status_solicitacao = :sqTipoStatusSolicitacao
                ORDER BY ss.dt_operacao DESC, rownum ASC";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('rownum', 'rownum');
        $rsm->addScalarResult('sq_solicitacao', 'sqSolicitacao');
        $rsm->addScalarResult('sq_pessoa_triagem', 'sqPessoaTriagem');
        $rsm->addScalarResult('sq_pessoa_responsavel', 'sqPessoaResponsavel');
        $rsm->addScalarResult('tx_comentario', 'txComentario');
        $rsm->addScalarResult('dt_operacao', 'dtOperacao');
        $rsm->addScalarResult('no_pessoa_triagem', 'noPessoaTriagem');
        $rsm->addScalarResult('no_pessoa_responsavel', 'noPessoaResponsavel');
        $rsm->addScalarResult('no_pessoa_solicitacao', 'noPessoaSolicitacao');
        $rsm->addScalarResult('tx_email', 'txEmail');

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqSolicitacao', $sqSolicitacao)
              ->setParameter('sqTipoStatusSolicitacao', $sqTipoStatusSolicitacao);

        return $query->getScalarResult();
    }
}
