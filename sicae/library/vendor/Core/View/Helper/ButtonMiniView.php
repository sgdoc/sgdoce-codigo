<?php
class Core_View_Helper_ButtonMiniView extends Core_View_Helper_ButtonMiniAbstract
{
    protected $defaultAttribs = array(
        'title' => 'Visualizar',
    );

    protected $icon = 'icon-eye-open';

    public function buttonMiniView(array $attribs)
    {
        return $this->render($attribs);
    }
}
