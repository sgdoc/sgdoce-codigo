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

use Doctrine\Common\Util\Debug;

/**
 * Classe para Service de Artefato
 *
 * @package  Minuta
 * @category Service
 * @name     Artefato
 * @version  1.0.0
 */
class DocumentoMigracao extends \Migracao\Service\ArtefatoMigracao
{


    /**
     * @var array
     */
    protected $_arInconsistencia = array(
        'f', // Origem
        'f', // Destino
        't', // Interessado
        'f', // Autor
        'f', // Assunto
        't', // Datas
        'f'  // Imagem
    );
    /**
     * @var string
     */
    protected $_entityName   = 'app:Artefato';
    protected $_msgDigital   = false;
    protected $_msgNumero    = false;
    protected $_firstTramite = false;

    public function findDigital (\Core_Dto_Search $dtoSearch)
    {
        return $this->getEntityManager()->getRepository($this->_entityName)
                        ->verificaDigital($dtoSearch);
    }

    /**
     *
     * @param \Core_Dto_Search $dtoSearch
     * @return boolean
     */
    public function verificaLiberacaoDigital (\Core_Dto_Search $dtoSearch)
    {
        return $this->getEntityManager()->getRepository('app:LoteEtiqueta')
                        ->verificaLiberacaoDigital($dtoSearch);
    }

    /**
     *
     * @param \Core_Dto_Search $dtoSearch
     * @return boolean
     */
    public function verificaLiberacaoDigitalEletronica (\Core_Dto_Search $dtoSearch)
    {
        return $this->getEntityManager()->getRepository('app:LoteEtiqueta')
                        ->verificaLiberacaoDigitalEletronica($dtoSearch);
    }

    /**
     *
     * @param \Core_Dto_Search $dtoSearch
     * @return boolean
     */
    public function verificaDigitalEmUso (\Core_Dto_Search $dtoSearch)
    {
        return $this->_getRepository('app:EtiquetasUso')
                        ->verificaDigitalEmUso($dtoSearch);
    }

    /**
     * Metódo que realiza o preSave.
     * @param Object $entity
     * @param Object $dto
     * @param Object $entityPrioridade
     * @param Object $entityGrauArtefato
     */
    public function preSave ($entity, $dto = NULL, $entityPrioridade = NULL, $entityGrauArtefato = NULL)
    {
        $this->getEntityManager()->getConnection()->beginTransaction();
        try {

            // [RN2.7] - verificando se existe um numero de digital. Caso não exista, gera-se um novo numero e salva.
            $nuDigital = $entity->getNuDigital()->getNuEtiqueta();

            if (!$entity->getSqArtefato()) {
                $entity->setDtCadastro(\Zend_Date::now());
            }
            if (!trim($entity->getDtPrazo())) {
                $entity->setDtPrazo(null);
            }

            $this->_validateAssuntoHomologado($dto->getSqAssunto());


            $entityTipoArtefatoAssunto = $this->_getRepository('app:TipoArtefatoAssunto')
                    ->findOneBy(array('sqAssunto' => $dto->getSqAssunto(), 'sqTipoArtefato' => $dto->getSqTipoArtefato()));

            if( !$entityTipoArtefatoAssunto ) {

                $entityAssunto = $this->getEntityManager()
                                      ->getPartialReference('app:Assunto', $dto->getSqAssunto());
                $entityTipoArtefato = $this->getEntityManager()
                                           ->getPartialReference('app:TipoArtefato', $dto->getSqTipoArtefato());

                $entityTipoArtefatoAssunto = $this->_newEntity('app:TipoArtefatoAssunto');
                $entityTipoArtefatoAssunto->setSqAssunto($entityAssunto);
                $entityTipoArtefatoAssunto->setSqTipoArtefato($entityTipoArtefato);

                // persistindo informacao
                $this->getEntityManager()->persist($entityTipoArtefatoAssunto);
                $this->getEntityManager()->flush($entityTipoArtefatoAssunto);
            }

            $entity->setSqTipoArtefatoAssunto($entityTipoArtefatoAssunto);

            // Assunto consistente
            if( !is_null($entityTipoArtefatoAssunto) ){
                $this->_arInconsistencia[4] = 't';
            }

            // salvar Tipo Prioridade
            $entity->setSqTipoPrioridade(
                    $this->_getRepository('app:TipoPrioridade')->find($entityPrioridade->getSqTipoPrioridade()));

            // trantando atributos
            if ($dto->getInDiasCorridos() == 'S') { //Corridos
                $entity->setInDiasCorridos(TRUE);
            } else if ($dto->getInDiasCorridos() == 'N') { //Uteis
                $entity->setInDiasCorridos(FALSE);
            }else{
                $entity->setInDiasCorridos(NULL);
            }

            $entity->getStMigracao($dto->getStMigracao());

            $entVwPessoa = null;

            if( $dto->getSqPessoaRecebimento() ){
                $arrDto = array(
                    'sqPessoa'  => $dto->getSqPessoaRecebimento()
                );

                $objCDto = \Core_Dto::factoryFromData($arrDto, 'search');

                $entVwPessoa    = $this->getServiceLocator()
                                       ->getService('Pessoa')
                                       ->findbyPessoaCorporativo($objCDto);
            }

            $entity->setSqPessoaRecebimento($entVwPessoa);

        } catch (\Exception $e) {
            $this->getEntityManager()->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * Metódo que realiza o post Save.
     * @param Object $entity
     * @param Object $dto
     */
    public function postSave ($entity, $dto = NULL)
    {
        try {

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

            // A ação de correção da migração somente irá atualizar os artefatos.
            $strMessage = $this->getServiceLocator()
                    ->getService('HistoricoArtefato')
                    ->getMessage('MH006',
                            \Zend_Date::now()->toString('dd/MM/YYYY HH:mm:ss'),
                            \Core_Integration_Sica_User::getUserName());

            $nuOcorrencia = \Core_Configuration::getSgdoceSqOcorrenciaAlterar();

            $this->getServiceLocator()
                 ->getService('HistoricoArtefato')
                 ->registrar($entity->getSqArtefato(),
                             $nuOcorrencia,
                             $strMessage);

            // verificando atributo
            // noResponsavelAssinatura vem o sqPessoa selecionado no autocomplete
            if ($dto->getNoResponsavelAssinatura() != NULL) {

                /**
                * em caso de update de documento de origem externa ou com tipo de documento sem assinatura e que não tiver assinante
                * efetua a exclusão caso ja tenha sido preenchido
                * anteriormente
                */
                if ($entity->getSqArtefato() && !$dto->getNoResponsavelAssinatura()) {
                   $this->_checkExcluirAssinante($entity, $dto);
                } else {
                    $sqPessoaResponsavelAssinatura =
                        $dto->getNoResponsavelAssinatura_hidden() ?
                            $dto->getNoResponsavelAssinatura_hidden() :
                                $dto->getNoResponsavelAssinatura();

                    $noPessoaResponsavelAssinatura =
                        $dto->getNoResponsavelAssinatura_autocomplete() ?
                            $dto->getNoResponsavelAssinatura_autocomplete() :
                                $dto->getNoResponsavelAssinatura();

                    if (!is_numeric($sqPessoaResponsavelAssinatura)) {
                        throw new \Core_Exception_ServiceLayer_Verification(
                                'Ocorreu um erro na identificação do assintante do documento. '
                                . "Preencha o campo <b>Assinatura</b> novamente.");
                    }

                    $entityPessoa = $this->pessoaCorporativoSgdoce(
                        $sqPessoaResponsavelAssinatura, $noPessoaResponsavelAssinatura
                    );

                    $pessoaArtefato = $this->getServiceLocator()->getService('Artefato')->cadastrarPessoaArtefato(
                            $entity, $entityPessoa, \Core_Configuration::getSgdocePessoaFuncaoAssinatura()
                    );

                    $this->getEntityManager()->persist($pessoaArtefato);
                    $this->getEntityManager()->flush($pessoaArtefato);

                    // verificando se existe registro em PessoaAssinanteArtefato
                    if ($dto->getProcedenciaInterno() != 'externo') {

                        //só quando é unidade
                        if ($dto->getSqPessoaOrigem() != '') {
                            $sqPessoaOrigem = NULL;
                        } else {
                            $sqPessoaOrigem = $dto->getSqPessoaIcmbio();
                        }

                        $pessoaUnidadeOrg = $this->hasPessoaUnidadeOrg($entityPessoa, $dto->getNoPessoaFuncaoAssinante(), $sqPessoaOrigem);

                        $criteria = array('sqArtefato' => $entity->getSqArtefato());
                        $entPessoaAssinante = $this->_getRepository('app:PessoaAssinanteArtefato')->findOneBy($criteria);

                        // verificando se existe registro
                        if (count($entPessoaAssinante)) {
                            // atualizando PessoaAssinanteArtefato
                            $entPessoaAssinante->setSqPessoaUnidadeOrg($pessoaUnidadeOrg);
                            if( $dto->getNoPessoaFuncaoAssinante() != $entPessoaAssinante->getNoCargoAssinante() ) {
                                $entPessoaAssinante->setNoCargoAssinante($dto->getNoPessoaFuncaoAssinante());
                            }
                        } else {
                            // Preparando Entidade para salvar
                            /** @var PessoaAssinanteArtefato $resPessoaAssinante */
                            $entPessoaAssinante = $this->_newEntity('app:PessoaAssinanteArtefato');
//                            $entPessoaAssinante->setSqArtefato($this->_getRepository('app:Artefato')->find($entity->getSqArtefato()));
                            $entPessoaAssinante->setSqArtefato($entity);
                            $entPessoaAssinante->setSqPessoaUnidadeOrg($pessoaUnidadeOrg);
                            $entPessoaAssinante->setNoCargoAssinante($dto->getNoPessoaFuncaoAssinante());
                        }

                        // salvando PessoaAssinanteArtefato
                        $this->getEntityManager()->persist($entPessoaAssinante);
                        $this->getEntityManager()->flush($entPessoaAssinante);
                    }
                }
            } else {
                /**
                 * em caso de update de documento de origem externa e não tiver assinante efetua a exclusão caso ja tenha sido preenchido
                 * anteriormente
                 */
                $this->_checkExcluirAssinante($entity, $dto);
            }

            $pessoaArtefatoAutor = $this->_addAutorDocumento($entity);
            $this->getEntityManager()->persist($pessoaArtefatoAutor);
            $this->getEntityManager()->flush($pessoaArtefatoAutor);

            //sqPrazo = 1 (data) e sqPrazo = 2 (dias)
            $entity->setDtPrazo(($dto->getSqPrazo() == 2 || !$dto->getSqPrazo()) ? NULL : $dto->getDtPrazo() );
            $entity->setNuDiasPrazo(($dto->getNuDiasPrazo() == '') ? NULL : $dto->getNuDiasPrazo());

            //Tira os Espaços do 'enter' para salvar com 250 caracteres
            $txAssuntoComplementar = $this->getServiceLocator()->getService('MinutaEletronica')
                    ->fixNewlines($entity->getTxAssuntoComplementar());
            $entity->setTxAssuntoComplementar((!$txAssuntoComplementar) ? NULL : $txAssuntoComplementar);

            // salvando Origem e Destino
            self::salvaOrigemDestino($entity, $dto);

            $this->_arInconsistencia[0] = 't';
            $this->_arInconsistencia[1] = 't';

            // Verifica se a imagem esta confirmada.
            $dtoSearch = \Core_Dto::factoryFromData(array('sqArtefato' => $entity->getSqArtefato()), 'search');
            if(  $this->getServiceLocator()
                      ->getService("Artefato")
                      ->isInconsistent($dtoSearch, true) ) {
                $this->_arInconsistencia[6] = 'f';
            } else {
                $this->_arInconsistencia[6] = 't';
            }

            if ($entity->getNuDigital() && !($entity->getNuDigital() instanceof \Sgdoce\Model\Entity\EtiquetaNupSiorg)) {
                $entityEtiquetaNupSiorg = $this->getEntityManager()->getPartialReference('app:EtiquetaNupSiorg', $entity->getNuDigital());
                $entity->setNuDigital($entityEtiquetaNupSiorg);
            }


            // Histórico
            // salva o historico do artefato
            $arrDto = array(
                'sqPessoa' => \Core_Integration_Sica_User::getPersonId(),
                'sqUnidade' => \Core_Integration_Sica_User::getUserUnit()
            );

            // #HistoricoArtefato::save();
            $strMessage = $this->getServiceLocator()
                    ->getService('HistoricoArtefato')
                    ->getMessage('MH022');

            $this->getServiceLocator()
                 ->getService('HistoricoArtefato')
                 ->registrar($entity->getSqArtefato(),
                             \Core_Configuration::getSgdoceSqOcorrenciaCorrigirMigracao(),
                             $strMessage);

            $entity->setStMigracao(TRUE);
            // persistindo informacao
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush($entity);

            /*
             * ##### INTERESSADO #####
             *
             * só é postado no create, em caso de edit os interessados são
             * manutenidos no proprio formulario
             *
             */
            if ($dto->getDataInteressado()) {
                $dataIntessado = $dto->getDataInteressado();
                foreach ($dataIntessado->getApi() as $method) {
                    $line = $dataIntessado->$method();

                    //metodo foi copiado e adaptado de Artefato_PessoaController::addInteressadoAction()
                    $add = $this->addInteressado(array(
                        'noPessoa' => $line->getNoPessoa()
                        , 'unidFuncionario' => $line->getUnidFuncionario()
                        , 'sqPessoaCorporativo' => $line->getSqPessoaCorporativo()
                        , 'sqTipoPessoa' => $line->getSqTipoPessoa()
                        , 'sqPessoaFuncao' => $line->getSqPessoaFuncao()
                        , 'sqArtefato' => $entity->getSqArtefato()
                    ));
                    if (!$add) {
                        throw new \Core_Exception_ServiceLayer($line->getNoPessoa() . ' já é um interessado deste documento.');
                    }
                }
            }

            /*
             * ##### REFERÊNCIA (VINCULO) #####
             */
            if ($dto->getDataVinculo()) { //só é postado no create
                $dataVinculo = $dto->getDataVinculo();
                foreach ($dataVinculo->getApi() as $method) {
                    $gridLine = $dataVinculo->$method();

                    //metodo foi copiado e adaptado de Artefato_DocumentoController::addDocumentoEletronicoAction()
                    $add = $this->addVinculo(array(
                        'nuDigital' => $gridLine->getNuDigital()
                        , 'nuArtefatoVinculacao' => $gridLine->getNuArtefatoVinculacao()
                        , 'sqTipoArtefato' => $gridLine->getSqTipoArtefato()
                        , 'sqArtefato' => $entity->getSqArtefato()
                        , 'tipoVinculo' => \Core_Configuration::getSgdoceTipoVinculoArtefatoReferencia()
                        , 'inOriginal' => $gridLine->getInOriginal()
                    ));
                    if (!$add) {
                        $msg = "A digital <b>{$gridLine->getNuDigital()}</b> já esta vinculada a este documento";
                        if ($gridLine->getSqTipoArtefato() == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
                            $msg = "O processo <b>{$gridLine->getNuArtefatoVinculacao()}</b> já esta vinculado a este documento";
                        }
                        throw new \Core_Exception_ServiceLayer($msg);
                    }
                }
            }


            #processar anexos para SIC
            if (true === (boolean) $dto->getIsSic() && (integer)$dto->getUploader_count() > 0) {
                $this->_processaAnexoSIC($entity, $dto);
            }

            $this->_arInconsistencia[5] = 't';


            $inMyDashboard = $this->getServiceLocator()
                                ->getService("Artefato")
                                ->inMyDashboard($entity->getSqArtefato());

            # Se estiver tudo corrigido, insere tramite se tiver que inserir.
            if( !in_array('f', $this->_arInconsistencia) && !$inMyDashboard ) {
                $this->getServiceLocator()
                     ->getService('VinculoMigracao')
                     ->setArtefatoCorrigido($entity->getSqArtefato());
            }

            $arInconsistencia = implode(",",$this->_arInconsistencia);
            $arInconsistencia = "{".$arInconsistencia."}";
            $entity->setArInconsistencia($arInconsistencia);


            //implementar a exclusão dos interessados sem sq_corporativo

            $this->_getRepository('app:PessoaInteressadaArtefato')->deleteInteressadoSemSqCorporativo($dto);
            $this->_getRepository('app:PessoaArtefato')->deleteInteressadoSemSqCorporativo($dto);

            // persistindo informacao
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush($entity);

            $this->getEntityManager()->getConnection()->commit();
        } catch (\Exception $e) {
            $this->getEntityManager()->getConnection()->rollback();
            throw $e;
        }
    }

    public function postUpdate ($entity, $dto = NULL)
    {
        $this->getMessaging()->addSuccessMessage(\Core_Registry::getMessage()->translate('MD002'), 'User');
        $this->getMessaging()->dispatchPackets();
    }

    /**
     *
     * @param \Sgdoce\Model\Entity\Artefato $entity
     * @return \Sgdoce\Model\Entity\PessoaArtefato
     */
    private function _addAutorDocumento(\Sgdoce\Model\Entity\Artefato $entity)
    {
        $sqPessoaCorporativo = \Core_Integration_Sica_User::getPersonId();
        $sqPessoaSgdoceAutor = $this->_getRepository('app:PessoaSgdoce')->findBySqPessoaCorporativo($sqPessoaCorporativo);

        if (empty($sqPessoaSgdoceAutor)) {
            $filter = new \Zend_Filter_Digits();

            $data['sqPessoaCorporativo'] = $this->_getRepository('app:VwPessoa')->find($sqPessoaCorporativo);
            $dtoPessoaSearch = \Core_Dto::factoryFromData($data, 'search');
            $cpfCnpjPassaportUnfiltered = $this->getServiceLocator()
                                               ->getService('VwPessoa')
                                               ->returnCpfCnpjPassaporte($dtoPessoaSearch);
            $cpfCnpjPassaport = $filter->filter($cpfCnpjPassaportUnfiltered);

            $noPessoaCorporativo = $data['sqPessoaCorporativo']->getNoPessoa();
            $entityPessoaSgdoceAutor = $this->addPessoaSgdoce($sqPessoaCorporativo, $noPessoaCorporativo, $cpfCnpjPassaport);
        }else{
            $entityPessoaSgdoceAutor = $sqPessoaSgdoceAutor[0];
        }

        $pessoaArtefatoAutor = $this->getServiceLocator()->getService('Artefato')->cadastrarPessoaArtefato(
                $entity, $entityPessoaSgdoceAutor, \Core_Configuration::getSgdocePessoaFuncaoAutor()
        );

        $this->_arInconsistencia[3] = 't';

        return $pessoaArtefatoAutor;
    }


    private function _processaAnexoSIC(\Sgdoce\Model\Entity\Artefato $entity, $dto)
    {
        $i = 0;
        $nuDigital = $entity->getNuDigital()->getNuEtiqueta();

        while ($i < $dto->getUploader_count()) {
            $methodTmpName = "getUploader_{$i}_tmpname";
            $methodName    = "getUploader_{$i}_name";
            $methodStatus  = "getUploader_{$i}_status";

            if ('done' === $dto->$methodStatus()) {
                $tmpFileName = $dto->$methodTmpName();
                $realFileName= $dto->$methodName();

                $mufService = $this->getServiceLocator()->getService('MoveFileUpload');

                $moved = $mufService->setDestinationPath("upload/sic/{$nuDigital}")
                                    ->move($tmpFileName);

                if ($moved) {
                    $mufService->unlinkOrigemFile($tmpFileName);
                }
            }

            $fileExtension = pathinfo($realFileName, PATHINFO_EXTENSION);

            $entityCheck = $this->_getRepository('app:AnexoSic')->findOneBy(array(
                'noArquivoReal' => $realFileName,
                'sqArtefato' => $entity->getSqArtefato()));

            if (!is_null($entityCheck)) {
                throw new \Core_Exception_ServiceLayer_Verification("Este SIC já possui um anexo com o nome '{$realFileName}'");
            }

            $entityAnexoSic = $this->_newEntity('app:AnexoSic');
            $entityAnexoSic->setSqArtefato($entity)
                    ->setTxCaminhoArquivo($mufService->getDestinationPath($tmpFileName))
                    ->setNoArquivoReal($realFileName)
                    ->setDtCadastro(\Zend_Date::now())
                    ->setTxExtensaoArquivo($fileExtension);

            $this->getEntityManager()->persist($entityAnexoSic);
            $this->getEntityManager()->flush($entityAnexoSic);

            $i++;
        }

        return $this;
    }

    /**
     *
     * @param type $dto
     * @return \Sgdoce\Model\Entity\EtiquetasUso
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    private function _doInsertEtiquetaUso($dto, $docEletronico = false)
    {
        //se for eletronico tem que recuperar o proximo numero de etiqueta
        if ($docEletronico) {
            $sqUnidadeOrg = \Core_Integration_Sica_User::getUserUnit();
                $dtoSearchLote = \Core_Dto::factoryFromData(array(
                            'nuAno' => date('Y'),
                            'sqUnidadeOrg' => $sqUnidadeOrg,
                                ), 'search'
                );

            $novoNuDigital = $this->getServiceLocator()->getService('Artefato')
                        ->getNextElectronicDigitalNumber($dtoSearchLote);

            $entityEtiquetaNupSiorg = $this->_getRepository('app:EtiquetaNupSiorg')
                    ->findOneBy(array(
                        'sqLoteEtiqueta' => $novoNuDigital['sqLoteEtiqueta'],
                        'nuEtiqueta' => $novoNuDigital['nuDigital']
                    ));
        } else {

            //se a digital informada tiver nup associado e a procedencia for externa não pode.
            //tem q usar digital sem nup para procedencia externa
            $entityEtiquetaNupSiorg = $this->getDigitalNupSiorg($dto);
        }

        $nuNupSiorgVinculado = $entityEtiquetaNupSiorg->getNuNupSiorg(true);
        if ($nuNupSiorgVinculado && $dto->getProcedenciaInterno() == 'externo' && !$dto->getIsSic()) {
            throw new \Core_Exception_ServiceLayer_Verification(
                    sprintf(\Core_Registry::getMessage()->translate('MN167'), $nuNupSiorgVinculado));
        }
        if (!$nuNupSiorgVinculado && $dto->getProcedenciaInterno() == 'interno') {
            throw new \Core_Exception_ServiceLayer_Verification(
                        \Core_Registry::getMessage()->translate('MN168'));
        }

        //se for externo o nup pode ter sido informado no cadastro
        if (!$nuNupSiorgVinculado && $dto->getProcedenciaInterno() == 'externo' && $dto->getNuNup()) {
            $nupValidado = $this->_validaNumeroNUP($dto->getNuNup());
            $entityEtiquetaNupSiorg->setNuNupSiorg($nupValidado);
            $this->getEntityManager()->persist($entityEtiquetaNupSiorg);
            $this->getEntityManager()->flush($entityEtiquetaNupSiorg);
        }

        /** @var \Sgdoce\Model\Entity\EtiquetasUso $entityEtiquetasUso */
        $entityEtiquetasUso = $this->_newEntity('app:EtiquetasUso');

        $entityEtiquetasUso->setNuEtiqueta($entityEtiquetaNupSiorg);
        $entityEtiquetasUso->setSqLoteEtiqueta($entityEtiquetaNupSiorg);

        //persist Etiquetas Uso
        $this->getEntityManager()->persist($entityEtiquetasUso);
        $this->getEntityManager()->flush($entityEtiquetasUso);

        return $entityEtiquetasUso;
    }


    /**
     *
     * @param string $nuNupSiorg
     * @return string
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    private function _validaNumeroNUP($nuNupSiorg)
    {
        //Remove, se houver, os caracteres não numéricos de $snumero
        $nuNupSiorg = preg_replace('/\D+/', '', $nuNupSiorg);

        $anoNup      = (integer) substr($nuNupSiorg, -6, 4);
        $anoCorrente = (integer) date('Y');

        if ($anoNup > $anoCorrente){
            throw new \Core_Exception_ServiceLayer_Verification('Ano do Número Único de Protocolo não pode ser maior que o ano atual. Por favor, verifique.');
        }

        if (!$this->validaDigitoVerificadorNUP($nuNupSiorg)) {
            throw new \Core_Exception_ServiceLayer_Verification('Número Único de Protocolo inválido.');
        }

        return $nuNupSiorg;
    }

    public function validaDigitoVerificadorNUP($nuNupSiorg)
    {
        #Remove, se houver, os caracteres não numéricos de $nuNupSiorg
        return (integer) bcmod(preg_replace('/\D+/', '', $nuNupSiorg),97) === 1 ? true : false;
    }


    /**
     * metodo foi copiado e adaptado de Artefato_PessoaController::addInteressadoAction()
     * @param array $params
     * @return boolean true para sucesso e false para interessado já existente
     */
    public function addInteressado (array $params)
    {
        if (empty($params['sqTipoPessoa'])) {
            $dtoUnidSearch = \Core_Dto::factoryFromData($params, 'search');

            if ($params['unidFuncionario'] == 'unidade') {
                $arrUnid = $this->getServiceLocator()->getService('Processo')->searchVwUnidadeOrg($dtoUnidSearch);
            } else {
                $arrUnid = $this->getServiceLocator()->getService('Processo')->searchFuncionarioIcmbio($dtoUnidSearch);
            }

            $params['sqTipoPessoa'] = (isset($arrUnid['sqTipoPessoa'])) ? $arrUnid['sqTipoPessoa'] : null;
        }

        $dtoPessoaSgdoce = \Core_Dto::factoryFromData($params, 'entity', array('entity' => 'Sgdoce\Model\Entity\PessoaSgdoce',
                    'mapping' => array(
                        'sqTipoPessoa' => 'Sgdoce\Model\Entity\VwTipoPessoa'
                        , 'sqPessoaCorporativo' => array('sqPessoa' => 'Sgdoce\Model\Entity\VwPessoa'))));


        $sqPessoaSgdoce = $this->getServiceLocator()->getService('Pessoa')->findPessoaSgdoce($dtoPessoaSgdoce);
        if (!$sqPessoaSgdoce) {
            $data['sqPessoaCorporativo'] = $dtoPessoaSgdoce->getSqPessoaCorporativo()->getSqPessoa();
            $dtoPessoaSearch = \Core_Dto::factoryFromData($data, 'search');
            $dtoPessoaSgdoce->setNuCpfCnpjPassaporte($this->getServiceLocator()->getService('VwPessoa')->returnCpfCnpjPassaporte($dtoPessoaSearch));
            $return = $this->getServiceLocator()->getService('MinutaEletronica')->saveDestinatario($dtoPessoaSgdoce);
            $sqPessoaSgdoce = $return->getSqPessoaSgdoce();
        }

        $params['sqPessoaSgdoce'] = $sqPessoaSgdoce;

        $dtoPessoaArtefato = \Core_Dto::factoryFromData($params, 'entity', array('entity' => 'Sgdoce\Model\Entity\PessoaInteressadaArtefato',
                    'mapping' => array(
                        'sqArtefato' => 'Sgdoce\Model\Entity\Artefato'
                        , 'sqPessoaSgdoce' => 'Sgdoce\Model\Entity\PessoaSgdoce')));

        $criteria = array('sqPessoaSgdoce' => $dtoPessoaArtefato->getSqPessoaSgdoce()->getSqPessoaSgdoce()
            , 'sqArtefato' => $dtoPessoaArtefato->getSqArtefato()->getSqArtefato());

        $returnP = $this->getServiceLocator()->getService('PessoaInterassadaArtefato')->findBy($criteria);

        if ($returnP) {
            return false;
        }
        $this->getServiceLocator()->getService('PessoaInterassadaArtefato')->savePessoaInteressada($dtoPessoaArtefato);
        return true;
    }

    /**
     * metodo foi copiado e adaptado de Artefato_DocumentoController::addDocumentoEletronicoAction()
     * @return boolean true para sucesso e false para vinculo já existente
     */
    public function addVinculo (array $params)
    {

        $dto = \Core_Dto::factoryFromData($params, 'search');


        if ($dto->getNuArtefatoVinculacao()) {
            $artefato = $this->getServiceLocator()->getService('Artefato')->findBy(array('nuArtefato' => $dto->getNuArtefatoVinculacao()));
        } else {
            $artefato = $this->getServiceLocator()->getService('Artefato')->findBy(array('nuDigital' => $dto->getNuDigital()));
        }

        $criteria = array(
            'sqArtefatoPai' => $dto->getSqArtefato()
            , 'sqArtefatoFilho' => $artefato[0]->getSqArtefato()
            , 'sqTipoVinculoArtefato' => $params['tipoVinculo']
            , 'dtRemocaoVinculo' => NULL);

        $result = $this->getServiceLocator()->getService('ArtefatoVinculo')->findBy($criteria);

        if (count($result) > 0) {
            return false;
        } else {
            $this->getServiceLocator()->getService('Documento')->addDocumentoEletronico($dto);
            return true;
        }
    }

    /**
     * Metodo que recupera o tipo atefato assunto
     * @param Object $entity
     * @param Object $dto
     * @param int $sqTipoArtefato Default 1
     *
     * @return Object \TipoArtefatoAssunto
     */
    public function tipoArtefatoAssunto ($entity, $dto, $sqTipoArtefato = 1)
    {
        // tratando parametros
        $entityTipoArtefatoAssunto = $this->_getRepository('app:TipoArtefatoAssunto')
                ->findBy(array('sqAssunto' => $dto->getSqAssunto(), 'sqTipoArtefato' => $sqTipoArtefato));
        // retornando valor
        return $entity->setSqTipoArtefatoAssunto($entityTipoArtefatoAssunto[0]);
    }

    /**
     * Metódo que realiza  a consulta de pessoa artefato
     * @param Object $artefato
     * @param Object $pessoa
     * @param int $sqTipoFuncao
     *
     * @return Object \PessoaArtefato
     */
    public function pessoaArtefato ($artefato, $pessoa, $sqTipoFuncao)
    {
        // retornando registros
        return $this->_getRepository('app:PessoaArtefato')->searchPessoaArtefato($artefato, $pessoa, $sqTipoFuncao);
    }

    public function recuperaNumeroLoteEtiqueta (\Core_Dto_Search $dtoSearch)
    {
        return $this->getEntityManager()->getRepository('app:LoteEtiqueta')
                        ->recuperaNumeroLoteEtiqueta($dtoSearch);
    }

    /**
     * Metódo que realiza o save do Artefato
     * @param Object $dtoSearch
     * @return \Sgdoce\Model\Entity\Artefato
     */
    public function saveArtefato (\Core_Dto_Search $dtoSearch)
    {
        $date = new \Zend_Date();
        $this->getEntityManager()->getConnection()->beginTransaction();

        try {
            $sqPessoaSgdoce = $this->_getRepository('app:PessoaSgdoce')->findBySqPessoaCorporativo(
                    \Core_Integration_Sica_User::getPersonId());

            if (empty($sqPessoaSgdoce)) {
                $filter = new \Zend_Filter_Digits();
                $sqPessoaCorporativo = \Core_Integration_Sica_User::getPersonId();

                $data['sqPessoaCorporativo'] = $this->_getRepository('app:VwPessoa')->find($sqPessoaCorporativo);
                $dtoPessoaSearch = \Core_Dto::factoryFromData($data, 'search');
                $cpfCnpjPassaportUnfiltered = $this->getServiceLocator()->getService('VwPessoa')->returnCpfCnpjPassaporte($dtoPessoaSearch);
                $cpfCnpjPassaport = $filter->filter($cpfCnpjPassaportUnfiltered);

                $noPessoaCorporativo = $data['sqPessoaCorporativo']->getNoPessoa();

                $this->addPessoaSgdoce($sqPessoaCorporativo, $noPessoaCorporativo, $cpfCnpjPassaport);
                $sqPessoaSgdoce = $this->_getRepository('app:PessoaSgdoce')->findBySqPessoaCorporativo(
                        \Core_Integration_Sica_User::getPersonId());
            }

            /** @var \Sgdoce\Model\Entity\Artefato $entityArtefato */
            $entityArtefato = $this->_newEntity('app:Artefato');

            //só seta o numero da digital se for documento tipo físico
            if (!$dtoSearch->getInEletronico()) {
                $entityLoteEtiqueta = $this->getEntityManager()->getPartialReference('app:LoteEtiqueta', $dtoSearch->getSqLoteEtiqueta());

                /** @var \Sgdoce\Model\Entity\EtiquetasUso $entityEtiquetasUso */
                $entityEtiquetasUso = $this->_newEntity('app:EtiquetasUso');
                $entityEtiquetasUso->setNuEtiqueta($dtoSearch->getNuDigital());
                $entityEtiquetasUso->setSqLoteEtiqueta($entityLoteEtiqueta);
                //persist Etiquetas Uso
                $this->getEntityManager()->persist($entityEtiquetasUso);
                $this->getEntityManager()->flush($entityEtiquetasUso);


                $entityArtefato->setNuDigital($entityEtiquetasUso);
                $entityArtefato->setSqLoteEtiqueta($entityEtiquetasUso);
            }

            $entityArtefato->setDtPrazo(NULL);
            $entityArtefato->setDtArtefato($date);
            $entityArtefato->setInEletronico($dtoSearch->getInEletronico());
            //persist artefato
            $this->getEntityManager()->persist($entityArtefato);
            $this->getEntityManager()->flush($entityArtefato);


            $sqPessoaFuncao = $this->getEntityManager()->getPartialReference('app:PessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoAutor());
            /** @var \Sgdoce\Model\Entity\PessoaArtefato $entityPessoaArtefato */
            $entityPessoaArtefato = $this->_newEntity('app:PessoaArtefato');
            $entityPessoaArtefato->setSqArtefato($entityArtefato);
            $entityPessoaArtefato->setSqPessoaSgdoce($sqPessoaSgdoce[0]);
            $entityPessoaArtefato->setSqPessoaFuncao($sqPessoaFuncao);

            $this->getEntityManager()->persist($entityPessoaArtefato);
            $this->getEntityManager()->flush($entityPessoaArtefato);

            // salva o historico do artefato
            $arrDto = array(
                'sqPessoa' => \Core_Integration_Sica_User::getPersonId(),
                'sqUnidade' => \Core_Integration_Sica_User::getUserUnit()
            );

            // #HistoricoArtefato::save();
            $strMessage = $this->getServiceLocator()
                    ->getService('HistoricoArtefato')
                    ->getMessage('MH005');

            $this->getServiceLocator()
                 ->getService('HistoricoArtefato')
                 ->registrar($entityArtefato->getSqArtefato(),
                             \Core_COnfiguration::getSgdoceSqOcorrenciaCadastrar(),
                             $strMessage);

            $this->getEntityManager()->getConnection()->commit();
        } catch (\Exception $e) {
            $this->getEntityManager()->getConnection()->rollback();
            throw $e;
        }

        return $entityArtefato;
    }

    /**
     * Método responsavel por verificar se existe  o sqPessoaCorporativo no PessoaSgdoce
     * (Caso não exista, o mesmo será cadastrado em PessoaSgdoce e retorna o objeto
     *
     * @param int $sqPessoaCorporativo PrimaryKey da PessoaCorporativo
     * @param String $noPessoaCorporativo Nome da PessoaCorporativo (para caso seja necessario cadastro)
     *
     * @return ObjectEntity PessoaSgdoce
     */
    public function pessoaCorporativoSgdoce ($sqPessoaCorporativo, $noPessoaCorporativo)
    {
        $filter = new \Zend_Filter_Digits();

        // recuperando PessoaSgedoce
        $entPessoaSgdoce = $this->searchPessoaSgdoce($sqPessoaCorporativo);

        if ($sqPessoaCorporativo != 0) {
            $data['sqPessoaCorporativo'] = $this->_getRepository('app:VwPessoa')->find($sqPessoaCorporativo);
            $dtoPessoaSearch = \Core_Dto::factoryFromData($data, 'search');
            $cpfCnpjPassaport = $this->getServiceLocator()->getService('VwPessoa')->returnCpfCnpjPassaporte($dtoPessoaSearch);
            $cpfCnpjPassaport = $filter->filter($cpfCnpjPassaport);
        }

        // se exstir restorno
        if (!count($entPessoaSgdoce)) {
            // cadastra PessoaSgdoce e retorna (pois, nao existe vinculo com PessoaAssinanteArtefato
            $entPessoaSgdoce = $this->addPessoaSgdoce($sqPessoaCorporativo, $noPessoaCorporativo, $cpfCnpjPassaport);
        }

        // retornando a Entity PessoaSgdoce
        return $entPessoaSgdoce;
    }

    /**
     * Verifica e retorna PessoaUnidadeOrg
     *
     * @param Object Sgdoce\Model\Entity\PessoaSgdoce $sqPessoaSgdoce
     *
     * @return Object Entity PessoaAssinanteArtefato
     */
    public function hasPessoaUnidadeOrg ($sqPessoaSgdoce, $noCargo = NULL, $sqUnidadeOrg = NULL)
    {
        $sqUnidade = NULL;
        $noUnidade = NULL;
        //Busca a Pessoa No vwProfissional
        $criteriaPessoa = array('sqPessoaFisica' => $sqPessoaSgdoce->getSqPessoaCorporativo()->getSqPessoa());
        $criteriaUnidade = array('sqUnidadeOrg' => $sqUnidadeOrg);
        # Removido para não pesquisar pessoa pela unidade lotada, somente para migração.
//        if ($sqUnidadeOrg) {
//            $criteria['sqUnidadeLotacao'] = $sqUnidadeOrg;
//        }
        $unidadeOrg = $this->_getRepository('app:vwUnidadeOrg')->findOneBy($criteriaUnidade);
        $pessoa = $this->_getRepository('app:vwPessoaFisica')->findOneBy($criteriaPessoa);

        // verificando se registro com sqArtefato e sqPessoaSgdoce
        $criteria = array('sqPessoaSgdoce' => $sqPessoaSgdoce->getSqPessoaSgdoce());
        $orderBy = array('sqPessoaUnidadeOrg' => 'desc');
        $pessoaUnidadeOrg = $this->_getRepository('app:PessoaUnidadeOrg')->findBy($criteria, $orderBy, 1);
        $pessoaUnidadeOrg = current($pessoaUnidadeOrg);

        $noUnidade = $unidadeOrg->getNoUnidadeOrg();
        $sqUnidade = $unidadeOrg->getSqUnidadeOrg();

        if ( empty($pessoaUnidadeOrg) ) {
            //inserir na tabela pessoa_unidade_org
            $pessoaUnidadeOrg = $this->insertPessoaUnidadeOrg($sqUnidade, $noUnidade, $noCargo, $sqPessoaSgdoce);
        } else {
            if ($pessoaUnidadeOrg->getSqPessoaUnidadeOrgCorp()->getSqUnidadeOrg() != $sqUnidade ||
                    $pessoaUnidadeOrg->getNoUnidadeOrg() != $noUnidade ||
                    $pessoaUnidadeOrg->getNoCargo() != $noCargo) {
                //inserir na tabela pessoa_unidade_org
                $pessoaUnidadeOrg = $this->insertPessoaUnidadeOrg($sqUnidade, $noUnidade, $noCargo, $sqPessoaSgdoce);
            }
        }

        return $pessoaUnidadeOrg;
    }

    /**
     * Método que Insere uma PessoaUnidadeOrg
     * @param unknown_type $sqUnidade
     * @param unknown_type $noUnidade
     * @param unknown_type $noCargo
     */
    public function insertPessoaUnidadeOrg ($sqUnidade, $noUnidade, $noCargo, $sqPessoaSgdoce)
    {

        //inserir na tabela pessoa_unidade_org
        $objPessoa = $this->getEntityManager()->getPartialReference('app:PessoaSgdoce', $sqPessoaSgdoce->getSqPessoaSgdoce());

        $newPessoaUnidadeOrg = new \Sgdoce\Model\Entity\PessoaUnidadeOrg();
        $objUnidade = $this->getEntityManager()->getPartialReference('app:VwUnidadeOrg', $sqUnidade);
        $newPessoaUnidadeOrg->setSqPessoaUnidadeOrgCorp($objUnidade);
        $newPessoaUnidadeOrg->setNoUnidadeOrg($noUnidade);
        $newPessoaUnidadeOrg->setSqPessoaSgdoce($objPessoa);
        $newPessoaUnidadeOrg->setNoCargo($noCargo);
        $this->getEntityManager()->persist($newPessoaUnidadeOrg);
        $this->getEntityManager()->flush($newPessoaUnidadeOrg);

        return $newPessoaUnidadeOrg;
    }

    /**
     * MetConsultando registro de materiais para documento controller
     * @param Object $entityArtefato
     * @return json
     */
    public function listGrid (\Core_Dto_Search $dtoSearch)
    {
        // retornando registros
        return $this->_getRepository()->searchPageDto('listGridMaterialApoioDocumento', $dtoSearch);
    }

    /**
     * MetConsultando registro de materiais para documento controller
     * @param Object $dtoSearch
     * @return json
     */
    public function listGridMaterialApoio ($dtoSearch)
    {
        //retornando registros
        return $this->_getRepository('app:AnexoArtefatoVinculo')->searchPageDto('listGridMaterial', $dtoSearch);
    }

    /**
     * MetConsultando registro de materiais para documento controller
     * @param \Core_Dto_Search $dtoSearch
     * @return json
     */
    public function listGridAnexoSic (\Core_Dto_Search $dtoSearch)
    {
        //retornando registros
        return $this->_getRepository('app:AnexoSic')->searchPageDto('listGrid', $dtoSearch);
    }

    /**
     * Metódo que retorna valor do paramtro
     * @param Object $dto
     * @retur int
     */
    public function recuperaSqPessoaDto ($dto)
    {
        // verificando paramento
        if ($dto->getSqPessoa()) {
            $return = $dto->getSqPessoa();
        }

        if ($dto->getSqPessoaIcmbioDestino()) {
            $return = $dto->getSqPessoaIcmbioDestino();
        }

        // retornando valor
        return $return;
    }

    /**
     *
     */
    public function tipoDocumento ($params)
    {
        return $this->_getRepository('app:TipoDocumento')->searchTipoDocumento($params['extraParam']);
    }

    public function findArtefatoByNuDigitalOrNuArtefato ($dto)
    {
        $criteria = $dto->toArray();
        $entity = $this->_getRepository('app:Artefato')->findBy($criteria);
        return $entity;
    }

    /**
     * metod que realiza o upload e a persistencia do Arquivo
     */
    protected function _upload ($dto)
    {
        $configs = \Core_Registry::get('configs');
        $upload = new \Zend_File_Transfer_Adapter_Http();

        $files = $upload->getFileInfo();
        $filesUp = array();
        $return = array();
        $error = false;

        foreach ($files as $file => $info) {
            $upload->setDestination($configs['upload']['material']);
            $upload->setValidators(array());

            $upload->addValidator('Size', TRUE, array('max' => $configs['upload']['materialApoioMaxSize'],
                'messages' => "O tamanho do arquivo é superior ao permitido. O tamanho permitido é 25MB."));

            $upload->addValidator('ExcludeExtension', TRUE, array('dll', 'msi', 'phtml', 'phar', 'pyc', 'py', 'jar', 'bat', 'com', 'exe', 'pif', 'bin', 'sh', 'pl', 'php') +
                    array('messages' => "Extensão do arquivo inválida."));

            $upload->getValidator('Upload')->setMessages(array(
//            		'fileUploadErrorIniSize'  => "O tamanho máximo permitido para upload é '100MB'",
//            		'fileUploadErrorFormSize' => "O tamanho máximo permitido para upload é '100MB'",
                'fileUploadErrorNoFile' => "Arquivo não selecionado."));

            if ($upload->isValid($file)) {
                $fileinfo = pathinfo($info['name']);
                $upload->receive($file);
                $filesUp[] = $upload->getFileName($file);

                $return[] = array('name' => $upload->getFileName($file));
            } else {
                $error = $upload->getMessages();
                break;
            }
        }

        if ($error) {
            if (count($filesUp)) {
                foreach ($filesUp as $file) {
                    unlink($file);
                }
            }

            return array('errors' => $error);
        }

        return $return;
    }

    /**
     * MetConsultando registro de Referencia para documento controller
     * @param Object $entityArtefato
     * @return json
     */
    public function listGridVinculacao ($dtoSearch)
    {
        // retornando registros
        return $this->_getRepository()->searchPageDto('listGridVinculacao', $dtoSearch);
    }

    public function searchNumeroDigital ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $dto = \Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $res = $service = $this->getService()->findNumeroDigital($dto);
        $this->_helper->json($res);
    }

    public function verificaDuplicidade ($dto)
    {
        return $this->_getRepository('app:Artefato')->verificaDuplicidade($dto);
    }

    /**
     *
     * @param \Core_Dto_Search $dto
     * @return \Sgdoce\Model\Entity\EtiquetaNupSiorg
     */
    public function getDigitalNupSiorg (\Core_Dto_Search $dto)
    {
        return $this->_getRepository('app:EtiquetaNupSiorg')->findOneBy(array('nuEtiqueta' => $dto->getNuDigital()));
    }

    public function deleteAnexoSic(\Core_Dto_Search $dto)
    {
        try {

            $entityAnexoSic = $this->_getRepository('app:AnexoSic')->find($dto->getSqAnexoSic());
            $fileDelete = $entityAnexoSic->getTxCaminhoArquivo();
            $this->getEntityManager()->remove($entityAnexoSic);
            $this->getEntityManager()->flush();

            unlink($fileDelete);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * método para pesquisa de grid de material de apoio
     * @param \Core_Dto_Search $dto
     */
    public function listGridDocumento(\Core_Dto_Search $dto) {
        $result = $this->_getRepository('app:Artefato')->searchPageDto('listPesquisaDocumento', $dto, FALSE);
        return $result;
    }

    public function setHasImage( $sqArtefato, $hasImage = 't' )
    {
        $entity = $this->find($sqArtefato);
        $arInconsistencia = $entity->getArInconsistencia();
        $arInconsistencia = str_replace(array("{", "}"), "", $arInconsistencia);
        $arInconsistencia = explode(",", $arInconsistencia);
        $arInconsistencia[6] = 't';

        # Se estiver tudo corrigido, insere tramite.
        if( !in_array('f', $arInconsistencia) ) {
            $this->getServiceLocator()
                 ->getService('VinculoMigracao')
                 ->setArtefatoCorrigido($entity->getSqArtefato());
        }

        $arInconsistencia = implode(",", $arInconsistencia);
        $arInconsistencia = "{".$arInconsistencia."}";
        $entity->setArInconsistencia($arInconsistencia);

        // persistindo informacao
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush($entity);
    }

    /**
     * @param \Core_Dto_Search $dto
     */
    public function getDataSgdocFisico(\Core_Dto_Search $dto) {
        return $this->_getRepository('app:vwDocumentoSgdocFisico')->findByNuDigital($dto);
    }

    private function _checkExcluirAssinante($entity, $dto)
    {
        if ($entity->getSqArtefato() &&
                !$dto->getNoResponsavelAssinatura() &&
                        ($dto->getProcedenciaInterno() == 'externo' || in_array($dto->getSqTipoDocumento(), $this->getTipoDocumentoSemAssinatura()))) {

            //só insere em pessoa_assinante_artefato se procedencia for interna
            if ($dto->getProcedenciaInterno() != 'externo') {
                $criteria = array('sqArtefato' => $entity->getSqArtefato());
                $entPessoaAssinante = $this->_getRepository('app:PessoaAssinanteArtefato')->findOneBy($criteria);
                if ($entPessoaAssinante) {
                    $this->_getRepository('app:PessoaAssinanteArtefato')->deleteByArtefato($entity->getSqArtefato());
                }
            }

            $criteriaPessoaArtefatoAssinante = array(
                'sqArtefato' => $entity->getSqArtefato(),
                'sqPessoaFuncao' => \Core_Configuration::getSgdocePessoaFuncaoAssinatura()
            );
            $entPessoaArtefatoAssinante = $this->_getRepository('app:PessoaArtefato')->findOneBy($criteriaPessoaArtefatoAssinante);
            if ($entPessoaArtefatoAssinante) {
                $this->getEntityManager()->remove($entPessoaArtefatoAssinante);
            }
        }
        return $this;
    }

    private function _validateAssuntoHomologado ($sqArtefato)
    {
        $entAssunto = $this->_getRepository('app:Assunto')->find($sqArtefato);

        if (! $entAssunto) {
            throw new \Core_Exception_ServiceLayer_Verification('Assunto não localizado.');
        }

        if (! $entAssunto->getStHomologado()) {
            throw new \Core_Exception_ServiceLayer_Verification('Somente assunto homologado podem ser informado.');
        }

        return $this;
    }


    private function _doRemoveComplementDataForPessoaSgdoce(\Sgdoce\Model\Entity\PessoaSgdoce $entPessoaSgdoce)
    {
        $this->_getRepository('app:EmailSgdoce')->deleteByPessoaSgdoce($entPessoaSgdoce->getSqPessoaSgdoce());
        $this->_getRepository('app:TelefoneSgdoce')->deleteByPessoaSgdoce($entPessoaSgdoce->getSqPessoaSgdoce());
        $this->_getRepository('app:EnderecoSgdoce')->deleteByPessoaSgdoce($entPessoaSgdoce->getSqPessoaSgdoce());

        return $this;
    }

    private function _doRemovePessoaSgdoce(\Sgdoce\Model\Entity\PessoaSgdoce $entPessoaSgdoce)
    {
        $this->getEntityManager()->remove($entPessoaSgdoce);
        $this->getEntityManager()->flush();
        return $this;
    }

    public function getTipoDocumentoSemAssinatura()
    {
        return array(
            \Core_Configuration::getSgdoceTipoDocumentoFatura(),
            \Core_Configuration::getSgdoceTipoDocumentoNotaFiscal(),
            \Core_Configuration::getSgdoceTipoDocumentoCurriculo(),
            \Core_Configuration::getSgdoceTipoDocumentoEmail(),
            \Core_Configuration::getSgdoceTipoDocumentoCertidao(),
            \Core_Configuration::getSgdoceTipoDocumentoMidia()
        );
    }

}
