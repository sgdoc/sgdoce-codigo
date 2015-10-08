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
 * Classe para Repository de Unidade Org
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwUnidadeOrg
 * @version      1.0.0
 * @since        2012-11-20
 */
class VwUnidadeOrg extends \Core_Model_Repository_Base
{
    const ZER = 0;

    /**
     * método que pesquisa unidades organizacinais para combo
     * @param array $params
     * @return array $out
     */
    public function searchUnidadesOrganizacionais ($params, $nuLimit = 10)
    {
        $search       = mb_strtolower($params['query'],'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $query = $queryBuilder
                ->select('u,rppn,p')
                ->from('app:VwPessoa', 'p')
                ->join('p.sqPessoaParaUnidadeOrg','u')
                ->leftJoin('p.sqRppn','rppn');
        if(isset($params['extraParam']) && is_integer($params['extraParam'])) {
            $query->innerJoin('u.sqProfissional', 'ue')
                ->andWhere('ue.sqProfissional = :sqProfissional')
                ->setParameter('sqProfissional', $params['extraParam']);
        }

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('p.noPessoa'));

        $sigla = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('u.sgUnidadeOrg'));

        $query->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            )
            ->orWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $sigla .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            )
            ->setMaxResults($nuLimit)
            ->orderBy('p.noPessoa');

        $res = $query->getQuery()
                     ->useResultCache(TRUE, NULL, __METHOD__)
                     ->getArrayResult();

        $out = array();
        foreach ($res as $item) {
             $out[$item['sqPessoa']] = $item['sqPessoaParaUnidadeOrg']['sgUnidadeOrg'] . ' - ' . $item['sqPessoaParaUnidadeOrg']['noUnidadeOrg'];
        }
        return $out;
    }

    /**
     * método para pesquisa de unidades organizacinais para combo
     * @param array $params
     * @return array $out
     */
    public function listUnidadesOrganizacionais ()
    {
        $queryBuilder = $this->getEntityManager()
                             ->createQueryBuilder()
                             ->select('u')
                             ->from('app:VwUnidadeOrg', 'u')
                             ->orderBy('u.noUnidadeOrg');

        $res = $queryBuilder->getQuery()->getArrayResult();

        $out = array();
        foreach ($res as $item) {
            $out[$item['sqUnidadeOrg']] = $item['noUnidadeOrg'];
        }

        return $out;
    }

    /**
     * método para pesquisa de unidades organizacinais para combo
     * @param array $params
     * @return array $out
     */
    public function getDadosUnidade ($dtoSearch)
    {
        $queryBuilder = $this->getEntityManager()
        ->createQueryBuilder()
        ->select('u')
        ->from('app:VwUnidadeOrg', 'u')
        ->andWhere('u.sqUnidadeOrg = :sqUnidadeOrg')
        ->setParameter('sqUnidadeOrg', $dtoSearch->getSqUnidade());

        $result = $queryBuilder->getQuery()->execute();
        return $result[self::ZER];
    }

    /**
     * método para pesquisa de unidades organizacinais para combo
     * @param array $params
     * @return array $out
     */
    public function getUnidadeOrigem ($dtoSearch)
    {
        $queryBuilder = $this->getEntityManager()
        ->createQueryBuilder()
        ->select('u.coUorg')
        ->from('app:VwUnidadeOrg', 'u')
        ->andWhere('u.sqUnidadeOrg = :sqUnidadeOrg')
        ->setParameter('sqUnidadeOrg', $dtoSearch->getSqUnidade());

        $result = $queryBuilder->getQuery()->execute();

        return $result;
    }

    /**
     * Obtém as unidades Org Icmbio
     * @return array
     */
    public function unidadeOrgIcmbio(\Core_Dto_Search $dto)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('uo.noUnidadeOrg'));

        $query = $queryBuilder->select('uo.sqUnidadeOrg', 'uo.noUnidadeOrg')
            ->from('app:VwUnidadeOrg', 'uo')
            ->innerJoin('uo.sqTipoUnidade', 'tu')
            ->where($queryBuilder->expr()->eq('tu.inEstrutura', ':inEstrutura'))
            ->setParameter(':inEstrutura', 'TRUE')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            );

        $res = $query->getQuery()->getArrayResult();

        $out = array();

        foreach ($res as $item) {
             $out[$item['sqUnidadeOrg']] = $item['noUnidadeOrg'];
        }

        return $out;
    }

    /**
     * método que pesquisa assinatura para preencher autocomplete
     * @param string $term
     * @return multitype:NULL
     */
    public function searchUnidade ($term)
    {
        $search       = mb_strtolower($term,'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('u.noPessoa'));

        $term = mb_strtolower($term, 'UTF-8');
        $query = $queryBuilder->select('u.sqPessoa', 'u.noPessoa')
            ->from('app:VwPessoa', 'u')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            )
            ->andWhere( $queryBuilder->expr()->eq('u.sqTipoPessoa', \Core_Configuration::getCorpTipoPessoaUnidadeOrg()));

        $res = $query->getQuery()->getArrayResult();

        $out = array();
        foreach ($res as $item) {
             $out[$item['sqPessoa']] = $item['noPessoa'];
        }

        return $out;
    }

    /**
     * método que pesquisa assinatura para preencher autocomplete
     * @param string $term
     * @return multitype:NULL
     */
    public function searchVwUnidadeOrg (\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('uo')
                     ->from('app:VwUnidadeOrg', 'uo')
                     ->where('uo.sqUnidadeOrg = :sqPessoa')
                     ->setParameter('sqPessoa',$dto->getSqPessoaCorporativo());

        $res = $queryBuilder->getQuery()->getArrayResult();

        if(count($res)){
            $out = array();
            foreach ($res as $item) {
                $out['sqTipoPessoa'] = $item['sqTipoPessoa'];
            }
            return $out;
        } else {
            return $res;
        }

    }

    public function isSede (\Core_Dto_Search $dto)
    {
        $sql = "select not :sqUnidadeOrg in (
                               SELECT
                               uo.sq_pessoa
                               FROM corporativo.vw_unidade_org uo
                               join corporativo.tipo_unidade_org_hierarq tp on  tp.sq_tipo_unidade_org = uo.sq_tipo_unidade
                               WHERE trilha_sigla ilike '%Aut-->UD-->%'
                           ) as is_sede";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('is_sede', 'isSede');

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqUnidadeOrg', $dto->getSqUnidadeOrg());

        return $query->getSingleScalarResult();
    }
}