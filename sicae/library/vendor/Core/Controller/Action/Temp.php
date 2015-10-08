<?php
/*
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
 * Base para as Controllers do framework que implementem CRUD
 *
 * @package    Core
 * @subpackage Controller
 * @subpackage Action
 * @name       Temp
 * @category   Controller
 */
class Core_Controller_Action_Temp extends Core_Controller_Action_CrudDto
{
    public $_temp;

    public function init()
    {
        $this->_setTemp();
        parent::init();
    }

    protected function _setTemp()
    {
        $this->_temp = $this->getService('Temp');
        $container   = new Zend_Session_Namespace('Temp');
        $this->_temp->setContainer($container);
        return $this;
    }

    public function downloadTempAction()
    {
        $params   = $this->_getAllParams();
        $registry = \Zend_Registry::get('configs');
        $options  = array('path' => $registry['folder'][$this->_folder]);
        $file     = $this->_temp->get($params['id'])->getDeCaminhoArquivo();

        $this->_helper->download($file, $options);
    }

    public function deleteTempAction()
    {
        $id = $this->_getParam('id');
        $this->_temp->delete($id);
        $this->_helper->json(NULL);
    }

    public function uploadTempAction()
    {
        $params     = $this->_getAllParams();
        $registry   = \Zend_Registry::get('configs');
        $result     = 'true';
        $data       = array('destination' => $registry['folder'][$this->_folder],
                            'validateMimeType' => FALSE);
        $dtoUpload  = Core_Dto::factoryFromData($data, 'Core_Dto_Mapping_Upload', array(1 => 'validateMimeType'));

        $data       = array('de_caminho_arquivo' => null);
        $dtoArquivo = Core_Dto::factoryFromData($data, 'entity', array('entity'=> $this->_entityTemp));

        $options    = $dtoUpload->toArray();
        $upload     = new \Core_Upload('Http', FALSE, $options);
        $fileName   = $upload->upload();

        $dtoArquivo->setdeCaminhoArquivo($fileName);

        $this->_temp->add($dtoArquivo);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->_response->setBody($result);
    }

}