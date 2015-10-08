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
namespace br\gov\sial\core\persist\database\mysql;
use br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\persist\database\Config as ParentConfig;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist.database
 * @subpackage mysql
 * @name Config
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class Config extends ParentConfig
{
    /**
     * @var string
     * */
    const CONFIG_MANDATORY_PROPERTY = 'É necessário definir a pripriedade PersistConfig::%s';

    /**
     * Construtor.
     *
     * @param string[] $config
     * @throws \br\gov\sial\core\exception\IllegalArgumentException
     * */
    public function __construct (array $config = array())
    {
        parent::__construct($config, NULL);
    }

    /**
     * {@inheritdoc}
     * */
    public function getDSN()
    {
        $port     = $this->get('port');
        $database = $this->get('source');
        $hostname = $this->get('hostname');
        return "mysql:host={$hostname};dbname={$database};port={$port}";
    }

    /**
     * {@inheritdoc}
     * @throws \br\gov\sial\core\persist\exception\PersistException
     * */
    protected function _valid (array $config)
    {
        $require = array('hostname', 'password', 'port', 'source', 'username');
        foreach ($require as $key) {
           PersistException::throwsExceptionIfParamIsNull($this->exists($key), sprintf(self::CONFIG_MANDATORY_PROPERTY, $key));
        }
    }
}