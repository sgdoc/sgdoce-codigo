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
use br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * Fabrica de objeto de consulta
 *
 * @package br.gov.sial.core.persist
 * @subpackage query
 * @name JoinAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class JoinAbstract extends ClauseAbstract
{
    /**
     * @var integer
     * */
    const JOINABSTRACT_FACTORY_PARAM_NUMBER = 2;

    /**
     * @var string
     * */
    const JOINABSTRACT_EXPECTED_TWO_PARAM = 'são esperados exatados 2 (dois) parametros';

    /**
     * @var string
     * */
    const JOINABSTRACT_FIRST_PARAM_MUST_BE_ENTITY = 'O primeiro argumento deve ser do tipo Entity';

    /**
     * @var string
     * */
    const JOINABSTRACT_SECOND_PARAM_MUST_BE_RELATIONALABSTRACT = 'O primeiro argumento deve ser do tipo RelationalAbstract';

    /**
     * Referência para entidade.
     *
     * @var Entity
     * */
    protected $_entity;

    /**
     * Expressão de comparação usada no relacionamento entre entidades.
     * ex.: LeftColumn Oper RightColumn/Expression
     *
     * @var
     * */
    protected $_conditional;

    /**
     * Retorna a entidade.
     *
     * @return Entity
     * */
    public function entity ()
    {
        return $this->_entity;
    }

    /**
     * @return string
     * */
    public function render ()
    {
        $content = $this::T_COMMAND                . ' '
                 . $this->_entity->qualifiedName() . ' ON '
                 . $this->_conditional->render();
        return str_replace("'", '', $content);
    }

    /**
     * Fábrica de JoinAbstract
     *
     * @return JoinAbstract
     * @throws IllegalArgumentException
     * */
    public static function factory ()
    {
        $entity          = func_get_arg(0);
        $conditional     = func_get_arg(1);

        IllegalArgumentException::throwsExceptionIfParamIsNull(self::JOINABSTRACT_FACTORY_PARAM_NUMBER == func_num_args(), self::JOINABSTRACT_EXPECTED_TWO_PARAM);
        IllegalArgumentException::throwsExceptionIfParamIsNull($entity instanceof Entity, self::JOINABSTRACT_FIRST_PARAM_MUST_BE_ENTITY);
        IllegalArgumentException::throwsExceptionIfParamIsNull($conditional, self::JOINABSTRACT_SECOND_PARAM_MUST_BE_RELATIONALABSTRACT);

        $join               = parent::factory();
        $join->_entity      = $entity;
        $join->_conditional = $conditional;
        return $join;
    }
}