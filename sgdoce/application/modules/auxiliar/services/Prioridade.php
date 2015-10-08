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
namespace Auxiliar\Service;
/**
 * Classe para Service de Prioridade
 *
 * @package  Auxiliar
 * @category Service
 * @name     Prioridade
 * @version  1.0.0
 */
class Prioridade extends \Core_ServiceLayer_Service_Crud
{
    /**
     * @var string
     */
    protected $_entityName = 'app:TipoPrioridade';

    /**
     * Método que popula os objetos para serem salvos no banco
     * @return object
     */
    public function setOperationalEntity($entityName = NULL)
    {
        $this->_data['sqPrioridade'] = $this->_createEntityManaged(
                                                                 array('sqPrioridade' => $this->_data['sqPrioridade']),
                                                                 'app:Prioridade');
        $this->_data['stRegistroAtivo'] = TRUE;
    }

    /**
     * método que implementa as regras de negócio
     */
    public function preSave($service)
    {
        $repository = $this->getEntityManager()->getRepository($this->_entityName);
        if ($repository->hasTipoPrioridade($this->_data['txTipoPrioridade']
                                          ,$this->_data['sqTipoPrioridade']
                                          ,$this->_data['sqPrioridade'])) {
            throw new \Core_Exception_ServiceLayer('MN066');
        }
        if (empty($this->_data['txTipoPrioridade'])) {
            throw new \Core_Exception_ServiceLayer('O campo Tipo de prioridade é de preenchimento obrigatório.');
        }
        if (empty($this->_data['sqPrioridade'])) {
            throw new \Core_Exception_ServiceLayer('O campo Prioridade é de preenchimento obrigatório.');
        }
    }

    /**
     * método que retorna pesquisa do banco para preencher combo
     * @return array
     */
    public function listItems()
    {
        return $this->getEntityManager()->getRepository('app:Prioridade')->listPrioridade();
    }

    /**
     * método que retorna lista de parametros da grid
     * @param string $params
     * @return array
     */
    public function listGrid(\Core_Dto_Search $params)
    {
        $repository = $this->getEntityManager()->getRepository($this->_entityName);
        $result     = $repository->searchPageDto('listGrid', $params);

        return $result;
    }

    /**
     * método que deleta sequência
     * @param integer $sequence
     * @return boolean
     */
    public function delete($sequence)
    {
        return $this->getEntityManager()->getRepository($this->_entityName)->deActivate($sequence);
    }

}
