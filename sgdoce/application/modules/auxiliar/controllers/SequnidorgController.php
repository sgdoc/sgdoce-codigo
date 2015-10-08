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
 * Classe para Controller de Sequnidorg
 *
 * @package	 Auxiliar
 * @category Controller
 * @name	 Sequnidorg
 * @version	 1.0.0
 */
class Auxiliar_SequnidorgController extends \Core_Controller_Action_Crud
{
    /**
     * @var string
     */
    protected $_service = 'Sequnidorg';

    /**
     * Este metodo é responsavel pela pesquisa do autocomplete.
     */
    public function searchUnidadesOrganizacionaisAction()
    {
        $params = $this->_getAllParams();
        $res = $this->getService()->searchUnidadesOrganizacionais($params);
        $this->getHelper('json')->sendJson($res);
    }

    /**
     * Este metodo restorna o resultado do pesquisa para o preenchimento do listGrid
     * @param array $params
     * @return JSon
     *
     */
    public function getResultList($params)
    {
        $params = \Core_Dto::factoryFromData($params, 'search');

        return $this->getService()->listGrid($params);
    }

    /**
     * Este metodo é responsavel pela a ordenação da listGrid
     * @return array
     */
    public function getConfigList()
    {
        $array = array(
                'columns' => array(
                        0 => array('alias' => 'a.nuAno'),
                        1 => array('alias' => 'td.noTipoDocumento'),
                        2 => array('alias' => 'a.nuSequencial')
                )
        );

        return $array;
    }

    /**
     * Este metodo é resonsavel pela inclusão da combobox contendo os tipos de documentos
     */
    public function editAction()
    {
        parent::editAction();
        if ($this->view->data->getSqTipoArtefato()->getSqTipoArtefato() == 2) {
            $this->view->unidadeOrg = $this->getService('UnidadeOrg')->find($this->getRequest()->getParam('unidade'));
            $this->render('form-processo');
        } else {
            $this->view->items = $this->getService('TipoDoc')->listItems();
        }
        $this->view->gridLength = $this->getRequest()->getParam('gridLength');
    }

    /**
     * Cria um novo sequencial quando nao existe e direciona o fluxo para editar
     */
    public function createAction()
    {
        $data = array(
                    'sqUnidadeOrg' => $this->getRequest()->getParam('unidade'),
                    'sqPessoa' => $this->getRequest()->getParam('unidade'),
                    'nuAno' => $this->getRequest()->getParam('ano'),
                    'sqTipoArtefato' => \Core_Configuration::getSgdoceTipoArtefatoDocumento(),
                    'nuSequencial' => 0,
                    'sqTipoDocumento' => $this->getRequest()->getParam('tipo'),
                    'coUorg' => $this->getRequest()->getParam('unidade'),
                );
        $id = $this->getService()->create($data)->getSqSequencialArtefato();
        $this->_redirect("/auxiliar/sequnidorg/edit/id/{$id}/unidade/{$data['sqUnidadeOrg']}");

    }

    /**
     * busca o sequencial de acordo como s parametros informados
     */
    public function buscarSequencialAction()
    {
        $params = $this->_getAllParams();
        $params = array(
                    'sqTipoDocumento' => $params['sqTipoDocumento'],
                    'sqUnidadeOrg' => $params['sqUnidadeOrg'],
                    'nuAno' => $params['nuAno']
                 );

        $res = $this->getService()->findBy($params);
        $result = NULL;
        if($res != NULL){
            $result = $res->toArray();
            $result['sqUnidadeOrg'] = $res->getSqUnidadeOrg()->toArray();
        }
        $this->getHelper('json')->sendJson($result);
    }

    public function saveAction()
    {
        $data       = $this->_getAllParams();
        try {
//            if ($data['sqTipoArtefato'] == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
//                unset($data['sqUnidadeOrg']);
//                unset($data['sqPessoa']);
//            }

            $this->getService()->save($data);
            $gwmsg = $this->getService()->getMessaging();
            $pkt = $gwmsg->retrievePackets('Service');
            if ($pkt) {
    	        foreach ($pkt->getMessages('success') as $message) {
    	             $this->getMessaging()->addSuccessMessage($message);
    	        }
            }
        }
        catch (Exception $e) {
            $this->getMessaging()->addErrorMessage($e->getMessage());
            $this->routedData->data = $this->getService()->getData() + $data;
            $this->_redirect("/auxiliar/sequnidorg/edit/id/{$data['sqSequencialArtefato']}/unidade/{$data['sqUnidadeOrg']}/gridLength/{$data['gridLength']}");
        }
        $this->getMessaging()->dispatchPackets();
        $this->_redirect("/auxiliar/sequnidorg/index/gridLength/{$data['gridLength']}");
    }

    public function searchNupAction()
    {
        $this->getHelper('layout')->disableLayout();
        $unidadeOrg = $this->_getParam('unidadeOrg');

        $result = $this->getService()->searchNup($unidadeOrg);

        $retorno['success'] = FALSE;
        if ($result && $result->getNuNup()){
            $retorno['success'] = TRUE;
            $retorno['nup'] = $result->getNuNup();
        }
        return $this->getHelper('json')->sendJson($retorno);

    }
}
