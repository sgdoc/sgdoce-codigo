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

class EnderecoSgdoce extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:EnderecoSgdoce';

    public function saveEnderecoDestinatario($dto, $params = null)
    {

        $filter = new \Zend_Filter();

        $entityEnderecoArtefato = $this->_newEntity('app:EnderecoSgdoce');

        $pessoaSgdoce = $this->getEntityManager()->getPartialReference(
                        'app:PessoaSgdoce',  $dto->getSqPessoaSgdoce()->getSqPessoaSgdoce());

        $entityEnderecoArtefato->setSqPessoaSgdoce($pessoaSgdoce);

        $tipoEndereco = $this->getEntityManager()
                             ->getPartialReference(
                                    'app:VwTipoEndereco',
                                     \Core_Configuration::getSgdoceTipoEnderecoResidencial()
                               );

        if($dto->getSqTipoEndereco()){
            $tipoEndereco = $this->getEntityManager()->getPartialReference(
                    'app:VwTipoEndereco',  $dto->getSqTipoEndereco()->getSqTipoEndereco());
        }

        $entityEnderecoArtefato->setDtCadastro(date('Y-m-d'));

        if($dto->getSqMunicipio()->getSqMunicipio()){
            $municipio    = $this->getEntityManager()->getPartialReference(
                    'app:VwMunicipio',  $dto->getSqMunicipio()->getSqMunicipio());
            $entityEnderecoArtefato->setSqMunicipio($municipio);
        }

        if($dto->getCoCep()){
            $entityEnderecoArtefato->setCoCep($filter->filterStatic($dto->getCoCep(),'Digits'));
        }

        if($dto && $dto->getNoContato()) {
            $entityEnderecoArtefato->setNoContato($dto->getNoContato());
        }

        $entityEnderecoArtefato->setNoBairro($dto->getNoBairro());
        $entityEnderecoArtefato->setTxComplemento($dto->getTxComplemento());
        $entityEnderecoArtefato->setNuEndereco($dto->getNuEndereco());
        $entityEnderecoArtefato->setSqTipoEndereco($tipoEndereco);
        $entityEnderecoArtefato->setTxEndereco($dto->getTxEndereco());

        $this->getEntityManager()->persist($entityEnderecoArtefato);
        $this->getEntityManager()->flush($entityEnderecoArtefato);

        if (!empty($params['sqAnexoComprovanteAntigo'])) {
            $anexoAntigo = $this->getEntityManager()->getReference(
                'app:anexoComprovante', $params['sqAnexoComprovanteAntigo']
            );
            $entityAnexoComprovante = $this->_newEntity('app:AnexoComprovante');
            $entityAnexoComprovante->setDeCaminhoArquivo($anexoAntigo->getDeCaminhoArquivo());
            $entityAnexoComprovante->setSqEnderecoSgdoce($entityEnderecoArtefato);

            $this->getEntityManager()->persist($entityAnexoComprovante);
            $this->getEntityManager()->flush($entityAnexoComprovante);
        }

        return $entityEnderecoArtefato;
    }

    public function getEnderecoFromCorporativo($entity, $sqPessoaSgdoce)
    {
        return $this->_getRepository('app:EnderecoSgdoce')->getEnderecoFromCorporativo($entity, $sqPessoaSgdoce);
    }

    public function saveEnderecoPessoaRodape($dto,$entityAux)
    {
        $filter = new \Zend_Filter();

        $criteria = array('sqPessoaSgdoce' => $entityAux->getSqPessoaSgdoce());
        $entityEnderecoArtefato = $this->_getRepository('app:EnderecoSgdoce')->findOneBy($criteria);

        if (!$entityEnderecoArtefato) {
            $entityEnderecoArtefato = $this->_newEntity('app:EnderecoSgdoce');
            $entityEnderecoArtefato->setSqPessoaSgdoce($entityAux);
        }

        $tipoEndereco = $this->getEntityManager()
                             ->getPartialReference(
                                     'app:VwTipoEndereco',
                                     \Core_Configuration::getSgdoceTipoEnderecoResidencial()
                               );

        $entityEnderecoArtefato->setSqTipoEndereco($tipoEndereco);

        $entityEnderecoArtefato->setTxEndereco($dto->getTxEnderecoRodape());
        $entityEnderecoArtefato->setCoCep($filter->filterStatic($dto->getCoCepRodape(),'Digits'));

        $this->getEntityManager()->persist($entityEnderecoArtefato);
        $this->getEntityManager()->flush($entityEnderecoArtefato);

        return $entityEnderecoArtefato;
    }

    public function dtoEntityEndereco($params)
    {
        $dtoEnderecoSgdoce = \Core_Dto::factoryFromData($params,
                'entity', array('entity'=> 'Sgdoce\Model\Entity\EnderecoSgdoce',
                        'mapping' => array(
                                'sqPessoaSgdoce'       => 'Sgdoce\Model\Entity\PessoaSgdoce',
                                'sqMunicipio'          => 'Sgdoce\Model\Entity\VwMunicipio',
                                'sqTipoEndereco'       => 'Sgdoce\Model\Entity\VwTipoEndereco')));

        $res = $this->saveEnderecoDestinatario($dtoEnderecoSgdoce);

        return $entityEndereco = $this->getEntityManager()->getPartialReference(
                                  'app:VwEndereco',$res->getSqEnderecoSgdoce());
    }

    public function findEndereco($dto)
    {
        return $this->_getRepository()->findOneBy(
                        array('sqPessoaSgdoce' => $dto->getSqPessoaSgdoce()));
    }

    public function saveEnderecoSgdoce($entity,$entityAux1)
    {

        $endereco = $this->getServiceLocator()->getService('VwEndereco')->findEndereco($entity->getSqPessoa());

        $entityEnderecoArtefato = NULL;
        $filter = new \Zend_Filter();

        if($endereco->getSqEndereco()){
            $entityEnderecoArtefato = $this->_newEntity('app:EnderecoSgdoce');

            $entityEnderecoArtefato->setDtCadastro(date('Y-m-d'));

            if($endereco->getSqMunicipio()){
                $municipio    = $this->getEntityManager()->getPartialReference(
                        'app:VwMunicipio',  $endereco->getSqMunicipio()->getSqMunicipio());
                $entityEnderecoArtefato->setSqMunicipio($municipio);
            }

            if($endereco->getSqCep()){
                $entityEnderecoArtefato->setCoCep($filter->filterStatic($endereco->getSqCep(),'Digits'));
            }

            $entityEnderecoArtefato->setSqPessoaSgdoce($entityAux1);
            $entityEnderecoArtefato->setSqTipoEndereco($endereco->getSqTipoEndereco());
            $entityEnderecoArtefato->setTxEndereco($endereco->getTxEndereco());

            $this->getEntityManager()->persist($entityEnderecoArtefato);
            $this->getEntityManager()->flush($entityEnderecoArtefato);
        }

        return $entityEnderecoArtefato;
    }

    public function saveExtraDadosPessoa($params,$dtoSearch){

        $entityPessoa = $this->getServiceLocator()->getService('Pessoa')->findPessoa($dtoSearch);

        //endereco
        if($params['sqEndereco'] == ''){
            $endereco  = $this->_getRepository('app:VwEndereco')->findEndereco(
                    $entityPessoa->getSqPessoaCorporativo()->getSqPessoa());

            if($endereco){
                $params['sqTipoEndereco'] = $endereco->getSqtipoEndereco()->getSqtipoEndereco();
            }
        }else{
            $endereco = $this->getServiceLocator()->getService('VwEndereco')->findId($dtoSearch->getSqEndereco());
        }

        $dtoEnderecoSgdoce = \Core_Dto::factoryFromData($params,
                'entity', array('entity'=> 'Sgdoce\Model\Entity\EnderecoSgdoce',
                        'mapping' => array(
                                'sqPessoaSgdoce'       => 'Sgdoce\Model\Entity\PessoaSgdoce',
                                'sqMunicipio'          => 'Sgdoce\Model\Entity\VwMunicipio',
                                'sqTipoEndereco'       => 'Sgdoce\Model\Entity\VwTipoEndereco')));

        // buscar pelo pessoa artefato
        $dadosEnderecoSgdoce  = $this->findEndereco($dtoEnderecoSgdoce->getSqPessoaSgdoce());

        // persistindo informacoes de endereco
        $params = $this->trataPersistEndereco($dadosEnderecoSgdoce, $dtoEnderecoSgdoce, $params, $endereco);

        $dtoTelefoneSgdoce = \Core_Dto::factoryFromData($params,
                'entity', array('entity'=> 'Sgdoce\Model\Entity\TelefoneSgdoce',
                        'mapping' => array(
                                'sqPessoaSgdoce'       => 'Sgdoce\Model\Entity\PessoaSgdoce')));

        $dadosTelefoneSgdoce     = $this->getServiceLocator()->getService('TelefoneSgdoce')->findTelefone(
                $dtoEnderecoSgdoce->getSqPessoaSgdoce());

        $params = $this->salvaDadosTelefone($dadosTelefoneSgdoce, $entityPessoa, $dtoTelefoneSgdoce, $params);

        $dtoEmailSgdoce = \Core_Dto::factoryFromData($params,
                'entity', array('entity'=> 'Sgdoce\Model\Entity\EmailSgdoce',
                        'mapping' => array(
                                'sqPessoaSgdoce'       => 'Sgdoce\Model\Entity\PessoaSgdoce')));

        $dadosEmailSgdoce     = $this->getServiceLocator()->getService('EmailSgdoce')
                                                          ->findEmail($dtoEnderecoSgdoce->getSqPessoaSgdoce());

        $params = $this->salvaDadosEmail($dadosEmailSgdoce, $entityPessoa, $dtoEmailSgdoce, $params);

        return $params;
    }

    public function salvaDadosEmail($dadosEmailSgdoce, $entityPessoa, $dtoEmailSgdoce, $params)
    {
        if($dadosEmailSgdoce == NULL){

            $email = $this->getServiceLocator()->getService('VwEmail')->findEmail(
                    $entityPessoa->getSqPessoaCorporativo()->getSqPessoa());
            if($email){
                $dadosEmailSgdoce = $this->getServiceLocator()->getService('EmailSgdoce')
                                                          ->saveEmailPessoaRodape($dtoEmailSgdoce,$entityPessoa);
            }
        }

        if($dadosEmailSgdoce){
            $params['sqEmailSgdoce'] = $dadosEmailSgdoce->getSqEmailSgdoce();
        }

        return $params;
    }

    public function salvaDadosTelefone($dadosTelefoneSgdoce, $entityPessoa, $dtoTelefoneSgdoce, $params)
    {
        if($dadosTelefoneSgdoce == NULL){
              $telefone = $this->getServiceLocator()->getService('VwTelefone')->findTelefone(
                    $entityPessoa->getSqPessoaCorporativo()->getSqPessoa());
            if($telefone){
                $dadosTelefoneSgdoce = $this->getServiceLocator()->getService('TelefoneSgdoce')
                ->saveTelefonePessoaRodape($dtoTelefoneSgdoce,$entityPessoa);
            }
        }

        if($dadosTelefoneSgdoce){
            $params['sqTelefoneSgdoce'] = $dadosTelefoneSgdoce->getSqTelefoneSgdoce();
        }

        return $params;
    }

    public function trataPersistEndereco($dadosEnderecoSgdoce, $dtoEnderecoSgdoce, $params, $endereco = NULL)
    {
        if($dadosEnderecoSgdoce == NULL || $params['checkCorporativo'] == '1'){
            if($endereco){
                $dadosEnderecoSgdoce = $this->saveEnderecoDestinatario($dtoEnderecoSgdoce);
            }
        }else{
            if(!$dadosEnderecoSgdoce->getNoBairro()){
                $dadosEnderecoSgdoce->setNoBairro($dtoEnderecoSgdoce->getNoBairro());
            }
            if(!$dadosEnderecoSgdoce->getTxComplemento()){
                $dadosEnderecoSgdoce->setTxComplemento($dtoEnderecoSgdoce->getTxComplemento());
            }
            if(!$dadosEnderecoSgdoce->getNuEndereco()){
                $dadosEnderecoSgdoce->setNuEndereco($dtoEnderecoSgdoce->getNuEndereco());
            }
            $this->getEntityManager()->persist($dadosEnderecoSgdoce);
            $this->getEntityManager()->flush($dadosEnderecoSgdoce);
        }

        if($dadosEnderecoSgdoce){
            $params['sqEnderecoSgdoce'] = $dadosEnderecoSgdoce->getSqEnderecoSgdoce();
        }

        return $params;
    }

    public function findByArray($pessoaSgdoce)
    {
        return $this->_getRepository()->findByArray($pessoaSgdoce);
    }

    public function addEnderecoPessoaSgdoce(\Sgdoce\Model\Entity\PessoaSgdoce $entPessoaSgdoce, $arrEntityVwEndereco)
    {
        foreach ($arrEntityVwEndereco as $entVwEndereco) {
            $entEnderecoSgdoce = $this->_newEntity();

            $entEnderecoSgdoce->setSqPessoaSgdoce($entPessoaSgdoce)
                    ->setDtCadastro(\Zend_Date::now())
                    ->setSqTipoEndereco($entVwEndereco->getSqTipoEndereco())
                    ->setSqMunicipio($entVwEndereco->getSqMunicipio())
//                    ->setCoCep() não tem essa informação em vwEndereco
                    ->setNoBairro($entVwEndereco->getNoBairro())
                    ->setTxEndereco($entVwEndereco->getTxEndereco())
                    ->setNuEndereco($entVwEndereco->getNuEndereco())
                    ->setTxComplemento($entVwEndereco->getTxComplemento());

            $this->getEntityManager()->persist($entEnderecoSgdoce);
            $this->getEntityManager()->flush($entEnderecoSgdoce);
        }
        return $this;
    }
}