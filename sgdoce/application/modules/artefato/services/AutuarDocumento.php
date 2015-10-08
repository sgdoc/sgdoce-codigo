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

/**
 * Classe para Service de ProcessoEletronico
 *
 * @package  Artefato
 * @category     Service
 * @name         ProcessoEletronico
 * @version  1.0.0
 */
class AutuarDocumento extends Artefato {

    /**
     * @var string
     */
    protected $_entityName = 'app:Artefato';

    /**
     * @var boolean
     */
    protected $_stopInsert = false;

    /**
     * @param Core_Model_Entity_Abstract $entity
     * @param type $dto
     */
    public function preInsert($entity, $searchDto = null, $artefatoProcesso = null, $digital = null)
    {
        if( $this->isDocumentoAutuado($searchDto->getNuDigitalAutuado()) ){
            throw new \Exception("Documento já autuado.");
        }
        
        $configs = \Core_Registry::get('configs');

        $nuArtefato = null;

        if( $configs['processo']['numberWithNupSiorg'] ) {
            // ATRUBUI NU NUP DA DIGITAL
            $nuNupSiorg = $digital->getNuDigital()->getNuNupSiorg();

            if( $nuNupSiorg == '' ) {
                throw new \Exception("Número Nup Siorg não definido.");
            }

            $nuArtefato = $nuNupSiorg;
        } else {
            $nuArtefato = $this->getServiceLocator()
                               ->getService('ProcessoEletronico')
                               ->getNovoNumeroProcesso();

            $this->getServiceLocator()
                 ->getService('SequencialArtefato')
                 ->setSequencialProcesso(\Core_Integration_Sica_User::getUserUnit());
        }

        $entity->setNuArtefato($nuArtefato);

        // PESQUISA TIPO ARTEFATO ASSUNTO.
        $sqTpArtAssunto = $this->_getRepository('app:TipoArtefatoAssunto')
                               ->findOneBy(array(
                                    'sqAssunto' => $searchDto->getSqAssunto(),
                                    'sqTipoArtefato' => \Core_Configuration::getSgdoceTipoArtefatoProcesso()
        ));
                    
        if( !$sqTpArtAssunto ) {
            $entityAssunto = $this->getEntityManager()
                                  ->getPartialReference('app:Assunto', $searchDto->getSqAssunto());
            $entityTipoArtefato = $this->getEntityManager()
                                       ->getPartialReference('app:TipoArtefato', \Core_Configuration::getSgdoceTipoArtefatoProcesso());

            $sqTpArtAssunto = $this->_newEntity('app:TipoArtefatoAssunto');
            $sqTpArtAssunto->setSqAssunto($entityAssunto);
            $sqTpArtAssunto->setSqTipoArtefato($entityTipoArtefato);

            // persistindo informacao
            $this->getEntityManager()->persist($sqTpArtAssunto);
            $this->getEntityManager()->flush($sqTpArtAssunto);
        }
        
        $entity->setSqTipoArtefatoAssunto($sqTpArtAssunto);
        $entity->setDtCadastro(\Zend_Date::now());
        $entity->setInEletronico(false);


        // trantando atributos
        if ($searchDto->getInDiasCorridos() == '1') { //Corridos
            $entity->setInDiasCorridos(TRUE);
        } else if ($searchDto->getInDiasCorridos() == '0') { //Uteis
            $entity->setInDiasCorridos(FALSE);
        }else{
            $entity->setInDiasCorridos(NULL);
        }

        ## Tipo Prioridade ##
        $entSqTipoPrioridade = $this->getEntityManager()->getPartialReference('app:TipoPrioridade',$entity->getSqTipoPrioridade());
        $entity->setSqTipoPrioridade($entSqTipoPrioridade);
    }

    /**
     * @param Core_Model_Entity_Abstract $entity
     * @param type $dto
     */
    public function postInsert($entity, $dto = null, $artefatoProcesso = null, $digital = null)
    {
        $retorno = false;

        $this->getEntityManager()->beginTransaction();

        try {
            // salva o artefato_processo
            $artefatoProcesso->setSqArtefato($entity);
            // Ao autuar um documento sempre vai abrir um volume, iniciando pela página um.
            // Adicionando um volume na linha 148 deste arquivo.
            $artefatoProcesso->setNuVolume(1);
            $artefatoProcesso->setNuPaginaProcesso(1);
            $this->getServiceLocator()
                 ->getService('ProcessoEletronico')
                 ->saveArtefatoProcesso($artefatoProcesso);

            $arrPesArtDto = array(
                'entity' => 'Sgdoce\Model\Entity\PessoaArtefato',
                'mapping' => array(
                    'sqPessoaFuncao' => 'Sgdoce\Model\Entity\PessoaFuncao',
                    'sqPessoaSgdoce' => 'Sgdoce\Model\Entity\PessoaSgdoce',
                    'sqArtefato' => 'Sgdoce\Model\Entity\Artefato'
            ));

            $arrParams = array();
            $arrParams['sqArtefato'] = $entity->getSqArtefato();
            $arrParams['sqPessoaFuncao'] = \Core_Configuration::getSgdocePessoaFuncaoAutor();
            $arrParams['sqPessoaSgdoce'] = current($this->getServiceLocator()
                                                        ->getService('PessoaSgdoce')->findBy(array(
                                                            'sqPessoaCorporativo' =>
                                                            \Core_Integration_Sica_User::getPersonId()
                                                        )))->getSqPessoaSgdoce();

            $objPessoaArtefatoDto = \Core_Dto::factoryFromData($arrParams, 'entity', $arrPesArtDto);
            $this->getServiceLocator()->getService('PessoaArtefato')->savePessoaArtefato($objPessoaArtefatoDto);

            $this->_salvaOrigem($entity, $dto);
            
            // RN - Caso não exista Grau de Acesso ao Artefato sera por default publico(1)
            if (!$dto->getSqGrauAcesso()) {
                $data = array('sqGrauAcesso' => \Core_Configuration::getSgdoceGrauAcessoPublico());
                $dtoAcesso = new \Core_Dto_Mapping($data, array_keys($data));
                $sqGrauAcesso = $this->_getRepository('app:GrauAcesso')->find($dtoAcesso->getSqGrauAcesso());
            } else {
                $sqGrauAcesso = $this->_getRepository('app:GrauAcesso')->find($dto->getSqGrauAcesso());
            }

            // realizando a persistencia do Grau de Acesso
            $this->getServiceLocator()->getService('Dossie')->persistGrauAcessoArtefato($entity, $sqGrauAcesso);
            
            /*
             * ##### INTERESSADO #####
             *
             * só é postado no create, em caso de edit os interessados são
             * manutenidos no proprio formulario
             *
             */
            if ($dto->getDataInteressado()){
                $dataIntessado = $dto->getDataInteressado();
                foreach( $dataIntessado->getApi() as $method){
                    $line = $dataIntessado->$method();

                    //metodo foi copiado e adaptado de Artefato_PessoaController::addInteressadoAction()
                    $add = $this->getServiceLocator()
                                ->getService('Documento')
                                ->addInteressado(array(
                                    'noPessoa'             => $line->getNoPessoa()
                                    ,'unidFuncionario'     => $line->getUnidFuncionario()
                                    ,'sqPessoaCorporativo' => $line->getSqPessoaCorporativo()
                                    ,'sqTipoPessoa'        => $line->getSqTipoPessoa()
                                    ,'sqPessoaFuncao'      => $line->getSqPessoaFuncao()
                                    ,'sqArtefato'          => $entity->getSqArtefato()
                        ));
                    if(!$add){
                        throw new \Core_Exception_ServiceLayer( $line->getNoPessoa(). ' já é um interessado deste documento.');
                    }
                }
            }

            /**
             * #### VOLUME ####
             */
            $sqPessoaAbertura = \Core_Integration_Sica_User::getPersonId();
            $sqUnidadeOrgAbertura = \Core_Integration_Sica_User::getUserUnit();

            $entPessoaAbertura      = $this->getEntityManager()
                                           ->getPartialReference('app:VwPessoa',
                                                        $sqPessoaAbertura);

            $entUnidadeOrgAbertura  = $this->getEntityManager()
                                           ->getPartialReference('app:VwUnidadeOrg',
                                                            $sqUnidadeOrgAbertura);

            $entVolume = $this->_newEntity('app:ProcessoVolume');
            $entVolume->setSqArtefato($entity);
            $entVolume->setNuVolume(1);
            $entVolume->setNuFolhaInicial(1);
            $entVolume->setDtAbertura(\Zend_Date::now());
            $entVolume->setSqPessoaAbertura($entPessoaAbertura);
            $entVolume->setSqUnidadeOrgAbertura($entUnidadeOrgAbertura);

            $this->getEntityManager()->persist($entVolume);
            /*
             * ##### (PEÇA) #####
             */
            $this->getServiceLocator()
                 ->getService('ArtefatoVinculo')
                 ->inserirPeca(array(
                     'parent' => $entity,
                     'child'  => $digital,
                 ),
                \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao()
            );
            
            // #HistoricoArtefato::save();
            $nuDigital = $digital->getNuDigital()->getNuEtiqueta();
            $dataAtual = new \Zend_Date(\Zend_Date::now());

            $nuProcesso = $this->getServiceLocator()
                               ->getService("Processo")
                               ->formataProcessoAmbitoFederal($entity);
            
            $strMessage = $this->getServiceLocator()
                               ->getService('HistoricoArtefato')
                               ->getMessage('MH012', $dataAtual->get("dd/MM/yyyy HH:mm:ss"), $nuProcesso);

            $this->getServiceLocator()
                 ->getService('HistoricoArtefato')
                 ->registrar($entity->getSqArtefato(),
                            \Core_Configuration::getSgdoceSqOcorrenciaCadastrar(),
                            $strMessage);

            // insere tramite
            $this->getServiceLocator()
                 ->getService('TramiteArtefato')
                 ->insertFirstTramite($entity->getSqArtefato());

            $this->getMessaging()
                 ->addSuccessMessage("Documento autuado gerando o processo " . $nuProcesso, "User");

            $retorno = $this->getEntityManager()->commit();

        } catch (\Exception $objException) {

            $this->getEntityManager()->rollback();

            $this->getMessaging()->addErrorMessage("[" . $objException->getCode() . "] " . $objException->getMessage(), "User");

            $retorno = $objException;
        }

        $this->getMessaging()->dispatchPackets();


        return $retorno;
    }

    /**
     * @return boolean
     */
    public function hasImage( $sqArtefato )
    {
        $return = true;
        try {
            if( $this->getServiceLocator()
                     ->getService('ArtefatoImagem')
                     ->hasImage($sqArtefato) == false ) {

                $entDigital = $this->getServiceLocator()
                                   ->getService('Artefato')
                                   ->find($sqArtefato);

                $nuDigital = $entDigital->getNuDigital()
                                        ->getNuEtiqueta();

                $this->getMessaging()->addErrorMessage("Digital " . $nuDigital . " sem imagem.", "User");
                $return = false;
            }
        } catch( \Core_Exception_ServiceLayer $exception ){
            $this->getMessaging()->addErrorMessage($exception->getMessage(), "User");
            $return = false;
        } catch( \Exception $exception ) {
            $this->getMessaging()->addErrorMessage($exception->getMessage(), "User");
            $return = false;
        }
        $this->getMessaging()->dispatchPackets();

        return $return;
    }

    /**
     * @return boolean
     */
    public function inAbreProcesso( $sqArtefato )
    {
        $artefato = $this->_getRepository('app:Artefato')
                         ->find($sqArtefato);
        if( $artefato ) {
            if($artefato->getSqTipoDocumento() == null){
                $this->getMessaging()->addErrorMessage("Artefato não é um documento.", "User");
            } else {
                $inAbreProcesso = $artefato->getSqTipoDocumento()->getInAbreProcesso();

                if(!$inAbreProcesso){
                    $this->getMessaging()->addErrorMessage("Documento não pode ser autuado.", "User");
                }
            }
        } else {
            $this->getMessaging()->addErrorMessage("Artefato não encontrado.", "User");
        }

        $this->getMessaging()->dispatchPackets();

        return $inAbreProcesso;
    }

    /**
     * @return boolean
     */
    public function isUnidadeProtocolizadora($withMessage = true)
    {
        $retorno = true;

        $sqUnidadeOrg = \Core_Integration_Sica_User::getUserUnit();

        $vwUnidadeOrg = $this->getServiceLocator()
                             ->getService('VwUnidadeOrg')
                             ->find($sqUnidadeOrg);

        if( $vwUnidadeOrg->getNuNup() == '' ) {
            if( $withMessage ) {
                $this->getMessaging()->addErrorMessage("Sua Unidade não pode autuar documento.", "User");
            }
            $retorno = false;
        }

        $this->getMessaging()->dispatchPackets();

        return $retorno;
    }
    
    /**
     * @return boolean
     */
    public function isDocumentoAutuado( $nuDigital, $withMessage = false )
    {                
        $entity = $this->findBy(array('nuDigital' => $nuDigital));
        $entity = current($entity);
        
        $data = $this->_getRepository('app:ArtefatoVinculo')->findBy(array(
            'sqArtefatoFilho' => $entity->getSqArtefato(),
            'sqTipoVinculoArtefato' => \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao()
        ));
        
        if( count($data) ) {
            if( $withMessage ) {
                $this->getMessaging()->addErrorMessage("Documento já autuado.", "User");                
                $this->getMessaging()->dispatchPackets();
            }
            return true;
        }
        return false;
    }

    /**
     * @return boolean
     */
    public function listInteressados( $sqArtefato )
    {
        $dto = \Core_Dto::factoryFromData(array(
            'sqArtefato' => $sqArtefato,
            'sqPessoaFuncao' => \Core_Configuration::getSgdocePessoaFuncaoInteressado()
        ), 'search');

        return $this->getServiceLocator()
                             ->getService('Pessoa')
                             ->listInteressados($dto);

    }
}
