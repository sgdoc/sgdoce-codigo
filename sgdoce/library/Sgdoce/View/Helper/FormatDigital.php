<?php

class Sgdoce_View_Helper_FormatDigital extends Zend_View_Helper_Abstract
{

    public function formatDigital($nuDigital, $nuAno)
    {
        return \Sgdoce_Util::montaDigital($nuDigital, $nuAno);
    }
}
