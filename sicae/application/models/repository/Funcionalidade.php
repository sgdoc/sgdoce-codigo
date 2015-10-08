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

use Bisna\Application\Resource\Doctrine;

/**
 * SISICMBio
 *
 * Classe para Repository de Perfil do Usuario
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 Funcionalidade
 * @version	 1.0.0
 */

class Funcionalidade extends \Sica_Model_Repository
{
    public function hasMenu($sqMenu = NULL)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
            ->select('f.sqFuncionalidade')
            ->from('app:Funcionalidade','f')
            ->innerJoin('f.sqMenu', 'm')
            ->where('f.stRegistroAtivo = :stRegistro')
            ->setParameter('stRegistro', TRUE, 'boolean');

        if (isset($sqMenu) && $sqMenu !== '') {
            $queryBuilder->andwhere('m.sqMenu = :sqMenu')
            ->setParameter('sqMenu', $sqMenu);
        }

        return $queryBuilder->getQuery()->execute();

    }

    public function listGrid($dto)
    {
        $querybuilder = $this->_em->createQueryBuilder();

        $querybuilder->select('f.noFuncionalidade',
                              'f.sqFuncionalidade',
                              'f.inFuncionalidadePrincipal',
                              'f.stRegistroAtivo',
                              'm.noMenu',
                              'f.stRegistroAtivo',
                              's.sgSistema',
                              's.noSistema',
                              'f.nuQuantidadePerfil')
                     ->from('app:VwFuncionalidade', 'f')
                     ->innerJoin('f.sqMenu', 'm')
                     ->innerJoin('f.sqSistema', 's');

        if ($dto->hasSqSistema()) {
            $querybuilder->andWhere($querybuilder->expr()->eq('s.sqSistema', ':sistema'))
                         ->setParameter('sistema', $dto->getSqSistema());
        }

        if ($dto->hasSqMenu()) {
            $querybuilder->andWhere($querybuilder->expr()->eq('m.sqMenu', ':menu'))
                         ->setParameter('menu', $dto->getSqMenu());
        }

        return $querybuilder;
    }

    public function validatePrincipal($entity)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('f.sqFuncionalidade')
                    ->from('app:Funcionalidade','f')
                    ->innerJoin('f.sqMenu', 'm')
                    ->andWhere($queryBuilder->expr()->eq('m.sqMenu', ':sqMenu'))
                    ->andWhere($queryBuilder->expr()->eq('f.inFuncionalidadePrincipal', ':principal'))
                    ->setParameter('sqMenu', $entity->getSqMenu()->getSqMenu())
                    ->setParameter('principal', TRUE, 'boolean');

        $codigo = $this->_class->getIdentifierValues($entity);

        if ($codigo && $entity->getSqFuncionalidade()) {
            $queryBuilder->andWhere($queryBuilder->expr()->neq('f.sqFuncionalidade', ':sqFuncionalidade'))
                  ->setParameter('sqFuncionalidade', $entity->getSqFuncionalidade());
        }

        $result = $queryBuilder->getQuery()
                           ->getArrayResult();

        if ($result) {
            return TRUE;
        }

        return FALSE;
    }

    public function validateName($entity)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('f.sqFuncionalidade')
                    ->from('app:Funcionalidade','f')
                    ->innerJoin('f.sqMenu', 'm')
                    ->where($queryBuilder->expr()->eq(
                        $queryBuilder->expr()->upper('f.noFuncionalidade'),
                        $queryBuilder->expr()->upper(':nome')
                    ))
                    ->andWhere($queryBuilder->expr()->eq('m.sqMenu', ':sqMenu'))
                    ->setParameter('sqMenu', $entity->getSqMenu()->getSqMenu())
                    ->setParameter('nome', $entity->getNoFuncionalidade());

        $codigo = $this->_class->getIdentifierValues($entity);

        if ($codigo && $entity->getSqFuncionalidade()) {
            $queryBuilder->andWhere($queryBuilder->expr()->neq('f.sqFuncionalidade', ':sqFuncionalidade'))
                  ->setParameter('sqFuncionalidade', $entity->getSqFuncionalidade());
        }

        $result = $queryBuilder->getQuery()
                           ->getArrayResult();

        if ($result) {
            return TRUE;
        }

        return FALSE;
    }

    public function findFuncionalities(\Core_Dto_Search $dtoSearch)
    {
        $query = $this->listGrid($dtoSearch) ;
        return $query->getQuery()->getArrayResult();
    }

    public function menuFuncionality(\Core_Dto_Entity $dto)
    {
        $queryBuilder    = $this->_em->createQueryBuilder();
        $subQueryBuilder = $this->_em->createQueryBuilder();

        $subQueryBuilder->select('m.noMenu')
        ->from('app:MenuHierarqManter','m')
        ->where($queryBuilder->expr()->eq('m.sqMenu', 'mhm.sqMenuPai'))
        ->getDQL();

        $queryBuilder->select('mhm.sqMenu',
                              'mhm.noMenu',
                              'f.noFuncionalidade',
                              'f.sqFuncionalidade',
                              'mhm.nuOrdemApresent',
                              'mhm.nuNivel',
                              'mhm.stRegistroAtivo'
        )
        ->addSelect('(' . $subQueryBuilder . ') noMenuPai')
        ->from('app:Funcionalidade','f')
        ->innerJoin('f.sqMenuHierarquico', 'mhm')
        ->where($queryBuilder->expr()->eq('mhm.sqSistema', ':sqSistema'))
                     ->andWhere($queryBuilder->expr()->eq('f.stRegistroAtivo', ':stAtivo'))
            ->setParameter('sqSistema', $dto->getSqSistema())
             ->setParameter(':stAtivo', 'TRUE');

        if ($dto->getSqMenu() != '0') {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                            $queryBuilder->expr()->eq('mhm.sqMenu', ':sqMenu'),
                            $queryBuilder->expr()->eq('mhm.sqMenuPai', ':sqMenuPai')));
            $queryBuilder->setParameter('sqMenu', $dto->getSqMenu());
            $queryBuilder->setParameter('sqMenuPai', $dto->getSqMenu());
        }

        $queryBuilder->orderBy('mhm.sqMenu, mhm.nuNivel ,mhm.nuOrdemApresent');

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
