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
 * Classe para Repository Pessoa Juridica
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwPessoaJuridica
 * @version      1.0.0
 * @since        2012-11-20
 */
class VwPessoaJuridica extends \Core_Model_Repository_Base
{
    public function searchRazaoSocial($criteria)
    {
        $query = null;

        try {
            $_qb   = $this->getEntityManager()->createQueryBuilder();
            $query = $_qb->select(array('pj'))
                ->from($this->_entityName, 'pj')
                ->where($_qb->expr()->lower('pj.noPessoa') . ' = :noPessoa')
                ->setParameter('noPessoa', $criteria['noPessoa'])
                ->getQuery()
                ->getSingleResult();
        } catch(\Exception $e) { }

        return $query;
    }

    public function getMatrizFilial($criteria)
    {
        $_qb   = $this->getEntityManager()->createQueryBuilder();
        $query = $_qb->select(
                array(
                    $_qb->expr()->substring('pj.nuCnpj', 9) . ' AS dv',
                    'pj'
                )
            )
            ->from($this->_entityName, 'pj')
            ->where($_qb->expr()->substring('pj.nuCnpj', 1, 8) . ' = :nuRaiz')
            ->orderBy('dv', 'ASC')
            ->setParameter('nuRaiz', $criteria['nuRaiz'])
            ->getQuery()
            ->getResult();

        return $query;
    }

    public function searchPessoaJuridica($dto, $retornaCpf = TRUE, $limit = 10)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('pj.noPessoa'));

        $query = $queryBuilder->select(
                'p.sqPessoa',
                'pj.noPessoa',
                'pj.nuCnpj'
            )
            ->from($this->_entityName, 'pj')
            ->innerJoin('pj.sqPessoaJuridica', 'p')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            )
            ->orderBy('pj.noPessoa')
            ->setMaxResults($limit);

        $res = $queryBuilder->getQuery()->getArrayResult();
        $out = array();

        if ($retornaCpf) {
            foreach ($res as $item) {
                $nuCnpj = null;

                if($item['nuCnpj']) {
                    $nuCnpj  = \Zend_Filter::filterStatic($item['nuCnpj'], 'MaskNumber', array('cnpj'), array('Core_Filter'));
                    $nuCnpj .= ' - ';
                }

                $out[$item['sqPessoa']] =  $nuCnpj . $item['noPessoa'];
            }
        } else {
            foreach ($res as $item) {
                $out[$item['sqPessoa']] =  $item['noPessoa'];
            }
        }

        return $out;
    }

    public function searchPessoaJuridicaPorCnpj($nuCnpj)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $query = $queryBuilder->select(
                'p.sqPessoa',
                'pj.noPessoa'
        )
        ->from('app:VwPessoa', 'p')
        ->innerJoin('p.sqPessoaJuridica', 'pj')
        ->where('pj.nuCnpj = :nuCnpj')
        ->setParameter('nuCnpj',  $nuCnpj);


        $result = $queryBuilder->getQuery()->getResult();

        return (count($result) > 0) ? $result[0]:array();

//        return $queryBuilder->getQuery()->getSingleResult();
    }
}
