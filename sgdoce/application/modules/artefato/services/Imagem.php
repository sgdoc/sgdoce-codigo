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

/*
 * Incluindo a Classe WideImage
 */

use Doctrine\Common\Util\Debug;

//include_once "../library/wideimage/lib/WideImage.php";

/**
 * Classe para Service de Imagens
 *
 * @package  Artefato
 * @category Service
 * @name     Imagem
 * @version  1.0.0
 */
class Imagem extends \Core_ServiceLayer_Service_CrudDto
{

    /**
     * Metdo responsavel por persistir os uploads
     * @param type $destino
     * @param type $thumb
     * @param type $validFile
     * @param type $invalidFile
     * @return type
     */
    public static function upload ($destino = 'anexoArtefato', $thumb = FALSE, $validFile = TRUE, $invalidFile = TRUE, $validImageSize = TRUE)
    {
        $configs = \Core_Registry::get('configs');
        $upload = new \Zend_File_Transfer_Adapter_Http();

        $files = $upload->getFileInfo();
        $filesUp = array();
        $return = array();
        $error = false;
        $pasta = 'anexo-material';

        if ($destino == 'anexoArtefato') {
            $pasta = 'anexo-artefato';
        }

        $path = current(explode('application', __DiR__))
                . 'data' . DIRECTORY_SEPARATOR
                . 'upload' . DIRECTORY_SEPARATOR
                . $pasta . DIRECTORY_SEPARATOR;

        foreach ($files as $file => $info) {
            $upload = new \Zend_File_Transfer_Adapter_Http();
            $upload->setDestination($path);
            $upload->setValidators(array());

            $upload->addValidator('Size', TRUE, array('max' => '100MB',
                'messages' => "O tamanho do arquivo é superior ao permitido. O tamanho permitido é 100MB."));

            self::_invalidFile($invalidFile, $upload);

            self::_validFile($validFile, $upload);

            self::_validImageSize($validImageSize, $upload);

            self::_getValidator($upload);

            if ($upload->isValid($file)) {
                $fileinfo = pathinfo($info['name']);
                $upload->receive($file);
                $filesUp[] = $upload->getFileName($file);

                $return[] = array('name' => $upload->getFileName($file),
                    'size' => $upload->getFileSize($file));
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
        if ($thumb) {
            $pasta = current(explode('application', __DiR__))
                    . 'data' . DIRECTORY_SEPARATOR
                    . 'upload' . DIRECTORY_SEPARATOR
                    . 'thumbs' . DIRECTORY_SEPARATOR;

            foreach ($filesUp as $endereco) {
                $fileinfo = pathinfo($endereco);
                $image = \WideImage::load($endereco);
                $image->resize(300, 300, 'outside')
                        ->crop('50% - 150', '50% - 150', 300, 300)
                        ->saveToFile($pasta . $fileinfo['filename'] . '_300_X_300.' .
                                strtolower($fileinfo['extension']));

                $image->resize(133, 89, 'outside')
                        ->crop('50% - 67', '50% - 45', 133, 89)
                        ->saveToFile($pasta . $fileinfo['filename'] . '_133_X_89.' .
                                strtolower($fileinfo['extension']));
            }
        }

        return $return;
    }

    protected static function _invalidFile ($invalidFile, &$upload)
    {
        if ($invalidFile) {
            $upload->addValidator('ExcludeExtension', TRUE, array('dll', 'msi', 'phtml', 'phar', 'pyc', 'py', 'jar', 'bat', 'com', 'exe', 'pif', 'bin', 'sh', 'pl', 'php') +
                    array('messages' => "Extensão do arquivo inválida. Selecione arquivos no formato .PNG."));
        }

        return TRUE;
    }

    protected static function _validFile ($validFile, &$upload)
    {
        if ($validFile) {
            $upload->addValidator('Extension', TRUE, array('png') +
                    array('messages' => "Extensão do arquivo inválida. Selecione arquivos no formato .PNG."));
        }

        return TRUE;
    }

    protected static function _validImageSize ($validImageSize, &$upload)
    {
        if ($validImageSize) {
            $upload->addValidator('ImageSize', TRUE, array('maxwidth' => 300) +
                    array('messages' => "A dimensão da imagem é maior que o limite permitido de 300x300px."));

            $upload->addValidator('ImageSize', TRUE, array('maxheight' => 300) +
                    array('messages' => "A dimensão da imagem é maior que o limite permitido de 300x300px."));
        }

        return TRUE;
    }

    protected static function _getValidator (&$upload)
    {
        $upload->getValidator('Upload')
                ->setMessages(array('fileUploadErrorIniSize' => "O tamanho máximo permitido para upload é '100MB'",
                    'fileUploadErrorFormSize' => "O tamanho máximo permitido para upload é '100MB'",
                    'fileUploadErrorNoFile' => "Arquivo não selecionado."));

        return TRUE;
    }

    /**
     *
     * @param type $dto
     * @param type $endereco
     */
    public static function showImage ($dto, $endereco)
    {
        $endereco = explode("/", $endereco);
        $nameImage = array_reverse($endereco);
        $pasta = current(explode('application', __DiR__))
                . 'data' . DIRECTORY_SEPARATOR
                . 'upload' . DIRECTORY_SEPARATOR
                . 'anexo-artefato' . DIRECTORY_SEPARATOR;

        $fileinfo = pathinfo($pasta . $nameImage[0]);

        $image = \WideImage::loadFromFile($pasta . $nameImage[0]);

        if ($dto->getResize()) {
            $image = $image->resize($dto->getWidth(), $dto->getHeight(), 'outside');
        }

        if ($dto->getCrop()) {
            $image = $image->resize($dto->getWidth(), $dto->getHeight(), 'outside')
                    ->crop('50% - ' . ceil($dto->getWidth() / 2), '50% - ' . ceil($dto->getHeight() / 2), $dto->getWidth(), $dto->getHeight());
        }

        unset($dto);
        unset($pasta);

        $image->output(strtolower($fileinfo['extension']));
        $image->destroy();
    }

    /**
     *
     * @param array $arrExtension
     */
    public function setInvalidExtension (array $arrExtension)
    {
        $this->_invalidExtension = $arrExtension;
    }

    /**
     *
     * @param array $arrExtension
     */
    public function getInvalidExtension (array $arrExtension)
    {
        return $this->_invalidExtension;
    }

    /**
     *
     * @param array $arrExtension
     */
    public function setValidExtension (array $arrExtension)
    {
        $this->_validExtension = $arrExtension;
    }

    /**
     *
     * @param array $arrExtension
     */
    public function getValidExtension (array $arrExtension)
    {
        return $this->_validExtension;
    }

}
