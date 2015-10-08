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
 * @name ModalAbstract
 * */
abstract class ModalAbstract extends ComponentAbstract implements IBuild
{
    /**
     * CSS default para o componente Modal
     */
    const T_MODALABSTRACT_DEFAULT_CSS = 'modal';

    /**
     * CSS default para efeito de transição no componente Modal
     */
    const T_MODALABSTRACT_FADE_CSS = 'fade';

    /**
     * Agregador do componente Modal
     * @var ModalAbstract
     */
    protected $_modal;

    /**
     * Agregador do título do componente Modal
     * @var Modal
     */
    protected $_title;

    /**
     * Agregador do body do componente Modal
     * @var Modal
     */
    protected $_body;

    /**
     * Agregador do footer do componente Modal
     * @var Modal
     */
    protected $_footer;

    /**
     * @return ModalAbstract
     */
    public function build ()
    {
        return $this;
    }

    /**
     * @param string $type
     * @return ComponentAbstract
     */
    public static function factory ($config, $type = 'html')
    {
        $namespace = self::NSComponent('modal', $type);

        return new $namespace($config);
    }

    /**
     * @return Modal
     */
    public function render ()
    {
        return $this->_modal->render();
    }
}