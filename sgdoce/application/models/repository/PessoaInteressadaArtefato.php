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
class PessoaInteressadaArtefato extends \Core_Model_Repository_Base
{
    public function searchPessoaInteressada($dto)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('ps.noPessoa'));

        $query = $queryBuilder->select(
            'ps.sqPessoaSgdoce',
            'ps.noPessoa',
            'ps.nuCpfCnpjPassaporte'
        )
            ->from('app:PessoaInteressadaArtefato', 'pia')
            ->innerJoin('pia.sqPessoaSgdoce', 'ps')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                        ->literal($this->removeAccent('%' . $search . '%'))
                    )
            )
            ->orderBy('ps.noPessoa');

        $res = $query->getQuery()->getArrayResult();
        $out = array();
        foreach ($res as $item) {
            $out[$item['sqPessoaSgdoce']] =  trim(ltrim(trim($item['nuCpfCnpjPassaporte'].' - '.$item['noPessoa']),'-'));
        }

        return $out;
    }

    /**
     * Deleta assinatura
     * @param \Core_Dto_Search $dto
     * @return array
     */
    public function deleteInteressado($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
        ->delete($this->_entityName, 'pa')

        ->andWhere('pa.sqArtefato = :sqArtefato')
        ->setParameter('sqArtefato', $dto->getSqArtefato()->getSqArtefato())
        ->andWhere('pa.sqPessoaSgdoce = :sqPessoaSgdoce')
        ->setParameter('sqPessoaSgdoce', $dto->getSqPessoaSgdoce()->getSqPessoaSgdoce());

        $out = $queryBuilder->getQuery()->execute();

        return $out;
    }


    public function deleteInteressadoSemSqCorporativo($dto)
    {
        $sql ="SELECT ps.sq_pessoa_sgdoce
                 FROM pessoa_interessada_artefato pa
                 JOIN pessoa_sgdoce ps using(sq_pessoa_sgdoce)
                WHERE sq_artefato = :sqArtefato
                  AND sq_pessoa_corporativo IS NULL";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('sq_pessoa_sgdoce', 'sqPessoaSgdoce', 'integer');

        $nq = $this->_em->createNativeQuery($sql, $rsm);
        $nq->setParameter('sqArtefato', $dto->getSqArtefato());

        $result = $nq->getArrayResult();

        if(! $result){
            return 0;
        }

        $aux = array();
        foreach ($result as $value) {
            $aux[] = $value['sqPessoaSgdoce'];
        }

        $queryBuilder = $this->_em->createQueryBuilder();
        $delete = $queryBuilder->delete($this->_entityName, 'pa')

        ->andWhere('pa.sqArtefato = :sqArtefato')
        ->andWhere($queryBuilder->expr()->in('pa.sqPessoaSgdoce', $aux))
        ->setParameter('sqArtefato', $dto->getSqArtefato());

        $out = $delete->getQuery()->execute();

        return $out;
    }

    public function getPessoaInteressadaArtefato($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
        ->select('ps.noPessoa')
        ->from('app:PessoaInteressadaArtefato', 'pia')
        ->innerJoin('pia.sqArtefato', 'a')
        ->innerJoin('pia.sqPessoaSgdoce', 'ps')
        ->andWhere('a.sqArtefato = :sqArtefato')
        ->setParameter('sqArtefato', $dto->getSqArtefato());

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * Obtén dados da pessoa de interesse de uma minuta
     * @param type $dto
     * @return null
     */
    public function getPessoaArtefatoInteressado($dto)
    {
        $query = $this->_em->createQueryBuilder()
        ->select('pf.sqPessoaFuncao, pf.noPessoaFuncao, p.sqPessoa, p.noPessoa, p.noProfissao,
                                t.noTratamento, tv.deEnderecamento, v.noVocativo, cid.noMunicipio, est.noEstado,
                                p.coCep, a.txEmenta')
                                    ->from($this->_enName, 'p')
                                    ->innerJoin('p.sqPessoaFuncao', 'pf')
                                    ->leftJoin('p.sqTratamentoVocativo', 'tv')
                                    ->leftJoin('tv.sqTratamento', 't')
                                    ->leftJoin('tv.sqVocativo', 'v')
                                    ->leftJoin('p.sqMunicipioEndereco', 'cid')
                                    ->leftJoin('cid.sqEstado', 'est')
                                    ->innerJoin('p.sqArtefato', 'a')
                                    ->andWhere('p.sqArtefato = :sqArtefato')
                                    ->setParameter('sqArtefato', $dto->getSqArtefato())
                                    ->andWhere('p.sqPessoaFuncao = :sqPessoaFuncao')
                                    ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoInteressado())
                                    ->getQuery()
                                    ->execute();

        if (empty($query)) {
            return NULL;
        }

        return $query;
    }

    /**
     * Obtén dados da pessoa de interesse de uma minuta
     * @param type $dto
     * @return null
     */
    public function findPessoaInteressada($entityPessoaSgdoce)
    {

        $nuCpfCnpjPassaporte = str_replace('-', '', $entityPessoaSgdoce->getNuCPFInteressado());
        $nuCpfCnpjPassaporte = str_replace('/', '', $nuCpfCnpjPassaporte);
        $nuCpfCnpjPassaporte = str_replace('.', '', $nuCpfCnpjPassaporte);

        $query = $this->_em->createQueryBuilder()
        ->select('p.sqPessoaSgdoce')
        ->from('app:Artefato', 'a')
        ->innerJoin('a.sqPessoaInteressadaArtefato', 'pa')
        ->innerJoin('pa.sqPessoaSgdoce', 'p')
        ->andWhere('a.sqArtefato = :sqArtefato')
        ->setParameter('sqArtefato', $entityPessoaSgdoce->getSqArtefato());
        if ($entityPessoaSgdoce->getSqPessoaCorporativo()) {
            $query->andWhere('p.sqPessoaCorporativo = :sqPessoaCorporativo')
            ->setParameter('sqPessoaCorporativo', $entityPessoaSgdoce->getSqPessoaCorporativo());
        } else {
            $query->andWhere('p.nuCpfCnpjPassaporte = :nuCpfCnpjPassaporte')
            ->setParameter('nuCpfCnpjPassaporte', $nuCpfCnpjPassaporte);
        }

        return $query->getQuery()->execute();
    }

    /**
     * método que retorna dados para grid de interessados nos Artefato
     * @param \Core_Dto_Abstract $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGridInteressadosArtefato(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
                      ->select('ps.sqPessoaSgdoce,
                                  ps.noPessoa,
                                  ps.nuCpfCnpjPassaporte'
                              )
                        ->from('app:Artefato', 'a')
                        ->innerJoin('a.sqPessoaInteressadaArtefato', 'pa')
                        ->innerJoin('pa.sqPessoaSgdoce', 'ps')
            ->andWhere('a.sqArtefato = :sqArtefato')
            ->setParameter('sqArtefato', $dto->getSqArtefato());
        return $queryBuilder;
    }


    /**
     * método que retorna dados para Vizualizar o Artefato
     * @param \Core_Dto_Abstract $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function countInteressadosArtefato(\Core_Dto_Search $dto)
    {
        $query = mb_strtolower($dto->getQuery(), 'UTF-8');
        $queryBuilder = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('count(ps.sqPessoaSgdoce) as nu_interessados')
                ->from('app:Artefato', 'a')
                ->innerJoin('a.sqPessoaInteressadaArtefato', 'pa')
                ->innerJoin('pa.sqPessoaSgdoce', 'ps')
                ->andWhere('a.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $dto->getSqArtefato());
        return $res = $queryBuilder->getQuery()->getSingleResult();
    }

    /**
     * Deleta assinatura
     * @param \Core_Dto_Search $dto
     * @return array
     */
    public function deleteTodosInteressado($sqArtefato)
    {
    	$queryBuilder = $this->_em->createQueryBuilder()
    	->delete($this->_entityName, 'm')
    	->andWhere('m.sqArtefato = :sqArtefato')
    	->setParameter('sqArtefato', $sqArtefato);

    	$out = $queryBuilder->getQuery()->execute();

    	return $out;
    }


    /**
     * método que retorna dados para Vizualizar o Artefato
     * @param \Core_Dto_Abstract $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function countInteressadosArtefatoValido(\Core_Dto_Search $dto)
    {
        $query = mb_strtolower($dto->getQuery(), 'UTF-8');
        $queryBuilder = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('count(ps.sqPessoaSgdoce) as nu_interessados')
                ->from('app:Artefato', 'a')
                ->innerJoin('a.sqPessoaInteressadaArtefato', 'pa')
                ->innerJoin('pa.sqPessoaSgdoce', 'ps')
                ->andWhere('a.sqArtefato = :sqArtefato')
                ->andWhere('ps.sqPessoaCorporativo IS NOT NULL')
                ->setParameter('sqArtefato', $dto->getSqArtefato());
        return $res = $queryBuilder->getQuery()->getSingleResult();
    }
}