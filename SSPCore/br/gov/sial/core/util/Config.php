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
use \br\gov\sial\core\util\ConfigAbstract;

/**
 * SIAL
 *
 * Config
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @name ConfigArray
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Config extends ConfigAbstract
{
    /**
     * hash dos dados
     *
     * @var string
     * */
    private static $_hash = NULL;

    /**
     * construtor
     *
     * @param string[] $config
     * @param string $section
     * */
    public function __construct ($config, $section)
    {
        parent::__construct($config, $section);
        self::$_hash = md5($this->toJSon());
    }

    /**
     * retorna o hash dos dados
     *
     * @return string
     * */
    public function hash ()
    {
        return self::$_hash;
    }

    /**
     * {@inheritdoc}
     * */
    public final function isSuported ($config)
    {
        return is_array($config);
    }

    /**
     * {@inheritdoc}
     * */
    protected final function load ($config, $section)
    {
        // @codeCoverageIgnoreStart
        require_once 'Zend/Config.php';
        // @codeCoverageIgnoreEnd
        $this->_resource = new \Zend_Config($config, $section);
    }
}