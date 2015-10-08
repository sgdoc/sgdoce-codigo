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
 * Componente de mensageria - Pacote de Mensagens
 *
 * @package    Core
 * @subpackage Messaging
 * @name       Packet
 * @category   Pacote
 * @author     Pablo Santiago Sánchez <phackwer@gmail.com>
 */
class Core_Messaging_Packet
{
//    (Implantar depois, junto com a integração e serviços)
//    /**
//     * Sistema remetente - valor automático
//     * @var string
//     */
//    protected $_systemFrom;
//
//    /**
//     * Objeto/Classe remetente - valor automático
//     * @var string
//     */
//    protected $_sender;
//
//    /**
//     * Sistema destinatário
//     * @var string
//     */
//    protected $_systemTo;

    /**
     * Destinatário
     * @var string
     */
    protected $_receiver;

    /**
     * Prioridade do pacote - nulo = ordem de entrada
     * @var integer
     */
    protected $_priority = null;

    /**
     * Usuário do sistema (pode ser nulo)
     * @var string
     */
    protected $_user;

    /**
     * IP da máquina cliente (quando houver)
     * @var string
     */
    protected $_clientAddress;

    /**
     * IP do servidor de aplicação
     * @var string
     */
    protected $_serverAddress;

    /**
     * Timestamp de criação
     * @var timestamp
     */
    protected $_createDate;

    /**
     * Timestamp de vida
     * @var timestamp
     */
    protected $_lifeTime;

    /**
     * Objetos de mensagem
     * @var Core_Messaging_Message
     */
    protected $_messages = array();

    public function __construct($receiver)
    {
        $this->_receiver      = $receiver;
        $this->_user          = Core_Integration_Sica_User::getUserData();

        if(php_sapi_name() !== 'cli') {
            $this->_clientAddress = $_SERVER['REMOTE_ADDR'];
            $this->_serverAddress = $_SERVER['SERVER_ADDR'];
        }

        $this->_createDate    = time();
        $this->_lifeTime      = time();
        $this->_priority      = null;
    }

    /**
     * Adiciona uma mensagem ao pacote
     * @param Core_Messaging_Message $message
     */
    public function addMessage($messageText, $messageType)
    {
        if (!isset($this->_messages[$messageType])){
            $this->_messages[$messageType] = array();
        }
        $this->_messages[$messageType][] = $messageText;
        $this->_messages[$messageType] = array_unique($this->_messages[$messageType]);
    }

    /**
     *
     */
    public function addMessages(array $messages, $messageType = null)
    {
        $this->_messages[$messageType] = array_merge($this->_messages[$messageType], $messages);
    }

    /**
     * Utilizado apenas pelo adapter para fazer o join dos pacotes registrados com os pacotes anteriores
     */
    public function replaceAllMessages($messages)
    {
    	$this->_messages = $messages;
    }

    /**
     *
     */
    public function getMessages($messageType)
    {
        if (isset($this->_messages[$messageType])) {
            return $this->_messages[$messageType];
        }

        return array();
    }

    /**
     *
     */
    public function getAllMessages()
    {
        return $this->_messages;
    }
}