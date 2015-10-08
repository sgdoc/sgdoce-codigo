<?php
class Core_Paginator_Adapter_Doctrine implements Zend_Paginator_Adapter_Interface
{
    protected $paginator;

    public function __construct($query)
    {
        $this->paginator = new Doctrine\ORM\Tools\Pagination\Paginator($query);
    }

    public function getItems($offset, $itemCountPerPage)
    {
        $query = $this->paginator->getQuery();

        $query->setFirstResult($offset)
              ->setMaxResults($itemCountPerPage);

        return $this->paginator->getIterator();
    }

    public function count()
    {
        return $this->paginator->count();
    }
}
