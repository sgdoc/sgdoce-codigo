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

namespace Principal\Service;

/**
 * SISICMBio
 *
 * Classe Service PerfilFuncionalidade
 *
 * @package      Principal
 * @subpackage   Services
 * @name         PerfilFuncionalidade
 * @version      1.0.0
 * @since        2012-09-10
 */
class PerfilFuncionalidade extends \Sica_Service_Crud
{

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:PerfilFuncionalidade';

    public function savePerfilFuncionalidade($perfilEntity, $dto)
    {
        $this->deletePerfilFuncionalidadeByPerfil($perfilEntity->getSqPerfil());

        $entityName = \Core_Util_Class::resolveNameEntity($this->_entityName, $this->getEntityManager());
        unset($dto['perfilPadraoExterno']);
        foreach ($dto as $object){
            $entity = new $entityName();
            $entity->setSqPerfil($perfilEntity);
            $funcionalidade = $object->getEntity();
            $this->getEntityManager()->getUnitOfWork()
            ->registerManaged($funcionalidade,
                            array('sqFuncionalidade' => $funcionalidade->getSqFuncionalidade()),
                            array());
            $funcionalidade->setStRegistroAtivo(NULL);
            $funcionalidade->setInFuncionalidadePrincipal(NULL);

            $entity->setSqFuncionalidade($funcionalidade);
            $entity->setStRegistroAtivo(TRUE);

            $this->getEntityManager()->persist($entity);
        }

    }

    public function deletePerfilFuncionalidadeByPerfil($perfil)
    {
        $this->_deletePerfilFuncionalidade(
            $this->getPerfilFuncionalidadeByPerfil($perfil)
        );
    }

    public function deletePerfilFuncionalidadeByFuncionalidade($funcionalidade)
    {
        $this->_deletePerfilFuncionalidade(
            $this->getPerfilFuncionalidadeByFuncionalidade($funcionalidade)
        );
    }

    public function getPerfilFuncionalidadeByPerfil($perfil)
    {
        return $this->_getRepository('app:PerfilFuncionalidade')->findBySqPerfil($perfil);
    }

    public function getPerfilFuncionalidadeByFuncionalidade($funcionalidade)
    {
        return $this->_getRepository('app:PerfilFuncionalidade')->findBySqFuncionalidade($funcionalidade);
    }

    public function menuAcessoById($sqPerfil)
    {
        return $this->_getRepository()->menuAcessoById($sqPerfil);
    }

    public function funcionalityByProfile($sqPerfil)
    {
        return $this->_getRepository()->funcionalityByProfile($sqPerfil);
    }

    private function _deletePerfilFuncionalidade($perfilFuncioalidadeCollection)
    {
        foreach($perfilFuncioalidadeCollection as $perfilFuncioalidade){
            $this->getEntityManager()->remove($perfilFuncioalidade);
            $this->getEntityManager()->flush();
        }
    }
}