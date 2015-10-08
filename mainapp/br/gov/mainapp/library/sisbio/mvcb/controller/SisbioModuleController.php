<?php
namespace br\gov\mainapp\library\sisbio\mvcb\controller;
use br\gov\mainapp\library\sisbio\mvcb\controller\ControllerAbstract;

# inclusao do componente de redirecionamento
$RFlowPath = constant('__MAINAPPDOCS__')
           . str_replace(':', DIRECTORY_SEPARATOR, ':br:gov:mainapp:library:SISBioBridge:');

$RFlowPath .= 'RedirectFlow.php';
require_once $RFlowPath;

class SisbioModuleController extends ControllerAbstract
{
    const T_NAMESPACE = 'br\gov\mainapp\application\sisbio\%s\%s\mvcb\controller\%sController';
    private $_module;

    private function _adjusteRequest ()
    {
        $scope     = 'get';
        $request   = $this->request();
        $func      = str_replace('Action', '', $request->getAction());
        $params    = $this->request()->getParams($scope);
        $uriGet    = explode('?', $_SERVER['REQUEST_URI']);
        $uriParams = explode('/', $uriGet[0]);

        # Seta o modulo atual apartir do caminho
        $this->_module = $params['f'];

        # Caso não haja nenhum parâmetro, seta a action para para defaultAction
        $action = NULL;

        if (isset($uriParams[4]) && $uriParams[4] != \RedirectFlow::T_TOKEN_IDENT_FLAG) {
            $action = $uriParams[4];
        }

        $action = $action ?: 'default';

        unset($params['m'], $params['f'], $params['a']);

        if (isset($uriGet[1])) {
            unset($params[$uriGet[1]]);
        }

        $urlParams = array();
        foreach ($params as $key => $value) {
            if ($key === $action){
                break;
            }

            $urlParams[$key] = $value;
            unset($params[$key]);
        }

        $this->request()
             ->clearParams($scope);

        reset($params);
        $nParams = array();

        while ($current = each($params)) {

            $key = current($current);

            if ($key) {
                $nParams[current($current)] = key($params);
            }
        }

        $nParams[$this->request()->getModuleKey()]       = 'sisbio';
        $nParams[$this->request()->getFuncionalityKey()] = $func;
        $nParams[$this->request()->getActionKey()]       = $action . 'Action';

        $this->request()->setParams( array_merge($nParams,$urlParams) );
    }

    public function hasAction ()
    {
        return TRUE;
    }

    public function __call ($ctrlName, $params)
    {
        $this->_adjusteRequest();

        $ctrlName = lcfirst(str_replace('Action', '', $ctrlName));
        $ctrlName = sprintf(self::T_NAMESPACE, $this->_module, $ctrlName, ucfirst($ctrlName));
        $ctrl     = new $ctrlName;
        $action   = $this->request()->getAction();

        parent::hasAction($action);
        $ctrl->applicationRegister($this->_SIALApplication)
             ->$action();
    }
}
