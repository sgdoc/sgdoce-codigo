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
namespace br\gov\sial\core\persist\database;
use br\gov\sial\core\persist\PersistConfig,
    br\gov\sial\core\persist\database\Config,
    br\gov\sial\core\persist\database\ResultSet,
    br\gov\sial\core\persist\Connect as ParentConnect,
    br\gov\sial\core\persist\exception\PersistException;

/**
 * SIAL
 *
 * Componente de conexão com o respositório
 *
 * @package br.gov.sial.core.persist
 * @subpackage database
 * @name Connect
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class Connect extends ParentConnect
{
    /**
     * @var string
     * */
    const TRANSATION_INIT_ERROR = 'Não foi possível iniciar transação.';

    /**
     * @var string
     * */
    const TRANSATION_COMMIT_ERROR = 'Não foi possível realizar o commit dos dados.';

    /**
     * @var string
     * */
    const TRANSATION_ROLLBACK_ERROR = 'Não foi possível desfazer a operação.';

    /**
     * @var \PDOStatement
     * */
    protected $_statement = NULL;

    /**
     * @var Annotation
     * */
    public static $annotation = NULL;

    /**
     * @var string[]
     *
     * @refer http://br2.php.net/manual/pt_BR/pdo.constants.php
     * */
    private static $_acceptedDataType = array(
        # string type
        'string'  => \PDO::PARAM_STR,
        'text'    => \PDO::PARAM_STR,
        'decimal' => \PDO::PARAM_STR,
        'float'   => \PDO::PARAM_STR,
        'date'    => \PDO::PARAM_STR,

        # integer type
        'datetime'=> \PDO::PARAM_INT,
        'integer' => \PDO::PARAM_INT,
        'time'    => \PDO::PARAM_INT,

        # boolean type
        'bool'    => \PDO::PARAM_BOOL,
        'boolean' => \PDO::PARAM_BOOL,

        'null'    => \PDO::PARAM_NULL,
        'lob'     => \PDO::PARAM_LOB
    );

    /**
     * Construtor.
     *
     * @param persistConfig $config
     * @throws PersistException
     * */
    public function __construct (Config $config)
    {
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}, tais como: Delete, Update, Insert, Create, etc.
     *
     * @throws PersistException
     * */
    public function update ()
    {
        try{
            if (TRUE == self::$autoCommit) {
                $this->_autoCommitExecute();
            } else {
                $this->_statement->execute();
            }
        } catch (\PDOException $pExc) {
            # grava log ocorrido no repositorio
            ;

            // @codeCoverageIgnoreStart
            throw new PersistException($pExc->getMessage(), 0, $pExc);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @throws PersistException
     * */
    private function _autoCommitExecute ()
    {
        try {
            $this->transaction();
            $this->_statement->execute();
            $this->commit();
        } catch (\PDOException $pExc) {
            // @codeCoverageIgnoreStart
            $this->rollback();

            # grava log ocorrido no repositorio
            ;

            throw new PersistException($pExc->getMessage(), 0);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return ResultSet
     * */
    public function retrieve ()
    {
        try {
            $this->_statement->execute();
        } catch (\PDOException $pExc) {
            # foi analisado e constatado que mesmo o php passando por este bloco
            # o coverage nao cobtabilizou por mais de uma vez sua passagem aqui

            # grava log ocorrido no repositorio

            // @codeCoverageIgnoreStart
            throw $pExc;
            // @codeCoverageIgnoreEnd
        }
        return new ResultSet($this->_statement);
    }

    /**
     * Prepara o comando que será executado no repositório.
     *
     * @param string $query
     * @param stdClass[] $params
     * @return Connect
     * */
    public function prepare ($query, $params = NULL)
    {
        try {
            $params           = $this->toggle($params, array());
            $this->_statement = $this->_resource->prepare($query);

            foreach ($params as $key => $param) {
                # converte o texto 'NULL' para o valor real NULL
                if ('NULL' === $param->value && 'boolean' != $param->type) {
                    $param->value = NULL;
                    $param->type  = 'null';
                }

                $this->_statement->bindValue($key, $param->value,
                        isset(self::$_acceptedDataType[$param->type])
                           ?  self::$_acceptedDataType[$param->type]
                           :  self::$_acceptedDataType['string']
                );
            }

            return $this;
        } catch (\PDOException $pExc) {
            throw new PersistException($pExc->getMessage(), 0);
        }
    }

    /**
     * {@inheritdoc}
     * */
    public function hasTransactionRunning ()
    {
        return (boolean) $this->_resource->inTransaction();
    }

    /**
     * Inicializa transação.
     *
     * @return Persist
     * @throws PersistException
     * */
    public function transaction ()
    {
        PersistException::throwsExceptionIfParamIsNull($this->_resource->beginTransaction(), self::TRANSATION_INIT_ERROR);
        return $this;
    }

    /**
     * Grava todos os dados pendentes e finaliza transação em curso.
     *
     * @return Persist
     * @throws PersistException
     * */
    public function commit ()
    {
        PersistException::throwsExceptionIfParamIsNull($this->_resource->commit(), self::TRANSATION_COMMIT_ERROR);
        return $this;
    }

    /**
     * Descarta toda as operações pendentes e desfaz a transação.
     *
     * @return Persist
     * @throws PersistException
     * */
    public function rollback ()
    {
        PersistException::throwsExceptionIfParamIsNull($this->_resource->rollBack(), self::TRANSATION_ROLLBACK_ERROR);
        return $this;
    }

    /**
     * Fecha o cursor do statement.
     *
     * @return br\gov\sial\core\persist\Persist
     * */
    public function closeCursor ()
    {
        $this->_statement->closeCursor();
        return $this;
    }

    /**
     * Fábrica de Connect.
     *
     * @param PersistConfig $config
     * @return Connect
     * @throws PersistException
     * */
    public static function factory (PersistConfig $config)
    {
        $hash = $config->hash();
        if (!isset(self::$_instance[$hash])) {
            $namespace = sprintf('%1$s%2$s%3$s%2$sConnect', __NAMESPACE__, self::NAMESPACE_SEPARATOR, $config->get('driver'));
            self::$_instance[$hash] = new $namespace($config);
        }
        return self::$_instance[$hash];
    }
}