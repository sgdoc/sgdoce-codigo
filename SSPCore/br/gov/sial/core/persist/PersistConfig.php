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
use br\gov\sial\core\util\ConfigIni,
    br\gov\sial\core\util\Config as ParentConfig,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage persist
 * @name ConfigAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class PersistConfig extends ParentConfig
{
    /**
     * @var string
     * */
    const PERSISTCONFIG_ENVIRONMENT_CONST_UNAVAILABLE = 'Não foi encontrado a definição do ambiente. "APPLICATION_ENV"';

    /**
     * @var string
     * */
    const PERSISTCONFIG_UNDEFINED_DEFAULT_DATASOURCE = 'Não foi definido a fonte de dados padrão. (config.ini): app.persist.default = NAME';

    /**
     * @var string
     * */
    const PERSISTCONFIG_INSTRUCTION_CREATE_CONFIG_OBJECT =  'Antes de criar um objeto PersistConfig é necessário registar a secao de
                                                persistencia em config.ini(app.persist) usando o metodo
                                                PersistConfig::registerConfigs';
    /**
     * @var string
     * */
    const PERSISTCONFIG_UNDEFINED_CONFIG_SECTION = 'A seção %s informada não existe no config.ini';

    /**
     * Adaptadores aceitos.
     *
     * A relação de adpatadores suportados pela camada de persistencia estao limitadas apenas
     * aos drivers suportados pela PDO. Assim, para habilitar novos adpatares consulte o manual
     * do PHP (http://br2.php.net/manual/pt_BR/pdo.drivers.php) e sua implementacao de DSN, ex:
     * Postgres: http://br2.php.net/manual/pt_BR/ref.pdo-pgsql.connection.php
     *
     * Nota: Habilitar um novo adaptador implica em criar criar/ajusta o metodo getDSN
     * na respectiva camda
     *
     * @var string[]
     * */
    private static $_adapters = array(
        'ldap'       ,
        'database'   ,
        'webservice' ,
    );

    /**
     * Armazena a configuracao de todos os configs de persistencia
     *
     * @var string[]
     * */
    public static $configs = NULL;

    /**
     * Construtor.
     *
     * @param string[] $config
     * @throws IllegalArgumentException
     * */
    public function __construct (array $config = array())
    {
        parent::__construct($config, NULL);
        $this->_valid($config);
    }

    /**
     * @param string $suspicious
     * @return boolean
     * */
    public function isSupported ($suspicious)
    {
        return self::adapaterAccepet($suspicious);
    }

    /**
     * Verifica se os dados informados sao válidos para o tipo de persistencia
     *
     * @param string[] $config
     * @throws IllegalArgumentException
     * */
    // @codeCoverageIgnoreStart
    protected abstract function _valid (array $config);
    // @codeCoverageIgnoreEnd

    /**
     * Verifica se o adaptador informado é suportado.
     *
     * @param string $adpater
     * @return bool
     * */
    public final static function adapaterAccepet ($adapter)
    {
        return in_array($adapter, self::$_adapters);
    }

    /**
     * Retorna DSN (Data Source Name) para conexao coma o repositorio
     *
     * @return string
     * */
    // @codeCoverageIgnoreStart
    public abstract function getDSN ();
    // @codeCoverageIgnoreEnd

    /**
     * Registra as configuracoes de conexao que poderao ser utilizadas para estabelecer
     * conexoes com os repositorios de dados.
     *
     * <b>string</b> para indicar o arquivo de configuracao (.ini) <br />
     * <b>string[]</b> para indicar um array de configuracoes
     *
     * @param [string | string[]] $data
     * @throws IllegalArgumentException
     * */
    public static function registerConfigs ($data)
    {
        $message = sprintf(self::PERSISTCONFIG_ENVIRONMENT_CONST_UNAVAILABLE);
        IllegalArgumentException::throwsExceptionIfParamIsNull(defined('APPLICATION_ENV'), $message);

        if (is_string($data)) {
            $data = new ConfigIni($data, constant('APPLICATION_ENV'));
            $data = $data->get('app')->get('persist')->toArray();
        }

        $message = self::PERSISTCONFIG_UNDEFINED_DEFAULT_DATASOURCE;
        IllegalArgumentException::throwsExceptionIfParamIsNull(isset($data['default']), $message);

        self::$configs = $data;
    }

    /**
     * Fábrica de config.
     *
     * @param string $dsName
     * @param string[] $data
     * @return PersistConfig
     * @throws IllegalArgumentException
     * */
    public static function factory ($dsName = NULL, array $data = null)
    {
        $nsSep  = self::NAMESPACE_SEPARATOR;
        $config = $data ?: self::$configs;

        $message = self::PERSISTCONFIG_INSTRUCTION_CREATE_CONFIG_OBJECT;
        IllegalArgumentException::throwsExceptionIfParamIsNull(!(NULL == $config), $message);

        $dsName = $dsName ?: $config['default'];

        $message = sprintf(self::PERSISTCONFIG_UNDEFINED_CONFIG_SECTION, $dsName);
        IllegalArgumentException::throwsExceptionIfParamIsNull(isset($config[$dsName]), $message);

        $nsep = self::NAMESPACE_SEPARATOR;
        $tmpNamespace = __NAMESPACE__ . $nsep . $config[$dsName]['adapter'] . $nsep . 'Config';

        return $tmpNamespace::factory($dsName);
    }
}
