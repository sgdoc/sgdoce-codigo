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
use br\gov\sial\core\util\document\Content,
    br\gov\sial\core\util\document\Pageable,
    br\gov\sial\core\util\lib\OdsPhpGenerator\odsTable,
    br\gov\sial\core\util\lib\OdsPhpGenerator\odsTableCellString,
    br\gov\sial\core\util\lib\OdsPhpGenerator\odsTableRow,
    br\gov\sial\core\util\document\Property;

/**
 * Esta classe é responsavel por especializar os metodos
 * referentes a paginas da biblioteca OdsPhpGenerator 0.0.2
 *
 * @package br.gov.sial.core.util.document.types
 * @subpackage ods
 * @name PageOds
 * @author michael fernandes <michael.rodrigues@icmbio.gov.br>
 * @author bruno menezes <bruno.menezes@icmbio.gov.br>
 * */
class PageOds implements Pageable
{
    /**
     * @var integer
     */
    private $_rows = array();

    /**
     * @var \br\gov\sial\core\util\lib\OdsPhpGenerator\odsTable
     */
    private $_page;

    /**
     * @var \br\gov\sial\core\util\document\Property
     */
    private $_property;

    /**
     * construtor
     * @param string $name
     */
    public function __construct ($name)
    {
        $this->_page = new odsTable($name);
        $this->_property = new Property();
    }

    /**
     * retorna a linha
     * @param param $row
     * @return integer
     */
    private function &_getRow ($row)
    {
        if (!array_key_exists($row, $this->_rows)) {
            $this->_rows[$row] = array(
                'row' => new odsTableRow(),
                'cells' => array()
            );
        }
        return $this->_rows[$row];
    }

    /**
     * adiciona conteudo
     * @param Content $content
     * @return Content
     */
    public function addContent (Content $content)
    {
        $this->addContentAt($content, count($this->_rows) + 1, 1);
        return $this;
    }

    /**
     * adiciona conteudo em uma posição especifica
     * @param Content $con
     * @param integer $row
     * @param integer $cell
     * @return PageOds
     */
    public function addContentAt (Content $content, $row, $col)
    {
        $row = &$this->_getRow($row);
        $row['cells'][$col] = new odsTableCellString($content->getContent());
        return $this;
    }

    /**
     * retorna todas as linhas
     * @return array
     */
    public function getRows ()
    {
        return $this->_rows;
    }

    /**
     * Limpa os conteudos inseridos
     * @return PageOds
     */
    public function clear ()
    {
        $this->_rows = array();
        return $this;
    }

    /**
     * retorna o conteudo
     * @return odsTable
     */
    public function content ()
    {
        foreach ($this->_rows as $index => $row) {
            foreach ($row['cells'] as $cell) {
                $row['row']->addCell($cell);
            }
            $this->_page->addRow($row['row']);
        }
        return $this->_page;
    }

    /**
     * retorna a propriedade
     * @return Property
     */
    public function property ()
    {
        return $this->_property;
    }
}