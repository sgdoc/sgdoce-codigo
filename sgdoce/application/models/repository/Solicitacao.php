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
 * Classe para Repository de Solicitacao
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Solicitacao
 * @version      1.0.0
 * @since        2012-11-20
 */
class Solicitacao extends \Core_Model_Repository_Base
{
    /**
     * @param \Core_Dto_Search $dto
     */
    public function searchArtefato($dto)
    {
        $sqTipoArtefato = $dto->getSqTipoArtefato();

        $nuArtefato = "sgdoce.formata_numero_digital(art.nu_digital) ILIKE '%{$dto->getQuery()}%'";

        if( $sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ) {
            $value      = preg_replace( '/[^0-9]/', '', $dto->getQuery() );
            $withOR     = "OR TRANSLATE(art.nu_artefato, './-', '') ILIKE '%{$value}%'";
            $nuArtefato = "sgdoce.formata_numero_artefato(art.nu_artefato, ap.co_ambito_processo) ILIKE '%{$value}%' $withOR";
        }

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato', 'sqArtefato', 'integer');
        $rsm->addScalarResult('nu_artefato', 'nuArtefato', 'string');
        $rsm->addScalarResult('nu_digital', 'nuDigital', 'string');

        $sql = sprintf('
                  SELECT art.sq_artefato
                         ,formata_numero_artefato(art.nu_artefato, ap.co_ambito_processo) AS nu_artefato
                         ,formata_numero_digital(art.nu_digital) as nu_digital
                    FROM artefato art
                    JOIN artefato                   art_pai ON obter_vinculo_pai(art.sq_artefato) = art_pai.sq_artefato
                    JOIN sgdoce.tramite_artefato    uta ON art_pai.sq_artefato = uta.sq_artefato AND uta.st_ultimo_tramite
                    JOIN tipo_artefato_assunto      taa ON taa.sq_tipo_artefato_assunto = art.sq_tipo_artefato_assunto
               LEFT JOIN artefato_processo           ap ON art.sq_artefato = ap.sq_artefato
               LEFT JOIN artefato_vinculo            av ON av.sq_artefato_filho = art.sq_artefato and av.sq_tipo_vinculo_artefato NOT IN(%5$d,%6$d,%7$d)
               LEFT JOIN artefato_arquivo_setorial  aas ON art.sq_artefato = aas.sq_artefato and aas.dt_desarquivamento is null
                   WHERE aas.sq_artefato IS NULL
                     AND uta.sq_pessoa_recebimento = %1$s
                     AND uta.sq_status_tramite <> %2$s
                     AND (%3$s)
                     AND taa.sq_tipo_artefato = %4$s',
                     // AND av.sq_artefato_vinculo IS NULL', //não pode ser filho para abrir demanda de suporte, (Agora pode)
                        //:sqPessoa
                        $dto->getSqPessoa(),
                        //:sqStatusTramite
                        \Core_Configuration::getSgdoceStatusTramiteTramitado(),
                        //:nuArtefato
                        $nuArtefato,
                        //:sqTipoArtefato
                        $dto->getSqTipoArtefato(),
                        \Core_Configuration::getSgdoceTipoVinculoArtefatoReferencia(),
                        \Core_Configuration::getSgdoceTipoVinculoArtefatoApoio(),
                        \Core_Configuration::getSgdoceTipoVinculoArtefatoDespacho()
                    );

        try {
            return $this->_em->createNativeQuery($sql, $rsm)->useResultCache(false)->getScalarResult();
        } catch ( \Exception $ex) {
            $this->getMessaging()->addErrorMessage(\Core_Registry::getMessage()->translate('MN180'), 'User');
            $this->getMessaging()->dispatchPackets();
            return array();
        }
    }

    /**
     * @return
     */
    public function listGrid( \Core_Dto_Search $dto )
    {
        $listCondition = array(
            'getSqPessoaAbertura' => array(
                "=" => array(
                    "AND" => 's.sq_pessoa'
                )
            ),
            'getSqUnidadeOrgAbertura' => array(
                "=" => array(
                    "AND" => 's.sq_unidade_org'
                )
            ),
            'getSqTipoAssuntoSolicitacao' => array(
                "=" => array(
                    "AND" => 'tas.sq_tipo_assunto_solicitacao'
                )
            ),
            'getSqTipoStatusSolicitacao' => array(
                "=" => array(
                    "AND" => 'uss.sq_tipo_status_solicitacao'
                )
            ),
            'getSqTipoStatusSolicitacaoNot' => array(
                "<>" => array(
                    "AND" => 'uss.sq_tipo_status_solicitacao'
                )
            ),
            'getSqPessoaResponsavel' => array(
                "=" => array(
                    "AND" => 'uss.sq_pessoa_responsavel'
                )
            ),
            'getSqPessoaResponsavelIsNull' => array(
                "IS" => array(
                    "AND" => 'uss.sq_pessoa_responsavel'
                )
            ),
            'getSqPessoaResponsavelNot' => array(
                "<>" => array(
                    "AND" => 'uss.sq_pessoa_responsavel'
                )
            ),
            'getSearch' => array(
                "ilike" =>
                array( 'OR' => array(
                        'tas.no_tipo_assunto_solicitacao',
                        's.ds_solicitacao',
                        'pes_atendimento.no_pessoa',
                        'und_abertura.sg_unidade_org',
                        'pes_abertura.no_pessoa',
                        'at.nu_artefato',
                        'formata_numero_digital(at.nu_digital)',
                        'cast(s.sq_solicitacao as text)',
                        'tx_email'
                    )
                )
            )
        );

        $operationTypeArtefato = 'IS NULL';
        if (in_array($dto->getSqTipoArtefato(), array(1,2))) {
            $operationTypeArtefato = '=';
        }

        $listCondition['getSqTipoArtefato'] = array(
            $operationTypeArtefato => array(
                "AND" => 'taa.sq_tipo_artefato'
            )
        );


        if( $dto->getNuArtefato() != '' && $dto->getSqTipoArtefato()){
            if( $dto->getSqTipoArtefato() == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ) {
                $listCondition['getNuArtefato'] = array(
                    "ilike" => array(
                        "OR" => array(
                            "formata_numero_artefato(at.nu_artefato, ap.co_ambito_processo)",
                            'at.nu_artefato',
                        )
                    )
                );
            } else {
                $listCondition['getNuArtefato'] = array(
                    "ilike" => array(
                        "AND" => "formata_numero_digital(at.nu_digital)"
                    )
                );
            }
        }

        $period = null;
        if( $dto->getDtSolicitacao() != "" ) {
            $period = "s.dt_solicitacao";
        }

        $where = $this->getEntityManager()
                      ->getRepository('app:Artefato')
                      ->getCriteriaText($listCondition, $dto, $period);

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_solicitacao', 'sqSolicitacao', 'integer');
        $rsm->addScalarResult('sq_artefato', 'sqArtefato', 'integer');
        $rsm->addScalarResult('no_tipo_assunto_solicitacao', 'noTipoAssuntoSolicitacao', 'string');
        $rsm->addScalarResult('ds_solicitacao', 'dsSolicitacao', 'string');
        $rsm->addScalarResult('sq_pessoa_abertura', 'sqPessoaAbertura', 'integer');
        $rsm->addScalarResult('no_pessoa_abertura', 'noPessoaAbertura', 'string');
        $rsm->addScalarResult('sq_unidade_abertura', 'sqUnidadeAbertura', 'integer');
        $rsm->addScalarResult('no_unidade_abertura', 'noUnidadeAbertura', 'string');
        $rsm->addScalarResult('sq_tipo_status_solicitacao', 'sqTipoStatusSolicitacao', 'integer');
        $rsm->addScalarResult('no_tipo_status_solicitacao', 'noTipoStatusSolicitacao', 'string');
        $rsm->addScalarResult('sq_tipo_assunto_solicitacao', 'sqTipoAssuntoSolicitacao', 'integer');
        $rsm->addScalarResult('sq_pessoa_responsavel', 'sqPessoaResponsavel', 'integer');
        $rsm->addScalarResult('has_image', 'hasImage', 'boolean');
        $rsm->addScalarResult('no_pessoa_atendimento', 'noPessoaAtendimento', 'string');
        $rsm->addScalarResult('dt_operacao', 'dtOperacao', 'string');
        $rsm->addScalarResult('dt_solicitacao', 'dtSolicitacao', 'string');
        $rsm->addScalarResult('nu_artefato', 'nuArtefato', 'string');
        $rsm->addScalarResult('nu_digital', 'nuDigital', 'string');
        $rsm->addScalarResult('sq_tipo_artefato', 'sqTipoArtefato', 'string');
        $rsm->addScalarResult('tx_email', 'txEmail', 'string');
        $rsm->addScalarResult('total_record', 'totalRecord', 'integer');

        $sql = "SELECT COUNT(s.sq_solicitacao) OVER() AS total_record,
                        s.sq_solicitacao,
                        s.sq_artefato,
                        tas.no_tipo_assunto_solicitacao,
                        s.ds_solicitacao,
                        s.sq_pessoa as sq_pessoa_abertura,
                        s.dt_solicitacao,
                        pes_abertura.no_pessoa || (COALESCE('<br>(' || tel.nu_ddd || ') ' || tel.nu_telefone, '')) as no_pessoa_abertura,
                        s.sq_unidade_org as sq_unidade_abertura,
                        und_abertura.sg_unidade_org as no_unidade_abertura,
                        uss.sq_tipo_status_solicitacao,
                        uss.no_tipo_status_solicitacao,
                        tas.sq_tipo_assunto_solicitacao,
                        uss.sq_pessoa_responsavel,
                        pes_atendimento.no_pessoa as no_pessoa_atendimento,
                        uss.dt_operacao,
                        CASE
                         WHEN at.nu_digital IS NOT NULL THEN
                            sgdoce.formata_numero_digital(at.nu_digital)
                         ELSE
                            sgdoce.formata_numero_artefato(at.nu_artefato, ap.co_ambito_processo)
                        END AS nu_artefato,
                        sgdoce.formata_numero_digital(at.nu_digital) as nu_digital,
                        taa.sq_tipo_artefato,
                        EXISTS
                        (
                            SELECT
                                 uia.sq_artefato
                             FROM
                                 sgdoce.vw_ultima_imagem_artefato uia
                             WHERE
                                 uia.sq_artefato = at.sq_artefato AND
                                 taa.sq_tipo_artefato = ".\Core_Configuration::getSgdoceTipoArtefatoDocumento()."
                        UNION
                            SELECT
                                artv.sq_artefato_filho
                            FROM
                                sgdoce.artefato_vinculo artv
                            JOIN sgdoce.artefato_imagem uia ON  uia.st_ativo and artv.sq_artefato_filho = uia.sq_artefato
                                AND artv.sq_tipo_vinculo_artefato = ".\Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao()."
                            WHERE
                                artv.sq_artefato_pai = at.sq_artefato AND
                                taa.sq_tipo_artefato = ".\Core_Configuration::getSgdoceTipoArtefatoProcesso()."
                        ) has_image,
                        em.tx_email
                   FROM sgdoce.solicitacao s
                   JOIN sgdoce.tipo_assunto_solicitacao     AS tas USING(sq_tipo_assunto_solicitacao)
                   LEFT JOIN
                   (
                       sgdoce.artefato AS at
                       INNER JOIN sgdoce.tipo_artefato_assunto AS taa
                           ON at.sq_tipo_artefato_assunto = taa.sq_tipo_artefato_assunto
                   )                                        ON s.sq_artefato = at.sq_artefato
                   LEFT JOIN sgdoce.artefato_processo AS ap ON at.sq_artefato = ap.sq_artefato
                   JOIN corporativo.vw_pessoa               AS pes_abertura ON s.sq_pessoa = pes_abertura.sq_pessoa
                   LEFT JOIN corporativo.vw_telefone        AS tel ON (tel.sq_pessoa = pes_abertura.sq_pessoa AND tel.sq_tipo_telefone = ".\Core_Configuration::getCorpTipoTelefoneInstitucional().")
                   JOIN corporativo.vw_unidade_org          AS und_abertura ON s.sq_unidade_org = und_abertura.sq_pessoa
                   JOIN sgdoce.vw_ultimo_status_solicitacao AS uss ON s.sq_solicitacao = uss.sq_solicitacao
              LEFT JOIN corporativo.vw_pessoa               AS pes_atendimento ON uss.sq_pessoa_responsavel = pes_atendimento.sq_pessoa
              LEFT JOIN corporativo.vw_email                AS em ON em.sq_pessoa = s.sq_pessoa AND em.sq_tipo_email = " . \Core_Configuration::getCorpTipoEmailInstitucional() . "
                  %s
              ORDER BY s.dt_solicitacao DESC";

        if( $where != "" ){
            $sql = sprintf($sql, "WHERE {$where}");
        } else {
            $sql = sprintf($sql, "");
        }

        return $this->_em->createNativeQuery($sql, $rsm)->useResultCache(false);
    }

    /**
     * @return
     */
    public function listGridHistorico( \Core_Dto_Search $dto )
    {
        $listCondition = array(

            'getSqTipoAssuntoSolicitacao' => array(
                "=" => array(
                    "AND" => 'tas.sq_tipo_assunto_solicitacao'
                )
            ),
        );

        $operationTypeArtefato = 'IS NULL';
        if (in_array($dto->getSqTipoArtefato(), array(1,2))) {
            $operationTypeArtefato = '=';
        }

        $listCondition['getSqTipoArtefato'] = array(
            $operationTypeArtefato => array(
                "AND" => 'taa.sq_tipo_artefato'
            )
        );

        if( $dto->getNuArtefato() != '' && $dto->getSqTipoArtefato()){
            if( $dto->getSqTipoArtefato() == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ) {
                $listCondition['getNuArtefato'] = array(
                    "ilike" => array(
                        "OR" => array(
                            "formata_numero_artefato(at.nu_artefato, ap.co_ambito_processo)",
                            'at.nu_artefato',
                        )
                    )
                );
            } else {
                $listCondition['getNuArtefato'] = array(
                    "ilike" => array(
                        "AND" => "formata_numero_digital(at.nu_digital)"
                    )
                );
            }
        }

        $where = $this->getEntityManager()
                      ->getRepository('app:Artefato')
                      ->getCriteriaText($listCondition, $dto);

        if( $dto->getDtSolicitacao() != "" ) {
            $dateInicial = new \Zend_Date($dto->getDtSolicitacao());
            $dateFinal   = new \Zend_Date($dto->getDtSolicitacao());

            $dateInicial->setHour(00)->setMinute(00)->setSecond(00);
            $dateFinal->setHour(23)->setMinute(59)->setSecond(59);


            $where .= ($where) ? ' AND ' : ' ' ;

            $where .= "s.dt_solicitacao between '{$dateInicial->get(\Zend_Date::ISO_8601)}' and '{$dateFinal->get(\Zend_Date::ISO_8601)}'";
        }

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('total_record'                , 'totalRecord'                 , 'integer');
        $rsm->addScalarResult('sq_artefato'                 , 'sqArtefato'                  , 'integer');
        $rsm->addScalarResult('sq_solicitacao'              , 'sqSolicitacao'               , 'integer');
        $rsm->addScalarResult('dt_solicitacao'              , 'dtSolicitacao'               , 'string');
        $rsm->addScalarResult('no_tipo_status_solicitacao'  , 'noTipoStatusSolicitacao'     , 'string');
        $rsm->addScalarResult('no_pessoa_abertura'          , 'noPessoaAbertura'            , 'string');
        $rsm->addScalarResult('no_unidade_abertura'         , 'noUnidadeAbertura'           , 'string');
        $rsm->addScalarResult('nu_artefato'                 , 'nuArtefato'                  , 'string');
        $rsm->addScalarResult('no_tipo_assunto_solicitacao' , 'noTipoAssuntoSolicitacao'    , 'string');
        $rsm->addScalarResult('ds_solicitacao'              , 'dsSolicitacao'               , 'string');
        $rsm->addScalarResult('has_image'                   , 'hasImage'                    , 'boolean');


        $sql = "SELECT COUNT(s.sq_solicitacao) OVER() AS total_record,
                        s.sq_artefato,
                        s.sq_solicitacao,
                        s.dt_solicitacao,
                        uss.no_tipo_status_solicitacao,
                        s.sq_pessoa as sq_pessoa_abertura,
                        und_abertura.sg_unidade_org as no_unidade_abertura,
                        pes_abertura.no_pessoa || (COALESCE('<br>(' || tel.nu_ddd || ') ' || tel.nu_telefone, '')) as no_pessoa_abertura,

                        CASE
                         WHEN at.nu_digital IS NOT NULL THEN
                            sgdoce.formata_numero_digital(at.nu_digital)
                         ELSE
                            sgdoce.formata_numero_artefato(at.nu_artefato, ap.co_ambito_processo)
                        END AS nu_artefato,
                        tas.no_tipo_assunto_solicitacao,
                        s.ds_solicitacao
                   FROM sgdoce.solicitacao s
                   JOIN sgdoce.tipo_assunto_solicitacao     AS tas USING(sq_tipo_assunto_solicitacao)
                   LEFT JOIN (
                       sgdoce.artefato AS at
                       INNER JOIN sgdoce.tipo_artefato_assunto AS taa ON at.sq_tipo_artefato_assunto = taa.sq_tipo_artefato_assunto
                        LEFT JOIN sgdoce.artefato_processo     AS ap  ON at.sq_artefato = ap.sq_artefato
                   ) ON s.sq_artefato = at.sq_artefato

                   JOIN corporativo.vw_pessoa               AS pes_abertura ON s.sq_pessoa = pes_abertura.sq_pessoa
                   JOIN corporativo.vw_unidade_org          AS und_abertura ON s.sq_unidade_org = und_abertura.sq_pessoa
                   JOIN sgdoce.vw_ultimo_status_solicitacao AS uss ON s.sq_solicitacao = uss.sq_solicitacao
                   LEFT JOIN corporativo.vw_telefone        AS tel ON (tel.sq_pessoa = pes_abertura.sq_pessoa AND tel.sq_tipo_telefone = ".\Core_Configuration::getCorpTipoTelefoneInstitucional().")
                  %s
                ORDER BY s.dt_solicitacao DESC";



        if( $where != "" ){
            $sql = sprintf($sql, "WHERE {$where}");
        } else {
            $sql = sprintf($sql, "");
        }

        return $this->_em->createNativeQuery($sql, $rsm)->useResultCache(false);
    }


    public function getSolicitacaoAberta (\Core_Dto_Search $dto)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato', 'sqArtefato', 'integer');
        $rsm->addScalarResult('sq_solicitacao', 'sqSolicitacao', 'integer');
        $rsm->addScalarResult('sq_pessoa', 'sqPessoa', 'integer');
        $rsm->addScalarResult('sq_unidade_org', 'sqUnidadeOrg', 'integer');
        $rsm->addScalarResult('sq_tipo_assunto_solicitacao', 'sqTipoAssuntoSolicitacao', 'integer');
        $rsm->addScalarResult('ds_solicitacao', 'dsSolicitacao', 'string');
        $rsm->addScalarResult('dt_solicitacao', 'dtSolicitacao', 'zenddate');

        $sql = 'SELECT s.*
                  FROM sgdoce.solicitacao s
                  JOIN sgdoce.vw_ultimo_status_solicitacao uss USING (sq_solicitacao)
                 WHERE s.sq_artefato = :sqArtefato
                   AND uss.sq_tipo_status_solicitacao <> :sqStatusAberta';

        $nq = $this->_em->createNativeQuery($sql, $rsm)
                ->setParameter('sqArtefato', $dto->getSqArtefato())
                ->setParameter('sqStatusAberta', \Core_Configuration::getSgdoceTipoStatusSolicitacaoFinalizada())
                ->useResultCache(false);

        return $nq->getScalarResult();
    }

    public function getSolicitacaoAbertaByAssuntoPessoaResponsavel (\Core_Dto_Search $dto, $listTipoAssuntoSolicitcao = array())
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato'     , 'sqArtefato'   , 'integer');
        $rsm->addScalarResult('sq_solicitacao'  , 'sqSolicitacao', 'integer');
        $rsm->addScalarResult('ds_solicitacao'  , 'dsSolicitacao', 'string');
        $rsm->addScalarResult('dt_solicitacao'  , 'dtSolicitacao', 'zenddate');
        $rsm->addScalarResult('sq_tipo_assunto_solicitacao', 'sqTipoAssuntoSolicitacao', 'integer');

        $sql = 'SELECT s.*
                  FROM sgdoce.solicitacao s
                  JOIN sgdoce.vw_ultimo_status_solicitacao uss USING (sq_solicitacao)
                 WHERE s.sq_artefato = :sqArtefato
                   AND uss.sq_tipo_status_solicitacao <> :sqStatusAberta
                   AND uss.sq_pessoa_responsavel = :sqPessoaResponsavel';

        if( count($listTipoAssuntoSolicitcao) ){
            $sql .= " AND s.sq_tipo_assunto_solicitacao IN(:sqTipoAssuntoSolicitacaoiIn)";
        }

        if( $dto->getSqTipoAssuntoSolicitacao() ){
            $sql .= " AND s.sq_tipo_assunto_solicitacao = :sqTipoAssuntoSolicitacao";
        }

        $nq = $this->_em->createNativeQuery($sql, $rsm)
                ->setParameter('sqArtefato', $dto->getSqArtefato())
                ->setParameter('sqStatusAberta', \Core_Configuration::getSgdoceTipoStatusSolicitacaoFinalizada())
                ->setParameter('sqPessoaResponsavel', \Core_Integration_Sica_User::getPersonId())
                ->useResultCache(false);

        if( count($listTipoAssuntoSolicitcao) ){
            $nq->setParameter('sqTipoAssuntoSolicitacaoiIn', $listTipoAssuntoSolicitcao);
        }

        if( $dto->getSqTipoAssuntoSolicitacao() ){
            $nq->setParameter('sqTipoAssuntoSolicitacao', $dto->getSqTipoAssuntoSolicitacao());
        }

        return $nq->getScalarResult();
    }

    public function getSolicitacaoDuplicado(\Core_Dto_Search $dto)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato', 'sqArtefato', 'integer');
        $rsm->addScalarResult('sq_solicitacao', 'sqSolicitacao', 'integer');
        $rsm->addScalarResult('sq_tipo_assunto_solicitacao', 'sqTipoAssuntoSolicitacao', 'integer');
        $rsm->addScalarResult('ds_solicitacao', 'dsSolicitacao', 'string');
        $rsm->addScalarResult('dt_solicitacao', 'dtSolicitacao', 'zenddate');

        $sql = 'SELECT s.*
                  FROM sgdoce.solicitacao s
                  JOIN sgdoce.vw_ultimo_status_solicitacao uss USING (sq_solicitacao)
                 WHERE s.sq_artefato = :sqArtefato
                   AND s.sq_tipo_assunto_solicitacao = :sqTipoAssuntoSolicitacao
                   AND s.sq_pessoa = :sqPessoa
                   AND s.sq_unidade_org = :sqUnidadeOrg
                   AND uss.sq_tipo_status_solicitacao <> :sqStatusAberta';

        $nq = $this->_em->createNativeQuery($sql, $rsm)
                ->setParameter('sqArtefato', $dto->getSqArtefato())
                ->setParameter('sqTipoAssuntoSolicitacao', $dto->getSqTipoAssuntoSolicitacao())
                ->setParameter('sqPessoa', $dto->getSqPessoa())
                ->setParameter('sqUnidadeOrg', $dto->getSqUnidadeOrg())
                ->setParameter('sqStatusAberta', \Core_Configuration::getSgdoceTipoStatusSolicitacaoFinalizada())
                ->useResultCache(false);

        return $nq->getScalarResult();
    }

}
