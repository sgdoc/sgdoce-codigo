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
namespace br\gov\sial\core\output\screen\component\html;
use br\gov\sial\core\output\screen\IBuild,
    br\gov\sial\core\output\screen\html\Div,
    br\gov\sial\core\output\screen\html\Text,
    br\gov\sial\core\output\screen\html\Button,
    br\gov\sial\core\output\screen\html\Strong,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\output\screen\component\AlertAbstract;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * @name Alert
 * */
class Alert extends AlertAbstract implements IBuild
{
    /**
     * @var string
     */
    const T_ALERT_CLOSE = '&times;';

    /**
     * @var string
     */
    const T_ALERT_SPACE = '&nbsp;';

    /**
     * Método construtor
     */
    public function __construct (\stdClass $config)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull($config->id, self::T_ALERTABSTRACT_ID_CANNOT_BE_NULL);

        $this->_alertId = $config->id;
        $this->_alertType = $config->type;
        $this->_alertTitle = $config->title;
        $this->_alertMessage = $config->message;

        $this->isValidType();

        $this->_alert = new Div;
        $this->_alert->addClass('alert')
                     ->addClass('alert-' . $this->_alertType)
                      ->attr('id', $this->_alertId);

        $this->_dismissButton = new Button();
        $this->_dismissButton->attr('type', 'button')
                             ->attr('data-dismiss', 'alert')
                             ->addClass('close')
                             ->setContent(self::T_ALERT_CLOSE);

        $this->_title = new Strong();
        $this->_title->setContent($this->_alertTitle . self::T_ALERT_SPACE);

        $this->_message = new Text();
        $this->_message->setContent($this->_alertMessage);
    }

    /**
     * Verifica se o tipo de Alert é suportado
     * @param string $type
     */
    private function isValidType ()
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull('warning' == $this->_alertType
                                                            || 'block' == $this->_alertType
                                                            || 'success' == $this->_alertType
                                                            || 'error' == $this->_alertType
                                                            || 'info' == $this->_alertType,
                                    self::T_ALERTABSTRACT_INVALID_TYPE);
    }

    /**
     * @return AlertAbstract
     */
    public function build ()
    {
        $this->_alert->add($this->_dismissButton);
        $this->_alert->add($this->_title);
        $this->_alert->add($this->_message);

        return $this;
    }
}