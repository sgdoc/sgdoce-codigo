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
namespace br\gov\sial\core\persist\query;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\Renderizable,
    br\gov\sial\core\persist\query\Column,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage query
 * @name ClauseAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class ClauseAbstract extends SIALAbstract implements Renderizable
{
    /**
     * @var string
     * */
    const CLAUSE_MANDATORY_COLUMN = 'É obrigatório informar uma coluna como argumento';

    /**
     * @var Column[]
     * */
    protected $_columns = array();

    /**
     * Construtor.
     *
     * @access private
     * */
    private function __construct ()
    {
    }

    /**
     * Column.
     *
     * @override
     * <ul>
     *     <li>self::column(Column $column);</li>
     *     <li>self::column(array(Column $column, ..., Column $column));</li>
     * </ul>
     *
     * @param Column[] $columns
     * @throws IllegalArgumentException
     * @return ClauseAbstract
     * */
    public function column ($columns)
    {
        if (TRUE == is_array($columns)) {
            foreach ($columns as $column) {
                $this->column($column);
            }
            return;
        }

        IllegalArgumentException::throwsExceptionIfParamIsNull($columns instanceof Column, self::CLAUSE_MANDATORY_COLUMN);
        $this->_columns[] = $columns;
        return $this;
    }

    /**
     * Retorna a representação textual.
     *
     * @return string
     * */
    public function render ()
    {
        $content = NULL;
        foreach ($this->_columns as $column) {
            $content[] = $column->qualifiedName();
        }

        if ($content) {
            $content = ' ' . implode(', ', $content);
        }

        return $this::T_COMMAND . $content;
    }

    /**
     * Fábrica de ClauseAbstract.
     *
     * @return ClauseAbstract
     * */
    public static function factory ()
    {
        $clause = get_called_class();
        return new $clause();
    }
}