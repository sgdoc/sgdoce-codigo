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
use br\gov\sial\core\SIALAbstract;
use br\gov\sial\core\exception\IOException;

/**
 * SIAL Downloader
 *
 * <p>
 *   Esta classe encapula as funcionalidades necessarias para um sistema fornecer
 *   conteudo sem informar o local real de seu armazenamento.
 * </p>
 *
 * <strong>NOTA:</strong> O uso desta implica na manipulacao do cabecalho (header) e
 * por tanto é necessário tomar as precauções necessárias antes de usá-la.
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @name Downloader
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Downloader extends SIALAbstract
{
    /**
     * @var integer
     * */
    const T_DOWNLOADER_CODE_FILE_NOT_FOUND = 0xD0001;

    /**
     * @var integer
     * */
    const T_DOWNLOADER_CODE_FILE_UNSUPPORTED = 0xD0002;

    /**
     * @var string
     * */
    const T_DOWNLOADER_STR_FILE_NOT_FOUND = 'O arquivo solicitado não esta disponível';

    /**
     * @var string
     * */
    const T_DOWNLOADER_STR_FILE_UNSUPPORTED = 'Arquivo não suportado';

    /**
     * @var string[]
     * */
    private $_mime = array();

    /**
     * @var string
     * */
    private $_storePath;

    /**
     * @var callback
     * */
    private $_before = NULL;

    /**
     * @var callback
     * */
    private $_after = NULL;

    /**
     * @param string[] $storePath
     * @param string[] $mime
     *
     * @code
     * <?php
     *   # local de armazenamento
     *   $storePath = array('fullpath_1', 'fullpath_2', 'fullpath_n', );
     *
     *   # ext = mime
     *   $mime = array(
     *     'doc'  => 'application/msword',
     *     'pdf'  => 'application/pdf'   ,
     *     'ogg'  => 'audio/ogg'         ,
     *     'mp4'  => 'video/mp4'         ,
     *     'mpg'  => 'video/mpeg'        ,
     *     'mpeg' => 'video/mpeg'        ,
     *     'flv'  => 'video/x-flv'       ,
     *   );
     *
     *  $download = new Download($storePath, $mime)
     *
     * ?>
     * @endcode
     * */
    public function __construct ($storePath, array $mime)
    {
        $this->addPath($storePath);
        $this->_mime = $mime;

        # inicializa o callback para evitar verificacao de existencia posterior
        $this->_before = function () {};
        $this->_after  = function () {};
    }

    /**
     * callback executado antes de iniciar o download
     *
     * @param \Closure
     * @return Downloader
     * */
    public function before (\Closure $callback) {
        $this->_before = $callback;
        return $this;
    }

    /**
     * callback executado depois do download
     *
     * @param \Closure
     * @return Downloader
     * */
    public function after (\Closure $callback) {
        $this->_after = $callback;
        return $this;
    }

    /**
     * Adiciona path
     *
     * @param string|string[] $path
     * */
    public function addPath ($path)
    {
        $paths = (array) $path;

        foreach ($paths as $path) {

            if (DIRECTORY_SEPARATOR != substr($path, -1)) {
                $path .= DIRECTORY_SEPARATOR;
            }

            $this->_storePath[] = $path;
        }
    }

    /**
     * verifica se o $filename passado existe
     *
     * @return boolean
     * */
    public function has ($filename)
    {
        return (boolean) $this->__fullpath($filename);
    }

    /**
     * verifica se o $filename passado existe fisicamente nos paths
     *
     * @param string $filename
     * @return string|null
     * */
    private final function __fullpath ($filename)
    {
        foreach ($this->_storePath as $path) {

            $fullpath = $path . $filename;

            if (is_file($fullpath)) {
                return $fullpath;
            }
        }

        return null;
    }

    /**
     * Envia para o browser o download do conteúdo do arquivo
     *
     * @param string $filename
     * @throws IOException
     * */
    public function content ($filename)
    {
        if (!$this->has($filename)) {
            throw new IOException(self::T_DOWNLOADER_STR_FILE_NOT_FOUND, self::T_DOWNLOADER_CODE_FILE_NOT_FOUND);
        }

        $this->_before->__invoke();

        $filename = $this->__fullpath($filename);
        $this->__header($filename)
             ->__read($filename);

        $this->_after->__invoke();
    }

    /**
     * @param string $filename
     * @return string
     * */
    public function contentRaw ($filename)
    {
        if (!$this->has($filename)) {
            throw new IOException(self::T_DOWNLOADER_STR_FILE_NOT_FOUND, self::T_DOWNLOADER_CODE_FILE_NOT_FOUND);
        }

        $this->_before->__invoke();

        return file_get_contents($this->__fullpath($filename));
    }

    /**
     * Envia o arquivo para o header
     *
     * @param string $filename
     * @return Downloader
     * @throws IOException
     * */
    private final function __header ($filename)
    {
        $info = pathinfo($filename);
        $file = $info['basename'];
        $exte = $info['extension'];
        $mime = $this->_mime[$exte];

        if (!isset($this->_mime[$exte])) {
            throw new IOException(self::T_DOWNLOADER_STR_FILE_NOT_FOUND, self::T_DOWNLOADER_CODE_FILE_NOT_FOUND);
        }

        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=3600, pre-check=18600");
        header("Cache-Control: public", FALSE);
        header("Content-Description: File Transfer");
        header("Content-type: " . $this->_mime[$info['extension']]);
        header("Accept-Ranges: bytes");
        header("Content-Disposition: attachment; filename=\"{$file}\";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($filename));

        return $this;
    }

    /**
     * lê o arquivo
     * <strong>NOTA:</strong> Este metodo produz saida!
     *
     * @author http://mobiforge.com/developing/story/content-delivery-mobile-devices
     * @param string $file
     * */
    private final function __read ($file)
    {
        $hfp     = fopen($file, 'rb');
        $size   = filesize($file); // File size
        $length = $size;           // Content length
        $start  = 0;               // Start byte
        $end    = $size - 1;       // End byte

        // Now that we've gotten so far without errors we send the accept range header
        /* At the moment we only support single ranges.
         * Multiple ranges requires some more work to ensure it works correctly
         * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
         *
         * Multirange support annouces itself with:
         * header('Accept-Ranges: bytes');
         *
         * Multirange content must be sent with multipart/byteranges mediatype,
         * (mediatype = mimetype)
         * as well as a boundry header to indicate the various chunks of data.
         */
        header("Accept-Ranges: 0-$length");

        // header('Accept-Ranges: bytes');
        // multipart/byteranges
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
        if (isset($_SERVER['HTTP_RANGE'])) {

            $c_start = $start;
            $c_end   = $end;
            // Extract the range string
            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);

            // Make sure the client hasn't sent us a multibyte range
            if (strpos($range, ',') !== false) {

                // (?) Shoud this be issued here, or should the first
                // range be used? Or should the header be ignored and
                // we output the whole content?
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                // (?) Echo some info to the client?
                exit;
            }

            // If the range starts with an '-' we start from the beginning
            // If not, we forward the file pointer
            // And make sure to get the end byte if spesified
            if ($range0 == '-') {

                // The n-number of the last bytes is requested
                $c_start = $size - substr($range, 1);
            } else {

                $range  = explode('-', $range);
                $c_start = $range[0];
                $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
            }

            /* Check the range and make sure it's treated according to the specs.
             * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
             */
            // End bytes can not be larger than $end.
            $c_end = ($c_end > $end) ? $end : $c_end;
            // Validate the requested range and return an error if it's not correct.
            if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {

                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                // (?) Echo some info to the client?
                exit;
            }
            $start  = $c_start;
            $end    = $c_end;
            $length = $end - $start + 1; // Calculate new content length
            fseek($hfp, $start);
            header('HTTP/1.1 206 Partial Content');
        }

        // Notify the client the byte range we'll be outputting
        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: $length");

        // Start buffered download
        $buffer = 1024 * 8;
        while(!feof($hfp) && ($p = ftell($hfp)) <= $end) {

            if ($p + $buffer > $end) {

                // In case we're only outputtin a chunk, make sure we don't
                // read past the length
                $buffer = $end - $p + 1;
            }

            set_time_limit(0); // Reset time limit for big files
            echo fread($hfp, $buffer);
            flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
        }

        fclose($hfp);
    }

    /**
     * fábrica de objetos
     *
     * @param string[] $storePath
     * @param string[] $mime
     * @return Downloader
     * */
    public static function factory ($storePath, array $mime)
    {
        return new self((array) $storePath, $mime);
    }
}