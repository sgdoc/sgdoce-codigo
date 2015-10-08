<?php

class Core_Acl_AclSession
{
    private $acl;

    public function __construct(Zend_Acl $acl)
    {
        $this->acl = $acl;
    }

    public function isAllowed()
    {
        return call_user_func_array(array($this->acl, 'isAllowed'), func_get_args());
    }

    public function hasRole($role)
    {
        return call_user_func(array($this->acl, 'hasRole'), $role);
    }

    public function has($resource)
    {
        return call_user_func(array($this->acl, 'has'), $resource);
    }
}
