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

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Query\ParameterTypeInferer;

/**
 * Classe para Service de Pessoa
 *
 * @package  Minuta
 * @category Service
 * @name     Pessoa
 * @version  1.0.0
 */
class PessoaArtefato extends \Core_ServiceLayer_Service_CrudDto
{
    protected $_entityName = 'app:PessoaArtefato';

    public function searchPessoaOrigem($dto)
    {
        return $this->_getRepository()->searchPessoaOrigem($dto);
    }

    public function getPessoaArtefato($dto, $sqPessoaFuncao)
    {
        $criteria = array('sqArtefato' => $dto->getSqArtefato(), 'sqPessoaFuncao' => $sqPessoaFuncao);
        return $this->_getRepository()->findBy($criteria);
    }

    public function findPessoaArtefatoCriteria($dto)
    {
        $criteria = array('sqArtefato' => $dto->getSqArtefato()->getSqArtefato()
                          ,'sqPessoaFuncao' => $dto->getSqPessoaFuncao()->getSqPessoaFuncao()
                          ,'sqPessoaSgdoce' => $dto->getSqPessoaSgdoce()->getSqPessoaSgdoce()
                          );
        return $this->_getRepository()->findOneBy($criteria);
    }

    public function findPessoaArtefato($dto)
    {
        return $this->_getRepository()->findPessoaArtefato($dto);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
    	return $this->_getRepository()->findBy($criteria);
    }

    public function findOneBy(array $criteria)
    {
    	return $this->_getRepository()->findOneBy($criteria);
    }

    /**
     * Metódo que realiza o save Pessoa Artefato
     */
    public function savePessoaArtefato($pessoaEntity)
    {
        $entityPessoaArtefato = $this->_newEntity('app:PessoaArtefato');

        $entityArtefato = $this->getEntityManager()
        ->getPartialReference('app:Artefato',$pessoaEntity->getSqArtefato()->getSqArtefato());

        $entityPessoaFuncao = $this->getEntityManager()
        ->getPartialReference('app:PessoaFuncao',$pessoaEntity->getSqPessoaFuncao()->getSqPessoaFuncao());

        $entityPessoaSgdoce = $this->getEntityManager()
        ->getPartialReference('app:PessoaSgdoce',$pessoaEntity->getSqPessoaSgdoce()->getSqPessoaSgdoce());

        if($pessoaEntity->getSqPessoaUnidadeOrg()){
            $entityPessoaUnidadeOrg = $this->getEntityManager()
            ->getPartialReference('app:PessoaUnidadeOrg'
                    ,$pessoaEntity->getSqPessoaUnidadeOrg()->getSqPessoaUnidadeOrg());
            $entityPessoaArtefato->setSqPessoaUnidadeOrg($entityPessoaUnidadeOrg);
        }

        if($pessoaEntity->getSqEnderecoSgdoce()->getSqEnderecoSgdoce()){
            $entityEnderecoSgdoce = $this->getEntityManager()
            ->getPartialReference('app:EnderecoSgdoce'
                    ,$pessoaEntity->getSqEnderecoSgdoce()->getSqEnderecoSgdoce());
            $entityPessoaArtefato->setSqEnderecoSgdoce($entityEnderecoSgdoce);
        }
        if($pessoaEntity->getSqEmailSgdoce()->getSqEmailSgdoce()){
            $entityEmailSgdoce = $this->getEntityManager()
            ->getPartialReference('app:EmailSgdoce'
                    ,$pessoaEntity->getSqEmailSgdoce()->getSqEmailSgdoce());
            $entityPessoaArtefato->setSqEmailSgdoce($entityEmailSgdoce);
        }
        if($pessoaEntity->getSqTelefoneSgdoce()->getSqTelefoneSgdoce()){
            $entityTelefoneSgdoce = $this->getEntityManager()
            ->getPartialReference('app:TelefoneSgdoce'
                    , $pessoaEntity->getSqTelefoneSgdoce()->getSqTelefoneSgdoce());
            $entityPessoaArtefato->setSqTelefoneSgdoce($entityTelefoneSgdoce);
        }
        if($pessoaEntity->getSqTratamentoVocativo()){
            $entityTratamentoSgdoce = $this->getEntityManager()
            ->getPartialReference('app:TratamentoVocativo'
                    , $pessoaEntity->getSqTratamentoVocativo()->getSqTratamentoVocativo());
            $entityPessoaArtefato->setSqTratamentoVocativo($entityTratamentoSgdoce);
        }

        if(strlen($pessoaEntity->getTxPosVocativo()) >= 1){
            $entityPessoaArtefato->setTxPosVocativo($pessoaEntity->getTxPosVocativo());
        }
        if(strlen($pessoaEntity->getTxPosTratamento()) >= 1){
            $entityPessoaArtefato->setTxPosTratamento($pessoaEntity->getTxPosTratamento());
        }

        $entityPessoaArtefato->setSqArtefato($entityArtefato);
        $entityPessoaArtefato->setSqPessoaSgdoce($entityPessoaSgdoce);
        $entityPessoaArtefato->setSqPessoaFuncao($entityPessoaFuncao);

        $this->getEntityManager()->persist($entityPessoaArtefato);
        $this->getEntityManager()->flush($entityPessoaArtefato);
    }

    /**
     * Metódo que realiza o save da Assinatura
     */
    public function saveRodape($dto,$pessoaEntity)
    {
        $filter = new \Zend_Filter();

        $criteria = array('sqPessoaSgdoce' => $pessoaEntity['PessoaSgdoce']->getSqPessoaSgdoce());
        $entityAux = $this->_getRepository('app:PessoaSgdoce')->findOneBy($criteria);

        $pessoaSgdoceEntity = $pessoaEntity['PessoaSgdoce']->getEntity();
        $pessoaArtefatoEntity = $pessoaEntity['PessoaArtefato']->getEntity();

        if (!$entityAux) {
            $entityAux = $this->_newEntity('app:PessoaSgdoce');
        }
        $tipoPessoa = $this->getEntityManager()
        ->getPartialReference('app:VwTipoPessoa',  $pessoaSgdoceEntity->getSqTipoPessoa()->getSqTipoPessoa());

        $sqPessoaCorporativo = $this->getEntityManager()
        ->getPartialReference('app:VwPessoa',  $pessoaSgdoceEntity->getSqPessoaCorporativo()->getSqPessoa());

        $entityAux->setNoPessoa($pessoaSgdoceEntity->getNoPessoa());
        $entityAux->setSqPessoaCorporativo($sqPessoaCorporativo);
        $entityAux->setSqTipoPessoa($tipoPessoa);

        $this->getEntityManager()->persist($entityAux);
        $this->getEntityManager()->flush($entityAux);

        //salvar endereco / telefone / email --
        // falta consultar se existe já o endereco e setar no id
        $entityEnderecoArtefato = $this->getServiceLocator()->getService('EnderecoSgdoce')
        ->saveEnderecoPessoaRodape($dto,$entityAux);

        $entityEmailArtefato    = $this->getServiceLocator()->getService('EmailSgdoce')
        ->saveEmailPessoaRodape($dto,$entityAux);

        $entityTelefoneArtefato = $this->getServiceLocator()->getService('TelefoneSgdoce')
        ->saveTelefonePessoaRodape($dto,$entityAux);
        //salvar pessoaArtefato
        $entityPessoaArtefato = $this->_newEntity('app:PessoaArtefato');

        $entityArtefato = $this->getEntityManager()
        ->getPartialReference('app:Artefato',  $pessoaArtefatoEntity->getSqArtefato()->getSqArtefato());

        $entityPessoaFuncao = $this->getEntityManager()
        ->getPartialReference('app:PessoaFuncao',  \Core_Configuration::getSgdocePessoaFuncaoDadosRodape());

        $entityPessoaArtefato->setSqArtefato($entityArtefato);
        $entityPessoaArtefato->setSqPessoaSgdoce($entityAux);
        $entityPessoaArtefato->setSqPessoaFuncao($entityPessoaFuncao);

        //outros sets ender/tel/email/
        $entityPessoaArtefato->setSqEnderecoSgdoce($entityEnderecoArtefato);
        $entityPessoaArtefato->setSqEmailSgdoce($entityEmailArtefato);
        $entityPessoaArtefato->setSqTelefoneSgdoce($entityTelefoneArtefato);

        $params['sqPessoaCorporativo'] =  $pessoaSgdoceEntity->getSqPessoaCorporativo()->getSqPessoa();
        $params['sqArtefato']          =  $pessoaArtefatoEntity->getSqArtefato()->getSqArtefato();
        $params['sqPessoaFuncao']      =  \Core_Configuration::getSgdocePessoaFuncaoDadosRodape();

        $dto = \Core_Dto::factoryFromData($params, 'search');
        $result = $this->getServiceLocator()->getService('Pessoa')->findPessoaAssinaturaArtefato($dto);

        if(!$result){
            $this->getEntityManager()->persist($entityPessoaArtefato);
            $this->getEntityManager()->flush($entityPessoaArtefato);
        }

        return $entityAux;
    }

    /**
     * Metódo que realiza o save da Origem e Autor da Minuta
     */
    public function saveOrigemAutor($entity,$unidadeEntity,$pessoaEntity)
    {

        $criteria = array('sqPessoaCorporativo' => $pessoaEntity->getSqPessoa());
        $entityAux1 = $this->_getRepository('app:PessoaSgdoce')->findOneBy($criteria);

        if (!$entityAux1) {
            $entityAux1 = $this->_newEntity('app:PessoaSgdoce');
        }

        $entityAux1->setSqPessoaCorporativo($pessoaEntity);

        $entityTipoPessoa = $this->getEntityManager()
        ->getPartialReference('app:VwTipoPessoa',  \Core_Configuration::getSgdoceTipoPessoaPessoaFisica());

        $entityAux1->setNoPessoa($pessoaEntity->getNoPessoa());
        $entityAux1->setSqTipoPessoa($entityTipoPessoa);

        $this->getEntityManager()->persist($entityAux1);
        $this->getEntityManager()->flush($entityAux1);

        $entityPessoaArtefato = $this->_newEntity('app:PessoaArtefato');

        $entityArtefato = $this->getEntityManager()
        ->getPartialReference('app:Artefato',  $entity->getSqArtefato()->getSqArtefato());

        $entityPessoaFuncao = $this->getEntityManager()
        ->getPartialReference('app:PessoaFuncao',  \Core_Configuration::getSgdocePessoaFuncaoAutor());

        $entityPessoaArtefato->setSqArtefato($entityArtefato);
        $entityPessoaArtefato->setSqPessoaSgdoce($entityAux1);
        $entityPessoaArtefato->setSqPessoaFuncao($entityPessoaFuncao);

        //grava demais dados,endereco,email,telefone
        $this->saveComplementoPessoa($entityPessoaArtefato,$pessoaEntity,$entityAux1);

        $this->getEntityManager()->persist($entityPessoaArtefato);
        $this->getEntityManager()->flush($entityPessoaArtefato);

        $vwPessoa = $this->getServiceLocator()->getService('VwPessoa')->find($unidadeEntity->getSqUnidadeOrg());

        $criteria = array('sqPessoaCorporativo' => $vwPessoa->getSqPessoa());
        $entityAux2 = $this->_getRepository('app:PessoaSgdoce')->findOneBy($criteria);

        if (!$entityAux2) {
            $entityAux2 = $this->_newEntity('app:PessoaSgdoce');
        }

        $entityAux2->setSqPessoaCorporativo($vwPessoa);

        $entityTipoPessoa = $this->getEntityManager()
        ->getPartialReference('app:VwTipoPessoa',  \Core_Configuration::getSgdoceTipoPessoaMinisterioPublico());

        $entityPessoaFuncao = $this->getEntityManager()
        ->getPartialReference('app:PessoaFuncao',  \Core_Configuration::getSgdocePessoaFuncaoOrigem());

        $entityAux2->setNoPessoa($unidadeEntity->getNoUnidadeOrg());

        $entityAux2->setSqTipoPessoa($entityTipoPessoa);

        $this->getEntityManager()->persist($entityAux2);
        $this->getEntityManager()->flush($entityPessoaArtefato);

        $entityPessoaArtefato = $this->_newEntity('app:PessoaArtefato');

        $entityPessoaArtefato->setSqArtefato($entityArtefato);
        $entityPessoaArtefato->setSqPessoaFuncao($entityPessoaFuncao);
        $entityPessoaArtefato->setSqPessoaSgdoce($entityAux2);

        $this->saveComplementoPessoa($entityPessoaArtefato,$pessoaEntity,$entityAux2);

        $this->getEntityManager()->persist($entityPessoaArtefato);
        $this->getEntityManager()->flush($entityPessoaArtefato);
    }

    public function saveComplementoPessoa(&$entityPessoaArtefato,$pessoaEntity,$entityAux1)
    {
        $dadosEnderecoSgdoce     =  $this->getServiceLocator()->getService('EnderecoSgdoce')
        ->findEndereco($entityAux1);

        if($dadosEnderecoSgdoce == NULL){
            $dadosEnderecoSgdoce = $this->getServiceLocator()->getService('EnderecoSgdoce')
            ->saveEnderecoSgdoce($pessoaEntity,$entityAux1);
        }
        $entityPessoaArtefato->setSqEnderecoSgdoce($dadosEnderecoSgdoce);

        $dadosTelefoneSgdoce     =  $this->getServiceLocator()->getService('TelefoneSgdoce')
        ->findTelefone($entityAux1);

        if($dadosTelefoneSgdoce == NULL){
            $dadosTelefoneSgdoce = $this->getServiceLocator()->getService('TelefoneSgdoce')
            ->saveTelefoneSgdoce($pessoaEntity,$entityAux1);
        }

        $entityPessoaArtefato->setSqTelefoneSgdoce($dadosTelefoneSgdoce);

        $dadosEmailSgdoce     =  $this->getServiceLocator()->getService('EmailSgdoce')
        ->findEmail($entityAux1);

        if($dadosEmailSgdoce == NULL){
            $dadosEmailSgdoce = $this->getServiceLocator()->getService('EmailSgdoce')
            ->saveEmailSgdoce($pessoaEntity,$entityAux1);
        }

        $entityPessoaArtefato->setSqEmailSgdoce($dadosEmailSgdoce);

        return $entityPessoaArtefato;

    }
}
