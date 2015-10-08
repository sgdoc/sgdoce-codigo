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

use \Sica\Model\Entity\Perfil as PerfilEntity;

/**
 * SISICMBio
 *
 * Classe Service Usuario
 *
 * @package      Principal
 * @subpackage   Services
 * @name         Perfil
 * @version      1.0.0
 * @since         2012-08-17
 */
class Perfil extends \Sica_Service_Crud
{

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:Perfil';

    /**
     * (non-PHPdoc)
     * @see Core_ServiceLayer_Service_CrudDto::delete()
     */
    public function delete($id)
    {
        $perfilEntity = $this->find($id);
        if (false === ($perfilEntity instanceof PerfilEntity)) {
            throw new \Core_Exception_ServiceLayer("MN189");
        }
        $this->getEntityManager()->getConnection()->beginTransaction();

        $perfilFuncionalidadeService = $this->getServiceLocator()->getService('PerfilFuncionalidade');
        $perfilFuncionalidadeCollection = $perfilFuncionalidadeService->getPerfilFuncionalidadeByPerfil($perfilEntity);
        foreach($perfilFuncionalidadeCollection as $perfilFuncionalidadeEntity) {
            $this->getEntityManager()->remove( $perfilFuncionalidadeEntity );
            $this->getEntityManager()->flush();
        }

        $sistemaCollection = $this->_getRepository('app:Sistema')->findBySqPerfilExternoPadrao($perfilEntity);
        foreach($sistemaCollection as $sistemaEntity) {
            $sistemaEntity->setSqPerfilExternoPadrao(null);
            $this->getEntityManager()->persist( $sistemaEntity );
            $this->getEntityManager()->flush();
        }

        $usuarioPerfilService = $this->getServiceLocator()->getService('UsuarioPerfil');
        $countUsuario = $usuarioPerfilService->countUsuarioByPerfil($perfilEntity);
        if ($countUsuario > 0) {
            $this->getEntityManager()->getConnection()->rollback();
            throw new \Core_Exception_ServiceLayer("MN190");
        }

        $usuarioExternoPerfilService = $this->getServiceLocator()->getService('UsuarioExternoPerfil');
        $countUsuarioExterno = $usuarioExternoPerfilService->countUsuarioExternoByPerfil($perfilEntity);
        if ($countUsuarioExterno > 0) {
            $this->getEntityManager()->getConnection()->rollback();
            throw new \Core_Exception_ServiceLayer("MN191");
        }

        $this->getEntityManager()->remove($perfilEntity);
        $this->getEntityManager()->flush();

        $this->getEntityManager()->getConnection()->commit();
    }

    /**
     * Grid de perfil
     * @param \Core_Dto_Search $dtoSearch
     */
    public function listGrid(\Core_Dto_Search $dtoSearch)
    {
        $repository = $this->getEntityManager()->getRepository($this->_entityName);
        $result = $repository->searchPageDto('listGrid', $dtoSearch);

        return $result;
    }

    public function comboProfile(\Core_Dto_Mapping $dto, $inFuncionalidade = TRUE)
    {
        return $this->_getRepository()->comboProfile($dto, $inFuncionalidade);
    }

    /**
     * (non-PHPdoc)
     * @see Core_ServiceLayer_Service_CrudDto::preSave()
     */
    public function preSave($entity, $dto = NULL)
    {
        $this->_hasName($entity);
        $this->_validateHasFuncionality($dto);

        if ($entity->getInPerfilExterno() == '1') {
            $entity->setInPerfilExterno(TRUE);
            $this->_checkExistsExternalProfileDefault($entity, $dto);
        } else if ($entity->getInPerfilExterno() == '0') {
            $entity->setInPerfilExterno(FALSE);
        }

        $this->getEntityManager()->getConnection()->beginTransaction();
    }

    public function preUpdate($entity, $dto = NULL)
    {
        $this->_hasName($entity);
    }

    public function preInsert($entity, $dto = NULL)
    {
        $entity->setStRegistroAtivo(TRUE);
    }

    public function postSave($entity, $dto = NULL)
    {
        $this->getServiceLocator()->getService('PerfilFuncionalidade')->savePerfilFuncionalidade($entity, $dto);
        $entitySistema = $this->_getRepository('app:Sistema')->find($entity->getSqSistema()->getSqSistema());

        if (isset($dto['perfilPadraoExterno']) && $dto['perfilPadraoExterno']->getPerfilPadraoExterno() == '1') {
            $entitySistema->setSqPerfilExternoPadrao($entity);
        } else {
            if ($entity->getSqPerfil() == $entitySistema->getSqPerfilExternoPadrao()->getSqPerfil()) {
                $entitySistema->setSqPerfilExternoPadrao(NULL);
            }

            if ($entitySistema->getSqPerfilExternoPadrao()->getSqPerfil() === NULL) {
                $entitySistema->setSqPerfilExternoPadrao(NULL);
            }
        }

        $this->getEntityManager()->persist($entitySistema);
        $this->getEntityManager()->flush();

        $this->getEntityManager()->getConnection()->commit();
    }

    /**
     * Verifica existência de perfil, pois não pode existir um perfil com o mesmo nome
     * dentro de um mesmo sistema
     * @param \Sica\Model\Entity\Perfil $entity
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    protected function _hasName($entity)
    {
        $has = $this->_getRepository()->hasName($entity);
        if (count($has)) {
            $this->getMessaging()->addErrorMessage('MN060');
            throw new \Core_Exception_ServiceLayer_Verification();
        }
    }

    protected function _validateHasFuncionality($dto)
    {
        unset($dto['perfilPadraoExterno']);
        if (!count($dto)) {
            $this->getMessaging()->addErrorMessage('MN063');
            throw new \Core_Exception_ServiceLayer_Verification();
        }
    }

    protected function _checkExistsExternalProfileDefault($entity, $dto)
    {

        $has = $this->_getRepository()->checkExistsExternalProfileDefault($entity);

        if (NULL !== $has && $dto['perfilPadraoExterno']->getPerfilPadraoExterno() == '1') {
            $this->getMessaging()->addErrorMessage('MN068');
            throw new \Core_Exception_ServiceLayer_Verification();
        }
    }

    public function view(\Core_Dto_Entity $dto)
    {
        return $this->_getRepository()->viewPerfil($dto);
    }

    public function findProfilesFull(\Core_Dto_Search $dto, $relatorio = FALSE)
    {
        $data = $this->_getRepository()->findProfilesFull($dto);

        if ($relatorio) {
            $data = $this->_prepareToPdf($data);
        }

        return $data;
    }

    protected function _prepareToPdf($data)
    {
        $noSistema = '';
        $noPerfil = '';
        $noMenu = '';
        $dataPdf = array();
        foreach ($data as $key => $value) {
            if ($noSistema != $value['noSistema']) {
                $noSistema = $value['noSistema'];

                $dataPdf[$noSistema] = array(
                    'noSistema' => $value['noSistema'],
                    'sgSistema' => $value['sgSistema']
                );
                $noPerfil = '';
                $noMenu = '';
            }

            if ($noPerfil != $value['noPerfil']) {
                $noPerfil = $value['noPerfil'];

                $dataPdf[$noSistema]['perfil'][$noPerfil] = array(
                    'noPerfil' => $value['noPerfil'],
                    'inPerfilExterno' => $value['inPerfilExterno'],
                    'stRegistroAtivo' => $value['stRegistroAtivo']
                );

                $noMenu = '';
            }

            if ($noMenu != $value['noMenu']) {
                $noMenu = $value['noMenu'];

                $dataPdf[$noSistema]['perfil'][$noPerfil]['menu'][$noMenu] = array(
                    'noMenu' => $value['noMenu']
                );
            }

            $dataPdf[$noSistema]['perfil'][$noPerfil]['menu'][$noMenu]['funcionalidade'][] = $value['noFuncionalidade'];
        }

        return $dataPdf;
    }

    /**
     * Verifica se existe algum usuário atrelado ao perfil
     * @param \Core_Dto_Entity $dto
     * @return array
     */
    public function checkUserExistsProfile(\Core_Dto_Entity $dto)
    {
        $countUser = $this->_getRepository()->existsUserInProfile($dto);

        if ($countUser > 0) {
            return array('users' => $countUser);
        }

        return array();
    }

}