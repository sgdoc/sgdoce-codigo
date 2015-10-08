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
use Doctrine\Common\Util\Debug;

/**
 * SISICMBio
 *
 * Classe para Repository de Caixa de Arquivo
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Caixa
 * @version      1.0.0
 * @since        2015-01-30
 */
class Caixa extends \Core_Model_Repository_Base
{
    /**
     *
     * @param \Core_Dto_Search $dto
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function listGrid(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $query = $this->_getInitSqlGrid();

        if ($dto->getNuCaixa()) {
            $query->where($queryBuilder->expr()
                            ->like($queryBuilder->expr()->trim('ca.nuCaixa'),
                                   $queryBuilder->expr()->literal('%' . $dto->getNuCaixa() . '%')
                            )
                    );
        }
        if ($dto->getNuAno()) {
            $query->where($queryBuilder->expr()->eq('ca.nuAno', $dto->getNuAno()));
        }
        if ($dto->getSqClassificacao()) {
            $query->where($queryBuilder->expr()->eq('ca.sqClassificacao', $dto->getSqClassificacao()));
        }
        if ($dto->getSqUnidadeOrg()) {
            $query->where($queryBuilder->expr()->eq('ca.sqUnidadeOrg', $dto->getSqUnidadeOrg()));
        }

        $stFechamento = $dto->getStFechamento();
        if ($stFechamento !== '') {
            if ($stFechamento) { //Fechada
                $query->where('ca.stFechamento = true');
            } else { //Aberta
                $query->where('ca.stFechamento = false');
            }
        }

        return $query;
    }

    /**
     *
     * @param \Core_Dto_Search $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGridCaixaAbertaPorClassificacao(\Core_Dto_Search $dto)
    {

        $sqClassificacao    = (integer) $dto->getSqClassificacao() ? : 0;
        $sqClassificacaoPai = 0;

        if ($sqClassificacao !== 0) {
            $qbClassificacaoPai = $this->_em->createQueryBuilder();
            $qbClassificacaoPai->select('IDENTITY(c.sqClassificacaoPai) as sqClassificacaoPai')
                    ->from('app:Classificacao', 'c')
                    ->where($qbClassificacaoPai->expr()->eq('c.sqClassificacao', $sqClassificacao));

            $sqClassificacaoPai = $qbClassificacaoPai->getQuery()->getSingleScalarResult();
        }

        $queryBuilder = $this->_getInitSqlGrid();
        $queryBuilder->where($queryBuilder->expr()->eq("ca.sqClassificacao", ':sqClassificacaoPai'))
                ->andWhere('ca.stFechamento = false')
                ->setParameter('sqClassificacaoPai', $sqClassificacaoPai);



        return $queryBuilder;
    }

    public function getNextBoxNumber(\Core_Dto_Search $dto)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('nu_caixa', 'nuCaixa', 'string');

        $sql = sprintf("
                    SELECT lpad((COALESCE(max(nu_caixa::INTEGER),0) + 1)::TEXT,7,'0') AS nu_caixa
                      FROM caixa
                     WHERE sq_unidade_org = %d"
                ,$dto->getSqUnidadeOrg());

        $nativeQuery = $this->_em->createNativeQuery($sql, $rsm);

        $result = $nativeQuery->getSingleResult();

        return $result['nuCaixa'];
    }

    /**
     *
     * @return @return \Doctrine\ORM\QueryBuilder
     */
    private function _getInitSqlGrid()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        return $queryBuilder->select('ca.sqCaixa',
                                    'ca.nuCaixa',
                                    'ca.nuAno',
                                    'ca.stFechamento',
                                    'ca.dtCadastro',
                                    'cl.nuClassificacao',
                                    'cl.txClassificacao',
                                    'u.noUnidadeOrg',
                                    'count(caa.sqArtefato) as qtdeArtefatoCaixa')
                           ->from('app:Caixa', 'ca')
                           ->innerJoin('ca.sqClassificacao', 'cl')
                           ->innerJoin('ca.sqUnidadeOrg', 'u')
                           ->leftJoin('ca.sqCaixaArtefato', 'caa')
                           ->groupBy('ca.sqCaixa',
                                     'ca.nuCaixa',
                                     'ca.nuAno',
                                     'ca.stFechamento',
                                     'cl.nuClassificacao',
                                     'cl.txClassificacao',
                                     'u.noUnidadeOrg');
    }

}