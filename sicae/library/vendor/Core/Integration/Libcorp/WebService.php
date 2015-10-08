<?php

class Core_Integration_Libcorp_WebService extends Core_Integration_Abstract_Soap
{
    protected function _init()
    {
        $registry = Zend_Registry::get('configs');
        $this->_url = $registry['service']['libcorp']['endpoint'];
    }

}

