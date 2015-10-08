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
use br\gov\sial\core\exception\IOException,
    br\gov\sial\core\persist\query\ClauseAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist.query
 * @subpackage database
 * @name LimitAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class LimitAbstract extends ClauseAbstract
{
    /**
     * @var
     * */
    const LIMITABSTRACT_UNAVAILABLE_LIMIT_OPERATOR = '"Limit" não encontrado/disponivel para o banco de dados escolhido';

    /**
     * @var string
     * */
    const DEFAULT_LIMIT = 10;

    /**
     * @var string
     * */
    const T_COMMAND = 'LIMIT';

    /**
     * Inteiro positivo que define a quantidade de registros que serão recuperados na pesquisa.
     *
     * @var integer
     * */
    protected $_limit;

    /**
     * Inteiro maior ou igual a zero que determina a posição inicial do cursor.
     *
     * @var integer
     * */
    protected $_offset;

    /**
     * Define o limit e offset de pesquisa
     *
     * @param mixed
     * <ul>
     *   <li><b>NULL</b>   : traduzido como ALL</li>
     *   <li><b>ALL</b>    : traduzido como ALL</li>
     *   <li><b>[0-9]+</b> : traduzido como o numero informado</li>
     *   <li>Qualquer outro valor sera traduzido como 0 (zero)</li>
     * </ul> $limit
     * @param integer $offset
     * @return LimitAbstract
     * */
    public function set ($limit, $offset = 0)
    {
        $isString = FALSE;

        if (NULL == $limit) {
            $limit = 'ALL';
        }

        if ('string' === gettype($limit)) {
            $isString = (boolean) $limit = strtoupper($limit);
        }

        if (TRUE === $isString && 'ALL' != $limit) {
            $limit = self::DEFAULT_LIMIT;
        }

        if (FALSE === $isString && 0 > (integer) $limit) {
            $limit = 0;
        }

        $this->_offset = (integer) $offset;
        $this->_limit  = $limit;
        return $this;
    }

    /**
     * Fábrica de LimitAbstract
     *
     * @param string driver
     * @return LimitAbstract
     * @throws IOException
     * */
    public static function factory ($driver)
    {
        $namespace = __NAMESPACE__ . self::NAMESPACE_SEPARATOR . strtolower($driver) . self::NAMESPACE_SEPARATOR . 'Limit';
        $message = self::LIMITABSTRACT_UNAVAILABLE_LIMIT_OPERATOR;
        IOException::throwsExceptionIfParamIsNull(TRUE == is_file(self::realpathFromNamespace($namespace) . '.php'), $message);
        return new $namespace();
    }
}