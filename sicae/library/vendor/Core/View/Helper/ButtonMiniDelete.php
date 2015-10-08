<?php
class Core_View_Helper_ButtonMiniDelete extends Core_View_Helper_ButtonMiniAbstract
{
    protected $defaultAttribs = array(
        'title' => 'Excluir',
    );

    protected $icon = 'icon-trash';

    public function buttonMiniDelete(array $attribs)
    {
        return $this->render($attribs);
    }
}
