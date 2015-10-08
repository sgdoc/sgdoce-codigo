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
 * Classe para Repository de VwAreaTrabalho
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwAreaTrabalho
 * @version      1.0.0
 * @since        2014-12-29
 */
class VwAreaTrabalho extends \Core_Model_Repository_Base
{
    public function getQtdItensMinhaCaixa ($sqPessoa, $sqUnidadeOrg, $sqTipoArtefato)
    {
        $index = 'totalRecord';
        $sql = 'SELECT total_linhas FROM sgdoce.fn_show_area_trabalho(NULL,:sqTipoArtefato, :sqPessoa, :sqUnidadeOrg, NULL, 1)';
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('total_linhas', $index, 'integer');
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqPessoa', $sqPessoa, 'integer');
        $query->setParameter('sqUnidadeOrg', $sqUnidadeOrg, 'integer');
        $query->setParameter('sqTipoArtefato', $sqTipoArtefato, 'integer');
        $result = $query->getScalarResult();
        if ($result) {
            return $result[0][$index];
        }
        return 0;
    }

    public function find ($id, $lockMode = \Doctrine\DBAL\LockMode::NONE, $lockVersion = null)
    {
        trigger_error('Metodo '.__METHOD__.' não pode ser usado para VwAreaTrabalho. USE o metodo '.__CLASS__.'::findArtefato()');
    }

    public function listGridAreaTrabalho (\Core_Dto_Search $dto)
    {
        //primerio parametro é o sq_artefato e nas grid fica sem nulo
        //o limit e offset é setado no Abstract.php
        $sql = " SELECT * FROM sgdoce.fn_show_area_trabalho(NULL, :sqTipoArtefato, :sqPessoa, :sqUnidadeOrg, :search, :limit, :offset)";

        $query = $this->_em->createNativeQuery($sql, $this->_getResultMapping());
        $query->setParameter('sqTipoArtefato', (int)$dto->sqTipoArtefato, 'integer')
              ->setParameter('search', ($dto->search)?:NULL);

        switch ($dto->caixa) {
            case 2: #Pessoa
                $query->setParameter('sqPessoa', $dto->sqPessoa, 'integer')
                      ->setParameter('sqUnidadeOrg', $dto->sqUnidadeOrg, 'integer');
                break;
            case 3: #Externo
                 $query->setParameter('sqPessoa', NULL)
                       ->setParameter('sqUnidadeOrg', NULL);
                break;
            default: #Unidade
                $query->setParameter('sqPessoa', NULL)
                      ->setParameter('sqUnidadeOrg', $dto->sqUnidadeOrg, 'integer');
                break;
        }

        return $query;
    }

    /**
     *
     * @param \Core_Dto_Search $dto
     * @param type $returnArray
     * @return mixed Core_Dto_Search array
     */
    public function findArtefato(\Core_Dto_Search $dto, $returnArray = FALSE)
    {
        $sql = "SELECT * FROM sgdoce.fn_show_area_trabalho(:sqArtefato, NULL, :sqPessoa, :sqUnidadeOrg, NULL, NULL, NULL)";

        $query = $this->_em->createNativeQuery($sql, $this->_getResultMapping());
        $query->setParameter('sqArtefato'  , (int) $dto->getSqArtefato(), 'integer')
              ->setParameter('sqPessoa'    , (int) \Core_Integration_Sica_User::getPersonId(), 'integer')
              ->setParameter('sqUnidadeOrg', (int) \Core_Integration_Sica_User::getUserUnit(), 'integer');

        $result = $query->getArrayResult();

        if ($result) {
            $data = current($result);
            if (! $returnArray) {
                $result = \Core_Dto::factoryFromData($data, 'search');
            }else{
                $result = $data;
            }
        } else {
            $result = NULL;
        }

        return $result;
    }

    /**
     * método que retorna dados para grid da área de trabalho de arquivo
     * @param \Core_Dto_Abstract $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGridAreaTrabalhoArquivo (\Core_Dto_Search $dto)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('sq_tipo_historico_arquivo', 'sqTipoHistoricoArquivo', 'integer');
        $rsm->addScalarResult('total_record'        , 'totalRecord'      , 'integer');
        $rsm->addScalarResult('sq_artefato'         , 'sqArtefato'       , 'integer');
        $rsm->addScalarResult('nu_digital'          , 'nuDigital'        , 'string');
        $rsm->addScalarResult('emprestado'          , 'emprestado'       , 'boolean');
        $rsm->addScalarResult('nu_artefato'         , 'nuArtefato'       , 'string');
        $rsm->addScalarResult('co_ambito_processo'  , 'coAmbitoProcesso' , 'string');
        $rsm->addScalarResult('dt_arquivamento'     , 'dtArquivamento'   , 'zenddate');
        $rsm->addScalarResult('tx_classificacao'    , 'txClassificacao'  , 'string');
        $rsm->addScalarResult('nu_caixa'            , 'nuCaixa'          , 'string');
        $rsm->addScalarResult('no_unidade_org_caixa', 'noUnidadeOrgCaixa', 'string');
        $rsm->addScalarResult('tx_movimentacao'     , 'txMovimentacao'   , 'string');
        $rsm->addScalarResult('no_tipo_documento'   , 'noTipoDocumento'  , 'string');
        $rsm->addScalarResult('dt_cadastro'         , 'dtCadastro'       , 'zenddate');
        $rsm->addScalarResult('tx_assunto'          , 'txAssunto'        , 'string');

        $sqEmprestado           = \Core_Configuration::getSgdoceTipoHistoricoArquivoEmprestado();
        $sqTipoArtefatoProcesso = \Core_Configuration::getSgdoceTipoArtefatoProcesso();
        $sqTipoArtefato         = $dto->getTipoArtefato();

        $caseNuDigital = "sgdoce.formata_numero_digital(art.nu_digital)";

        $sql = 'SELECT DISTINCT ON (ch.sq_artefato)
                       COUNT(ch.sq_artefato) OVER() AS total_record,
                       ch.sq_tipo_historico_arquivo,
                       ch.sq_artefato,
                       '.$caseNuDigital.' AS nu_digital,
                       (ch.sq_tipo_historico_arquivo = %2$d) AS emprestado,
                       sgdoce.formata_numero_artefato(art.nu_artefato, ap.co_ambito_processo) AS nu_artefato,
                       ap.co_ambito_processo,
                       ch.dt_operacao                  AS dt_arquivamento,
                       cl.tx_classificacao,
                       cx.nu_caixa || \'/\' || cx.nu_ano AS nu_caixa,
                       un.no_pessoa                    AS no_Unidade_Org_Caixa,
                       tha.ds_tipo_historico_arquivo   AS tx_movimentacao,
                       tp_doc.no_tipo_documento,
                       art.dt_cadastro,
                       ass.tx_assunto
                FROM sgdoce.caixa_artefato ca
                JOIN sgdoce.caixa cx
                  ON ca.sq_caixa = cx.sq_caixa
                JOIN sgdoce.caixa_historico ch
                  ON (ch.sq_caixa, ch.sq_artefato) = (ca.sq_caixa, ca.sq_artefato)
                JOIN sgdoce.artefato art
                  ON art.sq_artefato = ca.sq_artefato
                JOIN sgdoce.tipo_artefato_assunto tass
                  ON tass.sq_tipo_artefato_assunto = art.sq_tipo_artefato_assunto
                JOIN sgdoce.assunto ass
                  ON tass.sq_assunto = ass.sq_assunto
           LEFT JOIN sgdoce.artefato_processo ap
                  ON ap.sq_artefato = art.sq_artefato AND tass.sq_tipo_artefato = %3$d
           LEFT JOIN sgdoce.tipo_documento tp_doc
                  ON tp_doc.sq_tipo_documento = art.sq_tipo_documento
                JOIN sgdoce.tipo_historico_arquivo tha
                  ON tha.sq_tipo_historico_arquivo = ch.sq_tipo_historico_arquivo
                LEFT JOIN sgdoce.artefato_classificacao ac
                  ON art.sq_artefato = ac.sq_artefato
           LEFT JOIN sgdoce.classificacao cl
                  ON ac.sq_classificacao = cl.sq_classificacao
                JOIN corporativo.vw_unidade_org un
                  ON cx.sq_unidade_org = un.sq_pessoa
               WHERE tass.sq_tipo_artefato = %4$d
                %1$s';

        $optionalCondition = '';
        $search = mb_strtolower($dto->getSearch(), 'UTF-8');

        //Este order by tmb é obrigatório!!
        $orderBy = 'ORDER BY ch.sq_artefato, ch.dt_operacao DESC';

        if($search){

            $queryBuild = $this->_em->createQueryBuilder();
//            $nuArtefato = $this->_em->createQueryBuilder()->expr()->lower('art.nu_artefato')->__toString();
            $nuArtefato = $this->_em->createQueryBuilder()->expr()->lower("translate(art.nu_artefato::text, './-'::text, ''::text)")->__toString();

            if (is_numeric($search)) {
                $nuCaixa = $this->_em->createQueryBuilder()->expr()->lower('cx.nu_caixa')->__toString();
                $conditions =
                        " AND (
                                   {$queryBuild->expr()->like($caseNuDigital,$queryBuild->expr()->literal('%' . $search . '%')->__toString())->__toString()}
                                OR
                                   {$queryBuild->expr()->like($nuArtefato,$queryBuild->expr()->literal('%' . $search . '%')->__toString())->__toString()}
                              ) ";

                //Juliano [30-08-2015]
                //ESSA TRETA DO UNION É PARA MELHORAR A PERFORMACE DAS CONSULTAS POR NUMERO ENQUANTO NÃO FIZERMOS UMA REFATORAÇÃO
                //NA QUERY

                $union ='SELECT DISTINCT ON (ch.sq_artefato)
                                COUNT(ch.sq_artefato) OVER() AS total_record,
                                ch.sq_tipo_historico_arquivo,
                                ch.sq_artefato,
                                '.$caseNuDigital.' AS nu_digital,
                                (ch.sq_tipo_historico_arquivo = %1$d) AS emprestado,
                                sgdoce.formata_numero_artefato(art.nu_artefato, ap.co_ambito_processo) AS nu_artefato,
                                ap.co_ambito_processo,
                                ch.dt_operacao                  AS dt_arquivamento,
                                cl.tx_classificacao,
                                cx.nu_caixa || \'/\' || cx.nu_ano AS nu_caixa,
                                un.no_pessoa                    AS no_Unidade_Org_Caixa,
                                tha.ds_tipo_historico_arquivo   AS tx_movimentacao,
                                tp_doc.no_tipo_documento,
                                art.dt_cadastro,
                                ass.tx_assunto
                         FROM sgdoce.caixa_artefato ca
                         JOIN sgdoce.caixa cx
                           ON ca.sq_caixa = cx.sq_caixa
                         JOIN sgdoce.caixa_historico ch
                           ON (ch.sq_caixa, ch.sq_artefato) = (ca.sq_caixa, ca.sq_artefato)
                         JOIN sgdoce.artefato art
                           ON art.sq_artefato = ca.sq_artefato
                         JOIN sgdoce.tipo_artefato_assunto tass
                           ON tass.sq_tipo_artefato_assunto = art.sq_tipo_artefato_assunto
                         JOIN sgdoce.assunto ass
                           ON tass.sq_assunto = ass.sq_assunto
                    LEFT JOIN sgdoce.artefato_processo ap
                           ON ap.sq_artefato = art.sq_artefato AND tass.sq_tipo_artefato = %2$d
                    LEFT JOIN sgdoce.tipo_documento tp_doc
                           ON tp_doc.sq_tipo_documento = art.sq_tipo_documento
                         JOIN sgdoce.tipo_historico_arquivo tha
                           ON tha.sq_tipo_historico_arquivo = ch.sq_tipo_historico_arquivo
                         LEFT JOIN sgdoce.artefato_classificacao ac
                           ON art.sq_artefato = ac.sq_artefato
                    LEFT JOIN sgdoce.classificacao cl
                           ON ac.sq_classificacao = cl.sq_classificacao
                         JOIN corporativo.vw_unidade_org un
                           ON cx.sq_unidade_org = un.sq_pessoa
                        WHERE tass.sq_tipo_artefato = %3$d' . PHP_EOL;

                $sqlUnion = ' UNION ' . $union;
                $unionCondition = " AND (
                                           {$queryBuild->expr()->like($nuCaixa,$queryBuild->expr()->literal('%' . $search . '%')->__toString())->__toString()}
                                         OR
                                           {$queryBuild->expr()->like("cx.nu_ano::VARCHAR",$queryBuild->expr()->literal('%' . $search . '%')->__toString())->__toString()}
                                        )";

                $optionalCondition = $conditions . sprintf(
                    $sqlUnion,
                    $sqEmprestado,
                    $sqTipoArtefatoProcesso,
                    $sqTipoArtefato
                ) . $unionCondition ;

                $orderBy = 'ORDER BY sq_artefato, dt_arquivamento DESC';

            }else{
                $literalExpr = $queryBuild->expr()->literal($this->removeAccent('%' . $search . '%'))->__toString();

                $txClassificacao = $queryBuild->expr()->lower('cl.tx_classificacao')->__toString();
                $noTipoDocumento = $queryBuild->expr()->lower('tp_doc.no_tipo_documento')->__toString();
                $noPessoaOrigem  = $queryBuild->expr()->lower('un.no_pessoa')->__toString();
                $dsTpHistArquivo = $queryBuild->expr()->lower('tha.ds_tipo_historico_arquivo')->__toString();

                $translate = "TRANSLATE(%s, 'áàâãäéèêëíìîĩïóòôõöúùûũüÁÀÂÃÄÉÈÊËÍÌÎĨÏÓÒÔÕÖÚÙÛŨÜçÇ', 'aaaaaeeeeiiiiiooooouuuuuAAAAAEEEEIIIIIOOOOOUUUUUcC')";

                $optionalCondition =
                        ' AND ('. $queryBuild->expr()->like(sprintf($translate, $txClassificacao), $literalExpr)->__toString()
                        .' OR ' . $queryBuild->expr()->like(sprintf($translate, $noTipoDocumento), $literalExpr)->__toString()
                        .' OR ' . $queryBuild->expr()->like(sprintf($translate, $noPessoaOrigem ), $literalExpr)->__toString()
                        .' OR ' . $queryBuild->expr()->like(sprintf($translate, $dsTpHistArquivo), $literalExpr)->__toString()
                        .') ';
            }
        }

        $sql .= " {$orderBy}";

        $strQuery = sprintf(
                    $sql,
                    $optionalCondition,
                    $sqEmprestado,
                    $sqTipoArtefatoProcesso,
                    $sqTipoArtefato
                );

        return $this->_em
                    ->createNativeQuery($strQuery, $rsm)
                    ->useResultCache(FALSE);
    }

    /**
     * Listagem de artefatos de migração inconsistentes.
     *
     * @param \Core_Dto_Abstract $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGridArtefatosInconsistentes (\Core_Dto_Search $dto)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();

        $rsm->addScalarResult('total_record' , 'totalRecord' , 'integer');
        $rsm->addScalarResult('sq_artefato'  , 'sqArtefato'  , 'integer');
        $rsm->addScalarResult('nu_digital'   , 'nuDigital'   , 'string');
        $rsm->addScalarResult('nu_artefato'  , 'nuArtefato'  , 'string');
        $rsm->addScalarResult('dt_cadastro'  , 'dtCadastro'  , 'zenddate');

        $not  = "NOT";
        $proc = "LEFT";

        if( $dto->sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ){
            $not  = "";
            $proc = "";
        }

        $sql = 'SELECT COUNT(sq_artefato) OVER() AS total_record, *
                  FROM  (
                            SELECT a.sq_artefato,
                                   LPAD(CAST(a.nu_digital AS VARCHAR), 7, \'0\') AS nu_digital,
                                   a.dt_cadastro,
                                   sgdoce.formata_numero_artefato(a.nu_artefato, ap.co_ambito_processo ) as nu_artefato
                              FROM sgdoce.artefato a
                         LEFT JOIN sgdoce.tramite_artefato ta using(sq_artefato)
                    '.$proc.' JOIN sgdoce.artefato_processo ap ON a.sq_artefato = ap.sq_artefato
                             WHERE a.st_migracao
                               AND a.nu_digital IS '.$not.' NULL
                               AND a.ar_inconsistencia IS NOT NULL
                               AND FALSE = ANY(a.ar_inconsistencia)
                               AND ta.st_ultimo_tramite
                               AND ta.sq_pessoa_destino IS NULL
                               %1$s
                        ) AS totalizador
              ORDER BY nu_digital, nu_artefato NULLS LAST';

        $optionalCondition = '';

        $search = mb_strtolower($dto->getSearch(), 'UTF-8');

        if($search){
            $queryBuild    = $this->_em->createQueryBuilder();

            if( $dto->sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ){
                $nuArtefato    = $this->_em->createQueryBuilder()->expr()->lower("TRANSLATE(a.nu_artefato, './-', '')")->__toString();
                $optionalCondition = " AND (" . $queryBuild->expr()->eq('a.nu_artefato', $queryBuild->expr()->literal($search))->__toString() . " OR " .
                                           $queryBuild->expr()->eq($nuArtefato, $queryBuild->expr()->literal(str_replace(array('.','/','-'), '', $search)))->__toString() . ")";
            } else {
                $optionalCondition = " AND " . $queryBuild->expr()->eq('a.nu_digital', $search)->__toString();
            }
        }

        $query = $this->_em->createNativeQuery(sprintf($sql, $optionalCondition), $rsm);
        $query->setParameter('sqUnidadeOrg'  , \Core_Integration_Sica_User::getUserUnit())
              ->useResultCache(false);

        return $query;
    }

    /**
     * MÉTODO CONSULTA TODOS PROCESSOS DENTRO DA ÁREA DE TRABALHO DA PESSOA INFORMADA.
     *
     * @param array criteria
     * @return array
     */
    public function searchProcEletAutoComplete( $criteria )
    {
//        $objQb = $this->_em->createQueryBuilder()
//                           ->select('at')
//                           ->from('app:VwAreaTrabalho', 'at')
//                           ->where('at.sqPessoaRecebimento = :sqPessoaRecebimento')
//                           ->andWhere('at.sqTipoArtefato = :sqTipoArtefato')
//                           ->andWhere('at.nuArtefato LIKE :nuArtefato')
//                           ->andWhere('at.sqArtefato <> :sqArtefato')
//                           ->setParameters($criteria);
//
//        return $objQb->getQuery()->getArrayResult();


        $sql = "SELECT at.sq_artefato,
                       at.nu_artefato
                  FROM sgdoce.fn_show_area_trabalho(NULL, :sqTipoArtefato, :sqPessoaRecebimento, :sqUnidadeRecebimento, :nuArtefato) at
                 WHERE at.sq_artefato <> :sqArtefato";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato',    'sqArtefato',   'integer');
        $rsm->addScalarResult('nu_digital' ,    'nuDigital' ,   'string' );
        $rsm->addScalarResult('nu_artefato',    'nuArtefato',   'string' );

        $nq = $this->_em->createNativeQuery($sql, $rsm);
        $nq->setParameters($criteria);

        return $nq->getArrayResult();
    }

    public function getNotification()
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('tipo'      , 'tipo'      , 'integer');
        $rsm->addScalarResult('intervalo' , 'intervalo' , 'integer');
        $rsm->addScalarResult('qtde'      , 'qtde'      , 'integer');

        $sqlSGI       = "";
        $sqUnidadeOrg = (integer)\Core_Integration_Sica_User::getUserUnit();

        if (\Zend_Registry::get('isUserSgi')) {
            $sqlSGI = "UNION

                      SELECT 2 AS tipo
                             ,2 AS intervalo
                             ,COUNT(*) AS qtde
                        FROM solicitacao AS s
                        JOIN vw_ultimo_status_solicitacao AS vuss USING (sq_solicitacao)
                       WHERE vuss.sq_tipo_status_solicitacao = " . \Core_Configuration::getSgdoceTipoStatusSolicitacaoAberta() . "
                      HAVING COUNT(*) > 0

                       UNION

                      SELECT 2 AS tipo
                             ,1 AS intervalo
                             ,COUNT(*) AS qtde
                        FROM solicitacao AS s
                        JOIN vw_ultimo_status_solicitacao as vuss USING (sq_solicitacao)
                       WHERE vuss.sq_tipo_status_solicitacao = " . \Core_Configuration::getSgdoceTipoStatusSolicitacaoEmAndamento() . "
                         AND vuss.sq_pessoa_responsavel = :sqPessoa
                      HAVING COUNT(*) > 0";
        }

        $sql = "WITH dias_prazo_demanda AS (
                        SELECT 1 AS tipo
                               ,EXTRACT(day FROM dt_prazo - CURRENT_DATE) AS qtd_dias
                               ,sq_pessoa_destino
                               ,sq_unidade_org_pessoa_destino
                               ,sq_prazo
                          FROM prazo
                         WHERE dt_resposta IS NULL
                )

                SELECT 1 AS tipo
                       ,5 AS intervalo
                       ,COUNT(*) AS qtde
                  FROM prazo
                 WHERE dt_resposta is null
                   AND sq_unidade_org_pessoa_destino = {$sqUnidadeOrg}
                 GROUP BY tipo

                UNION

                SELECT tipo
                       ,4 AS intervalo
                       ,COUNT(*) AS qtde
                  FROM dias_prazo_demanda
                 WHERE qtd_dias > 5
                   AND sq_pessoa_destino = :sqPessoa
                   AND sq_unidade_org_pessoa_destino = {$sqUnidadeOrg}
                 GROUP BY tipo

                UNION

                SELECT tipo
                       ,3 AS intervalo
                       ,COUNT(*) AS qtde
                  FROM dias_prazo_demanda
                 WHERE qtd_dias BETWEEN 2 AND 5
                   AND sq_pessoa_destino = :sqPessoa
                   AND sq_unidade_org_pessoa_destino = {$sqUnidadeOrg}
                 GROUP BY tipo

                UNION

                SELECT tipo
                       ,2 AS intervalo
                       ,COUNT(*) AS qtde
                  FROM dias_prazo_demanda
                 WHERE qtd_dias between 0 AND 1
                   AND sq_pessoa_destino = :sqPessoa
                   AND sq_unidade_org_pessoa_destino = {$sqUnidadeOrg}
                 GROUP BY tipo

                UNION

                SELECT tipo
                       ,1 AS intervalo
                       ,COUNT(*) AS qtde
                  FROM dias_prazo_demanda
                 WHERE qtd_dias < 0
                   AND sq_pessoa_destino = :sqPessoa
                   AND sq_unidade_org_pessoa_destino = {$sqUnidadeOrg}
                 GROUP BY tipo

                 {$sqlSGI}

                ORDER BY tipo, intervalo";

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqPessoa', \Core_Integration_Sica_User::getPersonId());

        return $query->execute();
    }

    private function _getResultMapping()
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();

        $rsm->addScalarResult('total_linhas'                 ,'totalRecord'              ,'integer');
        $rsm->addScalarResult('sq_artefato'                  ,'sqArtefato'               ,'integer');
        $rsm->addScalarResult('sq_pessoa_recebimento'        ,'sqPessoaRecebimento'      ,'integer');
        $rsm->addScalarResult('sq_status_tramite'            ,'sqStatusTramite'          ,'integer');
        $rsm->addScalarResult('sq_pessoa_destino'            ,'sqPessoaDestino'          ,'integer');
        $rsm->addScalarResult('sq_pessoa_origem'             ,'sqPessoaOrigem'           ,'integer');
        $rsm->addScalarResult('sq_tipo_pessoa_destino'       ,'sqTipoPessoaDestino'      ,'integer');
        $rsm->addScalarResult('sq_tipo_artefato'             ,'sqTipoArtefato'           ,'integer');
        $rsm->addScalarResult('sq_prioridade'                ,'sqPrioridade'             ,'integer');
        $rsm->addScalarResult('nu_digital'                   ,'nuDigital'                ,'string');
        $rsm->addScalarResult('nu_artefato'                  ,'nuArtefato'               ,'string');
        $rsm->addScalarResult('nu_tramite'                   ,'nuTramite'                ,'integer');
        $rsm->addScalarResult('dt_cadastro'                  ,'dtCadastro'               ,'zenddate');
        $rsm->addScalarResult('no_tipo_documento'            ,'noTipoDocumento'          ,'string');
        $rsm->addScalarResult('tx_assunto'                   ,'txAssunto'                ,'string');
        $rsm->addScalarResult('no_pessoa_origem'             ,'noPessoaOrigem'           ,'string');
        $rsm->addScalarResult('tx_codigo_rastreamento'       ,'txCodigoRastreamento'     ,'string');
        $rsm->addScalarResult('tx_movimentacao'              ,'txMovimentacao'           ,'string');
        $rsm->addScalarResult('co_ambito_processo'           ,'coAmbitoProcesso'         ,'string');
        $rsm->addScalarResult('in_abre_processo'             ,'inAbreProcesso'           ,'boolean');
        $rsm->addScalarResult('pode_cancelar_tramite'        ,'podeCancelarTramite'      ,'boolean');
        $rsm->addScalarResult('pode_receber_tramite'         ,'podeReceberTramite'       ,'boolean');
        $rsm->addScalarResult('has_vinculo'                  ,'hasVinculo'               ,'boolean');
        $rsm->addScalarResult('has_tramite_rastreamento'     ,'hasTramiteRastreamento'   ,'boolean');
        $rsm->addScalarResult('pode_arquivar'                ,'podeArquivar'             ,'boolean');
        $rsm->addScalarResult('arquivado'                    ,'arquivado'                ,'boolean');
        $rsm->addScalarResult('has_imagem'                   ,'hasImagem'                ,'boolean');
        $rsm->addScalarResult('foi_citado'                   ,'foiCitado'                ,'boolean');
        $rsm->addScalarResult('is_tramite_externo'           ,'isTramiteExterno'         ,'boolean');
        $rsm->addScalarResult('has_solicitacao_aberta'       ,'hasSolicitacaoAberta'     ,'boolean');
        $rsm->addScalarResult('sq_tipo_documento'            ,'sqTipoDocumento'          ,'integer');
        $rsm->addScalarResult('sq_pessoa_destino_interno'    ,'sqPessoaDestinoInterno'   ,'integer');
        $rsm->addScalarResult('sq_unidade_org_origem_tramite','sqUnidadeOrgOrigemTramite','integer');

        $rsm->addScalarResult('is_inconsistente'             ,'isInconsistente'          ,'boolean');
        $rsm->addScalarResult('has_primeira_peca'            ,'hasPrimeiraPeca'          ,'boolean');

        return $rsm;
    }
    
    /**
     * Método para pesquisa de artefato por ID,
     * não é necessário o artefato estar na área do trabalho para retornar a pesquisa.
     * 
     * @return 
     */
    public function findById( $sqArtefato )
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();

        $rsm->addScalarResult('sq_artefato'         , 'sqArtefato'          , 'integer');
        $rsm->addScalarResult('nu_digital'          , 'nuDigital'           , 'string');
        $rsm->addScalarResult('nu_artefato'         , 'nuArtefato'          , 'string');
        $rsm->addScalarResult('sq_tipo_documento'   , 'sqTipoDocumento'     , 'integer');
        $rsm->addScalarResult('no_tipo_documento'   , 'noTipoDocumento'     , 'string');
        $rsm->addScalarResult('sq_tipo_artefato'    , 'sqTipoArtefato'      , 'integer');
        $rsm->addScalarResult('dt_cadastro'         , 'dtCadastro'          , 'zenddate');
        $rsm->addScalarResult('co_ambito_processo'  , 'coAmbitoProcessos'   , 'string');
        $rsm->addScalarResult('no_pessoa_origem'    , 'noPessoaOrigem'      , 'string');

        $sql = "SELECT
                    art.sq_artefato,
                    art.nu_digital,
                    art.nu_artefato,
                    art.dt_cadastro,
                    art_ass.sq_tipo_artefato,
                    art_pro.co_ambito_processo,
                    tip_doc.sq_tipo_documento,
                    tip_doc.no_tipo_documento,
                    pes_sgd.no_pessoa as no_pessoa_origem
                FROM artefato art
                JOIN tipo_artefato_assunto art_ass ON art.sq_tipo_artefato_assunto = art_ass.sq_tipo_artefato_assunto
                JOIN tipo_documento tip_doc ON art.sq_tipo_documento = tip_doc.sq_tipo_documento
                -- ORIGEM
                JOIN pessoa_artefato pes_art ON art.sq_artefato = pes_art.sq_artefato
                JOIN pessoa_sgdoce pes_sgd ON pes_art.sq_pessoa_sgdoce = pes_sgd.sq_pessoa_sgdoce                
                LEFT JOIN artefato_processo art_pro ON art.sq_artefato = art_pro.sq_artefato
                WHERE art.sq_artefato = :sqArtefato
                AND pes_art.sq_pessoa_funcao = :sqPessoaFuncao";

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqArtefato', $sqArtefato)
              ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoOrigem())
              ->useResultCache(false);
        
        return $query->getSingleResult();
    }

}
