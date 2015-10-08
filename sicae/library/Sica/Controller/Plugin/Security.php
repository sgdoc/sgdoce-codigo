<?php
/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */

class Sica_Controller_Plugin_Security extends Core_Controller_Plugin_Security
{
    /**
     * Array com as actions ajax que devem ser validadas pelo ACL
     *
     * @var array
     */
    private $_arrAjaxNotSkip = array(
        'delete',
        'toggle-status',         //ativar inativar (sistema,perfil,funcionalidade)
        'switch-status',         //ativar inativar (menu)
        'save-form-web-service', //ativar inativar (pessoa)
        'edit-bind',             //Editar perfis de usuario interno
        'create-bind',           //Adicionar novo perfl a usuario interno
        'delete-profile',        //Excluir perfis de usuario interno
    );

    public function getRedirectLogin(Zend_Controller_Request_Abstract $request)
    {
        $request->setControllerName('usuario')
                ->setActionName('login');
    }

    public function getRedirectHome(Zend_Controller_Request_Abstract $request)
    {
        $controller = $request->getControllerName();
        $action     = $request->getActionName();
        $actions    = array(
           'usuario-perfil' => array(
               'user-unit', 'perfil-unidade', 'user-profile'
            )
        );

        if (isset($actions[$controller]) && in_array($action, $actions[$controller])) {
            return;
        }

        $request->setControllerName('index')
                    ->setActionName('home');
    }

    public function skip(Zend_Controller_Request_Abstract $request)
    {
        return $this->_checkSkip($request, 'auth');
    }

    public function skipAcl(Zend_Controller_Request_Abstract $request)
    {
        return $this->_checkSkipAcl($request, 'acl');
    }

    public function skipHome(Zend_Controller_Request_Abstract $request)
    {
        return $this->_checkSkip($request, 'home');
    }

    protected function _checkSkip(Zend_Controller_Request_Abstract $request, $type)
    {
        $configs    = Zend_Registry::get('configs');

        // verificação de requisicao - Caso ajax, verifica se a action é delete, senao, SKIP nele.
        if( $request->isXmlHttpRequest() && !in_array($request->getActionName(), $this->_arrAjaxNotSkip) ){
            return TRUE;
        }

        $resource = $request->getModuleName()
                  . '/' . $request->getControllerName()
                  . '/' . $request->getActionName();

        return in_array($resource, $configs['security']['skip'][$type]);
    }

    protected function _checkSkipAcl(Zend_Controller_Request_Abstract $request, $type)
    {
        // verificação de requisicao - Caso ajax, verifica se a action é delete, senao, SKIP nele.
        if( $request->isXmlHttpRequest() && !in_array($request->getActionName(), $this->_arrAjaxNotSkip) ){
            return TRUE;
        }

        $configs    = Zend_Registry::get('configs');
        $skip = $configs['security']['skip'][$type];
        $result = FALSE;
        $result = in_array(
            $request->getActionName(),
            $skip
        );

        foreach($skip as $routers){
            $route = explode('/',$routers);
            switch(count($route)){
                case 1:// action
                    $result = in_array(
                        $request->getActionName(),
                        $skip
                    );
                    break;
                case 2:// controller/action
                    $result = in_array(
                        $request->getControllerName().'/'.$request->getActionName(),
                        $skip
                    );
                    break;
                case 3:// module/controller/action
                    $result = in_array(
                        $request->getModuleName().'/'.$request->getControllerName().'/'.$request->getActionName(),
                        $skip
                    );
                    break;
            }

            if ($result){
                return TRUE;
            }
        }
        return $result;
    }
}
