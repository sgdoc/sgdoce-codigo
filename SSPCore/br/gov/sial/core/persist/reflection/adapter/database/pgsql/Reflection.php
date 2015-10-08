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
namespace br\gov\sial\core\persist\reflection\adapter\database\pgsql;
use br\gov\sial\core\persist\Connect,
    br\gov\sial\core\persist\reflection\Reflection as ParentReflection,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist.reflection.adapter.database
 * @subpackage pgsql
 * @name Reflection
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Reflection extends ParentReflection
{
    /**
     * @var string
     * */
    const REFLECTION_INVALID_SOURCE = '"source" não é um argumento válido.';

    /**
     * @var string
     * */
    private $_source;

    /**
     * @var string[]
     * */
    private $_properties = NULL;

    /**
     * Fábrica de Reflection.
     *
     * @param string $source
     * @param Connect $connect
     * @return Reflection
     * @throws IllegalArgumentException
     * */
    public static function factory ($source, Connect $connect)
    {
        $annon = NULL;

        if (is_string($source)) {
            $annon = ValueObjectAbstract::factory($source, array())->annotation();

        } elseif ($source instanceof ValueObjectAbstract) {
            $annon = $source->annotation();

        } else {
            throw new IllegalArgumentException(self::REFLECTION_INVALID_SOURCE);
        }

        $reflect = new self($annon, $connect);
        $reflect->_source = $connect->getSource();
        $reflect->_loadProperties($reflect->_source);
        return $reflect;
    }

    /**
     * Retorna uma propriedade específica do elemento.
     *
     * @param string $name
     * @return stdClass
     * */
    public function property ($name = NULL)
    {
        if ( isset($this->_properties[$this->_source]->$name)) {
            return $this->_properties[$this->_source]->$name;
        }
        return NULL;
    }

    /**
     * Retorna torna as propriedades do elemento.
     *
     * @return string[]
     * */
    public function properties ()
    {
        return $this->_properties[$this->_source];
    }

    /**
     * Recupera as propriedades do repositório.
     *
     * @param string $source
     * */
    private function _loadProperties ($source)
    {
        // @codeCoverageIgnoreStart
        $query = sprintf('SELECT db.datname AS name,
                                 own.rolname AS owner,
                                 pg_encoding_to_char(db.encoding) AS encoding,
                                 ts.spcname AS namespace, -- local fisico de armazenamento em disco
                                 db.datcollate AS collate,
                                 db.datctype AS ctype,
                                 db.datconnlimit AS limitconnection
                            FROM pg_database db
                            JOIN pg_authid AS own ON own.oid = db.datdba
                            JOIN pg_tablespace AS ts On ts.oid = db.dattablespace
                           WHERE db.datname = :source');
        // @codeCoverageIgnoreEnd

        $params['source'] = (object) array(
            'type' => 'string',
            'value' => $source
        );

        $this->_connect->prepare($query, (object) $params);
        $this->_properties[$source] = $this->_connect
                                           ->retrieve()
                                           ->fetch();
    }
}