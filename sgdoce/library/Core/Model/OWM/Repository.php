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

use Doctrine\Common\Persistence\ObjectRepository;

class Core_Model_OWM_Repository implements ObjectRepository
{
    /**
     * @var string
     */
    protected $_entityName;

    /**
     * @var EntityManager
     */
    protected $_em;

    /**
     */
    protected $_class;

    protected $_handler;

    /**
     * Initializes a new <tt>EntityRepository</tt>.
     *
     * @param EntityManager $em The EntityManager to use.
     * @param ClassMetadata $classMetadata The class descriptor.
     */
    public function __construct($em, Core_Model_OWM_Mapping_ClassMetadataInfo $class, $handler)
    {
        $this->_entityName = $class->name;
        $this->_em = $em;
        $this->_handler = $handler;
        $this->_class = $class;
    }

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param $id The identifier.
     * @return object The entity.
     */
    public function find($id)
    {
    }

    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll()
    {
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array The objects.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @return object
     */
    public function findOneBy(array $criteria)
    {
    }

    /**
     * Adds support for magic finders.
     *
     * @return array|object The found entity/entities.
     * @throws BadMethodCallException  If the method called is an invalid find* method
     *                                 or no find* method at all and therefore an invalid
     *                                 method call.
     */
    public function __call($method, $arguments)
    {
        return $this->_triggerMethods($method, $arguments);
    }

    protected function hasFunction($function, $functions)
    {
         foreach ($functions as $functionCmp) {
            if (strnatcasecmp($function, $functionCmp)) {
                return TRUE;
            }
         }

         return FALSE;
    }

    public function _triggerMethods($method, $args)
    {
        $methodPre  = 'pre'  . $method;
        $methodPost = 'post' . $method;

        $originalArgs = $args;
        if (method_exists($this, $methodPre)) {
            $args = $this->{$methodPre}($args);
        }

        $result = $this->_handler->__call($method, $args);

        if (method_exists($this, $methodPost)) {
            $result = $this->{$methodPost}($result, $args, $originalArgs);
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getEntityName()
    {
        return $this->_entityName;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->getEntityName();
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->_em;
    }

    /**
     * @return Mapping\ClassMetadata
     */
    protected function getClassMetadata()
    {
        return $this->_class;
    }
}
