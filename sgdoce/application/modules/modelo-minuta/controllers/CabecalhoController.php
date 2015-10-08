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
 * Classe para Controller de ModeloMinuta
 *
 * @package    Minuta
 * @category   Controller
 * @name       ModeloMinuta
 * @version    1.0.0
 */

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Classe para Controller de ModeloMinuta
 *
 * @package    Minuta
 * @category   Controller
 * @name       ModeloMinuta
 * @version    1.0.0
 */
class ModeloMinuta_CabecalhoController extends \Core_Controller_Action_CrudDto
{
    /**
     * Variavel para receber o nome da service
     * @var    string
     * @access protected
     * @name   $_service
     */
    protected $_service = 'Cabecalho';

    /**
     * retorna dados da grid
     */
    public function getResultListCabecalho(\Core_Dto_Search $dtoSearch)
    {
        return $this->getService()->listGridCabecalho($dtoSearch);
    }

    /**
     * metodo que ordena grid
     */
    public function getConfigListCabecalho()
    {
        $array = array('columns' => array(0 => array('alias' => 'c.noCabecalho')));

        return $array;
    }

    /**
     * Action que realiza a pesquisa
     */
    public function listCabecalhoAction()
    {
        $this->getHelper('layout')->disableLayout();
        $params = $this->_getAllParams();
        $configArray = $this->getConfigListCabecalho();
        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getResultListCabecalho($this->view->dto);
        $entityCabecalho = $this->getService('ModeloMinuta')->find($params['sqModeloDocumento']);
        if($entityCabecalho){
            $this->view->cabecalho = $entityCabecalho->getSqCabecalho()->getSqCabecalho();
        }

    }

    /**
     * Action que realiza a pesquisa
     */
    public function viewCabecalhoAction()
    {
        $this->view->data = $this->getService()->find($this->_getAllParams());
        $this->_helper->layout->disableLayout();
        return TRUE;

    }
}