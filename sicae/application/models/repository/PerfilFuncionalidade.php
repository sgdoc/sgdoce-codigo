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
 * Classe para Repository de Perfil do Usuario
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 Menu
 * @version	 1.0.0
 */

class PerfilFuncionalidade extends \Sica_Model_Repository
{
    public function menuAcessoById($sqPerfil)
    {
         $queryBuilder = $this->_em->createQueryBuilder();
         $queryBuilder->select('m.sqMenu')
             ->from('app:PerfilFuncionalidade','pf')
             ->innerJoin('pf.sqFuncionalidade','f')
             ->innerJoin('f.sqMenu','m')
             ->where($queryBuilder->expr()->eq('pf.sqPerfil',':sqPerfil'))
                 ->setParameter('sqPerfil', $sqPerfil)
             ->andWhere($queryBuilder->expr()->eq('pf.stRegistroAtivo',':stAtivo'))
                 ->setParameter('stAtivo', true)
         ->groupBy('m.sqMenu');

         return $queryBuilder->getQuery()->getArrayResult();

    }

    public function funcionalidadeAcessoById($sqPerfil)
    {
        $arrResult = array();

        foreach($this->menuAcessoById($sqPerfil) as $menu ){
            $queryBuilder = $this->_em->createQueryBuilder();
            $queryBuilder->select('f.sqFuncionalidade')->distinct()
                        ->from('app:PerfilFuncionalidade','pf')
                            ->innerJoin('pf.sqFuncionalidade','f')
                            ->innerJoin('f.sqMenu','m')
                        ->where($queryBuilder->expr()->eq('m.sqMenu',':sqMenu'))
                            ->setParameter('sqMenu', $menu['sqMenu'])
                        ->andWhere($queryBuilder->expr()->eq('pf.stRegistroAtivo',':stAtivo'))
                            ->setParameter('stAtivo', true);

            $arrResult += $queryBuilder->getQuery()->getArrayResult();
        }

        return $arrResult;

    }

    public function menuAcessoByIdAndMenu($menu, $perfil)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('f.sqFuncionalidade')->distinct()
            ->from('app:PerfilFuncionalidade','pf')
                ->innerJoin('pf.sqFuncionalidade','f')
            ->where($queryBuilder->expr()->eq('f.sqMenu',':sqMenu'))
                ->setParameter('sqMenu', $menu)
            ->andWhere($queryBuilder->expr()->eq('pf.sqPerfil',':sqPerfil'))
                ->setParameter('sqPerfil', $perfil)
            ->andWhere($queryBuilder->expr()->eq('pf.stRegistroAtivo',':stAtivo'))
                ->setParameter('stAtivo', true);

        return $queryBuilder->getQuery()->getArrayResult();

    }

    public function funcionalityByProfile($sqPerfil)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('f.sqFuncionalidade')
        ->from('app:PerfilFuncionalidade','pf')
        ->innerJoin('pf.sqFuncionalidade','f')
        ->innerJoin('pf.sqPerfil','p')
        ->where($queryBuilder->expr()->eq('pf.sqPerfil',':sqPerfil'))
        ->setParameter('sqPerfil', $sqPerfil);

        return $queryBuilder->getQuery()->getArrayResult();

    }

    public function getAllByPerfil($perfil)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('r.txRota')
                    ->from($this->_entityName, 'pf')
                    ->innerJoin('pf.sqPerfil', 'p')
                    ->innerJoin('pf.sqFuncionalidade', 'f')
                    ->innerJoin('f.rotas', 'r')
                    ->where($queryBuilder->expr()->eq('p.sqPerfil', ':perfil'))
                    ->andWhere($queryBuilder->expr()->eq('f.stRegistroAtivo', ':ativo'))
                    ->setParameter('perfil', $perfil)
                    ->setParameter('ativo', 'TRUE')
                    ->groupBy('r.txRota');

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
