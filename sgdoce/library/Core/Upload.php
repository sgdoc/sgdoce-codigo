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
 * @package     Core
 * @name        Upload
 * @category    Upload
 * @version     1.0.0
 * @since       2012-07-09
 */
class Core_Upload extends Zend_File_Transfer
{
    /**
     * Arquivos enviado para o servidor
     * @var string
     */
    protected $_files;
    /**
     * False means Download, true means upload
     * @var boolean
     */
    protected $_direction;
    /**
     * Nome do arquivo para upload
     * @var string
     */
    protected $_fileName;
    /**
     * Array de opções para o upload
     * validMimeType     - tipos de mimeType válidos para o upload
     * destination       - caminho de destino do arquivo no servidor
     * validateMimeType  - (boolean) TRUE valida mimetypes , FALSE não valida
     * excludeExtension  - Extensões não permitidas
     * @var array
     */
    protected $_optionsConfig = array(
        'validMimeType' => array(
              'application/vnd.ms-office',                 //doc
              'application/msword',                        //doc
              'application/excel',                         //xls
              'application/octet-stream',                  //ods
              'application/vnd.oasis.opendocument.text',   //odt
              'application/pdf',                           //pdf
              'image/gif',                                 //gif
              'image/jpeg',                                //jpg
              'image/png',                                 //png
              'text/plain',                                //txt
         ),
        'excludeExtension' => array('sql','php','exe','bat','sh','java','jar','bin','rar','zip'
                                    ,'vdi','msi','ini'),
        'destination'      =>  NULL,
        'validateMimeType' => TRUE,
        'rename'           => FALSE
    );
    /**
     * Construtor da classe de Upload
     * @param string $adapter
     * @param boolean $direction
     * @param array $options
     */
    public function __construct($adapter = 'Http', $direction = FALSE, $options = array())
    {
        parent::__construct($adapter, $direction, $options);
        $this->_direction = (integer)$direction;
        if (NULL !== $options) {
            $this->setConfig($options);
        }

        $this->getAdapter()->addValidator('MimeType', TRUE, array());
    }
    /**
     * Seta as configurações no array de configuração.
     * @param array $options
     * @return Core_Upload
     */
    public function setConfig(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . $key;
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else if (method_exists($this->getAdapter(), $method)) {
                $this->getAdapter()->$method($value);
            } else {
                $this->_optionsConfig[$key] = $value;
            }
        }

        return $this;
    }
    /**
     * Retorna os array de configuração (path , validações , etc...)
     * @return array:
     */
    public function getConfig()
    {
        return $this->_optionsConfig;
    }

    /**
     * Método responsável por fazer o upload do Arquivo enviado para servidor.
     * @throws RuntimeException
     * @return string Nome do arquivo.
     */
    public function upload()
    {
        $this->_configUpload();

        if ($this->_optionsConfig['validateMimeType']) {
            $this->getAdapter()->getValidator('MimeType')->setMimeType(
                $this->_optionsConfig['validMimeType']
            );
        } else {
             $this->getAdapter()->removeValidator('MimeType');
        }

        $this->_validUpload();
        if (!$this->getAdapter()->receive()) {
            $this->_errorMessageDispatch();
        }
        return $this->_fileName;
    }

    /**
     * Método responsável por setar as validações e o nome do arquivo.
     */
    protected function _configUpload()
    {
        $this->_files    = $this->getAdapter()->getFileInfo();

        $filename        = pathinfo($this->getAdapter()->getFileName());

        if ($this->_optionsConfig['rename']) {
            $this->_fileName = $this->_optionsConfig['rename']
                             . '.'
                             . strtolower($filename['extension']);
        } else {
            $this->_fileName = date('YmdHis')
                             . '_'
                             . str_replace(' ', '_', mb_convert_encoding($filename['filename'], "UTF-8"))
                             . '.'
                             . strtolower($filename['extension']);
        }


        $this->getAdapter()
             ->getValidator('Upload')
             ->setMessages(array(
                 'fileUploadErrorIniSize'  =>
                     "O tamanho máximo permitido para upload é '25MB'",
                 'fileUploadErrorFormSize' =>
                     "O tamanho máximo permitido para upload é '25MB'",
                 'fileUploadErrorNoFile' =>
                     'Arquivo é obrigatório'
        ));

        if (!$this->getAdapter()->hasValidator('Size')) {
            $this->getAdapter()
                 ->addValidator('Size', TRUE, array(
                                         'max'      => '25MB',
                                         'messages' => "O tamanho máximo permitido para o arquivo '%value%' é '%max%'"
            ));
        }

        if (!$this->getAdapter()->hasValidator('ExcludeExtension')) {
              $this->getAdapter()
                  ->addValidator('ExcludeExtension',
                                  TRUE,
                                  $this->_optionsConfig['excludeExtension']
                                  + array('messages' => "Tipo de arquivo inválido"));
        }

        if (!$this->getAdapter()->hasFilter('Rename')) {
            $this->getAdapter()->addFilter('Rename', $this->_fileName);
        }

    }

    /**
     * Método responsável por executar as validaçoes isValid e isUploaded
     */
    protected function _validUpload()
    {
        if (!$this->getAdapter()->isValid()) {
            $this->_errorMessageDispatch();
        }

        if (!$this->getAdapter()->isUploaded()) {
           $this->_errorMessageDispatch();
        }
    }

    /**
     * (non-PHPdoc)
     * @see Zend_File_Transfer::getAdapter()
     */
    public function getAdapter($direction = NULL)
    {
        if ($direction === NULL) {
            $direction = $this->_direction;
        }
        return parent::getAdapter($direction);
    }

    /**
     * Adiciona novos tipos de MimeType para  o array de mimeTypes validos.
     * @param array $mimeType
     * @return Core_Upload
     */
    public function addValidMimeType($mimeType)
    {
        $this->_optionsConfig['validMimeType'][] = $mimeType;
        return $this;
    }

    /**
     * Sobrescreve array de mimeTypes com o tipos especificos enviados no parametro.
     * @param array $mimeType
     * @return Core_Upload
     */
    public function setValidMimeType(array $mimeType)
    {
        $this->_optionsConfig['validMimeType'] = array();
        $this->_optionsConfig['validMimeType'] = $mimeType;
        return $this;
    }

    /**
     * Adiciona novos tipos de MimeType para  o array de mimeTypes validos.
     * @param array $mimeType
     * @return Core_Upload
     */
    public function addExcludeExtension($excludeExtension)
    {
        $this->_optionsConfig['excludeExtension'][] = $excludeExtension;
        return $this;
    }

    /**
     * Sobrescreve array de mimeTypes com o tipos especificos enviados no parametro.
     * @param array $mimeType
     * @return Core_Upload
     */
    public function setExcludeExtension(array $excludeExtension)
    {
        $this->_optionsConfig['excludeExtension'] = array();
        $this->_optionsConfig['excludeExtension'] = $excludeExtension;
        return $this;
    }

    /**
     * Trata a mensageira para retorno ao Service.
     * @throws \Core_Exception
     */
    protected function _errorMessageDispatch()
    {
        $messageError = $this->getAdapter()->getMessages();
        $mm = Core_Messaging_Manager::getGateway('User');
        foreach ($messageError as $error => $msg) {
            $mm->addErrorMessage($msg);
        }

        throw new \Core_Upload_Exception();
    }

}