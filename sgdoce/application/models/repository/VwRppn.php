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

use Doctrine\ORM\EntityRepository;

/**
 * Rppn
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VwRppn extends \Core_Model_Repository_Base
{
    /**
     * método que lista pessoa
     * @param array $params
     * @return Query
     */
    public function listPessoa($params, $limit = 10)
    {
        $search = is_object($params)? mb_strtolower($params->getQuery(),'UTF-8') : mb_strtolower($params['query'],'UTF-8');

        $queryBuilder = $this->_em->createQueryBuilder()
            ->select('r')
            ->from('app:VwRppn', 'r');

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('r.sgRppn'));

        $queryBuilder->andWhere(
            $queryBuilder->expr()
                ->like(
                    'clear_accentuation(' . $field .')',
                    $queryBuilder->expr()
                        ->literal($this->removeAccent('%' . $search . '%'))
                )
        )->orderBy('r.sgRppn');
        
        $queryBuilder->setMaxResults($limit);

        return $queryBuilder->getQuery()->execute();
    }
}