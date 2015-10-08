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
 * @name	 Menu
 * @version	 1.0.0
 */
class Menu extends \Sica_Model_Repository
{

    public function userMenu(\Core_Dto_Entity $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('m')
                ->from('app:MontarMenu', 'm')
                ->where('m.sqPerfil1 = :sqPerfil1')
                ->setParameter('sqPerfil1', $dto->getSqPerfil()->getSqPerfil())
                ->orWhere('m.sqPerfil2 = :sqPerfil2')
                ->setParameter('sqPerfil2', $dto->getSqPerfil()->getSqPerfil())
                ->orWhere('m.sqPerfil3 = :sqPerfil3')
                ->setParameter('sqPerfil3', $dto->getSqPerfil()->getSqPerfil())
                ->orWhere('m.sqPerfil4 = :sqPerfil4')
                ->setParameter('sqPerfil4', $dto->getSqPerfil()->getSqPerfil());

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function findMenuBySystem($sqSistema, $ativo = FALSE, $order = NULL)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('mh')
                ->from('app:MenuHierarqManter', 'mh')
                ->where('mh.sqSistema = :sqSistema')
                ->setParameter('sqSistema', $sqSistema, 'integer');

        if ($ativo) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('mh.stRegistroAtivo', ':stAtivo'))
                    ->setParameter(':stAtivo', 'TRUE');
        }

        if (NULL !== $order && '' !== $order) {
            $queryBuilder->orderBy('mh.ordenacao', $order);
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function findMenu(\Core_Dto_Entity $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('mh')
                ->from('app:MenuHierarqManter', 'mh');

        if ($dto->getSqSistema()->getSqSistema() !== '' && $dto->getSqSistema()->getSqSistema() !== NULL) {
            $queryBuilder->where('mh.sqSistema = :sqSistema')
                    ->setParameter('sqSistema', $dto->getSqSistema()->getSqSistema(), 'integer');
        }

        if ($dto->getSqMenu() !== '' && $dto->getSqMenu() !== NULL) {
            $queryBuilder->andWhere('mh.sqMenu = :sqMenu')
                    ->setParameter('sqMenu', $dto->getSqMenu(), 'integer');
        }

        if ($dto->getNuNivel() !== '' && $dto->getNuNivel() !== NULL) {
            $queryBuilder->andWhere('mh.nuNivel = :nuNivel')
                    ->setParameter('nuNivel', $dto->getNuNivel(), 'integer');
        }

        if ($dto->getSqMenuPai()->getSqMenu() !== '' && $dto->getSqMenuPai()->getSqMenu() !== NULL) {
            $queryBuilder->andWhere('mh.sqMenuPai = :sqMenuPai')
                    ->setParameter('sqMenuPai', $dto->getSqMenuPai()->getSqMenu(), 'integer');
        }

        if ($dto->getRemoveDaLista() && $dto->getSqMenuLista() !== '' && $dto->getSqMenuLista() !== NULL){
            $queryBuilder->andWhere('mh.sqMenu != :sqMenu')
                ->setParameter('sqMenu', $dto->getSqMenuLista(), 'integer');
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function updateNuOrdemApresent($criteria)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->update('app:Menu', 'm')
                ->set('m.nuOrdemApresent', 'm.nuOrdemApresent + 1 ');

        if ($criteria['nuOrdemApresent'] == 1 || isset($criteria['sqMenu'])) {
            $queryBuilder->where('m.nuOrdemApresent >= :nuOrdemApresent')
                    ->setParameter('nuOrdemApresent', $criteria['nuOrdemApresent']);
        } else {
            $queryBuilder->where('m.nuOrdemApresent >= :nuOrdemApresent')
                    ->setParameter('nuOrdemApresent', $criteria['nuOrdemApresent']);
        }

        if (NULL == $criteria['sqMenuPai']) {
            $queryBuilder->andWhere($queryBuilder->expr()->isNull('m.sqMenuPai'));
        } else {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('m.sqMenuPai', ':sqMenuPai'))
                    ->setParameter('sqMenuPai', $criteria['sqMenuPai']);
        }

        $queryBuilder->andWhere('m.sqSistema = :sqSistema')
                ->setParameter('sqSistema', $criteria['sqSistema']);

        return $queryBuilder->getQuery()->execute();
    }

    public function hasName($entity)
    {

        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('m.noMenu')
                ->from('app:Menu', 'm')
                ->where('LOWER(m.noMenu) = :noMenu')
                ->setParameter(':noMenu', mb_strtolower($entity->getNoMenu(), 'utf-8'), 'string')
                ->andWhere('m.sqSistema = :sqSistema')
                ->setParameter(':sqSistema', $entity->getSqSistema()->getSqSistema(), 'integer');

        if ($entity->getSqMenu() !== NULL) {
            $queryBuilder->andWhere('m.sqMenu <> :sqMenu')
                         ->setParameter(':sqMenu', $entity->getSqMenu());
        }

        if ($entity->getSqMenuPai() !== NULL && $entity->getSqMenuPai()->getSqMenu() !== NULL) {
           $queryBuilder->andWhere('m.sqMenuPai = :sqMenuPai')
                        ->setParameter(':sqMenuPai', $entity->getSqMenuPai()->getSqMenu());
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function findMenuBySystemAndPai($sqSistema, $nuOrdemApresent, $sqMenuPai = NULL, $asc = NULL)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('mh')
                ->from('app:MenuHierarqManter', 'mh')
                ->where('mh.sqSistema = :sqSistema')
                ->setParameter('sqSistema', $sqSistema, 'integer')
                ->orderBy('mh.nuOrdemApresent');

        if ($asc == "asc") {
            $queryBuilder->andWhere($queryBuilder->expr()->gte('mh.nuOrdemApresent', ':ordemApresent'))
                    ->setParameter(':ordemApresent', $nuOrdemApresent, 'integer');
        } else if ($asc == "desc") {
            $queryBuilder->andWhere($queryBuilder->expr()->lte('mh.nuOrdemApresent', ':ordemApresent'))
                    ->setParameter(':ordemApresent', $nuOrdemApresent, 'integer');
        }

        if ($sqMenuPai) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('mh.sqMenuPai', ':menuPai'))
                    ->setParameter(':menuPai', $sqMenuPai, 'integer');
        } else {
            $queryBuilder->andWhere('mh.sqMenuPai IS NULL');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function updateNuOrdem($menu)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->update('app:Menu', 'm')
                ->set('m.nuOrdemApresent', $menu->getNuOrdemApresent());

        $queryBuilder->andWhere('m.sqMenu = :sqMenu')
                ->setParameter('sqMenu', $menu->getSqMenu());

        return $queryBuilder->getQuery()->execute();
    }

}
