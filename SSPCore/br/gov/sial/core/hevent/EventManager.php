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
    br\gov\sial\core\hevent\EventElement;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage hevent
 * @name EventManager
 * @author j. augusto <augustowebd@gmail.com
 * */
class EventManager extends SIALAbstract
{
    /**
     * Pilha de elementos.
     *
     * @var EventElement[]
     * */
    private $_queue = array();

    /**
     * Lista de eventos suspensos.
     *
     * @var string[]
     * */
    private $_preventList = array();

    /**
     * Registra o evento.
     *
     * @param $element
     * @return EventElement
     * */
    public function register ($element)
    {
        $elEvent = new EventElement($element);
        $this->_queue[] = $elEvent;
        return $elEvent;
    }

    /**
     * Suspende a execução de um evento.
     *
     * @param string $event
     * @return EventManager
     * */
    public function preventEvent ($event)
    {
        $this->_preventList[] = $event;
        return $this;
    }

    /**
     * Notifica todos elementos registrados sobre a ocorrência de um evento.
     * <i>$event</i>: Nome do evento
     * <i>$ignorePrevent</i>: Habilita a execução de evento suspenso.
     *
     * @param string $event
     * @param boolean $ignorePrevent = FALSE
     * @param mixed $eventParameters = NULL
     * */
    public function signal ($event, $ignorePrevent = FALSE, $eventParameters = NULL)
    {
        foreach ($this->_queue as $element) {

            if (in_array($event, $this->_preventList) && FALSE == $ignorePrevent) {
                continue;
            }

            $element->raise($event, $eventParameters);
        }
    }

    /**
     * Dispara um evento.
     *
     * @param string $event
     * @param mixed $eventParameters = NULL
     * @return EventManager
     * */
    public function raise ($event, $eventParameters = NULL)
    {
        $this->signal($event, TRUE, $eventParameters);
        return $this;
    }

    /**
     * Fábrica de EventManager.
     *
     * @return EventManager
     * */
    public static function factory ()
    {
        return new self;
    }
}