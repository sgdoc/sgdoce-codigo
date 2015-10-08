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
namespace br\gov\sial\core\util\mail;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\Registry,
    br\gov\sial\core\util\ConfigAbstract,
    br\gov\sial\core\util\lib\PHPMailer\SMTP,
    br\gov\sial\core\util\lib\PHPMailer\PHPMailer,
    br\gov\sial\core\util\mail\exception\MailException,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * Utilitário de e-mail
 *
 * @package br.gov.sial.core.util
 * @subpackage mail
 * @name Mail
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Mail extends SIALAbstract
{
    /**
     * chave de recuperacao do objeto de envio de e-mail
     *
     * @param string
     * */
    private $_hashKey;

    /**
     * constutor
     *
     * @param \br\gov\sial\core\util\ConfigAbstract $config
     * */
    public function __construct (ConfigAbstract $config)
    {
        try {
            # verifica se todas as propriedades basicas foram informados
            self::_valid($config);

            # o hash de acesso ao objeto eh baseado nas configurações de do objeto Mail
            # assim, havera sem um e somnete objeto de envio nao importando quantas vezes
            # esta classe seja instanciada
           $this->_hashKey = md5($config->toJSon());
           if (!Registry::isRegistered($this->_hashKey)) {
               Registry::set($this->_hashKey, new PHPMailer());
           }

           # aplica as configuracoes informadas ao objeto mail
           self::_setup($config);

        } catch (IllegalArgumentException $iExc) { throw $iExc; }
    }

    /**
     * retorna a conta, nome do usuario, do e-mail informado
     *
     * @param string $email
     * @return string
     * */
    private static function _accountName ($email)
    {
        return current(explode('@', $email));
    }

    /**
     * valida o objeto de configuracao
     *
     * @param \br\gov\sial\core\util\ConfigAbstract $config
     * @throws IllegalArgumentException
     * */
    private static function _valid (ConfigAbstract $config)
    {
        $tmpProp         = $config->toArray();
        $tmpPropRequired = array('sender', 'replyTo', 'priority', 'encoding', 'charset', 'contentType');

        # verifica se todas a propriedades basicas foram defindas
        foreach ($tmpPropRequired as $property) {
            IllegalArgumentException::throwsExceptionIfParamIsNull(isset($tmpProp[$property]), "'{$property}' é requido");
        }

        # verifica se as propriedades do sender foram informadas
        $message = 'As propriedades de configuralção do sender['.$tmpProp['sender'].'] não foi informado';
        IllegalArgumentException::throwsExceptionIfParamIsNull(isset($tmpProp[$tmpProp['sender']]), $message);
    }

    /**
     * configura o objeto mail
     *
     * @param \br\gov\sial\core\util\ConfigAbstract $config
     * */
    private function _setup (ConfigAbstract $config)
    {
        $tmpCfgMail = $config->toArray();
        foreach ($tmpCfgMail as $key => $val) {
            # verifica se existe um methodo com o nome da propriedade correspondente
            if (!$this->hasMethod($key)) { continue; }

            # verificacao necessaria para ajustar o valor do paramentro sender
            # que na verdade deve ser um array
            if ('sender' == $key) {
                # injeta o tipo de sender em suas configuracoes para que possa ser
                # escolhido o methodo de configruacao logo em seguinte
                $tmpCfgMail[$val]['type'] = $val;
                $val = $tmpCfgMail[$val];
            }

            $this->$key($val);
        }
    }

    /**
     * adiciona um ou mais arquivos a mensagem. O segundo paramentro, se for informado, sera usado
     * como nome de exibicao do arquivo no cliente do destinatario.
     *
     * @param string $attachment
     * @param string $name
     * @return \br\gov\sial\core\util\mail\Mail
     * @throws MailException
     * */
    public function addAttachment ($attachment, $name = NULL)
    {
        ob_start();
        $name   = $this->toggle($name, pathinfo($attachment, PATHINFO_FILENAME));
        $result = Registry::get($this->_hashKey)->AddAttachment($attachment, $name);
        $output = ob_get_clean();

        if (FALSE == $result || 'could not access file' == substr(strtolower($output), 0 , 21)) {
            throw new MailException('Não foi possível anexar o arquivo informado');
        }

        return $this;
    }

    /**
     * adiciona remenete em copia oculta
     *
     * @param string $email
     * @param string $name
     * @return Mail
     * */
    public function addBCC ($email, $name = NULL)
    {
        Registry::get($this->_hashKey)->AddBCC($email, $this->toggle($name, self::_accountName($email)));
        return $this;
    }

    /**
     * adiciona destinatario
     *
     * @param string $address
     * @param string $name
     * @return Mail
     * @throws MailException
     */
    public function addAddress ($email, $name = NULL)
    {
        ob_start();
        $result = Registry::get($this->_hashKey)->AddAddress($email, $this->toggle($name, self::_accountName($email)));
        $output = ob_get_clean();

        # verifica se o e-mail informado e invalidpo
        $invalidMail = ('invalid address' == substr($output, 0, 15));

        if (FALSE == $result || TRUE == $invalidMail) {
            throw new MailException('O e-mail informado não pode ser adicionado');
        }

        return $this;
    }

    /**
     * adiciona endereco que para receber copia da mensage
     *
     * @param string $address
     * @param string $name
     * @return Mail
     */
    public function addCC ($email, $name = NULL)
    {
        Registry::get($this->_hashKey)->AddCC($email, $this->toggle($name, self::_accountName($email)));
        return $this;
    }

    /**
     * adiciona uma conta para receber reposta da mensagem
     *
     * @param string $email
     * @param string $name
     * @return Mail
     * */
    public function addReplyTo ($email, $name = NULL)
    {
        Registry::get($this->_hashKey)->AddReplyTo($email, $this->toggle($name, self::_accountName($email)));
        return $this;
    }

    /**
     * define corpo alternativo da mensagem
     *
     * @param string $alternativeBody
     * @return Mail
     * */
    public function alternativeBody ($alternativeBody)
    {
        Registry::get($this->_hashKey)->AltBody = $alternativeBody;
        return $this;
    }


    /**
     * define o corpo da mensagem
     *
     * @param string $body
     * @param string $altBody
     * @return Mail
     * */
    public function body ($body, $altBody = NULL)
    {
        $mail = Registry::get($this->_hashKey);
        if ('text/html' == $mail->ContentType) {
            $mail->MsgHTML($body);
            $mail->IsHTML(TRUE);
        } else {
            $mail->Body = $body;
        }
        $this->alternativeBody($altBody);
        return $this;
    }

    /**
     * define o charset da mensagem
     *
     * @param string $charSet
     * @return Mail
     * */
    public function charSet ($charSet)
    {
        Registry::get($this->_hashKey)->CharSet = $charSet;
        return $this;
    }

    /**
     * define o tipo do conteudo da mensage
     *
     * @param enum(html|text) $contentType
     * @return Mail
     * @throws MailException
     * */
    public function contentType ($contentType)
    {
        $allow = array('html' => 'text/html', 'text' => 'text/plain');
        $type = next(explode('/', strtolower($contentType)));
        if (!empty($type)) {
            $contentType = $type;
        }

        MailException::throwsExceptionIfParamIsNull(isset($allow[$contentType]), 'Tipo de conteúdo inválido');
        Registry::get($this->_hashKey)->ContentType = $allow[$contentType];
        return $this;
    }

    /**
     * define a codificacao da mensagem
     *
     * @param string $encoder
     * @return Mail
     * @throws MailException
     * */
    public function encoding ($encoder)
    {
        $tmpAcceptedEncoder = array('8bit', '7bit', 'binary', 'base64', 'quoted-printable');
        $message = "<b>{$encoder}</b> não é um encoder válido";
        MailException::throwsExceptionIfParamIsNull(in_array($encoder, $tmpAcceptedEncoder), $message);
        Registry::get($this->_hashKey)->Encoding = $encoder;
        return $this;
    }

    /**
     * define a conta de email usada para o envio de mensagem
     *
     * @param string $email
     * @param string $name
     * @return Mail
     * */
    public function from ($email, $name= NULL)
    {
        Registry::get($this->_hashKey)->SetFrom($email, $this->toggle($name, self::_accountName($email)));
        return $this;
    }

    /**
     * define a porta default do smtp
     *
     * @param integer $port
     * @return Mail
     * @throws MailException
     * */
    public function port ($port)
    {
        $message = 'O número da porta deve ser um inteiro maior que zero';
        MailException::throwsExceptionIfParamIsNull('integer' == gettype($port), $message);
        Registry::get($this->_hashKey)->Port = (integer) $port;
        return $this;
    }

    /**
     * define a prioridade da mensagem
     *
     * @param string (high | normal | low)
     * @return Mail
     * */
    public function priority ($priority)
    {
        $tmpPriority = array('high' => 1, 'normal' => 3, 'low' => 5);
        if (!isset($tmpPriority[$priority])) {
            $tmpPriority = 3;
        }
        Registry::get($this->_hashKey)->Priority = $tmpPriority[$priority];
        return $this;
    }

    /**
     * define a conta que ira receber a resposta da mensagem
     *
     * @param string $email
     * @param string $name
     * @return Mail
     * */
    public function replyTo ($email, $name = NULL)
    {
        $mail = Registry::get($this->_hashKey);
        $mail->ClearReplyTos();
        $mail->AddReplyTo($email, $this->toggle($name, self::_accountName($email)));
        return $this;
    }

    /**
     * envia a mensagem
     *
     * @return Mail
     * @throws MailException
     * */
    public function send ()
    {
        $message = 'Não foi possível enviar a menasgem';
        MailException::throwsExceptionIfParamIsNull(Registry::get($this->_hashKey)->Send() , $message);
        return $this;
    }

    /**
     * configura o sender
     *
     * @param string[] $config
     * */
    public function sender (array $config = array())
    {
        $senderMethod = 'sender' . ucfirst($config['type']);
        // @codeCoverageIgnoreStart
        unset($config['type']);
        // @codeCoverageIgnoreEnd
        $message = __CLASS__ . "::{$senderMethod} não existe";
        IllegalArgumentException::throwsExceptionIfParamIsNull($this->hasMethod($senderMethod), $message);
        $this->$senderMethod($config);
    }

    /**
     * configura o sender para Smtp
     *
     * @param string[] $config
     * */
    public function senderSmtp (array $config = array())
    {
        # propriedades que devem ter seus nomes traduzidos
        # a traducao eh feita para o objeto PHPMail, mundando este componente
        # eh necessario rever esta necessidade
        $tmpDic = array(
            'useModeSecurity'  => 'SMTPAuth',
            'useModeKeepAlive' => 'SMTPKeepAlive'
        );

        $mail = Registry::get($this->_hashKey);

        foreach ($config as $key => $val) {
            $key = array_key_exists($key, $tmpDic) ? $tmpDic[$key] : $key;
            $key = ucfirst($key);
            $mail->$key = $val;
        }
    }

    /**
     * define o titulo da mensagem
     *
     * @param string $subject
     * @return \br\gov\sial\core\util\mail\Mail
     * */
    public function subject ($subject)
    {
        Registry::get($this->_hashKey)->Subject = trim($subject);
        return $this;
    }

    /**
     * define o destinario da mensagem
     *
     * @param string $email
     * @param string $name
     * @return Mail
     * @throws MailException
     * */
    public function to ($email, $name = NULL)
    {
        ob_start();
        Registry::get($this->_hashKey)->AddAddress($email, $this->toggle($name, self::_accountName($email)));
        $output = ob_get_clean();

        $assert = !('invalid address' == strtolower(substr($output, 0, 15)));
        MailException::throwsExceptionIfParamIsNull($assert, 'Destinatário inválido');
        return $this;
    }

    /**
     * define a quantidade maxima de caracteres por linha
     *
     * @param integer $maxLen
     * @return \br\gov\sial\core\util\mail\Mail
     * */
    public function wordWrap ($maxLen)
    {
        Registry::get($this->_hashKey)->SetWordWrap((integer) $maxLen);
        return $this;
    }
}