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
 * @name AlertAbstract
 * */
abstract class AlertAbstract extends ComponentAbstract implements IBuild
{
    /**
     * @var string
     */
    const T_ALERTABSTRACT_INVALID_TYPE = 'O tipo de Alert informado é inválido. São tipos aceitos: warning|block, success, error, info.';

    /**
     * @var string
     */
    const T_ALERTABSTRACT_ID_CANNOT_BE_NULL = 'O atributo "id" do componente Alert não pode ser nulo.';

    /**
     * Agrupador de elementos do componente Alert
     * @var string
     */
    protected $_alert;

    /**
     * Botão de fechamento do Alert
     * @var string
     */
    protected $_dismissButton;

    /**
     * Título da mensagem
     * @var string
     */
    protected $_title;

    /**
     * Mensagem a ser exibida em tela
     * @var string
     */
    protected $_message;

    /**
     * Valor do atributo 'id' do componente em HTML
     * @var string
     */
    protected $_alertId;

    /**
     * Tipo de Alert a ser criado (warning|block, success, error, info)
     * @var string
     */
    protected $_alertType;

    /**
     * Título da mensagem do Alert
     * @var string
     */
    protected $_alertTitle;

    /**
     * Conteúdo do Alert
     * @var string
     */
    protected $_alertMessage;

    /**
     * @return AlertAbstract
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
        $namespace = self::NSComponent('alert', $type);

        return new $namespace($config);
    }

    /**
     * @return string
     */
    public function render ()
    {
        return $this->_alert->render();
    }
}