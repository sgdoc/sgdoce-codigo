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

use Sgdoce\Model\Entity\Artefato as ArtefatoEntity;
use Sgdoce\Model\Entity\ArtefatoImagem as ArtefatoImagemEntity;
use Sgdoce\Model\Entity\SolicitacaoMigracaoImagem as SolicitacaoMigracaoImagemEntity;
use Artefato\Service\ArtefatoImagem as ArtefatoImagemService;
/**
 * Classe para Service de ArtefatoImagem
 *
 * @package  Migracao
 * @category Service
 * @name     MigrationImage
 * @version  1.0.0
 */
class MigrationImage extends \Core_ServiceLayer_Service_CrudDto
{
    //tipo em stPublico
    const ACESSO_CONFIDENCIAL = 0;
    const ACESSO_PUBLICO      = 1;
    const EXCLUIDO            = 2;
    //tipos em inTipoArquivo
    const ARQUIVO_TIF         = 7;
    const ARQUIVO_PNG         = 8; //todos os png estao com flag excluido, logo, não serão processado
    const ARQUIVO_PDF         = 9;
    const CODE_IMG_NOT_FOUND  = 10000;
    const CODE_IMG_CORRUPTED  = 10001;

    //@TODO: CONSTANTES temporarias devem ser removidas quando tiver informação de tramite
//    const DEFAULT_USER        = 42526;
//    const DEFAULT_UNIT        = 11086;

    private $_extension = array(
        self::ARQUIVO_TIF => '.tif',
        self::ARQUIVO_PDF => '.pdf',
        self::ARQUIVO_PNG => '.png',
    );

    private $_totalRegistroProcessar = 0;

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
    protected $_entityVwUltimaImagemArtefato = 'app:VwUltimaImagemArtefato';

    /**
     * Armazena o path de origem das dos pdf/tif a serem processados
     * Essa informação requer dados configurados no application.ini (migration.imagePath) para definir o root_path das imagens
     * @var string
     */
    private $_imagePathOut = null;

    /**
     * Armazena o path de destino do pdf final a ser persistido em artefato_imagem
     * @var string
     */
    private $_imagePathIn = null;

    /**
     * Armazena os dados de configuração 'migration' constantes em application.ini
     * @var array
     */
    private $_configsMigration = null;

    /**
     * Armazena os dados de configuração 'merge_pdf' constantes em application.ini
     * @var array
     */
    private $_configsMergePdf = null;

    /**
     * Armazena os pdf na sequencia que serão mergeados para formar um unico bloco documental para persistencia em artefato_imagem
     * @var array
     */
    private $_arrPdf = array();

    /**
     * Armazena os path de arquivos temporarios que devem ser excluidos ao final do processo de migração
     * @var array
     */
    private $_arrTmpFile = array();

    /**
     *
     * @var integer
     */
    private $_nuDigital = NULL;
    /**
     *
     * @var integer
     */
    private $_txDigital = '';

    /**
     * Armazena o contador de paginas para ser utilizado para persistencia em artefato_imagem
     * @var integer
     */
    private $_pageCount = 0;

    /**
     * Armazena ID da pessoa para persistencia em artefato_imagem
     * @var integer
     */
    private $_sqPessoa     = NULL;

    /**
     * Armazena ID da unidade para persistencia em artefato_imagem
     * @var integer
     */
    private $_sqUnidadeOrg = NULL;

    /**
     * Armazena ID do artefato que esta sendo processado
     * @var integer
     */
    private static $_sqArtefato = NULL;

    /**
     * Determina o step corrente para escrever no arquivo de log correto
     *
     * @var string
     */
    private static $_stepCurrent = NULL;

    private static $_debug = TRUE;

    /**
     *
     * @param type $key
     * @return type
     */
    private function _getMigrationConfigs ($key = NULL)
    {
        if (is_null($this->_configsMigration)) {
            $configs        = \Core_Registry::get('configs');
            $this->_configsMigration = $configs['migration'];
        }

        if (is_null($key)) {
            return $this->_configsMigration;
        }else{
            return $this->_configsMigration[$key];
        }
    }

    private function _getMergePdfConfigs ($key = NULL)
    {
        if (is_null($this->_configsMergePdf)) {
            $configs        = \Core_Registry::get('configs');
            $this->_configsMergePdf = $configs['merge_pdf'];
        }

        if (is_null($key)) {
            return $this->_configsMergePdf;
        }else{
            return $this->_configsMergePdf[$key];
        }
    }


    private function _closeConnection()
    {
        if ($this->getEntityManager()->getConnection()->isConnected()) {
            $this->getEntityManager()->getConnection()->close();
        }
    }

    /**
     * Metodo chamado pelo robo e responsável pro obter as Solicitações de Migração de Imagem e processar a migração
     * Obs.: só pode ser executado em modo "cli"
     *
     * @throws \Exception
     * @return void
     */
    public function runProcessRequests()
    {
        self::$_stepCurrent = \Robot\Debug::STEP_IMAGE_REQUESTED;

        \Robot\Debug::log('Iniciando processo', \Robot\Debug::INFO, self::$_stepCurrent);

         try{

            if(PHP_SAPI !== 'cli'){
                trigger_error("Este método '" . __METHOD__ . "' só deve ser utilizado via 'php cli'", E_USER_ERROR);
            }


            $pid = rand(0, 200000);
            $this->_sendMailProcess(\Zend_Date::now(), $pid, __FUNCTION__);

            \Robot\Debug::log('Recuperando informações a serem processadas', \Robot\Debug::INFO, self::$_stepCurrent);

            $arrSolicitacaoMigracao = $this->_getRepository('app:SolicitacaoMigracaoImagem')
                                           ->findRequestsToProcess($this->_getMigrationConfigs('limitByStepRequest'));

            //fecha a conexão com banco
            $this->_closeConnection();

            if(!$arrSolicitacaoMigracao){
                \Robot\Debug::log('SEM REGISTRO PARA PROCESSAR', \Robot\Debug::INFO, self::$_stepCurrent);
                $this->_sendMailProcess(\Zend_Date::now(), $pid, __FUNCTION__, FALSE, TRUE);
                exit(0);
            }
            $aux=array();
            foreach ($arrSolicitacaoMigracao as $entSolMigrImg) {
                $aux[] = $entSolMigrImg->getSqArtefato()->getSqArtefato();

            }
            \Robot\Debug::log('ID DOS ARTEFATOS A SEREM PROCESSADOS: ' . implode('-', $aux), \Robot\Debug::INFO, self::$_stepCurrent);

            $this->_totalRegistroProcessar = count($arrSolicitacaoMigracao);

            $arrError = array();
            $arrSuccess = array();
            \Robot\Debug::log('-INICIO-', \Robot\Debug::INFO, self::$_stepCurrent);
            foreach ($arrSolicitacaoMigracao as $entSolMigrImg) {

                self::_debug('Obtendo dados da solicitacao', FALSE);
                $sqArtefato = $entSolMigrImg->getSqArtefato()->getSqArtefato();

                //pra usar no debug
                self::$_sqArtefato = $sqArtefato;

                //inicia o contador de pagina
                $this->_pageCount    = 0;
                //informação utilizada na tabela artefato_imagem
                $this->_sqPessoa     = $entSolMigrImg->getSqPessoa()->getSqPessoa();
                $this->_sqUnidadeOrg = $entSolMigrImg->getSqUnidadeOrg()->getSqUnidadeOrg();

                self::_debug("Solicitante [pessoa[{$this->_sqPessoa}]] [unidade[{$this->_sqUnidadeOrg}]]", FALSE);

                try {
                    $entArtefato = $entSolMigrImg->getSqArtefato();

                    if( $entArtefato->isProcesso() ){
                        continue;
                    }

                    $digital = str_pad($entArtefato->getNuDigital()->getNuEtiqueta(), 7,'0',STR_PAD_LEFT);

                    self::_debug('Iniciando processamento', FALSE);
                    //verifica antes de processar pois o robo de migração
                    //pode ter migrado a imagem antes desse processamento
                    if ($this->_getRepository($this->_entityVwUltimaImagemArtefato)->find($sqArtefato)) {
                        self::_debug('Imagem migrada pelo robo WithoutImage');
                        $inTentativa = $entSolMigrImg->getInTentativa();
                        $entSolMigrImg->setInTentativa(++$inTentativa);
                        $entSolMigrImg->setStProcessado( TRUE );

                        $this->getEntityManager()->persist($entSolMigrImg);
                        $this->getEntityManager()->flush();

                        $arrSuccess[] = $digital;

                        continue;
                    }

                    /* Incrementa o nr de tentativas */
                    $inTentativa = $entSolMigrImg->getInTentativa();
                    $entSolMigrImg->setInTentativa(++$inTentativa);
                    $this->getEntityManager()->persist($entSolMigrImg);
                    $this->getEntityManager()->flush();

                    //este IF é só pra garantir
                    //não há solicitação em processo, apenas em seus filhos do tipo documento
                    if ($entArtefato->isProcesso()) {
                        continue;
                    } else {
                        $this->_migraDocumento($entArtefato);
                    }

                    $entSolMigrImg->setStProcessado( TRUE );

                    $this->getEntityManager()->persist($entSolMigrImg);
                    $this->getEntityManager()->flush();

                    $this->_sendMailMigrationPdf($entSolMigrImg);

                    $arrSuccess[] = $digital;
                } catch (\Zend_Mail_Exception $ex) {
                    self::_debug("Erro ao enviar email de notificaçao ao solicitante [{$entSolMigrImg->getTxEmail()}] {$ex->getMessage()}");
                    //envia email para adm do sistema
                    $arrError[] = PHP_EOL . "Ocorreu um erro ao enviar email [Artefato::{$sqArtefato}][{$entSolMigrImg->getTxEmail()}] " . $ex->getMessage();
                } catch (\Exception $ex) {
                    //validar email do solicitante e só enviar se estiver certo
                    $validate = new \Zend_Validate_EmailAddress();
                    $emailValid = $validate->isValid($entSolMigrImg->getTxEmail());

                    if (in_array($ex->getCode(), array(self::CODE_IMG_NOT_FOUND, self::CODE_IMG_CORRUPTED))) {
                        $entSolMigrImg->setStProcessado( TRUE );
                        $this->getEntityManager()->persist($entSolMigrImg);
                        $this->getEntityManager()->flush();

                        $corrupted = FALSE;
                        if (self::CODE_IMG_CORRUPTED === $ex->getCode()){
                            $corrupted = TRUE;
                        }
                        if ($emailValid){
                            $this->_sendMailMigrationPdfNotFound($entSolMigrImg, $corrupted);
                        }
                    } else if ($emailValid){
                        $this->_sendMailMigrationPdf($entSolMigrImg, TRUE);
                    }
                    $arrError[] = PHP_EOL . "Ocorreu um erro ao processar [Artefato::{$sqArtefato}] " . $ex->getMessage();
                }

                self::_debug('Processado', FALSE);
            }

            \Robot\Debug::log('-FIM-' . PHP_EOL, \Robot\Debug::INFO, self::$_stepCurrent);

            if ($arrError) {
                //registra no log do robo
                $this->_sendMailError($arrError, __FUNCTION__, $pid);
            }

            $this->_sendMailProcess(\Zend_Date::now(), $pid, __FUNCTION__, FALSE, FALSE, $arrSuccess);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Metodo chamado pelo robo e responsável pro obter "Documentos" sem "Vinculo" (avulso) para processar a migração de imagens
     * Obs.: só pode ser executado em modo "cli"
     * @throws \Exception
     * @return void
     */
    public function runArtefatoWithoutImage()
    {
        self::$_stepCurrent = \Robot\Debug::STEP_IMAGE;
        \Robot\Debug::log('Iniciando processo', \Robot\Debug::INFO, self::$_stepCurrent);

         try{
            if(PHP_SAPI !== 'cli'){
                trigger_error("Este método '" . __METHOD__ . "' só deve ser utilizado via 'php cli'", E_USER_ERROR);
            }

            $pid = rand(0, 200000);
            $this->_sendMailProcess(\Zend_Date::now(), $pid, __FUNCTION__);

            if (! $this->_checkOrigemFolder()) {
                $error = array('O path de origem das imagens para migração não '
                         . 'foi definido no arquivo de configuração "application.ini" ou esta inválido');
                $this->_sendMailError($error, 'runArtefatoWithoutImage', $pid);
                throw new \Exception('O path de origem das imagens não foi definido no arquivo de configuração "configs.ini"');
            }

            \Robot\Debug::log('Recuperando informações a serem processadas', \Robot\Debug::INFO, self::$_stepCurrent);
            $arrArtefatoWithoutImage = $this->_getRepository($this->_entityName)
                                            ->findDocumentoSemImagem($this->_getMigrationConfigs('limitByStep'));

            //fecha a conexão com banco
            $this->_closeConnection();

            \Robot\Debug::log('Informações recuperada', \Robot\Debug::INFO, self::$_stepCurrent);

            if(! $arrArtefatoWithoutImage){
                $this->_sendMailProcess(\Zend_Date::now(), $pid, __FUNCTION__, FALSE, TRUE);
                exit(1);
            }

            $this->_totalRegistroProcessar = count($arrArtefatoWithoutImage);

            $arrSuccess = $arrError = array();

            \Robot\Debug::log('-INICIO-', \Robot\Debug::INFO, self::$_stepCurrent);

            foreach ($arrArtefatoWithoutImage as $value) {
                $sqArtefato = $value['sqArtefato'];

                self::$_sqArtefato = $sqArtefato;

                try {
                    //inicia o contador de pagina
                    $this->_pageCount = 0;

                    $entArtefato = $this->_getRepository($this->_entityArtefato)->find($sqArtefato);
                    $digital = str_pad($entArtefato->getNuDigital()->getNuEtiqueta(), 7,'0',STR_PAD_LEFT);
                    //apesar da query que busca os artefatos a processar fazer a verificação
                    //de existencia de ultima imagem, verifica novamente antes de processar pois o robo de solicitação de migração
                    //pode ter migrado a imagem antes desse processamento
                    if ($this->_getRepository($this->_entityVwUltimaImagemArtefato)->find($sqArtefato)) {
                        $arrSuccess[] = $digital;
                        continue;
                    }

                    //seta sqPessoa e sqUnidadeOrg ou exception caso não encontra ultimo tramite
                    $this->_fillLastTramiteInformation($entArtefato);
                    $this->_migraDocumento($entArtefato);

                    $arrSuccess[] = $digital;
                } catch (\Exception $ex) {
                    self::_debug($ex->getMessage());

                    $this->getEntityManager()->getConnection()->insert(
                            'sgdoce.tmp_artefato_migration',
                            array('sq_artefato'=>$sqArtefato,'dt_operacao'=>  date('Y-m-d H:i:s.u'))
                    );
                    $arrError[] = PHP_EOL . "Ocorreu um erro ao processar [Artefato::{$sqArtefato}] " . $ex->getMessage();
                }
            }

            \Robot\Debug::log('-FIM-', \Robot\Debug::INFO, self::$_stepCurrent);

            if ($arrError) {
                //registra no log do robo
                $this->_sendMailError($arrError, __FUNCTION__, $pid);
            }

            $this->_sendMailProcess(\Zend_Date::now(), $pid, __FUNCTION__, FALSE, FALSE, $arrSuccess);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function _checkOrigemFolder()
    {
        if (!file_exists($this->_getMigrationConfigs('imagePath'))) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     *
     * @param ArtefatoEntity $entArtefato
     * @return boolean
     * @throws \Exception
     */
    private function _migraDocumento(ArtefatoEntity $entArtefato)
    {

        //fecha a conexão com banco
        $this->_closeConnection();

//        tb_documentos_imagem.flg_publico = [0=>confidencial, 1=publico, 2=>excluido]
//        tb_documentos_imagem.img_type = [7=>TIF, 8=>PNG, 9=>PDF]
//        todos o PNG esta excluido (2)

        $sqArtefato       = $entArtefato->getSqArtefato();
        $this->_nuDigital = $entArtefato->getNuDigital()->getNuEtiqueta();
        $this->_txDigital = str_pad($this->_nuDigital, 7,'0',STR_PAD_LEFT);

        $arrImagem = $this->_getRepository('app:VwImagemSgdocFisico')
                          ->findBy(array('sqArtefato'=> $sqArtefato),
                                   array('dtInclusao'=> 'ASC',
                                         'nuOrdem'   => 'ASC'));

        if (!$arrImagem) {
            self::_debug('Sem imagem para processar');
            throw new \Exception("Nenhuma imagem encontrada para a digital {$this->_txDigital}", self::CODE_IMG_NOT_FOUND);
        }

        $this->_setImagePathIn($entArtefato)
             ->_setImagePathOut();

        $this->_arrPdf = array();
        $arrTif        = array();
        $sigiloso      = false;
        $arrFileNotFound = array();

        self::_debug('Iniciando conversão das imagems caso necessário');

        foreach ($arrImagem as $imagem) {
            $typeFile       = $imagem->getInTipoArquivo();
            $filename       = $imagem->getTxNomeArquivo() . $this->_extension[$typeFile];
            $fullFilename   = $this->_imagePathOut . $filename;
            $stPublico      = $imagem->getStPublico();

            $this->_pageCount += $imagem->getInQtdePagina();

            //verifica se alguma imagem é confidencial para setar o grau acesso do artefato
            if ($stPublico === self::ACESSO_CONFIDENCIAL && false === $sigiloso) {
                $sigiloso = true;
            }

            //se o arquivo da imagem não existir no file system registra o nome do arquivo
            if (! file_exists($fullFilename)) {
                $arrFileNotFound[] = $filename;
                continue;
            }

            //adicion tif na stack de conversão
            if ($typeFile == self::ARQUIVO_TIF) {

                $arrTif[] = $fullFilename;

                if (! filesize($fullFilename)) {
                    self::_debug("Digital {$this->_txDigital} com imagem corrompida");
                    throw new \Exception("A digital {$this->_txDigital} possui uma ou mais imagens corrompidas", self::CODE_IMG_CORRUPTED);
                }
            }

            if ($typeFile == self::ARQUIVO_PDF) {
                //se for pdf e possui tif na stack
                //processa os tifs para transformar em pdf
                //antes de adicionar a proxima imagem pdf na stack
                if ($arrTif) {
                    $this->_convertTifToPdf($arrTif, $this->_arrPdf);
                    //limpa a fila de tif após a conversão
                    $arrTif = array();
                }

                $this->_arrPdf[] = $fullFilename;
            }
        }

        if ($arrFileNotFound) {
            self::_debug("O(s) arquivo(s) ". implode(', ', $arrFileNotFound)." não foi(ram) localizado(s) no diretório de origem ({$this->_imagePathOut}) para digital {$this->_txDigital}");

            //para o processo do artefato da vez
            $this->_unlinkTmpFile();
            throw new \Exception("O(s) arquivo(s) ". implode(', ', $arrFileNotFound)." não foi(ram) localizado(s) no diretório de origem ({$this->_imagePathOut}) para digital {$this->_txDigital}");
        }

        //por fim se sobrou tif na stack processa novamente
        if ($arrTif) {
            $this->_convertTifToPdf($arrTif);
            //limpa a fila de tif
            $arrTif = array();
        }

        //setar grau de acesso para o artefato
        $sqGrauAcesso = \Core_Configuration::getSgdoceGrauAcessoPublico();
        if ($sigiloso) {
            $sqGrauAcesso = \Core_Configuration::getSgdoceGrauAcessoSigiloso();
        }

        self::_debug('Conversões finalizadas');

        $this->_processMergePdf($entArtefato)
             ->_grauAcesso($entArtefato, $sqGrauAcesso)
             ->_unlinkTmpFile();
    }

    /**
     * Exclui os arquivos adicionando na lista de arquivos temporários
     *
     * @return \Migracao\Service\MigrationImage
     */
    private function _unlinkTmpFile()
    {
        self::_debug("Removendo arquivos temporários");

        foreach ($this->_arrTmpFile as $filename) {
            if (file_exists($filename)) {
                unlink ($filename);
            }
        }
        return $this;
    }

    /**
     * Seta o diretorio de destino das imagem no sistema eletronico
     *
     * @param ArtefatoEntity $entArtefato
     * @return \Migracao\Service\MigrationImage
     */
    private function _setImagePathIn(ArtefatoEntity $entArtefato)
    {
        $etiquetaNupSiorgEntity = $entArtefato->getNuDigital();
        $loteFolder = (string) $etiquetaNupSiorgEntity->getSqLoteEtiqueta()->getSqLoteEtiqueta();
        $digitalFolder = (string) $etiquetaNupSiorgEntity->getNuEtiqueta();
        $this->_imagePathIn = sprintf(
            'upload%1$simagem%1$s%2$s%1$s%3$s%1$s',
            DIRECTORY_SEPARATOR,
            $loteFolder,
            $digitalFolder
        );

        return $this;
    }

    /**
     * Seta o diretorio de origem das imagem no sistema físico
     *
     * @return \Migracao\Service\MigrationImage
     */
    private function _setImagePathOut()
    {
        $loteFolder    = 'LOTE'.(integer)($this->_nuDigital/10000);
        $this->_imagePathOut = sprintf($this->_getMigrationConfigs('imagePath').
            '%2$s%1$s%3$s%1$s',
            DIRECTORY_SEPARATOR,
            $loteFolder,
            $this->_txDigital
        );
        return $this;
    }

    /**
     * convert tiff em pdf
     *
     * @param array $arrTif lista de tif a ser trasformado em um unico pdf
     * @throws Exception
     */
    private function _convertTifToPdf(array $arrTif)
    {
        $tmpDir     = $this->_getMigrationConfigs('tmpDir');
        $tmpName    = $this->_generateTmpFilename();
        $tmpNameTIF = $tmpDir . $tmpName . $this->_extension[self::ARQUIVO_TIF];
        $tmpNamePDF = $tmpDir . $tmpName . $this->_extension[self::ARQUIVO_PDF];

        $this->_arrTmpFile[] = $tmpNamePDF;

        $cmdTif     = 'tiffcp -c lzw ' . implode(' ', $arrTif) . ' ' .  $tmpNameTIF;
        $cmdPdf     = 'tiff2pdf ' . $tmpNameTIF . ' -o ' . $tmpNamePDF;
        self::_debug("Convertendo varios TIFs em um unico TIF");
        $execTif = shell_exec($cmdTif);
        if ($execTif) {
            self::_debug("Não foi possível converter os arquivos TIF em TIF multi-page. [{$execTif}]");
            throw new \Exception("Não foi possível converter os arquivos TIF em TIF multi-page. [{$execTif}]");
        }
        self::_debug("Convertido com sucesso");

        self::_debug("Convertendo TIF em PDF");

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpNameTIF);
        finfo_close($finfo);

        if ($mimeType != 'image/tiff') {
            self::_debug("Erro de mimeType do arquivo {$tmpNameTIF} [{$mimeType}]");
            throw new \Exception("Erro de mimeType do arquivo {$tmpNameTIF} [{$mimeType}]");
        }

        $execPdf = shell_exec($cmdPdf);
        if ($execPdf) {
            self::_debug("Não foi possível converter os arquivos TIF multi-page em PDF. [{$execPdf}]");
            throw new \Exception("Não foi possível converter os arquivos TIF multi-page em PDF. [{$execPdf}]");
        }
        self::_debug("Convertido com sucesso");

        unlink($tmpNameTIF);
        $this->_arrPdf[] = $tmpNamePDF;
    }

    /**
     * gera nome de arquivo temporário
     *
     * @return string
     */
    private function _generateTmpFilename()
    {
        return $this->_nuDigital . '_tmp_' .rand(1, 10000);
    }

    /**
     * Persiste grau de acesso ao artefato
     *
     * @param ArtefatoEntity $entity
     * @param integer $sqGrauAcesso
     * @return \Migracao\Service\MigrationImage
     */
    private function _grauAcesso(ArtefatoEntity $entity, $sqGrauAcesso)
    {
        //fecha a conexão com banco
        $this->_closeConnection();

        self::_debug("Setando grau de acesso ao artefato");

        $entGrauAcesso = $this->_getRepository('app:GrauAcesso')->find($sqGrauAcesso);
        // realizando a persistencia do Grau de Acesso
        $this->getServiceLocator()->getService('Dossie')->persistGrauAcessoArtefato($entity, $entGrauAcesso);
        return $this;
    }

    /**
     * Processa o merge dos pdfs da fila (_arrPdf) quando há mais de um pdf ou apenas faz copy se existe apenas 1 pdf. Atribui
     * os metadados ao PDF final
     *
     * @param ArtefatoEntity $entArtefato
     * @return \Migracao\Service\MigrationImage
     * @throws \Exception
     */
    private function _processMergePdf(ArtefatoEntity $entArtefato)
    {
        //fecha a conexão com banco
        $this->_closeConnection();


        $tmpDir              = $this->_getMigrationConfigs('tmpDir');
        $fullOutputName      = $tmpDir . md5($this->_txDigital) . $this->_extension[self::ARQUIVO_PDF];
        $this->_arrTmpFile[] = $fullOutputName.'_original';
        $subject             = sprintf($this->_getMigrationConfigs('subject'), $this->_txDigital);
        $title               = sprintf($this->_getMigrationConfigs('title'), $this->_txDigital );

        //merge PDF
        $cmd = '';


        //se for só um arquivo e esse é temporário ele já esta no diretorio correto
        if (count($this->_arrPdf) === 1 && !preg_match('/\d_tmp_/', basename($this->_arrPdf[0]))) {
            self::_debug('Iniciando a cópia do PDF (só possui 1 arquivo de imagem)');

            $pdfFile = $this->_arrPdf[0];
            $cmd .= "cp {$pdfFile} {$tmpDir} ";
            $cmd .= " && ";
            $cmd .= "mv {$tmpDir}" . basename($pdfFile) . ' ' . $fullOutputName;

            $this->_arrTmpFile[] = $fullOutputName;

            $msgError = "Não foi possível copiar o PDF para o novo sistema";
        }else{
            self::_debug('Iniciando o merge de PDF');

//            $cmd .= "pdftk " . implode(' ', $this->_arrPdf) . " cat output {$fullOutputName} ";
            $cmd .= "gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile={$fullOutputName} " . implode(' ', $this->_arrPdf);

            $msgError = "Não foi possível executar o Merge dos PDF";
        }

        //set metadata
        $cmdExiftool = "exiftool -Title=\"{$title}\" -Author=\"{$this->_getMergePdfConfigs('author')}\" -Subject=\"{$subject}\" {$fullOutputName}";

        $fCmd = $cmd . ' && ' . $cmdExiftool;
        $result = shell_exec($fCmd);

        if ($result && !file_exists($fullOutputName)) {
            self::_debug("{$msgError} [{$result}]");
            throw new \Exception("{$msgError} [{$result}]");
        }

        self::_debug("Arquivo(s) copiado/mergeado");

        $this->_doInsertImagem($entArtefato, $fullOutputName);

        return $this;
    }

    /**
     * Insere a imagem processada no sistema novo
     *
     * @param ArtefatoEntity $entArtefato
     * @param string $fullFilename
     */
    private function _doInsertImagem(ArtefatoEntity $entArtefato, $fullFilename)
    {
        //fecha a conexão com banco
        $this->_closeConnection();

        self::_debug("Iniciando a insersão da imagem no banco de dados");

        self::_debug("Gerando hash do arquivo: {$fullFilename}");

        $hash = hash_file(ArtefatoImagemService::HASH_ALGORITHM, $fullFilename);

        self::_debug("Hash gerado: {$hash}");

        $newFilename = md5($hash);

        self::_debug("Nome do Arquivo: {$newFilename}");

        $sqOcorrencia         = \Core_Configuration::getSgdoceSqOcorrenciaIncluirImagem();
        $artefatoImagemEntity = $this->_newEntity('app:ArtefatoImagem');
        $entHistoricoArtefato = $this->_newEntity('app:HistoricoArtefato');
        $entVwPessoa          = NULL;
        $entVwUnidadeOrg      = NULL;

        if ($this->_sqPessoa) {
            $entVwPessoa      = $this->getEntityManager()->getPartialReference('app:VwPessoa'    , $this->_sqPessoa);
        }
        if ($this->_sqUnidadeOrg) {
            $entVwUnidadeOrg  = $this->getEntityManager()->getPartialReference('app:VwUnidadeOrg', $this->_sqUnidadeOrg);
        }

        $entOocorrencia       = $this->getEntityManager()->getPartialReference('app:Ocorrencia'  , $sqOcorrencia);
        $objZendDate          = \Zend_Date::now();
        $strMessage           = $this->getServiceLocator()
                                     ->getService('HistoricoArtefato')
                                     ->getMessage('MH021',$objZendDate->get(\Zend_Date::DATETIME_MEDIUM));

        //ARTEFATO_IMAGEM
        $artefatoImagemEntity->setSqArtefato( $entArtefato )
                             ->setNuBytes(filesize($fullFilename))
                             ->setNuQtdePaginas($this->_pageCount)
                             ->setDtOperacao($objZendDate)
                             ->setTxHash($hash)
                             ->setNoArquivo($newFilename)
                             ->setSqPessoa($entVwPessoa)
                             ->setStAtivo(TRUE)
                             ->setSqUnidadeOrg($entVwUnidadeOrg);

        $this->getEntityManager()->persist($artefatoImagemEntity);
        $this->getEntityManager()->flush($artefatoImagemEntity);

        //HISTORICO_ARTEFATO
        $entHistoricoArtefato->setSqOcorrencia($entOocorrencia)
                             ->setSqUnidadeOrg($entVwUnidadeOrg)
                             ->setSqPessoa($entVwPessoa)
                             ->setDtOcorrencia($objZendDate)
                             ->setTxDescricaoOperacao($strMessage)
                             ->setSqArtefato($entArtefato);

        $this->getEntityManager()->persist($entHistoricoArtefato);
        $this->getEntityManager()->flush($entHistoricoArtefato);
        self::_debug("Insersão da imagem no banco de dados e historico concluído");


        self::_debug("Copiando PDF para diretório final");
        #MOVENDO ARQUIVO PARA DIRETORIO DE DESTINO...
        $mufService         = $this->getServiceLocator()->getService('MoveFileUpload');
        $filenameTemporary  = pathinfo($fullFilename,PATHINFO_BASENAME);

        self::_debug("[{$filenameTemporary}] {$fullFilename}");

        $destination = $this->_getDestinationPath($artefatoImagemEntity);

        self::_debug("copiando para {$destination}");

        $mufService->setDestinationPath($destination);

        $newFilenamePDF = $newFilename . $this->_extension[self::ARQUIVO_PDF];

        $mufService->move($filenameTemporary, $newFilenamePDF);

        self::_debug("{$newFilenamePDF} copiado com sucesso");


        self::_debug("Cópia finalizada");
    }

    /**
     * monta o path de destino da imagem processada
     *
     * @param \Sgdoce\Model\Entity\ArtefatoImagem $artefatoImagemEntity
     * @return string
     */
    private function _getDestinationPath (ArtefatoImagemEntity $artefatoImagemEntity)
    {
        $etiquetaNupSiorgEntity = $artefatoImagemEntity->getSqArtefato()->getNuDigital();
        $loteFolder             = (string) $etiquetaNupSiorgEntity->getSqLoteEtiqueta()->getSqLoteEtiqueta();
        $digitalFolder          = (string) $etiquetaNupSiorgEntity->getNuEtiqueta();
        return sprintf(
            'upload%1$simagem%1$s%2$s%1$s%3$s%1$s',
            DIRECTORY_SEPARATOR,
            $loteFolder,
            $digitalFolder
        );
    }

    /**
     * Recupera dados do último tramite para setar as propriedades _sqPessoa e _sqUnidadeOrg
     * utilizadas para persistir a imagem
     *
     * @param ArtefatoEntity $entArtefato
     * @return \Migracao\Service\MigrationImage
     * @throws \Exception
     */
    private function _fillLastTramiteInformation(ArtefatoEntity $entArtefato)
    {
        $entTramite = $this->_getRepository('app:VwUltimoTramiteArtefato')->find($entArtefato->getSqArtefato());

        $sqUnidadeOrg = TRUE;
        $sqPessoa     = TRUE;

        if (! $entTramite) {
            $sqUnidadeOrg = FALSE;
            $sqPessoa     = FALSE;
        } else {
            //determina a unidade

            if(! $entTramite->getSqPessoaDestino() || ! $entTramite->getSqPessoaDestino()->getSqPessoa()){
                $sqUnidadeOrg = FALSE;
            }

            //determina a pessoa
            if(! $entTramite->getSqPessoaRecebimento() || ! $entTramite->getSqPessoaRecebimento()->getSqPessoa()){
                $sqPessoa = FALSE;
            }
        }


        $this->_sqPessoa     = (! $sqPessoa    ) ? NULL : $entTramite->getSqPessoaRecebimento()->getSqPessoa();
        $this->_sqUnidadeOrg = (! $sqUnidadeOrg) ? NULL : $entTramite->getSqPessoaDestino()->getSqPessoa();

        return $this;
    }

    /**
     * Envia email com a compilação dos erros gerados durente o ciclo de migração
     *
     * @param array $arrError
     * @return self
     */
    private function _sendMailError(array $arrError, $step, $pid)
    {
        $arrConfigs = $this->_getMigrationConfigs('mail');

        $enviroment = '';

        if (APPLICATION_ENV != 'production') {
            $enviroment = '[' . APPLICATION_ENV . ']';
        }

        $subject  = sprintf($arrConfigs['subject'], $pid.'::'.$step, $enviroment);
        $template = 'migration_image_error.phtml';

        $arrTo = array();
        foreach ($arrConfigs['to'] as $value) {
            $arrTo['para'][$value['name']] = $value['email'];
        }

        $arguments = array(
            'erros'   => $arrError,
            'imgLogo' => ArtefatoImagemService::PATH_IMAGE_LOGO,
        );

        $SgdoceMail = new \Sgdoce_Mail();
        $SgdoceMail->prepareBodyHtml($template, $arguments);
        $SgdoceMail->setRecipients($arrTo);
        $SgdoceMail->setSubject($subject);
        $SgdoceMail->send();

        return $this;
    }

    /**
     * Envia email a quem solicitou a migração informando o sucesso ou erro no processamento
     *
     * @param SolicitacaoMigracaoImagemEntity $ent
     * @param boolean $error
     * @return void
     */
    private function _sendMailMigrationPdf(SolicitacaoMigracaoImagemEntity $ent, $error = FALSE)
    {

        self::_debug('Notificando solicitante');

        $arrConfigs = $this->_getMigrationConfigs('mail');

        $subject  = $arrConfigs['subjectSuccess'];
        $template = 'migration_pdf_success.phtml';
        if ($error) {
            $subject = $arrConfigs['subjectError'];
            $template = 'migration_pdf_error.phtml';
        }

        $txEmailDestinatario = $ent->getTxEmail();

        $arguments = array('entSolicitacao'  => $ent,
                           'qtdeTentativa'   => (integer) $this->_getMigrationConfigs('qtdeTentativa'),
                           'dtProcessamento' => \Zend_Date::now(),
                           'imgLogo'         => ArtefatoImagemService::PATH_IMAGE_LOGO,
        );
        $SgdoceMail = new \Sgdoce_Mail();
        $SgdoceMail->prepareBodyHtml($template, $arguments);
        $SgdoceMail->setRecipients(array('para' => array($ent->getSqPessoa()->getNoPessoa() => $txEmailDestinatario)));
        $SgdoceMail->setSubject($subject);
        $SgdoceMail->send();

        return;
    }

    /**
     * Envia email a quem solicitou a migração informando o sucesso ou erro no processamento
     *
     * @param SolicitacaoMigracaoImagemEntity $ent
     * @param boolean $error
     * @return void
     */
    private function _sendMailMigrationPdfNotFound(SolicitacaoMigracaoImagemEntity $ent, $corrupted = FALSE)
    {
        $arrConfigs = $this->_getMigrationConfigs('mail');

        $subject  = $arrConfigs['subjectSuccess'];
        $template = 'migration_pdf_not_found.phtml';

        if ($corrupted) {
            $subject  = $arrConfigs['subjectError'];
            $template = 'migration_pdf_image_corrupted.phtml';
        }

        $txEmailDestinatario = $ent->getTxEmail();

        $arguments = array('entSolicitacao'  => $ent,
                           'dtProcessamento' => \Zend_Date::now(),
                           'imgLogo'         => ArtefatoImagemService::PATH_IMAGE_LOGO,
        );
        $SgdoceMail = new \Sgdoce_Mail();
        $SgdoceMail->prepareBodyHtml($template, $arguments);
        $SgdoceMail->setRecipients(array('para' => array($ent->getSqPessoa()->getNoPessoa() => $txEmailDestinatario)));
        $SgdoceMail->setSubject($subject);
        $SgdoceMail->send();

        return;
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

        $arrConfigs = $this->_getMigrationConfigs('mail');

        $enviroment = '';

        if (APPLICATION_ENV != 'production') {
            $enviroment = '[' . APPLICATION_ENV . ']';
        }

        $type = ($start) ? 'Start':'Stop';

        $subject  = "{$type}::{$pid} {$processType} {$enviroment}";
        $template = 'process.phtml';

        $arrTo = array();
        foreach ($arrConfigs['to'] as $value) {
            $arrTo['para'][$value['name']] = $value['email'];
        }

        $arguments = array(
            'totalRegistroProcessar'=> $this->_totalRegistroProcessar,
            'withoutProcess'=> $withoutProcess,
            'objZDProcess'  => $zd,
            'processType'   => $processType,
            'type'          => $type,
            'arrSucesso'    => $arrSuccess,
            'imgLogo'       => ArtefatoImagemService::PATH_IMAGE_LOGO,
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
        $arrConfigs = $this->_getMigrationConfigs('mail');
        return $arrConfigs['sendStatus'];
    }

    private static function _debug($msg, $tab = TRUE, $label = \Robot\Debug::INFO)
    {
        $strTab = ($tab) ? chr(9):'';

        if (self::$_debug) {
            \Robot\Debug::log("ARTEFATO[" . self::$_sqArtefato . "] {$strTab} {$msg}", $label, self::$_stepCurrent);
        }
    }
}