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
namespace br\gov\sial\core\output\screen\html;
use br\gov\sial\core\output\screen\ITabularData,
    br\gov\sial\core\output\screen\ElementContainerAbstract,
    br\gov\sial\core\output\screen\exception\ElementException;

/**
 * SIAL
 *
 * esta classe aceita como param apenas os elementos (caption, thead, tbody e tfooter)
 * qualquer outro elemento informado sera repelido com uma exception.
 *
 * @package br.gov.sial.core.output.screen
 * @subpackage html
 * @name Table
 * @author J. Augusto <augustowebd@gmail.com>
 * @example screen::html::Table
 * <code>
 * <?php
 * 	 # instancia table
 *   $table = new Table();
 *
 *	 # adiciona caption a table acima
 *   $table->caption = new TableCaption('title of table');
 *
 *   # adiciona head a table
 *   $table->thead = new THead();
 *
 *   # adiciona primeira linha ao cabecalho
 *   $table->thead->tr = new Tr();
 *
 *   # adiciona coluna a linha da cabecalho
 *   # note que eh necessario informar o indice da linha
 *   $table->thead->tr[0] = new Td(...);
 * ?>
 * </code>
 * */
class Table extends ElementContainerAbstract implements ITabularData
{
    /**
     * @var string
     * */
    const T_TAG = 'table';

    /**
     * @var TableCaption
     * */
    protected $_caption = NULL;

    /**
     * @var TableSectionAbstract
     * */
    protected $_thead = NULL;

    /**
     * @var TableSectionAbstract
     * */
    protected $_tfoot = NULL;

    /**
     * @var TableSectionAbstract
     * */
    protected $_tbody = NULL;

    /**
     * Nota: uma e somente uma secao do mesmo tipo eh aceita
     *
     * @param TableSectionAbstract $section
     * */
    public function __set ($name, $value)
    {
        ElementException::throwsExceptionIfParamIsNull($value instanceof TableSectionAbstract, 'Elemento não suportado');

        $attr = "_{$name}";
        if (property_exists($this, $attr)) {
            $method      = NULL == $this->$attr ? 'add' : 'replaceChild';
            $this->$attr = $value;
            $this->$method($this->$attr);
        }
    }

    /**
     * clona os objetos interno
     * */
    public function __clone ()
    {
        $this->_caption = $this->_caption instanceof parent ? clone $this->_caption : NULL;
        $this->_thead = $this->_thead instanceof parent ? clone $this->_thead : NULL;
        $this->_tfoot = $this->_tfoot instanceof parent ? clone $this->_tfoot : NULL;
        $this->_tbody = $this->_tbody instanceof parent ? clone $this->_tbody : NULL;
    }

    /**
     * redefine a ordem de renderizacao dos elementos de tabela
     *
     * @override
     * @return string
     * */
    public function render ()
    {
        $this->clear();

        if (NULL !== $this->_caption) {
            $this->add($this->_caption);
        }

        if (NULL !== $this->_thead) {
            $this->add($this->_thead);
        }

        if (NULL !== $this->_tfoot) {
            $this->add($this->_tfoot);
        }

        if (NULL !== $this->_tbody) {
            $this->add($this->_tbody);
        }

        return parent::render();
    }
}