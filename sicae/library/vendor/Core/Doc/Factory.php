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
 * Factory para geração de documentos em diversos
 * formatos com base em uma massa de dados e um
 * template
 *
 * @package      Core
 * @subpackage   Doc
 * @name         Factory
 * @category     Factory
 */
abstract class Core_Doc_Factory
{
    private static $adapter         = null;
    private static $docData         = null;
    private static $docTemplate     = null;
    private static $docContents     = null;
    private static $filePath         = null;
    private static $fileName         = null;

    /**
     * Define qual adapter deve ser utilizado
     *
     * @param string $adapter - shortname do adapter
     * @throws Exception
     */
    public static function setAdapter($adapter = 'Pdf')
    {
        if (!$adapter) {
            throw new Exception('No adapter set!');
        }

        try {
            $class = 'Core_Doc_Adapter_'.$adapter;
            self::$adapter = new $class;
        } catch (Exception $e) {
            throw new Exception('Defined adapter doesn\'t exist!!!');
        }
    }

    /**
     * Obtém a instância do adapter atualmente definido
     *
     * @return Core_Doc_Adapter_Abstract
     */
    public static function getAdapter()
    {
        if (!self::$adapter) {
            self::setAdapter();
        }

        return self::$adapter;
    }

    /**
     * Define o nome do arquivo
     * @param string $fileName
     * @throws Exception
     */
    public static function setFileName($fileName)
    {
        if (!$fileName && !self::$fileName) {
            throw new Exception('No fileName set!');
        }
        self::$fileName = $fileName;
    }

    public static function getFileName()
    {
        if (!self::$fileName) {
            throw new Exception('No fileName set');
        }

        return self::$fileName;
    }

    public static function setFilePath($filePath)
    {
        if (!$filePath && !self::$filePath) {
            throw new Exception('No path set!');
        }
        self::$filePath = $filePath;
    }

    public static function getFilePath()
    {
        if (!self::$filePath) {
            throw new Exception('No path set');
        }

        return self::$filePath;
    }

    public static function setDocTemplate($docTemplate)
    {
        if (!$docTemplate && !self::$docTemplate) {
            throw new Exception('No template set!');
        }
        self::$docTemplate = $docTemplate;
    }

    public static function getDocTemplate()
    {
        if (!self::$docTemplate) {
            throw new Exception('No adapter set');
        }
        self::$docTemplate;
    }

    public static function setDocData($docData)
    {
        if (!$docData && !self::$docData) {
            throw new Exception('No data set!');
        }
        self::$docData = $docData;
    }

    public static function getDocData()
    {
        if (!self::$docData) {
            throw new Exception('No adapter set');
        }
        return self::$docData;
    }

    public static function create($docTemplate = null, array $docData = array(), $adapter = 'Pdf')
    {
    	ini_set('max_execution_time', 0);

        self::getAdapter($adapter);
        self::setDocData($docData);
        self::setDocTemplate($docTemplate);

        return self::$docContents = self::getAdapter()->docGen($docTemplate, $docData, self::getFilePath());
    }

    public static function write($docTemplate = null, array $docData = array(), $filePath = null, $fileName = null, $adapter = 'Pdf')
    {

        self::create($docTemplate, $docData, $adapter);

        self::setFilePath($filePath);
        self::setFileName($fileName);

        $put = $filePath.DIRECTORY_SEPARATOR.$fileName;

        file_put_contents($put, self::$docContents);

        return $put;
    }

    public static function download($docTemplate = null, array $docData = array(), $fileName = null, $adapter = 'Pdf')
    {
        self::setFileName($fileName);

        self::create($docTemplate, $docData, $adapter);

        Zend_Controller_Front::getInstance()
            ->getResponse()
            ->clearAllHeaders()
            ->setHeader('Content-Type', 'application/ocstream')
            ->setHeader('Content-Disposition', 'attachment; fileName="'.self::getFileName().'"')
            ->setBody(self::$docContents)
            ->sendResponse();

        exit();
    }
}