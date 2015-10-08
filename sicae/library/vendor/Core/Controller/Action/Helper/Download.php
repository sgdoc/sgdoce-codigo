<?php
/**
 * Copyright 2012 do ICMBio
 *
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
/**
 * Helper de Action para Download de Arquivos
 *
 * @package    Core
 * @subpackage Controller
 * @subpackage Action
 * @subpackage Helper
 * @name       Download
 * @category   Controller
 * @version    1.0.0
 * @since      2012-07-09
 */
class Core_Controller_Action_Helper_Download extends Core_Controller_Action_Helper_Download_Abstract
{

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_Helper_Download_Abstract::_getFileSize()
     */
    protected function _getFileSize($fileName, array $options = array())
    {
        $this->_checkOptions($options);
        return filesize($this->_getFileAddress($fileName, $options));
    }
    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_Helper_Download_Abstract::_getContentFile()
     */
    protected function _getContentFile($fileName, array $options = array())
    {
        $this->_checkOptions($options);
        return file_get_contents($this->_getFileAddress($fileName, $options));
    }
    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_Helper_Download_Abstract::_prepare()
     */
    protected function _prepare($fileName, array $options = array())
    {
        $this->_checkOptions($options);
        $address = $this->_getFileAddress($fileName, $options);

        if (!is_writable($address) && !is_writable($options['path'])) {
            throw new RuntimeException('Arquivo não pode ser lido');
        }

        if (!$fp = fopen($address,'r')) {
            throw new RuntimeException('Arquivo Inexistente');
        }
        fclose($fp);
    }
    /**
     * Verifica se path do arquivo foi setado.
     * @param array $options
     * @throws RuntimeException
     */
    protected function _checkOptions($options)
    {
        if (!isset($options['path'])) {
            throw new RuntimeException('Necessário passar o path do arquivo array de opções');
        }
    }
    /**
     * Retorna o path do arquivo solicitado para o download
     * @param string $fileName Nome do arquivo
     * @param array $options   Array de opções para download
     * @return string
     */
    protected function _getFileAddress($fileName, $options)
    {
        return rtrim($options['path'],'/') .'/'. $fileName;
    }
}