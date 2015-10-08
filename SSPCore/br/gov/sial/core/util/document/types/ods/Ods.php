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
namespace br\gov\sial\core\util\document\types\ods;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\document\Pageable,
    br\gov\sial\core\util\document\Property,
    br\gov\sial\core\util\document\Documentable,
    br\gov\sial\core\util\lib\OdsPhpGenerator\ods as ThirdOds,
    br\gov\sial\core\util\document\exceptions\DocumentException;

/**
 * Este classe faz o papel de Adapter (Ods) e responsavel por especializar os
 * metodos da biblioteca OdsPhpGenerator 0.0.2
 *
 * @package br.gov.sial.core.util.document.types
 * @subpackage ods
 * @name Ods
 * @author michael fernandes <michael.rodrigues@icmbio.gov.br>
 * @author bruno menezes <bruno.menezes@icmbio.gov.br>
 * */
class Ods extends SIALAbstract implements Documentable
{
    /**
     * @const
     */
    const ERROR_SAVE = "Ocorreu um erro ao tentar salvar o arquivo. Verifique se o diretorio existe e possui permissao de escrita.";

    /**
     * @var \br\gov\sial\core\util\lib\OdsPhpGenerator\ods
     */
    private $_ods = NULL;

    /**
     * @var \br\gov\sial\core\util\document\Property
     */
    private $_property = NULL;

    /**
     * @var \br\gov\sial\core\util\document\Property
     */
    private $_pages = array();

    /**
     * @inheritdoc
     * @param string $type - tipo do arquivo a ser criado
     */
    public function __construct ()
    {
        $this->_ods = new ThirdOds();
        $this->_property = new Property();
    }

    /**
     * retorna as propriedades
     * @return Property
     */
    public function property ()
    {
        return $this->_property;
    }

    /**
     * adiciona página
     * @param Pageable $page
     * @return Ods
     */
    public function addPage (Pageable $page)
    {
        $this->_pages[] = $page;
        return $this;
    }

    /**
     * cria nova página
     * @return PageOds
     * @param string $name
     */
    public function newPage ($name)
    {
        return new PageOds($name);
    }

    /**
     * remove página
     * @param integer $id
     * @return Ods
     */
    public function removePage ($idx)
    {
        if (!isset($this->_pages[$idx - 1])) {
            throw new DocumentException("Não existe uma página com este número!");
        }
        unset($this->_pages[$idx - 1]);
        return $this;
    }

    /**
     * Remove todas as páginas
     * @return Ods
     */
    public function removePages ()
    {
        $this->_pages = array();
        return $this;
    }

    /**
     * Salva o documento Ods
     * @param string $path
     * @throws DocumentException
     */
    public function save ($dir)
    {
        foreach ($this->_pages as $page) {
            $this->_ods->addTable($page->content());
        }

        $dir = (substr($dir, -1, 1) == '/') ? $dir : $dir . '/';

        $file = $dir . $this->property()->getName() . '.ods';

        $this->_ods->genOdsFile($file);

        if (!file_exists($file)) {
            throw new DocumentException(self::ERROR_SAVE);
        }
    }
}