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

use Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\EventManager;

class Core_Model_OWM_EntityManager implements ObjectManager
{
    private $metadataFactory;

    private $eventManager;

    private $configuration;

    private $handler;

    /**
     * The EntityRepository instances.
     *
     * @var array
     */
    private $repositories = array();

    protected function __construct($handler, Core_Model_OWM_Configuration $configuration, EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
        $this->handler      = $handler;
        $metadataFactoryClassName = $configuration->getClassMetadataFactoryName();
        $this->metadataFactory = new $metadataFactoryClassName;
        $this->metadataFactory->setEntityManager($this);
        $this->configuration   = $configuration;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function find($className, $id)
    {
    }

    public function persist($object)
    {
    }

    public function remove($object)
    {
    }

    public function merge($object)
    {
    }

    public function detach($object)
    {
    }

    public function flush()
    {
    }

    public function refresh($object)
    {
    }

    public function getRepository($entityName)
    {
        $entityName = ltrim($entityName, '\\');

        if (isset($this->repositories[$entityName])) {
            return $this->repositories[$entityName];
        }

        $metadata = $this->getClassMetadata($entityName);
        $repositoryClassName = $metadata->customRepositoryClassName;

        if ($repositoryClassName === null) {
            $repositoryClassName = $this->configuration->getDefaultRepositoryClassName();
        }

        $handler = clone $this->handler;
        if (NULL !== $metadata->configKey) {
            $options = $this->configuration->getConfig($metadata->configKey);
            if (count($options)) {
                $handler->setOptions($options);
            }
        }

        $repository = new $repositoryClassName($this, $metadata, $handler);
        $this->repositories[$entityName] = $repository;

        return $repository;
    }

    public function getClassMetadata($className)
    {
        return $this->metadataFactory->getMetadataFor($className);
    }

    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    public function initializeObject($obj)
    {
    }

    public function contains($object)
    {
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public static function create($handler, Core_Model_Owm_Configuration $configuration, EventManager $eventManager = NULL)
    {
        switch (true) {
            case is_array($handler):
                $context = stream_context_create(array('ssl' => array('verify_peer' => false,'allow_self_signed' => true)));
                $handler = new Zend_Soap_Client(NULL, $handler);
                $handler->setStreamContext($context);
                break;
            default:
                throw new InvalidArgumentException('');
        }

        if (NULL === $eventManager) {
            $eventManager = new EventManager();
        }

        return new Core_Model_OWM_EntityManager($handler, $configuration, $eventManager);
    }
}
