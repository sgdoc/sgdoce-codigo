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
    br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage database
 * @name Config
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class Config extends PersistConfig
{
    /**
     * @var string
     * */
    const CONFIG_TYPE = 'database';

    /**
     * @var string
     * */
    const CONFIG_SECTION_UNDEFINED = 'A seção %s informada não existe no config.ini';

    /**
     * @var
     * */
    const CONFIG_DB_UNSUPPORTED = 'O banco de dados %s não é suportado pela SIAL::Persist';

    /**
     * @var string
     * */
    const CONFIG_MANDATORY_PARAMETER = 'Paramentro obrigatório';

    /**
     * Bancos de dados aceitos.
     *
     * @var string[]
     * */
    private static $_acceptedDrivers = array(
        'mysql'  ,
        'pgsql'  ,
        'sqlite' ,
    );

    /**
     * Verifica se um banco de dados é suportado.
     *
     * @param string $suspicious
     * @return bool
     * */
    public function isSupported ($suspicious)
    {
        return parent::isSupported(self::CONFIG_TYPE) && in_array($suspicious, self::$_acceptedDrivers);
    }

    /**
     * Fábrica de Config.
     *
     * @param string $dsName
     * @return br\gov\sial\core\persist\database\Config
     * @throws br\gov\sial\core\exception\IllegalArgumentException
     * */
    public static function factory ($dsName = NULL)
    {
        # devido a interface, o php obriga q o paramentro seja identico
        # ao da superclasse, mas nesta classe este paramentro é obrigatorio,
        # o contrario da superclasse, assim, eh necessario manter manter a
        # compatibilidade de escrita e esta verificacao bizzara devido a exigencia
        # NOTA: Obrigado ao CTIS::Mario
        IllegalArgumentException::throwsExceptionIfParamIsNull($dsName, self::CONFIG_MANDATORY_PARAMETER);

        $message = sprintf(self::CONFIG_SECTION_UNDEFINED, $dsName);
        IllegalArgumentException::throwsExceptionIfParamIsNull(isset(self::$configs[$dsName]), $message);

        $data = self::$configs[$dsName];

        $message = sprintf(self::CONFIG_DB_UNSUPPORTED, $data['driver']);
        IllegalArgumentException::throwsExceptionIfParamIsNull(self::isSupported($data['driver']), $message);

        $namespace = __NAMESPACE__ . self::NAMESPACE_SEPARATOR . $data['driver']. self::NAMESPACE_SEPARATOR . 'Config';

        return new $namespace($data);
    }
}