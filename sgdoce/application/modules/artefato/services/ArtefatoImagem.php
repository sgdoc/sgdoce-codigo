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

use \Sgdoce\Model\Entity\ArtefatoImagem as ArtefatoImagemEntity;
use \Sgdoce\Model\Entity\VwUltimaImagemArtefato as UltimaImagemArtefatoEntity;
use \Sgdoce\Model\Entity\Artefato as ArtefatoEntity;
use \Sgdoce\Model\Entity\GrauAcessoArtefato as GrauAcessoArtefatoEntity;
use \Sgdoce\Model\Entity\GrauAcesso as GrauAcessoEntity;
use \Sgdoce\Model\Entity\TipoArtefatoAssunto as TipoArtefatoAssuntoEntity;
use \Sgdoce\Model\Entity\TipoArtefato as TipoArtefatoEntity;
use \Sgdoce\Model\Entity\SolicitacaoDownloadImagem as SolicitacaoDownloadImagemEntity;
use \Doctrine\ORM\EntityManager as EntityManager;
use \Core_Dto_Search as Dto;

/**
 * Classe para Service de ArtefatoImagem
 *
 * @package  Artefato
 * @category Service
 * @name     ArtefatoImagem
 * @version  1.0.0
 */
class ArtefatoImagem extends \Core_ServiceLayer_Service_CrudDto
{

    const HASH_ALGORITHM   = 'sha256';
    const PATH_IMAGE_LOGO  = '/img/sgdoc_logo.png';

    /**
     * @var string
     */
    protected $_entityName = 'app:ArtefatoImagem';
    /**
     * @var string
     */
    protected $_entityArtefato = 'app:Artefato';
    /**
     * @var string
     */
    protected $_entityArtefatoVinculo = 'app:ArtefatoVinculo';

    /**
     * @var integer
     */
    protected $_totalRegistroProcessar = 0;

    /**
     * Armazena ID do artefato que esta sendo processado o merge
     * @var integer
     */
    private static $_sqArtefato = NULL;

    /**
     * @param  string $filename
     * @return string
     */
    public function getLinkedTemporaryURL ($filename)
    {
        if (empty($filename)){
            throw new \Exception("Imagem do artefato inexistente");
        }

        $ds = DIRECTORY_SEPARATOR;
        $applicationPath = APPLICATION_PATH;
        $publicPath = "{$applicationPath}{$ds}..{$ds}public";
        $dataPath = "{$applicationPath}{$ds}..{$ds}data";

        $url = sprintf('/tmp/%s.pdf', md5(str_replace($ds,'_',$filename)));
        $file = "{$dataPath}{$ds}{$filename}";
        $link = "{$publicPath}{$url}";

        if (!file_exists($link)) {
            symlink($file, $link);
        }

        return $url;
    }

    /**
     * @param integer $sqArtefato
     * @return boolean
     */
    public function forbiddenAccess ($sqArtefato, $sqPessoa)
    {
        $artefatoImagemEntity = $this->_getArtefatoImageEntity($sqArtefato);
        $artefatoEntity = $artefatoImagemEntity->getSqArtefato();
        if ($artefatoEntity instanceof ArtefatoEntity) {
            $grauAcessoArtefatoEntity = $artefatoEntity->getSqGrauAcessoArtefato();
            if ($grauAcessoArtefatoEntity instanceof GrauAcessoArtefatoEntity) {
                $grauAcessoEntity = $grauAcessoArtefatoEntity->getSqGrauAcesso();
                if ($grauAcessoEntity instanceof GrauAcessoEntity) {
                    $forbiddenList = array(
                        \Core_Configuration::getSgdoceGrauAcessoSigiloso()
                    );

                    //se o grau for controlado verifica se o usuario pode ver o artefato
                    if (in_array($grauAcessoEntity->getSqGrauAcesso(), $forbiddenList)) {
                        //se usuario pode ver inverte o boleano para manter a semantica do retorno
                        //do deste metodo (forbiddenAccess)
                        return !$this->_canViewSigiloso($sqArtefato,$sqPessoa);
                    }

                    return false;
                }
            }
        }
        return false;
    }

    /**
     * @param integer $sqArtefato
     * @return string
     */
    private function _canViewSigiloso ($sqArtefato,$sqPessoa)
    {
        return $this->_getRepository('app:TramiteArtefato')->canViewSigiloso(
                \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato,'sqPessoa'=>$sqPessoa), 'search'));
    }

    /**
     * @param integer $sqArtefato
     * @return boolean
     */
    public function canUpload ($sqArtefato)
    {
        $artefatoEntity = $this->_getRepository('app:Artefato')->find($sqArtefato);
        if ($artefatoEntity instanceof ArtefatoEntity) {
            $tipoArtefatoAssuntoEntity = $artefatoEntity->getSqTipoArtefatoAssunto();
            if ($tipoArtefatoAssuntoEntity instanceof TipoArtefatoAssuntoEntity) {
                $tipoArtefatoEntity = $tipoArtefatoAssuntoEntity->getSqTipoArtefato();
                if ($tipoArtefatoEntity instanceof TipoArtefatoEntity) {
                    $allowedList = array(
                        \Core_Configuration::getSgdoceTipoArtefatoDossie(),
                        \Core_Configuration::getSgdoceTipoArtefatoDocumento()
                    );
                    return in_array($tipoArtefatoEntity->getSqTipoArtefato(), $allowedList);
                }
            }
        }
        return false;
    }

    /**
     * @param integer $sqArtefato
     * @return boolean
     */
    public function hasImage ($sqArtefato, $sqTipoArtefato=null)
    {
        try {

            if($sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso()){
                $entityArtefato = $this->_getRepository($this->_entityArtefato)->find($sqArtefato);
                $sqTipoArtefato = $entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();

                /**
                 * se for processo recupera a 1ª peça para verificar se tem imagem
                 */
                if ($sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
                    $entityArtefatoVinculoPrimeiraPeca =
                            $this->_getRepository($this->_entityArtefatoVinculo)
                                 ->findOneBy(array('sqArtefatoPai' => $sqArtefato,
                                                   'sqTipoVinculoArtefato' => \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao()));

                    /**
                     * Não existe vinculo de primeira peça para o processo
                     */
                    if(null === $entityArtefatoVinculoPrimeiraPeca){
                        return false;
                    }

                    //pega o sqArtefato do pai
                    $sqArtefato = $entityArtefatoVinculoPrimeiraPeca->getSqArtefatoFilho()->getSqArtefato();
                }
            }
            $filePath = $this->getImagePath($sqArtefato);
            if (empty($filePath)){
                return false;
            }
            $file = sprintf(
                '%1$s%2$s..%2$sdata%2$s%3$s',
                APPLICATION_PATH,
                DIRECTORY_SEPARATOR,
                $filePath
            );
            if (!file_exists($file)) {
                throw new \Core_Exception_ServiceLayer('Imagem cadastrada, porém não foi encontrado o arquivo da mesma');
            }
            $artefatoImagemEntity = $this->_getArtefatoImageEntity($sqArtefato);
            $hashValidate = new \Zend_Validate_File_Hash(array(
                'hash' => $artefatoImagemEntity->getTxHash(),
                'algorithm' => self::HASH_ALGORITHM
            ));
            if (!$hashValidate->isValid($file)) {
                throw new \Core_Exception_ServiceLayer('Autenticidade da imagem violada');
            }
        } catch (\Exception $exp) {
            $message = sprintf(
                '[SGDoc-e] Exception %s in %s(%d): "%s"',
                get_class($exp),
                __METHOD__,
                $sqArtefato,
                $exp->getMessage()
            );
            error_log( $message );
            throw $exp;
        }
        return true;
    }

    /**
     * @param integer $sqArtefato
     * @return string
     */
    public function getImagePath ($sqArtefato)
    {
        $artefatoImagemEntity = $this->_getArtefatoImageEntity($sqArtefato);
        if ($artefatoImagemEntity instanceof ArtefatoImagemEntity) {
            return $this->_getPath($artefatoImagemEntity) . $this->_getFilename($artefatoImagemEntity);
        }
        return '';
    }

    /**
     * @param \Core_Dto_Search $dto
     * @return string
     * @todo ao subir imagem para artefato com imagem pendente de migração, sinalizar no registro que foi realizado e bloquear ação.
     */
    public function saveImage (\Core_Dto_Search $dto)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->beginTransaction();
        try {
            #Consulta última imagem do artefato se houver
            $rsImagemAtiva = $this->_getRepository('app:ArtefatoImagem')->findBy(array(
                'sqArtefato' => $dto->getId(),
                'stAtivo' => TRUE
            ));
            $artefatoImagemEntity = new ArtefatoImagemEntity();
            #Request information...
            $this->_fillRequestInformation($artefatoImagemEntity, $dto, $entityManager);
            #Session information...
            $this->_fillSessionInformation($artefatoImagemEntity, $entityManager);
            #Temporary file information
            $mufService = $this->getServiceLocator()->getService('MoveFileUpload');
            $pathTemporary = $mufService->getOriginPath();
            $filenameTemporary = $dto->getFilenameTemporary();
            $this->_fillFileInfromation($artefatoImagemEntity, $pathTemporary . $filenameTemporary);
            #Atualiza imagem anterior com st_ativo FALSE
            if( count($rsImagemAtiva) ){
                foreach($rsImagemAtiva as $entImagem) {
                    $entImagem->setStAtivo(FALSE);
                    $entityManager->persist($entImagem);
                    $entityManager->flush($entImagem);
                }
            }
            #Saving entity...
            $entityManager->persist($artefatoImagemEntity);
            $entityManager->flush($artefatoImagemEntity);
            #Saving history...
            $haService = $this->getServiceLocator()->getService('HistoricoArtefato');
            $sqOcorrencia = \Core_Configuration::getSgdoceSqOcorrenciaIncluirImagem();
            $strMessage = $haService->getMessage(
                'MH019',
                $artefatoImagemEntity->getDtOperacao()->toString('dd/MM/YYYY HH:mm:ss')
            );
            $sqArtefato = $artefatoImagemEntity->getSqArtefato()->getSqArtefato();
            $haService->registrar($sqArtefato, $sqOcorrencia, $strMessage);
            #Moving file...
            $filename = $this->_getFilename($artefatoImagemEntity);
            $mufService->setDestinationPath($this->_getPath($artefatoImagemEntity));
            $mufService->move($filenameTemporary, $filename);

            #se inconsistente define imagem como confirmada.
            $dtoSearch = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
            $isMigracao = $this->getServiceLocator()
                               ->getService("Artefato")
                               ->isMigracao($dtoSearch);

            $isInconsistente = $this->getServiceLocator()
                                    ->getService("Artefato")
                                    ->isInconsistent($dtoSearch);

            if( $isMigracao && $isInconsistente ) {
                $this->getServiceLocator()
                     ->getService('DocumentoMigracao')
                     ->setHasImage($sqArtefato);
            }

            $entityManager->commit();
        } catch (\Exception $exp) {
            $entityManager->rollback();
            throw $exp;
        }
    }

    /**
     * @param \Sgdoce\Model\Entity\ArtefatoImagem $artefatoImagemEntity
     * @return string
     */
    private function _getFilename (ArtefatoImagemEntity $artefatoImagemEntity)
    {
        return sprintf(
            '%s.pdf',
            $artefatoImagemEntity->getNoArquivo()
        );
    }

    /**
     * @param \Sgdoce\Model\Entity\ArtefatoImagem $artefatoImagemEntity
     * @return string
     */
    private function _getPath (ArtefatoImagemEntity $artefatoImagemEntity)
    {
        $etiquetaNupSiorgEntity = $artefatoImagemEntity->getSqArtefato()->getNuDigital();
        $loteFolder = (string) $etiquetaNupSiorgEntity->getSqLoteEtiqueta()->getSqLoteEtiqueta();
        $digitalFolder = (string) $etiquetaNupSiorgEntity->getNuEtiqueta();
        return sprintf(
            'upload%1$simagem%1$s%2$s%1$s%3$s%1$s',
            DIRECTORY_SEPARATOR,
            $loteFolder,
            $digitalFolder
        );
    }

    /**
     * @param \Sgdoce\Model\Entity\ArtefatoImagem $artefatoImagemEntity
     * @param \Core_Dto_Search $dto
     * @return void
     */
    private function _fillRequestInformation (ArtefatoImagemEntity $artefatoImagemEntity, Dto $dto)
    {
        $artefatoEntity = $this->_getRepository('app:Artefato')->find($dto->getId());

        $artefatoImagemEntity->setSqArtefato( $artefatoEntity );
        $artefatoImagemEntity->setNuBytes($dto->getBytes());
        $artefatoImagemEntity->setNuQtdePaginas($dto->getPages());
        $artefatoImagemEntity->setTxObservacao($dto->getReason());
        $artefatoImagemEntity->setDtOperacao(\Zend_Date::now());
        $artefatoImagemEntity->setStAtivo(TRUE);
    }

    /**
     * @param \Sgdoce\Model\Entity\ArtefatoImagem $artefatoImagemEntity
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @return void
     */
    private function _fillSessionInformation (ArtefatoImagemEntity $artefatoImagemEntity, EntityManager $entityManager)
    {
        $pessoaEntity = $entityManager->getPartialReference('app:VwPessoa', \Core_Integration_Sica_User::getPersonId());
        $unidadeOrgEntity = $entityManager->getPartialReference('app:VwUnidadeOrg', \Core_Integration_Sica_User::getUserUnit());

        $artefatoImagemEntity->setSqPessoa($pessoaEntity);
        $artefatoImagemEntity->setSqUnidadeOrg($unidadeOrgEntity);
    }

    /**
     * @param \Sgdoce\Model\Entity\ArtefatoImagem $artefatoImagemEntity
     * @return void
     */
    private function _fillFileInfromation (ArtefatoImagemEntity $artefatoImagemEntity, $filename)
    {
        if (!file_exists($filename)) {
            throw new \Core_Exception_ServiceLayer('Problema ao encontrar o arquivo');
        }
        $hash = hash_file(self::HASH_ALGORITHM, $filename);
        $md5 = md5($hash);

        $artefatoImagemEntity->setTxHash($hash);
        $artefatoImagemEntity->setNoArquivo($md5);
    }

    /**
     * @param integer $sqArtefato
     * @return \Sgdoce\Model\Entity\ArtefatoImagem
     */
    public function _getArtefatoImageEntity ($sqArtefato)
    {
        $ultimaImagemArtefatoEntity = $this->_getRepository("app:VwUltimaImagemArtefato")->find($sqArtefato);
        if ($ultimaImagemArtefatoEntity instanceof UltimaImagemArtefatoEntity) {
            $artefatoImagemEntity = $ultimaImagemArtefatoEntity->getSqArtefatoImagem();
            return $artefatoImagemEntity;
        }
        return null;
    }

    public function processDownloadFileRequest($sqArtefato)
    {
        try {
            $dto            = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
            $arrNotVinculo  = array(
                \Core_Configuration::getSgdoceTipoVinculoArtefatoReferencia(),
                \Core_Configuration::getSgdoceTipoVinculoArtefatoApoio(),
                \Core_Configuration::getSgdoceTipoVinculoArtefatoDespacho(),
            );
            $arrVinculo     = $this->getServiceLocator()
                                   ->getService('ArtefatoVinculo')
                                   ->findVinculoArtefato($dto, $arrNotVinculo);

            $entArtefato    = $this->_getRepository($this->_entityArtefato)->find($sqArtefato);
            $sqTipoArtefato = $entArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();

            if (($sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoDocumento()) && (count($arrVinculo) == 0)) {
                //Documento sem Vinculo
                $arrRetorno = $this->_processDownloadUniqueFile($sqArtefato);
            } else {
                //Processo com vinculo além da 1ª Peça
                //Documento com vinculo
                $sqPessoa = \Core_Integration_Sica_User::getPersonId();
                $configs = \Core_Registry::get('configs');
                $mergePdfOptions = $configs['merge_pdf'];

                $robotMode = (boolean) $mergePdfOptions['robotMode'];

                if (! $robotMode) {
                    $arrRetorno = $this->_processMergeFiles($sqArtefato, $sqPessoa);
                }else{

                    $txEmailCorporativo = $this->_getEmailCorporativo($sqPessoa);

                    if (!$txEmailCorporativo) {
                        throw new \Core_Exception_ServiceLayer('Email corporativo não localizado. Impossível prosseguir.');
                    }

                    $entSolicitacaoDownloadImagem = $this->_newEntity('app:SolicitacaoDownloadImagem');
                    $entSolicitacaoDownloadImagem->setSqArtefato($entArtefato)
                            ->setSqPessoa($this->getEntityManager()->getPartialReference('app:VwPessoa', $sqPessoa))
                            ->setSqUnidadeOrg($this->getEntityManager()->getPartialReference('app:VwUnidadeOrg', \Core_Integration_Sica_User::getUserUnit()))
                            ->setDtSolicitacao(\Zend_Date::now())
                            ->setStProcessado(FALSE)
                            ->setInTentativa(0)
                            ->setTxEmail($txEmailCorporativo);

                    $this->getEntityManager()->persist($entSolicitacaoDownloadImagem);
                    $this->getEntityManager()->flush();

                    $arrRetorno['success']  = true;
                    $arrRetorno['msg']      = sprintf(\Core_Registry::getMessage()->translate('MN169'), $txEmailCorporativo);
                    $arrRetorno['link']     = '';
                    $arrRetorno['filesize'] = '';
                }
            }

//            switch ($sqTipoArtefato) {
//                //Processo apenas com a 1ª Peça
//                case \Core_Configuration::getSgdoceTipoArtefatoProcesso() && count($arrVinculo) == 1:
//                    $arrRetorno = $this->_processDownloadUniqueFile($arrVinculo[1]['sqArtefato']);
//                    break;
//                //Documento sem Vinculo
//                case \Core_Configuration::getSgdoceTipoArtefatoDocumento() && count($arrVinculo) == 0:
//                    $arrRetorno = $this->_processDownloadUniqueFile($sqArtefato);
//                    break;
//                //Processo com vinculo além da 1ª Peça
//                //Documento com vinculo
//                default:
//
//                    $sqPessoa        = \Core_Integration_Sica_User::getPersonId();
//                    $configs         = \Core_Registry::get('configs');
//                    $mergePdfOptions = $configs['merge_pdf'];
//                    $robotMode       = (boolean) $mergePdfOptions['robotMode'];
//
//                    if (!$robotMode) {
//                        $arrRetorno = $this->_processMergeFiles($sqArtefato, $sqPessoa);
//                        break;
//                    }
//
//                    $txEmailCorporativo = $this->_getEmailCorporativo($sqPessoa);
//
//                    if (!$txEmailCorporativo) {
//                        throw new \Core_Exception_ServiceLayer('Email corporativo não localizado. Impossível prosseguir.');
//                    }
//
//                    $entSolicitacaoDownloadImagem = $this->_newEntity('app:SolicitacaoDownloadImagem');
//                    $entSolicitacaoDownloadImagem->setSqArtefato($entArtefato)
//                            ->setSqPessoa($this->getEntityManager()->getPartialReference('app:VwPessoa', $sqPessoa))
//                            ->setSqUnidadeOrg($this->getEntityManager()->getPartialReference('app:VwUnidadeOrg', \Core_Integration_Sica_User::getUserUnit()))
//                            ->setDtSolicitacao(\Zend_Date::now())
//                            ->setStProcessado(FALSE)
//                            ->setInTentativa(0)
//                            ->setTxEmail($txEmailCorporativo);
//
//                    $this->getEntityManager()->persist($entSolicitacaoDownloadImagem);
//                    $this->getEntityManager()->flush();
//
//                    $arrRetorno['success']  = true;
//                    $arrRetorno['msg']      = sprintf(\Core_Registry::getMessage()->translate('MN169'), $txEmailCorporativo);
//                    $arrRetorno['link']     = '';
//                    $arrRetorno['filesize'] = '';
//
//                    break;
//            }

        } catch (\Exception $e) {
            $arrRetorno['success']  = false;
            $arrRetorno['msg']      = $e->getMessage();
            $arrRetorno['link']     = '';
        }

        return $arrRetorno;
    }

    private function _processDownloadUniqueFile($sqArtefato)
    {
        $sqPessoa        = \Core_Integration_Sica_User::getPersonId();

        if ($this->forbiddenAccess($sqArtefato, $sqPessoa)) {
            throw new \Core_Exception_ServiceLayer('Documento sigiloso. Você não tem permissão para fazer o download da imagem.');
        }

        $path            = current(explode('application', __DIR__));
        $pathOut         = $path . 'public' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        $fullPathFile    = $this->getFullImagePath($sqArtefato);
        $newFileName     = $this->generateTmpFilename($sqPessoa, $sqArtefato, basename($fullPathFile));
        $fullNewFileName = $pathOut.$newFileName;

        if(file_exists($fullNewFileName)){
            unlink($fullNewFileName);
        }

        $copyed = copy($fullPathFile, $fullNewFileName);

        if ($copyed) {
            $arrRetorno['success']  = true;
            $arrRetorno['msg']      = '';
            $arrRetorno['link']     = DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $newFileName; //pasta public/tmp
            $arrRetorno['filesize'] = $this->humanFilesize(filesize($fullNewFileName));
        }else{
            $arrRetorno['success']  = false;
            $arrRetorno['msg']      = 'Ocorreu um erro ao tentar baixar o arquivo com os anexos';
            $arrRetorno['link']     = '';
        }
        return $arrRetorno;
    }


    private function _getFilesize($sqArtefato)
    {
        return filesize($this->getFullImagePath($sqArtefato));
    }

    private function _getEmailCorporativo($sqPessoa)
    {
        $entEmail = $this->_getRepository('app:VwEmail')->findOneBy(array(
                'sqPessoa'    => $sqPessoa,
                'sqTipoEmail' => \Core_Configuration::getCorpTipoEmailInstitucional(),
            )
        );

        if(!$entEmail){
            return NULL;
        }

        return $entEmail->getTxEmail();

    }

    public function getFullImagePath($sqArtefato)
    {
        $path = current(explode('application', __DIR__)) . 'data' . DIRECTORY_SEPARATOR;
        return $path . $this->getImagePath($sqArtefato);
    }

    /**
     *
     * @param int $bytes filesize($file)
     * @param int $decimals
     * @return string
     */
    public function humanFilesize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    public function generateTmpFilename($sqPessoa, $sqArtefato, $filename)
    {
        return md5( ($sqPessoa + $sqArtefato) . $filename );
    }

    /**
     *
     * @param integer $id
     * @return SolicitacaoDownloadImagemEntity
     */
    public function findSolicitacaoDownloadImagem($id)
    {
        return $this->_getRepository('app:SolicitacaoDownloadImagem')->find($id);
    }

    /**
     *
     * @param integer $id
     * @return SolicitacaoDownloadImagemEntity
     */
    public function updateDtDownloadSolicitacao(SolicitacaoDownloadImagemEntity $entity)
    {
        $entity->setDtDownload(\Zend_Date::now());
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }



    /*************************/

    /**
     * Funcão chamada pelo robo
     */
    public function processMerge()
    {
        try{
            if(PHP_SAPI !== 'cli'){
                trigger_error("Este método '" . __METHOD__ . "' só deve ser utilizado via 'php cli'", E_USER_ERROR);
            }

            $pid = rand(0, 200000);
            $this->_sendMailProcess(\Zend_Date::now(), $pid, __FUNCTION__);

            $arrDownloadRequest = $this->_getRepository('app:SolicitacaoDownloadImagem')->findRequestsToProcess();

            $this->_closeConnection();

            if(!$arrDownloadRequest){
                \Robot\Debug::log('SEM REGISTRO PARA PROCESSAR', \Robot\Debug::INFO, \Robot\Debug::STEP_MERGE);
                $this->_sendMailProcess(\Zend_Date::now(), $pid, __FUNCTION__, FALSE, TRUE);
                exit(1);
            }

            $aux=array();
            foreach ($arrDownloadRequest as $entSol) {
                $aux[] = $entSol->getSqArtefato()->getSqArtefato();

            }
            \Robot\Debug::log('ID DOS ARTEFATOS A SEREM PROCESSADOS: ' . implode('-', $aux), \Robot\Debug::INFO, \Robot\Debug::STEP_MERGE);

            $this->_totalRegistroProcessar = count($arrDownloadRequest);

            $idCurrent = NULL;
            $arrSuccess = $arrError = array();
            \Robot\Debug::log('-INICIO-', \Robot\Debug::INFO, \Robot\Debug::STEP_MERGE);
            foreach ($arrDownloadRequest as $entSolDownImg) {
                try {
                    $idCurrent   = $entSolDownImg->getSqSolicitacaoDownloadImagem();
                    $sqArtefato  = $entSolDownImg->getSqArtefato()->getSqArtefato();
                    $sqPessoa    = $entSolDownImg->getSqPessoa()->getSqPessoa();

                    //pra usar no debug
                    self::$_sqArtefato = $sqArtefato;

                    self::_debug('Iniciando processamento', FALSE);

                    /* Incrementa o nr de tentativas */
                    $inTentativa = $entSolDownImg->getInTentativa();
                    $entSolDownImg->setInTentativa(++$inTentativa);

                    $this->getEntityManager()->persist($entSolDownImg);
                    $this->getEntityManager()->flush();

                    $arrMerged  = $this->_processMergeFiles($sqArtefato, $sqPessoa);

                    if ($arrMerged['success']) {
                        $entSolDownImg->setStProcessado( TRUE );
                        $entSolDownImg->setTxLInk( $arrMerged['link'] );

                        $this->getEntityManager()->persist($entSolDownImg);
                        $this->getEntityManager()->flush();

                        $this->_sendMailMergePdf($entSolDownImg);

                        $entArtefato = $entSolDownImg->getSqArtefato();

                        if ($entArtefato->isProcesso()) {
                            $numero = $entArtefato->getNuArtefato();
                        } else {
                            $numero = str_pad($entArtefato->getNuDigital()->getNuEtiqueta(), 7,'0',STR_PAD_LEFT);
                        }
                        $arrSuccess[] = $numero;

                    } else {
                        $this->_sendMailMergePdf($entSolDownImg, TRUE);
                    }
                } catch (\Exception $ex) {
                    self::_debug($ex->getMessage());
                    //enviar email com erro
                    $this->_sendMailMergePdf($entSolDownImg, TRUE);
                    $arrError[] = PHP_EOL . "Ocorreu um erro ao processar [SolicitacaoDownloadImage::{$idCurrent}] " . $ex->getMessage();
                }

                self::_debug('Processado', FALSE);
            }
            \Robot\Debug::log('-FIM-' . PHP_EOL, \Robot\Debug::INFO, \Robot\Debug::STEP_MERGE);
            if ($arrError) {
                //registra no log do robo
                $this->_sendMailError($arrError);
            }
            $this->_sendMailProcess(\Zend_Date::now(), $pid, __FUNCTION__, FALSE, FALSE, $arrSuccess);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private static function _debug($msg, $tab = TRUE, $label = \Robot\Debug::INFO)
    {
        $strTab = ($tab) ? chr(9):'';

//        if (self::$_debug) {
            \Robot\Debug::log("ARTEFATO[" . self::$_sqArtefato . "] {$strTab} {$msg}", $label, \Robot\Debug::STEP_MERGE);
//        }
    }

    private function _processMergeFiles($sqArtefato, $sqPessoa)
    {
        //sudo apt-get install libimage-exiftool-perl pdftk

        if(APPLICATION_ENV != 'development' && PHP_SAPI !== 'cli'){
            trigger_error("Este método '" . __METHOD__ . "' só deve ser utilizado via 'php cli'", E_USER_ERROR);
        }

        $path               = current(explode('application', __DIR__));
        $pathIn             = $path . 'data'   . DIRECTORY_SEPARATOR;
        $pathOut            = $path . 'public' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
        $configs            = \Core_Registry::get('configs');
        $arrMetadataPDF     = $configs['merge_pdf'];

        $arrArtefatoSubject = array();
        $arrPdfToMerge      = array();
        $arrBookmark        = array();
        $pageCount          = 0;
        $i                  = 0;
        $arrArtefatoTreeview= array();
        $treeview           = $this->getServiceLocator()->getService('ArtefatoVinculo')->mostarArvore($sqArtefato);

        $this->_normalizeTreeview($arrArtefatoTreeview, $treeview);


        $this->_closeConnection();

        //lupa os artefatos para recuperar os PDF para merge
        foreach ($arrArtefatoTreeview as $key => $value) {
            $sqArtefato = $value['id'];
            $nuArtefato = $value['nr'];
            try{
                //$i = 0 é a raiz da treeview, logo é o artefato paizão
                if($i === 0){
                    $firstNumber    = $nuArtefato;
                    $entArtefato    = $this->_getRepository($this->_entityArtefato)->find($sqArtefato);
                    $noTipoArtefato = $entArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getNoTipoArtefato();
                }

                /**
                 * verifica se existe a imagem e sua integridade
                 * lança exception caso estiver violada ou cadastrada mas nao localizada
                 */
                $hasImage = $this->hasImage($sqArtefato);

                /**
                 * processo não possui imagem. A imagem é a do documento de 1ª Peça
                 */
                if ($hasImage) {
                    if ($this->forbiddenAccess($sqArtefato, $sqPessoa)) {
                        $value['qtdePage'] = 1; // se não tem permissão o forbiddenAccess.pdf só tem 1 pagina
                        $pathDoc           = $pathIn . 'upload' . DIRECTORY_SEPARATOR . 'forbiddenAccess.pdf';
                    }else{
                        $pathDoc = $pathIn . $this->getImagePath($sqArtefato);
                    }
                    $arrPdfToMerge[]      = $pathDoc . " ";
                    $arrArtefatoSubject[] = $nuArtefato;
                }
                $pageCount  += $value['qtdePage'];

                $arrBookmark[] = array(
                    'BookmarkTitle' => $nuArtefato,
                    'BookmarkLevel' => $value['nivel'],
                    'BookmarkPageNumber'=> ($pageCount - $value['qtdePage']) + 1,
                );

                $i++;
            } catch (\Exception $e) {
                throw new \Exception("Ocorreu um erro no artefato <b>{$nuArtefato}</b> e o PDF não pode ser montado para download: {$e->getMessage()}");
            }
        }

        $hashOutputName   = $this->generateTmpFilename($sqPessoa, $sqArtefato, $firstNumber);
        $bookmarkFileName = $pathOut . $hashOutputName . '.txt';
        //gera arquivo txt com os dados do bookmark para insersão no PDF final
        $bookmark         = $this->_generateBookmarkFile($bookmarkFileName, $arrBookmark);

        $outputName       = $hashOutputName.'.pdf';
        $fullOutputTmpName= $pathOut . $outputName.'_tmp';
        $fullOutputName   = $pathOut . $outputName;

        $subject          = sprintf($arrMetadataPDF['subject'], implode(', ', $arrArtefatoSubject));
        $title            = sprintf($arrMetadataPDF['title'], $noTipoArtefato ,$firstNumber);

/*
//        quando usava pdftk

//        $cmd = "pdftk " . implode(' ', $arrPdfToMerge);
//
//        if($bookmark){
//            //merge dos PDF
//            $cmd .= " cat output {$fullOutputTmpName}";
//            //set cria bookmark
//            $cmd .= " && pdftk {$fullOutputTmpName} update_info {$bookmarkFileName} output {$fullOutputName}";
//            //set metadata
//            $cmd .= " && exiftool -Title=\"{$title}\" -Author=\"{$arrMetadataPDF['author']}\" -Subject=\"{$subject}\" {$fullOutputName}";
//        } else {
//            //merge dos PDF
//            $cmd .= " cat output {$fullOutputName} ";
//            //set metadata
//            $cmd .= " && exiftool -Title=\"{$title}\" -Author=\"{$arrMetadataPDF['author']}\" -Subject=\"{$subject}\" {$fullOutputName}";
//        }
*/

        $cmd = "gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile={$fullOutputName} " . implode(' ', $arrPdfToMerge);
        $cmd .= " && exiftool -Title=\"{$title}\" -Author=\"{$arrMetadataPDF['author']}\" -Subject=\"{$subject}\" {$fullOutputName}";

        //se o arquivo já existe no /tmp exclui pois o doc pode ter sofrido alteração
        if (file_exists($fullOutputName)) {

            unlink($fullOutputName);

            $original = $fullOutputName.'_original';

            if (is_file($original)) {
                unlink($fullOutputName.'_original');
            }
        }

        $result = shell_exec($cmd);

        if ($result && file_exists($fullOutputName)) {
            $arrRetorno['success'] = true;
            $arrRetorno['link'] = DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $outputName;
            $arrRetorno['msg']      = '';
            $arrRetorno['filesize'] = $this->humanFilesize(filesize($fullOutputName));
        }else{
            $arrRetorno['success'] = false;
            $arrRetorno['msg']     = 'Ocorreu um erro ao tentar baixar o arquivo com os anexos';
            $arrRetorno['link']    = '';
        }

        //arquivo bookmark
        if (file_exists($bookmarkFileName)) {
            unlink($bookmarkFileName);
        }

        return $arrRetorno;
    }

    private function _generateBookmarkFile($fileName, array $arrBookmarkData)
    {
        $strBookmark='';
        foreach ($arrBookmarkData as $key => $values) {
            $strBookmark .= 'BookmarkBegin' . PHP_EOL;
            foreach ($values as $key => $value) {
                $strBookmark .= $key . ': ' . $value . PHP_EOL;
            }
        }
        return file_put_contents($fileName, $strBookmark);
    }

    /**
     *
     * @param array $arrAux
     * @param array $treeview
     * @return void
     */
    private function _normalizeTreeview(array &$arrAux, array $treeview)
    {
        foreach ($treeview as $key => $value) {
            $entUltimaImgArtefato = $this->_getRepository('app:VwUltimaImagemArtefato')
                                         ->findBy(array('sqArtefato'=>$key));
            $qtdePage = ($entUltimaImgArtefato) ? $entUltimaImgArtefato[0]->getNuQtdePaginas() : NULL;
            $nCount   = array();

            preg_match_all('/(?P<found>,)/mi', $value['nivel'], $nCount);
            $arrAux[] = array(
                'id'       => $key,
                'nr'       => $value['nuArtefato'],
                'nivel'    => count($nCount['found']) + 1,
                'qtdePage' => $qtdePage
            );

            $this->_normalizeTreeview($arrAux, $value['filhos']);
        }
        return;
    }

    private function _sendMailMergePdf(SolicitacaoDownloadImagemEntity $ent, $error = FALSE)
    {
        $configs = \Core_Registry::get('configs');

        $subject  = $configs['merge_pdf']['mail']['subjectSuccess'];
        $template = 'merge_pdf_success.phtml';
        if ($error) {
            $subject = $configs['merge_pdf']['mail']['subjectError'];
            $template = 'merge_pdf_error.phtml';
        }

        $txEmailDestinatario = trim($ent->getTxEmail());
        $url  = $configs['merge_pdf']['mail']['urlLink'];
        $url .= '/artefato/imagem/view-image-download/id/'.  base64_encode($ent->getSqSolicitacaoDownloadImagem());

        $arguments = array('entSolicitacao'  => $ent,
                           'urlLink'         => $url,
                           'qtdeTentativa'   => (integer) $configs['merge_pdf']['qtdeTentativa'],
                           'imgLogo'         => self::PATH_IMAGE_LOGO,
        );
        $SgdoceMail = new \Sgdoce_Mail();
        $SgdoceMail->prepareBodyHtml($template, $arguments);
        $SgdoceMail->setRecipients(array('para' => array($ent->getSqPessoa()->getNoPessoa() => $txEmailDestinatario)));
        $SgdoceMail->setSubject($subject);
        $SgdoceMail->send();
    }

    /**
     * Envia email com a compilação dos erros gerados durente o ciclo de migração
     *
     * @param array $arrError
     * @return self
     */
    private function _sendMailProcess(\Zend_Date $zd, $pid, $processType, $start=TRUE, $withoutProcess = FALSE, $arrSuccess=array())
    {
        if (!$this->_checkSendEmail()) {
            return $this;
        }

        $configs = \Core_Registry::get('configs');

        $enviroment = '';

        if (APPLICATION_ENV != 'production') {
            $enviroment = '[' . APPLICATION_ENV . ']';
        }

        $type = ($start) ? 'Start':'Stop';

        $subject  = "{$type}::{$pid} {$processType} {$enviroment}";
        $template = 'process.phtml';

        $arrTo = array();
        ;
        foreach ($configs['merge_pdf']['mail']['to'] as $value) {
            $arrTo['para'][$value['name']] = $value['email'];
        }

        $arguments = array(
            'totalRegistroProcessar'=> $this->_totalRegistroProcessar,
            'withoutProcess'=> $withoutProcess,
            'objZDProcess'  => $zd,
            'processType'   => $processType,
            'type'          => $type,
            'arrSucesso'    => $arrSuccess,
            'imgLogo'       => self::PATH_IMAGE_LOGO,
        );

        $SgdoceMail = new \Sgdoce_Mail();
        $SgdoceMail->prepareBodyHtml($template, $arguments);
        $SgdoceMail->setRecipients($arrTo);
        $SgdoceMail->setSubject($subject);
        $SgdoceMail->send();

        return $this;
    }

    /**
     * Envia email com a compilação dos erros gerados durente o ciclo de migração
     *
     * @param array $arrError
     * @return self
     */
    private function _sendMailError(array $arrError)
    {
        $configs = \Core_Registry::get('configs');

        $enviroment = '';

        if (APPLICATION_ENV != 'production') {
            $enviroment = '[' . APPLICATION_ENV . ']';
        }

        $subject  = "Resumo de erros ocorridos no MergePdf {$enviroment}";
        $template = 'merge_pdf_resume_error.phtml';

        $arrTo = array();
        foreach ($configs['merge_pdf']['mail']['to'] as $value) {
            $arrTo['para'][$value['name']] = $value['email'];
        }

        $arguments = array(
            'erros'  => $arrError,
            'imgLogo' => self::PATH_IMAGE_LOGO,
        );

        $SgdoceMail = new \Sgdoce_Mail();
        $SgdoceMail->prepareBodyHtml($template, $arguments);
        $SgdoceMail->setRecipients($arrTo);
        $SgdoceMail->setSubject($subject);
        $SgdoceMail->send();

        return $this;
    }

    private function _checkSendEmail()
    {
        $configs = \Core_Registry::get('configs');
        return $configs['merge_pdf']['mail']['sendStatus'];
    }

    public function excluirImagem( $entArtefato, $txObservacao, $stAtivo = FALSE )
    {
        try {
            $entArtefatoImagem = $this->_getRepository("app:ArtefatoImagem")->findBy(array('sqArtefato' => $entArtefato->getSqArtefato(), 'stAtivo' => true));
            $entArtefatoImagem = current($entArtefatoImagem);

            if( $entArtefatoImagem ) {

                $arrDto = array(
                    'sqPessoa'  => \Core_Integration_Sica_User::getPersonId(),
                    'sqUnidade' => \Core_Integration_Sica_User::getUserUnit()
                );

                $objCDto = \Core_Dto::factoryFromData($arrDto, 'search');

                $entVwPessoa    = $this->getServiceLocator()
                                       ->getService('Pessoa')
                                       ->findbyPessoaCorporativo($objCDto);
                $entVwUnidOrg   = $this->getServiceLocator()
                                       ->getService('VwUnidadeOrg')
                                       ->getDadosUnidade($objCDto);

                $entArtefatoImagem->setStAtivo($stAtivo);
                $entArtefatoImagem->setSqPessoaInativacao($entVwPessoa);
                $entArtefatoImagem->setSqUnidadeOrgInativacao($entVwUnidOrg);
                $entArtefatoImagem->setTxObservacao($txObservacao);
                $entArtefatoImagem->setDtInativacao(\Zend_Date::now());

                $this->getEntityManager()->persist($entArtefatoImagem);
                $this->getEntityManager()->flush();

                // REGISTRO DA EXCLUSÃO DA IMAGEM. #HistoricoArtefato::save();
                $haService    = $this->getServiceLocator()->getService('HistoricoArtefato');
                $sqOcorrencia = \Core_Configuration::getSgdoceSqOcorrenciaExcluirImagem();
                $strMessage   = $haService->getMessage(
                    'MH024',
                    \Zend_Date::now()->get(\Zend_Date::DATETIME_MEDIUM),
                    \Core_Integration_Sica_User::getUserName()
                );
                $haService->registrar($entArtefato->getSqArtefato(), $sqOcorrencia, $strMessage);


                $this->getMessaging()->addSuccessMessage('MD003','User');
            } else {
                $this->getMessaging()->addErrorMessage('MN174', 'User');
            }

            $this->getMessaging()->dispatchPackets();

            return $entArtefatoImagem;
        } catch( \Core_Exception_ServiceLayer $e ) {
            $this->getMessaging()->addErrorMessage($e->getMessage(), 'User');
            $this->getMessaging()->dispatchPackets();
        }
        return false;
    }

    /**
     *
     * @param Dto $dto
     * @return array
     */
    public function getSolicitacaoMigracaoImagem( \Core_Dto_Search $dto )
    {
        $listSolicitacoes = $this->_getRepository('app:SolicitacaoMigracaoImagem')->findBy(array(
            'sqArtefato' => $dto->getSqArtefato()
        ));

        return $listSolicitacoes;
    }

    /**
     *
     * @param Dto $dto
     * @return boolean
     */
    public function hasArtefatoImagemData( \Core_Dto_Search $dto )
    {
        $listArtefatoImagem = $this->findBy(array(
            'sqArtefato' => $dto->getSqArtefato()
        ));

        if( count($listArtefatoImagem) ) {
            return true;
        }
        return false;
    }

    public function hasArtefatoImagemAtiva( \Core_Dto_Search $dto )
    {
        $listArtefatoImagem = $this->findBy(array(
            'sqArtefato' => $dto->getSqArtefato(),
            'stAtivo' => TRUE
        ));

        if( count($listArtefatoImagem) ) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param Dto $dto
     * @return boolean
     */
    public function hasSoliciacaoProcessado( \Core_Dto_Search $dto )
    {
        $listSolicitacao = $this->getSolicitacaoMigracaoImagem($dto);
        if(count($listSolicitacao)) {
            $entSolicitacao = current($listSolicitacao);
            return !$entSolicitacao->getStProcessado();
        }
        return false;
    }

    /**
     * Usuários autorizados para alteração de imagem.
     *
     * @return array
     */
    public function getUsersAllowedAlterImage()
    {
        return array(
            \Core_Configuration::getSgdocePerfilSedoc(),
            \Core_Configuration::getSgdocePerfilSgi(),
        );
    }


    private function _closeConnection()
    {
        if ($this->getEntityManager()->getConnection()->isConnected()) {
            $this->getEntityManager()->getConnection()->close();
        }
    }
}