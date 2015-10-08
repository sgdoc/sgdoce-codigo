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
 * @see Zend_Loader_Autoloader_Resource
 */
/**
 * @package     Core
 * @subpackage  Loader
 * @subpackage  Autoloader
 * @name        Resource
 * @category    Resource
 */
class Core_Loader_Autoloader_Resource extends Zend_Loader_Autoloader_Resource
{
    protected $_namespaceSeparator = '\\';

    /**
     * @inheritdoc
     */
    public function __construct($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (!is_array($options)) {
            require_once 'Zend/Loader/Exception.php';
            throw new Zend_Loader_Exception('Options must be passed to resource loader constructor');
        }

        $this->setOptions($options);

        $namespace = $this->getNamespace();
        if ((null === $namespace)
            || (null === $this->getBasePath())
        ) {
            require_once 'Zend/Loader/Exception.php';
            throw new Zend_Loader_Exception('Resource loader requires both a namespace and a base path for initialization');
        }

        if (!empty($namespace)) {
            $namespace .= $this->getNamespaceSeparator();
        }
        require_once 'Zend/Loader/Autoloader.php';
        Zend_Loader_Autoloader::getInstance()->unshiftAutoloader($this, $namespace);
    }

    /**
     * @inheritdoc
     */
    public function getClassPath($class)
    {
        $segments          = explode($this->getNamespaceSeparator(), $class);
        $namespaceTopLevel = $this->getNamespace();
        $namespace         = '';

        if (!empty($namespaceTopLevel)) {
            $namespace = array();
            $topLevelSegments = count(explode($this->getNamespaceSeparator(), $namespaceTopLevel));
            for ($i = 0; $i < $topLevelSegments; $i++) {
                $namespace[] = array_shift($segments);
            }
            $namespace = implode($this->getNamespaceSeparator(), $namespace);
            if ($namespace != $namespaceTopLevel) {
                // wrong prefix? we're done
                return false;
            }
        }

        if (count($segments) < 2) {
            // assumes all resources have a component and class name, minimum
            return false;
        }

        $final     = array_pop($segments);
        $component = $namespace;
        $lastMatch = false;
        do {
            $segment    = array_shift($segments);
            $component .= empty($component) ? $segment : $this->getNamespaceSeparator() . $segment;
            if (isset($this->_components[$component])) {
                $lastMatch = $component;
            }
        } while (count($segments));

        if (!$lastMatch) {
            return false;
        }

        $final = substr($class, strlen($lastMatch) + 1);
        $path = $this->_components[$lastMatch];
        $classPath = $path . '/' . str_replace($this->getNamespaceSeparator(), '/', $final) . '.php';

        if (Zend_Loader::isReadable($classPath)) {
            return $classPath;
        }

        return false;
    }

    public function setNamespaceSeparator($separator)
    {
        $this->_namespaceSeparator = (string) $separator;
        return $this;
    }

    public function getNamespaceSeparator()
    {
        return $this->_namespaceSeparator;
    }

    /**
     * @inheritdoc
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = rtrim((string) $namespace, $this->getNamespaceSeparator());
        return $this;
    }

    /**
     * @inheridoc
     */
    public function addResourceType($type, $path, $namespace = null)
    {
        $type = strtolower($type);
        if (!isset($this->_resourceTypes[$type])) {
            if (null === $namespace) {
                require_once 'Zend/Loader/Exception.php';
                throw new Zend_Loader_Exception('Initial definition of a resource type must include a namespace');
            }
            $namespaceTopLevel = $this->getNamespace();
            $namespace = ucfirst(trim($namespace, $this->getNamespaceSeparator()));
            $this->_resourceTypes[$type] = array(
                'namespace' => empty($namespaceTopLevel) ? $namespace : $namespaceTopLevel . $this->getNamespaceSeparator() . $namespace,
            );
        }
        if (!is_string($path)) {
            require_once 'Zend/Loader/Exception.php';
            throw new Zend_Loader_Exception('Invalid path specification provided; must be string');
        }
        $this->_resourceTypes[$type]['path'] = $this->getBasePath() . '/' . rtrim($path, '\/');

        $component = $this->_resourceTypes[$type]['namespace'];
        $this->_components[$component] = $this->_resourceTypes[$type]['path'];
        return $this;
    }

    /**
     * @inheridoc
     */
    public function load($resource, $type = null)
    {
        if (null === $type) {
            $type = $this->getDefaultResourceType();
            if (empty($type)) {
                require_once 'Zend/Loader/Exception.php';
                throw new Zend_Loader_Exception('No resource type specified');
            }
        }
        if (!$this->hasResourceType($type)) {
            require_once 'Zend/Loader/Exception.php';
            throw new Zend_Loader_Exception('Invalid resource type specified');
        }
        $namespace = $this->_resourceTypes[$type]['namespace'];
        $class     = $namespace . $this->getNamespaceSeparator() . ucfirst($resource);
        if (!isset($this->_resources[$class])) {
            $this->_resources[$class] = new $class;
        }
        return $this->_resources[$class];
    }
}

