<?php

/**
 * Copyright 2012 do ICMBio
 *
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

/**
 * Concrete Service
 *
 * @category   Service Layer
 * @package    Core
 * @subpackage ServiceLayer
 * @subpackage Service
 * @name       Base
 */
class Core_ServiceLayer_Service_CrudDto extends Core_ServiceLayer_Service_Base
{

    protected function initializeService()
    {
        parent::initializeService();
        $this->_initializeEventsDefault();
    }

    final protected function _initializeEventsDefault()
    {
        $events = array(
            'preDelete',
            'postDelete',
            'preSave',
            'postSave',
            'preInsert',
            'postInsert',
            'preUpdate',
            'postUpdate'
        );
        foreach ($events as $event) {
            if (!isset($this->options[$event])) {
                $method = '_getCallback' . ucfirst($event);
                $this->options[$event] = $this->{$method}();
            }
        }
    }

    /**
     * @return array
     */
    protected function _getCallbackPreDelete()
    {
        return array($this, 'preDelete');
    }

    /**
     * @return array
     */
    protected function _getCallbackPostDelete()
    {
        return array($this, 'postDelete');
    }

    /**
     * @return array
     */
    protected function _getCallbackPreSave()
    {
        return array($this, 'preSave');
    }

    /**
     * @return array
     */
    protected function _getCallbackPostSave()
    {
        return array($this, 'postSave');
    }

    protected function _getCallbackPreInsert()
    {
        return array($this, 'preInsert');
    }

    /**
     * @return array
     */
    protected function _getCallbackPostInsert()
    {
        return array($this, 'postInsert');
    }

    /**
     * @return array
     */
    protected function _getCallbackPreUpdate()
    {
        return array($this, 'preUpdate');
    }

    /**
     * @return array
     */
    protected function _getCallbackPostUpdate()
    {
        return array($this, 'postUpdate');
    }

    final protected function _runCallEvent($event, $args)
    {
        if (isset($this->options[$event])) {
            if (!is_callable($this->options[$event])) {
                throw new RuntimeException("Evento '$event' inexistente");
            }

            if (!is_array($args)) {
                $args = array($args);
            }

            call_user_func_array($this->options[$event], $args);
        }
    }

    public function delete($id)
    {
        $entity = $this->find($id);

        if (!$entity) {
            return FALSE;
        }

        $this->_runCallEvent('preDelete', $id);
        $this->getEntityManager()->remove($entity);
        $this->_runCallEvent('postDelete', $id);

        return TRUE;
    }

    public function finish()
    {
        $this->getEntityManager()->flush();
    }

    public function preDelete($id)
    {

    }

    public function postDelete($id)
    {

    }

    public function preInsert($entity, $dto = NULL)
    {

    }

    public function postInsert($entity, $dto = NULL)
    {

    }

    public function preUpdate($entity, $dto = NULL)
    {

    }

    public function postUpdate($entity, $dto = NULL)
    {

    }

    public function preSave($entity, $dto = NULL)
    {

    }

    public function postSave($entity, $dto = NULL)
    {

    }

    /**
     * Filtra os dados antes do processamento
     *
     * @todo   deve aceitar DTO's e ser renomeado
     * @param  array $data
     * @return array
     */
    public function filterSave($data)
    {
        return $data;
    }

    /**
     * Chamada de eventos pre save
     *
     * @param  boolean $isUpdate
     * @return void
     */
    final protected function _triggerPreSave($isUpdate, $args)
    {
        $this->_runCallEvent('preSave', $args);

        if (FALSE === $isUpdate) {
            $this->_runCallEvent('preInsert', $args);
        } else {
            $this->_runCallEvent('preUpdate', $args);
        }
    }

    /**
     * Chamada de eventos post save
     *
     * @param  boolean $isUpdate
     * @param  object  $entity
     * @param  object  $dto
     * @return void
     */
    final protected function _triggerPostSave($isUpdate, $args)
    {
        $this->_runCallEvent('postSave', $args);

        if (FALSE === $isUpdate) {
            $this->_runCallEvent('postInsert', $args);
        } else {
            $this->_runCallEvent('postUpdate', $args);
        }
    }

    /**
     * @param array $data
     * @return string mensagem de sucesso
     */
    public function save(Core_Dto_Entity $dto)
    {
        $entity = clone $dto->getEntity();

        $entityName = Core_Util_Class::resolveNameEntity($this->_entityName, $this->getEntityManager());
        if (!$entity instanceof $entityName) {
            throw new InvalidArgumentException('Tipo do Dto inválido');
        }

        $isUpdate = FALSE;
        $metadata = $this->getEntityManager()->getClassMetadata(get_class($entity));

        $ids = $metadata->getIdentifierValues($entity);
        $uow = $this->getEntityManager()->getUnitOfWork();

        // @todo rever
        foreach ($metadata->associationMappings as $field => $prop) {
            $value = $metadata->reflFields[$field]->getValue($entity);
            if (is_object($value) && !$value instanceof \Doctrine\Common\Collections\Collection) {
                $metadataAssoc = $this->getEntityManager()
                        ->getClassMetadata(get_class($value));

                $idsFk = $metadataAssoc->getIdentifierValues($value);
                if ($idsFk) {
                    $uow->registerManaged($value, $idsFk, array());
                    $uow->removeFromIdentityMap($value);
                } else { // @todo
                    $uow->registerManaged($value, array(0), array());
                }
            }
        }

        $isUpdate = FALSE;
        if (count($ids)) {
            $this->getEntityManager()->getUnitOfWork()->clear($metadata->name);
            $originalEntity = $entity;
            $entity = $this->getEntityManager()
                           ->getRepository($this->_entityName)
                           ->findOneBy($ids);

            if (NULL !== $entity) {
                $isUpdate = TRUE;
                $entity->fromArray($originalEntity->toArray());
            } else {
                $entity = $originalEntity;
            }
        }

        $args = func_get_args();
        $args[0] = $entity;

        $this->_triggerPreSave($isUpdate, $args);
        $this->getEntityManager()->persist($entity);
        $this->_triggerPostSave($isUpdate, $args);

        return $entity;
    }

    public function getDto()
    {
        return $this->getEntityManager()->getPartialReference($this->_entityName, 0);
    }

    /**
     * Metodo generico para combo
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array The objects.
     * @return array
     */
    public function getComboDefault(array $criteria = array(), array $orderBy = NULL, $limit = NULL, $offset = NULL)
    {
        return $this->_getRepository()->getComboDefault($criteria, $orderBy, $limit, $offset);
    }

}
