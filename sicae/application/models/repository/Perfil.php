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
 * @name	 Perfil
 * @version	 1.0.0
 */
class Perfil extends \Sica_Model_Repository
{

    public function listGrid(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->distinct()
                     ->select(
                         'p.sqPerfil',
                         'p.noPerfil',
                         'p.inPerfilExterno',
                         'p.stRegistroAtivo',
                         'p.nuQuantidadeUsuario',
                         'p.nuQuantidadeUsuarioExterno'
                     )->from(
                         'app:VwPerfil',
                         'p'
                     )->leftJoin(
                         'p.sqPerfilFuncionalidade', 
                         'pf'
                     );
        if ($dto->getSqSistema()) {
            $queryBuilder->where('p.sqSistema = :sqSistema')
                         ->setParameter(':sqSistema', $dto->getSqSistema());
        }

        if ($dto->hasinPerfilExterno()) {
            $this->filtroinPerfilExterno($dto, $queryBuilder);
        }

        if ($dto->hasSqPerfil() && $dto->getSqPerfil() !== "") {
            $queryBuilder->andWhere('p.sqPerfil =  :sqPerfil')
                         ->setParameter(':sqPerfil', $dto->getSqPerfil());
        }

        // $queryBuilder->groupBy('p.sqPerfil', 'p.noPerfil', 'p.inPerfilExterno', 'p.stRegistroAtivo');

        return $queryBuilder;
    }

    public function comboProfile(\Core_Dto_Mapping $dto, $inFuncionalidade = TRUE)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('p.sqPerfil', 'p.noPerfil')
                ->from('app:Perfil', 'p')
                ->innerJoin('p.sqSistema', 's')
                ->where('p.stRegistroAtivo = :stRegistroAtivo')
                ->setParameter('stRegistroAtivo', 'TRUE');

        if ($inFuncionalidade) {
            $queryBuilder->leftJoin('p.sqPerfilFuncionalidade', 'pf');
        }

        if ($dto->getSqSistema() !== '') {
            $queryBuilder->andWhere('p.sqSistema = :sqSistema')
                    ->setParameter('sqSistema', $dto->getSqSistema());
        }

        if ($dto->getInPerfilExterno() == '1') {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('p.inPerfilExterno', ':perfilExterno'));
            $queryBuilder->setParameter('perfilExterno', 'TRUE');
        } else if ($dto->getInPerfilExterno() == '0') {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('p.inPerfilExterno', ':perfilExterno'));
            $queryBuilder->setParameter('perfilExterno', 'FALSE');
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function hasName($entity)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('p.noPerfil')
                ->from('app:Perfil', 'p')
                ->where('LOWER(p.noPerfil) = :noPerfil')
                ->andWhere('p.sqSistema = :sqSistema')
                ->setParameter(':noPerfil', trim(mb_strtolower($entity->getNoPerfil(), 'UTF-8')))
                ->setParameter(':sqSistema', $entity->getSqSistema()->getSqSistema());

        if ($entity->getSqPerfil() != "") {
            $queryBuilder->andWhere('p.sqPerfil <> :sqPerfil')
                    ->setParameter(':sqPerfil', $entity->getSqPerfil());
        }

        if ($entity->getInPerfilExterno() != "") {
            $queryBuilder->andWhere('p.inPerfilExterno = :inPerfilExterno')
                    ->setParameter(':inPerfilExterno', $entity->getInPerfilExterno());
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function checkExistsExternalProfileDefault($entity)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('p.noPerfil')
                ->from('app:Sistema', 's')
                ->innerJoin('s.sqPerfilExternoPadrao', 'p')
                ->where('p.sqSistema = :sqSistema')
                ->andWhere('p.inPerfilExterno = :inPerfilExterno')
                ->andWhere($queryBuilder->expr()->isNotNull('s.sqPerfilExternoPadrao'))
                ->setParameter(':sqSistema', $entity->getSqSistema()->getSqSistema())
                ->setParameter(':inPerfilExterno', 'TRUE');

        if ($entity->getSqPerfil() != "") {
            $queryBuilder->andWhere('p.sqPerfil <> :sqPerfil')
                    ->setParameter(':sqPerfil', $entity->getSqPerfil());
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function filtroinPerfilExterno($dto, &$queryBuilder)
    {
        $queryBuilder->andWhere($queryBuilder->expr()->eq('p.inPerfilExterno', ':inPerfilExterno'));
        if ($dto->getinPerfilExterno() == '0') {
            $queryBuilder->setParameter(':inPerfilExterno', 'FALSE');
        } else {
            $queryBuilder->setParameter(':inPerfilExterno', 'TRUE');
        }
    }

    public function viewPerfil(\Core_Dto_Entity $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('s.noSistema'
                        , "CASE WHEN s.sqPerfilExternoPadrao = p.sqPerfil THEN TRUE ELSE FALSE END padrao"
                        , 'p.noPerfil'
                        , 'p.inPerfilExterno'
                        , 'm.noMenu'
                        , 'f.noFuncionalidade'
                        , 'tp.noPerfil noTipoPerfil'
                        , 'p.stRegistroAtivo')
                ->from('app:PerfilFuncionalidade', 'pf')
                ->innerJoin('pf.sqPerfil', 'p')
                ->innerJoin('pf.sqFuncionalidade', 'f')
                ->innerJoin('p.sqSistema', 's')
                ->innerJoin('f.sqMenu', 'm')
                ->leftJoin('p.sqTipoPerfil', 'tp')
                ->where($queryBuilder->expr()->eq('p.sqPerfil', ':sqPerfil'))
                ->setParameter('sqPerfil', $dto->getSqPerfil())
                ->groupBy('s.noSistema'
                        , 'p.noPerfil'
                        , 'p.inPerfilExterno'
                        , 'm.noMenu'
                        , 'f.noFuncionalidade'
                        , 'tp.noPerfil'
                        , 's.sqPerfilExternoPadrao'
                        , 'p.sqPerfil'
                        , 'p.stRegistroAtivo');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function findProfilesFull(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('s.noSistema'
                        , 's.sgSistema'
                        , 'p.noPerfil'
                        , 'p.inPerfilExterno'
                        , 'p.stRegistroAtivo'
                        , 'm.noMenu'
                        , 'f.noFuncionalidade')
                ->from('app:PerfilFuncionalidade', 'pf')
                ->innerJoin('pf.sqPerfil', 'p')
                ->innerJoin('p.sqSistema', 's')
                ->innerJoin('pf.sqFuncionalidade', 'f')
                ->innerJoin('f.sqMenu', 'm')
                ->where('p.sqSistema = :sqSistema')
                ->setParameter(':sqSistema', $dto->getSqSistema());

        if ($dto->hasinPerfilExterno()) {
            $this->filtroinPerfilExterno($dto, $queryBuilder);
        }

        if ($dto->hasSqPerfil() && $dto->getSqPerfil() !== "") {
            $queryBuilder->andWhere('p.sqPerfil =  :sqPerfil')
                    ->setParameter(':sqPerfil', $dto->getSqPerfil());
        }

        $queryBuilder->groupBy('s.noSistema'
                , 's.sgSistema'
                , 'p.noPerfil'
                , 'p.inPerfilExterno'
                , 'p.stRegistroAtivo'
                , 'm.noMenu'
                , 'f.noFuncionalidade'
        );

        $queryBuilder->orderBy('s.noSistema, p.noPerfil, m.noMenu');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function existsUserInProfile(\Core_Dto_Entity $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(
                        $queryBuilder->expr()->count('up.sqUsuarioPerfil')
                )
                ->from('app:UsuarioPerfil', 'up')
                ->innerJoin('up.sqPerfil', 'p')
                ->where($queryBuilder->expr()->eq('p.sqPerfil', ':sqPerfil'))
                ->setParameter('sqPerfil', $dto->getSqPerfil());

        $result = $queryBuilder->getQuery()->getSingleScalarResult();

        if ($result) {
            return $result;
        }

        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(
                        $queryBuilder->expr()->count('up.sqUsuarioPerfil')
                )
                ->from('app:UsuarioExternoPerfil', 'up')
                ->innerJoin('up.sqPerfil', 'p')
                ->where($queryBuilder->expr()->eq('p.sqPerfil', ':sqPerfil'))
                ->setParameter('sqPerfil', $dto->getSqPerfil());

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

}
