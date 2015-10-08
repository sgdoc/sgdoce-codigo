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

use Sica\Model\Entity\Menu as MenuEntity;

/**
 * SISICMBio
 *
 * Classe Service Usuario
 *
 * @package      Principal
 * @subpackage   Services
 * @name         Menu
 * @version      1.0.0
 * @since        2012-07-25
 */
class Menu extends \Sica_Service_Crud
{

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:Menu';

    /**
     * Busca o menu de acordo como o perfil do Usuário
     * @param \Core_Dto_Entity $dto
     * @return array
     */
    public function userMenu(\Core_Dto_Entity $dto = NULL)
    {
        $userMenu = $this->_getRepository()->userMenu($dto);
        $menu = $this->mountMenuTwoLevels($userMenu);
        return $menu;
    }

    /**
     * Monta o menu seguindo a hierarquia para dois niveis (Sistemas Legado)
     * @param array $userMenu
     * @return array
     */
    public function mountMenuTwoLevels($userMenu)
    {
        $menu = array();
        $pai = NULL;
        $aux = -1;

        foreach ($userMenu as $key => $value) {

            # Menu primeiro nivel
            if ($pai !== $value['sqMenuPai']) {
                $pai = $value['sqMenuPai'];
                $aux++;

                $menu[$aux] = array(
                    'MenuPai' => array(
                        'sqMenu' => $value['sqMenuPai'],
                        'sqMenuPai' => '',
                        'noMenu' => $value['noMenuPai'],
                        'sqFuncionalidade' => $value['funcionalidadePai']
                    ),
                    'Acao' => $value['txRotaPai']
                );
            }

            # Menu segundo nivel
            if ($pai !== NULL && $value['sqMenuNivel2'] !== '') {
                $menu[$aux]['MenuFilho'][] = array(
                    'MenuFilho' => array(
                        'sqMenu' => $value['sqMenuNivel2'],
                        'sqMenuPai' => $value['sqMenuPai'],
                        'noMenu' => $value['menuNivel2'],
                        'sqFuncionalidade' => $value['funcionalidadeNivel2']
                    ),
                    'Acao' => $value['txRotaNivel2']
                );
            }

            # Menu Terceiro nivel
            if ($pai !== NULL && $value['sqMenuNivel2'] !== NULL && $value['sqMenuNivel3'] !== NULL) {
                $menu[$aux]['MenuNeto'][] = array(
                    'MenuNeto' => array(
                        'sqMenu' => $value['sqMenuNivel3'],
                        'sqMenuPai' => $value['sqMenuNivel2'],
                        'noMenu' => $value['menuNivel3'],
                        'sqFuncionalidade' => $value['funcionalidadeNivel3']
                    ),
                    'Acao' => $value['txRotaNivel3']
                );
            }

            # Menu Quarto nivel
            if ($pai !== NULL && $value['sqMenuNivel2'] !== NULL && $value['sqMenuNivel3'] !== NULL && $value['sqMenuNivel4'] !== NULL) {
                $menu[$aux]['MenuBisNeto'][] = array(
                    'MenuBisNeto' => array(
                        'sqMenu' => $value['sqMenuNivel4'],
                        'sqMenuPai' => $value['sqMenuNivel3'],
                        'noMenu' => $value['menuNivel4'],
                        'sqFuncionalidade' => $value['funcionalidadeNivel4']
                    ),
                    'Acao' => $value['txRotaNivel4']
                );
            }
        }

        return $menu;
    }

    public function delete($id)
    {
        $menuEntity = $this->find($id);
        if (false === ($menuEntity instanceof MenuEntity)) {
            throw new \Core_Exception_ServiceLayer("MN183");
        }
        $funcionalidadeCollection = $this->_getRepository('app:Funcionalidade')->findBySqMenu($menuEntity);
        if (count($funcionalidadeCollection) > 0) {
            throw new \Core_Exception_ServiceLayer("MN182");
        }
        $menuFilhoCollection = $this->_getRepository()->findBySqMenuPai($menuEntity);
        if (count($menuFilhoCollection) > 0) {
            throw new \Core_Exception_ServiceLayer("MN185");
        }
        $this->getEntityManager()->remove($menuEntity);
        $this->getEntityManager()->flush();
    }

    /**
     * (non-PHPdoc)
     * @see Core_ServiceLayer_Service_CrudDto::preSave()
     */
    public function preSave($entity, $dto = NULL)
    {
        $args = func_get_args();
        $this->_hasName($entity);

        $criteria = array(
            'sqSistema' => $entity->getSqSistema()->getSqSistema(),
            'sqMenuPai' => NULL,
            'nuOrdemApresent' => $args[2]['abaixoDe'] + 1,
            'sqMenu' => ($entity->getSqMenu() != '') ? $entity->getSqMenu() : NULL
        );

        if ($entity->getSqMenuPai()->getSqMenu() !== "") {
            $criteria['sqMenuPai'] = $entity->getSqMenuPai()->getSqMenu();
        }

        $this->getEntityManager()->beginTransaction();
        $this->_getRepository()->updateNuOrdemApresent($criteria);

        $nuOrdem = $args[2]['abaixoDe'] + 1;

        if ($entity->getSqMenuPai()->getSqMenu() === NULL || $entity->getSqMenuPai()->getSqMenu() === '') {
            $entity->setSqMenuPai(NULL);
        }

        $entity->setNuOrdemApresent($nuOrdem);
    }

    public function preInsert($entity, $dto = NULL)
    {
        $entity->setStRegistroAtivo(TRUE);
    }

    /**
     * (non-PHPdoc)
     * @see Core_ServiceLayer_Service_CrudDto::postSave()
     */
    public function postSave($entity, $dto = NULL)
    {
        $this->getEntityManager()->commit();
    }

    /**
     * Verifica existência de menu
     * @param \Sica\Model\Entity\Menu $entity
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    protected function _hasName($entity)
    {
        $has = $this->_getRepository()->hasName($entity);

        if ($has) {
            $this->getMessaging()->addErrorMessage('MN064');
            throw new \Core_Exception_ServiceLayer_Verification();
        }

        return NULL;
    }

    /**
     *
     * @param integer $sqMenu
     * @return string
     */
    public function switchStatus($sqMenu)
    {
        $entity = $this->_getRepository()->find($sqMenu);
        $status = ($entity->getStRegistroAtivo() === TRUE) ? FALSE : TRUE;
        $entity->setStRegistroAtivo($status);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        if ($status) {
            return 'MN051';
        }

        return 'MN049';
    }

    /**
     *
     * @param \Core_Dto_Mapping $dto
     */
    public function order(\Core_Dto_Mapping $dto)
    {
        $entity = $this->_getRepository()->find($dto->getSqMenu());

        if ($entity->getSqMenuPai() !== NULL) {
            $sqMenuPai = $entity->getSqMenuPai()->getSqMenu();
        } else {
            $sqMenuPai = NULL;
        }

        $criteria = array(
            'sqMenuPai' => $sqMenuPai,
            'sqSistema' => $dto->getSqSistema()
        );

        if ($dto->getDirecao() == 'up') {
            $criteria['nuOrdemApresent'] = $entity->getNuOrdemApresent() - 1;

            $entity->setNuOrdemApresent($entity->getNuOrdemApresent() - 1);

            $entity2 = current($this->_getRepository()->findBy($criteria));

            if ($entity2) {
                $entity2->setNuOrdemApresent($entity2->getNuOrdemApresent() + 1);
            }
        } else if ($dto->getDirecao() == 'down') {
            $criteria['nuOrdemApresent'] = $entity->getNuOrdemApresent() + 1;

            $entity->setNuOrdemApresent($entity->getNuOrdemApresent() + 1);

            $entity2 = current($this->_getRepository()->findBy($criteria));

            if ($entity2) {
                $entity2->setNuOrdemApresent($entity2->getNuOrdemApresent() - 1);
            }
        }

        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);

        if ($entity2) {
            $entityManager->persist($entity2);
        }

        $entityManager->flush();

        $menusDown = $this->_getRepository()->findMenuBySystemAndPai(
                $dto->getSqSistema()
                , NULL
                , ($entity->getSqMenuPai() !== NULL ? $entity->getSqMenuPai()->getSqMenu() : NULL));

        $indice = 1;
        if ($menusDown) {
            foreach ($menusDown as $menu) {
                $menu->setNuOrdemApresent($indice);
                $this->_getRepository()->updateNuOrdem($menu);
                $indice++;
            }
        }
    }

    public function findMenu(\Core_Dto_Entity $dto)
    {
        return $this->_getRepository()->findMenu($dto);
    }

}