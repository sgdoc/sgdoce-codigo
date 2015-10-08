<?php
class Core_View_Helper_Grid extends Zend_View_Helper_HtmlElement
{
    protected static $defaultTranslator;

    protected static $defaultRange;

    protected static $defaultRangeSelected;

    protected $translator;

    public function grid($patialLoop = NULL, $result = NULL, array $options = array())
    {
        if (0 === func_num_args()) {
            return $this;
        }

        $options['pageUrl'] = isset($options['pageUrl']) ? $options['pageUrl'] : array();

        $header = '';
        if (isset($options['headers'])) {
            $header .= $this->gridHeader($options['headers']);
        }

        $content = $this->view->partialLoop($patialLoop, $result);

        if (!isset($options['rangeSelect'])) {
            $options['rangeSelect'] = self::getDefaultRange();
        }

        if (!isset($options['rangeSelected'])) {
            $options['rangeSelected'] = self::getDefaultRangeSelected();
        }

        if (is_array($options['rangeSelect'])) {
            $content = $this->comboRange(
                            $options['rangeSelect'],
                            $options['rangeSelected'],
                            $options['pageUrl'],
                            isset($options['currentPage']) ?  $options['currentPage'] : $result
            ) . $content;
        }

        $html    = str_replace('%content%',
            $header . $content,
            $this->buildTable(isset($options['attribs']) ? $options['attribs'] : NULL)
        );

        if ($result instanceof Zend_Paginator) {
            $html .= $this->getPager(
                $result->getPages(),
                $options['pageUrl']
            );
        }

        return $html;
    }

    public function comboRange($range, $selected, $pageUrl, $current)
    {
        if ($current instanceof Zend_Paginator) {
            $infoPages = $current->getPages();
            $current   = $infoPages->current;
        }

        $html = '<div class="row-fluid">';
        $html .= '<div class="span6"></div>';
        $html .= '<div class="span6">';
        $html .= '<div class="dataTables_length">';

        $html .= '<label>Registros por página ';

        $html .= '<select>';
        $href = $this->view->urlCurrent(array('page' => $current) + $pageUrl);
        foreach ($range as $number) {
            $selectedStr = $selected  == $number ? 'selected="selected"' : '';
            $html .= '<option value="' . $href . '/range/' . $number . '" ' . $selectedStr . '>' . $number . '</option>';
        }

        $html .= '</select></label></div></div></div>';

        return $html;
    }

    public function getPager($result, array $pageUrl)
    {
        $pages   = $result->pagesInRange;

        if (count($pages) < 2) {
            return '';
        }
        $html = '<div class="pagination pull-right">';
        $href = $this->view->urlCurrent(array('page' => $result->first) + $pageUrl);
        $html .= '<li class="prev"><a href="' . $href . '">← </a></li>';

        foreach ($pages as $page) {
            $pageUrl['page'] = $page;
            $class           = '';
            $href            = $this->view->urlCurrent($pageUrl);
            if ($result->current === $page) {
                $class = 'active';
                $href  = '';
            }
            $html .= '<li class="' . $class . '"><a href="' . $href . '">' . $page . '</a></li>';
        }

        $href = $this->view->urlCurrent(array('page' => $result->last) + $pageUrl);
        $html .= '<li class="next"><a href="' . $href . '"> → </a></li>';
        $html .= '</ul></div>';

        return $html;
    }

    public function gridHeader($headers)
    {
        $html      = "\n<thead>\n<tr>\n";
        $translate = $this->getTranslator();

        foreach ($headers as $header) {
            if ($translate instanceof Zend_Translate) {
                $header = $translate->translate($header);
            }

            $html .= '<th>' . $header . "</th>\n";
        }

        $html .= "</tr>\n</thead>";

        return $html;
    }

    public function setTranslator(Zend_Translate $translate = NULL)
    {
        $this->translator = $translate;
    }

    public function getTranslator()
    {
        if (NULL === $this->translator) {
            $this->setTranslator(self::getDefaultTranslator());
        }

        return $this->translator;
    }

    public static function setDefaultTranslator(Zend_Translate $translate)
    {
        self::$defaultTranslator = $translate;
    }

    public static function setDefaultRange(array $range)
    {
        self::$defaultRange = $range;
    }

    public static function setDefaultRangeSelected($value)
    {
        self::$defaultRangeSelected = $value;
    }

    public static function getDefaultRangeSelected()
    {
        return self::$defaultRangeSelected;
    }

    public function getDefaultRange()
    {
        return self::$defaultRange;
    }

    public function getDefaultTranslator()
    {
        return self::$defaultTranslator;
    }

    protected function buildTable($attribs)
    {
        $attribs = $this->_htmlAttribs($attribs);
        return '<table class="table table-striped table-bordered"' . $attribs . '>%content%</table>';
    }
}
