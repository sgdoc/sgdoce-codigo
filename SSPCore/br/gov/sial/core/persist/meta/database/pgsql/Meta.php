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
namespace br\gov\sial\core\persist\meta\database\pgsql;
use br\gov\sial\core\persist\Persist,
    br\gov\sial\core\persist\meta\MetaAbstract,
    br\gov\sial\core\persist\meta\exception\MetaException,
    br\gov\sial\core\valueObject\ValueObjectAbstract AS ValueObject;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist.meta.database
 * @subpackage pgsql
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Meta extends MetaAbstract
{
    const T_META_MISS_SCHEMA  = 'O esquema da entidade não foi informado';
    const T_META_MISS_ENTITY  = 'O nome da entidade não foi informado';
    const T_META_RESULT_EMPTY = 'Nenhuma propriedade da entidade %s.%s foi encontrada';

    /**
     * query de recuperacao da versao
     * */
    private static $_queryVersion = 'SELECT version() AS version';

    /**
     * query de recuperacao dos meta-dados
     * */
    private static $_queryMeta = "SELECT tbl.table_catalog AS database_name,
                                    tbl.table_schema,
                                    tbl.table_name,
                                    tbl.is_insertable_into AS table_canwrite,
                                    CASE
                                      WHEN tbl.table_type = 'BASE TABLE' THEN 'table'
                                      WHEN tbl.table_type = 'VIEW'       THEN 'view'
                                      ELSE 'uknown'
                                    END AS table_type,
                                    col.column_name,
                                    col.ordinal_position AS column_position,
                                    CASE
                                      WHEN col.data_type IN ('serial', 'bigserial', 'integer','smallint', 'bigint', 'bigserial') THEN 'integer'
                                      WHEN col.data_type IN ('real', 'decimal', 'numeric', 'money') THEN 'double'
                                      WHEN substr(col.data_type, 1, 6) = 'double'    THEN 'double'
                                      WHEN substr(col.data_type, 1, 4) = 'time'      THEN 'time'
                                      WHEN substr(col.data_type, 1, 9) = 'timestamp' THEN 'timestamp'
                                      WHEN substr(col.data_type, 1, 4) = 'date'      THEN 'date'
                                      WHEN substr(col.data_type, 1, 4) = 'text'      THEN 'string'
                                      WHEN substr(col.data_type, 1, 9) = 'character' THEN 'string'
                                      WHEN substr(col.data_type, 1, 3) = 'bit'       THEN 'bit'
                                      ELSE col.data_type
                                    END AS column_type,
                                    CASE
                                      WHEN col.data_type IN ('serial', 'integer', 'smallint', 'bigint', 'bigserial') THEN col.numeric_precision
                                      WHEN substr(col.data_type, 1, 9) = 'character' THEN col.character_maximum_length
                                      WHEN substr(col.data_type, 1, 4) = 'text'      THEN -1
                                    END AS column_data_size,
                                    col.is_updatable AS column_canwrite,
                                    col.is_nullable  AS column_nullable,
                                    col.column_default AS column_default_value,
                                    (SELECT ARRAY_AGG(
                                       CASE
                                         WHEN con.constraint_type = 'PRIMARY KEY' THEN 'primaryKey'
                                         WHEN con.constraint_type = 'FOREIGN KEY' THEN 'foreignKey'
                                         WHEN con.constraint_type = 'CHECK'       THEN 'check'
                                         ELSE 'common' END )
                                      FROM information_schema.table_constraints AS con
                                     WHERE constraint_name IN (
                                        SELECT constraint_name
                                          FROM information_schema.key_column_usage
                                         WHERE table_schema = tbl.table_schema
                                           AND table_name   = tbl.table_name
                                           AND column_name  = col.column_name
                                    )) AS column_constraint_type,
                                    (SELECT ARRAY_AGG(ROW(table_name, column_name))::TEXT AS column_constraint_refer
                                     FROM information_schema.constraint_column_usage
                                    WHERE constraint_name IN (
                                        SELECT in_clu.constraint_name
                                          FROM information_schema.key_column_usage  AS in_clu
                                    INNER JOIN information_schema.table_constraints AS in_tco
                                                ON in_clu.constraint_name = in_tco.constraint_name
                                               AND in_tco.constraint_type = 'FOREIGN KEY'
                                         WHERE in_clu.table_schema = tbl.table_schema
                                           AND in_clu.table_name   = tbl.table_name
                                           AND in_clu.column_name  = col.column_name
                                    )) AS column_constraint_refer
                               FROM information_schema.tables  AS tbl
                         INNER JOIN information_schema.columns AS col
                             ON tbl.table_catalog = col.table_catalog
                                AND tbl.table_schema  = col.table_schema
                                AND tbl.table_name    = col.table_name
                              WHERE tbl.table_schema  = :schema
                                AND tbl.table_name    = :entity
                           ORDER BY column_position";

    /**
     * @return stirng
     * */
    public function version ()
    {
        $result =  $this->_persist->execute(self::$_queryVersion, $filter)->fetch();
        return next(explode(' ', $result->version));
    }

    /**
     * @param string $schema
     * @param string $entity
     * @return ArrayObject
     * @throws MetaException
     * */
    public function data ($schema, $entity)
    {
        $filter['schema']        = new \stdClass();
        $filter['schema']->type  = 'string';
        $filter['schema']->value = $schema;

        $filter['entity']        = new \stdClass();
        $filter['entity']->type  = 'string';
        $filter['entity']->value = $entity;

        /* recupera os dados */
        $result = $this->_persist->prepare(self::$_queryMeta, $filter)->retrieve();
        $meta  = array();

        while (($infor = $result->fetch())) {
            $meta[self::translateName($infor->column_name)] = $infor;
        }

        return $meta;
    }

    /**
     * junta as informacoes do atributo encontradas no proprio value object
     * com as informecoes recuperadas no banco de dados
     *
     * @param stdClass $meta
     * @param stdClass $voInfo
     * */
    public function infoMerge (\stdClass $meta, \stdClass $voInfo)
    {
        /* nome compativel com o sial */
        $columnName = self::translateName($meta->column_name);

        self::infoCastValue($meta);

        # verifica se o usuario definiu alguma anotacao
        if (isset($voInfo->attrs->$columnName)) {
            self::infoMergeInsert($columnName, $meta, $voInfo);
        }

        return $meta;
    }

    /**
     * insere informacoes definidas pelo usuario
     *
     * @param string $columnName
     * @param stdClass $meta
     * @param stdClass $voInfo
     * */
    public function infoMergeInsert ($columnName, \stdClass &$meta, \stdClass $voInfo)
    {
        # compatibilidade com as anotacoes existentes
        # as demais anotacoes são dispensaveis
        $dictionary = array(
            'get'           => 'get',
            'set'           => 'set',
            'type'          => 'column_type',
            'default'       => 'column_default_value',
            'primaryKey'    => 'primaryKey',
            'autoIncrement' => 'autoIncrement',
            'nullable'      => 'nullable',
        );

        foreach ($voInfo->attrs->$columnName as $name => $val) {

            if (!isset($dictionary[$name])) {
                continue;
            }

            $transName        = $dictionary[$name];
            $meta->$transName = $voInfo->attrs->$columnName->$name;
        }
    }

    /**
     * corrige valores recuperados do banco
     * */
    public function infoCastValue (&$meta)
    {
        /* ajusta propriedades */
        $meta->table_canwrite  = 'YES' === $meta->table_canwrite ;
        $meta->column_nullable = 'YES' === $meta->column_nullable;
        $meta->column_canwrite = 'YES' === $meta->column_canwrite;
    }

    /**
     * converte o nome da coluna para o formato lowerCamelCase
     *
     * @param string $name
     * @return string
     * */
    public function translateName ($name)
    {
        $compatibleName = explode('_', $name);
        $fPart          = array_shift($compatibleName);
        $compatibleName = array_map(function ($val) { return ucfirst($val); }, $compatibleName);
        array_unshift($compatibleName, $fPart);
        return implode($compatibleName);
    }
}

/*
 * -- entidades
 * @schema                : nome do schema que armazena a entidade
 * @entity                : nome da entidade que armazena os atributos
 * @entityCanWrite        : define se a entidade pode ser gravada
 *
 * @foreignKey            : especifica, explicitamente, um relacionamento da entidade
 * @foreignKey::attr      : nome do attr de referencia local
 * @foreignKey::refer     : nome da entidade::attr de referencia
 *
 * @foreignKeyRev         : especifica, explicitamente, um ralcionamento reverso, ou seja, outra entidade aponta para atual
 * @foreignKeyRev::attr   : nome do attr de referencia local
 * @foreignKeyRev::refer  : nome da entidade::attr que faz referencia a entidade corrente
 *
 * @manyToMany            : estudar como fazer relacionamento (n-m)
 *
 * @attr                  : especifica as caracteristicas do atributo
 * @attr::name            : nome do atributo
 * @attr::type            : tipo do atributo
 * @attr::database        : nome do atributo no banco de dados (deprecated)
 * @attr::canWrite        : define se attr podera ser gravado no repositorio
 * @attr::canRead         : define se attr podera ser lido no repositorio(default: true)
 * @attr::nullable        : define se o attr podera ter seu valor nulo gravado no repositorio
 * @attr::defaultValue    : valor default do attr
 * @attr::primaryKey      : define o attr como chave primaria
 * @attr::get             : metodo acessor para recuperacao do valor do attr no valueObject
 * @attr::set             : metodo acessor para definir o valor do attr no valueObject
 *
 *
 * -- definicao da annon da class --
 * @schema(name="sial_tests")
 * @entity(name="s_pessoa_fisica")
 * @foreignKey(attr="sqPessoa", refer="com\...\PessoaFisicaObject::sqPessoa")
 * @foreignKey(attr="sqEstado", refer="com\...\EstadoValueObject::sqestado")
 * class ClassName
 * {
 * }
 * */