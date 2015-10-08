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
namespace br\gov\sial\core\util;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\persist\Connect,
    br\gov\sial\core\util\Annotation,
    br\gov\sial\core\persist\cache\Cache,
    br\gov\sial\core\persist\PersistConfig,
    br\gov\sial\core\exception\IOException,
    br\gov\sial\core\persist\meta\MetaAbstract;

/**
 * SIAL
 *
 * manipulacao de cache de anotacao
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @author J. Augusto <augustowebd@gmail.com>
 * @todo refatorar para usar a classe File na geracao/carregamento do arquivo
 * */
class AnnotationCache extends SIALAbstract
{
    /**
     * @var string
     * */
    const T_ANNONTATIONCACHE_UNABLE_TO_WRITE_CACHE = 'Falta permissão de gravação na pasta de cache';

    /**
     * @var stirng
     * */
    const T_ANNONTATIONCACHE_UNABLE_TO_GET_CLASS = 'Não foi possível recuperar o nome da classe';

    /**
     * define o diretorio dos arquivos de cache
     *
     * @var string
     * */
    public static $cacheDir;

    /**
     * @var Config
     * */
    public static $persistConfig = NULL;

    /**
     * define a extensao dos arquivos de cache
     *
     * @var string
     * */
    public static $cacheExt = '.cache';

    /**
     * cachea a anotacao informada
     *
     * @param Annotation|stdClass $annon
     * @return AnnotationCache
     * */
    public function save ($annon)
    {
        # verifica se o cache é antigo
        if ($this->cacheOldest($annon)) {

            # informacoes coletadas do ValueObject
            $data = json_decode(self::encodeAnnotation($annon) );

            # verifica a existência de chave-estrageira
            $foreignKeys = $this->safeToggle($data, 'foreignKey', array());
            foreach ($foreignKeys as $foreignKey) {
                $fAnnon = $this->makeForeignAnnon($data, $foreignKey);
                $this->write($fAnnon);
            }

            $this->write($data);
        }

        return $this;
    }

    /**
     * grava o arquivo de cache
     *
     * @param stdClass $data
     * @throws IOException
     * */
    public function write (\stdClass $data)
    {
        # define o nome do arquivo que armazenará o cache
        $cacheFileName = self::cacheFileName($data);

        # verifica se é possível gravar na pasta de cache
        IOException::throwsExceptionIfParamIsNull(
            is_writable(dirname($cacheFileName)),
            self::T_ANNONTATIONCACHE_UNABLE_TO_WRITE_CACHE
        );

        # verifica se foi informando um dataSourceName para obter as metas-informacoes
        if ($metaDataSourceName = $this->safeToggle($data, 'persist', NULL)) {
            $this->meta($data, $metaDataSourceName);
        }

        IOException::throwsExceptionIfParamIsNull(
            file_put_contents($cacheFileName, json_encode($data)),
            'Não foi possível gravar o cache solicitado'
        );
    }

    /**
     * monta o objeto de anotacao da chefe estrangeira
     * @param stdClass $annon
     * @param stdClass $foreignKey
     * @return stdClass
     * */
    public function makeForeignAnnon (\stdClass $annon, \stdClass $foreign)
    {
        $fAnnon = new \stdClass;

        # recupera informacoes do value object
        $refer = self::referData($foreign->refer, $annon);

        # define o namespace da classe estrangeira
        $fAnnon->class   = $refer->namespace;
        $fAnnon->attrs   = new \stdClass;
        $fAnnon->persist = $refer->persist;
        $fAnnon->schema  = $refer->schema;
        $fAnnon->entity  = $refer->entity;

        return $fAnnon;
    }

    /**
     * converte a assinatura de ferignKey em informacoes
     *
     * @param string $foreign
     * @param stdClass $parentAnnon
     * @return stdClass
     * */
    public static function referData ($foreignAssign, $parentAnnon = NULL)
    {
        $result = new \stdClass;
        # recupera informacoes do value object
        $pattern = '/(?P<namespace>[^:]+):+(?P<refer>\w+)@(?<schema>\w+)\.(?<entity>\w+)(?::(?P<persist>\w+))?$/';
        preg_match_all($pattern, $foreignAssign, $matches, PREG_SET_ORDER);

        $result->namespace = self::safeToggle($matches[0], 'namespace', NULL);
        $result->schema    = self::safeToggle($matches[0], 'schema', NULL);
        $result->entity    = self::safeToggle($matches[0], 'entity', NULL);
        $result->refer     = self::safeToggle($matches[0], 'refer', NULL);
        $result->persist   = self::safeToggle($matches[0], 'persist', self::safeToggle((object) $parentAnnon, 'persist', NULL));

        return $result;
    }

    /**
     * recupera meta informacao do valueObject informado
     *
     * @param stdClass $annon
     * @param string $dataSource
     * */
    public function meta (\stdClass $annon, $dataSource)
    {
        $config = PersistConfig::factory($dataSource, self::$persistConfig->toArray());
        $data   = MetaAbstract::factory(Connect::factory($config))->data($this->safeToggle($annon, 'schema', NULL), $this->safeToggle($annon, 'entity', NULL));
        $this->merge($annon, $data);
    }

    /**
     * junta dados da anontação com os do bando de dados
     *
     * @param stdClass $annon
     * @param stdClass[] $meta
     * @return stdClass
     * */
    public function merge (&$annon, $meta)
    {
        foreach ($meta as $node => $info) {

            $attr = &$annon->attrs->$node ?: new \stdClass;

            # Column name
            $attr->name = $node;

            # Database Column Name
            $attr->database = $info->column_name;

            # Column Type
            $attr->type = $info->column_type;

            # Column size
            $attr->size = $info->column_data_size;

            # Column can write
            $attr->canWrite = 'YES' == $info->column_canwrite;

            # Nullable
            $attr->nullable = 'YES' == $info->column_nullable;

            # Default value
            if  ($default = ($info->column_default_value ?: $this->safeToggle($attr, 'defaultValue', NULL))){
                $attr->defaultValue = $default;
            }

            # Primary Key
            if ('{primaryKey}' == $info->column_constraint_type) {
                $attr->primaryKey = TRUE;
            }

            # Foreign key
            if ('{foreignKey}' == $info->column_constraint_type) {
                $attr->foreignKey = str_replace(array('"', '{', '}'), NULL, $info->column_constraint_refer);
                $attr->foreignKey = self::foreignGetMethod($annon, $attr->foreignKey);
            }
        }
    }

    /**
     * gera o metodo foreign_get do attr informado
     *
     * @param stdClass $annon
     * @param string $forign
     * @return string
     * */
    public static function foreignGetMethod ($annon, $foreign)
    {
        $fkey               = current(array_filter(explode(')', $foreign)));
        list($refer, $attr) = explode(',', str_replace('(', '', $fkey));

        $getter = 'get';

        # corrige o nome do attr
        $attr     = implode(array_map(function ($frag) { return ucfirst($frag); },explode('_', $attr)));
        $getter  .= $attr;
        $attr[0]  = strtolower($attr[0]);

        # separador do namespace
        $sep = 'valueObject';

        # corrige nome do valueObject
        $refer = implode(array_map(function ($frag) { return ucfirst($frag);}, explode('_', $refer)));

        # define o nome do VORefer
        $namespace  = current(explode($sep, $annon->class)) . sprintf('%s\%sValueObject', $sep, ucfirst($refer));

        # corrige o nome do modulo no namespace
        list($namespace, $module) = explode('\\valueObject\\', $namespace);
        $namespace = explode('\\', $namespace);
        array_pop($namespace);
        $refer[0]  = strtolower($refer[0]);
        array_push($namespace, $refer, $sep, $module);

        return array(
            'attr'   => $attr,
            'getter' => $getter,
            'refer'  => implode('\\', $namespace)
        );
    }

    /**
     * retorna o nome do arquivo de cache
     *
     * @param Annotation $annon
     * @return string
     * @throws IOException
     * */
    public static function cacheFileName ($annon)
    {

        $class = NULL;
        if ($annon instanceof Annotation) {
            $class = $annon->getClassName();
        } elseif ($annon instanceof \stdClass) {
            $class = $annon->class;
        } elseif (is_string($annon)) {
            /* quando tratar de um namespace: situacao que ocorre na montagem da query::find */
            $class = $annon;
        } else {
            throw new IOException(self::T_ANNONTATIONCACHE_UNABLE_TO_GET_CLASS);
        }

        $class = implode('_', array_filter(explode(self::NAMESPACE_SEPARATOR, $class)));
        return (self::$cacheDir ?: sys_get_temp_dir()) . DIRECTORY_SEPARATOR . $class . self::$cacheExt;
    }

    /**
     * retorna TRUE se o cache da anotacao informada for mais antivo
     * que o arquivo que origina o cache
     *
     * @return boolean
     * */
    public function cacheOldest ($annon)
    {
        # devido a comparacao de arquivos em um curto espaco de tempo, e necessario limpar o cache
        clearstatcache();

        $cacheFileName = self::cacheFileName($annon);

        # tao logo a classe File fique pronta este metodo deve ser refatorado
        $tmpCacheBorn = 0;
        $tmpClassBorn = filemtime($annon->getFileName());

        if (is_file($cacheFileName)) {
            $tmpCacheBorn = filemtime($cacheFileName);
        }

        return ($tmpClassBorn >= $tmpCacheBorn);
    }

    /**
     * Monta o conteudo do arquivo de cache
     *
     * @param Annotation $annon
     * @return string
     * */
    public function encodeAnnotation (Annotation $annon)
    {
        $content          = array('class' => self::NAMESPACE_SEPARATOR . $annon->getClassName());
        $tmpEntity        = $annon->getClassDoc();
        $content['attrs'] = $annon->getAttrsDoc();

        foreach ($tmpEntity as $key => $value) {
            $content[$key] = $value;
        }

        return json_encode($content);
    }

    /**
     * carrega e retorna o conteudo do arquivo de cache
     *
     * @param stdClass|string $annon
     * @return stdClass
     * */
    public function load ($annon)
    {
        $content   = new \stdClass;
        $fileCache = self::cacheFileName($annon);

        if (is_file($fileCache)) {
            $content = json_decode(file_get_contents($fileCache));
        };

        return $content;
    }
}