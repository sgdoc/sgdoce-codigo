<?php

class Core_Integration_Sica_WebService extends Core_Integration_Abstract_Soap
{
    protected function _init()
    {
        $registry = Zend_Registry::get('configs');
        $this->_url = $registry['service']['sica']['endpoint'];
    }

    public function getConfiguracao($noConstante)
    {
        $noConstante = strtoupper($noConstante);
        $result      = parent::getConfiguracao($noConstante);

        return $this->xmlToArray($result);
    }

}

