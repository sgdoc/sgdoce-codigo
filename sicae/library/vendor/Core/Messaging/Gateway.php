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
 * Componente de mensageria - Gateway
 *
 * @package    Core
 * @subpackage Messaging
 * @name       Gateway
 * @category   Gateway
 * @author     Pablo Santiago Sánchez <phackwer@gmail.com>
 */
class Core_Messaging_Gateway
{
    /**
     * Identificador do Gateway
     * @var string
     */
    protected $id;

    /**
     * Adapter padrão
     * @var string
     */
    protected $_adapter = 'Core_Messaging_Adapter_Session';

    /**
     * Configurações para o Adapter
     * @var array
     */
    protected $_configAdapter;

    /**
     * Pacotes em processamento pelo Gateway
     * @var array Core_Messaging_Packet
     */
    protected $_packets = array();

    /**
     * Construtor - Instancia o adapter para o gateway novo
     * Pode ser um objeto ou o nome da classe
     * @param unknown_type $id
     * @param unknown_type $adapter
     */
    public function __construct($id, $adapter = null, $configAdapter = null)
    {
        $this->id = $id;
        if ($adapter) {
            $this->_adapter = $adapter;
        }
        if ($configAdapter) {
            $this->_configAdapter = $configAdapter;
        }

        if (!is_subclass_of($this->_adapter, 'Core_Messaging_Adapter_Interface')) {
            throw new InvalidArgumentException('Invalid adapter');
        }
    }

    /**
     * Retorna instancia do adapter definido
     */
    private function _getAdapter()
    {
        if (is_string($this->_adapter)) {
            $adapter = $this->_adapter;
            $this->_adapter = new $adapter($this->_configAdapter);
        }

        return $this->_adapter;
    }

    /**
     * Cria pacote de mensagens por destinatário
     * @param unknown_type $receiver
     * @param unknown_type $systemTo
     */
    private function _setPacketContent($messageText, $messageType, $receiver)
    {
        if (!isset($this->_packets[$receiver]) || !$this->_packets[$receiver] instanceof Core_Messaging_Packet) {
            $this->_packets[$receiver] = new Core_Messaging_Packet($receiver);
        }

        $this->_packets[$receiver]->addMessage($messageText, $messageType);

        return $this->_packets;
    }

    /**
     *
     * Enter description here ...
     */
    private function _flushPackets()
    {
        $this->_packets = array();
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $messageText
     * @param unknown_type $messageType
     * @param unknown_type $receiver
     */
    public function addMessage($messageText, $messageType, $receiver = null)
    {
        if (!$receiver) {
            $receiver = $this->id;
        }

        if (Core_Registry::getMessage()->isTranslated($messageText)) {
            $messageText = Core_Registry::getMessage()->translate($messageText);
        }

        return $this->_setPacketContent($messageText, $messageType, $receiver);
    }

    public function addInfoMessage($messageText, $receiver = null)
    {
        return $this->addMessage($messageText, 'info', $receiver);
    }

    public function addSuccessMessage($messageText, $receiver = null)
    {
        return $this->addMessage($messageText, 'success', $receiver);
    }

    public function addAlertMessage($messageText, $receiver = null)
    {
        return $this->addMessage($messageText, 'alert', $receiver);
    }

    public function addErrorMessage($messageText, $receiver = null)
    {
        return $this->addMessage($messageText, 'error', $receiver);
    }

    public function getErrorMessages($receiver = null)
    {
        if (NULL === $receiver) {
            $receiver = $this->id;
        }

        if (!isset($this->_packets[$receiver])) {
            return array();
        }

        if (!$this->_packets[$receiver] instanceof Core_Messaging_Packet) {
            return array();
        }

        return $this->_packets[$receiver]->getMessages('error');
    }

    public function getAlertMessages($receiver = null)
    {
        if (NULL === $receiver) {
            $receiver = $this->id;
        }

        if (!isset($this->_packets[$receiver])) {
            return array();
        }

        if (!$this->_packets[$receiver] instanceof Core_Messaging_Packet) {
            return array();
        }

        return $this->_packets[$receiver]->getMessages('alert');
    }

    public function getSuccessMessages($receiver = null)
    {
        if (NULL === $receiver) {
            $receiver = $this->id;
        }

        if (!isset($this->_packets[$receiver])) {
            return array();
        }

        if (!$this->_packets[$receiver] instanceof Core_Messaging_Packet) {
            return array();
        }

        return $this->_packets[$receiver]->getMessages('success');
    }

    public function getInfoMessages($receiver = null)
    {
        if (NULL === $receiver) {
            $receiver = $this->id;
        }

        if (!isset($this->_packets[$receiver])) {
            return array();
        }

        if (!$this->_packets[$receiver] instanceof Core_Messaging_Packet) {
            return array();
        }

        return $this->_packets[$receiver]->getMessages('info');
    }

    /**
     *
     * Enter description here ...
     */
    public function dispatchPackets()
    {
        $this->_getAdapter()->dispatchPackets($this->_packets);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $flush
     */
    public function retrievePackets($receiver = null, $flush = true, $dispatch = FALSE)
    {
        if ($dispatch) {
            $this->dispatchPackets();
        }

        if (!$receiver) {
            $receiver = $this->id;
        }

        $packets = $this->_getAdapter()->retrievePackets($receiver);

        if ($receiver !== $this->id) {
            $packetsGateway = Core_Messaging_Manager::getGateway($receiver)->retrievePackets();
            $packets = (array) $packets;
            array_splice($packets, count($packetsGateway), 0, $packetsGateway);
        }

        if ($flush) {
            $this->_packets[$receiver] = array();
            $this->_getAdapter()->flushPackets($receiver);
        }

        return $packets;
    }
}