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

namespace br\gov\sial\core\util\document;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\document\exceptions\DocumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.util
 * @subpackage document
 * @name Document
 * @author michael fernandes <michael.rodrigues@icmbio.gov.br>
 * @author bruno menezes <bruno.menezes@icmbio.gov.br>
 * */
class Document extends SIALAbstract implements Documentable
{

    /**
     * @var Documentable
     * @var Pageable
     */
    private $_adapter = NULL;

    /**
     * tipos de arquivo suportados
     * @var string[]
     */
    private static $_supportedTypes = array('ods');

    /**
     * @inheritdoc
     * @param string $type - tipo do arquivo a ser criado
     */
    private function __construct ($type)
    {
        $type = strtolower($type);

        if (!self::isSupported($type)) {
            throw new DocumentException("O tipo de arquivo '$type' não é suportado.");
        }

        $namespace = $this->getNamespace() . '\types\\' . $type . '\\' . ucfirst($type);
        $this->_adapter = new $namespace();
    }

    /**
     * fábrica de objetos
     * @param string $type
     * @return Document
     */
    public static function factory ($type)
    {
        return new self($type);
    }

    /**
     * Verifica se o tipo informado é suportado pela classe
     * @access public
     * @static
     * @param string $type
     * @return boolean
     */
    public static function isSupported ($type)
    {
        if (in_array($type, self::$_supportedTypes)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Adiciona ma nova página no documento
     * @param Pageable
     * @return Document
     */
    public function addPage (Pageable $page)
    {
        $this->_adapter->addPage($page);
        return $this;
    }

    /**
     * Retorna uma nova página
     * @param string $name
     * @return Pageable
     */
    public function newPage ($name)
    {
        return $this->_adapter->newPage($name);
    }

    /**
     * Remove uma página especifica do documento
     * @param mixed $id - número da página que deseja excluir
     * @return Document
     */
    public function removePage ($idx)
    {
        $this->_adapter->removePage($idx);
        return $this;
    }

    /**
     * Remove todas as páginas do documento
     * @return Document
     */
    public function removePages ()
    {
        $this->_adapter->removePages();
        return $this;
    }

    /**
     * Salva o documento em disco em um local especifico
     * @param string $dir
     */
    public function save ($dir)
    {
        $this->_adapter->save($dir);
    }

    /**
     * Retorna o objeto com as propiedades do documento
     * @return Property
     */
    public function property ()
    {
        return $this->_adapter->property();
    }
}