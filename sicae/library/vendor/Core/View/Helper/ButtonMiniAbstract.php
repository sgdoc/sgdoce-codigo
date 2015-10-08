<?php
abstract class Core_View_Helper_ButtonMiniAbstract extends Zend_View_Helper_HtmlElement
{
    protected $html = '<a %attribs%
                          class="btn btn-mini %class%">
                <span class="%icon%"></span>
            </a>';

    protected $defaultAttribs = array();

    protected $icon;

    protected function render($attribs)
    {
        $this->setAttribs($attribs);
        $this->setIcon();

        return $this->html;
    }

    protected function setAttribs(array $attribs)
    {
        $attribs = $this->defaultAttribs + $attribs;
        $this->validRequiredAttribs($attribs);

        $class = '';
        if (isset($attribs['class'])) {
            $class = $attribs['class'];
            unset($attribs['class']);
        }

        $attribs = $this->_htmlAttribs($attribs);
        $this->html = str_replace('%class%', $class, $this->html);
        $this->html = str_replace('%attribs%', $attribs, $this->html);
    }

    protected function setIcon()
    {
        if (!$this->icon) {
            throw new UnexpectedValueException('Is need define icon.');
        }

        $this->html = str_replace('%icon%', $this->icon, $this->html);
    }

    protected function validRequiredAttribs(array $attribs)
    {
        if (!isset($attribs['href'])) {
            throw new UnexpectedValueException('Attribute \'href\' is required');
        }

        if (!isset($attribs['title'])) {
            throw new UnexpectedValueException('Attribute \'title\' is required');
        }
    }
}
