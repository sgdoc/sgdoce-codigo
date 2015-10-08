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
namespace br\gov\sial\core\persist\ldap;
use br\gov\sial\core\persist\ldap\Connect,
    br\gov\sial\core\persist\PersistConfig,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\Persist as ParentPersist,
    br\gov\sial\core\persist\exception\PersistException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage ldap
 * @name Persist
 * @author Fábio Lima <fabioolima@gmail.com>
 * */
class Persist extends ParentPersist
{
    /**
     * Testa no construtor acima se este tipo é valido.
     *
     * @var string
     * */
    const PERSIST_TYPE = 'ldap';

    /**
     * @var string
     * */
    const DELETE_FUNCTION = '_delInLdap';

    /**
     * @var string
     * */
    const UPDATE_FUNCTION = '_updateInLdap';

    /**
     * @var string
     * */
    const SAVE_FUNCTION = '_addInLdap';

    /**
     * @var string
     * */
    const FIND_FUNCTION = '_findInLdap';

    /**
     * @var string
     * */
    const PERSIST_GETQUERY_UNSUPPORTED = 'Esse adapter não tem suporte a getQuery';

    /**
     * @var string
     * */
    const PERSIST_GETENTITY_UNSUPPORTED = 'Esse adapter não tem suporte a getEntity';

    /**
     * Construtor.
     *
     * @param PersistConfig $config
     * */
    public function __construct (PersistConfig $config = NULL)
    {
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     * */
    protected function _connect (PersistConfig $config)
    {
        return Connect::factory($config);
    }

    /**
     * Recupera registro por chave primária.
     *
     * <b>Nota</b>: Este metodo suporta pesquisa apenas com chave simples, ou seja, nao é suportado
     * chave composta por mais de um campo. Se necessário, este metodo deverá ser especializado.
     *
     * @param integer $key
     * @return ResultSet
     * @throws PersistException
     * */
    public function find ($key)
    {
        $annon = parent::annotation()->load();

        $sial_queryFields= '';

        foreach ($annon->attrs as $field) {
            if (($attr = self::getIfDefined($field, 'keyLdap'))) {
                $sial_queryFields .= "{$field->keyLdap}";
            }
        }

        $query = "{$sial_queryFields}={$key}";
        $param = array('cn','mail','uid','dn');

        return $this->getConnect()->prepare($query, $param)->retrieve()->fetch();
    }

    public function findByParam (ValueObjectAbstract $valueObject)
    {
        $param = self::FIND_FUNCTION;

        return $this->getConnect()->prepare($valueObject, $param)->retrieve();
    }

    /**
     * Persiste dados no repositório.
     *
     * @param ValueObjectAbstract $valueObject
     * @return Persist
     * @throws PersistException
     * */
    public function save (ValueObjectAbstract $valueObject)
    {
        $params = array();
        $params = $this->_prepareLdapParams($valueObject);

        $query = self::SAVE_FUNCTION;

        try {
            $this->getConnect()->prepare($valueObject, $query)->update();
        } catch (PersistException $pExc) {
            throw $pExc;
        }

    }

    /**
     * Altera dados no repositório.
     *
     * @param ValueObjectAbstract $valueObject
     * @return Persist
     * @throws PersistException
     * */
    public function update (ValueObjectAbstract $valueObject)
    {
        $params = array();

        $params = $this->_prepareLdapParams($valueObject);

        $query = self::UPDATE_FUNCTION;
        try {
            $this->getConnect()->prepare($valueObject, $query)->update();
        } catch (PersistException $pEexc) {
            throw $pEexc;
        }
    }

    /**
     * Prepara os parâmetros para consulta do LDAP.
     *
     * @param ValueObjectAbstract $valueObject
     * @return array[]
     */
    private function _prepareLdapParams(ValueObjectAbstract $valueObject)
    {
        $annon = $valueObject->annotation()->load();

        foreach ($annon->attrs as $field) {
            if (($attr = self::getIfDefined($field, 'ldap'))) {
                $get = $field->get;
                $params[$field->ldap] = $valueObject->$get();
            }
            if (($attr = self::getIfDefined($field, 'keyLdap'))) {
                $params['keyLdap'] = $field->keyLdap;
            }
        }
        return $params;
    }

    /**
     * Deleta dados no repositório.
     *
     * @param  ValueObjectAbstract $valueObject
     * @return Persist
     * @throws PersistException
     * */
    public function delete (ValueObjectAbstract $valueObject)
    {
        $annon = parent::annotation()->load();
        $sial_queryFields= '';

        foreach ($annon->attrs as $field) {
            if (($attr = self::getIfDefined($field, 'keyLdap'))) {
                $get = $field->get;
                $params[$field->ldap] = $valueObject->$get();
                $params['keyLdap'] = $field->keyLdap;
            }
        }

        $query = self::DELETE_FUNCTION;

        try {
            $this->getConnect()->prepare($query, $params)->update();
        } catch (PersistException $pEexc) {
            throw $pEexc;
        }
    }

    /**
     * {@inheritdoc}
     * @see Persistable::getQuery()
     */
    public function getQuery ($entity)
    {
        throw new PersistException(self::PERSIST_GETQUERY_UNSUPPORTED);
    }

    /**
     * {@inheritdoc}
     * @see Persistable::getEntity()
     */
    public function getEntity ($entity, array $columns = array())
    {
        throw new PersistException(self::PERSIST_GETENTITY_UNSUPPORTED);
    }

    public function execute ($query, $params = NULL)
    {
        ;
    }
}