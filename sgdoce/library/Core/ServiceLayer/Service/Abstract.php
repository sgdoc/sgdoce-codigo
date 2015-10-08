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
use Doctrine\ORM\Mapping\Entity,
    Bisna\Service\Service;
/**
 * @category   Service Layer
 * @package    Core
 * @subpackage ServiceLayer
 * @subpackage Service
 * @name       Abstract
 */
abstract class Core_ServiceLayer_Service_Abstract extends Service
{
    protected $_entityName;

    protected function initializeService()
    {
        $this->_initialiazeEntity();
    }

    /**
     * Inicializa nome da entidade de acordo com o valor da propriedade caso não esteja atribuído
     * através do método getServiceConfiguration()
     * Inicializa a mensageria de Service
     *
     * @return void)
     */
    protected function _initialiazeEntity()
    {
        if (!isset($this->options['entityName'])) {
            $this->options['entityName'] = $this->_entityName;
        }
    }

    /**
     * Return Doctrine EntityManager
     *
     * @param string $emName
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager($emName = null)
    {
        $containers = Core_Registry::getContainers();

        $emName = strtolower($emName);
        if (array_key_exists($emName, $containers)) {
            return $containers[$emName];
        }

        $dContainer = $this->locator->getDoctrineContainer();
        return $dContainer->getEntityManager($emName);
    }

    /**
     * @return Core_Messaging_Gateway
     */
    public function getMessaging()
    {
        return Core_Messaging_Manager::getGateway('Service');
    }

    /**
     * Proxy para getRepository() do entityManager()
     *
     * @param string $entityName
     */
    protected function _getRepository($entityName = null)
    {
        if (null === $entityName) {
            $entityName = $this->_getEntityName();
        }

        return $this->getEntityManager()->getRepository($entityName);
    }

    /**
     * Cria uma entitade com estado managed
     *
     * @deprecated
     * @param  array  $data
     * @param  string $entityName
     * @return Entity
     */
    protected function _createEntityManaged(array $data, $entityName = null)
    {
        if (null === $entityName) {
            $entityName = $this->_getEntityName();
        }

        $metadata = $this->getEntityManager()->getClassMetadata($entityName);
        $ids      = $metadata->getIdentifier();
        $valuesId = array();

        foreach ($data as $nameId => $valueId) {
            if (in_array($nameId, $ids)) {
                $valuesId[$nameId] = $valueId;
            }
        }

        $entity = $this->_newEntity($entityName);

        $unitOfWork = $this->getEntityManager()->getUnitOfWork();

        $entity = $unitOfWork->createEntity($entityName, $valuesId);
        if ($this->getEntityManager()->getUnitOfWork()->isInIdentityMap($entity)) {
            $unitOfWork->removeFromIdentityMap($entity);
        }

        return $entity;
    }

    /**
     * Cria uma entitade e seta o valor das propriedades de acordo com os métodos set*
     *
     * @deprecated
     * @param array       $data
     * @param string|null $entity
     */
    protected function _createEntityFromArray(array $data, $entity = null)
    {
        $entity = $this->_newEntity($entity);
        $entity->fromArray($data);
        return $entity;
    }

    /**
     * Cria uma entidade
     *
     * @param  string|null $entity
     * @return Entity
     */
    protected function _newEntity($entity = null)
    {
        if (null === $entity) {
            $entity = $this->_getEntityName();
        }

        $nameEntity = Core_Util_Class::resolveNameEntity($entity);
        return new $nameEntity();
    }

    /**
     * Verifica se o nome da entitade foi atribuída e retorna o nome
     *
     * @throws UnexpectedValueException
     * @return string
     */
    protected function _getEntityName()
    {
        if (!isset($this->options['entityName'])) {
            throw new UnexpectedValueException("Necessário setar o nome da entidade '{$this->options['entityName']}'");
        }

        return $this->options['entityName'];
    }
}
