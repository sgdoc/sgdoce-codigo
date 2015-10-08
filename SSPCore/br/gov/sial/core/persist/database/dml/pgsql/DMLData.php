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
namespace br\gov\sial\core\persist\database\dml\pgsql;
use br\gov\sial\core\lang\Date,
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\database\dml\DMLDataAbstract;

/**
 * SIAL Persist DML Data
 *
 * @package br.gov.sial.core.persist.database.dml
 * @subpackage pgsql
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class DMLData extends SIALAbstract
{
    /**
     * retorna os dados de todos as atributos manipulados pela persistencia, opcionalmente uma
     * lista de attrs podem ser informados limitando a relacao dos valores que serao retornados.
     *
     * <ul>
     *     <li>DMLData::get(ValueObject $valueObject, string[] $attrs)</li>
     * </ul>
     *
     * @example DMLData::get
     * @code
     * <?php
     *     ...
     *     # uso sem informar a lista de colunas
     *     $DMLData->get($valueObject);
     *
     *     # uso informando lista de colunas
     *     $DMLData->get($valueObject, array('col_1', 'colu2', 'col_n'));
     *     ...
     * ?>
     * @endcode
     * @param ValueObject
     * @param string[] $attrs
     * */
    public function get (ValueObjectAbstract $valueObject, array $attrs = NULL, $ignoreNULL = FALSE)
    {
        $data = new \StdClass;

        $annon = $valueObject->annotation()
                             ->load();

        $columns = $annon->attrs;

        if (!empty($attrs)) {

            $columns = array();

            foreach ($attrs as $key => $name) {
                $columns[$name] = $annon->attrs->$name;
            }
        }

        foreach ($columns as $column) {

            if (!self::_isPersistable($column)) {
                continue;
            }

            $name   = ':' . $column->name;

            $value = self::getter($valueObject, $column);

            if (is_null($value) && $ignoreNULL) {
                continue;
            }

            # vefifica se a attr referencia uma chave-primaria
            # e se possui valor definido para este attr ou se
            # algum valor default fora definido
            if (self::_isPrimaryKey($column)) {

                # verifica se o valor para Pk foi informado
                if (empty($value) && isset($column->defaultValue)) {
                    $value = $column->defaultValue;
                }

                # se nem o valor e nem o valor default foi informado
                # entao ignora a pk
                if (empty($value)) {
                    continue;
                }
            }

            # corrige o tipo de date para realidade PDO
            if ('date' == $column->type) {
                $params->type = 'string';
            }

            $defValue = NULL;
            if (!empty($value) || is_bool($value) || is_numeric($value)) {
                $defValue = $value;
            }

            # verifica se o valor informado eh para ficar NULL
            if ('NULL' === strtoupper($value)) {
                $defValue = NULL;
            }

            $data->$name = (object) array(
                'value' => $defValue,
                'type'  => $column->type
            );
        }

        return $data;
    }

    /**
     * retorna apenas os attr preenchidos
     * */
    public function getFilledAttr (ValueObjectAbstract $valueObject)
    {
        return $this->get($valueObject, NULL, TRUE);
    }

    /**
     * retorna apenas os dados das chaves primarias
     *
     * @param ValueObject
     * */
    public function primaryKey (ValueObjectAbstract $valueObject)
    {
        $annon = $valueObject->annotation()
                             ->load();

        $attrs = NULL;

        foreach ($annon->attrs as $column) {

            if (self::_isPrimaryKey($column)) {
                $attrs[] = $column->name;
            }

        }

        return
        self::get($valueObject, $attrs);
    }

    /**
     * @param stdClass $attr
     * @return boolean
     * */
    private function _isPersistable (\stdClass $attr)
    {
        return isset($attr->database);
    }

    /**
     * @param stdClass $attr
     * @return boolean
     * */
    private function _isPrimaryKey ($attr)
    {
        return isset($attr->primaryKey);
    }

    /**
     * @param ValueObjectAbstract $valueObject
     * @param stdClass $attr
     * @return boolean
     * */
    private function _isEmpty (ValueObjectAbstract $valueObject, $attr)
    {
        $data = self::getter($valueObject, $attr);

        return !empty($data)
               ? $data
               : !isset($attr->defaultValue);
    }

    /**
     * @param ValueObjectAbstract $valueObject
     * @param stdClass $attr
     * @return string
     * */
    public static function getter (ValueObjectAbstract $valueObject, $attr)
    {
        $getter = $attr->get;

        $value = $valueObject->$getter();

        if ($value instanceof ValueObjectAbstract) {
            $value = self::_isEmpty($value, $attr);
        }

        if ($value instanceof Date) {
            $value = $value->output();
        }

        return $value;
    }

    /**
     * @return DMLData
     * */
    public static function factory ()
    {
        return new self;
    }
}
