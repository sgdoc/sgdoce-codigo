<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */

namespace br\gov\sial\core\util\file;
use br\gov\sial\core\lang\TFile,
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\lang\TFileContent,
    br\gov\sial\core\valueObject\ValueObjectAbstract;

/**
 * SIAL
 *
 * Camada de Negocio do Mordomo que trata arquivos
 *
 * @package br.gov.sial.core.util
 * @subpackage file
 * @name James
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class James extends SIALAbstract
{
    /**
     * @var br\gov\sial\core\util\file\File
     * */
    private static $_instance = NULL;

    /**
     * @var ValueObjectAbstract
     */
    private static $_voFile;

    /**
     * Hash do arquivo a ser salvo
     * @var string
     */
    private static $_hashFile  = NULL;

    /**
     * Caminho do Arquivo root
     * @var mixed
     */
    private static $_pathDwnld = NULL;

    /**
     * construtor
     * @param \br\gov\sial\core\valueObject\ValueObjectAbstract $voFile
     */
    public function __construct (ValueObjectAbstract $voFile)
    {
        self::$_voFile = $voFile;
        if (self::$_voFile instanceof TFile) {
            if ((0 == self::$_voFile->getSize()) && (NULL == self::$_voFile->getType())){
                self::$_pathDwnld = $this->_decryptPath(base64_decode(self::$_voFile->getSource()));
                self::$_hashFile = basename(self::$_pathDwnld,'.xml');
            } else {
                self::$_hashFile = hash_file('md5', self::$_voFile->getSource());
            }
        }
    }

    /**
     * Efetua a persistencia de arquivos
     * @return ValueObjectAbstract
     * @todo transformar para sistemas genericos do SIAL, nao somente do SIAL
     */
    public function filePersist ()
    {
        $source    = end(explode(self::NAMESPACE_SEPARATOR, self::$_voFile->getClassName()));
        $dstPath   = base64_decode(Folder::factory()->createFolderStructure($source));
        $finalPath = self::_saveRootTamburete($dstPath);

        if (NULL != self::$_hashFile) {
            $rootHash = basename(self::_getRootTamburete($dstPath),'.xml');
            $src = $dstPath . DIRECTORY_SEPARATOR . $rootHash . DIRECTORY_SEPARATOR . self::$_hashFile;
            self::$_voFile->setSource(base64_encode($this->_cryptPath($src)));
            self::$_voFile->setName(self::$_hashFile);
        }

        return self::$_voFile;
    }

    /**
     * Obtenho o caminho do Root
     * @param string $path
     * @return string
     */
    private static function _getRootTamburete ($path)
    {
        $rootPath = dirname(dirname($path));
        return $rootPath . DIRECTORY_SEPARATOR . md5(self::_cryptPath($rootPath) . 'root') . '.xml';
    }

    /**
     * Obtenho o Caminho do Log
     * @param string $path
     * @return string
     */
    private static function _getLogTamburete ($path)
    {
        $logPath = dirname(dirname($path));
        return $logPath . DIRECTORY_SEPARATOR . md5(self::_cryptPath($logPath) . 'log') . '.xml';
    }

    /**
     * Salva o Root do Tamburete e Caso tenha log salva o mesmo e leaf
     * @param string $path
     * @return string
     */
    private static function _saveRootTamburete ($path)
    {
        $rootPath = self::_getRootTamburete($path);
        $logPath  = self::_getLogTamburete($path);

        $hashLeaf = NULL != self::$_hashFile ? self::$_hashFile : NULL;
        $tambu = new Tamburete(self::$_voFile, $path, $hashLeaf);

        if (is_file($rootPath)) {
            $result = $tambu->update(file_get_contents($rootPath));
        } else {
            $result = $tambu->save();
        }

        # Salvo o XML root
        file_put_contents($rootPath, $result->root);

        # Verifico se existe o Log para ser salvo
        if (NULL != $result->log) {
            file_put_contents($logPath, $result->log);
        }

        if (NULL != $result->leaf) {
            self::_saveLeafTamburete($path, $result->leaf);
        }
        return $rootPath;
    }

    /**
     * Persiste a ponta do Tamburete
     * @param string $path
     * @param mixed $content
     */
    private static function _saveLeafTamburete ($path, $content)
    {
        $leafPath = $path . DIRECTORY_SEPARATOR . self::$_hashFile . '.xml';
        file_put_contents($leafPath, $content);
    }

    /**
     * Retorna o path para efetuar o download do arquivo <br> OBS.: Esta só poderá ser chamada pela Helper
     * @return TFileContent
     */
    public function fileDownloadRecover ()
    {
        $arrPath  = explode('/',self::$_pathDwnld);
        $leafHash = array_pop($arrPath);
        $rootHash = array_pop($arrPath);

        $fileDown = implode('/',$arrPath) . DIRECTORY_SEPARATOR . $leafHash . '.xml';
        $leafTamb = file_get_contents($fileDown);

        $return = simplexml_load_string(utf8_encode($leafTamb), NULL, LIBXML_NOCDATA);

        $voContent = new TFileContent();
        $voContent->setContent(base64_decode($return->content->__toString()));
        $voContent->setName($return->name->__toString());
        $voContent->setSize($return->size->__toString());
        $voContent->setType($return->type->__toString());

        return $voContent;
    }

    /**
     * Criptografa a String com o caminho
     * @param String $dstPath
     * @return String
     */
    private function _cryptPath ($dstPath)
    {
        $arrPath     = array_filter(explode('/', $dstPath));
        $arrPthSize  = sizeof($arrPath);
        $arrCrypto   = array();
        $strPath     = '';
        if ((boolean) ($arrPthSize % 2)) {
            $arrPthSize++;
            array_push($arrPath, '_XX_');
        }

        for($int = 1; $int <= $arrPthSize; $int++) {
            if ($int <= $arrPthSize/2) {
                $arrCrypto[] = $arrPath[(($arrPthSize/2)+1) - $int];
            } else {
                $strPath .= $arrPath[$int] . '__HP__';
            }
        }
        return '__PH__' . implode('__PH__', $arrCrypto) . strrev(str_replace('_XX___HP__', '', $strPath));
    }

    /**
     * Descriptografa a string referente ao caminho
     * @param String $dstPath
     * @return String
     */
    private function _decryptPath ($dstPath)
    {
        $arrPath     = array_filter(explode('__PH__', $dstPath));
        $arrPthSize  = sizeof($arrPath);
        $arrCrypto   = array();
        $strPath     = '';
        if ((boolean) ($arrPthSize % 2)) {
            $arrPthSize++;
            array_push($arrPath, '_XX_');
        }

        for($int = 1; $int <= $arrPthSize; $int++) {
            if ($int <= $arrPthSize/2) {
                $arrCrypto[] = $arrPath[(($arrPthSize/2)+1) - $int];
            } else {
                $strPath .= $arrPath[$int] . '/';
            }
        }
        return '/' . implode('/', $arrCrypto) . strrev(str_replace('_XX_/', '', $strPath));
    }

    /**
     * @param ValueObjectAbstract $voFile
     * @return James
     * */
    public function factory (ValueObjectAbstract $voFile)
    {
        if (NULL == self::$_instance) {
            self::$_instance = new self($voFile);
        }
        return self::$_instance;
    }
}