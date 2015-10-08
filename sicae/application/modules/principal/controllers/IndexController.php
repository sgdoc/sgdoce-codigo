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

/**
 * SISICMBio
 *
 * Classe Controller Index
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         Index
 * @version      1.0.0
 * @since        2012-07-24
 */
class Principal_IndexController extends Core_Controller_Action_CrudDto
{
    /**
     * Constante para nome da sessão do usuário
     * @var string
     */

    const USER = 'USER';

    /**
     * Nome do servico
     * @var string
     */
    protected $_service = 'Usuario';

    public function init ()
    {
        if(FALSE === isset(Zend_Auth::getInstance()->getIdentity()->sqUsuario)) {
            $this->_redirect('/usuario/login');
        }
    }

    /**
     * Action para pagina home apos usuário logado.
     * @return void
     */
    public function homeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->setLayout('home');
        $user = Zend_Auth::getInstance()->getIdentity();
        $systens = $this->getService('UsuarioPerfil')->findSystensByUser($user->sqUsuario);
        $session = new Core_Session_Namespace(self::USER, FALSE, TRUE);
        $session->sistemas = $systens;

        $this->view->listSystem = $systens;

        $this->view->changePassword = ($this->getRequest()->getParam('change')==='password');
    }

    public function blankAction()
    {}

    public function indexAction()
    {
        $user = Zend_Auth::getInstance()->getIdentity();
        if( (int) $user->sqSistema !== (int) \Core_Configuration::getSicaeSqSistema() ) {
            $this->_redirect('/index/home');
        }
    }
}
