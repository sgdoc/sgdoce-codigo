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

use \Doctrine\ORM\AbstractQuery;

/**
 * SISICMBio
 *
 * Classe para Repository de Usuarios
 *
 * @package	ModelsRepository
 * @category    Repository
 * @name	VwUsuario
 */
class VwUsuario extends \Sgdoce\Model\Repository\Base
{
    public function isUserSgi (\Core_Dto_Search $dtoSearch)
    {
        $sql = "SELECT count(*) > 0 as is_user_sgi
                  FROM sicae.vw_perfil p
                 WHERE p.sq_perfil = :sqPerfil
                   AND p.sq_tipo_perfil = :sqTipoPerfil";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('is_user_sgi', 'isUserSgi');

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqTipoPerfil', \Core_Configuration::getSicaeTipoPerfilAdministrador());
        $query->setParameter('sqPerfil', $dtoSearch->getSqPerfil());

        return $query->useResultCache(TRUE, NULL, __METHOD__)
                     ->getSingleResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    public function isGestor (\Core_Dto_Search $dtoSearch)
    {
        $sql = "SELECT count(*) > 0 as is_gestor
                  FROM sicae.vw_perfil p
                 WHERE p.sq_perfil = :sqPerfil
                   AND p.sq_tipo_perfil = :sqTipoPerfil";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('is_gestor', 'isGestor');

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqTipoPerfil', \Core_Configuration::getSicaeTipoPerfilGestor());
        $query->setParameter('sqPerfil', $dtoSearch->getSqPerfil());

        return $query->useResultCache(TRUE, NULL, __METHOD__)
                     ->getSingleResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param \Core_Dto_Search $dto
     * Perfil do Usuário ex. SGI = \Core_Configuration::getSicaeTipoPerfilAdministrador()
     * @return QueryBuilder
     */
    public function listUsuarioPorPerfil ( $dto )
    {
        $sqPerfil = null;

        $sql = "SELECT DISTINCT
                       u.sq_usuario,
                       u.sq_pessoa,
                       u.st_ativo,
                       pe.no_pessoa,
                       p.no_perfil,
                       p.in_perfil_externo
                  FROM sicae.vw_usuario u
                  JOIN sicae.vw_usuario_perfil up USING(sq_usuario)
                  JOIN sicae.vw_perfil p USING(sq_perfil)
                  JOIN corporativo.vw_pessoa pe USING(sq_pessoa)
                 WHERE u.st_ativo = TRUE
                   AND p.sq_perfil = :sqPerfil
                 ORDER BY pe.no_pessoa";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('sq_usuario', 'sqUsuario');
        $rsm->addScalarResult('sq_pessoa', 'sqPessoa');
        $rsm->addScalarResult('st_ativo', 'stAtivo');
        $rsm->addScalarResult('no_pessoa', 'noPessoa');
        $rsm->addScalarResult('no_perfil', 'noPerfil');
        $rsm->addScalarResult('in_perfil_externo', 'inPerfilExterno');

        $nativeQuery = $this->_em->createNativeQuery($sql, $rsm);
        $nativeQuery->setParameter('sqPerfil', $dto->getSqPerfil());

        return $nativeQuery->useResultCache(TRUE, NULL, __METHOD__)
                           ->getArrayResult();
    }
}


