<?php
/*
 * Copyright 2013 ICMBio
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
namespace br\gov\mainapp\library\persist\database;
use br\gov\sial\core\persist\database\Persist as ParentPersist;

/**
 * @package br.gov.mainapp.library.persist
 * @subpackage database
 * @name Persist
 * */
class Persist extends ParentPersist
{
    /**
     * @var string
     */
    protected $_where = array();

    /**
     * Agregador de condicionais numa cláusula WHERE
     * @var stdClass
     */
    protected $_params = NULL;

    /**
     * Query
     * @var string
     */
    protected $_query;

    /**
     * Monta a clásula WHERE
     * @param type $condicionals
     * @return \br\gov\mainapp\application\sisvp\quantitativoFgDas\persist\database\QuantitativoFgDasPersist
     */
    public function buildWhere($condicionals)
    {
        $count = 0;
        $operator = ' = ';

        foreach ($condicionals as $elem) {

            if ($elem['value'] !== '' && $elem['value'] !== NULL) {
                $count++;

                $fieldObject = new \stdClass();
                $fieldObject->type  = $elem['entity']->column($elem['field'])->entity()->columns()->$elem['field']->type;

                # se a condição for is null ou is not null
                if (in_array(strtoupper($elem['value']), array('NULL', 'NOT NULL'))) {
                    $operator = ' IS ';
                    $fieldObject->value = $elem['value'];

                    $this->_where[] = $elem['entity']->column($elem['field'])->entity()->alias() . '.' .
                                      $elem['entity']->column($elem['field'])->entity()->columns()->$elem['field']->database . ' ' .
                                      $operator . ' ' .
                                      $fieldObject->value;

                    continue;

                # se o atributo da tabela for do tipo string
                } elseif ($fieldObject->type == 'string') {
                    $operator = ' ILIKE ';
                    $fieldObject->value = $elem['value'] . '%';

                    $subject = $elem['value'];
                    $pattern = "/[áàâãäéèêëíìîĩïóòôõöúùûũüÁÀÂÃÄÉÈÊËÍÌÎĨÏÓÒÔÕÖÚÙÛŨÜçÇ]+/";
                    $result  = preg_match_all($pattern, $subject, $matches, PREG_PATTERN_ORDER);

                    if ($result) {
                        $this->_where[] = $elem['entity']->column($elem['field'])->entity()->alias() . '.' .
                                    $elem['entity']->column($elem['field'])->entity()->columns()->$elem['field']->database . ' ' .
                                    $operator . ' :' .
                                    $elem['field'] .
                                    ' OR ' .$elem['entity']->column($elem['field'])->entity()->alias() . '.' .
                                    $elem['entity']->column($elem['field'])->entity()->columns()->$elem['field']->database . ' ' .
                                    $operator . " '" . $this->translate($elem['value']) . "%'";
                    } else {
                        $this->_where[] = $elem['entity']->column($elem['field'])->entity()->alias() . '.' .
                                    $elem['entity']->column($elem['field'])->entity()->columns()->$elem['field']->database . ' ' .
                                    $operator . ' :' .
                                    $elem['field'];
                    }

                # se o atributo da tabela não for do tipo string
                } else {
                    $operator = ' = ';
                    $fieldObject->value = $elem['value'];

                    $this->_where[] = $elem['entity']->column($elem['field'])->entity()->alias() . '.' .
                                  $elem['entity']->column($elem['field'])->entity()->columns()->$elem['field']->database . ' ' .
                                  $operator . ' :' .
                                  $elem['field'];
                }

                $this->_params[$elem['field']] = $fieldObject;
            }
        }

        if ($count) {
            $this->_query .= ' WHERE ' . implode(($count > 1 ? ' AND ' : ' '), $this->_where);
        }

        return $this;
    }

    /**
     * Converte string em Camelcase para underscore na instrução de ordenação por único campo - Coluna na Grid.
     * @param string $orderBy
     * @param string $order
     */
    public function orderByField ($orderBy, $order)
    {
        $this->_query .= ' ORDER BY ';
        $field = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $orderBy));
        $this->_query .=  $field . ' ' . $order;
    }

    /**
     * Remove acentuação de uma string
     * @param string $text
     * @return string
     */
    public function translate($text)
    {
        $from = 'áàâãäéèêëíìîĩïóòôõöúùûũüÁÀÂÃÄÉÈÊËÍÌÎĨÏÓÒÔÕÖÚÙÛŨÜçÇ';
        $to = 'aaaaaeeeeiiiiiooooouuuuuAAAAAEEEEIIIIIOOOOOUUUUUcC';

        return str_replace($this->mb_str_split($from), $this->mb_str_split($to), $text);
    }

    /**
     *
     * @param string $text
     * @return string
     */
    private function mb_str_split($text)
    {
        return preg_split('~~u', $text, null, PREG_SPLIT_NO_EMPTY);
    }
}