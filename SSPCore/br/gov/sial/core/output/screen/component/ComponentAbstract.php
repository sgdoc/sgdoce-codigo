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
namespace br\gov\sial\core\output\screen\component;
use br\gov\sial\core\output\screen\ElementContainerAbstract,
    br\gov\sial\core\output\screen\component\exception\ComponentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen
 * @subpackage component
 * @name ComponentAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class ComponentAbstract extends ElementContainerAbstract
{
    /**
     * @var Iterator
     * */
    protected $_data;

    /**
     * @param Iterator $data
     * */
    public function __construct (\Iterator $data = NULL)
    {
        $this->_data = $data;
    }

    /**
     * @param string $name
     * @param string $type
     * @return string
     * @throws ComponentException
     * */
    public function NSComponent ($name, $type)
    {
        $namespace = __NAMESPACE__ . self::NAMESPACE_SEPARATOR
                   . $type         . self::NAMESPACE_SEPARATOR
                   . ucfirst($name);

        ComponentException::throwsExceptionIfParamIsNull(is_file(self::realpathFromNamespace($namespace) . '.php'), 'Componente indisponível');

        return $namespace;
    }
}