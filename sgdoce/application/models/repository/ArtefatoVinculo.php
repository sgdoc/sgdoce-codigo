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
 * Classe para Repository de ArtefatoVinculo
 *
 * @package      Model
 * @subpackage   Repository
 * @name         ArtefatoVinculo
 * @version      1.0.0
 * @since        2012-11-20
 */
class ArtefatoVinculo extends \Core_Model_Repository_Base
{
    const T_SGDOCE_TIPO_VINCULO_ARTEFATO_ANEXACAO      = 'SGDOCE_TIPO_VINCULO_ARTEFATO_ANEXACAO';
    const T_SGDOCE_TIPO_VINCULO_ARTEFATO_APENSACAO     = 'SGDOCE_TIPO_VINCULO_ARTEFATO_APENSACAO';
    const T_SGDOCE_TIPO_VINCULO_ARTEFATO_INSERCAO      = 'SGDOCE_TIPO_VINCULO_ARTEFATO_INSERCAO';
    const T_SGDOCE_TIPO_ARTEFATO_PROCESSO              = 'SGDOCE_TIPO_ARTEFATO_PROCESSO';
    const T_SGDOCE_TIPO_ARTEFATO_DOCUMENTO             = 'SGDOCE_TIPO_ARTEFATO_DOCUMENTO';
    const T_SGDOCE_TIPO_VINCULO_ARTEFATO_REFERENCIA    = 'SGDOCE_TIPO_VINCULO_ARTEFATO_REFERENCIA';
    const T_SGDOCE_TIPO_VINCULO_ARTEFATO_APOIO         = 'SGDOCE_TIPO_VINCULO_ARTEFATO_APOIO';
    const T_SGDOCE_TIPO_VINCULO_ARTEFATO_DESPACHO      = 'SGDOCE_TIPO_VINCULO_ARTEFATO_DESPACHO';
    const T_SGDOCE_TIPO_VINCULO_ARTEFATO_AUTUACAO      = 'SGDOCE_TIPO_VINCULO_ARTEFATO_AUTUACAO';
    const T_SGDOCE_TIPO_STATUS_SOLICITACAO_FINALIZADA  = 'SGDOCE_TIPO_STATUS_SOLICITACAO_FINALIZADA';
    const T_SGDOCE_GRAU_ACESSO_SIGILOSO                = 'SGDOCE_GRAU_ACESSO_SIGILOSO';

    /**
     * método que retorna dados para grid
     * @param \Core_Dto_Abstract $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGridVinculacaoPeca (\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
                ->select(
                        'af.nuArtefato',
                        'af.nuDigital',
                        'av.inOriginal',
                        'av.sqArtefatoVinculo'
                )
                ->from('app:ArtefatoVinculo', 'av')
                ->leftJoin('av.sqArtefatoPai', 'ap')
                ->leftJoin('av.sqArtefatoFilho', 'af')
                ->leftJoin('av.sqTipoVinculoArtefato', 'tva')
                ->andWhere('av.sqTipoVinculoArtefato in(:insercao)')
                ->setParameters(array('insercao' => array(\Core_Configuration::getSgdoceTipoVinculoArtefatoInsercao(),
                    \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao())))
                ->andWhere('av.dtRemocaoVinculo IS NULL')
                ->andWhere('av.sqArtefatoPai = :id')
                ->setParameter('id', $dto->getSqArtefato());

        return $queryBuilder;
    }

    /**
     * método que retorna dados para grid
     * @param \Core_Dto_Abstract $dto
     * @return \Doctrine\ORM\NativeQuery
     */
    public function listGridVinculacaoReferencia (\Core_Dto_Search $dto)
    {
        $sql = 'SELECT ta.no_tipo_artefato
                      ,formata_numero_artefato(art.nu_artefato,ap.co_ambito_processo) AS nu_artefato
                      ,art.nu_digital
                      ,av.dt_vinculo

                  FROM artefato_vinculo av
                  JOIN artefato art ON av.sq_artefato_filho = art.sq_artefato
                  JOIN tipo_vinculo_artefato tva ON av.sq_tipo_vinculo_artefato = tva.sq_tipo_vinculo_artefato
                  JOIN tipo_artefato_assunto taa ON art.sq_tipo_artefato_assunto = taa.sq_tipo_artefato_assunto
                  JOIN tipo_artefato ta ON taa.sq_tipo_artefato = ta.sq_tipo_artefato
             LEFT JOIN artefato_processo ap ON art.sq_artefato = ap.sq_artefato
                 WHERE av.sq_tipo_vinculo_artefato = %1$d
                   AND av.dt_remocao_vinculo IS NULL
                   AND av.sq_artefato_pai = %2$d
              ORDER BY av.dt_vinculo ASC';


        $strSql = sprintf(
                $sql
                ,\Core_Configuration::getSgdoceTipoVinculoArtefatoReferencia()
                ,$dto->getSqArtefato()
            );

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('no_tipo_artefato',    'noTipoArtefato',  'string');
        $rsm->addScalarResult('nu_digital',          'nuDigital',       'string');
        $rsm->addScalarResult('nu_artefato',         'nuArtefato',      'string');
        $rsm->addScalarResult('dt_vinculo',          'dtVinculo',       'zenddate');

        $nativeQuery = $this->_em->createNativeQuery($strSql, $rsm);

        $nativeQuery->useResultCache(false);

        return $nativeQuery;
    }

    /**
     * método que retorna dados para Vizualizar os vinculos do artefato
     * @param \Core_Dto_Abstract $dto
     * @return array
     */
    public function findVinculoArtefato (\Core_Dto_Search $dto, array $notInVinculo = array())
    {
        $queryBuilder = $this->getEntityManager()
                ->createQueryBuilder()
                ->distinct()
                ->select('a.sqArtefato,IDENTITY(a.nuDigital) as nuDigital,ta.sqTipoArtefato,a.nuArtefato,'
                        .'tva.sqTipoVinculoArtefato,aav.sqAnexoArtefatoVinculo')
                ->from('app:ArtefatoVinculo', 'av')
                ->innerJoin('av.sqArtefatoFilho', 'a')
                ->innerJoin('av.sqTipoVinculoArtefato', 'tva')
                ->leftJoin('av.sqAnexoArtefatoVinculo','aav')
                ->leftJoin('a.sqTipoArtefatoAssunto', 'taa')
                ->leftJoin('taa.sqTipoArtefato', 'ta')
                ->andWhere('av.sqArtefatoPai = :sqArtefato')
                ->setParameter('sqArtefato', $dto->getSqArtefato())
                ->andWhere('av.dtRemocaoVinculo is null');



        if ($notInVinculo) {
            $queryBuilder->andWhere($queryBuilder->expr()->notIn('av.sqTipoVinculoArtefato', $notInVinculo));
        }
        return $res = $queryBuilder->getQuery()->execute();
    }

    public function searchReferencia ($dto)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field1 = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('a.nuArtefato'));

        $field2 = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('a.nuDigital'));

    	$query = $queryBuilder->select('a.sqArtefato,a.nuArtefato,a.nuDigital')
            ->from('app:ArtefatoVinculo', 'av')
            ->innerJoin('av.sqArtefatoFilho', 'a')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field1 .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            )
            ->orWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field2 .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            )
            ->orderBy('av.sqArtefatoFilho');
    	$res = $queryBuilder->getQuery()->execute();
    	$out = array();
    	foreach ($res as $item){
                $pos1 = stripos($query, 'e');
                if(is_int($pos1)){
                    $out[$item['sqArtefato']] = $item['nuDigital'];
                }else{
                    $out[$item['sqArtefato']] = $item['nuArtefato'];
                }
    	}
    	return $out;
    }

    /**
     * método que verifica se o artefato possui vinculo com pai ou como filho com outro artefato
     * @param \Core_Dto_Abstract $dto
     * @return array
     */
    public function verificaVinculoArfato (\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('av.sqArtefatoVinculo')
            ->from('app:ArtefatoVinculo', 'av')
            ->where('av.sqArtefatoFilho = :sqArtefatoFilho')
            ->andWhere('av.sqTipoVinculoArtefato NOT IN(:sqTipoVinculoArtefato)')
            ->setParameter('sqArtefatoFilho', $dto->getSqArtefatoFilho())
            ->setParameter('sqTipoVinculoArtefato', array(
                \Core_Configuration::getSgdoceTipoVinculoArtefatoApoio(),
                \Core_Configuration::getSgdoceTipoVinculoArtefatoDespacho(),
                \Core_Configuration::getSgdoceTipoVinculoArtefatoReferencia(),
            ));

        return $res = $queryBuilder->getQuery()->execute();
    }

    /**
     * método que verifica se o artefato possui vinculo com pai ou como filho com outro artefato
     * @param \Core_Dto_Abstract $dto
     * @return array
     */
    public function verificaVinculoArfatoPai ($dto)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('av.sqArtefatoVinculo')
            ->from('app:ArtefatoVinculo', 'av')
            ->where('av.sqArtefatoPai = :sqArtefatoPai')
            ->andWhere('av.sqArtefatoFilho =:sqArtefatoFilho')
            ->andWhere('av.dtRemocaoVinculo is null')
            ->andWhere('av.inOriginal = :inOriginal')
            ->andWhere('av.sqTipoVinculoArtefato = :sqTipoVinculoArtefato')
            ->setParameter('sqTipoVinculoArtefato', $dto->getSqTipoVinculoArtefato()->getSqTipoVinculoArtefato())
            ->setParameter('inOriginal', $dto->getInOriginal())
            ->setParameter('sqArtefatoPai', $dto->getSqArtefatoPai()->getSqArtefato())
            ->setParameter('sqArtefatoFilho', $dto->getSqArtefatoFilho()->getSqArtefato());
        return $res = $queryBuilder->getQuery()->execute();
    }

    /**
     * Recupera todos os vinculos de artefatos do tipo processo em caso de tramite
     * externo para montagem da guia de tramite
     *
     * @param \Core_Dto_Abstract $dto
     * @return array
     */
    public function findProcessGuiaProcess (\Core_Dto_Abstract $dto)
    {
        $sql  = $this->_getSqlVinculoRecursivo();
        $sql .= " SELECT a.sq_artefato,
                         taa.sq_tipo_artefato,
                         a.nu_digital,
                         a.nu_artefato,
                         null as no_tipo_documento,
                         ap.co_ambito_processo,
                         ps.no_pessoa as no_pessoa_origem
                    FROM arvore AS ar
                    JOIN artefato AS a on ar.sq_artefato_filho = a.sq_artefato
                    JOIN pessoa_artefato AS pa USING (sq_artefato)
                    JOIN pessoa_sgdoce AS ps USING (sq_pessoa_sgdoce)
                    JOIN artefato_processo AS ap USING (sq_artefato)
                    JOIN tipo_artefato_assunto AS taa USING(sq_tipo_artefato_assunto)
                   WHERE taa.sq_tipo_artefato = :sqTipoArtefato
                     AND pa.sq_pessoa_funcao = :sqPessoaFuncao
                     AND ar.sq_tipo_vinculo_artefato NOT IN(:sqTipoVinculoArtefato)";

        $query = $this->_getQuerySqlGuiaTramiteExterno($dto, $sql);

        return $query->getScalarResult();
    }

    /**
     * Recupera todos os vinculos de artefatos do tipo documento em caso de tramite
     * externo para montagem da guia de tramite
     *
     * @param \Core_Dto_Abstract $dto
     * @return array
     */
    public function findProcessGuiaDocument (\Core_Dto_Abstract $dto)
    {
        $sql  = $this->_getSqlVinculoRecursivo();
        $sql .= " SELECT a.sq_artefato,
                         taa.sq_tipo_artefato,
                         a.nu_digital,
                         a.nu_artefato,
                         td.no_tipo_documento,
                         null AS co_ambito_processo,
                         ps.no_pessoa AS no_pessoa_origem
                    FROM arvore AS ar
                    JOIN artefato AS a ON ar.sq_artefato_filho = a.sq_artefato
                    JOIN tipo_documento AS td using(sq_tipo_documento)
                    JOIN pessoa_artefato AS pa USING (sq_artefato)
                    JOIN pessoa_sgdoce AS ps USING (sq_pessoa_sgdoce)
                    JOIN tipo_artefato_assunto AS taa USING(sq_tipo_artefato_assunto)
                   WHERE taa.sq_tipo_artefato = :sqTipoArtefato
                     AND pa.sq_pessoa_funcao = :sqPessoaFuncao
                     AND ar.sq_tipo_vinculo_artefato NOT IN(:sqTipoVinculoArtefato)";

        $query = $this->_getQuerySqlGuiaTramiteExterno($dto, $sql);

        return $query->getScalarResult();
    }

    /**
     *
     * @return string
     */
    private function _getSqlVinculoRecursivo ()
    {
        $sql = "WITH RECURSIVE arvore (sq_artefato_vinculo, sq_artefato_pai, sq_artefato_filho, dt_vinculo, sq_tipo_vinculo_artefato) as (
                        SELECT av.sq_artefato_vinculo
                              ,av.sq_artefato_pai
                              ,av.sq_artefato_filho
                              ,av.dt_vinculo
                              ,av.sq_tipo_vinculo_artefato
                          FROM artefato_vinculo AS av
                         WHERE sq_artefato_pai = :sqArtefato
                         UNION ALL
                        SELECT av2.sq_artefato_vinculo
                              ,av2.sq_artefato_pai
                              ,av2.sq_artefato_filho
                              ,av2.dt_vinculo
                              ,av2.sq_tipo_vinculo_artefato
                          FROM artefato_vinculo AS av2
                         INNER JOIN arvore AS a ON av2.sq_artefato_pai = a.sq_artefato_filho
                ) ";
        return $sql;
    }

    /**
     *
     * @param \Core_Dto_Abstract $dto
     * @param string $sql
     * @return \Doctrine\ORM\NativeQuery
     */
    private function _getQuerySqlGuiaTramiteExterno(\Core_Dto_Abstract $dto, $sql)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('sq_artefato'       , 'sqArtefato');
        $rsm->addScalarResult('sq_tipo_artefato'  , 'sqTipoArtefato');
        $rsm->addScalarResult('nu_digital'        , 'nuDigital');
        $rsm->addScalarResult('nu_artefato'       , 'nuArtefato');
        $rsm->addScalarResult('no_tipo_documento' , 'noTipoDocumento');
        $rsm->addScalarResult('co_ambito_processo', 'coAmbitoProcesso');
        $rsm->addScalarResult('no_pessoa_origem'  , 'noPessoaOrigem');

        $arrNotIn = array(
            \Core_Configuration::getSgdoceTipoVinculoArtefatoReferencia(),
            \Core_Configuration::getSgdoceTipoVinculoArtefatoApoio(),
            \Core_Configuration::getSgdoceTipoVinculoArtefatoDespacho(),
        );

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqArtefato'    , $dto->getSqArtefato());
        $query->setParameter('sqTipoArtefato', $dto->getSqTipoArtefato());
        $query->setParameter('sqPessoaFuncao', $dto->getSqPessoaFuncao());
        $query->setParameter('sqTipoVinculoArtefato', $arrNotIn);

        return $query;
    }

    public function vinculoListGrid (\Core_Dto_Abstract $dto)
    {
        if( $this->hasNoOrderVinculo( $dto->getSqArtefatoParent() ) ) {
            $this->setOrderIn( $dto->getSqArtefatoParent() );
        }

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('total_record',             'totalRecord',           'integer');
        $rsm->addScalarResult('nu_ordem',                 'nuOrdem',               'integer');
        $rsm->addScalarResult('sq_artefato_vinculo',      'sqArtefatoVinculo',     'integer');
        $rsm->addScalarResult('sq_artefato',              'sqArtefato',            'integer');
        $rsm->addScalarResult('sq_artefato_pai',          'sqArtefatoPai',         'integer');
        $rsm->addScalarResult('sq_tipo_artefato',         'sqTipoArtefato',        'integer');
        $rsm->addScalarResult('sq_tipo_vinculo_artefato', 'sqTipoVinculoArtefato', 'integer');
        $rsm->addScalarResult('nu_fvalue',                'nuFvalue',              'integer');
        $rsm->addScalarResult('nu_lvalue',                'nuLvalue',              'integer');
        $rsm->addScalarResult('nu_digital',               'nuDigital',             'string');
        $rsm->addScalarResult('nu_artefato',              'nuArtefato',            'string');
        $rsm->addScalarResult('dt_tramite',               'dtTramite',             'string');
        $rsm->addScalarResult('no_tipo_documento',        'noTipoDocumento',       'string');
        $rsm->addScalarResult('tx_assunto',               'txAssunto',             'string');
        $rsm->addScalarResult('tx_movimentacao',          'txMovimentacao',        'string');
        $rsm->addScalarResult('is_vinculo',               'isVinculo',             'boolean');
        $rsm->addScalarResult('is_disponivel',            'isDisponivel',          'boolean');
        $rsm->addScalarResult('is_anexado',               'isAnexado',             'boolean');
        $rsm->addScalarResult('is_apensado',              'isApensado',            'boolean');
        $rsm->addScalarResult('is_primeira_peca',         'isPrimeiraPeca',        'boolean');

        $strQuery = sprintf('
                   WITH configs AS (
                    SELECT %5$s,
                           %6$s,
                           %7$s,
                           %8$s,
                           %9$s,
                           %12$s,
                           %13$s,
                           %14$s,
                           %15$s,
                           %16$s
                      FROM sicae.lista_constantes(
                                \'%5$s\',
                                \'%6$s\',
                                \'%7$s\',
                                \'%8$s\',
                                \'%9$s\',
                                \'%12$s\',
                                \'%13$s\',
                                \'%14$s\',
                                \'%15$s\',
                                \'%16$s\'
                            ) as c(
                                    %5$s integer,
                                    %6$s integer,
                                    %7$s integer,
                                    %8$s integer,
                                    %9$s integer,
                                    %12$s integer,
                                    %13$s integer,
                                    %14$s integer,
                                    %15$s integer,
                                    %16$s integer
                                 )
            )
            SELECT COUNT(sq_artefato) OVER() as total_record,
                   *
              FROM (
                        (SELECT vin.sq_artefato_vinculo
                               , art.sq_artefato
                               , art_art_ass.sq_tipo_artefato
                               , vin.sq_artefato_pai
                               , vin.sq_tipo_vinculo_artefato
                               , formata_numero_digital(art.nu_digital) as nu_digital
                               , sgdoce.formata_numero_artefato(art.nu_artefato, ap.co_ambito_processo) as nu_artefato
                               , tip_doc.no_tipo_documento
                               , ass.tx_assunto
                               , sgdoce.ultima_movimentacao_artefato(art.sq_artefato) as tx_movimentacao
                               , TRUE AS is_vinculo
                               , (configs.%5$s = vin.sq_tipo_vinculo_artefato)::BOOLEAN AS is_anexado
                               , (vin.sq_tipo_vinculo_artefato IN (configs.%6$s, configs.%9$s)) AS is_apensado
                               , FALSE AS is_disponivel
                               , (configs.%15$s = vin.sq_tipo_vinculo_artefato) as is_primeira_peca
                               , vin.nu_ordem AS nu_ordem
                               , (first_value(vin.nu_ordem) OVER(PARTITION BY vin.sq_artefato_pai ORDER BY vin.nu_ordem ASC)::INTEGER)::INTEGER AS nu_fvalue
                               , (last_value(vin.nu_ordem) OVER(PARTITION BY vin.sq_artefato_pai)::INTEGER)::INTEGER AS nu_lvalue
                          FROM sgdoce.artefato_vinculo AS vin
                    INNER JOIN configs ON TRUE
                    INNER JOIN sgdoce.artefato AS art
                            ON (vin.sq_artefato_filho = art.sq_artefato)
                    INNER JOIN sgdoce.tipo_artefato_assunto AS art_art_ass
                            ON (art.sq_tipo_artefato_assunto = art_art_ass.sq_tipo_artefato_assunto)
                    INNER JOIN sgdoce.assunto AS ass
                         USING (sq_assunto)
                     LEFT JOIN sgdoce.tipo_documento AS tip_doc
                         USING (sq_tipo_documento)
                     LEFT JOIN sgdoce.artefato_processo AS ap
                            ON (art.sq_artefato = ap.sq_artefato)
                         WHERE vin.sq_artefato_pai = %2$d
                           AND vin.sq_tipo_vinculo_artefato NOT IN (configs.%12$s,configs.%13$s,configs.%14$s)
                           AND ((art_art_ass.sq_tipo_artefato = %11$s) OR (%11$s IS NULL))

                      )
                      UNION

                      (SELECT null as sq_artefato_vinculo
                             ,art.sq_artefato
                             ,art_art_ass.sq_tipo_artefato
                             ,NULL AS sq_artefato_pai
                             ,NULL AS sq_tipo_vinculo_artefato
                             ,formata_numero_digital(art.nu_digital) as nu_digital
                             ,sgdoce.formata_numero_artefato(art.nu_artefato, ap.co_ambito_processo) as nu_artefato
                             ,tip_doc.no_tipo_documento
                             ,ass.tx_assunto
                             ,sgdoce.ultima_movimentacao_artefato(art.sq_artefato) as tx_movimentacao
                             ,FALSE AS is_vinculo
                             ,FALSE AS is_anexado
                             ,FALSE AS is_apensado
                             ,TRUE AS is_disponivel
                             ,FALSE AS is_primeira_peca
                             ,NULL AS nu_ordem
                             ,0 AS nu_fvalue
                             ,0 AS nu_lvalue
                        FROM sgdoce.artefato AS art
                  INNER JOIN configs ON TRUE
                  INNER JOIN sgdoce.tramite_artefato uta
                          ON  uta.sq_artefato = art.sq_artefato
                         AND uta.st_ultimo_tramite
                  INNER JOIN sgdoce.tipo_artefato_assunto AS art_art_ass
                       USING (sq_tipo_artefato_assunto)
                  INNER JOIN sgdoce.assunto AS ass
                       USING (sq_assunto)
                   LEFT JOIN sgdoce.artefato_imagem uia
                          ON art.sq_artefato = uia.sq_artefato
                         AND uia.st_ativo
                        JOIN configs const ON TRUE
                   LEFT JOIN sgdoce.artefato_vinculo atv
                          ON sq_tipo_vinculo_artefato = const.SGDOCE_TIPO_VINCULO_ARTEFATO_AUTUACAO
                         AND art.sq_artefato = atv.sq_artefato_pai
                   LEFT JOIN sgdoce.artefato_imagem uiaf
                          ON uiaf.sq_artefato = atv.sq_artefato_filho
                   LEFT JOIN sgdoce.tipo_documento AS tip_doc
                          ON (tip_doc.sq_tipo_documento = art.sq_tipo_documento)
                   LEFT JOIN sgdoce.caixa_artefato AS arq
                          ON (arq.sq_artefato = art.sq_artefato)
                   LEFT JOIN sgdoce.artefato_processo AS ap
                          ON (art.sq_artefato = ap.sq_artefato)
                   LEFT JOIN sgdoce.artefato_vinculo av
                          ON (art.sq_artefato = av.sq_artefato_filho
                         AND av.sq_tipo_vinculo_artefato NOT IN (configs.%12$s,configs.%13$s,configs.%14$s))
                   LEFT JOIN sgdoce.artefato_arquivo_setorial aas
                          ON art.sq_artefato = aas.sq_artefato AND aas.dt_desarquivamento IS NULL
                   LEFT JOIN (
                                SELECT s.sq_artefato,
                                       COUNT(CASE
                                                WHEN uss.sq_tipo_status_solicitacao = configs_1.%16$s THEN NULL::INTEGER
                                                ELSE 1
                                             END) AS qtd_solicitacao_aberta
                                  FROM sgdoce.solicitacao s
                                  JOIN configs configs_1 ON true
                                  JOIN sgdoce.vw_ultimo_status_solicitacao uss USING (sq_solicitacao)
                                 WHERE s.sq_artefato IS NOT NULL
                                 GROUP BY s.sq_artefato
                             ) sol ON sol.sq_artefato = art.sq_artefato
                    WHERE av.sq_artefato_vinculo IS NULL
                        AND (sol.qtd_solicitacao_aberta = 0 OR sol.qtd_solicitacao_aberta IS NULL)
                        AND (sgdoce.formata_numero_digital(art.nu_digital) LIKE \'%4$s%%\' OR LOWER(translate(art.nu_artefato, \'./-\', \'\')) LIKE \'%4$s%%\' )
                        AND EXISTS (    SELECT *
                                          FROM sgdoce.artefato pai
                                    INNER JOIN sgdoce.tipo_artefato_assunto tp_art_pai
                                            ON tp_art_pai.sq_tipo_artefato_assunto = pai.sq_tipo_artefato_assunto
                                    INNER JOIN sgdoce.tipo_artefato tp_pai
                                            ON tp_art_pai.sq_tipo_artefato = tp_pai.sq_tipo_artefato
                                         WHERE pai.sq_artefato = %2$d
                                           AND (art_art_ass.sq_tipo_artefato IN (configs.SGDOCE_TIPO_ARTEFATO_PROCESSO, configs.SGDOCE_TIPO_ARTEFATO_DOCUMENTO)
                                                AND tp_pai.sq_tipo_artefato = configs.SGDOCE_TIPO_ARTEFATO_PROCESSO)
                                                OR (art_art_ass.sq_tipo_artefato = (configs.SGDOCE_TIPO_ARTEFATO_DOCUMENTO)
                                                AND tp_pai.sq_tipo_artefato = configs.SGDOCE_TIPO_ARTEFATO_DOCUMENTO))
                        AND art.sq_artefato != %2$d
                        AND ((uta.sq_status_tramite > %19$d AND uta.sq_pessoa_recebimento = %1$d AND (uta.sq_unidade_org_tramite = %3$d OR uta.sq_pessoa_destino = %3$d)))
                        AND arq.sq_artefato IS NULL -- artefatos arquivados não podem ser vinculados
                        AND aas.sq_artefato IS NULL -- artefatps arqiovados no setor não podem ser vinculados
                        AND art_art_ass.sq_tipo_artefato in (%10$s)
                        AND (uia.sq_artefato_imagem IS NOT NULL OR (art_art_ass.sq_tipo_artefato = %18$d AND uiaf.sq_artefato_imagem IS NOT NULL))
                        AND ((art_art_ass.sq_tipo_artefato = %11$s) OR (%11$s IS NULL))
                   )
                ) sub1'
              , $dto->getSqPessoa()
              , $dto->getSqArtefatoParent()
              , $dto->getSqUnidadeOrg()
              , $dto->getNuArtefato()
              , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_ANEXACAO
              , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_APENSACAO
              , self::T_SGDOCE_TIPO_ARTEFATO_PROCESSO
              , self::T_SGDOCE_TIPO_ARTEFATO_DOCUMENTO
              , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_INSERCAO
              , implode(',', (array) $dto->getTipoArtefatoAceito())
              , ($dto->getSqArtefatoTipo()) ? (integer) $dto->getSqArtefatoTipo() : 'NULL'
              , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_REFERENCIA
              , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_APOIO
              , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_DESPACHO
              , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_AUTUACAO
              , self::T_SGDOCE_TIPO_STATUS_SOLICITACAO_FINALIZADA
              , \Core_Configuration::getSgdoceStatusTramiteCancelado()
              , \Core_Configuration::getSgdoceTipoArtefatoProcesso()
              , \Core_Configuration::getSgdoceStatusTramiteTramitado()
             );

        return $this->_em->createNativeQuery($strQuery, $rsm)->useResultCache(false);
    }

    public function arvoreVinculo ($sqArtefato)
    {
        $sql = 'WITH RECURSIVE
                configs AS (
                        SELECT %2$s,
                               %3$s,
                               %4$s
                          FROM sicae.lista_constantes (
                                    \'%2$s\',
                                    \'%3$s\',
                                    \'%4$s\'
                                ) AS c (
                                        %2$s integer,
                                        %3$s integer,
                                        %4$s integer)
                ),
                raiz AS (
                    SELECT art.sq_artefato
                      FROM sgdoce.artefato AS art
                     WHERE art.sq_artefato = (
                        SELECT sgdoce.obter_vinculo_pai(%1$d)
                    )
                ),
                arvore_artefato AS ((
                            SELECT sq_artefato AS sq_artefato_pai,
                                   NULL sq_artefato_filho,
                                   0::INTEGER AS sq_tipo_vinculo_artefato,
                                   sq_artefato::TEXT trilha,
                                   NULL::TIMESTAMP dt_vinculo,
                                   ARRAY[0]::INTEGER[] nivel,
                                   1::INTEGER nu_ordem
                              FROM raiz
                             UNION
                            SELECT art_vin.sq_artefato_pai,
                                   art_vin.sq_artefato_filho,
                                   art_vin.sq_tipo_vinculo_artefato,
                                   sq_artefato_pai || \'-->\' || sq_artefato_filho::TEXT trilha,
                                   art_vin.dt_vinculo,
                                   ARRAY[0, (ROW_NUMBER() OVER(PARTITION BY art_vin.sq_artefato_pai ORDER BY art_vin.dt_vinculo ASC))::INTEGER]::INTEGER[] nivel,
                                   COALESCE(art_vin.nu_ordem, (ROW_NUMBER() OVER(PARTITION BY art_vin.sq_artefato_pai ORDER BY art_vin.dt_vinculo ASC))::INTEGER) nu_ordem
                              FROM sgdoce.artefato_vinculo AS art_vin
                              JOIN configs ON TRUE
                        INNER JOIN raiz ON raiz.sq_artefato = sq_artefato_pai
                             WHERE art_vin.sq_tipo_vinculo_artefato NOT IN (configs.%2$s,configs.%3$s,configs.%4$s)
                    ) UNION ALL (
                            SELECT art_vin.sq_artefato_pai,
                                   art_vin.sq_artefato_filho,
                                   art_vin.sq_tipo_vinculo_artefato,
                                   art_arv.trilha || \'-->\' || art_vin.sq_artefato_filho,
                                   art_vin.dt_vinculo,
                                   ARRAY_APPEND(
                                       art_arv.nivel, (ROW_NUMBER() OVER(PARTITION BY art_vin.sq_artefato_pai ORDER BY art_vin.dt_vinculo ASC))::INTEGER),
                                   COALESCE(art_vin.nu_ordem, (ROW_NUMBER() OVER(PARTITION BY art_vin.sq_artefato_pai ORDER BY art_vin.dt_vinculo ASC))::INTEGER) nu_ordem
                              FROM sgdoce.artefato_vinculo AS art_vin
                              JOIN configs ON TRUE
                        INNER JOIN arvore_artefato AS art_arv ON art_arv.sq_artefato_filho = art_vin.sq_artefato_pai
                             WHERE art_vin.sq_tipo_vinculo_artefato NOT IN (configs.%2$s,configs.%3$s,configs.%4$s)
                ))
                    SELECT arv_art.sq_artefato_pai,
                           arv_art.sq_artefato_filho,
                           arv_art.sq_tipo_vinculo_artefato,
                           tip_art.sq_tipo_artefato,
                           tip_art.no_tipo_artefato,
                           COALESCE(sgdoce.formata_numero_digital(art.nu_digital),
                                    sgdoce.formata_numero_artefato(art.nu_artefato, ap.co_ambito_processo)
                            ) AS nu_artefato
                           ,nivel
                           ,nu_ordem
                      FROM arvore_artefato              AS arv_art
                INNER JOIN sgdoce.artefato              AS art ON art.sq_artefato = COALESCE(arv_art.sq_artefato_filho, arv_art.sq_artefato_pai)
                INNER JOIN sgdoce.tipo_artefato_assunto AS tip_art_ass using(sq_tipo_artefato_assunto)
                INNER JOIN sgdoce.tipo_artefato         AS tip_art using(sq_tipo_artefato)
                 LEFT JOIN sgdoce.artefato_processo     AS ap ON art.sq_artefato = ap.sq_artefato
                  ORDER BY nu_ordem ASC, nivel ASC';

        $strQuery = sprintf(
                  $sql
                , $sqArtefato
                , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_REFERENCIA
                , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_APOIO
                , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_DESPACHO
        );

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato_pai'         , 'sqArtefatoPai'        , 'integer');
        $rsm->addScalarResult('sq_artefato_filho'       , 'sqArtefatoFilho'      , 'integer');
        $rsm->addScalarResult('sq_tipo_vinculo_artefato', 'sqTipoVinculoArtefato', 'string');
        $rsm->addScalarResult('no_tipo_artefato'        , 'noTipoArtefato'       , 'string');
        $rsm->addScalarResult('sq_tipo_artefato'        , 'sqTipoArtefato'       , 'string');
        $rsm->addScalarResult('nu_artefato'             , 'nuArtefato'           , 'string');
        $rsm->addScalarResult('nivel'                   , 'nivel'                , 'string');

        return
        $this->_em
             ->createNativeQuery($strQuery, $rsm)->useResultCache(false);
    }

    public function arvoreVinculoMigracao( $dto )
    {
        $sql = 'WITH RECURSIVE
                configs AS (
                            SELECT MAX(CASE WHEN configuracao.no_constante::TEXT = \'%2$s\'::TEXT THEN configuracao.sq_valor ELSE NULL::INTEGER END ) AS %2$s,
                                   MAX(CASE WHEN configuracao.no_constante::TEXT = \'%3$s\'::TEXT THEN configuracao.sq_valor ELSE NULL::INTEGER END ) AS %3$s,
                                   MAX(CASE WHEN configuracao.no_constante::TEXT = \'%4$s\'::TEXT THEN configuracao.sq_valor ELSE NULL::INTEGER END ) AS %4$s
                              FROM sicae.vw_configuracao AS configuracao
                             WHERE configuracao.no_constante::TEXT = ANY (
                                ARRAY[
                                    \'%2$s\'::TEXT,
                                    \'%3$s\'::TEXT,
                                    \'%4$s\'::TEXT
                                ]
                            )
                ),
                raiz AS (

                    SELECT art.sq_artefato
                      FROM sgdoce.artefato AS art
                     WHERE art.sq_artefato = (
                        SELECT sgdoce.obter_vinculo_pai(%1$d)
                    )
                ),
                arvore_artefato AS ((
                            SELECT sq_artefato AS sq_artefato_pai,
                                   NULL sq_artefato_filho,
                                   0::INTEGER AS sq_tipo_vinculo_artefato,
                                   sq_artefato::TEXT trilha,
                                   NULL::TIMESTAMP dt_vinculo,
                                   ARRAY[0]::INTEGER[] nivel,
                                   1::INTEGER nu_ordem
                              FROM raiz
                             UNION
                            SELECT art_vin.sq_artefato_pai,
                                   art_vin.sq_artefato_filho,
                                   art_vin.sq_tipo_vinculo_artefato,
                                   sq_artefato_pai || \'-->\' || sq_artefato_filho::TEXT trilha,
                                   art_vin.dt_vinculo,
                                   ARRAY[0, (ROW_NUMBER() OVER(PARTITION BY art_vin.sq_artefato_pai ORDER BY art_vin.dt_vinculo ASC))::INTEGER]::INTEGER[] nivel,
                                   COALESCE(art_vin.nu_ordem, (ROW_NUMBER() OVER(PARTITION BY art_vin.sq_artefato_pai ORDER BY art_vin.dt_vinculo ASC))::INTEGER) nu_ordem
                              FROM sgdoce.artefato_vinculo AS art_vin
                              JOIN configs ON TRUE
                        INNER JOIN raiz ON raiz.sq_artefato = sq_artefato_pai
                             WHERE art_vin.sq_tipo_vinculo_artefato NOT IN (configs.%2$s,configs.%3$s,configs.%4$s)
                    ) UNION ALL (
                            SELECT art_vin.sq_artefato_pai,
                                   art_vin.sq_artefato_filho,
                                   art_vin.sq_tipo_vinculo_artefato,
                                   art_arv.trilha || \'-->\' || art_vin.sq_artefato_filho,
                                   art_vin.dt_vinculo,
                                   ARRAY_APPEND(
                                       art_arv.nivel, (ROW_NUMBER() OVER(PARTITION BY art_vin.sq_artefato_pai ORDER BY art_vin.dt_vinculo ASC))::INTEGER),
                                   COALESCE(art_vin.nu_ordem, (ROW_NUMBER() OVER(PARTITION BY art_vin.sq_artefato_pai ORDER BY art_vin.dt_vinculo ASC))::INTEGER) nu_ordem
                              FROM sgdoce.artefato_vinculo AS art_vin
                              JOIN configs ON TRUE
                        INNER JOIN arvore_artefato AS art_arv ON art_arv.sq_artefato_filho = art_vin.sq_artefato_pai
                             WHERE art_vin.sq_tipo_vinculo_artefato NOT IN (configs.SGDOCE_TIPO_VINCULO_ARTEFATO_REFERENCIA,configs.SGDOCE_TIPO_VINCULO_ARTEFATO_APOIO,configs.SGDOCE_TIPO_VINCULO_ARTEFATO_DESPACHO)
                ))
                    SELECT arv_art.sq_artefato_pai,
                           arv_art.sq_artefato_filho,
                           arv_art.sq_tipo_vinculo_artefato,
                           tip_art.sq_tipo_artefato,
                           tip_art.no_tipo_artefato,
                           art.nu_digital,
                           CASE
                            WHEN length(cast(art.nu_digital as text)) > 7 THEN
                                cast(art.nu_digital as text)
                            WHEN length(cast(art.nu_digital as text)) <= 7 THEN
                                lpad(cast(art.nu_digital as varchar), 7, \'0\')
                            WHEN art.nu_digital IS NULL THEN
                                sgdoce.formata_numero_artefato(art.nu_artefato, ap.co_ambito_processo)
                           END AS nu_artefato
                           ,nivel
                           ,nu_ordem
                           ,(art.ar_inconsistencia)[1] as is_origem_valid
                           ,(art.ar_inconsistencia)[2] as is_destino_valid
                           ,(art.ar_inconsistencia)[3] as is_interessado_valid
                           ,(art.ar_inconsistencia)[4] as is_autor_valid
                           ,(art.ar_inconsistencia)[5] as is_assunto_valid
                           ,(art.ar_inconsistencia)[6] as is_datas_valid
                           ,(art.ar_inconsistencia)[7] as is_image_valid
                           ,CASE WHEN FALSE = ANY(ar_inconsistencia) THEN TRUE ELSE FALSE END AS is_inconsistent
                           ,smi.st_processado
                           ,smi.in_tentativa
                      FROM arvore_artefato              AS arv_art
                INNER JOIN sgdoce.artefato              AS art ON art.sq_artefato = COALESCE(arv_art.sq_artefato_filho, arv_art.sq_artefato_pai)
                LEFT JOIN sgdoce.solicitacao_migracao_imagem AS smi ON art.sq_artefato = smi.sq_artefato
                LEFT JOIN sgdoce.tipo_artefato_assunto AS tip_art_ass using(sq_tipo_artefato_assunto)
                LEFT JOIN sgdoce.tipo_artefato         AS tip_art using(sq_tipo_artefato)
                LEFT JOIN sgdoce.artefato_processo     AS ap ON art.sq_artefato = ap.sq_artefato
                  ORDER BY nu_ordem ASC, nivel ASC';

        $strQuery = sprintf(
                  $sql
                , $dto->getSqArtefato()
                , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_REFERENCIA
                , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_APOIO
                , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_DESPACHO
        );

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato_pai'         , 'sqArtefatoPai'        , 'integer');
        $rsm->addScalarResult('sq_artefato_filho'       , 'sqArtefatoFilho'      , 'integer');
        $rsm->addScalarResult('sq_tipo_vinculo_artefato', 'sqTipoVinculoArtefato', 'string');
        $rsm->addScalarResult('sq_tipo_artefato'        , 'sqTipoArtefato'       , 'integer');
        $rsm->addScalarResult('nu_digital'              , 'nuDigital'            , 'integer');
        $rsm->addScalarResult('no_tipo_artefato'        , 'noTipoArtefato'       , 'string');
        $rsm->addScalarResult('nu_artefato'             , 'nuArtefato'           , 'string');
        $rsm->addScalarResult('is_origem_valid'         , 'isOrigemValid'        , 'boolean');
        $rsm->addScalarResult('is_destino_valid'        , 'isDestinoValid'       , 'boolean');
        $rsm->addScalarResult('is_interessado_valid'    , 'isInteressadoValid'   , 'boolean');
        $rsm->addScalarResult('is_autor_valid'          , 'isAutorValid'         , 'boolean');
        $rsm->addScalarResult('is_assunto_valid'        , 'isAssuntoValid'       , 'boolean');
        $rsm->addScalarResult('is_datas_valid'          , 'isDatasValid'         , 'boolean');
        $rsm->addScalarResult('is_image_valid'          , 'isImageValid'         , 'boolean');
        $rsm->addScalarResult('nivel'                   , 'nivel'                , 'string');
        $rsm->addScalarResult('in_tentativa'            , 'inTentativa'          , 'integer');
        $rsm->addScalarResult('st_processado'           , 'stProcessado'         , 'boolean');
        $rsm->addScalarResult('is_inconsistent'         , 'isInconsistent'       , 'boolean');

        return
        $this->_em
             ->createNativeQuery($strQuery, $rsm)->useResultCache(false);
    }

    public function hasVinculoSigiloso ($sqArtefato)
    {
        $sql = 'WITH RECURSIVE
                configs AS (
                        SELECT %2$s,
                               %3$s,
                               %4$s,
                               %5$s
                          FROM sicae.lista_constantes (
                                    \'%2$s\',
                                    \'%3$s\',
                                    \'%4$s\',
                                    \'%5$s\'
                                ) AS c (
                                        %2$s INTEGER,
                                        %3$s INTEGER,
                                        %4$s INTEGER,
                                        %5$s INTEGER)
                ),
                raiz AS (
                    SELECT art.sq_artefato
                      FROM sgdoce.artefato AS art
                     WHERE art.sq_artefato = (
                        SELECT sgdoce.obter_vinculo_pai(%1$d)
                    )
                ),
                arvore_artefato AS ((
                            SELECT sq_artefato AS sq_artefato_pai,
                                   NULL sq_artefato_filho
                              FROM raiz
                             UNION
                            SELECT art_vin.sq_artefato_pai,
                                   art_vin.sq_artefato_filho
                              FROM sgdoce.artefato_vinculo AS art_vin
                              JOIN configs ON TRUE
                        INNER JOIN raiz ON raiz.sq_artefato = sq_artefato_pai
                             WHERE art_vin.sq_tipo_vinculo_artefato NOT IN (configs.%2$s,configs.%3$s,configs.%4$s)
                    ) UNION ALL (
                            SELECT art_vin.sq_artefato_pai,
                                   art_vin.sq_artefato_filho
                              FROM sgdoce.artefato_vinculo AS art_vin
                              JOIN configs ON TRUE
                        INNER JOIN arvore_artefato AS art_arv ON art_arv.sq_artefato_filho = art_vin.sq_artefato_pai
                             WHERE art_vin.sq_tipo_vinculo_artefato NOT IN (configs.%2$s,configs.%3$s,configs.%4$s)
                ))
                    SELECT (COUNT(sq_artefato_pai) > 0) as has_sigilo
                      FROM arvore_artefato              AS arv_art
                INNER JOIN sgdoce.artefato              AS art ON art.sq_artefato = COALESCE(arv_art.sq_artefato_filho, arv_art.sq_artefato_pai)
                INNER JOIN sgdoce.grau_acesso_artefato  AS gaa using(sq_artefato)
                INNER JOIN configs ON TRUE
                     WHERE gaa.sq_grau_acesso = configs.%5$s';

        $strQuery = sprintf(
                  $sql
                , $sqArtefato
                , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_REFERENCIA
                , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_APOIO
                , self::T_SGDOCE_TIPO_VINCULO_ARTEFATO_DESPACHO
                , self::T_SGDOCE_GRAU_ACESSO_SIGILOSO
        );

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('has_sigilo'         , 'hasSigilo'        , 'boolean');

        return
        $this->_em
             ->createNativeQuery($strQuery, $rsm)
             ->useResultCache(FALSE)
             ->getSingleScalarResult();
    }

    public function isChild(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $query = $queryBuilder
                ->select('av')
                ->from('app:ArtefatoVinculo', 'av')
                ->andWhere('av.sqArtefatoFilho = :id')
                ->andWhere( $queryBuilder->expr()->notIn(
                        'av.sqTipoVinculoArtefato',
                        array(\Core_Configuration::getSgdoceTipoVinculoArtefatoDespacho(),
                              \Core_Configuration::getSgdoceTipoVinculoArtefatoApoio(),
                              \Core_Configuration::getSgdoceTipoVinculoArtefatoReferencia()
                )))
                ->setParameter('id', $dto->getSqArtefato());

        $result = $query->getQuery()->getOneOrNullResult();
        return $result ? true : false;
    }

    public function isCitacao(\Core_Dto_Search $dto, $sqTipoVinculo=null)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        if (!is_null($sqTipoVinculo)) {
            $sqTipoVinculo = (array) $sqTipoVinculo;
        }else{
            $sqTipoVinculo = array(
                \Core_Configuration::getSgdoceTipoVinculoArtefatoDespacho(),
                \Core_Configuration::getSgdoceTipoVinculoArtefatoApoio(),
                \Core_Configuration::getSgdoceTipoVinculoArtefatoReferencia()
            );
        }

        $query = $queryBuilder
                ->select('av')
                ->from('app:ArtefatoVinculo', 'av')
                ->andWhere('av.sqArtefatoFilho = :id')
                ->andWhere( $queryBuilder->expr()->In('av.sqTipoVinculoArtefato',$sqTipoVinculo))
                ->setParameter('id', $dto->getSqArtefato());

        $result = $query->getQuery()->getOneOrNullResult();
        return $result ? true : false;
    }

    /**
     *
     * @param \Core_Dto_Search $dto
     * @return array
     */
    public function searchDocumentsToFirstPiece(\Core_Dto_Search $dto, $isExterno = false, $isLegado = false)
    {
        $sqTipoArtefato = $dto->getSqTipoArtefato();

        $field = "at.nu_digital";

        if( $sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ) {
            $field = $this->_em->createQueryBuilder()->expr()->lower("translate(at.nu_artefato::text, './-'::text, ''::text)")->__toString();
        }

        $inAbreProcesso = '';
        if( !$isExterno ){
            $inAbreProcesso = ' AND td.in_abre_processo';
        }

        if( $isLegado ) {
            $inAbreProcesso = '';
        }

        $sql = "SELECT at.sq_artefato,
                       at.nu_digital,
                       at.nu_artefato
                  FROM sgdoce.fn_show_area_trabalho(NULL, :sqTipoArtefato, :sqPessoaLogada, :sqUnidadeLogada, :search) at
                  JOIN tipo_documento td
                    ON td.sq_tipo_documento = at.sq_tipo_documento
                 WHERE at.sq_status_tramite > :sqStatusTramite

                   AND at.has_imagem
                   AND NOT at.arquivado
                   AND {$field} LIKE '{$dto->getQuery()}%'
                   AND NOT at.has_solicitacao_aberta
                   {$inAbreProcesso}
                 ORDER BY at.nu_digital";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato',    'sqArtefato',   'integer');
        $rsm->addScalarResult('nu_digital',     'nuDigital',    'string');
        $rsm->addScalarResult('nu_artefato',    'nuArtefato',    'string');

        $nq = $this->_em->createNativeQuery($sql, $rsm);

        $nq->setParameter('sqStatusTramite', \Core_Configuration::getSgdoceStatusTramiteTramitado())
           ->setParameter('sqTipoArtefato' , $dto->getSqTipoArtefato())
           ->setParameter('sqUnidadeLogada', \Core_Integration_Sica_User::getUserUnit())
           ->setParameter('sqPessoaLogada' , \Core_Integration_Sica_User::getPersonId())
           ->setParameter('search'         , $dto->getQuery());

        $nq->useResultCache(false);

        return $nq->getArrayResult();
    }

    public function getSqArtefatoPrincipal(\Core_Dto_Search $dto)
    {
        $sql = "SELECT art.sq_artefato
                  FROM sgdoce.artefato AS art
                 WHERE art.sq_artefato = (SELECT sgdoce.obter_vinculo_pai(:sqArtefato))";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato',    'sqArtefato',   'integer');

        $nq = $this->_em
                   ->createNativeQuery($sql, $rsm)
                   ->setParameter('sqArtefato', $dto->getSqArtefato());

        return $nq->useResultCache(false)->getSingleScalarResult();
    }

    /**
     *
     * @param array $criteria
     * @param array $not
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     * @return
     */
    public function findByNot( array $criteria, array $not, array $orderBy = null, $limit = null, $offset = null )
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $expr = $this->getEntityManager()->getExpressionBuilder();

        $qb->select( 'av' )
            ->from( $this->getEntityName(), 'av' );

        foreach ( $criteria as $field => $value ) {
            $qb->andWhere( $expr->eq( 'av.' . $field, $value ) );
}

        foreach ( $not as $field => $value ) {
            $qb->andWhere( $expr->neq( 'av.' . $field, $value ) );
        }

        if ( $orderBy ) {
            foreach ( $orderBy as $field => $order ) {
                $qb->addOrderBy( 'av.' . $field, $order );
            }
        }

        if ( $limit ) {
            $qb->setMaxResults( $limit );
        }

        if ( $offset ){
            $qb->setFirstResult( $offset );
        }

        return $qb->getQuery()
                  ->getResult();
    }

    public function getMaxNuOrderByParent(\Core_Dto_Search $dto)
    {
        $sql = "SELECT MAX(nu_ordem) as nu_ordem FROM artefato_vinculo WHERE sq_artefato_pai = :sqArtefatoPai";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('nu_ordem', 'nuOrdem', 'integer');

        $nq = $this->_em
                   ->createNativeQuery($sql, $rsm)
                   ->setParameter('sqArtefatoPai', $dto->getSqArtefatoPai());

        return $nq->useResultCache(false)->getSingleScalarResult();
    }

    public function hasNoOrderVinculo($sqArtefatoPai)
    {
        $sql = "SELECT count(sq_artefato_filho) as total FROM artefato_vinculo WHERE sq_artefato_pai = :sqArtefatoPai AND nu_ordem IS NULL";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('total', 'total', 'integer');

        $nq = $this->_em
                   ->createNativeQuery($sql, $rsm)
                   ->setParameter('sqArtefatoPai', $sqArtefatoPai);
        $result = $nq->useResultCache(false)->getSingleScalarResult();

        return ($result > 0);
    }

    public function setOrderIn( $sqArtefatoPai )
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);

        $sql = 'DO $$
                DECLARE
                    registros CURSOR FOR SELECT sq_artefato_vinculo, sq_artefato_pai, sq_artefato_filho, dt_vinculo, sq_tipo_vinculo_artefato, ROW_NUMBER() OVER(PARTITION BY sq_artefato_pai ORDER BY nu_ordem ASC, dt_vinculo ASC) as nu_ordem FROM artefato_vinculo WHERE sq_artefato_pai = %1$s ORDER BY nu_ordem ASC, dt_vinculo ASC;
                BEGIN
                    FOR registro IN registros LOOP
                        EXECUTE \'UPDATE sgdoce.artefato_vinculo SET nu_ordem = \' || registro.nu_ordem || \' WHERE sq_artefato_vinculo = \' || registro.sq_artefato_vinculo || \';\';
                    END LOOP;
                END$$; ';

        $query = sprintf($sql, $sqArtefatoPai);

        $nq = $this->_em->createNativeQuery($query, $rsm);

        return $nq->useResultCache(false)->execute();
    }

}