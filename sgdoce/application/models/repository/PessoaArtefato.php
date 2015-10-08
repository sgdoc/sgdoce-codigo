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
 * Classe para Repository de PessoaFuncao
 *
 * @package      Model
 * @subpackage   Repository
 * @name         PessoaFuncao
 * @version      1.0.0
 * @since        2012-11-20
 */
class PessoaArtefato extends \Core_Model_Repository_Base
{

    public function searchPessoaOrigem($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
            ->select('pf.sqPessoaFuncao', 'ps.noPessoa')
            ->from('app:PessoaArtefato', 'pa')
            ->innerJoin('pa.sqPessoaFuncao', 'pf')
            ->innerJoin('pa.sqPessoaSgdoce', 'ps')
            ->where('pf.sqPessoaFuncao in(' . \Core_Configuration::getSgdocePessoaFuncaoOrigem() . ')');

            if (method_exists($dto, 'query')) {
                $query = mb_strtolower($dto->getQuery(), 'UTF-8');
                $queryBuilder->andWhere('LOWER(ps.noPessoa) like :noPessoa')
                        ->setParameter('noPessoa', '%' . $query . '%');
            } else {
                $queryBuilder->andWhere('pa.sqArtefato = :sqPessoaArtefato')
                ->setParameter('sqPessoaArtefato', $dto->getSqPessoaArtefato()->getSqArtefato()->getSqArtefato());
            }

            $queryBuilder->orderBy('ps.noPessoa');

        $res = $queryBuilder->getQuery()->getArrayResult();
        $out = array();
        foreach ($res as $item) {
            $out[$item['sqPessoaFuncao']] = $item['noPessoa'];
        }

        return $out;
    }

    public function getPessoaArtefato($dto, $entityPessoaFuncao)
    {
        $query = $this->_em->createQueryBuilder()
                            ->select('a,pa')
                            ->from('app:Artefato', 'a')
                                ->leftJoin('a.sqPessoaArtefato', 'pa')
                                ->leftJoin('pa.sqPessoaSgdoce', 'p')
                                ->leftJoin('pa.sqPessoaFuncao', 'pf')
                            ->andWhere('a.sqArtefato = :sqArtefato')
                                ->setParameter('sqArtefato', $dto->getSqArtefato())
                            ->andWhere('pf.sqPessoaFuncao = :sqPessoaFuncao')
                                ->setParameter('sqPessoaFuncao', $entityPessoaFuncao);

        $resultQuery = $query->getQuery()->getArrayResult();

        if (count($resultQuery)) {
            return $resultQuery;
        }

        return array(new \Sgdoce\Model\Entity\PessoaArtefato());
    }

    public function searchPessoaArtefato($dto, $sqPessoa, $sqPessoaFuncao)
    {
        $query = $this->_em->createQueryBuilder()
                            ->select('a,pa')
                            ->from('app:Artefato', 'a')
                                ->leftJoin('a.sqPessoaArtefato', 'pa')
                                ->leftJoin('pa.sqPessoaSgdoce', 'p')
                                ->leftJoin('pa.sqPessoaFuncao', 'pf')
//                                ->where('1 != 1')
                            ->andWhere('a.sqArtefato = :sqArtefato')
                                ->setParameter('sqArtefato', $dto->getSqArtefato())
                            ->andWhere('pf.sqPessoaFuncao = :sqPessoaFuncao')
                                ->setParameter('sqPessoaFuncao', $sqPessoaFuncao);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Deleta Destinatario
     * @param \Core_Dto_Search $dto
     * @return array
     */
    public function deleteDestinatario($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
        ->delete($this->_entityName, 'pa')

        ->andWhere('pa.sqArtefato = :sqArtefato')
        ->setParameter('sqArtefato', $dto->getSqArtefato()->getSqArtefato())

        ->andWhere('pa.sqPessoaSgdoce = :sqPessoaSgdoce')
        ->setParameter('sqPessoaSgdoce', $dto->getSqPessoaSgdoce()->getSqPessoaSgdoce())

        ->andWhere('pa.sqPessoaFuncao = :sqPessoaFuncao')
        ->setParameter('sqPessoaFuncao', $dto->getSqPessoaFuncao()->getSqPessoaFuncao());

        $out = $queryBuilder->getQuery()->execute();

        return $out;
    }

    /**
     * Deleta Destinatario
     * @param \Core_Dto_Search $dto
     * @return array
     */
    public function deleteInteressadoSemSqCorporativo($dto)
    {
        $sqPessoaFuncaoInteressado = \Core_Configuration::getSgdocePessoaFuncaoInteressado();

        $sql ="SELECT ps.sq_pessoa_sgdoce
                 FROM pessoa_artefato pa
                 JOIN pessoa_sgdoce ps using(sq_pessoa_sgdoce)
                WHERE pa.sq_artefato = :sqArtefato
                  AND pa.sq_pessoa_funcao = :sqPessoaFuncaoInteressado
                  AND ps.sq_pessoa_corporativo IS NULL";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('sq_pessoa_sgdoce', 'sqPessoaSgdoce', 'integer');

        $nq = $this->_em->createNativeQuery($sql, $rsm);
        $nq->setParameter('sqArtefato', $dto->getSqArtefato());
        $nq->setParameter('sqPessoaFuncaoInteressado', $sqPessoaFuncaoInteressado);

        $result = $nq->getArrayResult();

        if(! $result){
            return 0;
        }

        $aux = array();
        foreach ($result as $value) {
            $aux[] = $value['sqPessoaSgdoce'];
        }

        $queryBuilder = $this->_em->createQueryBuilder();

        $delete = $queryBuilder
            ->delete($this->_entityName, 'pa')

            ->setParameter('sqArtefato', $dto->getSqArtefato())
            ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoInteressado())

            ->andWhere('pa.sqArtefato = :sqArtefato')
            ->andWhere('pa.sqPessoaFuncao = :sqPessoaFuncao')
            ->andWhere($queryBuilder->expr()->in('pa.sqPessoaSgdoce', $aux));

        return $delete->getQuery()->execute();
    }

    public function findPessoaArtefato($dto)
    {

        $queryBuilder = $this->_em->createQueryBuilder()
        ->select('a,pa')
        ->from('app:Artefato', 'a')
        ->leftJoin('a.sqPessoaArtefato', 'pa')
        ->leftJoin('pa.sqPessoaSgdoce', 'p')
        ->leftJoin('pa.sqPessoaFuncao', 'pf')
        ->andWhere('a.sqArtefato = :sqArtefato')
        ->setParameter('sqArtefato', $dto->getSqArtefato())
        ->andWhere('pf.sqPessoaFuncao = :sqPessoaFuncao')
        ->setParameter('sqPessoaFuncao', $dto->getSqPessoaFuncao())
        ->andWhere('p.sqPessoaCorporativo = :sqPessoaCorporativo')
        ->setParameter('sqPessoaCorporativo', $dto->getSqPessoaCorporativo());


        $out = $queryBuilder->getQuery()->execute();

        return $out;
    }

    /**
     * método que obtén informarções da pessoa 'rodape' relacionada ao artefato
     * @param integer
     * @return array
     */
    public function getPessoaArtefatoRodape($dto)
    {

        $select = 'pf.sqPessoaFuncao, pf.noPessoaFuncao, p.sqPessoaSgdoce
                  ,e.txEndereco, e.nuEndereco,
                   em.txEmail, p.noPessoa, p.noProfissao, te.nuTelefone,te.nuDdd, t.noTratamento,
                   tv.deEnderecamento, v.noVocativo,
                    cid.noMunicipio, est.noEstado,e.coCep,
                   a.deImagemRodape, pc.sqPessoa sqPessoaCorporativo';

        $query = $this->_em->createQueryBuilder()
        ->select($select)
        ->from('app:Artefato', 'a')
        ->innerJoin('a.sqPessoaArtefato', 'pa')
        ->innerJoin('pa.sqPessoaSgdoce', 'p')
        ->innerJoin('pa.sqPessoaFuncao', 'pf')
        ->leftJoin('pa.sqTratamentoVocativo', 'tv')
        ->leftJoin('tv.sqTratamento', 't')
        ->leftJoin('tv.sqVocativo', 'v')
        ->leftJoin('p.sqPessoaCorporativo', 'pc')
        ->leftJoin('pa.sqEnderecoSgdoce', 'e')
        ->leftJoin('e.sqMunicipio', 'cid')
        ->leftJoin('cid.sqEstado', 'est')
        ->leftJoin('pa.sqEmailSgdoce', 'em')
        ->leftJoin('pa.sqTelefoneSgdoce', 'te')
        ->andWhere('a.sqArtefato = :sqArtefato')
        ->setParameter('sqArtefato', $dto->getSqArtefato())
        ->andWhere('pf.sqPessoaFuncao = :sqPessoaFuncao')
        ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoDadosRodape())
        ->getQuery()->execute();

        if (empty($query)) {
            return NULL;
        }
        return $query[0];
    }


    public function getPessoaArtefatoByPessoaSgdocePessoaEncaminhado (\Core_Dto_Search $dto)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('t')
            ->from($this->_entityName, 't')
                ->where($qb->expr()->orX(
                        $qb->expr()->eq('t.sqPessoaSgdoce', $dto->getSqPessoaSgdoce()),
                        $qb->expr()->eq('t.sqPessoaEncaminhado', $dto->getSqPessoaEncaminhado())
                        )
                    );
        return $qb->getQuery()->execute();
    }
}
