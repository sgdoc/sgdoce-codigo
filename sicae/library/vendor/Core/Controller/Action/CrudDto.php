<?php

/**
 * Copyright 2012 do ICMBio
 *
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

/**
 * Base para as Controllers do framework que implementem CRUD
 *
 * @package    Core
 * @subpackage Controller
 * @subpackage Action
 * @name       Crud
 * @category   Controller
 */
class Core_Controller_Action_CrudDto extends Core_Controller_Action_Base
{
    protected $_messageCreate = 'MD001';
    protected $_messageEdit = 'MD002';
    protected $_messageDelete = 'MD003';
    protected $_dataSave = NULL;

    /**
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => '',
        'mapping' => array()
    );

    /**
     * Action que realiza a pesquisa
     */
    public function listAction()
    {
        $this->getHelper('layout')->disableLayout();

        $params = $this->_getAllParams();
        $configArray = $this->getConfigList();

        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);

        $this->view->dto = Core_Dto::factoryFromData($params, 'search');

        $this->view->result = $this->getResultList($this->view->dto);
    }

    /**
     * Método para preencher os dados da pesquisa
     * @param Core_Dto_Search $dto Dados da requisição
     */
    public function getResultList(\Core_Dto_Search $dto)
    {
        return $this->getService()->listGrid($dto);
    }

    /**
     * Retorna array de configuração da pesquisa
     */
    public function getConfigList()
    {
        trigger_error('Este método tem que ser implementado de acordo com a Controller', E_USER_ERROR);
    }

    public function deleteAction()
    {
        $this->getService()->delete($this->_getParam('id'));
        $this->getService()->finish();

        if ($this->_messageDelete) {
            $this->getMessaging()->addSuccessMessage($this->_getMessageTranslate($this->_messageDelete));
        }

        return $this->_redirectActionDefault('index');
    }

    public function createAction()
    {
        if ($this->getHelper('persist')->has('data')) {
            $this->view->data = $this->getHelper('persist')->get('data');
        } else {
            $this->view->data = $this->getService()->getDto();
        }
    }

    public function editAction()
    {
        $id = $this->_getParam('id');

        if (!$id) {
            throw new RuntimeException('É necessário passar o ID');
        }

        if ($this->getHelper('persist')->has('data')) {
            $this->view->data = $this->getHelper('persist')->get('data');
        } else {
            $this->view->data = $this->getService()->find($id);
        }
    }

    public function saveAction()
    {
        if (!$this->_request->isPost()) {
            throw new RuntimeException('A requisição deve ser POST');
        }

        $this->_dataSave = $this->_save();
        $this->getService()->finish();
        $this->_addMessageSave();
        return $this->_redirectActionDefault('index');
    }

    protected function _save()
    {
        $data = $this->_request->getPost();

        if (isset($this->_optionsDtoEntity['mapping'])) {
            foreach ((array) $this->_optionsDtoEntity['mapping'] as $key => $value) {
                $data[$key] = (
                    isset($data[$key]) && 
                    empty($data[$key]) ? 
                        NULL : 
                        (isset($data[$key]) ? $data[$key] : NULL)
                );
            }
        }

        $dto = Core_Dto::factoryFromData($data, 'entity', $this->_optionsDtoEntity);
        $this->_persistDataError['data'] = $dto;
        $this->_persistDataErrorSave($dto);
        $args = (array) $this->_factoryParamsExtrasSave($data);
        array_unshift($args, $dto);
        return call_user_func_array(array($this->getService(), 'save'), $args);
    }

    protected function _persistDataErrorSave($dto)
    {

    }

    protected function _factoryParamsExtrasSave($data)
    {
        return array();
    }

    protected function _getCodeMessageSave()
    {
        $referer = $this->_request->getHeader('referer');
        $code = NULL;

        if (strpos($referer, 'create')) {
            $code = $this->_messageCreate;
        } else if (strpos($referer, 'edit')) {
            $code = $this->_messageEdit;
        }

        return $code;
    }

    protected function _addMessageSave()
    {
        $code = $this->_getCodeMessageSave();

        if ($code) {
            $this->getMessaging()->addSuccessMessage($this->_getMessageTranslate($code));
        }
    }

    /**
     * Metodo generico para combo
     * @param array $criteria
     * @return type
     */
    public function getComboDefault(array $criteria = array())
    {
        return $this->getService()->getComoboDefault($criteria);
    }

}
