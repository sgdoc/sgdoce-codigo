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
 * Classe para Controller de Carimbo
 *
 * @category Controller
 * @package  Auxiliar
 * @name         Carimbo
 * @version  1.0.0
 */
class Auxiliar_CarimboController extends \Core_Controller_Action_Crud
{
    /**
     * @var string
     */
    protected $_service = 'Carimbo';

    /**
     * Ação de Criação de Carimbos
     */
    public function createAction()
    {
        parent::createAction();
        $this->view->items = $this->getService('TipoArtefato')->listItems();
    }

    /**
     * Ação de listagem de Carimbos
     */
    public function indexAction()
    {
        $this->view->items = $this->getService()->listItems();
    }

    /**
     * Ação de edução de Carimbos
     */
    public function editAction()
    {
        parent::editAction();
        $this->view->items = $this->getService('TipoArtefato')->listItems();
    }

    /**
     * Ação de visualização de carimbos
     */
    public function visualizarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->view->data = $this->getService()->find($this->_request->get('id'));
    }

    /**
     * Ação que trás o resultado da pesquisa
     * @param  array $params Dados da requisição
     * @return array         Dados para preenchimento de tela
     */
    public function getResultList($params)
    {
        $params = \Core_Dto::factoryFromData($params, 'search');

        return $this->getService()->listGrid($params);
    }

    /**
     * Configuração da pesquisa (grid)
     */
    public function getConfigList()
    {
        $array = array(
            'columns' => array(
                0 => array(
                    'alias' => 'c.noCarimbo'
                ),
            )
        );

        return $array;
    }

    /**
     * Ação para download de anexos
     */
    public function downloadAnexoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $resultAnexo = $this->getService()->find($this->_request->get('id'));
        $registry    = \Core_Registry::get('configs');
        $path = current(explode ('application', __DiR__))
            . 'data'           . DIRECTORY_SEPARATOR
            . 'upload'         . DIRECTORY_SEPARATOR
            . 'carimbo' . DIRECTORY_SEPARATOR;
        $address     = $path . $resultAnexo->getDeCaminhoArquivo();

        $fsize       = filesize($address);
        $nameArquivo = basename($address);
        $extensao    = substr($nameArquivo, strpos($nameArquivo, ".") + 1);

        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",FALSE); // required for certain browsers
        header("Content-Type: image/png");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".$fsize);
        ob_clean();
        flush();
        readfile( $address );
    }
}