<?php
class Core_View_Helper_ButtonMiniEdit extends Core_View_Helper_ButtonMiniAbstract
{
    protected $defaultAttribs = array(
        'title' => 'Alterar',
    );

    protected $icon = 'icon-pencil';

    public function buttonMiniEdit(array $attribs)
    {
        return $this->render($attribs);
    }
}
