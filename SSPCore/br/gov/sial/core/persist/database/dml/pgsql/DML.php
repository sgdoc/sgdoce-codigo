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
use br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\database\dml\DMLAbstract,
    br\gov\sial\core\persist\database\dml\pgsql\DMLData,
    br\gov\sial\core\persist\database\dml\exception\DMLException;

/**
 * SIAL Persist DML
 *
 * @package br.gov.sial.core.persist.database
 * @subpackage dml
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class DML extends DMLAbstract
{
    /**
     * retorna representacao textual do comando para salvar os dados
     *
     * @param ValueObjectAbstract
     * @return string
     * */
    public function save (ValueObjectAbstract $valueObject)
    {
        $annon = $valueObject->annotation()
                             ->load();

        $primaryKey = NULL;
        $columns    = array();

        foreach ($this->persistAttr($valueObject, 'insert') as $column) {
            $columns['name'][] = $column->name;
            $columns['database'][] = $column->database;
        }

        foreach ($annon->attrs as $key => $value) {
            $primaryKey[] = isset($value->primaryKey) ? $value->database : NULL;
        }

        return
        sprintf(
            'INSERT INTO %s (%s) VALUES (:%s) RETURNING %s'
            , $this->_persist->getEntity($valueObject)->qualifiedName()
            , implode(', ', $columns['database'])
            , implode(', :', $columns['name'])
            , implode(', ', array_filter($primaryKey))
        );
    }

    /**
     * retorna representacao textual do comando para alterar os dados
     *
     * @param ValueObjectAbstract
     * @return string
     * */
    public function update (ValueObjectAbstract $valueObject)
    {
        $annon = $valueObject->annotation()
                             ->load();

        $dmlQuery    =
        $conditional = NULL;

        foreach ($this->persistAttr($valueObject, 'update') as $column) {

            # verifica se  o campo eh uma chave primaira
            if (isset($column->primaryKey)) {

                $conditional .= sprintf(
                    ' %s %s = :%s'
                    , trim($conditional) ? 'AND' : 'WHERE'
                    , $column->database
                    , $column->name
                );

                continue;
            }

            $dmlQuery .= sprintf(
                ', %s = :%s'
                , $column->database
                , $column->name
            );
        }

        $dmlQuery = ltrim($dmlQuery, ', ');

        # por questao de seguranca, nao sera permitido a alteracao
        # de toda entidade, ou seja, seja necessario que ao menos
        # uma PK exista na entidade
        DMLException::throwsExceptionIfParamIsNull(
            $conditional, self::T_DMLABSTRACT_FILTER_REQUIRED
        );

        return
        sprintf(
            'UPDATE %s SET %s%s'
            , $this->_persist->getEntity($valueObject)->qualifiedName()
            , $dmlQuery
            , $conditional
        );
    }

    /**
     * retorna representacao textual do comando para remover os dados
     *
     * @param ValueObjectAbstract
     * @return string
     * */
    public function delete (ValueObjectAbstract $valueObject)
    {
        $data = new DMLData();
        $data = $data->getFilledAttr($valueObject);

        DMLException::throwsExceptionIfParamIsNull(
            (array) $data, self::T_DMLABSTRACT_FILTER_REQUIRED
        );

        $annon = $valueObject->annotation()
                             ->load();

        $filter = NULL;

        foreach ($data as $key => $column) {

            $attrName = str_replace(':', '', $key);

            if (isset($annon->attrs->$attrName) && isset($annon->attrs->$attrName->database)) {
                $filter .= sprintf(
                    ' %s %s %s :%s'
                    , $filter ? 'AND' : 'WHERE'
                    , $annon->attrs->$attrName->database
                    , $column->value === NULL ? 'IS' : '='
                    , $annon->attrs->$attrName->name
                );
            }
        }

        return
        sprintf(
            'DELETE FROM %s%s'
            , $this->_persist->getEntity($valueObject)->qualifiedName()
            , $filter
        );
    }

    /**
     * retorna representacao textual do comando para persistir os dados,
     * nesse contexto, persistir indicar salvar ou alterar os dados.
     * sendo que na primeira hipotese apenas se o registro ainda nao exisitir
     *
     * @param ValueObjectAbstract
     * @return string
     * */
    public function persist (ValueObjectAbstract $valueObject)
    {
    }

    public static function factory ()
    {
        return new self;
    }
}