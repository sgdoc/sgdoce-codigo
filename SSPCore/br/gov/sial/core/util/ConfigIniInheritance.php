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
//use br\gov\sial\core\util\ConfigAbstract;
//use br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class ConfigIniInheritance extends SIALAbstract
{
    /**
     * @var string
     * */
    const T_CONFIG_INI_HEADER_MSG_INHERITANCE = <<<FILEHEADER
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;; ATENÇÃO: Este arquivo é criado dinamicamente!!!                ;;
;; Qualquer alteração realizada neste arquivo poderá ser perdida. ;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
FILEHEADER;

    /**
     * @var string
     * */
    const T_CONFIG_INI_HEADER_MSG_INHERITANCE_CONTENT_CURRENT = "\n\n;; Conteudo original de <%s>\n";

    /**
     * @var string
     * */
    const T_CONFIG_INI_HEADER_MSG_INHERITANCE_CONTENT_INHERITS_FROM = "\n;; Conteudo herdado de <%s>\n";

    /**
     * @var string
     * */
    const T_CONFIG_INI_PATTERN_DEPENDENCE_HAS = '/^;+\s*<depends>\s*[^\n]+$/mi';

    /**
     * @var string
     * */
    const T_CONFIG_INI_PATTERN_DEPENDENCE_GET = '/(?:[;\s]+<depends>\s*(?P<depends>[^\n]+)\n)+/mi';

    /**
     * @var string
     * */
    const T_CONFIG_INI_PATTERN_DEPENDENCE_SECTION = '/^\[(?<completeDef>(?P<section>\w+)(?:[\w\s\:]*)*)\]/ims';

    /**
     * @var string
     * */
    const T_CONFIG_INI_PATTERN_DEPENDENCE_SECTION_CONTENT = '/\[%s(\s*:\s*\w+)?\]\n(?P<content>([^\[]|\[[^\w]){1,})/m';

    /**
     * @var string
     * */
    const T_CONFIG_INI_PATTERN_INHERITANCE_REMOVE_COMMENTS = '/^;([^\n]*)$/m';

    /**
     * @var string
     * */
    const T_CONFIG_INI_PATTERN_INHERITANCE_REMOVE_EMTPY_LINES = '/(^([\r\n]*)$)/m';


    private static $_configFilenameExtension = 'ini';

    private static $_extendsFilenameSeparator = '-x-';

    private static $_solded = array();

    /**
     * pilha de execucao
     *
     * @var Stack
     * */
    private $_stackExec;

    /**
     * @var string[]
     */
    private static $_depends = NULL;

    /**
     * Dado um arquivo ini qualquer, retorna o nome do arquivo com
     * todo o conteudo herado
     *
     * @param string
     * @return stirng
     */
    public static function getInitFilename ($filename)
    {
        if (! self::hasDependence($filename)) {
            return $filename;
        }

        $listDeps = self::solveDependence($filename);
        $target   = self::targetNameFromFileWithDeps($listDeps);
        $basedir  = self::createTargetDirectory(dirname($filename));

        $cached = self::targetInflate(
            $listDeps,
            $basedir,
            $target
        );

        return $cached;
    }

    /**
     *
     * @param string[]
     * @param string
     * @param string
     * @return string
     */
    public static function targetInflate (array $files, $directory, $filename)
    {

        $target = $directory. DIRECTORY_SEPARATOR . $filename;

        if (! self::canCreate($files, $target)) {
            return $target;
        }

        $workFile = end($files);
        $baseContent = file_get_contents($workFile);

        $baseContent = preg_replace(self::T_CONFIG_INI_PATTERN_INHERITANCE_REMOVE_COMMENTS, NULL, $baseContent);
        $baseContent = preg_replace(self::T_CONFIG_INI_PATTERN_INHERITANCE_REMOVE_EMTPY_LINES, NULL, $baseContent);

        $baseSection = self::getSections($baseContent, TRUE);
        $workContent = self::getSectionsData($baseContent, $baseSection['section']);

        $finalContent = array();

        foreach ($files as $fileKey => $file) {

            $depContent = file_get_contents($file);

            $depContent = preg_replace(self::T_CONFIG_INI_PATTERN_INHERITANCE_REMOVE_COMMENTS, NULL, $depContent);
            $depContent = preg_replace(self::T_CONFIG_INI_PATTERN_INHERITANCE_REMOVE_EMTPY_LINES, NULL, $depContent);

            $depSection = self::getSections($depContent, TRUE);
            $toWorkContent = self::getSectionsData($depContent, $depSection['section']);

            foreach ($baseSection['section'] as $sectionKey => $section) {
                $superKey = $baseSection['completeDef'][$sectionKey];

                if (! isset($finalContent[$superKey])) {
                    $finalContent[$superKey] = sprintf('%2$s[%1$s]%2$s',$superKey, PHP_EOL);
                }

                if (array_key_exists($section, $toWorkContent)) {
                    $finalContent[$superKey] .= sprintf(
                        $workFile == $file ?
                            self::T_CONFIG_INI_HEADER_MSG_INHERITANCE_CONTENT_CURRENT : self::T_CONFIG_INI_HEADER_MSG_INHERITANCE_CONTENT_INHERITS_FROM,
                        $fileKey
                    );
                    $finalContent[$superKey] .= $toWorkContent[$section] . PHP_EOL;
                }
            }
        }

        $content = self::T_CONFIG_INI_HEADER_MSG_INHERITANCE . PHP_EOL;
        $content .= implode(PHP_EOL, $finalContent);

        IOException::throwsExceptionIfParamIsNull(
            file_put_contents( $target, preg_replace(self::T_CONFIG_INI_PATTERN_INHERITANCE_REMOVE_EMTPY_LINES, NULL, $content) ),
            sprintf('Não foi possível inflar o conteudo de %s', $target)
        );

        return $target;
    }

    public static final function canCreate ($files, $target)
    {
        clearstatcache();

        if (! is_file($target)) {
            return TRUE;
        }

        $dtLastModifiedTarget = filemtime($target);

        foreach ($files as $file) {

            if (filemtime($file) >= $dtLastModifiedTarget) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * responsavel por resolver as dependencias do arquivo informado
     *
     * @param string
     * @return string[]
     * */
    public static function solveDependence ($filename)
    {
        $pathinfo = pathinfo($filename);

        # verifica se o arquivo em questão possui suas proprias dependencias
        if (self::hasDependence($filename)) {

            $content = file_get_contents($filename);

            foreach (self::getDependence($content) as $depence) {

                $depFilename = self::converteDependence($depence, $pathinfo['dirname']);

                if (self::hasDependence($depFilename) && !isset(self::$_depends[$depence])) {
                    self::solveDependence($depFilename);
                }

                self::$_depends[$depence] = $depFilename;
            }
        }

        /* inseri o nome do arquivo que deu origem a depencia */
        self::$_depends[$pathinfo['filename']] = $filename;

        return self::$_depends;
    }

    /**
     * @param string
     * @return boolean
     * */
    public static final function hasDependence ($filename)
    {
        return preg_match(
            self::T_CONFIG_INI_PATTERN_DEPENDENCE_HAS,
            file_get_contents($filename)
        );

    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * retorna as dependencias detectadas no conteudo informado
     *
     * @param string
     * @return string[]
     */
    public static final function getDependence ($content)
    {
        preg_match(
            self::T_CONFIG_INI_PATTERN_DEPENDENCE_GET,
            $content,
            $depends
        );

        return array_filter(preg_split('/,\s*/', $depends['depends']));
    }

    /**
     * @param string
     * @return string[]
     */
    public static function getSections ($content, $fullSectionName = FALSE)
    {
        if(preg_match_all(
            self::T_CONFIG_INI_PATTERN_DEPENDENCE_SECTION,
            $content,
            $currSections
        )) {

            if ($fullSectionName) {
                return array(
                    'section' => $currSections['section'],
                    'completeDef' => $currSections['completeDef'],
                );
            }

            return $currSections['section'];
        }

        return array();
    }

    /**
     * @param string
     * @return string[]
     */
    public function getSectionsData ($content, $sections)
    {
        $arrData = array();

        foreach ($sections as $section) {
            preg_match(
                sprintf(self::T_CONFIG_INI_PATTERN_DEPENDENCE_SECTION_CONTENT, $section)
                , $content
                , $sectData
            );

            $arrData[$section] = isset($sectData['content'])
                               ? trim($sectData['content'])
                               : NULL
                               ;
        }

        return $arrData;
    }

    /**
     * converte o nome da depencia no caminho para o arquivo referencia
     *
     * @param string $depence
     * @param string $dirname
     * @return string
     */
    public static final function converteDependence ($depence, $dirname)
    {
        return sprintf(
            '%s%s%s.%s',
            $dirname,
            DIRECTORY_SEPARATOR,
            $depence,
            self::$_configFilenameExtension
        );
    }

    /**
     * @param string[]
     * @return string
     */
    public static final function targetNameFromFileWithDeps ($listDeps)
    {
        return implode(
            self::$_extendsFilenameSeparator,
            array_reverse(array_keys($listDeps))
        ) . '.' . self::$_configFilenameExtension;
    }

    /**
     * cria a pasta que acomodará o arquivo de configuracao final
     *
     * @param string
     * @return string
     * */
    public static final function createTargetDirectory ($dirname)
    {
        IOException::throwsExceptionIfParamIsNull(
            is_writeable($dirname),
            sprintf('Não há permissão de escrita no diretório %s', $dirname)
        );

        $target = $dirname . DIRECTORY_SEPARATOR . 'cache';

        if (! is_dir($target)) {
            IOException::throwsExceptionIfParamIsNull(
                mkdir($target), 'Não foi possível criar pasta de cache de configuração'
            );
        }

        return $target;
    }
}