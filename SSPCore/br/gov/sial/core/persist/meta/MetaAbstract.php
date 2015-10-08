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
namespace br\gov\sial\core\persist\meta;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\persist\database\Connect,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\meta\exception\MetaException;

/**
 * SIAL
 *
 * recupera informacoes da entidade representada pelo ValueObject
 *
 * @package br.gov.sial.core.persist
 * @subpackage meta
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class MetaAbstract extends SIALAbstract
{
    /**
     * @var Connect
     * */
    protected $_persist;

    /**
     * @param Persist $persist
     * */
    public function __construct (Connect $persist)
    {
        $this->_persist = $persist;
    }

    /**
     * recupera a versao repositorio de dados
     *
     * @return string
     * */
    public abstract function version ();

    /**
     * @param string $schema
     * @param string $entity
     * @return ArrayObject
     * */
    public abstract function data ($schema, $entity);

    /**
     * @param Connect $persist
     * */
    public static function factory (Connect $persist)
    {
        $NSMeta = __NAMESPACE__          . self::NAMESPACE_SEPARATOR
                . $persist->getAdapter() . self::NAMESPACE_SEPARATOR
                . $persist->getDriver()  . self::NAMESPACE_SEPARATOR
                . 'Meta';

        return new $NSMeta($persist);
    }
}