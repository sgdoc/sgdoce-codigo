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
 * Helper Abstract de Action para Download de Arquivos
 *
 * @package    Core
 * @subpackage Controller
 * @subpackage Action
 * @subpackage Helper
 * @subpackage Download
 * @name       Abstract
 * @category   Controller
 * @version     1.0.0
 * @since       2012-07-09
 */
abstract class Core_Controller_Action_Helper_Download_Abstract extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Método responsável em efetuar o donwload do arquivo (Strategy Partners)
     * @param string $fileName Nome do arquivo
     * @param array $options   Array de opções para download
     */
    public function download($fileName, array $options = array())
    {
        $this->_prepare($fileName, $options);
        Zend_Controller_Action_HelperBroker::getStaticHelper('layout')->disableLayout();
        Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(TRUE);
        $response = $this->getResponse();

        $response->clearAllHeaders();

        $response->setHeader('Content-Description', 'File Transfer')
                        ->setHeader('Content-Type','application/force-download')
                        ->setHeader('Content-Type','application/octet-stream')
                        ->setHeader('Content-Type','application/download')
                        ->setHeader('Content-Disposition', 'attachment;Filename="' . $fileName . '"')
                        ->setHeader('Content-Transfer-Encoding', 'binary')
                        ->setHeader('Expires', 0)
                        ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                        ->setHeader('Pragma', 'public')
                        ->setHeader('Content-Length', $this->_getFileSize($fileName, $options))
                        ->setBody($this->_getContentFile($fileName, $options));

        $response->sendResponse();
    }

    /**
     * Executa helper quando chamado como $this->_helper->download() no action da controller
     *
     * Proxies to {@link simple()}
     *
     * @param string $fileName Nome do arquivo
     * @param array $options   Array de opções para download
     */
    public function direct($fileName, array $options = array())
    {
        $this->download($fileName, $options);
    }

    /**
     * Retorna o tamanho do arquivo
     * @param string $fileName Nome do arquivo
     * @param array $options   Array de opções para download
     */
    protected abstract function _getFileSize($fileName, array $options = array());

    /**
     * Retorna o conteúdo arquivo
     * @param string $fileName Nome do arquivo
     * @param array $options   Array de opções para download
     */
    protected abstract function _getContentFile($fileName, array $options = array());

    /**
     *
     * @param string $fileName Nome do arquivo
     * @param array $options   Array de opções para download
     */
    protected abstract function _prepare($fileName, array $options = array());
}