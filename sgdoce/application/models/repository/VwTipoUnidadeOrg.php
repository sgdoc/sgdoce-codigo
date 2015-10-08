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

use Doctrine\ORM\Query;

/**
 * SISICMBio
 *
 * Classe para Repository Tipo Unidade Org
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwTipoUnidadeOrg
 * @version      1.0.0
 * @since        2012-11-20
 */
class VwTipoUnidadeOrg extends \Core_Model_Repository_Base
{
    private $_enName = 'app:VwTipoUnidadeOrg';

    /**
     * Efetua a busca do tipo unidade org
     * @param \Core_Dto_Abstract $dto
     * @return array
     */
    public function searchTipoUnidadeOrg (\Core_Dto_Abstract $dto)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $query = $queryBuilder->select('t.sqTipoUnidadeOrg, t.noTipoUnidadeOrg')
            ->from($this->_enName, 't')
            ->join('t.sqUnidadeOrg', 'uo');

        //Consulta case-insensitive
        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('t.noTipoUnidadeOrg'));

        $query->andWhere(
            $queryBuilder->expr()
                ->like(
                    'clear_accentuation(' . $field .')',
                    $queryBuilder->expr()
                        ->literal($this->removeAccent('%' . $search . '%'))
                )
        )
            ->groupBy('t.sqTipoUnidadeOrg, t.noTipoUnidadeOrg');

        $res = $query->getQuery()->getArrayResult();
        $out = array();

        foreach ($res as $item) {
            $out[$item['sqTipoUnidadeOrg']] = $item['noTipoUnidadeOrg'];
        }

        return $out;
    }

    /**
     * Obtém os dados da unidade
     * @return array
     */
    public function searchUnidadeOrg (\Core_Dto_Abstract $dto)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $query = $queryBuilder->select('u.sqUnidadeOrg, u.noUnidadeOrg')
            ->from('app:VwUnidadeOrg', 'u');

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('u.noUnidadeOrg'));

        //Consulta case-insensitive
        $query->andWhere(
            $queryBuilder->expr()
                ->like(
                    'clear_accentuation(' . $field .')',
                    $queryBuilder->expr()
                        ->literal($this->removeAccent('%' . $search . '%'))
                )
        );

        if($dto->getExtraParam()) {
            $query->andWhere('u.sqTipoUnidade = :sqTipoUnidade')
                ->setParameter('sqTipoUnidade', $dto->getExtraParam());
        }

        $res = $query->getQuery()->getArrayResult();

        $out = array();
        foreach ($res as $item) {
            $out[$item['sqUnidadeOrg']] = $item['noUnidadeOrg'];
        }
        return $out;
    }

    /**
     * Obtém os dados da unidade por tipo
     * @return Query
     */
    public function searchUnidadeOrgPorTipo (\Core_Dto_Abstract $dto)
    {

        $queryBuilder = $this->_em
        ->createQueryBuilder()
        ->select('u.sqUnidadeOrg, u.noUnidadeOrg')
        ->from('app:VwUnidadeOrg', 'u');

        $queryBuilder->andWhere('u.sqTipoUnidade = :sqTipoUnidade')
        ->setParameter('sqTipoUnidade', $dto->getSqTipoUnidadeOrg());

        $res = $queryBuilder->getQuery()->execute();

        return $res;
    }
}