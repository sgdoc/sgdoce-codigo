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

use Doctrine\ORM\Query\AST\WhereClause;

use Doctrine\ORM\EntityRepository;

/**
 * Profissional
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VwProfissional extends \Core_Model_Repository_Base
{
    public function searchCargoCorporativo(\Core_Dto_Search $dto)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('c.noCargo'));

        $query = $queryBuilder->select('c')
            ->from('app:VwCargo', 'c')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            );

        $res = $queryBuilder->getQuery()->getArrayResult();
        $out = array();

        foreach ($res as $item) {
            $out[$item['sqCargo']] = $item['noCargo'];
        }

        return $out;
    }

    public function searchNomeCargo(\Core_Dto_Search $dto)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field1 = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('pa.noCargoEncaminhado'));

        $field2 = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('c.noCargo'));

        $sqlSgdoce = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('pa.noCargoEncaminhado')
            ->from('app:PessoaArtefato', 'pa')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field1 .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            );

        $sqlCorporativo = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c.noCargo')
            ->from('app:VwCargo', 'c')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field2 .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            );

        $sqlUnion = "{$sqlSgdoce->getQuery()->getSQL()} UNION {$sqlCorporativo->getQuery()->getSQL()}";

        $resultUnion = $this->_em->getConnection()->fetchAll($sqlUnion);

        $out = array();
        if(count($resultUnion)){
            foreach ($resultUnion as $item) {
                $out[$item['no_cargo_encaminhado0']] = $item['no_cargo_encaminhado0'];
            }
        }
        return $out;
    }

    /**
     * Obtém os dados da pessoa
     * @return array
     */
    public function searchPessoaInterna(\Core_Dto_Abstract $dto, $limit = NULL)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $where = NULL;
        if ($dto->getProcedencia() == 'interna' && !is_null($dto->getSqPessoaOrigem()) ) {
            $where  = $queryBuilder->andWhere('vf.sqUnidadeExercicio = :unidadeExercicio');
            if ($dto->getTipoPessoa() == \Core_Configuration::getCorpTipoPessoaFisica()) {
                $pessoaOrigem = $this->getEntityManager()
                    ->getRepository('app:VwProfissional')
                    ->find($dto->getSqPessoaOrigem());
                $queryBuilder->setParameter('unidadeExercicio',$pessoaOrigem->getSqUnidadeExercicio()->getSqUnidadeOrg());
            } else if ($dto->getTipoPessoa() == \Core_Configuration::getCorpTipoPessoaUnidadeOrg()) {
                $queryBuilder->setParameter('unidadeExercicio',$dto->getSqPessoaOrigem());
            }
        }

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('p.noPessoa'));

        $query = $queryBuilder->select('p.sqPessoa,p.noPessoa')->distinct()
            ->from('app:VwProfissional', 'vf')
            ->innerJoin('vf.sqPessoa', 'p')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            );

        if ($where) {
            $where;
        }

        if ($limit) {
            $query->setMaxResults($limit);
        }

        $query->orderBy('p.noPessoa');

        $res = $query->getQuery()->execute();

        $out = array();
        foreach ($res as $item) {
            $out[$item['sqPessoa']] = $item['noPessoa'];
        }

        return $out;
    }

    /**
     * Obtém os dados da pessoa
     * @return array
     */
    public function searchPessoa(\Core_Dto_Abstract $dto)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('p.noPessoa'));

        $query = $queryBuilder->select('p.sqPessoa,p.noPessoa')
            ->from('app:VwProfissional', 'vf')
            ->innerJoin('vf.sqPessoa', 'p')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            );

        if ($dto->hasExtraParam()) {
            $query->andWhere('vf.sqUnidadeExercicio = :sqUnidadeExercicio')
                ->setParameter('sqUnidadeExercicio', $dto->getExtraParam());
        }

        $query->orderBy('p.noPessoa');

        $res = $query->getQuery()->getArrayResult();
        $out = array();

        foreach ($res as $item) {
            $out[$item['sqPessoa']] = $item['noPessoa'];
        }

        return $out;
    }

    /**
     * Obtém os dados da pessoa
     * @return array
     */
    public function searchDadosProfissinal(\Core_Dto_Abstract $dto)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('vf,c')
            ->from('app:VwProfissional', 'vf')
            ->innerJoin('vf.sqCargo', 'c');
        $queryBuilder->andWhere('vf.sqProfissional = :sqProfissional')
            ->setParameter('sqProfissional', $dto->getSqPessoaSgdoce());

        return $queryBuilder->getQuery()->getSingleResult();
    }

    /**
     * Obtém os dados da pessoa
     * @return array
     */
    public function searchPessoaProfissinal(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('vf,c.noCargo')
            ->from('app:VwProfissional', 'vf')
            ->innerJoin('vf.sqCargo', 'c');
        $queryBuilder->andWhere('vf.sqProfissional = :sqProfissional')
            ->setParameter('sqProfissional', $dto->getSqPessoa());

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * Obtém os dados da pessoa
     * @return array
     */
    public function searchPessoaPorSetorOuUnidade($dto)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('pc.noPessoa'));

        $query = $queryBuilder->select('pc.sqPessoa,pc.noPessoa')
            ->from('app:VwProfissional', 'pf')
            ->innerJoin('pf.sqPessoa', 'pc')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            )
            ->andWhere('pf.sqUnidadeExercicio = :sqUnidadeExercicio')
            ->setParameter('sqUnidadeExercicio', $dto->getSqUnidadeExercicio());

        $res = $queryBuilder->getQuery()->execute();
        $out = array();

        foreach ($res as $item) {
            $out[$item['sqPessoa']] = $item['noPessoa'];
        }

        return $out;
    }

    /**
     * Obtém os dados da pessoa
     * @return array
     */
    public function recuperaDadosProfissinal($dto)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('ps,pc,pf')
            ->from('app:PessoaSgdoce', 'ps')
                ->innerJoin('ps.sqPessoaCorporativo', 'pc')
                ->innerJoin('pc.sqProfissional', 'pf')
            ->andWhere('pc.sqPessoa = :sqPessoa')
                ->setParameter('sqPessoa', $dto->getExtraParam());
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Obtém os dados da pessoa
     * @return array
     */
    public function validaDadosInterno(\Core_Dto_Abstract $dto)
    {
        $filter = new \Zend_Filter_Digits();
        $nuCpfCnpjPassaporte = $filter->filter($dto->getNuCPFInteressado());

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('vf, p')
            ->from('app:VwProfissional', 'vf')
            ->innerJoin('vf.sqPessoa', 'p');

        if ($dto->hasQuery()) {
            $query = mb_strtolower($dto->getQuery(), 'UTF-8');
            $queryBuilder->andWhere('LOWER(p.noPessoa) like :noPessoa')
                ->setParameter('noPessoa', '%' . $query . '%');
        }

        if ($nuCpfCnpjPassaporte) {
            $queryBuilder->leftJoin('p.sqPessoaFisica', 'pf');
            if ($nuCpfCnpjPassaporte != '') {
                $queryBuilder->andWhere('pf.nuCpf = :nuCpf')
                    ->setParameter('nuCpf', $nuCpfCnpjPassaporte);
            }
        }

        $queryBuilder->orderBy('p.noPessoa');

        $out = array();
        $result = $queryBuilder->getQuery()->execute();
        foreach ($result as $value) {
            $out[] = array('sqPessoa' => $value->getSqProfissional()->getSqPessoa(),
                'noPessoa' => $value->getSqProfissional()->getNoPessoa(),
                'nuCpfCnpjPassaporte' => $value->getSqProfissional()->getSqPessoaFisica() != NULL ?
                                         $value->getSqProfissional()->getSqPessoaFisica()->getNuCpf() : NULL
            );
        }
        return $out;
    }

    public function queryAnalise(\Doctrine\ORM\QueryBuilder &$queryBuilder,$dto ,$query)
    {
         $queryBuilder->from('app:VwProfissional', 'vprof')
             ->innerJoin('vprof.sqPessoa', 'p')
             ->innerJoin('vprof.sqUnidadeExercicio', 'ue');

        $this->andWhereQuery($queryBuilder, $dto ,$query);
    }

    public function queryAssinatura(\Doctrine\ORM\QueryBuilder &$queryBuilder,$dto, $query)
    {
        $queryBuilder->from('app:PessoaAssinanteArtefato', 'pa')
            ->innerJoin('pa.sqPessoaUnidadeOrg', 'ue')
            ->innerJoin('ue.sqPessoaSgdoce', 'ps')
            ->innerJoin('pa.sqArtefato', 'a')
            ->innerJoin('ps.sqPessoaCorporativo', 'p')
            ->innerJoin('p.sqProfissional', 'vprof')
            ->where('pa.dtAssinado IS NULL');

        $this->andWhereQuery($queryBuilder, $dto, $query);
        $queryBuilder->andWhere('a.sqArtefato = :sqArtefato')
            ->setParameter('sqArtefato', $dto->getSqArtefato());
    }

    public function andWhereQuery(\Doctrine\ORM\QueryBuilder &$queryBuilder, $dto , $query)
    {
        $_qb = $this->_em->createQueryBuilder();

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('p.noPessoa'));

        $queryBuilder->andWhere(
            $queryBuilder->expr()
                ->like(
                    'clear_accentuation(' . $field .')',
                    $queryBuilder->expr()
                        ->literal($this->removeAccent('%' . $query . '%'))
                )
        );

        $queryBuilder->andWhere('vprof.sqPessoa <> :sqPessoa')
        ->setParameter('sqPessoa', $dto->getSqPessoaLogada());

        if($dto->getExtraParam()) {
            $queryBuilder->andWhere('vprof.sqUnidadeExercicio = :sqUnidadeLotacao')
                ->setParameter('sqUnidadeLotacao', $dto->getExtraParam());
        }
    }

    /**
     * Obtém os dados da pessoa para encaminhar minuta para analise ou assinatura
     * @return array $out
     */
    public function searchPessoas(\Core_Dto_Abstract $dto)
    {
        $sqArtefato    = $dto->getSqArtefato();
        $inExistentAss = $this->verificaPessoaAssinatura($sqArtefato);
        $query         = mb_strtolower($dto->getQuery(), 'UTF-8');
        $queryBuilder  = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('DISTINCT p.sqPessoa, p.noPessoa');

        if ($dto->getInAssinatura() && $inExistentAss) {
            $this->queryAssinatura($queryBuilder,$dto,$query);
        } else {
            $this->queryAnalise($queryBuilder,$dto,$query);
        }

        $queryBuilder->orderBy('p.noPessoa');

        $res = $queryBuilder->getQuery()->execute();
        $out = array();
        foreach ($res as $key => $data) {
            $out[$data['sqPessoa']] = $data['noPessoa'];
        }
        return $out;
    }

    /**
     *
     * Verifica se existe pessoa para assinatura
     * @param $sqArtefato integer
     * @return $inExistentAss boolean
     */
    public function verificaPessoaAssinatura($sqArtefato)
    {
        if (isset($sqArtefato) && !empty($sqArtefato)) {

            $queryExistentAss = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('pss.sqPessoaSgdoce, pss.noPessoa')
                ->from('app:PessoaAssinanteArtefato', 'paa')
                ->innerJoin('paa.sqPessoaUnidadeOrg', 'unid')
                ->innerJoin('unid.sqPessoaSgdoce', 'pss')

                ->andWhere('paa.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $sqArtefato)
                ->getQuery()
                ->execute();
        }

        if (isset($queryExistentAss) && count($queryExistentAss) > 0) {
            $inExistentAss = TRUE;
        } else {
            $inExistentAss = FALSE;
        }

        return $inExistentAss;
    }
}