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
use br\gov\sial\core\util\ConfigAbstract;
use br\gov\sial\core\exception\IOException;
use br\gov\sial\core\exception\IllegalArgumentException;

/* arquivos necessarios antes do carregamento do ClassLoader */
require_once 'ConfigAbstract.php';
require_once 'ConfigIniInheritance.php';
require_once dirname(__DIR__) .
             DIRECTORY_SEPARATOR . 'exception' .
             DIRECTORY_SEPARATOR . 'IllegalArgumentException.php';

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class ConfigIni extends ConfigAbstract
{
    /**
     * @var string
     * */
    private $_configIniFilename;

    /**
     * @param string
     * @param string
     * */
    public function __construct ($filename, $section)
    {
        parent::__construct($filename, $section);
    }

    /**
     * {@inheritdoc}
     *
     * @throws IllegalArgumentException
     * */
    public final function load ($filename, $section)
    {
        try {
            require_once 'Zend/Config/Ini.php';

            $this->_configIniFilename = $filename;

            if (ConfigIniInheritance::hasDependence($filename)) {
                $this->_configIniFilename = ConfigIniInheritance::getInitFilename($filename);
            }

            $this->_resource = new \Zend_Config_Ini($this->_configIniFilename, $section);

        } catch (\Exception $exc) {
            throw new IllegalArgumentException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * @return string
     * */
    public function getConfigIniFilename ()
    {
        return $this->_configIniFilename;
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * {@inheritdoc}
     * */
    public final function isSuported ($filename)
    {
        return is_file($filename) && 'ini' === strtolower(pathinfo($filename, \PATHINFO_EXTENSION));
    // @codeCoverageIgnoreStart
    }
    // // @codeCoverageIgnoreEnd



}