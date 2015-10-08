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
namespace br\gov\sial\core\persist\database\dml;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\persist\database\Persist,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\database\dml\exception\DMLException;

/**
 * SIAL Persist DML
 *
 * @package br.gov.sial.core.persist.database
 * @subpackage dml
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class DMLAbstract extends SIALAbstract
{
    /**
     * @var string
     * */
    const T_DMLABSTRACT_UNSUPPORTED_OPERATION = 'A operação informada "%s" não é suportada';

    /**
     * @var string
     * */
    const T_DMLABSTRACT_PRIMARYKEY_REQUIRED  = 'Só é permitida a alteracao de forma automatica se houver uma primaryKey na entidade';

    /**
     * @var string
     * */
    const T_DMLABSTRACT_FILTER_REQUIRED = 'Só é permitido remover registro informando ao menos um parâmetro como filtro';

    /**
     * @param Persist
     * */
    protected $_persist;

    /**
     * retorna representacao textual do comando para salvar os dados
     *
     * @param ValueObjectAbstract
     * @return string
     * */
    public abstract function save (ValueObjectAbstract $valueObject);

    /**
     * retorna representacao textual do comando para alterar os dados
     *
     * @param ValueObjectAbstract
     * @return string
     * */
    public abstract function update (ValueObjectAbstract $valueObject);

    /**
     * retorna representacao textual do comando para remover os dados
     *
     * @param ValueObjectAbstract
     * @return string
     * */
    public abstract function delete (ValueObjectAbstract $valueObject);

    /**
     * retorna representacao textual do comando para persistir os dados,
     * nesse contexto, persistir indicar salvar ou alterar os dados.
     * sendo que na primeira hipotese apenas se o registro ainda nao exisitir
     *
     * @param ValueObjectAbstract
     * @return string
     * */
    public abstract function persist (ValueObjectAbstract $valueObject);

    /**
     * Retorna todos os atributos do valueObject que podem ser utilizados na camada de persistência, definida por
     * 'self::PERSIST_TYPE'. Opcionalmente, o tipo da operacao podera ser informada (insert, update, delete). Nota:
     * Por padrao os atributos selecionados serao validos para pesquisa.
     *
     * @param ValueObjectAbstract $valueObject
     * @param string $operType
     * @return \stdClass
     * @throws PersistException
     * */
    public function persistAttr (ValueObjectAbstract $valueObject, $operType = NULL)
    {
        $annon     = $valueObject->annotation()->load();

        $list      = array();

        if (NULL !== $operType) {

            $method = '_persistAttr' . ucfirst(strtolower($operType));

            $message = sprintf(self::T_DMLABSTRACT_UNSUPPORTED_OPERATION, $method);

            DMLException::throwsExceptionIfParamIsNull($this->hasMethod($method), $message);
        }

        foreach ($annon->attrs as $attr) {

            # verifica se o attr pode ser manipulado pela camada de persistencia
            if (FALSE == $this->_persist->isAttrPersistable($attr, $this->_persist->adapter())) {

                 continue;

            }

            if (NULL !== $operType) {
                $this->$method($list, $attr, $valueObject);
            }
         }

        return $list;
    }

    /**
     * PersistAttr para insert
     *
     * @param array &$list
     * @param string $attr
     * @param ValueObjectAbstract $valueObject
     * @throws DMLException
     * */
    private function _persistAttrInsert (array &$list, $attr, $valueObject)
    {
        $getter = self::getIfDefined($attr, 'get');

        $type = strtolower(self::getIfDefined($attr, 'type'));

        $value = $valueObject->$getter();

        $isRequired = 'false' == strtolower(
            self::getIfDefined($attr, 'nullable')
        ) ? TRUE : FALSE;

        $isPrimaryKey = 'true'  == strtolower(
            self::getIfDefined($attr, 'primaryKey')
        ) ? TRUE : FALSE;

        # considera chave-primaria apenas se um valor for
        # definido explicitamente
        if (empty($value) && TRUE === $isPrimaryKey) {
            return;
        }

        # critica campo requerido sem valor
        DMLException::throwsExceptionIfParamIsNull(
            !(
                NULL === $value && TRUE  === $isRequired
            ), sprintf(Persist::PERSIST_ATTR_REQUIRED, $attr->name)
        );

        $list[] = $attr;
    }

    /**
     * PersistAttr para select
     *
     * @param array &$list
     * @param string $attr
     * @param ValueObjectAbstract $valueObject
     * */
    private function _persistAttrSelect (array &$list, $attr, $valueObject)
    {
        $list[] = $attr;
    }

    /**
     * PersistAttr para update
     *
     * @param array &$list
     * @param string $attr
     * @param ValueObjectAbstract $valueObject
     * */
    private function _persistAttrUpdate (array &$list, $attr, $valueObject)
    {
        $this->_persistAttrSelect($list, $attr, $valueObject);
    }

    /**
     * PersistAttr para delete
     *
     * @param array &$list
     * @param string $attr
     * @param ValueObjectAbstract $valueObject
     * */
    private function _persistAttrDelete (array &$list, $attr, $valueObject)
    {
        $list[] = $attr;
    }

    /**
     * @param Persist
     * @return DMLAbstract
     * */
    public static function factory ($persist)
    {
        # @todo validar se adaptador eh do tipo database
        ;

        $namespace = __NAMESPACE__
                   . self::NAMESPACE_SEPARATOR
                   . $persist->driver()
                   . self::NAMESPACE_SEPARATOR
                   . 'DML';

        $dml = $namespace::factory();
        $dml->_persist = $persist;

        return $dml;
    }
}