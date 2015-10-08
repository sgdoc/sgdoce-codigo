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
use br\gov\sial\core\output\screen\IBuild,
    br\gov\sial\core\output\screen\component\ComponentAbstract;

/**
 * @package br.gov.sial.core.output.screen
 * @subpackage component
 * @name BreadcrumbAbstract
 * */
abstract class BreadcrumbAbstract extends ComponentAbstract implements IBuild
{
    /**
     * Agrupador do componente breadcrumb
     * @var Breadcrumb
     */
    protected $_breadcrumb;

    /**
     * Agrupador de elementos de um item no componente breadcrumb
     * @var string
     */
    protected $_listItem;

    /**
     * @param stdClass $config
     * @return ComponentAbstract
     */
    public static function factory ($config , $type = 'html')
    {
        $namespace = self::NSComponent('breadcrumb', $type);

        return new $namespace($config);
    }

    /**
     * @return BreadcrumbAbstract
     */
    public function build ()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function render ()
    {
        return $this->_breadcrumb->render();
    }
}