<?php
namespace br\gov\mainapp\library\sisbio\mvcb\controller;
use br\gov\mainapp\library\sisbio\mvcb\controller\ControllerAbstract as ParentController;

class SisbioController extends ParentController
{
    const SISBIO_LAYOUT = '/application/layout/sisbio';
    protected $scriptsFolder = '';
    
    public function __construct() {
        parent::__construct();
        
        $this->scriptsFolder = parent::erReplace(array('mvcb\\controller' => 'mvcb/view/scripts', '\\' => '/'), substr($this->getNamespace(),14)) . '/';
    }

    public function setViewParam($attr, $value)
    {
        $this->_SIALApplication->set($attr, $value);
        return $this;
    }
    
    public function setViewParams($arr)
    {
        foreach($arr as $key => $value)
            $this->setViewParam($key, $value);

        return $this;
    }

    public function renderMenu()
    {
        $this->_SIALApplication->render('/application/sisbio/tabelasAuxiliares/menu/mvcb/view/scripts/html/Menu');
    }    
    
    public function renderHtml($view = null, $defaultLayout = true)
    {
        $this->setViewParam('isExterno', $this->isExterno());
        if ($defaultLayout) {
            $this->_SIALApplication->raise($this::T_EVENT_CONTROLLER_ABSTRACT_SHOW_MENU);
            $this->_SIALApplication->render(self::SISBIO_LAYOUT);
        }

        if (!$view) {
            $view = current( explode('Action', $this->request()->getAction()) );
            $this->_SIALApplication
                 ->render($this->scriptsFolder . 'html/' . ucfirst($view));
         } else {
            if ($view[0] !== '/') {
                $this->_SIALApplication->render($this->scriptsFolder . 'html/' . ucfirst($view));
            } else {
                $this->_SIALApplication->render($view);
            }
         }
         return $this;
    }

    public function renderJson($view = null)
    {
        if ($view == null)
            $view = current(explode('Action', $this->request()->getAction()));

        $this->_SIALApplication
             ->render($this->scriptsFolder . 'json/' . ucfirst($view));
        return $this;
    }
}