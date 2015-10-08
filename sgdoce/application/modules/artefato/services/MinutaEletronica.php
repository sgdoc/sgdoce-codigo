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

use Sgdoce\Model\Entity\PessoaSgdoce;

use Doctrine\ORM\Mapping\Entity;

/**
 * Classe para Service de Artefato
 *
 * @package  Minuta
 * @category Service
 * @name     Artefato
 * @version  1.0.0
 */
class MinutaEletronica extends \Core_ServiceLayer_Service_CrudDto
{

    /**
     * @var string
     */
    protected $_entityName = 'app:ArtefatoMinuta';

    public function findUnidadeExercicio($sqPessoa)
    {
        return $this->_getRepository('app:VwProfissional')->find($sqPessoa);
    }

    /**
     * Metódo que realiza o save do Artefato
     */
    public function saveArtefato(\Core_Dto_Search $dtoSearch)
    {
        $entityArtefato = $this->_newEntity('app:Artefato');

        $entityArtefato->setDtPrazo(NULL);
        $date = new \Zend_Date();
        $entityArtefato->setDtArtefato($date);

        $entityTipoArtefatoAssunto = $this->_getRepository('app:TipoArtefatoAssunto')
            ->findBy(array(
                'sqAssunto' => $dtoSearch->getSqAssunto(),
                'sqTipoArtefato' => 5
            ));

        $entityTipoDoc = $this->_getRepository('app:TipoDocumento')->find($dtoSearch->getSqTipoDocumento());
        $entityArtefato->setSqTipoArtefatoAssunto($entityTipoArtefatoAssunto[0]);
        $entityArtefato->setSqTipoDocumento($entityTipoDoc);
        $this->getEntityManager()->persist($entityArtefato);
        $this->getEntityManager()->flush($entityArtefato);

        $idModelo = $this->hasModeloDocumentoCadastrado($dtoSearch,TRUE);
        $entityModelo = $this->_getRepository('app:ModeloDocumento')->find($idModelo);

        $entityArtefatoMinuta = $this->_newEntity('app:ArtefatoMinuta');
        $entityArtefatoMinuta->setSqModeloDocumento($entityModelo);
        $entityArtefatoMinuta->setSqArtefato($entityArtefato);

        $this->getEntityManager()->persist($entityArtefatoMinuta);
        $this->getEntityManager()->flush($entityArtefatoMinuta);

        return $entityArtefato;
    }

    /**
     * Metódo que verifica se o modelo está cadastrado
     * @return boolean
     */
    public function hasModeloDocumentoCadastrado(\Core_Dto_Search $dtoSearch,$result = FALSE)
    {
        $repository = $this->getEntityManager()->getRepository('app:ModeloDocumento');
        return $repository->hasModeloDocumentoCadastrado($dtoSearch,$result);
    }

    /**
     * Metódo que retorna os campos de acordo com o Modelo
     * @return array
     */
    public function getCampoModeloDocumento(\Core_Dto_Abstract $dtoSearch)
    {
        $repository = $this->getEntityManager()->getRepository('app:ModeloDocumentoCampo');
        return $repository->getCampoModeloDocumento($dtoSearch);
    }

    /**
     * Metódo que retorna os dados de Origem da Pessoa
     * @return array
     */
    public function getDadosOrigem(\Core_Dto_Search $dtoSearch)
    {
        $repository = $this->getEntityManager()->getRepository('app:VwEndereco');
        return $repository->buscarEnderecoPorIdUsuario($dtoSearch);
    }

    /**
     * Metódo que realiza o upload do arquivo.
     */
    private function _upload()
    {
        $upload = $this->getCoreUpload();

        if (is_string($upload->getFileName())) {
            $upload->setOptions(array('ignoreNoFile' => TRUE));

            $upload->addValidator('Extension',
                    TRUE,
                    array(
                            'extensions'      => 'png,jpg',
                            'messages' => str_replace('<extensão>', 'png,jpg', \Core_Registry::getMessage()->_('MN076'))
                    )
            );

            $upload->addValidator('ImageSize',
                    TRUE,
                    array('minwidth' => '120',
                            'maxwidth' => '120',
                            'minheight' => '120',
                            'maxheight' => '120',
                            'messages' => \Core_Registry::getMessage()->_('MN130')
                    ));
            return $upload->upload();
        }
    }

    public function getPessoaSgdoce($entity,$sqPessoaFuncao)
    {
        $criteria = array('sqArtefato' => $entity->getSqArtefato(),
                'sqPessoaFuncao' => $sqPessoaFuncao);

        return $this->_getRepository('app:PessoaArtefato')->findBy($criteria);
    }

    public function fixNewlines($text)
    {

        // replace \r\n to \n
        $text = str_replace("\r\n", "\n", $text);
        // remove \rs
        $text = str_replace("\r", "\n", $text);

        $text = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $text);

        return $text;
    }

    /**
     * Metódo que realiza o preSave.
     */
    public function preSave($entity, $dto = NULL, $dtoPessoa = NULL)
    {
    	if($dto->getTxTextoArtefato()) {
    	    $txAtual = preg_replace('/<br\\\\s*?\\/?/i', "\\n" , $dto->getTxTextoArtefato());
    	    $txAtual = rtrim($txAtual,"&nbsp;.<br>.\n.' '");
    	    //$entity->setTxTextoArtefato($dto->getTxTextoArtefato());
        	$entity->setTxTextoArtefato($txAtual);
    	}

    	if($dto->getTxEmenta()) {
        	$entity->setTxEmenta(rtrim($dto->getTxEmenta(),"&nbsp;.<br>.\n.' '"));
    	}

    	if($dto->getTxReferencia()) {
        	$entity->setTxReferencia(rtrim($dto->getTxReferencia(),"&nbsp;.<br>.\n.' '"));
    	}
    }

    /**
     * Metódo que realiza o save da pessoa interessa e assinante.
     */
    public function saveExtraPessoa($entity,$entityArtefatoClone,$entityArtefato)
    {
        //grava pessoa interessada artefato
        $entityInteressadaArtefato      = $this->_getRepository('app:PessoaInteressadaArtefato')->findBy(
                array('sqArtefato' =>  $entity->getSqArtefato()->getSqArtefato()));
        if(is_array($entityInteressadaArtefato)){
            foreach ($entityInteressadaArtefato as $entityInteressada) {
                $entityInteressadaClone = clone $entityInteressada;
                $entityInteressadaClone->setSqArtefato($entityArtefatoClone);
                $this->getEntityManager()->persist($entityInteressadaClone);
                $this->getEntityManager()->flush($entityInteressadaClone);
            }
        }

        //grava pessoa interessada artefato
        $entityAssinanteArtefato      = $this->_getRepository('app:PessoaAssinanteArtefato')->findBy(
                array('sqArtefato' =>  $entity->getSqArtefato()->getSqArtefato()));
        if(is_array($entityAssinanteArtefato)){
            foreach ($entityAssinanteArtefato as $entityAssinante) {
                $entityAssinanteClone = clone $entityAssinante;
                $entityAssinanteClone->setSqArtefato($entityArtefatoClone);
                $this->getEntityManager()->persist($entityAssinanteClone);
                $this->getEntityManager()->flush($entityAssinanteClone);
            }
        }

        //grava pessoa motivacao artefato
        $entityMotivacaoArtefato      = $this->_getRepository('app:Motivacao')->findBy(
                array('sqArtefato' =>  $entity->getSqArtefato()->getSqArtefato()));
        if(is_array($entityMotivacaoArtefato)){
            foreach ($entityMotivacaoArtefato as $entityMotivacao) {
                $entityMotivacaoClone = clone $entityMotivacao;
                $entityMotivacaoClone->setSqArtefato($entityArtefatoClone);
                $this->getEntityManager()->persist($entityMotivacaoClone);
                $this->getEntityManager()->flush($entityMotivacaoClone);
            }
        }

        //grava rodape
        $criteria = array('sqArtefato'=> $entityArtefato->getSqArtefato()
                ,'sqPessoaFuncao' => \Core_Configuration::getSgdocePessoaFuncaoDadosRodape());

        $entityRodapeArtefato      = $this->_getRepository('app:PessoaArtefato')->findOneBy($criteria);
        if($entityRodapeArtefato){
            $entityRodapeArtefatoClone = clone $entityRodapeArtefato;
            $entityRodapeArtefatoClone->setSqArtefato($entityArtefatoClone);
            $this->getEntityManager()->persist($entityRodapeArtefatoClone);
            $this->getEntityManager()->flush($entityRodapeArtefatoClone);
        }

        //grava o artefato minuta
        $entityArtefatoMinuta = $this->_getRepository()->find($entity->getSqArtefato()->getSqArtefato());
        $entityArtefatoMinutaClone = clone $entityArtefatoMinuta;
        $entityArtefatoMinutaClone->setSqArtefato($entityArtefatoClone);
        $this->getEntityManager()->persist($entityArtefatoMinutaClone);
        $this->getEntityManager()->flush($entityArtefatoMinutaClone);

        //save historico
        $criteria = array('sqArtefato' => $entity->getSqArtefato()->getSqArtefato());
        $entityHistorico = $this->_getRepository('app:HistoricoArtefato')->findBy($criteria);
        $entityHistoricoClone = clone $entityHistorico[0];
        $entityHistoricoClone->setSqArtefato($entityArtefatoClone);
        $this->getEntityManager()->persist($entityHistoricoClone);
        $this->getEntityManager()->flush($entityHistoricoClone);

        //save origem
        $entityOrigemSgdoce = $this->getPessoaSgdoce($entity,
                \Core_Configuration::getSgdocePessoaFuncaoOrigem());
        $entityOrigemSgdoceClone = clone $entityOrigemSgdoce[0];
        $entityOrigemSgdoceClone->setSqArtefato($entityArtefatoClone);
        $this->getEntityManager()->persist($entityOrigemSgdoceClone);
        $this->getEntityManager()->flush($entityOrigemSgdoceClone);

        //save autor
        $entityAutorSgdoce = $this->getPessoaSgdoce($entity,
                \Core_Configuration::getSgdocePessoaFuncaoAutor());
        $entityAutorSgdoceClone = clone $entityAutorSgdoce[0];
        $entityAutorSgdoceClone->setSqArtefato($entityArtefatoClone);
        $this->getEntityManager()->persist($entityAutorSgdoceClone);
        $this->getEntityManager()->flush($entityAutorSgdoceClone);
    }

    /**
     * Metódo que realiza o postUpdate.
     */
    public function postUpdate($entity, $dto = NULL, $dtoPessoa = NULL)
    {

        $resultHistorico = $this->getEntityManager()->getRepository('app:HistoricoArtefato')->findBy(
                array('sqArtefato' => $entity->getSqArtefato()));

        if(count($resultHistorico) == 0){            
            // #HistoricoArtefato::save();            
            $strMessage = $this->getServiceLocator()
                               ->getService('HistoricoArtefato')
                               ->getMessage('MH009');
            
            $this->getServiceLocator()
                 ->getService('HistoricoArtefato')
                 ->registrar($sqArtefato,
                             \Core_Configuration::getSqOcorrenciaSalvarMinuta(),
                             $strMessage);
            
            $this->getServiceLocator()->getService('PessoaArtefato')
                                      ->saveOrigemAutor($entity,$dtoPessoa['unidade'],$dtoPessoa['pessoa']);
        }

        if ($dto->hasSqGrauAcesso() && ($dto->getSqGrauAcesso() != '')) {
            $sqGrauAcesso = $this->getEntityManager()->getRepository('app:GrauAcesso')->findBy(
                    array('sqGrauAcesso' => $dto->getSqGrauAcesso()));

            $this->getServiceLocator()->getService('GrauAcessoArtefato')
            ->saveGrauAcessoArtefato($entity->getSqArtefato(),$sqGrauAcesso[0]);
        }

        if ($dtoPessoa['assinatura']){
            $res = $this->getServiceLocator()->getService('PessoaAssinanteArtefato')
            ->saveAssinatura($dtoPessoa['assinatura'],$dto);
        }

        $entitySgdoce = $this->getPessoaSgdoce($entity, \Core_Configuration::getSgdocePessoaFuncaoDestinatario());

        $externo = FALSE;
        foreach ($dtoPessoa['externo'] as $campo) {
            if($campo['noCampo'] == 'Destino Externo'){
                $externo = TRUE;
            }
        }
        if($externo){
            foreach ($entitySgdoce as $key => $entityPessoaArtefato) {
                if($key == 0){
                    continue;
                }
                //grava o artefato
                $entityArtefato      = $this->_getRepository('app:Artefato')->find(
                                       $entity->getSqArtefato()->getSqArtefato());
                $entityArtefatoClone = clone $entityArtefato;
                $entityArtefatoClone->setSqArtefato(NULL);
                $this->getEntityManager()->persist($entityArtefatoClone);
                $this->getEntityManager()->flush($entityArtefatoClone);

                //grava pessoa artefato
                $entityPessoaArtefato->setSqArtefato($entityArtefatoClone);
                $this->getEntityManager()->persist($entityPessoaArtefato);
                $this->getEntityManager()->flush($entityPessoaArtefato);

                $this->saveExtraPessoa($entity, $entityArtefatoClone,$entityArtefato);

            }
        }
    }

    /**
     * Metódo que realiza o postSave.
     */
    public function postSave($entity, $dto = NULL, $dtoPessoa = NULL)
    {
        if((!$dtoPessoa['artefato']->getDtPrazo()) && (!$dtoPessoa['artefato']->getNuDiasPrazo()) ){
            $dtoPessoa['artefato']->setDtPrazo(NULL);
            $dtoPessoa['artefato']->setNuDiasPrazo(NULL);
            $dtoPessoa['artefato']->setInDiasCorridos(NULL);
        } else {
            if(!$dtoPessoa['artefato']->getDtPrazo()){
                $dtoPessoa['artefato']->setDtPrazo(NULL);
            } else {
                $dtoPessoa['artefato']->setNuDiasPrazo(NULL);
                $dtoPessoa['artefato']->setInDiasCorridos(NULL);
            }
        }

        $filename = $this->_upload();

        if ($dtoPessoa['rodape']) {
            $res = $this->getServiceLocator()->getService('PessoaArtefato')->saveRodape($dto,$dtoPessoa['rodape']);
        }

        $nameGetFile = '';
        if($dto->getDeImagemRodapeHidden() != NULL) {
        	$dtoPessoa['artefato']->setDeImagemRodape($dto->getDeImagemRodapeHidden());
        	$nameGetFile = substr($dto->getDeImagemRodapeHidden(),
        			strpos($dto->getDeImagemRodapeHidden(), "_") + 1);
        }

        if ($filename) {
        	$nameFileSave = substr($filename,strpos($filename, "_") + 1);

        	if($nameGetFile != $nameFileSave) {
        		$dtoPessoa['artefato']->setDeImagemRodape($filename);
        	}
        }

        if(!$dtoPessoa['artefato']->getSqTipoPrioridade()) {
            $entityAux2 = $this->_newEntity('app:TipoPrioridade');
            $dtoPessoa['artefato']->setSqTipoPrioridade($entityAux2);
        }

        if($dto->hasNoCargoInternoDestinatario()) {
            $dtoPessoa['artefato']->setNoCargoInterno($dto->getNoCargoInternoDestinatario());
        }

        $this->getServiceLocator()->getService('Artefato')->save($dtoPessoa['artefato']);
    }

    /**
     * Metódo que realiza o save do destinatario
     */
    public function saveDestinatario($pessoaEntity,$nuDocumento = NULL)
    {
        if($pessoaEntity instanceof  \Core_Dto_Abstract){
            $pessoaEntity = $pessoaEntity->getEntity();
        }
        if($nuDocumento['documento']){
            $pessoaEntity->setNuCpfCnpjPassaporte($nuDocumento['documento']);
        }
        $metadata = $this->getEntityManager()->getClassMetadata(get_class($pessoaEntity));
        $uow  = $this->getEntityManager()->getUnitOfWork();

        foreach ($metadata->associationMappings as $field => $prop) {
            $value = $metadata->reflFields[$field]->getValue($pessoaEntity);
            if (is_object($value)) {
                $metadataAssoc = $this->getEntityManager()->getClassMetadata(get_class($value));
                $idsFk = $metadataAssoc->getIdentifierValues($value);
                if ($idsFk) {
                    $uow->registerManaged($value, $idsFk, array());
                    $uow->removeFromIdentityMap($value);
                }
            }
        }
        $this->getEntityManager()->persist($pessoaEntity);
        $this->getEntityManager()->flush($pessoaEntity);
        return $pessoaEntity;
    }

    public function getCoreUpload()
    {
        $configs = \Core_Registry::get('configs');

        return new \Core_Upload('Http', FALSE, $configs['upload']['rodape']);
    }
}
