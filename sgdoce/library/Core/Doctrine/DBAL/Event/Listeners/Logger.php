<?php

/**
 * Copyright 2012 do ICMBio
 *
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
use Doctrine\ORM\Events,
    Doctrine\Common\EventSubscriber,
    Doctrine\ORM\Event\LifecycleEventArgs,
    Doctrine\ORM\Query\ResultSetMapping;

/**
 * Listener de conexão Postgres - define determinadas configurações apenas para este banco
 *
 * @package     Core
 * @subpackage  Doctrine
 * @subpackage  DBAL
 * @subpackage  Event
 * @subpackage  Listeners
 * @name        PostgresPathInit
 * @category    Listener
 */
class Core_Doctrine_DBAL_Event_Listeners_Logger implements EventSubscriber
{

    /**
     * Evento de insert conforme definido na entidade
     */
    const EVENT_INSERT = 'insert';

    /**
     * Evento de update conforme definido na entidade
     */
    const EVENT_UPDATE = 'update';

    /**
     * Evento de delete conforme definido na entidade
     */
    const EVENT_DELETE = 'delete';

    /**
     * Verifica se uma coluna contém determinados valores
     * conteudo_coluna(coluna('auditoria', 'teste_aud', 'nome'), xm_trilha) @> '{valor4096, aqui687}'
     */
    const CONTEM = '@>';

    /**
     * Verifica se um conjunto de valores estão contidos em uma coluna
     * '{valor4096, aqui687}' <@ conteudo_coluna(coluna('auditoria', 'teste_aud', 'nome'), xm_trilha)
     */
    const ESTA_CONTIDO = '<@';

    /**
     * Verifica se ao menos um valor de um conjunto é encontrado em uma coluna
     * conteudo_coluna(coluna('auditoria', 'teste_aud', 'nome'), xm_trilha) && '{valor4096, aqui687}'
     * ou
     * '{valor4096, aqui687}' && conteudo_coluna(coluna('auditoria', 'teste_aud', 'nome'), xm_trilha)
     */
    const AO_MENOS_UM = '&&';

    public static $sqOperacao = 'I';

    /**
     *
     * @return type
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove
        );
    }

    /**
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     * @return type
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        if (!$this->isEventActive( $args, self::EVENT_UPDATE )) {
            return;
        }

        self::$sqOperacao = 'U';
        self::createLog( array(), $args );
    }

    /**
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        if (!$this->isEventActive( $args, self::EVENT_INSERT )) {
            return;
        }

        self::$sqOperacao = 'I';
        self::createLog( array(), $args );
    }

    /**
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        if (!$this->isEventActive( $args, self::EVENT_DELETE )) {
            return;
        }

        self::$sqOperacao = 'D';
        self::createLog( array(), $args );
    }

    /**
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     * @param type $event
     * @return boolean
     */
    public function isEventActive(LifecycleEventArgs $args, $event)
    {
        $metadata = $args->getEntityManager()->getClassMetadata( get_class( $args->getEntity() ) );

        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $logger = $reader->getClassAnnotation( $metadata->getReflectionClass(), '\Core\Model\OWM\Mapping\Logger' );

        if ($logger) {
            return in_array( $event, explode( '::', $logger->getEventLog() ) );
        }

        return false;
    }

    /**
     * Metodo que executa a funcao auditoria.trilha_insere
     *
     * sqSistema    = Sigla do sistema
     * $sqClass     = sequencial da class, para indefinido utilize 0 (zero)
     * $sqMetodo    = sequencial do metodo, para indefinido utilize 0 (zero)
     * $noSchema    = Nome do Schema
     * $noTabela    = Nome da tabela
     * $sqUsuario   = Usuario logado
     * $sgOperacao  = I, U, D
     * $columns     = {campo1, campo2, campo3}
     * $values      = {value1, value2, value3}
     * @param type $params
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public static function createLog($params = array(), LifecycleEventArgs $args = NULL)
    {
        // caso nao exista usuario logado, não executa o log.
        if (! \Core_Integration_Sica_User::getUserId()) {
            if (PHP_SAPI != 'cli') {
                $request = \Zend_Controller_Front::getInstance()->getRequest();
                error_log(sprintf('[%s]%s%s%s',
                        'Impossivel criar log de auditoria',
                        'UserId undefined',
                        PHP_EOL,
                        var_export(
                                $params
                                +
                                array(
                                    'noClasse' => $request->getModuleName() . '/' . $request->getControllerName(),
                                    'noMetodo' => $request->getActionName()
                                )
                        ,true)));
            }
            return;
        }

        $sistema  = \Core_Integration_Sica_User::getInfoSystem( \Core_Integration_Sica_User::getUserSystem() );

        if (! $sistema['sgSistema']) {
            if (PHP_SAPI != 'cli') {
                $request = \Zend_Controller_Front::getInstance()->getRequest();
                error_log(sprintf('[%s]%s%s%s',
                        'Impossivel criar log de auditoria',
                        'SgSistema undefined',
                        PHP_EOL,
                        var_export(
                                $params
                                +
                                array(
                                    'noClasse' => $request->getModuleName() . '/' . $request->getControllerName(),
                                    'noMetodo' => $request->getActionName()
                                )
                        ,true)));
            }
            return;
        }

        if ($args instanceof Doctrine\ORM\Event\LifecycleEventArgs) {
            $params = self::getParams( $args );
        }

        $xml = pg_escape_string($params['xmTrilha']);
        $query = 'SELECT auditoria.trilha_insere';
        $query .= "('{$params['sgSistema']}', '{$params['noClasse']}', '{$params['noMetodo']}',
                    '{$params['sqUsuario']}'::int, '{$params['sgOperacao']}','{$params['stUsuarioExterno']}'::boolean,
                    '{$xml}'::xml);";

        \Zend_Registry::get( 'doctrine' )->getEntityManager()->getConnection()->executeQuery( $query );
    }

    /**
     * $sqSistema   = Sigla do sistema
     * $sqClass     = sequencial da class, para indefinido utilize 0 (zero)
     * $sqMetodo    = sequencial do metodo, para indefinido utilize 0 (zero)
     * $noSchema    = Nome do Schema
     * $noTabela    = Nome da tabela
     * $sqUsuario   = Usuario logado
     * $sgOperacao  = I, U, D
     * $columns     = {campo1, campo2, campo3}
     * $values      = {value1, value2, value3}
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    protected static function getParams(LifecycleEventArgs $args)
    {
        $metadata = $args->getEntityManager()->getClassMetadata( get_class( $args->getEntity() ) );
        $sistema  = Core_Integration_Sica_User::getInfoSystem( Core_Integration_Sica_User::getUserSystem() );
        $request  = Zend_Controller_Front::getInstance()->getRequest();

        $params = array(
            //'sqSistema'        => (int)$sistema['sqSistema'],//@todo Ver se este item vai entrar (veio da migração).
            //'sqClasse'         => 0,//@todo Ver se este item vai entrar (veio da migração).
            'sgSistema' => (string) $sistema['sgSistema'],
            'noClasse' => $request->getModuleName() . '/' . $request->getControllerName(),
            'noMetodo' => $request->getActionName(),
            'sqUsuario' => \Core_Integration_Sica_User::getUserId(),
            'sgOperacao' => self::$sqOperacao,
            'stUsuarioExterno' => 0//\Core_Integration_Sica_User::getUserProfileExternal() //@todo Confirmar se é essa informação...
        );

        $columns = array();
        $values = array();

        $associationMaps = array_merge( $metadata->getFieldNames(), $metadata->getAssociationNames() );

        foreach ($associationMaps as $name) {

            $value = $metadata->getFieldValue( $args->getEntity(), $name );
            $column = $metadata->getColumnName( $name );

            if ($metadata->hasAssociation( $name )) {
                $associationMap = $metadata->getAssociationMapping( $name );

                $value = self::validateValue( $value, $args );

                if (isset( $associationMap['sourceToTargetKeyColumns'] )) {
                    $column = key( $associationMap['sourceToTargetKeyColumns'] );
                } else {
                    $column = $associationMap['fieldName'];
                }
            }

            array_push( $columns, $column );
            array_push( $values, $value );
        }

        foreach ($values as $key => $value) {
            if (NULL === $value) {
                $values[$key] = '';
            }

            if (!count( $value )) {
                $values[$key] = '';
            }
        };

        self::trataAlgo( $values );

        $params['xmTrilha'] = self::geraTagXml( $columns, $values, $metadata, $args );

        return $params;
    }

    private static function geraTagXml($columns, $values, $metadata, $args)
    {
        $schema = current( $args->getEntityManager()->getConnection()->getSchemaManager()->getSchemaSearchPaths() );
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $rota = implode( '/', array($request->getModuleName(), $request->getControllerName(), $request->getActionName()) );
        $vCont = 0;

        $xml = new XMLWriter;
        $xml->openMemory();
        $xml->startDocument( '1.0', 'UTF-8' );
        $xml->startElement( "schema" );
        $xml->writeElement( "nome", $schema );
        $xml->writeElement( "rota", $rota );
        $xml->startElement( "tabela" );
        $xml->writeElement( "nome", $metadata->getTableName() );

        foreach ($columns as $colum) {
            $xml->startElement( "coluna" );
            $xml->writeElement( "nome", $colum );
            $xml->writeElement( "valor", $values[$vCont] );
            $xml->endElement();
            $vCont++;
        }
        $xml->endElement();
        $xml->endElement();

        return $xml->outputMemory( true );
    }

    protected static function validateValue($value, $args)
    {
        if (NULL === $value || $value instanceof \Doctrine\ORM\PersistentCollection) {
            return '';
        }

        if ($value instanceof \Zend_Date) {
            return $value->toString( 'Y-M-d H:m:s' );
        }

        if ($value instanceof \DateTime) {
            return $value->format( 'Y-m-d H:i:s' );
        }

        $metadataFk = $args->getEntityManager()->getClassMetadata( get_class( $value ) );
        $identifierValues = $metadataFk->getIdentifierValues( $value );
        $data = $identifierValues ? current( $identifierValues ) : $identifierValues;

        if ($data instanceof \Core_Model_Entity_Abstract) {
            $data = self::validateValue( $data, $args );
        }

        if (is_array( $data )) {
            $array = '[';
            $i = 0;
            foreach ($data as $datum) {
                if ($i++ > 0) {
                    $array .= ',';
                }
                if ($datum instanceof \Core_Model_Entity_Abstract) {
                    $array .= self::validateValue( $datum, $args );
                } else {
                    $array .= $datum;
                }
            }
            $array .= ']';
            $data = $array;
        }

        return $data;
    }

    /**
     * Incialmente, sendo especifico para tratar valores do SARR.
     *
     * @param Object $values
     */
    protected static function trataAlgo(&$values)
    {
        foreach ($values as $key => $value) {
            if (!$value instanceof Doctrine\ORM\PersistentCollection && is_string( $value )) {
                $arrGRU = array(
                    '<span id="instrucaoScdp">',
                    '<span id="instrucaoTelefone">',
                    '<span id="instrucaoProcesso">',
                    '<span id="instrucaoContrato">',
                    '<span id="instrucaoQuantidade">',
                    '<span id="instrucaoArea">',
                    '<span id="instrucaoComprimento">',
                    '<span>', '</span>', '<br>', "\r\n"
                );
                $value = str_replace( $arrGRU, '', $value );
                $values[$key] = (!empty( $value )) ? html_entity_decode( $value ) : $value;
            }
            if($value instanceof \DateTime){
                $values[$key] = $value->format('Y-m-d H:i:s');
            }
            if($value instanceof \Zend_Date){
                $values[$key] = $value->toString( 'Y-M-d H:m:s' );
            }
        }
    }

    /**
     * Metodo que executa consulta na tabela auditoria.trilha_auditoria
     * $params = array(
     *     'paramsColumn' => array(
     *           array(
     *               'column' => 'column1',
     *               'condition' => '=',
     *               'value' => 'value1'
     *           ),
     *           array(
     *               'column' => 'column2',
     *               'condition' => '>',
     *               'value' => 'value2'
     *           )
     *       ),
     *       'paramsXml' => array(
     *           'noSistema' => 'sistema',
     *           'noTabela' => 'tabela',
     *           'noCampo' => 'campo',
     *           'condition' => '@>',
     *           'value' => '{valor4096}'
     *       ),
     *       'order' => array(
     *          'sq_sistema' => 'ASC'
     *       ),
     *       'limit' => '1',
     *       'offset' => '1'
     * );
     * @param type $params
     * @param array $params
     * @return array
     */
    public static function searchLog(array $params)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult( 'sq_sistema', 'sqSistema' );
        $rsm->addScalarResult( 'sq_classe', 'sqClasse' );
        $rsm->addScalarResult( 'sq_metodo', 'sqMetodo' );
        $rsm->addScalarResult( 'no_schema', 'noSchema' );
        $rsm->addScalarResult( 'no_tabela', 'noTabela' );
        $rsm->addScalarResult( 'sq_usuario', 'sqUsuario' );
        $rsm->addScalarResult( 'sg_operacao', 'sgOperacao' );
        $rsm->addScalarResult( 'xm_trilha', 'trilha' );
        $rsm->addScalarResult( 'dt_datahora', 'dtDatahora' );

        $sql = 'SELECT sq_sistema, sq_classe, sq_metodo, no_schema, no_tabela, sq_usuario, sg_operacao, dt_datahora';
        $sql .= ' FROM auditoria WHERE 1=1 ';

        if (isset( $params['paramsColumn'] ) && $params['paramsColumn']) {
            foreach ($params['paramsColumn'] as $value) {
                $sql .= ' AND ' . $value['column'];
                $sql .= ' ' . $value['condition'] . ' ';
                $sql .= "'{$value['value']}'";
            }
        }

        if (isset( $params['paramsXml'] ) && $params['paramsXml']) {
            $trilha = " conteudo_coluna(coluna('noSistema', 'noTabela', 'noCampo'), xm_trilha) ";
            $query = str_replace( array_keys( $params['paramsXml'] ), $params['paramsXml'], $trilha );

            switch (true) {
                case isset( $params['paramsXml']['condition'] ) && $params['paramsXml']['condition'] == self::CONTEM:
                    $sql .= ' AND ' . $query . self::CONTEM . " '{$params['paramsXml']['value']}'";
                    break;

                case isset( $params['paramsXml']['condition'] ) && $params['paramsXml']['condition'] == self::ESTA_CONTIDO:
                    $sql .= " AND '{$params['paramsXml']['value']}' " . self::CONTEM . ' ' . $query;
                    break;

                case isset( $params['paramsXml']['condition'] ) && $params['paramsXml']['condition'] == self::AO_MENOS_UM:
                    $sql .= ' AND ' . $query . self::AO_MENOS_UM . " '{$params['paramsXml']['value']}'";
                    break;

                default:
                    $sql .= ' AND ' . $query . $params['paramsXml']['condition'] . " '{$params['paramsXml']['value']}'";
                    break;
            }
        }

        if (isset( $params['order'] ) && $params['order']) {
            $sql .= ' ORDER BY ' . key( $params['order'] ) . ' ' . current( $params['order'] );
        }

        if (isset( $params['limit'] ) && $params['limit']) {
            $sql .= ' LIMIT ' . $params['limit'];
        }

        if (isset( $params['offset'] ) && $params['offset']) {
            $sql .= ' OFFSET ' . $params['offset'];
        }

        return \Zend_Registry::get( 'doctrine' )->getEntityManager()->createNativeQuery( $sql, $rsm )->getResult();
    }



    public static function getTrilhaAuditoria(array $params)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('sq_auditoria', 'sqAuditoria');
        $rsm->addScalarResult('dt_datahora', 'dtDataHora');
        $rsm->addScalarResult('sq_sistema', 'sqSistema');
        $rsm->addScalarResult('sq_classe', 'sqClasse');
        $rsm->addScalarResult('sq_metodo', 'sqMetodo');
        $rsm->addScalarResult('sq_usuario', 'sqUsuario');
        $rsm->addScalarResult('xm_trilha', 'xmTrilha');
        $rsm->addScalarResult('sg_operacao', 'sgOperacao');
        $rsm->addScalarResult('st_usuario_externo', 'stUsuarioExterno');

        $sql = 'SELECT sq_auditoria, dt_datahora, sq_sistema, sq_classe, sq_metodo, sq_usuario, xm_trilha, sg_operacao, st_usuario_externo';
        $sql .= ' FROM trilha_auditoria WHERE 1=1 ';

        if (isset($params['paramsColumn']) && $params['paramsColumn']) {
            foreach ($params['paramsColumn'] as $value) {
                $sql .= ' AND ' . $value['column'];
                $sql .= ' ' . $value['condition'] . ' ';
                $sql .= "'{$value['value']}'";
            }
        }
        $sql .= ' ORDER BY 1 DESC';
        $sql .= ' LIMIT 20';

        return \Zend_Registry::get('doctrine')->getEntityManager()->createNativeQuery($sql, $rsm)->getResult();
    }

    public function getDadosTabela($tabela, $chave)
    {
        $db = \Zend_Registry::get('doctrine')->getEntityManager()->getConnection();

        $sql = 'SELECT * ';
        $sql .= ' FROM ' . $tabela .' as tabela';
        $sql .= ' WHERE  1=1';

        foreach ($chave as $value) {
            if (!empty($value->valor)){
                $sql .= ' AND ' . $value->nome;
                $sql .= ' = ';
                $sql .= $value->valor;
            }
        }

        $sql .= ' LIMIT 10';

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $dados = $stmt->fetchAll();

        $result = array();
        if (isset($dados[0])) {
            foreach ($dados[0] as $key => $valor) {
                if (is_string($key)) {
                    $result[$key] = $valor;
                }
            }
        }
        ksort($result);
        return $result;
    }

    public function ucName($string) {
        $string =ucwords(strtolower($string));
        $string =implode('', array_map('ucfirst', explode('_', $string)));

        return $string;
    }

}
