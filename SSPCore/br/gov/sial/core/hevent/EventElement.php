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
namespace br\gov\sial\core\hevent;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage hevent
 * @name EventElement
 * @author j. augusto <augustowebd@gmail.com
 * */
class EventElement extends SIALAbstract
{
    /**
     * Mensagem do SIAL.
     *
     * @var string
     * */
    const T_EVENT_ELEMENT_INVALID_EVENT = 'O evento \'%s\' não pode ser invocado. Eventos iniciados por ((on)?(\'before\'|\'after\')) são reservados';

    /**
     * Referência do objeto Element.
     *
     * @var SIALAbstract
     * */
    private $_element;

    /**
     * Lista de eventos do elemento.
     *
     * @param string[]
     * */
    private $_events = array();

    /**
     * Construtor.
     *
     * @param SIALAbstract $element
     * */
    public function __construct (SIALAbstract $element)
    {
        $this->_element = $element;
    }

    /**
     * Registra os eventos.
     *
     * @param string $ident
     * @param mixed $event
     * @return EventElement
     * */
    public function __call ($event, $args)
    {
        $event = $this->eventNameStandardize($event);
        $this->_events[$event] = isset($args[0]) ? $args[0] : NULL;
        return $this;
    }

    /**
     * Padroniza em caixa-baixa o nome do evento.
     *
     * @param string $event
     * @return string
     * */
    public function eventNameStandardize ($event)
    {
        return strtolower('on' == substr($event, 0, 2) ? substr($event, 2) : $event);
    }

    /**
     * Verifica se um evento está registrado.
     *
     * @param string $event
     * @return boolean
     * */
    public function hasEvent ($event)
    {
        return isset($this->_events[$event]);
    }

    /**
     * Verifica se um evento ocorre <i>before</i> ou <i>after</i>.
     *
     * @param string $event
     * @return boolean
     * */
    public function isAutoEvent ($event)
    {
        $event = substr($event, 0, 5);
        return ('befor' == $event || 'after' == $event);
    }

    /**
     * Dispara um evento.
     *
     * @param string $event
     * @param mixed $eventParameters = NULL
     * @throws IllegalArgumentException
     * */
    public function raise ($event, $eventParameters = NULL)
    {
        $event = $this->eventNameStandardize($event);

        if (!$this->hasEvent($event)) {
            return;
        }

        IllegalArgumentException::throwsExceptionIfParamIsNull(!$this->isAutoEvent($event), sprintf(self::T_EVENT_ELEMENT_INVALID_EVENT, $event));

        $before = 'before' . $event;
        $after  = 'after'  . $event;

        # raise onbefore
        if ($this->hasEvent($before)) {
            $this->_raise($before, $eventParameters);
        }

        # dispara evento principal
        $this->_raise($event, $eventParameters);

        # raise onafter
        if ($this->hasEvent($after)) {
            $this->_raise($after, $eventParameters);
        }
    }

    /**
     * Garante o disparo do evento em mais de uma Action.
     *
     * @param string $event
     * @param mixed $eventParameters = NULL
     * */
    private function _raise ($event, $eventParameters = NULL)
    {
        foreach ((array) $this->_events[$event] as $key => $action) {
            if (is_callable($action)) {
                if (is_null($eventParameters)) {
                    $action();
                } else {
                    $action($eventParameters);
                }
            } else {
                if (is_null($eventParameters)) {
                    $this->_element->$action();
                } else {
                    $this->_element->$action($eventParameters);
                }
            }
        }
    }
}