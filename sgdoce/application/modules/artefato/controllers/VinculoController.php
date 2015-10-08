<?php

/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

/**
 * Classe para Controller de Vinculo
 *
 * @package    Artefato
 * @category   Controller
 * @name       Vinculo
 * @version    0.0.1
 * */
class Artefato_VinculoController extends \Core_Controller_Action_Crud
{

    /**
     * @var string
     */
    protected $_service = 'Artefato';

    /**
     * @var string[]
     * */
    private static $T_DIC_ACTION = array(
        'anexar' => 'ANEXAÇÃO',
        'apensar' => 'APENSAÇÃO',
        'desapensar' => 'DESAPENSAÇÃO',
    );

    const T_ICMBIO_IMAGE_LOGO_PATH = '/img/marcaICMBio.png';

    private $_tmp = '';

    private function _hasPermissionAcl($action = null, $controller = null, $module = null)
    {
        $acl      = Zend_Registry::get('acl');
        $profile  = Core_Integration_Sica_User::getUserNoProfile();

        $resource = '';

        if (null === $module) {
            $resource .= $this->getRequest()->getModuleName();
        }else{
            $resource .= $module;
        }
        $resource .=  '/';
        if (null === $controller) {
            $resource .= $this->getRequest()->getControllerName();
        }else{
            $resource .= $controller;
        }
        $resource .=  '/';
        if (null === $action) {
            $resource .= $this->getRequest()->getActionName();
        }else{
            $resource .= $action;
        }


        $this->_tmp = $resource;

        $permission = FALSE;
        if ($acl->has($resource) && $acl->isAllowed($profile, $resource)) {
            $permission = TRUE;
        }

        return $permission;
    }

    private function _getPermissionActions()
    {
        $actions = array(
            array('action'=>'anexar'),
            array('action'=>'anexar-processo'),
            array('action'=>'apensar'),
            array('action'=>'apensar-processo'),
            array('action'=>'inserir-peca'),
            array('action'=>'remover-peca'),
            array('action'=>'desapensar'),
            array('action'=>'desapensar-processo'),
            array('action'=>'desanexar'),
            array('action'=>'desanexar-processo'),
            array('action'=>'desmembrar' , 'controller'=>'desmembrar-desentranhar'),
            array('action'=>'desentranhar', 'controller'=>'desmembrar-desentranhar')
        );

        $filter = new Zend_Filter_Word_UnderscoreToCamelCase();
        foreach ($actions as $data) {
            $module     = (isset($data['module'])) ? $data['module'] : null;
            $controller = (isset($data['controller'])) ? $data['controller'] : null;

            $keyAction = lcfirst($filter->filter(str_replace('-', '_', $data['action'])));
            $permissions[$keyAction] = $this->_hasPermissionAcl($data['action'], $controller, $module);
        }

        return $permissions;
    }

    private function _checkAcl()
    {
        if (!$this->_hasPermissionAcl()) {
            throw new \Exception('MN156', 9999);
        }
        return $this;
    }

    public function indexAction ()
    {
        $params = $this->_getAllParams();
        $redirect = '/artefato/area-trabalho';

        if (!isset($params['id'])) {
            $this->_redirect($redirect);
        }

        if (isset($params['back'])) {
            $redirect = $this->view->backUrl = str_replace('.','/',$params['back']);
        }

        $params['sqArtefato'] = $params['id'];
        $this->view->hasPermission = $this->getService("Artefato")->inMyDashboard($params['sqArtefato']);


        $entityArtefato     = $this->getService('Artefato')->find($params['sqArtefato']);
        $entityTipoArtefato = $entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato();
        /**
         * Verifica se o artefato possui imagem.
         * Caso não possui imagem não pode receber vinculos
         */
        $hasImage = $this->getService('ArtefatoImagem')->hasImage(
                        $entityArtefato->getSqArtefato(),
                        $entityTipoArtefato->getSqTipoArtefato());

        if (!$hasImage) {
            $nrArtefato     = $this->view->nuArtefato($entityArtefato);
            $noTipoArtefato = mb_strtolower($entityTipoArtefato->getNoTipoArtefato(),'utf-8');
            $msg            = "O {$noTipoArtefato} <b>{$nrArtefato}</b> não possui imagem. Não pode acessar a área de vínculos.";
            $this->getMessaging()->addAlertMessage($msg, 'User');
            $this->_redirect($redirect);
        }

        $dtoAT = \Core_Dto::factoryFromData($params, 'search');
        $entityAreaTrabalho = $this->getService('AreaTrabalho')->findArtefato($dtoAT);

        /* se o documento não estiver na area e usuario nao for SGI, vai direto para visualizar a arvore de vinculo */
        if(!$this->_isUserSgi()){
            if (!$this->view->hasPermission || $entityAreaTrabalho->getHasSolicitacaoAberta()) {

                $urlParams = NULL;

                # lima parametros desnecessarios
                unset(
                    $params['module'], $params['controller'], $params['action']
                );

                foreach ($params as $key => $value) {
                    $urlParams .= sprintf('/%s/%s', $key, $value);
                }

                $gotoUrl = '/artefato/vinculo/motrar-arvore' . $urlParams;

                $this->_redirect($gotoUrl);
            }
        }

        $dtoSearch = Core_Dto::factoryFromData($params, 'search');
        $this->view->artefato = $this->getService()->findVisualizarArtefato($dtoSearch);
        $this->view->entityArtefato = $entityArtefato;

        if ($this->view->artefato['sqTipoArtefato'] == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
            $this->view->artefatoProcesso = $this->getService('ArtefatoProcesso')->find($dtoSearch->getSqArtefato());
        }

        $this->view->sqArtefato = $entityArtefato->getSqArtefato();
        $this->view->permissionAction = $this->_getPermissionActions();
        $this->render('list');
    }

    public function vinculoAction ()
    {
        $this->render('index');
    }

    public function workspaceArtefatoListAction ()
    {
        # dispensa o uso do template
        $this->getHelper('layout')->disableLayout();

        $params = $this->_getAllParams();
        $configArray = $this->getConfigList();

        if (is_array($configArray)) {
            $grid = new Core_Grid($configArray);
            $params = $grid->mapper($params);
        }

        $result = $this->resultListWorkspaceArtefato($params);

        $this->view->result = $result;
        $this->view->params = $params;

        $this->view->permissionAction = $this->_getPermissionActions();

        $entArtefatoPai = $this->getService('Artefato')
                ->find((integer) $params['sqArtefatoParent']);
        
        $this->view->sqArtefatoTipoSource = $entArtefatoPai->getSqTipoArtefatoAssunto()
                                                           ->getSqTipoArtefato()
                                                           ->getSqTipoArtefato();
        
        $this->view->isProcesso = $entArtefatoPai->isProcesso();        

        $this->view->arrTipoArtefato = array(
            'DOCUMENTO' => \Core_Configuration::getSgdoceTipoArtefatoDocumento(),
            'PROCESSO' => \Core_Configuration::getSgdoceTipoArtefatoProcesso(),
        );
    }

    public function getConfigList ()
    {
        $array = array(
            'columns' => array(
                array('alias' => 'nu_ordem'),
                array('alias' => 'nu_ordem'),
                array('alias' => 'nu_digital'),
//                array('alias' => 'dt_tramite'),
                array('alias' => 'nu_artefato'),
                array('alias' => 'no_tipo_documento'),
                array('alias' => 'tx_assunto'),
                array('alias' => 'tx_movimentacao'),
                array('alias' => 'is_vinculo'),
            ),
        );

        return $array;
    }

    public function resultListWorkspaceArtefato ($params)
    {
        return$this->getService('ArtefatoVinculo')
                   ->vinculoListGrid($params);
    }

    public function anexarAction ()
    {
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();

        try {
            $this->_checkAcl();

            $entArtefatoPai = $this->getService()->find((integer) $params['parent']);
            $entArtefatoFilho = $this->getService()->find((integer) $params['child']);

            // Se qualquer um dos artefatos forem processo não permite operação.
            if( $entArtefatoPai->isProcesso()
                || $entArtefatoFilho->isProcesso() ) {

                $this->_helper->json(array(
                    "status" => FALSE, "message" => 'MN191',
                ));
            }
            
            $retorno = $this->_doAnexo($entArtefatoPai, $entArtefatoFilho);

            $msgId = ($retorno) ? 'MN013' : 'MN205' ;

            $this->_helper->json(array(
                "status" => $retorno, "message" => $msgId,
            ));
        } catch (\Exception $e) {
           $this->_trataException($e);
        }
    }

    public function anexarProcessoAction ()
    {
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();

        try {
            $this->_checkAcl();

            $entArtefatoPai = $this->getService()->find((integer) $params['parent']);
            $entArtefatoFilho = $this->getService()->find((integer) $params['child']);

            // Se qualquer um dos artefatos não forem processo não permite operação.
            if( !$entArtefatoPai->isProcesso()
                || !$entArtefatoFilho->isProcesso() ) {

                $this->_helper->json(array(
                    "status" => FALSE, "message" => 'MN191',
                ));
            } 
            
            $retorno = $this->_doAnexo($entArtefatoPai, $entArtefatoFilho);

            $msgId = ($retorno) ? 'MN013' : 'MN205' ;

            $this->_helper->json(array(
                "status" => $retorno, "message" => $msgId,
            ));
        } catch (\Exception $e) {
           $this->_trataException($e);
        }
    }

    public function apensarAction ()
    {
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();

        try {
            $this->_checkAcl();

            $entArtefatoPai = $this->getService()->find((integer) $params['parent']);
            $entArtefatoFilho = $this->getService()->find((integer) $params['child']);
            // Se qualquer um dos artefatos forem processo não permite operação.
            if( $entArtefatoPai->isProcesso()
                || $entArtefatoFilho->isProcesso() ) {

                $this->_helper->json(array(
                    "status" => FALSE, "message" => 'MN191',
                ));
            } 
            
            $retorno = $this->_doApenso($entArtefatoPai, $entArtefatoFilho);

            $msgId = ($retorno) ? 'MN013' : 'MN205' ;

            $this->_helper->json(array(
                "status" => $retorno, "message" => $msgId,
            ));
        } catch (\Exception $e) {
            $this->_trataException($e);
        }
    }

    public function apensarProcessoAction ()
    {
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();

        try {
            $this->_checkAcl();

            $entArtefatoPai = $this->getService()->find((integer) $params['parent']);
            $entArtefatoFilho = $this->getService()->find((integer) $params['child']);
            // Se qualquer um dos artefatos não forem processo não permite operação.
            if( !$entArtefatoPai->isProcesso()
                || !$entArtefatoFilho->isProcesso() ) {

                $this->_helper->json(array(
                    "status" => FALSE, "message" => 'MN191',
                ));
            } 
        
            $retorno = $this->_doApenso($entArtefatoPai, $entArtefatoFilho);

            $msgId = ($retorno) ? 'MN013' : 'MN205' ;

            $this->_helper->json(array(
                "status" => $retorno, "message" => $msgId,
            ));
        
        } catch (\Exception $e) {
            $this->_trataException($e);
        }
    }
    
    public function apensarMultiProcAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $entArtefatoPai = $this->getService()->find((integer) $params['parent']);
        $msg = '';
        $status = true;
        
        if( !$entArtefatoPai->isProcesso() ){
            $status = false;
            $msg .= \Core_Registry::getMessage()->translate('MN191') . "<br />";
        }
        
        foreach( $params['child'] as $item ) {
            
            $entArtefatoFilho = $this->getService()->find((integer) $item);
            // Se qualquer um dos artefatos não forem processo não permite operação.
            if( !$entArtefatoFilho->isProcesso() ) {
                $status = false;
                $msg .= \Core_Registry::getMessage()->translate('MN191') . "<br />";
            } 

            $retorno = $this->_doApenso($entArtefatoPai, $entArtefatoFilho, false);
            
            if( is_array($retorno) ) {
                $status = false;
                $msg .= $retorno['message'] . "<br />";
            }
        }
        
        if( $status ) {
            $msg = \Core_Registry::getMessage()->translate('MN013');
        }

        $this->_helper->json(array(
            "status" => $status, "message" => $msg,
        ));
    }
    
    protected function _doApenso( $entArtefatoPai, $entArtefatoFilho, $exit = true )
    {
        try {            
            $this->getService('ArtefatoVinculo')
                        ->apensar(array(
                            'parent' => $entArtefatoPai,
                            'child' => $entArtefatoFilho,
            ));
            return true;
        } catch (\Exception $e) {
            if( $exit ) {
                $this->_trataException($e, $exit);
            } else {
                return $this->_trataException($e, $exit);
            }
        }
    }    
    
    protected function _doAnexo( $entArtefatoPai, $entArtefatoFilho )
    {
        try {            
            return $this->getService('ArtefatoVinculo')
                ->anexar(array(
                    'parent' => $entArtefatoPai,
                    'child' => $entArtefatoFilho,
            ));            
        } catch (\Exception $e) {
            $this->_trataException($e);
        }
    }

    public function inserirPecaAction ()
    {
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();

        try {
            $this->_checkAcl();

            $this->getService('ArtefatoVinculo')
                    ->inserirPeca(array(
                        'parent' => $this->getService()->find((integer) $params['parent']),
                        'child' => $this->getService()->find((integer) $params['child']),
            ));

            $this->_helper->json(array(
                "status" => TRUE, "message" => 'MN013',
            ));
        } catch (\Exception $e) {
            $this->_trataException($e);
        }
    }

    public function termoJuntadaApensarAction ()
    {
        $this->_generateTerm('juntada');
    }

    public function termoJuntadaAnexarAction ()
    {
        $this->_generateTerm('juntada');
    }

    public function termoRemocaoDesapensarAction ()
    {
        $this->_generateTerm('remocao');
    }

    public function termoRemocaoDesanexarAction ()
    {
        $this->_generateTerm('remocao');
    }

    public function _generateTerm ($type)
    {
        # dispensa o uso do template
        $this->getHelper('layout')->disableLayout();
        $params = $this->_getAllParams();
        $anexado = null;
        
        if( is_array($params['child']) ){
            $anexado = array();
            foreach( $params['child'] as $item ){
                $anexado[] = $this->getService('Artefato')->find((integer)$item);
            }
        } else {
            $anexado = $this->getService('Artefato')->find((integer) $params['child']);
        }

        $anexador   = $this->getService('Artefato')->find((integer) $params['parent']);        
        $despacho   = $this->getService('DespachoInterlocutorio')->find((integer) $params['despacho']);
        $assinante  = $this->getService('VwPessoa')->find((integer) $params['assinante']);

        $noCargoFuncao = null;
        if( $params['cargo'] ) {
            $cargoFuncao = $this->getService('Cargo')->find((integer) $params['cargo']);
            $noCargoFuncao = $cargoFuncao->getNoCargo();
        } else if( $params['funcao'] ) {
            $cargoFuncao = $this->getService('Funcao')->find((integer) $params['funcao']);
            $noCargoFuncao = $cargoFuncao->getNoFuncao();
        }

        $options = array(
            'fname' => sprintf('Termo%s-%s.pdf', ucfirst($type), date('YmdHis')),
            'path' => APPLICATION_PATH . '/modules/artefato/views/scripts/vinculo/',
        );

        $logo = current(explode('application', __FILE__))
                . 'public' . DIRECTORY_SEPARATOR
                . ltrim(self::T_ICMBIO_IMAGE_LOGO_PATH, DIRECTORY_SEPARATOR);

        \Core_Doc_Factory::setFilePath($options['path']);

        $vData = array(
            'data' => (object) array(
                'tipoOperacao' => self::$T_DIC_ACTION[$params['tOper']],
                'unidadeBase' => \Core_Integration_Sica_User::getUserUnitName(),
                'unidadeAtendida' => $despacho->getSqUnidadeAssinatura()->getNoUnidadeOrg(),
                'processoAnexador' => $anexador,
                'processoAnexado' => $anexado,
                'despacho' => str_pad((integer) $params['despacho'], 8, '0', \STR_PAD_LEFT),
                'assinante' => $assinante->getNoPessoa(),
                'cargoFuncao' => $noCargoFuncao,
                'dataExtenso' => \Zend_Date::now()->get("dd 'de' MMMM 'de' yyyy"),
            ),
            'logo' => $logo,
        );

        \Core_Doc_Factory::download(sprintf('termo-%s-anexacao-doc', $type), $vData, $options['fname']);
    }

    public function desanexarAction ()
    {
        # dispensa o uso do template
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->_getAllParams();

        try {
            $this->_checkAcl();

            $entArtefatoPai = $this->getService()->find((integer) $params['parent']);
            $entArtefatoFilho = $this->getService()->find((integer) $params['child']);

            // Se qualquer um dos artefatos não forem processo não permite operação.
            if( $entArtefatoPai->isProcesso()
                || $entArtefatoFilho->isProcesso() ) {

                $this->_helper->json(array(
                    "status" => FALSE, "message" => 'MN191',
                ));
            } else {
                $this->getService('ArtefatoVinculo')
                        ->desanexar(array(
                            'parent' => $entArtefatoPai,
                            'child' => $entArtefatoFilho,
                ));
            }

            /*
             * operacao realizada com sucesso
             */
            $this->_helper->json(array(
                "status" => TRUE,
                "message" => 'MN013',
            ));
        } catch (\Exception $e) {
            $this->_trataException($e);
        }
    }

    public function desanexarProcessoAction ()
    {
        # dispensa o uso do template
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->_getAllParams();

        try {
            $this->_checkAcl();

            $entArtefatoPai = $this->getService()->find((integer) $params['parent']);
            $entArtefatoFilho = $this->getService()->find((integer) $params['child']);

            // Se qualquer um dos artefatos não forem processo não permite operação.
            if( !$entArtefatoPai->isProcesso()
                || !$entArtefatoFilho->isProcesso() ) {

                $this->_helper->json(array(
                    "status" => FALSE, "message" => 'MN191',
                ));
            } else {
                $this->getService('ArtefatoVinculo')
                        ->desanexar(array(
                            'parent' => $entArtefatoPai,
                            'child' => $entArtefatoFilho,
                ));
            }

            /*
             * operacao realizada com sucesso
             */
            $this->_helper->json(array(
                "status" => TRUE,
                "message" => 'MN013',
            ));
        } catch (\Exception $e) {
            $this->_trataException($e);
        }
    }

    public function desapensarAction ()
    {
        # dispensa o uso do template
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();

        try {
            $this->_checkAcl();

            $entArtefatoPai = $this->getService()->find((integer) $params['parent']);
            $entArtefatoFilho = $this->getService()->find((integer) $params['child']);

            // Se qualquer um dos artefatos for processo não permite operação.
            if( $entArtefatoPai->isProcesso()
                || $entArtefatoFilho->isProcesso() ) {

                $this->_helper->json(array(
                    "status" => FALSE, "message" => 'MN191',
                ));
            } else {
                $this->_doDesapenso($entArtefatoPai, $entArtefatoFilho);
            }
            # mensgem de sucesso
            $this->_helper->json(array(
                "status" => TRUE,
                "message" => 'MN013'
            ));
        } catch (\Exception $e) {
            $this->_trataException($e);
        }
    }

    public function desapensarProcessoAction()
    {
        # dispensa o uso do template
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();

        try {
            $this->_checkAcl();

            $entArtefatoPai = $this->getService()->find((integer) $params['parent']);
            $entArtefatoFilho = $this->getService()->find((integer) $params['child']);

            // Se qualquer um dos artefatos não forem processo não permite operação.
            if( !$entArtefatoPai->isProcesso()
                || !$entArtefatoFilho->isProcesso() ) {

                $this->_helper->json(array(
                    "status" => FALSE, "message" => 'MN191',
                ));
            } else {
                $this->_doDesapenso($entArtefatoPai, $entArtefatoFilho);
            }

            # mensgem de sucesso
            $this->_helper->json(array(
                "status" => TRUE,
                "message" => 'MN013'
            ));
        } catch (\Exception $e) {
            $this->_trataException($e);
        }
    }
    
    
    public function desapensarMultiProcAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        $entArtefatoPai = $this->getService()->find((integer) $params['parent']);
        $msg = '';
        $status = true;
        
        if( !$entArtefatoPai->isProcesso() ){
            $status = false;
            $msg .= \Core_Registry::getMessage()->translate('MN191') . "<br />";
        }
        
        foreach( $params['child'] as $item ) {
            
            $entArtefatoFilho = $this->getService()->find((integer) $item);
            // Se qualquer um dos artefatos não forem processo não permite operação.
            if( !$entArtefatoFilho->isProcesso() ) {
                $status = false;
                $msg .= \Core_Registry::getMessage()->translate('MN191') . "<br />";
            } 

            $retorno = $this->_doDesapenso($entArtefatoPai, $entArtefatoFilho, false);
            
            if( is_array($retorno) ) {
                $status = false;
                $msg .= $retorno['message'] . "<br />";
            }
        }
        
        if( $status ) {
            $msg = \Core_Registry::getMessage()->translate('MN013');
        }

        $this->_helper->json(array(
            "status" => $status, "message" => $msg,
        ));
    }
    
    protected function _doDesapenso( $entArtefatoPai, $entArtefatoFilho, $exit = true )
    {
        try {            
            $this->getService('ArtefatoVinculo')
                        ->desapensar(array(
                            'parent' => $entArtefatoPai,
                            'child' =>  $entArtefatoFilho,
            ));         
            
            return true;
        } catch (\Exception $e) {
            if( $exit ) {
                $this->_trataException($e, $exit);
            } else {
                return $this->_trataException($e, $exit);
            }
        }
    }    

    public function removerPecaAction ()
    {
        # dispensa o uso do template
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();

        try {
            $this->_checkAcl();

            $this->getService('ArtefatoVinculo')
                    ->removerPeca(array(
                        'parent' => $this->getService()->find((integer) $params['parent']),
                        'child' => $this->getService()->find((integer) $params['child']),
            ));

            # mensgem de sucesso
            $this->_helper->json(array(
                "status" => TRUE,
                "message" => 'MN013'
            ));
        } catch (\Exception $e) {
            $this->_trataException($e);
        }
    }

    public function motrarArvoreAction ()
    {
        $params = $this->_getAllParams();

        $this->view->data = (object) array();
        $this->view->data->sqArtefato = $params['sqArtefato'];

        if (isset($params['back'])) {
            $this->view->backUrl = str_replace('.','/',$params['back']);
        }

        $this->view->data->artefatos = $this->getService('ArtefatoVinculo')
                ->mostarArvore((integer) $params['sqArtefato']);
    }

    public function formTermoJuntadaAnexarAction ()
    {
        $this->getHelper('layout')->disableLayout();
        if (!$this->_hasPermissionAcl()) {
            throw new \Exception(Core_Registry::getMessage()->translate('MN156'));
        }

        $this->_formTermo('juntada');
    }

    public function formTermoJuntadaApensarAction ()
    {
        $this->getHelper('layout')->disableLayout();
        if (!$this->_hasPermissionAcl()) {
            throw new \Exception(Core_Registry::getMessage()->translate('MN156'));
        }

        $this->_formTermo('juntada');
    }

    public function formTermoRemocaoDesapensarAction ()
    {
        $this->_formTermo('remocao');
    }

    public function modalFirstPieceAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->view->entityArtefato = $this->getService('Artefato')->find($this->_getParam('id'));
    }

    public function saveFirstPieceAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();

        try {
            $this->_checkAcl();

            $this->getService('ArtefatoVinculo')
                    ->inserirPeca(array(
                        'parent' => $this->getService()->find((integer) $params['parent']),
                        'child' => $this->getService()->find((integer) $params['child'])),
                    \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao()
            );

            $this->_helper->json(array(
                "status" => TRUE, "message" => 'MN013',
            ));
        } catch (\Exception $e) {
            $this->_trataException($e);
        }
    }

    public function autocompleteDocumentsFirstPieceAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->_getAllParams();
        $params['sqTipoArtefato'] = \Core_Configuration::getSgdoceTipoArtefatoDocumento();
        $dto = Core_Dto::factoryFromData($params, 'search');

        $result = $this->getService('ArtefatoVinculo')->searchDocumentsFirstPiece($dto);
        if (0 === count($result)) {
            $result = array('__NO_CLICK__' => \Core_Registry::getMessage()->_('MN025'). ' Apenas documentos com imagem são apresentados');
        }
        $this->_helper->json($result);
    }

    private function _formTermo ($type)
    {
        $this->getHelper('layout')->disableLayout();

        $params = $this->_getAllParams();
        
        $parent = $this->getService()->find((integer) $params['parent']);
        $child = null;
        
        if( is_array($params['child']) ) {
            $child = array();
            foreach( $params['child'] as $sqArtefatoChild ){
                $child[] =$this->getService()->find((integer)$sqArtefatoChild);
            }
        } else {
            $child = $this->getService()->find((integer) $params['child']);
        }
        
        $this->view->data         = (object) array();
        $this->view->data->tOper  = $params['tOper'];
        $this->view->data->parent = $parent;
        $this->view->data->child  = $child;
        $this->view->arrCargo     = $this->getService('VwCargo')->comboCargo(false);
        $this->view->arrFuncao    = $this->getService('Funcao')->comboFuncao();

        $VScript = sprintf('form-termo-%s', $type);

        $this->render($VScript);
    }

    private function _trataException(\Exception $e, $exit = true)
    {
        $msgCode = 'MN120';
        $errComplementar = '';

        if ( 9999 == $e->getCode() ) {
            $msgCode = $e->getMessage();
        } else {
            //Traduz pois pode vir codigo de msg
            $errComplementar = Core_Registry::getMessage()->translate($e->getMessage());
        }

        $retorno = array(
            "status" => FALSE,
            "message" => $msgCode,
            "errorCompl" => $errComplementar
        );
        
        if( $exit ) {
            $this->_helper->json($retorno);
        }
        
        return $retorno;
    }
    
    
    public function ordenarAction ()
    {
        # dispensa o uso do template
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $params = $this->_getAllParams();
        
        try {
            
            $this->getService('ArtefatoVinculo')->ordenar($params['id'], $params['op']);

            # mensgem de sucesso
            $this->_helper->json(array(
                "status" => TRUE,
                "message" => 'MN013'
            ));
        } catch (\Exception $e) {
            $this->_trataException($e);
        }
    }

}
