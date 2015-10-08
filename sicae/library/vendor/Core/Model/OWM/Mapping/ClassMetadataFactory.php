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

use Doctrine\Common\Persistence\Mapping\ReflectionService,
    Doctrine\Common\Persistence\Mapping\ClassMetadata as ClassMetadataInterface,
    Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory;

class Core_Model_OWM_Mapping_ClassMetadataFactory extends AbstractClassMetadataFactory
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var \Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
     */
    private $driver;

    public function setEntityManager($em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function doLoadMetadata($class, $parent, $rootEntityFound)
    {
        try {
            $this->driver->loadMetadataForClass($class->getName(), $class);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function newClassMetadataInstance($className)
    {
        return new Core_Model_OWM_Mapping_ClassMetadataInfo($className);
    }

    protected function initialize()
    {
        $this->driver = $this->em->getConfiguration()->getMetadataDriverImpl();
        $this->initialized = true;
    }

    /**
     * {@inheritDoc}
     */
    protected function wakeupReflection(ClassMetadataInterface $class, ReflectionService $reflService)
    {
        /* @var $class ClassMetadata */
        // @todo
        //$class->wakeupReflection($reflService);
    }

    /**
     * {@inheritDoc}
     */
    protected function initializeReflection(ClassMetadataInterface $class, ReflectionService $reflService)
    {
        /* @var $class ClassMetadata */
       $class->initializeReflection($reflService);
    }

    /**
     * {@inheritDoc}
     */
    protected function getFqcnFromAlias($namespaceAlias, $simpleClassName)
    {
        return $this->em->getConfiguration()->getEntityNamespace($namespaceAlias) . '\\' . $simpleClassName;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDriver()
    {
        return $this->driver;
    }
}
