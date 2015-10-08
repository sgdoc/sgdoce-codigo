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
 * Classe para Controller de Tipodoc
 *
 * @package    Auxiliar
 * @category   Controller
 * @name       Tipodoc
 * @version    1.0.0
 */
use Doctrine\DBAL\Query\QueryBuilder;

class Auxiliar_TipodocController extends \Core_Controller_Action_Crud
{
    /**
     * @var string
    */
    protected $_service = 'Tipodoc';

    /**
     * muda status do registro
     * @return boolean
     */
    public function switchStatusAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $data = $this->_getAllParams();
        $this->getService('Tipodoc')->switchStatus($data);
        $this->_helper->json(array('success'=>TRUE));
    }

    /**
     * método que faz pesquisa no banco para preencher o autocomplete
     * @return json
     */
    public function searchTipoDocumentoAction()
    {
        $term = $this->_getParam('query','');
        $res = $service = $this->getService()->searchTipoDocumento($term);
        $this->_helper->json($res);
    }

    /**
     * metodo que monta grid
     * @param string $params
     * @return QueryBuilder
     */
    public function getResultList($params)
    {
        $params = \Core_Dto::factoryFromData($params, 'search');

        return $this->getService()->listGrid($params);
    }

    /**
     * createAction
     */
    public function createAction()
    {
        parent::createAction();
        //Configura a opção DEFAULT para inAbreProcesso
        $this->view->data->setInAbreProcesso(FALSE);
    }

    /**
     * metodo que faz ordenação da grid
     * @return array
     */
    public function getConfigList()
    {
        $array = array(
                'columns' => array(
                        0 => array(
                                'alias' => 'td.noTipoDocumento'
                        ),
                        1 => array(
                                'alias' => 'td.inAbreProcesso'
                        ),
                        2 => array(
                                'alias' => 'td.stAtivo'
                        )
                )
        );

        return $array;
    }

    public function updateAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $data = $this->_getAllParams();
        $this->getService()->update($data);

        $this->getMessaging()->addSuccessMessage($this->_getMessageTranslate('MD002'));

        $this->_redirect("/auxiliar/tipodoc/");
    }
}
