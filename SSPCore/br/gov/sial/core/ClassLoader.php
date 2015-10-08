<?php
namespace br\gov\sial\core;

/**
 * SplClassLoader implementation that implements the technical interoperability
 * standards for PHP 5.3 namespaces and class names.
 *
 * http://groups.google.com/group/php-standards/web/final-proposal
 *
 *     // Example which loads classes for the Doctrine Common package in the
 *     // Doctrine\Common namespace.
 *     $classLoader = new SplClassLoader('Doctrine\Common', '/path/to/doctrine');
 *     $classLoader->register();
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Roman S. Borschel <roman@code-factory.org>
 * @author Matthew Weier O'Phinney <matthew@zend.com>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Fabien Potencier <fabien.potencier@symfony-project.org>
 * */
class ClassLoader
{
    private $_fileExtension = '.php';
    private $_namespace;
    private $_includePath;
    private $_namespaceSeparator = '\\';

    /**
     * Creates a new <tt>ClassLoader</tt> that loads classes of the
     * specified namespace.
     *
     * @param string
     */
    public function __construct ($namespace = NULL, $includePath = NULL)
    {
        $this->_namespace = $namespace;
        $this->_includePath = $includePath;
    }

    /**
     * Sets the namespace separator used by classes in the namespace of this class loader.
     *
     * @param string
     */
    public function setNamespaceSeparator ($sep)
    {
        $this->_namespaceSeparator = $sep;
    }

    /**
     * Gets the namespace seperator used by classes in the namespace of this class loader.
     */
    public function getNamespaceSeparator ()
    {
        return $this->_namespaceSeparator;
    }

    /**
     * Sets the base include path for all class files in the namespace of this class loader.
     *
     * @param string
     */
    public function setIncludePath ($includePath)
    {
        $this->_includePath = $includePath;
    }

    /**
     * Gets the base include path for all class files in the namespace of this class loader.
     *
     * @return string
     */
    public function getIncludePath ()
    {
        return $this->_includePath;
    }

    /**
     * Sets the file extension of class files in the namespace of this class loader.
     *
     * @param string
     */
    public function setFileExtension ($fileExtension)
    {
        $this->_fileExtension = $fileExtension;
    }

    /**
     * Gets the file extension of class files in the namespace of this class loader.
     *
     * @return string
     */
    public function getFileExtension ()
    {
        return $this->_fileExtension;
    }

    /**
     * Installs this class loader on the SPL autoload stack.
     */
    public function register ()
    {
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->_includePath);
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     */
    public function unregister ()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param string
     * @throws Exception
     */
    public function loadClass ($className)
    {
        $hasClass = FALSE;
        $class = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $className)
                  . $this->_fileExtension;

        array_map(function ($path) use($class) {
            $file = $path . DIRECTORY_SEPARATOR . $class;

               if (is_file($file)) {
                    require_once $file;
                    $hasClass = TRUE;
                    return;
               }
        }, array_reverse(explode(PATH_SEPARATOR, get_include_path())));

        if ($hasClass) {
            throw new \Exception("class {$class} not found.");
        }
    }

    /**
     * @author Doctrine
     * @param string $className
     * @return boolean
     * */
    public function canLoadClass ($className)
    {
        return file_exists(($this->_includePath !== NULL ? $this->_includePath . DIRECTORY_SEPARATOR : '')
               . str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $className)
               . $this->_fileExtension);
    }

    /**
     * fabrica de classLoader
     *
     * @param string $namespace
     * @param string $includePath
     * @return ClassLoader
     * */
    public static function factory ($namespace = NULL, $includePath = NULL)
    {
        return new self($namespace, $includePath);
    }
}
