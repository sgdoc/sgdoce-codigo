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

namespace Artefato\Service;
/**
 * Classe para Service de GrauAcesso
 *
 * @package  Minuta
 * @category Service
 * @name     GrauAcesso
 * @version  1.0.0
 */

class GrauAcessoArtefato extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:GrauAcessoArtefato';
    /**
     * Método que retorna pesquisa do banco para preencher combo
     * @return array
     */
    public function getGrauAcessoArtefato(\Core_Dto_Search $dtoSearch)
    {
        return $this->_getRepository()->getGrauAcessoArtefato($dtoSearch);
    }

    public function saveGrauAcessoArtefato($entity, $dto)
    {
         if($this->getEntityManager()->getRepository('app:GrauAcessoArtefato')
                                     ->hasGrauAcessoArtefato($entity, $dto)) {

            $entityGrauAcesso = new \Sgdoce\Model\Entity\GrauAcessoArtefato;

            $entityGrauAcesso->setSqGrauAcesso($this->_getRepository('app:GrauAcesso')->find($dto->getSqGrauAcesso()));
            $entityGrauAcesso->setSqArtefato($entity);
            $entityGrauAcesso->setDtAtribuicao(\Zend_Date::now());
            $entityGrauAcesso->setStAtivo(1);

            $this->getEntityManager()->persist($entityGrauAcesso);
            $this->getEntityManager()->flush();
        }


    }

    public function deleteGrauAcessoArtefato($dto)
    {
        $entity = $this->find($dto->getSqGrauAcessoArtefato());
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();

    }
}
