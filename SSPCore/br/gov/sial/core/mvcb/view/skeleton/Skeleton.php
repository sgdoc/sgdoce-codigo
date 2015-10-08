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
namespace br\gov\sial\core\mvcb\view\skeleton;
use br\gov\sial\core\SIALAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.mvcb.view
 * @subpackage skeleton
 * @name Skeleton
 * @author J. Augusto <augustowebd@gmail.com>
 * */
 abstract class Skeleton extends SIALAbstract
 {
    /**
     * Ponteiro para linguagem de tradução.
     *
     * @var Language
     * */
    private $_language;

    /**
     * @var Reader
     * */
    private $_reader;

     /**
      * @var Writer
      * */
    private $_writer;

    /**
     * Tratudor de instrução do modo skeleton para linguagem alvo.
     *
     * @var SkeletonTranslator
     * */
    private $_translator;

    /**
     * Construtor.
     *
     * @param Reader $reader
     * @param Writer $writer
     * @param Translator $translator
     * */
    public function __construct (Reader $reader, Writer $writer, Translator $translator)
    {
        $this->_writer     = $writer;
        $this->_reader     = $reader;
        $this->_translator = $translator;
        $this->_language   = $this->_translator->translate($this->_reader);
    }

    /**
     * Renderiza o conteúdo de Skeleton.
     *
     * @return string
     * */
    public function content ()
    {
        return $this->_language->render();
    }

    /**
     * @return Skeleton
     * @todo falta definir onde gravar o arquivo
     * */
    public function write ()
    {
      $content = $this->_language->render();
    }

    /**
     * Fábrica de Skeleton.
     * 
     * @param string $language
     * @param string $filename
     * @return Skeleton
     * */
     public static function factory ($language, $filename)
     {
         $language  = strtolower($language);
         $namespace = __NAMESPACE__ . self::NAMESPACE_SEPARATOR
                    . $language     . self::NAMESPACE_SEPARATOR
                    . 'Skeleton';
        return new $namespace($filename);
     }
 }