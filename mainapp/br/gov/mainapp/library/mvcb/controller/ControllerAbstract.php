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
namespace br\gov\mainapp\library\mvcb\controller;
use br\gov\sial\core\util\Session,
    br\gov\sial\core\exception\SIALException,
    br\gov\sial\core\mvcb\controller\ControllerAbstract as ParentController;

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
    CONST T_EVENT_CONTROLLER_ABSTRACT_SHOW_MENU = 'showMenuSignal';
    /**
     * Evento de renderização de retornos
     * assincronos (AJAX -> JSON).
     * Parametro opcional $jsonData com os
     * dados que serão serializados e exibidos.
     *
     * @example Emitindo 'Render JSON Signal'
     * @code
     * <?php
     *     //Dentro de uma Controller::action()
     *     //...
     *     $jsonData = array(
     *         'result' => array() //Array ou \stdClass
     *         'status' => 'success' //String com 'success', 'error' ou 'warning'
     *     );
     *     $this->_SIALApplication
     *           ->raise($this::T_EVENT_CONTROLLER_ABSTRACT_RENDER_JSON, $jsonData);
     * ?>
     * @endcode
     */
    const T_EVENT_CONTROLLER_ABSTRACT_RENDER_JSON = 'renderJsonSignal';

    CONST T_VIEW_HTML_PATH = 'view/scripts/html';

    /**
     * Gera Menus para a View
     */
    public function getMenu ()
    {
        # Monto a opção de escolha dos sistemas do Usuario
        try {
            $sess = Session::getLiveSession('sisicmbio','USER');
            SIALException::ThrowsExceptionIfParamIsNull($sess, 'Não foi encontrada a sessão para o objeto informado');
        } catch (SIALException $excp) {
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

        $this->_SIALApplication->set('menuH',$this->getSAF()->create('menu', $menuParam));

        $this->_SIALApplication->set('param',array('nome'         => $sess->noPessoa,
                                                   'perfil'       => $sess->noPerfil,
                                                   'uorg'         => $sess->noUnidadeOrg,
                                                   'help'         => 'HTML',
                                                   'sysId'        => $sess->sqSistema,
                                                   'sysAlias'     => isset($sess->sistemas[$sess->sqSistema]['sgSistema']) ? $sess->sistemas[$sess->sqSistema]['sgSistema'] : '',
                                                   'multiProfile' => count($sess->allProfile) > 1 ? TRUE : FALSE,
                                                   'inPerfilExterno' => $sess->inPerfilExterno,
                                                  )
                                    );

        # Monto os Menus
        $menu = $sess->MenuExterno;

        $count = 0;
        $countSon = 0;
        $arrMenu = array();
        foreach ($menu as $menuCadastro) {
            $arrMenu[$count]['text'] = $menuCadastro['MenuPai']['noMenu'];
            $arrMenu[$count]['href'] = $menuCadastro['Acao'];

            if (isset($menuCadastro['MenuFilho'])) {
                $arrTmpFilho = current($menuCadastro['MenuFilho']);
                if (empty($arrTmpFilho['MenuFilho']['noMenu'])) {
                   // continue;
                }

                foreach ($menuCadastro['MenuFilho'] as $menuFilho) {

                    if (!isset($arrMenu[$count])) {
                        continue;
                    }

                    if (isset($menuFilho['Acao']) && NULL !== $menuFilho['Acao']) {
                        $arrMenu[$count][$countSon]['href'] = $menuFilho['Acao'];
                    }

                    if (!empty($menuFilho['MenuFilho']['noMenu'])) {
                        $arrMenu[$count][$countSon]['text'] = $menuFilho['MenuFilho']['noMenu'];
                    }

                    $countSon++;
                }
            }
            $count++;
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

    public function showView ($strView, $showMenuAndLayout = TRUE, $arrSet = NULL)
    {
        $app    = $this->_SIALApplication;
        $layout = sprintf("/application/layout/%s", $this->request()->getParam('m', 'get'));
        if ($showMenuAndLayout) {
            $app->raise($this::T_EVENT_CONTROLLER_ABSTRACT_SHOW_MENU);
            $app->render($layout);
        }

        if (is_array($arrSet) && !empty($arrSet)) {
            foreach ($arrSet as $key => $value) {
                $app->set($key, $value);
            }
        }

        $path  = DIRECTORY_SEPARATOR . strstr(dirname(dirname(preg_replace('/\\\/', '/', get_class($this)))), 'application');
        $path .= DIRECTORY_SEPARATOR . self::T_VIEW_HTML_PATH . DIRECTORY_SEPARATOR;
        $app->render($path . $strView);
    }
}
