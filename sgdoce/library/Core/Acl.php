<?php
require_once 'Zend/Acl.php';

class Core_Acl extends Zend_Acl
{
    protected $classRole     = 'Zend_Acl_Role';

    protected $classResource = 'Zend_Acl_Resource';

    public function __construct(array $roles = array(), array $resources = array())
    {
        $this->createRoles($roles);
        $this->createResources($resources);
    }

    public function createRoles($roles)
    {
        foreach ($roles as $role) {
            $this->createRole($role);
        }

        return $this;
    }

    public function createRole($role)
    {
        $roleClass = $this->getClassRole();
        $objectRole =  new $roleClass($role);
        $this->addRole($objectRole);
    }

    public function createResource($resource)
    {
        $resourceClass  = $this->getClassResource();
        $objectResource =  new $resourceClass($resource);
        $this->addResource($objectResource);
    }

    public function createResources($resources)
    {
        foreach ($resources as $resource) {
            $this->createResource($resource);
        }

        return $this;
    }

    public function setClassRole($class)
    {
        if (!is_subclass_of($class, 'Zend_Acl_Role_Interface')) {
            throw new InvalidArgumentException('');
        }

        $this->classRole = $class;
        return $this;
    }

    public function getClassRole()
    {
        return $this->classRole;
    }

    public function setClassResource($class)
    {
        if (!is_subclass_of($class, 'Zend_Acl_Resource_Interface')) {
            throw new InvalidArgumentException('');
        }

        $this->classResource = $class;
        return $this;
    }

    public function getClassResource()
    {
        return $this->classResource;
    }

    public function allowAll()
    {
        $this->allow($this->getRoles(), $this->getResources());
    }
}
