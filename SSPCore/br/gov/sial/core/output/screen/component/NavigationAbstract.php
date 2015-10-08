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
 * @name NavigationAbstract
 * */
abstract class NavigationAbstract extends ComponentAbstract implements IBuild
{
    /**
     * @var string
     */
    const T_NAVIGATIONABSTRACT_INVALID_TYPE = 'Tipo de navegação inválido. São aceitos os tipos: \'tabs\', \'pills\' e \'lists\'';

    /**
     * Agregador de elementos que compõem o componente Tab
     * @var ElementAbstract
     */
    protected $_nav;

    /**
     * Agregador de elementos que compõem um item do componente Tab
     * @var Elementabstract
     */
    protected $_item;

    /**
     * Agregador de elementos que compõem um dropdown para um item de Tab
     * @var ElementAbstract
     */
    protected $_dropdown;

    /**
     * @return JsonAbstract
     */
    public function build ()
    {
        return $this;
    }

    /**
     * @param stdClass $config
     * @return ComponentAbstract
     */
    public static function factory ($config , $type = 'html')
    {
        $namespace = self::NSComponent('navigation', $type);

        return new $namespace($config);
    }

    /**
     * @return string
     */
    public function render ()
    {
        return $this->_nav;
    }
}