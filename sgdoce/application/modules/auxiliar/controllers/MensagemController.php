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
 * Classe para Controller de Mensagem
 *
 * @category Controller
 * @package  Auxiliar
 * @name         Mensagem
 * @version  1.0.0
 */
class Auxiliar_MensagemController extends \Core_Controller_Action_Crud
{
    /**
     * @var string
     */
    protected $_service = 'Mensagem';

    /**
     * Ação de listagem de Mensagens
     */
    public function indexAction()
    {
        $this->view->items = $this->getService('TipoDoc')->listItems();
    }

    /**
     * Ação Criação de Mensagem
     */
    public function createAction()
    {
        parent::createAction();
        $this->view->actionName = $this->getRequest()->getActionName();
        $this->view->items = $this->getService('TipoDoc')->listItems();
    }

    /**
     * Ação para manutenção de mensagem geral
     */
    public function geralAction()
    {
        $this->view->data  = $this->getService()->getMensagemGeral();
    }

    /**
     * Ação para trocar status de mensagem
     */
    public function switchStatusAction()
    {
        $params = $this->_getAllParams();
        $result = $this->getService()->switchStatus($params);
        $this->_helper->json($result);
    }

    /**
     * Ação para encontrar uma mensagem ativa do mesmo
     * tipo de documento e assunto
     */
    public function findMensagemAtivaAction()
    {
        $params = $this->_getAllParams();
        $result = $this->getService()->findMessageAtiva($params);
        $this->_helper->json(array($result));
    }

    /**
     * Ação para edição de mensagem
     */
    public function editAction()
    {
        parent::editAction();
        $this->view->actionName = $this->getRequest()->getActionName();
        $this->view->items = $this->getService('TipoDoc')->listItems();
    }

    /**
     * Ação para busca de mensagem
     */
    public function findmessageAction()
    {
        $params = $this->_getAllParams();
        $result = $this->getService()->findMessage(new \Core_Dto_Search($params));
        $this->_helper->json($result);
    }

    /**
     * Ação para preencher os dados da pesquisa
     * @param  array $params Dados da requisição
     */
    public function getResultList($params)
    {
        $params = \Core_Dto::factoryFromData($params, 'search');

        return $this->getService()->listGrid($params);
    }

    /**
     * Retorna array de configuração da pesquisa
     */
    public function getConfigList()
    {
        $array = array(
            'columns' => array(
                0 => array(
                    'alias' => 'td.noTipoDocumento'
                ),
                1 => array(
                    'alias' => 'a.txAssunto'
                ),
                2 => array(
                    'alias' => 'm.txMensagem'
                ),
                3 => array(
                    'alias' => 'm.stMensagemAtiva'
                ),
            )
        );

        return $array;
    }

}