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
 * Classe para Controller de Prioridade
 *
 * @package  Auxiliar
 * @category Controller
 * @name     Prioridade
 * @version  1.0.0
  */
use Doctrine\ORM\QueryBuilder;

class Auxiliar_PrioridadeController extends \Core_Controller_Action_Crud
{
    /**
     * @var string
     */
    protected $_service = 'Prioridade';

    /**
     * método que inicia index
     */
    public function indexAction()
    {
        parent::indexAction();
        $this->view->items = $this->getService('Prioridade')->listItems();
    }

    /**
     * método que monta tela criação
     */
    public function createAction()
    {
        parent::createAction();
        $this->view->items = $this->getService('Prioridade')->listItems();
    }

    /**
     * método que monta tela de edição
      */
    public function editAction()
    {
        parent::editAction();
        $this->view->items = $this->getService('Prioridade')->listItems();
    }

    /**
     * Este metodo retorna o resultado do pesquisa para o preenchimento do listGrid
     * @param string $params
     * @return QueryBuilder
     */
    public function getResultList($params)
    {
        $params = \Core_Dto::factoryFromData($params, 'search');

        return $this->getService()->listGrid($params);
    }

    /**
     * método que ordena grid
     * @return array
     */
    public function getConfigList()
    {
        $array = array(
            'columns' => array(
                0 => array('alias' => 'tp.txTipoPrioridade'),
                1 => array('alias' => 'p.noPrioridade')
            )
        );

        return $array;
    }
}
