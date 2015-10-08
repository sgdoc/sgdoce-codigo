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
    br\gov\sial\core\output\screen\html\Paragraph,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\output\screen\component\WellAbstract;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * */
class Well extends WellAbstract implements IBuild
{
    /**
     * Classe CSS default para o componente Well
     * @var string
     */
    const T_WELL_DEFAULT_CLASS = 'top-bar';

    /**
     * @param string $config
     */
    public function __construct ($config)
    {
        $this->_well = new Div;
        $this->_well->addClass($this::T_WELL_DEFAULT_CLASS);
        $this->_action = new \br\gov\sial\core\output\screen\html\Text();

        if (isset($config->id)) {
            $this->id        =
            $this->_well->id = $config->id;
        }

        if (isset($config->size)) {
            $this->_size = $config->size;
            $this->isValidSize($this->_size);
            $this->setSize($this->_size);
        }

        $this->_message = new Paragraph();
        $this->_message->addClass('requiredLegend pull-left')
                       ->setContent($this->safeToggle($config, 'message'));

        /**
         * @todo Aceitar customização no botão da action
         */
        if (isset($config->action)) {
            $actionButton = (isset($config->actionButton)) ? $config->actionButton : 'Cadastrar' ;
            $actionClass = (isset($config->class)) ? $config->class : 'btn btn-primary';

            $action = new \br\gov\sial\core\output\screen\html\Anchor($actionButton, $config->action);
            $action->addClass($actionClass);

            $this->_action = new Div();
            $this->_action->addClass('btn-group pull-right')
                          ->add($action);
        }
    }

    /**
     * Verifica se o tamanho do Well é suportado
     * @param string $type
     */
    private function isValidSize ($type)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull('large' == $type || 'small' == $type, self::T_WELLABSTRACT_INVALID_SIZE);
    }

    /**
     * Determina o tamanho do Well
     * @param string $size
     */
    private function setSize ($size)
    {
        if(!empty($size)){
            $this->_well->addClass($this::T_WELL_DEFAULT_CLASS . '-' . $size);
        }
    }

    /**
     * @return WellAbstract
     */
    public function build ()
    {
        $this->_well->add($this->_message)
                    ->add($this->_action);

        return $this;
    }

    /**
     * @param stdClass $param
     * @return Well
     * */
    public static function factory (\stdClass $config)
    {
        return new self($config);
    }
}