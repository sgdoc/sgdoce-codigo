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

use Doctrine\DBAL\Types\IntegerType,
    Doctrine\ORM\Mapping\Entity;

/**
 * SISICMBio
 *
 * Classe para Repository de PessoaInteressadoArtefato
 *
 * @package      Model
 * @subpackage   Repository
 * @name         PessoaInteressadoArtefato
 * @version      1.0.0
 * @since        2012-11-20
 */
class PessoaAssinanteArtefato extends \Core_Model_Repository_Base
{

    /**
     * Realiza busca para grid
     * @param array $params
     * @return array
     */
    public function listAssinatura ($search)
    {
        $query = $this->_em
                ->createQueryBuilder()
                ->select('a.sqArtefato,puo.sqPessoaUnidadeOrg,p.noPessoa')
                ->from('app:Artefato', 'a')
                ->innerJoin('a.sqPessoaAssinanteArtefato', 'pa')
                ->innerJoin('pa.sqPessoaUnidadeOrg', 'puo')
                ->innerJoin('puo.sqPessoaSgdoce', 'p')
                ->andWhere('a.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $search->getSqArtefato());
        return $query;
    }

    /**
     * Deleta assinatura
     * @param \Core_Dto_Search $dto
     * @return array
     */
    public function deleteAssinatura ($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
                ->delete($this->_entityName, 'm')
                ->andWhere('m.sqPessoaUnidadeOrg = :sqPessoaUnidadeOrg')
                ->setParameter('sqPessoaUnidadeOrg', $dto->getSqPessoaUnidadeOrg()->getSqPessoaUnidadeOrg())
                ->andWhere('m.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $dto->getSqArtefato()->getSqArtefato());

        $out = $queryBuilder->getQuery()->execute();

        return $out;
    }

    /**
     * Deleta assinatura
     * @param \Core_Dto_Search $dto
     * @return array
     */
    public function deleteTodasAssinatura ($sqArtefato)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
                ->delete($this->_entityName, 'm')
                ->andWhere('m.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $sqArtefato);

        $out = $queryBuilder->getQuery()->execute();

        return $out;
    }

    /**
     * Realiza busca para grid
     * @param array $params
     * @return array
     */
    public function getDadosAssinaturaUnica ($search)
    {
        $queryBuilder = $this->_em
                ->createQueryBuilder()
                ->select('a.sqArtefato,ta.sqTipoAssinante,p.sqPessoaSgdoce,p.noPessoa,pc.sqPessoa,puo.noCargo')
                ->from('app:Artefato', 'a')
                ->innerJoin('a.sqPessoaAssinanteArtefato', 'pa')
                ->innerJoin('pa.sqPessoaUnidadeOrg', 'puo')
                ->innerJoin('puo.sqPessoaSgdoce', 'p')
                ->innerJoin('pa.sqTipoAssinante', 'ta')
                ->innerJoin('p.sqPessoaCorporativo', 'pc')
                ->andWhere('a.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $search->getSqArtefato())
                ->andWhere('ta.sqTipoAssinante is not null');

        $out = $queryBuilder->getQuery()->execute();

        return $out;
    }

    /**
     * Realiza busca para grid
     * @param array $params
     * @return array
     */
    public function findAssinaturaArtefato ($dto)
    {
        $queryBuilder = $this->_em
                ->createQueryBuilder()
                ->select('pa,puo, p')
                ->from('app:PessoaAssinanteArtefato', 'pa')
                ->innerJoin('pa.sqPessoaUnidadeOrg', 'puo')
                ->innerJoin('puo.sqPessoaSgdoce', 'p')
                ->andWhere('pa.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $dto->getSqArtefato())
                ->andWhere('p.sqPessoaCorporativo = :sqPessoaCorporativo')
                ->setParameter('sqPessoaCorporativo', $dto->getSqPessoaCorporativo());

        $out = $queryBuilder->getQuery()->execute();

        return $out;
    }

    /**
     * método que obtén informarções da pessoa 'assinatura' relacionada ao artefato
     * @param integer
     * @return array
     */
    public function getPessoaArtefatoAssinatura ($dto)
    {
        $query = $this->_em->createQueryBuilder()
                ->select('p.noPessoa, p.noProfissao, p.noUnidadeOrg')
                ->from($this->_enName, 'p')
                ->innerJoin('p.sqPessoaFuncao', 'pf')
                ->andWhere('p.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $dto->getSqArtefato())
                ->andWhere('p.sqPessoaFuncao = :sqPessoaFuncao')
                ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoAssinatura())
                ->getQuery()
                ->execute();

        if (empty($query)) {
            return NULL;
        }
        return $query;
    }

    public function getPessoaUnidadeOrgByArtefato ($dto)
    {
        $query = $this->_em->createQueryBuilder()
                ->select('puo.sqPessoaUnidadeOrg')
                ->from($this->_entityName, 'p')
                ->innerJoin('p.sqPessoaUnidadeOrg', 'puo')
                ->andWhere('p.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $dto->getSqArtefato());

        $result = $query->getQuery()->getOneOrNullResult();
        return ($result) ? $result['sqPessoaUnidadeOrg']: $result;
    }

    public function deleteByArtefato ($sqArtefato)
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb->delete($this->_entityName, 't')
                        ->where($qb->expr()->eq('t.sqArtefato', $sqArtefato))
                        ->getQuery()
                        ->execute();
    }

}
