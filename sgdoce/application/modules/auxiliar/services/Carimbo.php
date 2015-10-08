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
use Doctrine\Common\Util\Debug;

/**
 * Classe para Service de Carimbo
 *
 * @category Service
 * @package  Auxiliar
 * @name   Carimbo
 * @version  1.0.0
  */
class Carimbo extends \Core_ServiceLayer_Service_Temp
{
    /**
     * @var string
     */
    protected $_entityName = 'app:Carimbo';

    /**
     * Configura uma entidade para inserir no banco de dados
     * @param string $entityName nome da entidade
     */
    public function setOperationalEntity($entityName = NULL)
    {
        if (empty($this->_data['sqTipoArtefato'])) {
            $msg = \Core_Registry::getMessage()->translate('MN003');
            $msg = str_replace('<campo>', 'Tipo de Artefato', $msg);
            throw new \Core_Exception_ServiceLayer($msg);
        }
        $this->_data['sqTipoArtefato'] = $this->_createEntityManaged(array(
            'sqTipoArtefato' => $this->_data['sqTipoArtefato']
            ), 'app:TipoArtefato');
        $this->_data['stRegistroAtivo'] = TRUE;
        $this->_data['inEditavel'] = TRUE;
    }

    /**
     * Método para fazer o preenchimento da grid
     * @param  array $params Parâmetros da requisição
     * @return array         Retorna um array
     */
    public function listGrid(\Core_Dto_Search $params)
    {
        $result = $this->_getRepository()->searchPageDto('pesquisaCarimbo', $params);

        return $result;
    }

    /**
     * Cria items para combo de carimbo
     * @return array Retorna array no formato esperado para a criação do combo
     */
    public function listItems()
    {
        return $this->_getRepository()->listItems();
    }

    /**
     * Faz a filtragem de dados e upload do arquivo
     * @param  array $data dados do formulário
     * @return array       dados tratados pelo método
     */
    public function filterSave($data)
    {
        $fileName = $this->upload($data);
        $data['deCaminhoArquivo'] = $fileName;
        return $data;
    }

    /**
     * Obtém o Adapater requerido para o File Transter
     * @return object Adapter
     */
    protected function getFileTransferAdapter()
    {
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        return $adapter;
    }

    //http://stackoverflow.com/questions/5495275/how-to-check-if-an-image-has-transparency-using-gd
    /**
     * Verifica se um arquivo é um png transparente
     * @param  string  $fileName Caminho do nome do arquivo
     * @return boolean           Retorna true se o carminho é u m png transparente, false no caso de não ser.
     */
    public function isAlphaPng($fileName)
    {
        if (!in_array(ord(file_get_contents($fileName, NULL, NULL, 25, 1)),array(4,6)))
        {
            throw new \Core_Exception_ServiceLayer("MN112");
        }
        return TRUE;
    }

    /**
     * Verifica se o arquivo encontra-se dentro das dimensões esperadas
     * @param  string  $filename Caminho do arquivo a ser verificado
     * @return boolean           retorna verdadeiro no caso de tamanho válido ou lança exceção
     */
    public function isValidSize($filename)
    {
        $imgSize =  getimagesize($filename);
        if ($imgSize[0] > 300 || $imgSize[1] > 300) {
            throw new \Core_Exception_ServiceLayer("MN078");
        }
        return TRUE;
    }

    /**
     * Wrapper para a função file_exists do PHP (necessário para a execução dos testes)
     * @param  string $path Caminho do arquivo
     * @return boolean       Arquivo Existe?
     */
    protected function fileExists($path)
    {
        return file_exists($path);
    }

    /**
     * Método que retorna o nome final do arquivo a ser salvo
     * @param  string $filename Nome do arquivo
     * @return string           Nome do arquivo alterado pelo sistema
     */
    public function getUploadedFilename($filename)
    {
        $registry = \Core_Registry::get('configs');
        $path     = $registry['upload']['folderCarimbo'];
        $newFileName = $path
                 . date('Ymdhis')
                 . "_"
                 . $filename;
        return $newFileName;
    }

    /**
     * Faz o upload do arquivo
     * @param  array $params Parâmetros http
     * @return string         Retorna o basename do arquivo enviado
     */
    public function upload($params)
    {
        $registry = \Core_Registry::get('configs');
        $maxSize     = $registry['upload']['carimboMaxSize'];

        $adapter = $this->getFileTransferAdapter();

        $adapter->addValidator('Extension', FALSE, 'png')
                ->addValidator('Size', TRUE, array('max'=>$maxSize));

        $errorCodes = array();
        $errorCodes['fileExtensionFalse'] = str_replace
                                        ( '<extensão>','.png',\Core_Registry::getMessage()->translate('MN076'));
        $errorCodes['fileSizeTooBig'] = str_replace
                                        ('<tamanho>', $maxSize, \Core_Registry::getMessage()->translate('MN077'));

        $uploadErrors = array();
        if (!$adapter->isValid()) {
            $errors = $adapter->getErrors();
            foreach ($errors as $error) {
                if (isset($errorCodes[$error]) ) {
                   $uploadErrors[] = $errorCodes[$error];
                }
            }
            if (empty($uploadErrors))
            {
                $msg= 'Erro na operação';
            }
            else
            {
                $msg = implode('<br />', $uploadErrors);
            }
            throw new \Core_Exception_ServiceLayer($msg);
        }

        $info = $adapter->getFileInfo();
        $this->isAlphaPng($info['deCaminhoArquivo']['tmp_name']);
        $this->isValidSize($info['deCaminhoArquivo']['tmp_name']);

	    $path = current(explode ('application', __DiR__))
              . 'data'    . DIRECTORY_SEPARATOR
              . 'upload'  . DIRECTORY_SEPARATOR
              . 'carimbo' . DIRECTORY_SEPARATOR;

        if (!$this->fileExists($path)) {
            throw new \Core_Exception_ServiceLayer("MN111");
        }

        if (!is_writable($path)) {
            throw new \Core_Exception_ServiceLayer("MN110");
        }
	
        // pegando os dados do documento
        $currentFile = $info['deCaminhoArquivo']['name'];
        $newFile = $this->getUploadedFilename($info['deCaminhoArquivo']['name']);

        $data    = array('de_caminho_arquivo' => date('Ymdhis') . "_" . $info['deCaminhoArquivo']['name']);
        $newFile = $path.$data['de_caminho_arquivo'];
        $adapter->addFilter(
            'Rename',
            array('source' => $info['deCaminhoArquivo']['tmp_name'],
                  'target' => $newFile,
                  'overwrite' => TRUE)
        );

        // adicionando no temp
        $this->addTemp($data);

        // colocando o arquivo na pasta
        $adapter->receive($currentFile);

        if (!$this->fileExists($newFile)) {
            throw new \Core_Exception_ServiceLayer("MN109");
        }

        return basename($newFile);
    }
}
