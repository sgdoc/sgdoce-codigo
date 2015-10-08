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
class Core_Model_OWM_Configuration
{
    protected $_attributes = array();

    /**
     * Set a class metadata factory.
     *
     * @param string $cmf
     */
    public function setClassMetadataFactoryName($cmfName)
    {
        $this->_attributes['classMetadataFactoryName'] = $cmfName;
    }

    /**
     * @return string
     */
    public function getClassMetadataFactoryName()
    {
        if (!isset($this->_attributes['classMetadataFactoryName'])) {
            $this->_attributes['classMetadataFactoryName'] = 'Core_Model_OWM_Mapping_ClassMetadataFactory';
        }
        return $this->_attributes['classMetadataFactoryName'];
    }

    /**
     * Set default repository class.
     *
     * @since 2.2
     * @param string $className
     * @throws ORMException If not is a \Doctrine\ORM\EntityRepository
     */
    public function setDefaultRepositoryClassName($className)
    {
        if ($className != "Core_Model_OWM_Repository" &&
           !is_subclass_of($className, 'Core_Model_OWM_Repository')){
            throw new InvalidArgumentException();
        }
        $this->_attributes['defaultRepositoryClassName'] = $className;
    }

    /**
     * Get default repository class.
     *
     * @return string
     */
    public function getDefaultRepositoryClassName()
    {
        return isset($this->_attributes['defaultRepositoryClassName']) ?
                $this->_attributes['defaultRepositoryClassName'] : 'Core_Model_OWM_Repository';
    }

    public function setMetadataDriverImpl($driver)
    {
        $this->_attributes['medataDriverImpl'] = $driver;
    }

    public function getMetadataDriverImpl()
    {
        return isset($this->_attributes['medataDriverImpl'])
               ? $this->_attributes['medataDriverImpl']
               : NULL;
    }

    public function setDefaultMetadataDriverImpl(array $paths)
    {
        $this->setMetadataDriverImpl($this->newDefaultAnnotationDriver($paths));
    }

    public function newDefaultAnnotationDriver($paths = array())
    {
        \Doctrine\Common\Annotations\AnnotationRegistry::registerFile(__DIR__ . '/Mapping/Driver/DoctrineAnnotations.php');

        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $reader = new \Doctrine\Common\Annotations\CachedReader($reader, new \Doctrine\Common\Cache\ArrayCache());

        return new Core_Model_OWM_Mapping_Driver_AnnotationDriver($reader, (array) $paths);
    }

    /**
     * Set the entity alias map
     *
     * @param array $entityAliasMap
     * @return void
     */
    public function setEntityNamespaces(array $entityNamespaces)
    {
        $this->_attributes['entityNamespaces'] = $entityNamespaces;
    }

    /**
     * Retrieves the list of registered entity namespace aliases.
     *
     * @return array
     */
    public function getEntityNamespaces()
    {
        return $this->_attributes['entityNamespaces'];
    }

    /**
     * Resolves a registered namespace alias to the full namespace.
     *
     * @param string $entityNamespaceAlias
     * @return string
     * @throws MappingException
     */
    public function getEntityNamespace($entityNamespaceAlias)
    {
        if ( ! isset($this->_attributes['entityNamespaces'][$entityNamespaceAlias])) {
            throw new InvalidArgumentException("Alias invalid '$entityNamespaceAlias'");
        }

        return trim($this->_attributes['entityNamespaces'][$entityNamespaceAlias], '\\');
    }

    public function setConfigs(array $configs)
    {
        foreach ($configs as $key => $config) {
            $this->setConfig($key, $config);
        }
    }

    public function setConfig($key, array $configs)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('');
        }

        $this->_attributes['configs'][strtolower($key)] = $configs;
    }

    public function getConfig($key)
    {
        $key = strtolower($key);

        if (!isset($this->_attributes['configs'])) {
            return array();
        }

        if (!isset($this->_attributes['configs'][$key])) {
            return array();
        }

        return (array) $this->_attributes['configs'][$key];
    }
}

