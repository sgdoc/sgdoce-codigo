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

use Sica\Model\Entity\Funcionalidade as FunionalidadeEntity;

/**
 * SISICMBio
 *
 * Classe Service Usuario
 *
 * @package      Principal
 * @subpackage   Services
 * @name         Funcionalidade
 * @version      1.0.0
*/
class Funcionalidade extends \Sica_Service_Crud
{

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:Funcionalidade';

    public function delete($id)
    {
        $funcionalidadeEntity = $this->find($id);
        if (false === ($funcionalidadeEntity instanceof FunionalidadeEntity)) {
            throw new \Core_Exception_ServiceLayer("MN177");
        }
        $this->getEntityManager()->getConnection()->beginTransaction();

        $rotaPrincipalEntity = $funcionalidadeEntity->getSqRotaPrincipal();
        $funcionalidadeEntity->setSqRotaPrincipal(null);

        $rotaCollection = $this->_getRepository('app:Rota')->findBySqFuncionalidade($funcionalidadeEntity);
        foreach($rotaCollection as $rotaEntity) {
            $this->getEntityManager()->remove($rotaEntity);
            $this->getEntityManager()->flush();
        }

        $perfilFuncionalidadeService = $this->getServiceLocator()->getService('PerfilFuncionalidade');
        $perfilFuncionalidadeCollection = $perfilFuncionalidadeService->getPerfilFuncionalidadeByFuncionalidade($funcionalidadeEntity);
        if (count($perfilFuncionalidadeCollection) > 0) {
            $this->getEntityManager()->getConnection()->rollback();
            throw new \Core_Exception_ServiceLayer("MN178");
        }

        $this->getEntityManager()->remove($funcionalidadeEntity);
        $this->getEntityManager()->flush();

        $this->getEntityManager()->getConnection()->commit();
    }

    public function preSave($entity, $dto = NULL)
    {
        $repository = $this->_getRepository();
        if ($repository->validateName($entity)) {
            $this->getMessaging()->addErrorMessage('MN066');
            throw new \Core_Exception_ServiceLayer_Verification();
        }

        if ($entity->getInFuncionalidadePrincipal()) {
            if ($repository->validatePrincipal($entity)) {
                $this->getMessaging()->addErrorMessage('MN065');
                throw new \Core_Exception_ServiceLayer_Verification();
            }
        }

        if (!$entity->getSqRotaPrincipal()->getSqRota()){
            $entity->setSqRotaPrincipal(null);
        }
        $this->getEntityManager()->getConnection()->beginTransaction();
    }

    public function postSave($entity, $dto = NULL)
    {
        $this->_getRepository('app:Rota')->save($entity, $dto);
        $this->getEntityManager()->getConnection()->commit();
    }

    public function findFuncionalities($dtoSearch)
    {
        return $this->_getRepository()->findFuncionalities($dtoSearch);
    }

    public function menuFuncionality(\Core_Dto_Entity $dto, $perfil = NULL)
    {
        $menu = $this->_getRepository()->menuFuncionality($dto);

        if (NULL !== $perfil) {
            $menuArray = $menu;

            foreach ($menuArray as $key => $m) {
                $mPerfil = $this->_getRepository('app:PerfilFuncionalidade')->menuAcessoByIdAndMenu($m['sqMenu'],$perfil);

                foreach($mPerfil as $f){
                    if($f['sqFuncionalidade'] == $m['sqFuncionalidade']){
                        $m["checked"] = "checked";
                        $menu[$key] = $m;
                    }
                }
            }
        }

        return $menu;
    }

    public function getAllByPerfil($perfil)
    {
        $rotas = $this->_getRepository('app:PerfilFuncionalidade')->getAllByPerfil($perfil);
        $data = array();
        $filter = new \Zend_Filter_StringTrim();
        $filter->setCharList('/');
        foreach ($rotas as $rota) {
            $data[] = $filter->filter($rota['txRota']);
        }

        return $data;
    }

    public function preInsert($entity, $dto = NULL)
    {
        $entity->setStRegistroAtivo(TRUE);
    }

}
