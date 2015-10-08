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
namespace ModeloMinuta\Service;
/**
 * Classe para Service de ModeloMinuta
 *
 * @package  Minuta
 * @category Service
 * @name     ModeloMinuta
 * @version  1.0.0
 */

class ModeloMinuta extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * Variavel para receber o nome da entidade
     * @var string
     * @access protected
     * @name $_entityName
     */
    protected $_entityName = 'app:ModeloDocumento', $_entity;

    /**
     * Valida o Documento de Minuta
     * @param object $entity
     * @return string/NULL
     */
    public function validaDocumento($entity)
    {
        $repository = $this->_getRepository($this->_entityName);
        $result     = $repository->validaDocumento($entity,TRUE);

        if(count($result)){
            $repository = $this->_getRepository('app:ModeloDocumentoCampo');
            $entity->setSqModeloDocumento($result[0]['sqModeloDocumento']);
            $resultModelo     = $repository->getCampoModeloDocumento($entity);
            $return['sqModeloDocumento'] = $result[0]['sqModeloDocumento'];
            $return['sqPadraoModeloDocumento'] = $resultModelo[0]['sqPadraoModeloDocumento'];
            return $return;
        }else{
            $return['sqModeloDocumento'] = '';
            return $return;
        }

    }

    /**
     * Implementa as regras de negócio
     * @param object $entity
     * @param object $dto
     * @return string/NULL
     */
    public function preSave($entity, $dto = NULL)
    {
        $entity->setInAtivo(TRUE);
        if(!$entity->getSqPosicaoTipoDocumento()->getSqPosicaoTipoDocumento()){

            $entityPosicao = $this->_getRepository('app:PosicaoTipoDocumento')
                ->find(\Core_Configuration::getSgdoceSqPosicaoTipoDocEsquerda());
            $entity->setSqPosicaoTipoDocumento($entityPosicao);
        }
        if(!$entity->getSqPosicaoData()->getSqPosicaoData()){

            $entityData = $this->_getRepository('app:PosicaoData')
                ->find(\Core_Configuration::getSgdoceSqPosicaoDataLadoDoTipo());
            $entity->setSqPosicaoData($entityData);
        }
        $entity->getSqPosicaoTipoDocumento();
        $this->updateExistente($entity);
    }

    /**
     * Atualiza o registro existente para inativo
     * @param Sgdoce\Model\Entity\ModeloDocumento $entity
     * @return boolean
     */
    public function updateExistente($entity) {
        if ($entity->getSqModeloDocumento()) {
            $entity->setInAtivo(FALSE);
            $this->getEntityManager()->persist($entity);
        }
        return $entity;
    }

    /**
     * configura a grid principal
     * @param \Core_Dto_Search $dtoSearch
     * @return array
     */
    public function listGrid(\Core_Dto_Search $dtoSearch)
    {
        $repository = $this->_getRepository($this->_entityName);
        $result     = $repository->searchPageDto('listGrid', $dtoSearch);

        return $result;
    }

    /**
     * Localiza um modelo de documento
     * @return array
     */
    public function findModelo(\Core_Dto_Abstract $dtoSearch)
    {
        $repository = $this->_getRepository($this->_entityName);
        $result     = $repository->findModelo($dtoSearch);

        return $result;
    }

    /**
     * Realiza save extras
     */
    public function postSave($entity, $dto = NULL)
    {
        $this->saveModeloDocumentoCampo($entity, $dto);
    }

    /**
     * Realiza o post save dos campos do modelo minuta
     */
    protected function saveModeloDocumentoCampo(\Sgdoce\Model\Entity\ModeloDocumento $entity, $dto)
    {
        foreach ($dto as $dtoAux) {
            $entityModeloDocCam = $dtoAux->getEntity();

            $criteria = array('sqPadraoModeloDocumentoCam' => $entityModeloDocCam->getSqPadraoModeloDocumentoCam());
            $entityAux = $this->_getRepository('app:PadraoModeloDocumentoCampo')->findOneBy($criteria);

            $entityModeloDocCam->setSqModeloDocumento($entity);
            $entityModeloDocCam->setSqPadraoModeloDocumentoCam($entityAux);
            $this->getEntityManager()->detach($entityModeloDocCam);
            $this->getEntityManager()->persist($entityModeloDocCam);
        }
    }

    /**
     * Atualiza o status do modelo para deletado
     */
    public function deleteModelo(\Core_Dto_Search $dtoSearch)
    {
        $repository = $this->_getRepository($this->_entityName);
        $modeloDocumento = $repository->find($dtoSearch->getSqModeloDocumento());
        $modeloDocumento->setInAtivo(FALSE);
        $this->getEntityManager()->persist($modeloDocumento);
        $this->getEntityManager()->flush($modeloDocumento);
    }

    /**
     * Método que retorna o grau de acesso do artefato.
     * @return int
     */
    public function getGrauAcesso(\Core_Dto_Search $dtoSearch){
        $repository = $this->_getRepository($this->_entityName);
        $result =  $repository->find($dtoSearch->getSqModeloDocumento());
        return $result->getSqGrauAcesso()->getSqGrauAcesso();
    }
}
