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

namespace Sica\Model\Repository;

/**
 * SISICMBio
 *
 * Classe para Repository de Usuario
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 Usuario
 * @version	 1.0.0
 */
class UnidadeOrg extends \Sica_Model_Repository
{

    public function unitsActives()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('u')
                ->from($this->_entityName, 'u')
                ->andWhere($queryBuilder->expr()->eq('u.stAtivo', ':active'))
                ->setParameter('active', TRUE, 'boolean')
                ->orderBy('u.sgUnidadeOrg, u.noPessoa');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function findByNoUnidade($noUnidade)
    {
        $queryBuilder = $this->getEntityManager()
                ->createQueryBuilder();

        $queryBuilder
                ->select('u')
                ->from($this->_entityName, 'u')
                ->andWhere($queryBuilder->expr()->eq('u.stAtivo', ':active'))
                ->setParameter('active', TRUE, 'boolean')
                ->orderBy('u.sgUnidadeOrg, u.noPessoa')
                ->setMaxResults(10);

        $expre1 = $queryBuilder->expr()->lower($queryBuilder->expr()->trim('u.noPessoa'));
        $expre2 = $queryBuilder->expr()->lower($queryBuilder->expr()->trim('u.sgUnidadeOrg'));
        $value = "%" . mb_strtolower(trim($noUnidade), 'UTF-8') . "%";

        $queryBuilder->andWhere($queryBuilder->expr()->orX(
                        '(' . $queryBuilder->expr()->like($expre1, ':pessoa') . ' OR ' .
                        $queryBuilder->expr()->like($expre1, ':orPessoa') . ' OR ' .
                        $queryBuilder->expr()->like('clear_accentuation(' . $expre1 . ')'
                                , $queryBuilder->expr()->literal($this->translate($value))) . ')'
                        , '(' . $queryBuilder->expr()->like($expre2, ':pessoa') . ' OR ' .
                        $queryBuilder->expr()->like($expre2, ':orPessoa') . ' OR ' .
                        $queryBuilder->expr()->like('clear_accentuation(' . $expre2 . ')'
                                , $queryBuilder->expr()->literal($this->translate($value))) . ')'
                ));
        $queryBuilder->setParameter('pessoa', $value)
                ->setParameter('orPessoa', $this->translate($value));

        return $queryBuilder->getQuery()->getArrayResult();
    }

}
