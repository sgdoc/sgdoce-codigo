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
class Core_ServiceLayer_Service_Crud extends Core_ServiceLayer_Service_Base
{
    protected $_data = array();

    protected $_entity = null;

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
            call_user_func($this->options[$event], $args);
        }
    }

    public function getNewEntity($entity = null)
    {
        return $this->_newEntity($entity);
    }

    /**
     * Deve ser implementado para as operações de criação e edição
     */
    public function setOperationalEntity($entityName = null)
    {

    }

    final protected function _populateManagers()
    {
        foreach ($this->_data as $attribute => $value) {
            /**
             * verifica se existe algum atributo tipo obj vindo do array
             * caso exista instancia a classe responsavel por esse objeto
             */
            if (strstr($attribute, 'obj')) {

                $attribute = substr($attribute, 3);
                $attrEntity = 'sq' . $attribute;

                $this->_data[$attrEntity] = $this->_createEntityManaged(array($attrEntity => $value), 'app:' . $attribute);
                unset($this->_data['obj' . $attribute]);
            }
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

        $this->getEntityManager()->flush();

        $msg = $this->deleteSuccessMessage();
        $this->getMessaging()->addSuccessMessage($msg);
        $this->getMessaging()->dispatchPackets();

        return TRUE;
    }

    public function deleteSuccessMessage()
    {
        return 'Exclusão realizada com sucesso.';
    }

    public function preDelete($id)
    {}

    public function postDelete($id)
    {}

    public function preInsert($service)
    {}

    public function postInsert($service)
    {}

    public function preUpdate($service)
    {}

    public function postUpdate($service)
    {}

    /**
     * Filtra os dados antes do processamento
     * @param array $data
     * @return array
     */
    public function filterSave($data)
    {
        return $data;
    }

    public function preSave($service)
    {}

    public function postSave($service)
    {}

    public function getEntityFromArray($data)
    {
        $this->_data = $data;
        $isUpdate = FALSE;
        $metadata = $this->getEntityManager()
                         ->getClassMetadata($this->_getEntityName());

        $ids      = $metadata->getIdentifier();
        $valuesId = array();
        $entity   = null;

        foreach ($ids as $id) {
            if (isset($data[$id]) && $data[$id]) {
                $valuesId[$id] = $data[$id];
            } else {
                $this->_data[$id] = null;
            }
        }

        if (count($ids) === count($valuesId)) {
            $entity = $this->find($valuesId);
        }

        $this->_populateManagers();
        $this->setOperationalEntity();

        if (!$entity) {
            $entity   = $this->_createEntityFromArray($this->_data);
        } else {
            $isUpdate = TRUE;
            $entity->fromArray($this->_data);
        }

         $this->_entity = $entity;

        return array(
                    'isUpdate' => $isUpdate,
                    'entity'   => $entity
        );
    }

    /**
     *
     * @param array $data
     * @return string mensagem de sucesso
     */
    public function save(array $data)
    {
        $data = $this->filterSave($data);

        $data = $this->getEntityFromArray($data);
        $this->_runCallEvent('preSave', $this);

        $isUpdate   = $data['isUpdate'];
        $entity     = $data['entity'];

        if (FALSE === $isUpdate) {
            $this->_runCallEvent('preInsert', $this);
        } else {
            $this->_runCallEvent('preUpdate', $this);
        }

        $this->getEntityManager()->persist($entity);
        $this->_runCallEvent('postSave', $this);

        if (FALSE === $isUpdate) {
            $this->_runCallEvent('postInsert', $this);
        } else {
            $this->_runCallEvent('postUpdate', $this);
        }

        $this->getEntityManager()->flush();

        $msg = $this->saveSuccessMessage($isUpdate);
        $this->getMessaging()->addSuccessMessage($msg);
        $this->getMessaging()->dispatchPackets();

        return $entity;
    }

    public function saveSuccessMessage($isUpdate)
    {
        if ($isUpdate) {
            return 'Alteração realizada com sucesso.';
        } else {
            return 'Inclusão realizada com sucesso.';
        }
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setData($value, $key = null)
    {
        if (null === $key) {
            $this->_data = (array) $value;
            return $this;
        }

        $this->_data[$key] = $value;
        return $this;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function getEntity()
    {
        return $this->_entity;
    }
}
