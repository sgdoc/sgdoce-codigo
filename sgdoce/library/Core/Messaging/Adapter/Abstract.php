<?php
/**
 * Copyright 2012 do ICMBio
 *
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
/**
 * Componente de mensageria - Adapter
 *
 * @package    Core
 * @subpackage Messaging
 * @subpackage Adapter
 * @name       Abstract
 * @category   Adapter Abstract
 * @author     Pablo Santiago Sánchez <phackwer@gmail.com>
 */
abstract class Core_Messaging_Adapter_Abstract implements Core_Messaging_Adapter_Interface
{
    /**
     * pacotes gerenciados pelo adapter
     * @var array
     */
    protected $_packets = array();

    /**
     * handler do repositório
     * @var resource
     */
    protected $_repository;

    /**
     * Construtor
     * @param array $configs
     */
    public function __construct($configs = null)
    {
        $this->_repository; //crie o repositório que será utilizado neste ponto com as configurações passadas
    }

    /**
     * Faz o merge dos pacotes anteriores com os novos
     * @param array $packets
     */
    protected function _joinPackets($packets)
    {
        foreach ($packets as $receiver => $packet) {
            if (isset($this->_packets[$receiver]) && $this->_packets[$receiver] instanceof Core_Messaging_Packet) {
                $currMessages = $this->_packets[$receiver]->getAllMessages();
                $newMessages  = $packet->getAllMessages();
                foreach ($currMessages as $type => $msgs) {
                    if (isset($newMessages[$type])) {
                        $newMessages[$type] = array_unique((array_merge((array)$newMessages[$type], (array)$currMessages[$type])));
                    }
                    else {
                        $newMessages[$type] = $currMessages[$type];
                    }
                }

                $this->_packets[$receiver]->replaceAllMessages($newMessages);

            }
            else {
                $this->_packets[$receiver] = $packet;
            }
        }
    }

    /**
     * Persiste os pacotes de mensagens no repositório definido
     */
    protected function _persistPackets()
    {
    }

    /**
     * Recupera os pacotes de mensagens do repositório definido
     */
    protected function _aquirePackets()
    {
    }

    /**
     * Envia pacotes para serem persistidos
     * @param array $packets
     */
    public function dispatchPackets($packets)
    {
        $this->_aquirePackets();
        $this->_joinPackets($packets);
        $this->_persistPackets();
    }

    /**
     * Limpa as mensagens de um destinatário
     * @param string $receiver
     */
    public function flushPackets($receiver)
    {
        if (isset($this->_packets[$receiver])) {
            unset($this->_packets[$receiver]);
        }
        $this->_persistPackets();
    }

    /**
     * Recupera as mensagens de um destinatário
     * @param string $receiver
     * @return array
     */
    public function retrievePackets($receiver)
    {
        $this->_aquirePackets();
        if (isset($this->_packets[$receiver])) {
            $packets = $this->_packets[$receiver];
            return $packets;
        } else {
            return array();
        }
    }
}