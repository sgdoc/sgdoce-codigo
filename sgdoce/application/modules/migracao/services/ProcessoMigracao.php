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

namespace Migracao\Service;

/**
 * Classe para Service de ProcessoEletronico
 *
 * @package  Artefato
 * @category     Service
 * @name         ProcessoEletronico
 * @version  1.0.0
 */
class ProcessoMigracao extends \Migracao\Service\ArtefatoMigracao {

    /**
     * @var string
     */
    protected $_entityName = 'app:Artefato';

    /**
     * @var boolean
     */
    protected $_firstTramite = true;



    /**
     * @var array
     */
    protected $_arInconsistencia = array(
        'f', // Origem
        't', // Destino
        'f', // Interessado
        'f', // Autor
        'f', // Assunto
        'f', // Datas
        'f'  // Imagem
    );

    /**
     *
     * @param type $entity
     * @param type $searchDto
     * @param type $artefatoProcesso
     */
    public function preInsert($entity, $searchDto = null, $artefatoProcesso = null)
    {
        $this->_firstTramite = true;
        $entity->setDtCadastro(\Zend_Date::now());
        $entity->setInEletronico(false);
    }

    /**
     * @param type $objEntity
     * @param type $objSearchDto
     * @param type $objArtProDto
     */
    public function preUpdate($entity, $dto = NULL)
    {
        $this->_firstTramite = false;
        $inEletronico = (boolean)$entity->getInEletronico();
        $entity->setInEletronico($inEletronico);
    }
    /**
     *
     * @param type $objEntity
     * @param type $objSearchDto
     * @param type $objArtProDto
     */
    public function preSave($objEntity, $objSearchDto = null, $objArtProDto = null) {
        $nuArtefato = $objEntity->getNuArtefato();

        if( $objArtProDto->getCoAmbitoProcesso() == 'F'
            && strlen($nuArtefato) == 20 ) {
            $nuArtefato = preg_replace('/[^a-zA-Z0-9]/', '', $nuArtefato);
        }

        $objEntity->setNuArtefato($nuArtefato);
        // PESQUISA TIPO ARTEFATO ASSUNTO.
        $entityTipoArtefatoAssunto = $this->_getRepository('app:TipoArtefatoAssunto')
                                  ->findOneBy(array(
                                            'sqAssunto' => $objSearchDto->getSqAssunto(),
                                            'sqTipoArtefato' => \Core_Configuration::getSgdoceTipoArtefatoProcesso()
        ));

        if( !$entityTipoArtefatoAssunto ) {

            $entityAssunto = $this->getEntityManager()
                                  ->getPartialReference('app:Assunto', $objSearchDto->getSqAssunto());
            $entityTipoArtefato = $this->getEntityManager()
                                       ->getPartialReference('app:TipoArtefato', \Core_Configuration::getSgdoceTipoArtefatoProcesso());

            $entityTipoArtefatoAssunto = $this->_newEntity('app:TipoArtefatoAssunto');
            $entityTipoArtefatoAssunto->setSqAssunto($entityAssunto);
            $entityTipoArtefatoAssunto->setSqTipoArtefato($entityTipoArtefato);

            // persistindo informacao
            $this->getEntityManager()->persist($entityTipoArtefatoAssunto);
            $this->getEntityManager()->flush($entityTipoArtefatoAssunto);
        }

        $objEntity->setSqTipoArtefatoAssunto($entityTipoArtefatoAssunto);

            // Assunto consistente
        if( !is_null($entityTipoArtefatoAssunto) ){
            $this->_arInconsistencia[4] = 't';
        }

        if( $objEntity->getDtPrazo() == '' ) {
           $objEntity->setDtPrazo(null);
        }

        if( $objEntity->getSqTipoPrioridade() ){
            $sqTipoPrioridade = $objEntity->getSqTipoPrioridade()
                                          ->getSqTipoPrioridade();
            $objTipoPrioridade = $this->getServiceLocator()
                                      ->getService('TipoPrioridade')
                                      ->find($sqTipoPrioridade);
            $objEntity->setSqTipoPrioridade($objTipoPrioridade);
        }

        $objEntity->setStMigracao(TRUE);

    }

    /**
     * @param type $entity
     * @param type $dto
     * @param type $objArtProDto
     */
    public function postSave($entity, $dto = null, $objArtProDto = null) {

        $retorno = false;

        $this->getEntityManager()->beginTransaction();
        $sqPessoaCorporativo = \Core_Integration_Sica_User::getPersonId();
        try {
            // salva o artefato_processo
            $objArtProDto->setSqArtefato($entity);
            $this->saveArtefatoProcesso($objArtProDto);

            $arrPesArtDto = array(
                'entity' => 'Sgdoce\Model\Entity\PessoaArtefato',
                'mapping' => array(
                    'sqPessoaFuncao' => 'Sgdoce\Model\Entity\PessoaFuncao',
                    'sqPessoaSgdoce' => 'Sgdoce\Model\Entity\PessoaSgdoce',
                    'sqArtefato' => 'Sgdoce\Model\Entity\Artefato'
            ));

            $sqPessoaSgdoce = $this->_getRepository('app:PessoaSgdoce')->findBySqPessoaCorporativo($sqPessoaCorporativo);

            if (empty($sqPessoaSgdoce)) {
                $filter = new \Zend_Filter_Digits();

                $data['sqPessoaCorporativo'] = $this->_getRepository('app:VwPessoa')->find($sqPessoaCorporativo);
                $dtoPessoaSearch = \Core_Dto::factoryFromData($data, 'search');
                $cpfCnpjPassaportUnfiltered = $this->getServiceLocator()->getService('VwPessoa')->returnCpfCnpjPassaporte($dtoPessoaSearch);
                $cpfCnpjPassaport = $filter->filter($cpfCnpjPassaportUnfiltered);

                $noPessoaCorporativo = $data['sqPessoaCorporativo']->getNoPessoa();

                $this->addPessoaSgdoce($sqPessoaCorporativo, $noPessoaCorporativo, $cpfCnpjPassaport);
                $sqPessoaSgdoce = $this->_getRepository('app:PessoaSgdoce')->findBySqPessoaCorporativo($sqPessoaCorporativo);
            }

            $arrParams = array();
            $arrParams['sqArtefato'] = $entity->getSqArtefato();
            $arrParams['sqPessoaFuncao'] = \Core_Configuration::getSgdocePessoaFuncaoAutor();
            $arrParams['sqPessoaSgdoce'] = $sqPessoaSgdoce[0]->getSqPessoaSgdoce();
            $objPessoArtefato = $this->getServiceLocator()
                                     ->getService('PessoaArtefato')
                                     ->findBy($arrParams);

            if(!count($objPessoArtefato)) {
                $objPessoaArtefatoDto = \Core_Dto::factoryFromData($arrParams, 'entity', $arrPesArtDto);
                $this->getServiceLocator()
                     ->getService('PessoaArtefato')
                     ->savePessoaArtefato($objPessoaArtefatoDto);


            }
            // Autor
            $this->_arInconsistencia[3] = 't';

            $this->_salvaOrigem($entity, $dto);

            $this->_arInconsistencia[0] = 't';

            // SALVA GRAU DE ACESSO.
            if( $dto->getSqGrauAcesso() ) {
                $grauAcesso  = $this->getEntityManager()
                                    ->getPartialReference('app:GrauAcesso',  $dto->getSqGrauAcesso());
                $this->getServiceLocator()
                     ->getService('GrauAcessoArtefato')
                     ->saveGrauAcessoArtefato($entity, $grauAcesso);
            }

            /*
             * ##### VOLUME #####
             *
             * só é postado no create
             *
             */
            if ($dto->getDataVolume()){
                $dataIntessado = $dto->getDataVolume();
                $sqPessoaAbertura = \Core_Integration_Sica_User::getPersonId();
                $sqUnidadeOrgAbertura = \Core_Integration_Sica_User::getUserUnit();
                foreach( $dataIntessado->getApi() as $method){
                    $line = $dataIntessado->$method();

                    if(!(integer)$line->getNuVolume()){
                        throw new \Core_Exception_ServiceLayer( 'Volume não informado.' );
                    }

                    $nuFolhaFinal       = (integer)$line->getNuFolhaFinal();
                    $dtEncerramento     = null;

                    if( !empty($nuFolhaFinal) ) {
                        $dtEncerramento = \Zend_Date::now();
                    } else {
                        $nuFolhaFinal = null;
                    }

                    $add = $this->getServiceLocator()
                                ->getService('ProcessoVolume')
                                ->addVolume(array(
                                    'nuVolume'             => (integer)$line->getNuVolume()
                                    ,'nuFolhaInicial'      => (integer)$line->getNuFolhaInicial()
                                    ,'nuFolhaFinal'        => $nuFolhaFinal
                                    ,'sqArtefato'          => $entity->getSqArtefato()
                                    ,'sqPessoa'            => $sqPessoaAbertura
                                    ,'sqUnidadeOrg'        => $sqUnidadeOrgAbertura
                                    ,'dtAbertura'          => \Zend_Date::now()
                                    ,'dtEncerramento'      => $dtEncerramento
                        ));

                    if(!$add){
                        throw new \Core_Exception_ServiceLayer( 'Erro ao adicionar volume.' );
                    }
                }
            }

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
                        throw new \Core_Exception_ServiceLayer( $line->getNoPessoa(). ' já é um interessado deste processo.');
                    }
                    $this->_arInconsistencia[2] = 't';

                }
            } else {

                $dtoInteressado = \Core_Dto::factoryFromData(array('sqArtefato' => $entity->getSqArtefato()), 'search');

                $nuInteressados = $this->getServiceLocator()
                                       ->getService('PessoaInterassadaArtefato')
                                       ->countInteressadosArtefato($dtoInteressado);

                if( $nuInteressados['nu_interessados'] > 0 ) {
                    $this->_arInconsistencia[2] = 't';
                } else {
                    throw new \Core_Exception_ServiceLayer(\Core_Registry::getMessage()->translate('MN176'));
                }

            }

            /*
             * ##### REFERÊNCIA (VINCULO) #####
             *
             * só é postado no create, em caso de edit os vinculos são
             * manutenidos no proprio formulario
             *
             */
            if ($dto->getDataVinculo()){ //só é postado no create
                $dataVinculo = $dto->getDataVinculo();
                foreach( $dataVinculo->getApi() as $method){
                    $gridLine = $dataVinculo->$method();

                    //metodo foi copiado e adaptado de Artefato_DocumentoController::addDocumentoEletronicoAction()
                    $add = $this->getServiceLocator()
                                ->getService('Documento')
                                ->addVinculo(array(
                        'nuDigital' => $gridLine->getNuDigital()
                        ,'nuArtefatoVinculacao' => $gridLine->getNuArtefatoVinculacao()
                        ,'sqTipoArtefato' => $gridLine->getSqTipoArtefato()
                        ,'sqArtefato' => $entity->getSqArtefato()
                        ,'tipoVinculo' => \Core_Configuration::getSgdoceTipoVinculoArtefatoReferencia()
                        ,'inOriginal'=>$gridLine->getInOriginal()
                    ));
                    if(!$add){
                        $msg = "A digital <b>{$gridLine->getNuDigital()}</b> já esta vinculada a este documento";
                        if($gridLine->getSqTipoArtefato() == \Core_Configuration::getSgdoceTipoArtefatoProcesso()){
                            $msg = "O processo <b>{$gridLine->getNuArtefatoVinculacao()}</b> já esta vinculado a este processo.";
                        }
                        throw new \Core_Exception_ServiceLayer($msg);
                    }
                }
            }

            // #HistoricoArtefato::save();
            $dtAcao         = new \Zend_Date(\Zend_Date::now());

            #Datas default
            $this->_arInconsistencia[5] = 't';

            # Se estiver tudo corrigido, insere tramite se tiver que inserir.
            # existe um parametro no form que indica se o tramite deve ser inserido
            # pois o documento já poderá estar na area de trabalho da pessoa (neste caso nao insere)
            if( !in_array('f', $this->_arInconsistencia) && $dto->getPersistTramite()) {
                $this->getServiceLocator()
                     ->getService('VinculoMigracao')
                     ->setArtefatoCorrigido($entity->getSqArtefato());
            }else{
                $this->_arInconsistencia[6] = 't';
            }

            $arInconsistencia = implode(",",$this->_arInconsistencia);
            $arInconsistencia = "{".$arInconsistencia."}";
            $entity->setArInconsistencia($arInconsistencia);

            // persistindo informacao
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush($entity);

            // #HistoricoArtefato::save();
            $strMessage = $this->getServiceLocator()
                    ->getService('HistoricoArtefato')
                    ->getMessage('MH022');

            $this->getServiceLocator()
                 ->getService('HistoricoArtefato')
                 ->registrar($entity->getSqArtefato(),
                             \Core_Configuration::getSgdoceSqOcorrenciaCorrigirMigracao(),
                             $strMessage);


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
     * @param \Core_Dto_Abstract $dto
     * @return array
     */
    public function findTipoArtefato($dto) {
        return $entityTipoArtefatoAssunto = $this->_getRepository('app:Artefato')
                ->findTipoArtefato($dto);
    }

    /**
     * @param unknown $entity
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    public function validateError($entity) {
        $hasErrors = FALSE;
        if (!$this->validaNumeroProcesso($entity)) {
            $hasErrors = TRUE;
            $this->getMessaging()->addErrorMessage('MN019');
        }
        if ($this->hasNumeroProcesso($entity)) {
            $hasErrors = TRUE;
            $this->getMessaging()->addErrorMessage('MN020');
        }
        if ($hasErrors) {
            throw new \Core_Exception_ServiceLayer_Verification();
        }
    }

    /**
     * @param unknown $entityArtefatoProcesso
     */
    public function saveArtefatoProcesso($entityArtefatoProcesso) {
        $entityArtefato = $entityArtefatoProcesso->getEntity()->getSqArtefato();
        $entityArtefato = $this->_getRepository('app:Artefato')->find($entityArtefato->getSqArtefato());

        $entityProcesso = $this->_getRepository('app:ArtefatoProcesso')->findOneBy(
                array('sqArtefato' => $entityArtefato->getSqArtefato()));
        if (!$entityProcesso) {
            $entity = new \Sgdoce\Model\Entity\ArtefatoProcesso();
            if ($entityArtefatoProcesso->getSqEstado()->getSqEstado()) {
                $entityVwEstado = $entityArtefatoProcesso->getEntity()->getSqEstado();
                $entityVwEstado = $this->_getRepository('app:VwEstado')->find($entityVwEstado->getSqEstado());
                $entity->setSqEstado($entityVwEstado);
            }
            if ($entityArtefatoProcesso->getSqMunicipio()->getSqMunicipio()) {
                $entityVwMunicipio = $entityArtefatoProcesso->getEntity()->getSqMunicipio();
                $entityVwMunicipio = $this->_getRepository('app:VwMunicipio')->find($entityVwMunicipio->getSqMunicipio());
                $entity->setSqMunicipio($entityVwMunicipio);
            }
            $entityArtefato->setDtArtefato(new \Zend_Date());
            if( $entityArtefato->getDtPrazo() == '' ) {
                $entityArtefato->setDtPrazo(null);
            }
            $entity->setSqArtefato($entityArtefato);
            $entity->setCoAmbitoProcesso($entityArtefatoProcesso->getCoAmbitoProcesso());

            $entity->setNuPaginaProcesso((integer)$entityArtefatoProcesso->getNuPaginaProcesso());
            if ($entityArtefatoProcesso->getInNumeracaoVerso() != '') {
                $entity->setInNumeracaoVerso($entityArtefatoProcesso->getInNumeracaoVerso());
            }

            $entity->setNuVolume((integer)$entityArtefatoProcesso->getNuVolume());

            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush($entity);
        } else {

            if ($entityArtefatoProcesso->getSqEstado()->getSqEstado()) {
                $entityVwEstado = $entityArtefatoProcesso->getEntity()->getSqEstado();
                $entityVwEstado = $this->_getRepository('app:VwEstado')->find($entityVwEstado->getSqEstado());
                $entityProcesso->setSqEstado($entityVwEstado);
            }
            if ($entityArtefatoProcesso->getSqMunicipio()->getSqMunicipio()) {
                $entityVwMunicipio = $entityArtefatoProcesso->getEntity()->getSqMunicipio();
                $entityVwMunicipio = $this->_getRepository('app:VwMunicipio')->find($entityVwMunicipio->getSqMunicipio());
                $entityProcesso->setSqMunicipio($entityVwMunicipio);
            }
            $entityProcesso->setNuPaginaProcesso((integer)$entityArtefatoProcesso->getNuPaginaProcesso());

            if ($entityArtefatoProcesso->getInNumeracaoVerso() != '') {
                $entityProcesso->setInNumeracaoVerso($entityArtefatoProcesso->getInNumeracaoVerso());
            }

            $entityProcesso->setNuVolume((integer)$entityProcesso->getNuVolume());
            $this->getEntityManager()->persist($entityProcesso);
            $this->getEntityManager()->flush($entityProcesso);
        }
    }

    /**
     * TROCA \r\n PARA \n.
     *
     * @param text $text
     * @return text
     */
    public function fixNewlines($text) {
        return str_replace(array("\r\n", "\r"), "\n", $text);
    }

    /**
     * @param unknown $entity
     * @return boolean
     */
    public function validaNumeroProcesso($entity) {
        return TRUE;
    }

    /**
     * @param unknown $entity
     * @return boolean
     */
    public function hasNumeroProcesso($entity) {
        return FALSE;
    }

    /**
     * método para pesquisa de grid de material de apoio
     * @param \Core_Dto_Search $dto
     */
    public function listGridTemaTratado(\Core_Dto_Search $dto) {
        $result = $this->_getRepository('app:ProcessoCaverna')->searchPageDto('listGridTemaTratado', $dto, FALSE);
        return $result;
    }

    /**
     * @param string $numero
     * @return string
     */
    public function calcularDigitoVerificador( $numero )
    {

        $_numero = strrev($numero);

        if( strlen($_numero) < 15 ) {
            // Número inválido.
            return false;
        }

        $end = true;
        $div1 = false;
        $div2 = false;

        do {

            $_listNumero = str_split($_numero);

            $div = 0;
            $pes = 2;

            foreach( $_listNumero as $key => $val ) {
                $div += ($pes * $val);
                ++$pes;
            }

            $div = $div % 11;
            $div = 11 - $div;

            if( $div > 9 ){ $div = substr($div, 1, 1); }
            $_numero = $div . $_numero;

            if( $div1 === false ) {
                $div1 = $div;
            } else if( $div2 === false ) {
                $div2 = $div;
                $end  = false;
            }

        } while( $end );

        return $numero . $div1 . $div2;
    }

    /**
     * Metódo que verifica se o modelo está cadastrado
     * @return boolean
     */
    public function checkProcessoCadastrado(\Core_Dto_Search $dtoSearch) {
        $nuArtefato = preg_replace('/[^a-zA-Z0-9]/', '', $dtoSearch->getNuArtefato());
        $return = $this->_getRepository()->findByNuArtefato($nuArtefato);

        if (count($return) > 0) {

            return TRUE;

        } else {
            $nuArtefato = preg_replace('/[^a-zA-Z0-9\.\-\/]/', '', $dtoSearch->getNuArtefato());
            $return = $this->_getRepository()->findByNuArtefato($nuArtefato);

            if (count($return) > 0) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * @param \Core_Dto $dto
     */
    public function findArtefatoPecaProcesso($dto) {
        return $this->_getRepository('app:Artefato')->findArtefatoPecaProcesso($dto);
    }

    /**
     * @param \Core_Dto $dto
     * @return multitype:
     */
    public function findSqArtefatoPai($dto) {
        $criteria = array('sqArtefatoPai' => $dto->getSqArtefato(),
            'dtRemocaoVinculo' => NULL,
            'sqTipoVinculoArtefato' => \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao()
        );

        return $this->_getRepository('app:ArtefatoVinculo')->findBy($criteria);
    }

    /**
     * MÉTODO RETORNA OS PROCESSOS NA AREA DE TRABALHO DA PESSOA LOGADA.
     *
     * @return array
     */
    public function searchInMyDashboard( $criteria )
    {
        $listInMyDashboard = $this->_getRepository('app:VwAreaTrabalho')->searchProcEletAutoComplete($criteria);

        $listProcessosEletronico = array();
        $repoArtefato = $this->_getRepository('app:Artefato');
        $serviceProcesso = $this->getServiceLocator()->getService('Processo');
        foreach( $listInMyDashboard as $ProcessoEletronico ) {
            $entityArtefato = $repoArtefato->find($ProcessoEletronico['sqArtefato']);
            $listProcessosEletronico[$ProcessoEletronico['sqArtefato']] =
                    $serviceProcesso->formataProcessoAmbitoFederal($entityArtefato);
        }

        return $listProcessosEletronico;
    }

    /**
     * @return boolean
     */
    public function hasProcessoEletronico( $sqArtefato )
    {
        return $this->_getRepository('app:ArtefatoProcesso')->find($sqArtefato);
    }

    /**
     * @return string
     */
    public function getNovoNumeroProcesso()
    {
        $sqUnidadeOrg = \Core_Integration_Sica_User::getUserUnit();
        $nowZd = \Zend_Date::now();

        $vwUnidadeOrg = $this->getServiceLocator()
                             ->getService('VwUnidadeOrg')
                             ->find($sqUnidadeOrg);

        if( $vwUnidadeOrg->getNuNup() == '' ) {
            throw new \Exception("Unidade não protocolorizadora.");
        }

        $nuSequencial = $this->getServiceLocator()
                             ->getService('SequencialArtefato')
                             ->getNextSequencialProcesso();

        $nuArtefato = str_pad($vwUnidadeOrg->getNuNup(), 5, '0', STR_PAD_LEFT);
        $nuArtefato.= str_pad($nuSequencial->getNuSequencial(), 6, '0', STR_PAD_LEFT);
        $nuArtefato.= $nowZd->get(\Zend_Date::YEAR);

        return $this->calcularDigitoVerificador($nuArtefato);
    }

    /**
     * método para pesquisa de grid de material de apoio
     * @param \Core_Dto_Search $dto
     */
    public function listGrid(\Core_Dto_Search $dto) {
        $result = $this->_getRepository('app:ArtefatoProcesso')->searchPageDto('listGridProcesso', $dto, FALSE);
        return $result;
    }
}
