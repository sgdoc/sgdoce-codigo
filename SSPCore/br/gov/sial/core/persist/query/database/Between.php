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
use br\gov\sial\core\persist\query\database\pgsql\Select;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist.query
 * @subpackage database
 * @name Between
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Between extends RelationalAbstract
{
    /**
     * Segundo argumento do between.
     *
     * @var mixed $sParam;
     * */
    private $_sParam;

    /**
     * @var string
     * */
    const T_COMMAND = 'BETWEEN';

    /**
     * Construtor.
     *
     * @param Column $column
     * @param mixed $fParam
     * @param mixed $sParam
     * */
    public function __construct (Column $column, $fParam, $sParam)
    {
        $this->_value  = $fParam;
        $this->_sParam = $sParam;
        $this->_column = $column;
    }

    /**
     * Representação textual do comando
     *
     * @return string
     * */
    public function render ()
    {
        $tpl    = '%s %s %s AND %s';
        $column = current(preg_split('/\s*AS\s*/', $this->_column->render()));
        return sprintf($tpl, $column, $this::T_COMMAND, $this->converteValue($this->_value) , $this->converteValue($this->_sParam));
    }
}