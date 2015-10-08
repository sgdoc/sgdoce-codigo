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
 * Classe para Repository de DespachoInterlocutorio
 *
 * @package      Model
 * @subpackage   Repository
 * @name         DespachoInterlocutorio
 * @version      1.0.0
 * @since        2014-11-27
 */
class DespachoInterlocutorio extends \Core_Model_Repository_Base
{
    public function listGridHistoricoDespacho(\Core_Dto_Search $dto)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();

        $rsm->addScalarResult('total_record'              , 'totalRecord'             , 'integer');
        $rsm->addScalarResult('sq_despacho_interlocutorio', 'sqDespachoInterlocutorio', 'integer');
        $rsm->addScalarResult('sq_artefato'               , 'sqArtefato'              , 'integer');
        $rsm->addScalarResult('dt_despacho'               , 'dtDespacho'              , 'zenddate');
        $rsm->addScalarResult('tx_despacho'               , 'txDespacho'              , 'string');
        $rsm->addScalarResult('no_assinatura'             , 'noAssinatura'            , 'string');
        $rsm->addScalarResult('no_origem'                 , 'noOrigem'                , 'string');
        $rsm->addScalarResult('no_encaminhado'            , 'noEncaminhado'           , 'string');
        $rsm->addScalarResult('sq_pessoa_operacao'        , 'sqPessoaOperacao'        , 'integer');

        $sql = "SELECT count(sq_despacho_interlocutorio) over() as total_record,
                       di.sq_despacho_interlocutorio,
                       di.sq_artefato,
                       di.dt_despacho,
                       di.tx_despacho,
                       pes_ass.no_pessoa as no_assinatura,
                       uo_orig.sg_unidade_org AS no_origem,
                       uo_enc.sg_unidade_org AS no_encaminhado,
                       pes_oper.sq_pessoa AS sq_pessoa_operacao
                  FROM despacho_interlocutorio di
                  JOIN vw_pessoa pes_ass ON di.sq_pessoa_assinatura = pes_ass.sq_pessoa
                  JOIN vw_unidade_org uo_orig ON di.sq_unidade_assinatura = uo_orig.sq_pessoa
                  JOIN vw_unidade_org uo_enc ON di.sq_unidade_destino = uo_enc.sq_pessoa
                  JOIN vw_pessoa pes_oper ON di.sq_pessoa_operacao = pes_oper.sq_pessoa
                 WHERE di.sq_artefato = :sqArtefato
              ORDER BY di.sq_despacho_interlocutorio DESC";

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqArtefato', $dto->getSqArtefato());

        return $query;
    }
}
