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
namespace br\gov\sial\core\persist\database\sqlite;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\persist\Persistable,
    br\gov\sial\core\persist\database\ResultSet,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\persist\database\Persist as PDatabase
    ;

/**
 * SIAL
 *
 * Persistência em banco de dados Sqlite
 *
 * @package br.gov.sial.core.persist.database.sqlite
 * @subpackage sqlite
 * @name Persist
 * @author bruno menezes <bruno.menezes@icmbio.gov.br>
 * */
class Persist extends SIALAbstract implements Persistable
{
    /**
     * @var string
     * */
    const PERSIST_PRIMARY_KEY_NOT_FOUND = 'Nenhuma chave primária foi encontrada na entidade "%s". Certifique-se que o annotation do valueObject está definido corretamente.';

    /**
     * @var string
     * */
    const PERSIST_UNAVAILABLE_FILTER = "Não foi possível definir nenhum filtro para atulização da entidade %s";

    /**
     * @var string
     * */
    const PERSIST_UNAVAILABLE_DELETE_FILTER = "Não possível definir nenhum filtro de exclusão na entidade %s";

    /**
     * @var \br\gov\sial\core\persist\database\Persist
     * */
    private $_persist = NULL;

    /**
     * Construtor.
     *
     * @param br\gov\sial\core\persist\database\Persist $persist
     * */
    public function __construct (PDatabase $persist)
    {
        $this->_persist = $persist;
    }

    /**
     * Executa consulta de forma direta.
     *
     * @param string $query
     * @param string[] $params
     * @return \br\gov\sial\core\persist\ResultSet
     * @throws \br\gov\sial\core\persist\exception\PersistException
     * */
    public function execute ($query, $params = NULL)
    {
        return $this->_persist->getConnect()->prepare($query, $params)->retrieve();
    }

    /**
     * Recupera registro com base em seu ID
     *
     * <b>Nota</b>: Este método suporta pesquisa apenas com chave simples, ou seja, nao é suportado
     * chave composta por mais de um campo. Se necessário, este metodo deverá ser especializado
     *
     * @param integer $key
     * @return \br\gov\sial\core\persist\ResultSet
     * @throws \br\gov\sial\core\persist\exception\PersistException
     * */
    public function find ($key)
    {
        $annon = $this->_persist
                      ->annotation()
                      ->load();

        $tmpFilter = NULL;

        # note que este metodo filtra apenas por uma PK, caso sua entidade possua mais de uma
        # sobrescreva este metodo adequando-o a quantidade de pk
        foreach ($annon->attrs as $field) {
            # - - - - - - - - - - - - - - -[ NOTA ]- - - - - - - - - - - - - - -
            # para um mehlhor desempenho deste metodo, defina sempre as PK nos primerios atributos
            # de cada um dos valueObject
            if (isset($field->primaryKey)) {
               $tmpFilter = sprintf('%1$s = :%1$s', $field->database);
               $params[$field->database]        = new \stdClass();
               $params[$field->database]->type  = $field->type;
               $params[$field->database]->value = $key;
               // @codeCoverageIgnoreStart
               break;
               // @codeCoverageIgnoreEnd
            }
        }

        PersistException::throwsExceptionIfParamIsNull($tmpFilter, self::PERSIST_PRIMARY_KEY_NOT_FOUND);
        $query = sprintf('%s WHERE %s', self::_buildBasicQuery($annon), $tmpFilter);
        return $this->_persist->getConnect()->prepare($query, $params)->retrieve();
    }

    /**
     * Efetua a pesquisa de todos os registro da entidade.
     *
     * @return ResultSet
     * @throws PersistException
     * */
    public function findAll ()
    {
        try {
            $entity = $this->_persist->annotation()->load()->class;
            return $this->findByParam(new  $entity, 'ALL');
        // @codeCoverageIgnoreStart
        } catch (\PDOException $pExc) {
            throw new PersistException($pExc->getMessage(), 0, $pExc);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Efetua pesquisa parametrizada.
     *
     * Se informado, o segundo parâmentro $limit define o numero de resultado que será retornado. O terceiro parâmentro
     * $offSet que também é opcional define o registro inicial que sera contato o limite
     *
     * @param ValueObjectAbstract $valueObject
     * @param integer $limit
     * @param integer $offSet
     * @return Persist
     * */
    public function findByParam (ValueObjectAbstract $valueObject, $limit = NULL, $offSet = 0)
    {
        $annon = $this->_persist
                      ->annotation()
                      ->load();

        # filtro de pesquisa
        $tmpFilter = NULL;

        # esta tecnica foi empregada para definir que o primeiro operador deve ser um WHERE
        # e os demais um AND sem ficar efetuando um IF/ELSE a cada passada do foreach
        $tmpOperator = array('WHERE', 'AND');

        # recupera a query basica de pesquisa
        $tmpQuery = self::_buildBasicQuery($annon);

        # inicializa os parametros
        $params = NULL;

        # REGRA: extra filtros do valueObject, cada um dos campos contendo valor diferente de nulo
        # sera usado como paramento
        foreach ($annon->attrs as $field) {

            # verifica na anotacao se existe referencia do atributo para banco de dados
            if (!isset($field->database)) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }

            # recupera o nome do metodo acessor que recupera o valor do atributo no valueObject
            $get = $field->get;
            $value = $valueObject->$get();

            # se value for um valueObject entao busca pelo metodo de mesmo nome soh que valueObject
            if($value instanceof ValueObjectAbstract) {
                $value = $value->$get();
            }

            # o campo so entra para relacao de filtros se for avaliado como diferente de NULL
            # informe a string 'NULL' para informar um filtro NULL
            if (NULL == $value) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }

            $params[$field->database] = self::_getValue($valueObject, $field->get, $field->type);
            $tmpFilter .= sprintf(' %1$s %2$s = :%2$s', $tmpOperator[(bool) $tmpFilter], $field->database);
        }

        # neste ponto deve ser verificao se eh para permitir a execucao de pesquisa completa,
        # ou seja, sem que nenhum filtro seja aplicado
        if (NULL == $tmpFilter) {
            ; # verificar com o marcone se eh para permitir
        }

        # alica o filtra ao consulta
        $tmpQuery .= $tmpFilter;

        return $this->_persist->getConnect()->prepare($tmpQuery, $params)->retrieve();
    }

    /**
     * Persiste dados no repositório.
     *
     * @param ValueObjectAbstract $valueObject
     * @return br\gov\sial\core\persist\Persist
     * @throws br\gov\sial\core\persist\exception\PersistException
     * */
    public function save (ValueObjectAbstract $valueObject)
    {
        $annon       = $valueObject->annotation()->load();
        $queryFields = array();

        foreach ($annon->attrs as $field) {
            if (!isset($field->database)) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }

            # ignora campos autoincremento
            if (isset($field->primaryKey) && isset($field->autoIncrement)) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }

            array_push($queryFields, $field->database);
            $params[$field->database] = self::_getValue($valueObject, $field->get, $field->type);
        }

        # certifica-se de que foi encotnrado algum campo
        $message = "Nenhum campo foi encontrado para o INSERT de '{$annon->entity}'";
        PersistException::throwsExceptionIfParamIsNull(sizeof($queryFields), $message);

        # monta o comando de insert
        $query = sprintf('INSERT INTO %s(%s) VALUES(:%s)', "{$annon->entity}"
                                                         , implode(', ', $queryFields)
                                                         , implode(', :', $queryFields)
        );

        $this->_persist->getConnect()->prepare($query, $params)->update();
    }

    /**
     * Altera dados no repositório.
     *
     * @param ValueObjectAbstract $valueObject
     * @return br\gov\sial\core\persist\Persist
     * @throws PersistException
     * */
    public function update (ValueObjectAbstract $valueObject)
    {
        $annon       = $valueObject->annotation()->load();
        $tmpFilter   = NULL;
        $tmpQuery    = NULL;

        # tecnica explicada no metodo findByParam
        $tmpOperator = array('WHERE', 'AND');

        foreach ($annon->attrs as $field) {

            if (!isset($field->database)) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }

            if (isset($field->primaryKey)) {
                $tmpFilter .= sprintf(' %1$s %2$s = :%2$s', $tmpOperator[(bool) $tmpFilter], $field->database);
            } else {
                $tmpQuery .= sprintf('%1$s = :%1$s, ', $field->database);
            }

            $params[$field->database] = self::_getValue($valueObject, $field->get, $field->type);
        }

        # por padrao nao eh possivel atualizar toda a entidade, se realmente  for necessario realizar
        # esta operacao sobreescreve este metodo
        PersistException::throwsExceptionIfParamIsNull($tmpFilter, sprintf(self::PERSIST_UNAVAILABLE_FILTER, $annon->entity));

        $query = sprintf('UPDATE %s SET %s%s', $annon->entity, substr($tmpQuery, 0, -2), $tmpFilter);
        $this->_persist->getConnect()->prepare($query, $params)->update();
    }

    /**
     * Deleta dados no repositório.
     *
     * @param ValueObjectAbstract $valueObject
     * @return br\gov\sial\core\persist\Persist
     * @throws PersistException
     * */
    public function delete (ValueObjectAbstract $valueObject)
    {
        $annon       = $valueObject->annotation()->load();
        $tmpFilter   = NULL;

        # tecnica explicada no metodo findByParam
        $tmpOperator = array('WHERE', 'AND');

        foreach ($annon->attrs as $field) {

            if (!isset($field->database) || !isset($field->primaryKey)) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }

            $tmpFilter .= sprintf(' %1$s %2$s = :%2$s', $tmpOperator[(bool) $tmpFilter], $field->database);
            $params[$field->database] = self::_getValue($valueObject, $field->get, $field->type);
        }

        # por padrao nao eh possivel atualizar toda a entidade, se realmente  for necessario realizar
        # esta operacao sobreescreve este metodo
        PersistException::throwsExceptionIfParamIsNull($tmpFilter, sprintf(self::PERSIST_UNAVAILABLE_DELETE_FILTER, $annon->entity));

        $query = sprintf('DELETE FROM %s %s', $annon->entity, $tmpFilter);
        $this->_persist->getConnect()->prepare($query, $params)->update();
    }

    /**
     * Recupera o valor do valueObject baseado na anotação para ser usado na composição do comando SQL
     *
     * @param ValueObjectAbstract $valueObject
     * @param string $accessorMethod
     * @param string $dataType
     * @return \stdClass
     * */
    private static function _getValue (ValueObjectAbstract $valueObject, $accessorMethod, $dataType)
    {
        $tmpValue = $valueObject->$accessorMethod();

        # @todo trocar por recurssao
        $tmpValue = ($tmpValue instanceof ValueObjectAbstract) ? $tmpValue->$accessorMethod() : $tmpValue;

        # @todo esta validacao irah para os validadores de tipos
        switch (substr($dataType, 0, 4)) {
            case 'bool':
                // @codeCoverageIgnoreStart
                $tmpValue = $tmpValue ? 'TRUE' : 'FALSE';
                break;
                // @codeCoverageIgnoreEnd

            case 'inte':
                $tmpValue = (integer) $tmpValue;
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'stri':
                $tmpValue = (string) $tmpValue;
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd
        }

        $tmpValue = ('NULL' == $tmpValue) ? NULL : $tmpValue;

        $params = new \stdClass();
        $params->value = $tmpValue;
        $params->type  = $dataType;
        return $params;
    }

    /**
     * Retorna a relação dos campos da entidade.
     *
     * @param \stdClass $annon
     * @return string
     * */
    protected static final function _getEntityFields (\stdClass $annon)
    {
        $tmpFieldList = array();
        foreach ($annon->attrs as $field) {
            if (isset($field->database)) {
                $tmpFieldList[] = "{$annon->entity}.{$field->database}";
            }
        }
       return implode(', ', $tmpFieldList);
    }

    /**
     * Cria comando de consulta (SELECT)
     *
     * @param \stdClass $annon
     * @return string
     * */
    protected static function _buildBasicQuery (\stdClass $annon)
    {
        # armazena o comando de consulta
        $tmpQuery = 'SELECT ';

        # monta o nome da entidade, caso o drvier do banco nao deh suprote a namespace
        # especialize este a classe na pasta de driver especifica
        $tmpEntity = $annon->entity;

        # monta a lista de campos
        $tmpQuery .= self::_getEntityFields($annon);

        # informa a entity
        $tmpQuery .= " FROM {$tmpEntity}";
        return $tmpQuery;
    }

    /**
     * {@inheritdoc}
     * @see br\gov\sial\core\persist.Persistable::getQuery()
     * @todo implementar Query tambem para SQLite
     * */
    // @codeCoverageIgnoreStart
    public function getQuery ($entity)
    {
        ;
    }

    /**
     * {@inheritdoc}
     * @see br\gov\sial\core\persist.Persistable::getEntity()
     * @todo implementar Query tambem para SQLite
     */
    public function getEntity ($entity, array $columns = array())
    {
        ;
    }
    // @codeCoverageIgnoreEnd
}