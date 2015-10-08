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
 * @name	 UsuarioPerfil
 * @version	 1.0.0
 */

class UsuarioPerfil extends \Sica_Model_Repository
{
    public function findSystensByUser($sqUsuario)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $repository = 'app:UsuarioPerfil';
        $column = 'up.sqUsuario';
        $inPerfilExterno = 'FALSE';

        if(\Core_Integration_Sica_User::getUserProfileExternal()){
            $repository = 'app:UsuarioExternoPerfil';
            $column = 'up.sqUsuarioExterno';
            $inPerfilExterno = 'TRUE';
        }

        $queryBuilder->select('s.sqSistema',
                        's.noSistema',
                        's.sgSistema',
                        's.txUrl',
                        's.txUrlHelp',
                        's.txEnderecoImagem',
                        's.txDescricao',
                        'l.sqLeiaute')
                        ->from($repository, 'up')
                        ->innerJoin('up.sqPerfil','p')
                        ->innerJoin('p.sqSistema','s')
                        ->innerJoin('s.sqLeiaute', 'l')
                        ->where($column . ' = :usuario')
                        ->setParameter('usuario', $sqUsuario)
                        ->andWhere('s.stRegistroAtivo = :stRegistro')
                        ->setParameter('stRegistro', 'TRUE')
                        ->andWhere($queryBuilder->expr()->eq('p.inPerfilExterno',':inPerfilExterno'))
                                    ->setParameter('inPerfilExterno', $inPerfilExterno)
                        ->andWhere($queryBuilder->expr()->eq('p.stRegistroAtivo',':stRegistroAtivo'))
                                    ->setParameter('stRegistroAtivo', TRUE)
                        ->groupBy('s.sqSistema, l.sqLeiaute')
                        ->orderBy('s.sgSistema, s.noSistema');

        return $queryBuilder->getQuery()->execute();
    }

    public function userUnit(\Core_Dto_Mapping $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $repository = 'app:UsuarioPerfil';
        $column = 'up.sqUsuario';
        $columns = 'vwp.sqPessoa, vwuo.sgUnidadeOrg, vwp.noPessoa, p.sqPerfil';
        $group = 'p.sqPerfil, vwp.sqPessoa, vwuo.sgUnidadeOrg, vwp.noPessoa';
        $inPerfilExterno = 'FALSE';

        if(\Core_Integration_Sica_User::getUserProfileExternal()){
            $repository = 'app:UsuarioExternoPerfil';
            $column = 'up.sqUsuarioExterno';
            $columns = 'p.sqPerfil, p.noPerfil, p.sqPerfil sqPessoa';
            $group = 'p.sqPerfil, p.noPerfil';
            $inPerfilExterno = 'TRUE';
        }

        $queryBuilder->select($columns)
                        ->from($repository,'up')
                        ->innerJoin('up.sqPerfil','p');

        if(!\Core_Integration_Sica_User::getUserProfileExternal()){
            $queryBuilder->innerJoin('up.sqUnidadeOrgPessoa','vwuo')
                            ->innerJoin('vwuo.sqUnidadeOrgPessoa','vwp')
                            ->orderBy('vwuo.sgUnidadeOrg', 'ASC');
        }

        $queryBuilder->where($column . ' = :sqUsuario')
                        ->setParameter('sqUsuario',$dto->getSqUsuario())
                        ->andWhere('p.sqSistema = :sqSistema')
                        ->setParameter('sqSistema',$dto->getSqSistema())
                        ->andWhere($queryBuilder->expr()->eq('p.inPerfilExterno',':inPerfilExterno'))
                                    ->setParameter('inPerfilExterno', $inPerfilExterno)
                        ->andWhere($queryBuilder->expr()->eq('p.stRegistroAtivo',':stRegistroAtivo'))
                                    ->setParameter('stRegistroAtivo', TRUE)
                        ->groupBy($group)
                        ;

        return $queryBuilder->getQuery()->getResult();
    }

    public function unitProfile(\Core_Dto_Mapping $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $repository = 'app:UsuarioPerfil';
        $inPerfilExterno = 'FALSE';
        $column = 'up.sqUsuario';

        if(\Core_Integration_Sica_User::getUserProfileExternal()){
            $repository = 'app:UsuarioExternoPerfil';
            $column = 'up.sqUsuarioExterno';
            $inPerfilExterno = 'TRUE';
        }

        $queryBuilder->select('up')
                    ->from($repository,'up')
                    ->innerJoin('up.sqPerfil','p');

        if(!\Core_Integration_Sica_User::getUserProfileExternal()){
            $queryBuilder->where('up.sqUnidadeOrgPessoa = :sqUnidadeOrg')
                    ->setParameter('sqUnidadeOrg',$dto->getSqUnidadeOrgPessoa());
        }

        $queryBuilder->andWhere($column . ' = :sqUsuario')
                    ->setParameter('sqUsuario',$dto->getSqUsuario())
                    ->andWhere('p.sqSistema = :sqSistema')
                    ->setParameter('sqSistema',$dto->getSqSistema())
                    ->andWhere($queryBuilder->expr()->eq('p.inPerfilExterno',':inPerfilExterno'))
                    ->setParameter('inPerfilExterno', $inPerfilExterno);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findProfileByUnit(\Core_Dto_Mapping $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('p.sqPerfil',
                        'p.noPerfil')
                        ->from('app:UsuarioPerfil','up')
                        ->innerJoin('up.sqPerfil','p')
                        ->innerJoin('up.sqUsuario','u')
                        ->where('up.sqUnidadeOrgPessoa = :sqUnidadeOrg')
                            ->setParameter('sqUnidadeOrg', $dto->getSqUnidadeOrgPessoa())
                        ->andWhere('p.sqSistema = :sqSistema')
                            ->setParameter('sqSistema', $dto->getSqSistema())
                        ->andWhere($queryBuilder->expr()->eq('p.inPerfilExterno',':inPerfilExterno'))
                            ->setParameter('inPerfilExterno', 'FALSE')
                        ->andWhere($queryBuilder->expr()->eq('u.sqUsuario', ':sqUsuario'))
                            ->setParameter('sqUsuario', $dto->getSqUsuario())
                        ->andWhere($queryBuilder->expr()->eq('p.stRegistroAtivo', ':stRegistroAtivo'))
                            ->setParameter('stRegistroAtivo', TRUE)
                        ->groupBy('p.sqPerfil, p.noPerfil');

        return $queryBuilder->getQuery()->getResult();
    }

    public function getProfilesByUnit(\Core_Dto_Mapping $mapping)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('p.sqPerfil')
                    ->from($this->_entityName, 'up')
                    ->innerJoin('up.sqPerfil', 'p')
                    ->where('up.sqUnidadeOrgPessoa = :sqUnidadeOrg')
                    ->andWhere('up.sqUsuario = :usuario')
                    ->andWhere($queryBuilder->expr()->eq('p.inPerfilExterno', 'FALSE'))
                    ->setParameter('sqUnidadeOrg', $mapping->getUnidade())
                    ->setParameter('usuario', $mapping->getUsuario());

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function countPerfil(\Core_Dto_Mapping $mapping)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('COUNT(up.sqPerfil)')
                    ->from($this->_entityName, 'up')
                    ->where($queryBuilder->expr()->eq('up.sqUsuario', ':usuario'))
                    ->setParameter('usuario', $mapping->getUsuario());

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
