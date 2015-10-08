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
 * Classe para Service de Fecho
 *
 * @package  Minuta
 * @category Service
 * @name     Vocativo
 * @version  1.0.0
 */

class TelefoneSgdoce extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:TelefoneSgdoce';

    public function saveTelefonePessoaRodape($dto,$entityAux)
    {
        $filter = new \Zend_Filter();

        $criteria = array('sqPessoaSgdoce' => $entityAux->getSqPessoaSgdoce());
        $entityTelefoneArtefato = $this->_getRepository('app:TelefoneSgdoce')->findOneBy($criteria);

        if (!$entityTelefoneArtefato) {
            $entityTelefoneArtefato = $this->_newEntity('app:TelefoneSgdoce');
            $entityTelefoneArtefato->setSqPessoaSgdoce($entityAux);
        }

        $tipoTelefone = $this->getEntityManager()
                             ->getPartialReference(
                                     'app:VwTipoTelefone',
                                     \Core_Configuration::getSgdoceTipoTelefoneResidencial()
                               );

        $entityTelefoneArtefato->setSqTipoTelefone($tipoTelefone);
        $entityTelefoneArtefato->setDtCadastro(new \Zend_Date());
        $entityTelefoneArtefato->setNuDdd($filter->filterStatic($dto->getNuDdd(),'Digits'));
        $entityTelefoneArtefato->setNuTelefone($filter->filterStatic($dto->getTxTelefoneRodape(),'Digits'));

        $this->getEntityManager()->persist($entityTelefoneArtefato);
        $this->getEntityManager()->flush($entityTelefoneArtefato);

        return $entityTelefoneArtefato;
    }

    public function findTelefone($dto)
    {
        return $this->_getRepository()->findOneBy(
                array('sqPessoaSgdoce' => $dto->getSqPessoaSgdoce()));
    }

    public function saveTelefoneSgdoce($entity,$entityAux1)
    {
        $telefone = $this->getServiceLocator()->getService('VwTelefone') ->findTelefone($entity->getSqPessoa());
        $entityTelefoneArtefato = NULL;
        $filter = new \Zend_Filter();

        if($telefone->getSqTelefone()){
            $entityTelefoneArtefato = $this->_newEntity('app:TelefoneSgdoce');

            $tipoTelefone = $this->getEntityManager()
                                 ->getPartialReference(
                                         'app:VwTipoTelefone',
                                          \Core_Configuration::getSgdoceTipoTelefoneResidencial()
                                   );

            $entityTelefoneArtefato->setSqPessoaSgdoce($entityAux1);
            $entityTelefoneArtefato->setSqTipoTelefone($tipoTelefone);
            $entityTelefoneArtefato->setDtCadastro(new \Zend_Date());
            $entityTelefoneArtefato->setNuDdd($filter->filterStatic($telefone->getNuDdd(),'Digits'));
            $entityTelefoneArtefato->setNuTelefone($filter->filterStatic($telefone->getNuTelefone(),'Digits'));

            $this->getEntityManager()->persist($entityTelefoneArtefato);
        }

        $this->getEntityManager()->flush($entityTelefoneArtefato);

        return $entityTelefoneArtefato;
    }

    public function addTelefonePessoaSgdoce(\Sgdoce\Model\Entity\PessoaSgdoce $entPessoaSgdoce, $arrEntityVwTelefone)
    {
        foreach ($arrEntityVwTelefone as $entVwTelefone) {
            $entTelefoneSgdoce = $this->_newEntity();

            $entTelefoneSgdoce->setSqPessoaSgdoce($entPessoaSgdoce)
                    ->setDtCadastro(\Zend_Date::now())
                    ->setSqTipoTelefone($entVwTelefone->getSqTipoTelefone())
                    ->setNuDdd($entVwTelefone->getNuDdd())
                    ->setNuTelefone($entVwTelefone->getNuTelefone());

            $this->getEntityManager()->persist($entTelefoneSgdoce);
            $this->getEntityManager()->flush($entTelefoneSgdoce);
        }
        return $this;
    }
}
