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

namespace Auxiliar\Service;

/**
 * Classe para Service de Upload
 *
 * @package  Auxiliar
 * @category Service
 * @name     Upload
 * @version  1.0.0
 */
class MoveFileUpload extends \Core_ServiceLayer_Service_CrudDto
{

    private $_pathDestination = null;

    public function getOriginPath ()
    {
        return current(explode('application', __DIR__))
            . 'data'   . DIRECTORY_SEPARATOR
            . $this->getTemporaryPath();
    }

    public function getTemporaryPath ()
    {
        return 'upload' 
            . DIRECTORY_SEPARATOR 
            . 'tmp'
            . DIRECTORY_SEPARATOR;
    }

    public function getDestinationPath ($fileName = null)
    {
        return $this->_pathDestination . $this->_clearFileName($fileName);
    }

    /**
     * Seta o path de DESTINO dos arquivos a partir do diretorio DATA da aplicaçao
     *
     * @param string $pathDestination
     * @return \Auxiliar\Service\Upload
     */
    public function setDestinationPath ($pathDestination)
    {
        $path = current(explode('application', __DIR__)) . 'data' . DIRECTORY_SEPARATOR;
        $this->_pathDestination = $path . trim($pathDestination, '/') . DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * @todo: Temos um problema semantico aqui...
     *        Sugestão:
     *        Renomear para um nome mais coerente.
     *        Motivo:
     *        Move com o rename() ou copia com o copy()
     */
    public function move ($fileName, $newFileName = null)
    {
        $fileNameClean = $this->_clearFileName($fileName);
        $origem = $this->getOriginPath() . $fileNameClean;
        $destination = $this->_pathDestination . $fileNameClean;

        if (is_null($this->_pathDestination)) {
            throw new \Core_Exception_ServiceLayer("Caminho de destino não informado.");
        }
        if (!file_exists($origem)) {
            throw new \Core_Exception_ServiceLayer("Arquivo não encontrado na origem.");
        }
        if (!file_exists($this->_pathDestination)) {
            mkdir($this->_pathDestination, 0775, true);
        }
        if (file_exists($destination)) {
            throw new \Core_Exception_ServiceLayer("Arquivo já existe no destino.");
        }
        if ($newFileName) {
            return rename($origem, $this->_pathDestination . $this->_clearFileName($newFileName));
        } else {
            return copy($origem, $destination);
        }
    }

    public function unlinkOrigemFile ($fileName)
    {
        $path = $this->getOriginPath() . $this->_clearFileName($fileName);
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    public function unlinkDestinationFile ($fileName)
    {
        $path = $this->_pathDestination . $this->_clearFileName($fileName);
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    private function _clearFileName ($fileName)
    {
        return preg_replace('/[^\w\._]+/', '_', ltrim($fileName, '/'));
    }

    
}