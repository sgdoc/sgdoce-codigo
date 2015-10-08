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
 * Classe para Repository de Arquivo Setorial
 *
 * @package      Model
 * @subpackage   Repository
 * @name         ArtefatoArquivoSetorial
 * @version      1.0.0
 * @since        2015-09-02
 */
class ArtefatoArquivoSetorial extends \Core_Model_Repository_Base
{

    /**
     *
     * @param \Core_Dto_Search $dto
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function listGrid (\Core_Dto_Search $dto)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();

        $rsm->addScalarResult('total_record', 'totalRecord', 'integer');
        $rsm->addScalarResult('sq_artefato', 'sqArtefato', 'integer');
        $rsm->addScalarResult('digital_numero', 'digitalNumero', 'string');
        $rsm->addScalarResult('dt_arquivamento', 'dtArquivamento', 'zenddate');
        $rsm->addScalarResult('no_pessoa_arquivamento', 'noPessoaArquivamento', 'string');
        $rsm->addScalarResult('tx_assunto', 'txAssunto', 'string');
        $rsm->addScalarResult('no_tipo_documento', 'noTipoDocumento', 'string');
        $rsm->addScalarResult('no_pessoa_origem', 'noPessoaOrigem', 'string');

        $caseNuDigital = "sgdoce.formata_numero_digital(a.nu_digital)";
        $sql = "
               SELECT DISTINCT
                      COUNT(a.sq_artefato) OVER() AS total_record
                      ,a.sq_artefato
                      ,COALESCE({$caseNuDigital}, formata_numero_artefato(a.nu_artefato,ap.co_ambito_processo)) AS digital_numero
                      ,dt_arquivamento
                      ,p.no_pessoa as no_pessoa_arquivamento
                      ,ass.tx_assunto
                      ,td.no_tipo_documento
                      ,ps.no_pessoa AS no_pessoa_origem
                 FROM artefato_arquivo_setorial aas
                 JOIN vw_pessoa p ON aas.sq_pessoa_arquivamento = p.sq_pessoa
                 JOIN artefato a USING(sq_artefato)
                 JOIN sgdoce.tipo_artefato_assunto taa USING(sq_tipo_artefato_assunto)
                 JOIN sgdoce.assunto ass ON ass.sq_assunto = taa.sq_assunto
                 JOIN sgdoce.pessoa_artefato pa ON pa.sq_artefato = a.sq_artefato and pa.sq_pessoa_funcao = %1\$d
                 JOIN sgdoce.pessoa_sgdoce ps ON ps.sq_pessoa_sgdoce = pa.sq_pessoa_sgdoce
            LEFT JOIN artefato_processo ap ON a.sq_artefato = ap.sq_artefato
            LEFT JOIN tipo_documento td USING(sq_tipo_documento)
                WHERE dt_desarquivamento IS NULL
                  AND sq_unidade_arquivamento = %2\$d
                  AND sq_tipo_artefato = %3\$d
                  %4\$s
             ORDER BY dt_arquivamento DESC";

        $optionalCondition = '';
        $search = mb_strtolower($dto->getSearch(), 'UTF-8');

        if ($search) {
            $queryBuild = $this->_em->createQueryBuilder();
            if( $dto->getSqTipoArtefato() == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ){
                $nuArtefato    = $this->_em->createQueryBuilder()->expr()->lower("TRANSLATE(a.nu_artefato, './-', '')")->__toString();
                $optionalCondition = " AND (" . $queryBuild->expr()->eq('a.nu_artefato', $queryBuild->expr()->literal($search))->__toString() . " OR " .
                                           $queryBuild->expr()->eq($nuArtefato, $queryBuild->expr()->literal(str_replace(array('.','/','-'), '', $search)))->__toString() . ")";
            } else {
                $optionalCondition = " AND " . $queryBuild->expr()->eq('a.nu_digital', $search)->__toString();
            }
        }

        $strSql = sprintf($sql,
                \Core_Configuration::getSgdocePessoaFuncaoOrigem(),
                \Core_Integration_Sica_User::getUserUnit(),
                $dto->getSqTipoArtefato(),
                $optionalCondition);

        return $this->_em->createNativeQuery($strSql, $rsm)->useResultCache(false);
    }

    public function hasArquivamento ($sqArtefato)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();

        $rsm->addScalarResult('has_arquivamento', 'hasArquivamento', 'boolean');

        $sql = "
               SELECT (COUNT(sq_artefato_arquivo_setorial) > 0) as has_arquivamento
                 FROM artefato_arquivo_setorial
                WHERE sq_artefato = :sqArtefato
                  AND dt_desarquivamento IS NULL";

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqArtefato', $sqArtefato)
                ->useResultCache(false);

        return $query->getSingleScalarResult();
    }

    public function getKeyArquivamento ($sqArtefato)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();

        $rsm->addScalarResult('sq_artefato_arquivo_setorial', 'sqArtefatoArquivoSetorial', 'integer');

        $sql = "
               SELECT sq_artefato_arquivo_setorial
                 FROM artefato_arquivo_setorial
                WHERE sq_artefato = :sqArtefato
                  AND dt_desarquivamento IS NULL
                LIMIT 1";

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqArtefato', $sqArtefato)
                ->useResultCache(false);

        return $query->getSingleScalarResult();
    }

}
