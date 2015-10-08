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
namespace br\gov\sial\core\persist\reflection;
use
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\persist\Connect,
    br\gov\sial\core\persist\util\Annotation,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * Superclasse de reflexão de repositório
 *
 * @package br.gov.sial.core.persist
 * @subpackage reflection
 * @name Reflection
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class Reflection extends SIALAbstract
{
    /**
     * @var Annotation
     * */
    protected $_annotation;

    /**
     * @var Connect
     * */
    protected $_connect = NULL;

    /**
     * Construtor.
     *
     * @param Annotation $annotation
     * @param Connect $connect
     * */
    public function __construct (Annotation $annotation, Connect $connect)
    {
        $this->_annotation = $annotation;
        $this->_connect = $connect;
    }

    /**
     * Fábrica de Reflection
     *
     * a fabrica de reflaxao trabalha em conjunto com a anotacao do valueObject e o objeto de
     * conexao, sendo o primeiro necessario para obter a entidade de armazena definida na primeira
     * parte da annotacao. ja o segundo param (connect) eh utilizado para obter as propriedades
     * da entidade propriamente dito.
     *
     * O primeiro paramentro ($source) pode varia em dois tipos namespace do ValueObject ou um
     * objeto deste para que se possa recuperar sua anotacao.
     *
     * @param [string | Valueobject] $source
     * @param Connect $connect
     * @return Reflection
     * @throws IllegalArgumentException
     * */
    public static function factory ($source, Connect $connect)
    {
        $tmpNSReflection = __NAMESPACE__
                         . self::NAMESPACE_SEPARATOR . 'adapter'
                         . self::NAMESPACE_SEPARATOR . $connect->getAdapter()
                         . self::NAMESPACE_SEPARATOR . $connect->getDriver()
                         . self::NAMESPACE_SEPARATOR . 'Reflection';

        try {
            return $tmpNSReflection::factory ($source, $connect);

        } catch (IllegalArgumentException $illExc) {
            // @codeCoverageIgnoreStart
            throw $illExc;
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Retorna uma propriedade específica do elemento.
     *
     * @param string $name
     * @return stdClass
     * */
    // @codeCoverageIgnoreStart
    public abstract function property ($name = NULL);
    // @codeCoverageIgnoreEnd

    /**
     * Retorna as propriedades do elemento.
     *
     * @return stdClass[]
     * */
    // @codeCoverageIgnoreStart
    public abstract function properties ();
    // @codeCoverageIgnoreEnd
}