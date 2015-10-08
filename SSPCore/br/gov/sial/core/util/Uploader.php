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
namespace br\gov\sial\core\util;
use br\gov\sial\core\lang\TFile;
use br\gov\sial\core\SIALAbstract;
use br\gov\sial\core\exception\IOException;

/**
 * @package br.gov.sial.core
 * @subpackage util
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Uploader extends SIALAbstract
{
    const T_UPLOADER_INVALID_TYPE        = 'O arquivo informado não é suportado';
    const T_UPLOADER_INVALID_TARGET      = 'O destino informado é inválido';
    const T_UPLOADER_NO_READ_PERMISSION  = 'O sem permissão de leitura';
    const T_UPLOADER_NO_WRITE_PERMISSION = 'Não é possível gravar no diretório informado';
    const T_UPLOADER_CANT_OPEN_DIR       = 'Não é possível obter acesso ao diretório informado';
    const T_UPLOADER_CANT_COPY_FILE      = 'Não foi possível copiar o arquivo: %s';

    /**
     * tipos de arquivos aceito como anexo
     *
     * @var string[]
     */
    private $_allowTypes;

    /**
     * local de armazenado dos arquivos
     *
     * @var string
     * */
    private $_storagePath;

    /**
     * @var boolean
     * */
    private $_strictType;

    /**
     * O parâmetro $storagePath define o local de armazenamento do arquivo.
     * O parâmetro $allowTypes consiste num conjunto de strings, cada uma
     * representando uma extensão de arquivo, que será usado para definir
     * quais os tipos de arquivos será aceitos. Opcionamente o tipo de dados
     * poderá ser realizada verificando o mimetype, do contrário apenas a
     * extensão será avaliada.
     *
     * @param string $storagePath
     * @param string[] $allowTypes
     * @param boolean $strictType
     * */
    public function __construct
    (
        /* string */ $storagePath,
        array $allowTypes = array(),
        /* boolean */ $strictType = FALSE
    )
    {
        self::_isValidStorage($storagePath);
        $this->_storagePath = rtrim($storagePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->_allowTypes  = $allowTypes;
        $this->_strictType  = (boolean) $strictType;
    }

    /**
     * salva o arquivo informado no storagePath, opcionalmente um
     * novo nome poderá ser informado ao arquivo
     *
     * @param  TFile  $tTile
     * @param  string $alias
     * @return boolean
     * @example
     */
    public function save (TFile $tFile, $alias = NULL)
    {
        IOException::throwsExceptionIfParamIsNull(
            self::_isValidaType($this, $tFile),
            self::T_UPLOADER_INVALID_TYPE
        );

        $filename = $tFile->getsource();

        $target   = $this->_storagePath;
        $target  .= $alias ? $alias : $tFile->getName();

        return is_uploaded_file($filename)
               ? move_uploaded_file($filename, $target)
               : copy($filename, $target)
               ;
    }

    /**
     * @param string $targetPath
     * @throws IOException
     * */
    public function copyAllTo ($targetPath)
    {
        self::_manipuleFile($targetPath, 'copy');
    }

    /**
     * @param string $targetPath
     * @throws IOException
     * */
    public function moveAllTo ($targetPath)
    {
        self::_manipuleFile($targetPath, 'rename');
    }

    /**
     * @param string $targetPath
     * @throws IOException
     * */
    public function _manipuleFile ($targetPath, $operator)
    {
        $targetPath = rtrim($targetPath, DIRECTORY_SEPARATOR)
                    . DIRECTORY_SEPARATOR;

        self::_isValidStorage($targetPath);

        IOException::throwsExceptionIfParamIsNull(
            is_writeable($targetPath), self::T_UPLOADER_NO_WRITE_PERMISSION
        );

        foreach (self::_listContent($this->_storagePath) as $elm) {
            $file = explode(DIRECTORY_SEPARATOR, $elm);
            $file = end($file);

            IOException::throwsExceptionIfParamIsNull(
                $operator($elm, $targetPath . $file),
                sprintf(self::T_UPLOADER_CANT_COPY_FILE, $file)
            );
        };
    }

    /**
     * @return string[]
     * @throws IOException
     */
    private static function _listContent ($targetPath)
    {
        IOException::throwsExceptionIfParamIsNull(
            is_readable($targetPath), self::T_UPLOADER_NO_READ_PERMISSION
        );

        $handler = opendir($targetPath);

        IOException::throwsExceptionIfParamIsNull(
            $handler, self::T_UPLOADER_CANT_OPEN_DIR
        );

        $list = array();

        while (FALSE !== ($elm = readdir($handler))) {

            $current = $targetPath . $elm;

            if ('.' == $elm || '..' == $elm || is_dir($current)) {
                continue;
            }

            $list[] = $current;
        }

        closedir($handler);

        return $list;
    }

    /**
     * verifica se o tipo do arquivo informado é aceito
     *
     * @param  Uploader $self
     * @param  TFile $tFile
     * @return boolean
     */
    private static function _isValidaType (Uploader $self, TFile $tFile)
    {
        foreach ($self->_allowTypes as $type) {
            if ($tFile->isType($type, $self->_strictType)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * verifica se o local para armazenamento dos arquivos é valido,
     * defini-se por válido:
     *   - a existência prévia da pasta
     *   - permimissão de leitura
     *   - permissão de escrita
     *
     * @param  string $fullpath
     * @throws IOException
     */
    private static function _isValidStorage ($fullpath)
    {
        IOException::throwsExceptionIfParamIsNull(
            is_dir($fullpath),
            self::T_UPLOADER_INVALID_TARGET
        );
    }
}