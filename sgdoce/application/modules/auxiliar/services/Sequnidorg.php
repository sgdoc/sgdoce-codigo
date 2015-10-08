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
 * Classe para Service de Sequnidorg
 *
 * @package	 Auxiliar
 * @category	 Service
 * @name		 Sequnidorg
 * @version	 1.0.0
 */
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Mapping\Entity;

class Sequnidorg extends \Core_ServiceLayer_Service_Crud
{
    /**
     * @var string
     */
    protected $_entityName = 'app:SequencialArtefato';

    /**
     * método que seta os objetos
     */
    public function setOperationalEntity($entityName = NULL)
    {
        $this->_data['sqUnidadeOrg'] = $this->_createEntityManaged(
            array('sqUnidadeOrg' => $this->_data['sqPessoa']),'app:VwUnidadeOrg');
        $this->_data['sqTipoArtefato'] = $this->_createEntityManaged(
            array('sqTipoArtefato' => $this->_data['sqTipoArtefato']),'app:TipoArtefato');

        if (!empty($this->_data['sqTipoDocumento'])) {
            $this->_data['sqTipoDocumento'] = \Zend_Filter::filterStatic($this->_data['sqTipoDocumento'], 'null');
            $this->_data['sqTipoDocumento'] = $this->_createEntityManaged(
                array('sqTipoDocumento' => $this->_data['sqTipoDocumento']),'app:TipoDocumento');
        }
    }

    /**
     * método que retira pontos de campos numéricos antes de salvar
     * @return array $data
     */
    public function filterSave($data)
    {
        $data['coUorg']       = (int) str_replace('.', '', $data['coUorg']);
        $data['nuSequencial'] = (int) str_replace('.', '', $data['nuSequencial']);
        $data['nuAno']        = (int) str_replace('.', '', $data['nuAno']);

        return $data;
    }

    /**
     * método que valida a criação de um novo registro
     */
    public function preSave ($service)
    {

        if ($this->_data['nuSequencial'] === NULL) {
            throw new \Core_Exception_ServiceLayer("MN003"); //o campo é de preenchimento obrigatório
        }

        $processo = ($this->_data['sqTipoArtefato']->getSqTipoArtefato() == \Core_Configuration::getSgdoceTipoArtefatoProcesso());

        if ($this->_data['nuSequencial'] != 0 && !$processo) {
            $sequencial = $this->hasSequencialArtefato($this->_data);
            if ($sequencial){
                $anterior = $sequencial->getNuSequencial();
                $msg = \Core_Registry::getMessage()->translate('MN074');
                $msg = str_replace('<proximo>', $anterior + 1, $msg);
                $msg = str_replace('<anterior>', $anterior, $msg);

                throw new \Core_Exception_ServiceLayer($msg);
            }else{
                return FALSE;
            }
        }
    }

    /**
     * método que implementa grid
     * @param array $params
     * @return array $result
     */
    public function listGrid(\Core_Dto_Search $params)
    {
        $repository = $this->getEntityManager()->getRepository($this->_entityName);
        $result = $repository->searchPageDto('listGrid', $params);

        // caso a unidade possua numero de NUP então visualiza o sequencia de processo
        if ($params->getNoPessoa() != '') {
            $entityPessoa = $this->getServiceLocator()->getService('VwUnidadeOrg')->find($params->getNoPessoa());
            
            if ($entityPessoa && $entityPessoa->getNuNup() != NUll ) {
                $seqProcesso = $repository->findOneBy(
                    array('sqTipoArtefato' => \Core_Configuration::getSgdoceTipoArtefatoProcesso(),'nuAno' => $params->getNuAno(), 'sqUnidadeOrg' => $entityPessoa->getSqUnidadeOrg())
                );
                if (!$seqProcesso) {
                    $sqTipoArtefato = $this->_getRepository('app:TipoArtefato')->find(\Core_Configuration::getSgdoceTipoArtefatoProcesso());
                    $seqProcesso = new \Sgdoce\Model\Entity\SequencialArtefato();
                    $seqProcesso->setSqTipoArtefato($sqTipoArtefato);
                    $seqProcesso->setNuAno($params->getNuAno());
                    $seqProcesso->setSqUnidadeOrg($entityPessoa);
                    $seqProcesso->setNuSequencial(0);

                    $this->getEntityManager()->persist($seqProcesso);
                    $this->getEntityManager()->flush();
                }

                if ($seqProcesso != NULL) {
                    $result['data']->append($seqProcesso->toArray());
                }
            }
        }
        return $result;
    }

    /**
     * método que implementa pesquisa do autocomplete
     * @param array $params
     * @return NULL
     */
    public function searchUnidadesOrganizacionais ($params)
    {
        if (isset($params['query']) && $params['query']){
            return $repository = $this->getEntityManager()
                                      ->getRepository('app:VwUnidadeOrg')
                                      ->searchUnidadesOrganizacionais($params);
        }
        return NULL;
    }

    /**
     * método que implementa pesquisa do autocomplete
     * @param array $params
     * @return NULL
     */
    public function findBy (array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
    	$return = $this->_getRepository('app:SequencialArtefato')->findBy($criteria,$orderBy,$limit);
    	if(count($return) > 0){
			return $return[0];
    	}
    	return NULL;
    }

    /**
     * adiciona um novo numero sequencial se nao existir
     * @param array $params
     * @return NULL
     */
    public function create ($params) {
        $entidade = $this->save($params);
        $this->getMessaging()->retrievePackets();
        return $entidade;
    }

    /**
     * busca o sequencial de acordo com os parametros informados
     * @param array $params
     * @return SequencialArtefato
     */
    public function getSequencialPorUnidade($params) {
        $criteria = array(
                    'sqUnidadeOrg' => $params['sqUnidadeOrg'],
                    'sqTipoArtefato' => \Core_Configuration::getSgdoceTipoArtefatoDocumento(),
                    'sqTipoDocumento' => !empty($params['sqTipoDocumento']) ? $params['sqTipoDocumento'] : NULL,
                    'nuAno' => date('Y')
                );

        $sequencial = $this->findBy($criteria);
        // se o sequencial nao estiver cadastrado deve inserir um novo registro
        if ($sequencial == null) {
            $criteria['coUorg'] = '';
            $criteria['nuSequencial'] = 0;
            $criteria['sqPessoa'] = $criteria['sqUnidadeOrg'];
            $criteria['nuSequencialHidden'] = 0;
            $sequencial = $this->create($criteria);
        }
        return $sequencial;
    }

    /**
     * verifica disponibilidade do numero sequencial
     * @param array $params
     * @return Bollean
     */
    public function numeroSequencialDisponivel($params) {

        $criteria = array(
                'sqUnidadeOrg' => $params['sqUnidadeOrg'],
                'sqTipoArtefato' => \Core_Configution::getSgdoceTipoArtefatoDocumento(),
                'sqTipoDocumento' => $params['sqTipoDocumento'],
                'nuAno' => date('Y')
        );

        $sequencial = $this->findBy($criteria);
        // se o sequencial nao estiver cadastrado deve inserir um novo registro
        if ($sequencial == null) {
            $criteria['coUorg'] = '';
            $criteria['nuSequencial'] = 0;
            $criteria['sqPessoa'] = $criteria['sqUnidadeOrg'];
            $criteria['nuSequencialHidden'] = 0;
            $sequencial = $this->create($criteria);
        }
        return $sequencial;
    }

    /**
     * Verifica a existencia do proximo sequencial para o artefato
     * @param  $params
     * @return array
     */
    public function hasSequencialArtefato($params)
    {
        $params['sqUnidadeOrg']     = $params['sqUnidadeOrg']->getSqUnidadeOrg();
        $params['sqTipoArtefato']   = \Core_Configuration::getSgdoceTipoArtefatoDocumento();
        if (!empty($params['sqTipoDocumento'])) {
            $params['sqTipoDocumento']  = $params['sqTipoDocumento']->getSqTipoDocumento();
        }
        $params['action'] = 'alterar-sequencial';
        $sequencial = $this->getServiceLocator()->getService('Artefato')->recuperaProximoNumeroArtefato($params);

        //se for igual o proximo numero do sequencial esta liberado
        if ((int)$params['nuSequencial'] == $sequencial->getNuSequencial()) {
            return FALSE;
        }

        return $sequencial;
    }

    public function searchNup($unidadeorg)
    {
        return $this->_getRepository('app:VwUnidadeOrg')->find($unidadeorg);
    }

    public function alteraSequencial($tipoDoc, $newTipoDoc)
    {
        //Recupera sequenciais do tipo de documento antigo
        $sequenciais = $this->_getRepository($this->_entityName)->findBySqTipoDocumento($tipoDoc->getSqTipoDocumento());

        $count = 0;
        //percorre todos os sequenciais do tipo de documento antigo
        foreach ($sequenciais as $oldSequencial) {
            $newSequencial = clone $oldSequencial;
            $newSequencial->setSqSequencialArtefato(NULL);
            $newSequencial->setSqTipoDocumento($newTipoDoc);

            $this->getEntityManager()->persist($newSequencial);
            $this->getEntityManager()->flush($newSequencial);
            $count++;
        }
        if ($count == 20) {
            $this->getEntityManager()->clear();
            $count = 0;
        }

        return TRUE;
    }
}
