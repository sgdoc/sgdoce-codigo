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
class EmailSgdoce extends \Core_ServiceLayer_Service_CrudDto
{

    /**
     * @var string
     */
    protected $_entityName = 'app:EmailSgdoce';

    public function saveEmailPessoaRodape($dto, $entityAux)
    {
        $criteria = array('sqPessoaSgdoce' => $entityAux->getSqPessoaSgdoce());
        $entityEmailArtefato = $this->_getRepository('app:EmailSgdoce')->findOneBy($criteria);

        if (!$entityEmailArtefato) {
            $entityEmailArtefato = $this->_newEntity('app:EmailSgdoce');
            $entityEmailArtefato->setSqPessoaSgdoce($entityAux);
        }

        $tipoEmail = $this->getEntityManager()
                          ->getPartialReference(
                                  'app:VwTipoEmail',
                                  \Core_Configuration::getSgdoceTipoEmailParticular()
                           );

        $entityEmailArtefato->setDtCadastro(new \Zend_Date());
        $entityEmailArtefato->setSqTipoEmail($tipoEmail);
        $entityEmailArtefato->setTxEmail($dto->getTxEmailRodape());

        $this->getEntityManager()->persist($entityEmailArtefato);
        $this->getEntityManager()->flush($entityEmailArtefato);

        return $entityEmailArtefato;
    }

    public function findEmail($dto)
    {
        return $this->_getRepository()->findOneBy(
                array('sqPessoaSgdoce' => $dto->getSqPessoaSgdoce()));
    }

    public function saveEmailSgdoce($entity, $entityAux)
    {
        $email = $this->getServiceLocator()->getService('VwEmail')->findEmail($entity->getSqPessoa());
        $entityEmailArtefato = NULL;
        $filter = new \Zend_Filter();
        if ($email->getSqEmail()) {

            $entityEmailArtefato = $this->_newEntity('app:EmailSgdoce');

            $entityEmailArtefato->setSqPessoaSgdoce($entityAux);
            $entityEmailArtefato->setDtCadastro(new \Zend_Date());
            $entityEmailArtefato->setSqTipoEmail($email->getsqTipoEmail());
            $entityEmailArtefato->setTxEmail($email->getTxEmail());

            $this->getEntityManager()->persist($entityEmailArtefato);
            $this->getEntityManager()->flush($entityEmailArtefato);
        }
        return $entityEmailArtefato;
    }


    public function addEmailPessoaSgdoce(\Sgdoce\Model\Entity\PessoaSgdoce $entPessoaSgdoce, $arrEntityVwEmail)
    {
        foreach ($arrEntityVwEmail as $entVwEmail) {
            $entEmailSgdoce = $this->_newEntity();

            $entEmailSgdoce->setSqPessoaSgdoce($entPessoaSgdoce)
                    ->setDtCadastro(\Zend_Date::now())
                    ->setSqTipoEmail($entVwEmail->getSqTipoEmail())
                    ->setTxEmail($entVwEmail->getTxEmail());

            $this->getEntityManager()->persist($entEmailSgdoce);
            $this->getEntityManager()->flush();
        }
        return $this;
    }

}
