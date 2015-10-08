<?php
/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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

class Sica_Controller_Action extends Core_Controller_Action_CrudDto
{
    protected $_entity;

    protected $_codeMessageToggleActive;

    protected $_codeMessageToggleInactive;

    protected $_pdf;

    public function init()
    {
        $this->_entity = $this->getService('Usuario')->userEntity();
    }

    public function toggleStatusAction()
    {
        $dto = new Core_Dto_Mapping(
            $this->_getAllParams(),
            array('status', 'id')
        );

        $currentStatus = $this->_getParam('status', 0);
        $status        = $this->getService()->toggleStatus($dto);

        $message = $this->_codeMessageToggleActive;
        if ($currentStatus) {
            $message = $this->_codeMessageToggleInactive;
        }

        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            if ($status) {
                $this->_helper->parseJson()->sendJson($message);
            }
        }

        exit($message);
    }

    public function gerarPdfAction()
    {
        $data = $this->getDataPdf();

        $registry = \Zend_Registry::get('configs');
        $options  = array('path' => $registry['folder']['pdf']);

        $file = $this->_pdfName;

        $controller = $this->getFrontController()->getRequest()->getControllerName();
        \Core_Doc_Factory::setFilePath(APPLICATION_PATH . '/layouts/pdf/' . $controller);
        \Core_Doc_Factory::write('pdf', array('data' => $data), $options['path'], $file);
        $this->_helper->download($file, $options);
    }

    public function getDataPdf()
    {
        trigger_error('Método deve ser implementado');
    }
}

