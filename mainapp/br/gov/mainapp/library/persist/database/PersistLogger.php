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
use br\gov\sial\core\persist\PersistLogAbstract,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\exception\PersistException;

/**
 * Persistência do log em banco de dados (trilha_auditoria)
 *
 * @package br.gov.mainapp.library.persist
 * @subpackage database
 * @name PersistLogger
 * @author Sósthenes Neto <sosthenes.neto.terceirizado@icmbio.gov.br>
 * */
class PersistLogger extends PersistLogAbstract
{
    /**
     * Resultado do Log de persistência.
     *
     * @var mixed
     */
    protected $_result = NULL;

    const APP_WSDL = 'WSDL';

    /**
     * Registra a classe de persistencia.
     *
     * @param br\gov\sial\core\persist\Persist $persist
     */
    public function setPersist (\br\gov\sial\core\persist\Persist $persist)
    {
        $this->_persist = $persist;
    }

    /**
     * {@inheritdoc}
     */
    public function save (ValueObjectAbstract $valueObject, $operation)
    {
        if (FALSE == $this->isKeepHistory($valueObject)) {
            return;
        }
        $query  = $this->makeQuery($valueObject, $operation);
        $this->_result = $this->_persist->getConnect()->prepare($query)->retrieve();
        return $this;
    }

    /**
     * Monta o comando SQL de registro de log.
     *
     * @param br\gov\sial\core\valueObject\ValueObjectAbstract $valueObject
     * @param char $operation
     * @return string
     */
    public function makeQuery (ValueObjectAbstract $valueObject, $operation)
    {
        $annon  = $valueObject->annotation();
        $infor  = $annon->getClassDoc();
        $fields = $this->fetch($valueObject);

        $module        = self::$bootstrap->request()->getModule();
        $functionality = self::$bootstrap->request()->getFuncionality();
        $action        = self::$bootstrap->request()->getAction();

        $route = implode('/', array(
            $module,
            $functionality,
            $action
        ));

        $userId         = 0;
        $userIsExternal = 0;
        $sgSistema      = NULL;

        $sessionUserData = \br\gov\sial\core\util\Session::getLiveSession('sisicmbio','USER');
        if ($sessionUserData) {
            $userId         = (int) $sessionUserData->sqUsuario;
            $userIsExternal = (int) $sessionUserData->inPerfilExterno;

            if (isset($sessionUserData->sqSistema) && isset( $sessionUserData->sistemas[ $sessionUserData->sqSistema ])) {
                $sgSistema = $sessionUserData->sistemas[ $sessionUserData->sqSistema ]['sgSistema'];
            } else {
                $sgSistema = isset($sessionUserData->sgSistema) ? $sessionUserData->sgSistema : NULL;
            }
        }

        $xml = pg_escape_string(
            $this->_makeXml($infor['schema'], $infor['entity'], $route, $fields['field'],  $fields['value'])
        );

        return sprintf(
            "SELECT auditoria.trilha_insere('%s', '%s', '%s', '%d'::INT, '%s', '%s'::BOOLEAN, '%s'::XML)"
            , $sgSistema
            , "/{$module}/{$functionality}"
            , $action
            , $userId
            , $operation[0]
            , $userIsExternal
            , $xml
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isKeepHistory (ValueObjectAbstract $valueObject)
    {
        $info = $valueObject->annotation()->getClassDoc();
        return isset($info['log']);
    }

    /**
     * {@inheritdoc}
     */
    public function fields (\br\gov\sial\core\util\Annotation $annon, array $list = NULL)
    {
        $fields  = array();
        $lfields = $annon->getAttrsDoc();
        foreach ($lfields as $key => $field) {

            if (isset($field['database'])) {
                $fields[$key] = $field['database'];
            }
        }
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function translante (\br\gov\sial\core\util\Annotation $annon, array $list = NULL)
    {
        $fields  = array();
        $arrList = (array) $annon->getAttrsDoc();
        foreach ($list as $elm) {
            $elm = trim($elm);
            if (isset($arrList[$elm]) && isset($arrList[$elm]['database'])) {
                $fields[$elm] = $arrList[$elm]['database'];
            }
        }
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public static function factory (\br\gov\sial\core\persist\Persist $persist)
    {
        $instance = new self();
        $instance->setPersist($persist);
        return $instance;
    }

    /**
     * Monta o XML que vai para o log
     *
     * @param string $schema
     * @param string $table
     * @param string $route
     * @param array $fields
     * @param array $values
     * @return string
     * @throws PersistException
     */
    private function _makeXml($schema, $table, $route, array $fields, array $values)
    {
        if (count($fields) !== count($values)) {
            throw new PersistException('Parametros incorretos para persistir o log de auditoria');
        }

        $xml = new \XMLWriter;
        $xml->openMemory();
        $xml->startDocument( '1.0', 'UTF-8' );
        $xml->startElement( "schema" );
        $xml->writeElement( "nome", (string) $schema );
        $xml->writeElement( "rota", (string) $route );
        $xml->startElement( "tabela" );
        $xml->writeElement( "nome", (string) $table );
        foreach ($fields as $key => $field) {
            $xml->startElement( "coluna" );
            $xml->writeElement( "nome", $field );
            $xml->writeElement( "valor", $values[$key] );
            $xml->endElement();
        }
        $xml->endElement();
        $xml->endElement();
        return $xml->outputMemory( true );
    }

}