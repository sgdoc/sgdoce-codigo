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
namespace br\gov\sial\core\persist\query\database;
use br\gov\sial\core\Renderizable,
    br\gov\sial\core\persist\query\database\Column,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\persist\query\RelationalAbstract as ParentRelational;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage query
 * @name RelationalAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */

abstract class RelationalAbstract extends ParentRelational
{
    /**
     * @var string
     * */
    const RELATIONALABSTRACT_UNSUPPORTED_TYPE = 'O tipo de dado "%s" não é suportado.';

    /**
     * @var Column
     * */
    protected $_column;

    /**
     * @var mixed
     * */
    protected $_value;

    /**
     * Sobrepõe o tipo da coluna.
     *
     * @var string
     * */
    protected $_forceType = NULL;

    /**
     * Construtor.
     *
     * @param Column$column
     * @param mixed $value
     * */
    public function __construct (Column $column, $value)
    {
        $this->_value  = $value;
        $this->_column = $column;
    }

    /**
     * Converte o valor informado para representação em banco de dados
     *
     * @param mixed $value
     * @return mixed
     * @throws IllegalArgumentException
     * @todo implementar AntiSQLInject para os valores informados
     * */
    public function converteValue ($value)
    {
        $dataType = NULL === $this->_forceType ? $this->_column->getDataType() : $this->_forceType;
        $result   = NULL;
        $content  = NULL;

        if (TRUE == is_array($value)) {
            foreach ($value as $val) {
                $content[] = $this->converteValue($val);
            }
            return '(' . implode(', ', $content). ')';
        }

        if ($value instanceof Renderizable) {
            $result = current(preg_split('/\s*AS\s*/', $value->render()));

        } elseif ('boolean' == $dataType) {
            switch (strtolower(trim($value))) {
                case ''     :
                case 'f'    :
                case '0'    :
                case 'false':
                case 'null' :
                case NULL   :
                    $result = 'FALSE';
                    // @codeCoverageIgnoreStart
                    break;
                    // @codeCoverageIgnoreEnd

                default     :
                    $result = 'TRUE';
                    // @codeCoverageIgnoreStart
                    break;
                    // @codeCoverageIgnoreEnd
            }

        } elseif (TRUE == is_null($value)) {
            $result = 'NULL';

        } elseif (
                  'char'      == $dataType ||
                  'date'      == $dataType ||
                  'string'    == $dataType ||
                  'character' == $dataType ||
                  'datetime'  == $dataType ||
                  'timestamp' == $dataType
        ) {
            $result = "'{$value}'";

        } elseif ('integer' == $dataType) {
            $result = $value + 0;

        } elseif ('double' == $dataType) {
            $result = $value;

        } else {
            throw new IllegalArgumentException(sprintf(self::RELATIONALABSTRACT_UNSUPPORTED_TYPE, $dataType));

        }
        return $result;
    }

    /**
     * Representação textual do comando.
     *
     * @return string
     * */
    public function render ()
    {
        $column = current(preg_split('/\s*AS\s*/', $this->_column->render()));
        return sprintf('%s %s %s', $column, $this::T_COMMAND, $this->converteValue($this->_value));
    }
}