<?php

class Core_Controller_Action_Helper_ParseJson extends Zend_Controller_Action_Helper_Abstract
{

    /**#@+
     * @const tipo de notificações
     */
    const TYPE_ERROR   = 1;
    const TYPE_ALERT   = 2;
    const TYPE_SUCCESS = 3;
    /**#@-*/

    /**#@+
     * @const Titulo de acordo com o tipo
     */
    const SUBJECT_ERROR   = 'Erro';
    const SUBJECT_ALERT   = 'Alerta';
    const SUBJECT_SUCCESS = 'Sucesso';
    /**#@-*/

    protected function _getSubjectType($type)
    {
        switch ($type) {
            case self::TYPE_ALERT:
                return self::SUBJECT_ALERT;
                break;
            case self::TYPE_SUCCESS:
                return self::SUBJECT_SUCCESS;
                break;
            case self::TYPE_ERROR:
                return self::SUBJECT_ERROR;
                break;
            default:
                throw new InvalidArgumentException("Invalid Argument '$type'!");
        }
    }

    public function parseJson($code, array $tokens = array(), $data = NULL, $success = TRUE)
    {
        $body    = Core_Registry::getMessage()->translate($code, NULL, $tokens);
        $type    = Core_Registry::getMessage()->getType($code);
        $subject = NULL;

        if (NULL === $type) {
            $type = static::TYPE_ERROR;
        }

        $subject = $this->_getSubjectType($type);

        return $this->_makeJsonPacket($success, $type, $subject, $body, $data);
    }

    /**
     *
     * @param  array $params
     * @return boolean
     */
    public function direct($code = null, $tokens = array(), $data = NULL, $success = TRUE)
    {
        if (0 === func_num_args()) {
            return $this;
        }

        return $this->parseJson($code, $tokens, $data, $success);
    }

    protected function _makeJsonPacket($success, $type, $subject, $body, $data = NULL)
    {
        $jsonPacket = array(
            'success'  => $success,
        ) + $this->_getMetadataContent($data);

        if ($type && $subject) {
            $jsonPacket += array(
                'message'  => array(
                    'type'    => $type,
                    'subject' => $subject,
                    'body'    => $body
            ));
        }

        return $jsonPacket;
    }

    protected function _getMetadataContent($data)
    {
        return array('content' => $data);
    }

    public function sendJson($code = NULL, $tokens = array(), $data = NULL, $gateway = NULL, $type = NULL)
    {
        $json  = Zend_Controller_Action_HelperBroker::getStaticHelper('json');

        if (NULL === $code) {
            $code = $this->_retriveMessages($gateway, $type);
            if (is_array($code)) {
                $code = reset($code);
                if (is_array($code)) {
                     $code = reset($code);
                }
            }
        }

        $makeJson = $this->parseJson($code, $tokens, $data, TRUE);
        $json->direct($makeJson);
    }

    protected function _retriveMessages($gateway = 'Service', $type = NULL)
    {
        if (NULL === $gateway) {
            $gateway = 'Service';
        }

        $gateway  = Core_Messaging_Manager::getGateway($gateway);
        $packets  = $gateway->retrievePackets(NULL, TRUE, TRUE);
        $messages = array();

        if ($packets instanceof Core_Messaging_Packet) {
            $messages = $packets->getAllMessages();
        }

        if (isset($messages[$type])) {
            $messages = $messages[$type];
        }

        return $messages;
    }
}
