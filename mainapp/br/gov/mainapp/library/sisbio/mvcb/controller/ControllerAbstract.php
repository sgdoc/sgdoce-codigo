<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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
namespace br\gov\mainapp\library\sisbio\mvcb\controller;
use br\gov\sial\core\util\Session,
    br\gov\sial\core\exception\SIALException,
    //br\gov\sial\core\mvcb\controller\ControllerAbstract as ParentController;
    br\gov\mainapp\library\mvcb\controller\ControllerAbstract as ParentController;

/**
 * @package br.gov.mainapp.library.mvcb
 * @subpackage controller
 * @name ControllerAbstract
 */
class ControllerAbstract extends ParentController
{
    /**
     * Evento para Disparar Menu
     */
    CONST T_EVENT_CONTROLLER_ABSTRACT_SHOW_MENU = 'onShowMenu';

    private $_user;
    
    /**
     * Construtor
     */
    public function __construct() {
        parent::__construct();
        $this->_user = Session::getLiveSession('sisicmbio','USER');
    }
    
    public function isExterno() {
        return $this->_user->inPerfilExterno;
    }
    
    public function getSqPessoa() {
        return $this->_user->sqPessoa;
    }
	
    /**
     * Gera Menus para a View
     */
    public function getMenu ()
    {
        # Monto a opção de escolha dos sistemas do Usuario
        try {
            $sess = Session::getLiveSession('sisicmbio','USER');
            SIALException::ThrowsExceptionIfParamIsNull($sess, 'Não foi encontrada a sessão para o objeto informado');
        } catch (SIALException $e) {
            # Não foi encontrada a Sessão para Este Sistema
            header("Location: " . $this->bootstrap()->config()->get('app')->get('authSystem'));
        }

        $count = 0;
        foreach ($sess->sistemas as $sistema) {
            $arrSistema[$count]['href'] = $sistema['sqSistema'];
            $arrSistema[$count]['text'] = $sistema['sgSistema'];
            $count++;
        }

        $Menuoptions = array('__DIVIDER_VERTICAL__',
                             'Início' => array(),
                             '__DIVIDER_VERTICAL__' ,
                             'Sistemas' => $arrSistema,
                             '__DIVIDER_VERTICAL__');

        $menuParam = new \stdClass;
        $menuParam->options = $Menuoptions;
        $menuParam->type    = 'h';

        $this->_SIALApplication->set('menuH', $this->getSAF()->create('menu', $menuParam));
        $this->_SIALApplication->set('param', array(
                                        'nome'         => $sess->noPessoa,
                                        'perfil'       => $sess->noPerfil,
                                        'uorg'         => (isset($sess->noUnidadeOrg)) ? $sess->noUnidadeOrg : NULL,
                                        'help'         => 'HTML',
                                        'sysId'        => $sess->sqSistema,
                                        'multiProfile' => count($sess->allProfile) > 1 ? TRUE : FALSE
                                    ));

        # Monta os Menus
        $menu = $sess->MenuExterno;
        $arrMenu = array();

        foreach ($menu as $no) {
            #Sq da Raíz
            $curr = $no['MenuPai']['sqMenu'];
            $arrMenu[$curr]['text'] = $no['MenuPai']['noMenu'];
            $arrMenu[$curr]['href'] = $no['Acao'];

            #adiciona cada filho
            if (isset($no['MenuFilho'])) {
                foreach ($no['MenuFilho'] as $filho) {
                    if (isset($filho['Acao']) && NULL !== $filho['Acao'])
                        $arrMenu[$curr][$filho['MenuFilho']['sqMenu']]['href'] = $filho['Acao'];

                    if (!empty($filho['MenuFilho']['noMenu']))
                        $arrMenu[$curr][$filho['MenuFilho']['sqMenu']]['text'] = $filho['MenuFilho']['noMenu'];

                }
            }

            if (isset($no['MenuNeto'])) {
                foreach ($no['MenuNeto'] as $menuNeto) {
                    $netoList = current($menuNeto);
                    if (isset($arrMenu[$curr][$netoList['sqMenuPai']])) {
                        $arrMenu[$curr][$netoList['sqMenuPai']][$netoList['sqMenu']]['href']
                            = (isset($menuNeto['Acao'])) ? $menuNeto['Acao'] : '#';

                        if (isset($netoList['noMenu']))
                            $arrMenu[$curr][$netoList['sqMenuPai']][$netoList['sqMenu']]['text']
                                = $netoList['noMenu'];

                    }
                }
            }
        }

        $menuParam = new \stdClass;
        $menuParam->title   = 'Menu';
        $menuParam->options = $arrMenu;
        $menuParam->type    = 'v';

        $this->_SIALApplication->set('menuV',$this->getSAF()->create('menu', $menuParam));
    }

    /**
     * @return br\gov\sial\core\saf\ISAF
     */
    public function getSAF ()
    {
        return $this->_SIALApplication->saf;
    }

    /**
     * @param string $layout
     */
    public function render ($layout = NULL)
    {
        $this->_SIALApplication->render($layout);
    }

    /**
     * @return string
     */
    public function getCdn ()
    {
        return $this->bootstrap()->config()->get('app')->get('layout')->get('cdn');
    }
}
