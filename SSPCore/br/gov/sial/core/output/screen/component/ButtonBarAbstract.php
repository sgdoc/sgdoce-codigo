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
 * */
abstract class ButtonBarAbstract extends ComponentAbstract implements IBuild
{
    /**
     * Mensagem de erro ao requerer um botão inexistente.
     * var @string
     */
    const T_BUTTONBARABSTRACT_INVALID_BUTTON = 'O botão informado é inválido.';

    /**
     * Agrupador do componente ButtonBar
     * @var ButtonBarAbstract
     */
    protected $_buttonBar;

    /**
     * @param string|string[] $btn
     * */
    public abstract function enable ($btn);

    /**
     * @override
     * */
    public function getElementsByClass ($class)
    {
        return $this->_buttonBar->getElementsByClass($class);
    }

    /**
     * @return ButtonBarAbstract
     */
    public function build ()
    {
        return $this;
    }

    /**
     * @param stdClass $config
     * @return ComponentAbstract
     */
    public static function factory ($config , $type)
    {
        $namespace = self::NSComponent('buttonBar', $type);

        return new $namespace($config);
    }

    /**
     * @return string
     */
    public function render ()
    {
        return $this->_buttonBar->render();
    }
}