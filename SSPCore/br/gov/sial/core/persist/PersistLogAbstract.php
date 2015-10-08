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
namespace br\gov\sial\core\persist;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\Annotation,
    br\gov\sial\core\persist\Persist,
    br\gov\sial\core\BootstrapAbstract,
    br\gov\sial\core\valueObject\ValueObjectAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage persist
 * @name PersistLogAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class PersistLogAbstract extends SIALAbstract
{
    /**
     * Referência do bootstrap
     *
     * @var br\gov\sial\core\BootstrapAbstract
     * */
    public static $bootstrap = NULL;

    /**
     * Referência da Persist
     *
     * @var br\gov\sial\core\persist\database\Persist
     * */
    protected $_persist = NULL;

    /**
     * Usuário executor da operação.
     *
     * @var integer
     * */
    protected $_userid = NULL;

    /**
     * Registra bootstrap.
     *
     * @param br\gov\sial\core\BootstrapAbstract
     * */
    public static function bootstrap (BootstrapAbstract $bootstrap)
    {
        self::$bootstrap = $bootstrap;
    }

    /**
     * Registra o usuário executor da operação.
     *
     * @param integer $userid
     * @return br\gov\sial\core\persist\PersistLogAbstract
     */
    public function registerUser ($userid)
    {
        $this->_userid = $this->_persist->getUserId();
        return $this;
    }

    /**
     * Efetua o registro do log
     *
     * @param ValueObjectAbstract $valueObject
     * @param string $operation
     * @return br\gov\sial\core\persist\PersistLogAbstract
     * @codeCoverageIgnoreStart
     * */
    public abstract function save (ValueObjectAbstract $valueObject, $operation);
    // @codeCoverageIgnoreEnd

    /**
     * Retorna TRUE se o ValueObject representa uma entidade que precise menter historico de seus
     * atributos (log)
     *
     * @param ValueObjectAbstract $valueObject
     * @return boolean
     * @codeCoverageIgnoreStart
     * */
    public abstract function isKeepHistory (ValueObjectAbstract $valueObject);
    // @codeCoverageIgnoreEnd

    /**
     * Recupera o valor recursivamente do metodo do VO informado
     *
     * @param ValueObjectAbstract $value
     * @param string $get
     * @return string
     * */
    public static function getValueRecursively (ValueObjectAbstract $valueObject, $get)
    {
        $value = $valueObject->$get();
        if ($value instanceof  ValueObjectAbstract) {
            $value = self::getValueRecursively($value, $get);
        }
        return $value;
    }

    /**
     * Monta o comando SQL de registro de log
     *
     * @param br\gov\sial\core\valueObject\ValueObjectAbstract $valueObject
     * @param char $operation
     * @return string
     * @codeCoverageIgnoreStart
     * */
    public abstract function makeQuery (ValueObjectAbstract $valueObject, $operation);
    // @codeCoverageIgnoreEnd

    /**
     * Retorna lista de campos que compõe a entidade
     *
     * @param ValueObjectAbstract $valueObject
     * @return string[]
     * */
    public function fetch (ValueObjectAbstract $valueObject)
    {
        $fields = array('field' => array(), 'value' => array());
        $annon  = $valueObject->annotation();
        $infor  = $annon->getClassDoc();
        $attrs  = $annon->getAttrsDoc();

        $list  = 'all' == strtolower($infor['log'])
                 ? $this->fields($annon)
                 : $this->translante($annon, explode(',', $infor['log']))
                 ;

        foreach ($list as $key => $field) {
            $fields['field'][]  = $field;
            $fields['value'][]  = self::getValueRecursively($valueObject, $attrs[$key]['get']);
        }
        return $fields;
    }

    /**
     * Recupera a relação de todos os campos que serão logados, o segundo argumento, se informado, devera' conter
     * uma relação de nomes.
     *
     * @param \br\gov\sial\core\util\Annotation $annon
     * @return string[]
     * @codeCoverageIgnoreStart
     * */
    public abstract function fields (Annotation $annon);
    // @codeCoverageIgnoreEnd

    /**
     * Converte a relação dos atributos que serão logados, nomes atributos do ValueObject, para os nomes corretos
     * de atributos de repositório.
     *
     * @param \br\gov\sial\core\util\Annotation $annon
     * @param string[] $list
     * @return string[]
     * @codeCoverageIgnoreStart
     * */
    public abstract function translante (Annotation $annon, array $list = NULL);
    // @codeCoverageIgnoreEnd

    /**
     * Fábrica de PersistLogAbstract.
     *
     * @static
     * @param \br\gov\sial\core\persist\Persist $persist
     * @return PersistLogAbstract
     * @codeCoverageIgnoreStart
     * */
    public abstract static function factory (Persist $persist);
    // @codeCoverageIgnoreEnd
}