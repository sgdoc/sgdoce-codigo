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

class Sica_Model_OLM_EntityManager implements ObjectManager
{

    private $metadataFactory;

    private $eventManager;

    private $configuration;

    private $persist;

    /**
     * The EntityRepository instances.
     *
     * @var array
     */
    private $repositories = array();

    protected function __construct($persist, Sica_Model_OLM_Configuration $configuration, EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
        $this->persist      = $persist;
        $metadataFactoryClassName = $configuration->getClassMetadataFactoryName();
        $this->metadataFactory = new $metadataFactoryClassName;
        $this->metadataFactory->setEntityManager($this);
        $this->configuration   = $configuration;
    }

    public function getPersist()
    {
        return $this->persist;
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
    }

    public function getClassMetadata($className)
    {
    }

    public function getMetadataFactory()
    {
    }

    public function initializeObject($obj)
    {
    }

    public function contains($object)
    {
    }

    public function search($filter)
    {
        return $this->getPersist()->search($filter);
    }

    public function bind($login, $password)
    {
        $acctname = $this->getPersist()->getCanonicalAccountName($login,
                        Zend_Ldap::ACCTNAME_FORM_DN);

        return $this->getPersist()->bind($acctname, $password);
    }

    public function update($boundUser, $userBind)
    {
        return $this->getPersist()->update($boundUser, $userBind);
    }

    public function getEntry($entry)
    {
        return $this->getPersist()->getEntry($entry);
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public static function create($handler, Sica_Model_OLM_Configuration $configuration, EventManager $eventManager = NULL)
    {
        switch (true) {
            case is_array($handler):
                $handler = new Zend_Ldap($handler);
                break;
            default:
                throw new InvalidArgumentException('');
        }

        if (NULL === $eventManager) {
            $eventManager = new EventManager();
        }

        return new Sica_Model_OLM_EntityManager($handler, $configuration, $eventManager);
    }
}
