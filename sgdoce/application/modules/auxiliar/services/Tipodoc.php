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
 * Classe para Service de Tipodoc
 *
 * @package  Auxiliar
 * @category Service
 * @name     Tipodoc
 * @version  1.0.0
 */
use Doctrine\Common\Util\Debug;
use Sgdoce\Model\Entity\Artefato;
use Sgdoce\Model\Repository\TipoDocumento;

class Tipodoc extends \Core_ServiceLayer_Service_Crud
{
    /**
    * @var string
    */
    protected $_entityName = 'app:TipoDocumento';

    /**
     * método que implementa as regras de negócio
     */
    public function preSave($service)
    {
        $hasErrors = FALSE;
        $repository = $this->getEntityManager()->getRepository($this->_entityName);

        if ($repository->hasTipoDocumento($this->_data['noTipoDocumento'], $this->_data['sqTipoDocumento'])) {
            $hasErrors = TRUE;
            $msg = \Core_Registry::getMessage()->translate('MN046');
            $this->getMessaging()->addErrorMessage($msg);
        }
        if (empty($this->_data['noTipoDocumento'])) {
            $hasErrors = TRUE;
            $msg = \Core_Registry::getMessage()->translate('MN003');
            $msg = str_replace('<campo>', 'Tipo de Documento', $msg);
            $this->getMessaging()->addErrorMessage($msg);
        }
        if (!$this->_data['inAbreProcesso'] && $this->_data['inAbreProcesso'] !== '0') {
            $hasErrors = TRUE;
            $msg = \Core_Registry::getMessage()->translate('MN003');
            $msg = str_replace('<campo>', 'Abre Processo', $msg);
            $this->getMessaging()->addErrorMessage($msg);
        }
        if($hasErrors){
            $this->getMessaging()->dispatchPackets();
            throw new \Core_Exception_ServiceLayer_Verification();
        }
    }

    /**
     *método que preenche combo tipo documento
     *@return string
     */
    public function listItems()
    {
        return $this->_getRepository()->listItemsTipoDocumento();
    }

    /**
     * método para deletar sequencia
     * @return boolean
     */
    public function delete($sequence)
    {
        return $this->getEntityManager()->getRepository($this->_entityName)->deActivate($sequence);
    }

    /**
     * método que pesquisa tipo de documento no banco para preencher combo
     * @param string $term
     */
    public function searchTipoDocumento($term)
    {
        return $this->getEntityManager()->getRepository($this->_getEntityName())->searchTipoDocumento($term);
    }

    /**
     * método que retorna lista de parametros para grid
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
     * método que muda status do registro
     * @param array $data
     */
    public function switchStatus(array $data)
    {
        $entity = $this->find($data['sqTipoDocumento']);
        $entity->setStAtivo($data['stAtivo']);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function update($data)
    {
        /** @var \Sgdoce\Model\Entity\TipoDocumento $entity */
        $entity = $this->find($data['sqTipoDocumento']);
        $entity->setStAtivo(FALSE);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        /** @var \Sgdoce\Model\Entity\TipoDocumento $newEntity */
        $newEntity = $this->_newEntity('app:TipoDocumento');
        $newEntity->setNoTipoDocumento($data['noTipoDocumento']);
        $newEntity->setStAtivo(TRUE);
        $newEntity->setInAbreProcesso($data['inAbreProcesso']);

        $this->getEntityManager()->persist($newEntity);
        $this->getEntityManager()->flush();

        $this->getServiceLocator()->getService('Sequnidorg')->alteraSequencial($entity,$newEntity);
        $this->alteraTipoDocArtefato($entity,$newEntity);
        return TRUE;
    }

    /**
     * Alterar todos os artefatos para o novo tipo de documento
     * @param $oldTipoDoc
     * @param $newTipoDoc
     */
    public function alteraTipoDocArtefato($oldTipoDoc, $newTipoDoc)
    {
        $arrArtefatos = $this->_getRepository('app:Artefato')->findBySqTipoDocumento($oldTipoDoc->getSqTipoDocumento());
        foreach($arrArtefatos as $artefato){
            /** @var Artefato $artefato */
            $artefato->setSqTipoDocumento($newTipoDoc);

            $this->getEntityManager()->persist($artefato);
            $this->getEntityManager()->flush($artefato);
        }
    }
}
