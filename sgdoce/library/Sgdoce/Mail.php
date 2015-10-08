<?php

/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

/**
 * Description of Constants
 * @author Rafael Yoo <rafael.yoo.terceirizado@icmbio.gov.br>
 */
class Sgdoce_Mail
{
    /**
     * @var \Zend_Mail
     */
    protected $_objZMail = null;

    /**
     * @var
     */
    protected $_config = null;

    /**
     * @var string
     */
    protected $_body = null;

    /**
     * @var string
     */
    protected $_subject = null;

    /**
     * @var array
     */
    protected $_exceptions = null;

    /**
     * @var array
     */
    protected $_recipients = null;

    /**
     * @var \Zend_Validate
     */
    protected $_validators = null;

    /**
     * @var integer
     */
    protected $_interval   = 0;

    /**
     * @var array
     */
    protected $_toAlias = array(
        'para' => 'addTo',
        'cc'   => 'addcc',
        'cco'  => 'addBcc',
    );

    /**
     * @return \Zend_Mail
     */
    public function getObjZMail ()
    {
        return $this->_objZMail;
    }

    /**
     * @param \Zend_Mail $_objZMail
     */
    public function setObjZMail (\Zend_Mail $_objZMail )
    {
        $this->_objZMail = $_objZMail;
        return $this;
    }

    /**
     * @return string
     */
    public function getBodyHtml ()
    {
        return $this->_body;
    }

    /**
     * @param string $body
     */
    public function setBodyHtml ( $body )
    {
        $this->_body = $body;

        $this->getObjZMail()
             ->setBodyHtml($this->_body);

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject ()
    {
        return $this->_subject;
    }

    /**
     * @param string $_subject
     */
    public function setSubject ($_subject)
    {
        $this->_subject = utf8_decode($_subject);

        $this->getObjZMail()
             ->setSubject($this->_subject);

        return $this;
    }

    /**
     * @return array
     */
    public function getExceptions ()
    {
        return $this->_exceptions;
    }

    /**
     * @param mixed $_exceptions
     */
    public function setExceptions ( $_exceptions )
    {
        $this->_exceptions[] = $_exceptions;
        return $this;
    }

    /**
     * @return array
     */
    public function getRecipients()
    {
        return $this->_recipients;
    }

    /**
     * @return \Zend_Validate
     */
    public function getValidators ()
    {
        return $this->_validators;
    }

    /**
     * @param \Zend_Validate $validators
     */
    public function setValidators (\Zend_Validate $validators)
    {
        $this->_validators = $validators;
        return $this;
    }

    /**
     * @return integer
     */
    public function getInterval ()
    {
        return $this->_interval;
    }

    /**
     * Intervalo de envio em microsegundos. Ex. 2000000 = 2 segundos.
     *
     * @param integer $interval
     */
    public function setInterval ($interval)
    {
        $this->_interval = $interval;
        return $this;
    }

    /**
     * @return type
     */
    public function getConfig ()
    {
        return $this->_config;
    }

    /**
     * @param type $config
     */
    public function setConfig ($config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * @param array $recipients
     * @return \Sgdoce_Mail
     */
    public function setRecipients( $recipients, $multiple = false )
    {
        $objZValidate = $this->getValidators();

        if( $multiple == false ) {

            if( is_array( $recipients )
                && count( $recipients ) > 0 ) {

                foreach( $recipients as $alias => $recipient ) {

                    $name  = key($recipient);
                    $email = current($recipient);

                    if( is_string($name) ) {
                        $name = null;
                    }

                    if( $objZValidate->isValid($email) ) {

                        if( isset($this->_toAlias[$alias]) ) {

                            $method = $this->_toAlias[$alias];
                            $this->getObjZMail()
                                 ->$method($email, $name);

                        } else {

                            $this->setExceptions(array(
                                $email => "Formato de argumento ( {$alias} => {$email} ) inválido, use Ex. (" . implode(", ", $this->_toAlias) . ").",
                            ));

                        }

                    } else {

                        $this->setExceptions(array(
                            $recipients => $objZValidate->getMessages(),
                        ));

                    }

                }

            } else if( is_string($recipients) ) {

                if( $objZValidate->isValid($recipients) ) {

                    $this->getObjZMail()
                         ->addTo($recipients);

                } else {

                    $this->setExceptions(array(
                        $recipients => $objZValidate->getMessages(),
                    ));

                }

            } else {

                $this->setExceptions("Argumento Inválido.");

            }

            $exceptions = $this->getExceptions();

            if( count($exceptions) ) {
                throw new \Zend_Mail_Exception("Ocorreu um erro ao enviar o(s) email(s), verifique a lista de erros.");
            }
            $this->_recipients = false;
        } else {
            $this->_recipients = $recipients;
        }

        return $this;
    }

    /**
     * @return void
     */
    public function init()
    {
        $objZMail = new \Zend_Mail();
        $this->setObjZMail($objZMail);

        $objZValidate = new \Zend_Validate();
        $objZValidate->addValidator(new Zend_Validate_EmailAddress());
        $this->setValidators($objZValidate);

        $config = \Zend_Registry::get('configs');
        $this->setConfig($config['resources']['mail']);
    }

    /**
     * @return void
     */
    public function __construct ()
    {
        $this->init();
    }


    /**
     * @return \Sgdoce_Mail Objeto Sgdoce_Email
     */
    public function send()
    {
        $recipients     = $this->getRecipients();
        $objZValidate   = $this->getValidators();

        if( is_array($recipients)
            && count($recipients) ) {

            foreach( $recipients as $key => $recipient ) {

                $name  = key($recipient);
                $email = current($recipient);

                if( is_string($name) ) {
                    $name = null;
                }

                if ( $objZValidate->isValid($email) ) {
                    $this->getObjZMail()
                         ->addTo($email, $name);
                    $this->getObjZMail()
                         ->send();
                    $this->getObjZMail()
                         ->clearRecipients();
                } else {
                    $this->setExceptions(array(
                        $recipients => $objZValidate->getMessages(),
                    ));
                }

                $interval = $this->getInterval();

                if( $interval > 0 ) {
                    usleep($interval);
                }

            }

            $exceptions = $this->getExceptions();

            if( count($exceptions) ) {
                throw new \Exception("Ocorreu um erro ao enviar o(s) email(s), verifique a lista de erros.");
            }
        } else {
            $this->getObjZMail()
                 ->send();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function prepareBodyHtml( $template, $arguments )
    {
        $config = $this->getConfig();

        $view = new \Zend_View();
        $view->addScriptPath($config['layoutPath']);

        foreach( $arguments as $alias => $argument ) {
            $view->assign($alias, $argument);
        }

        $this->setBodyHtml(utf8_decode($view->render($template)))
             ->_buildHtml();

        return $this;
    }

    /**
     * faz o parce do html localizando tags img para gerar anexos dos arquivos
     * o src da imagem deve ser conforme exemplo
     *
     * @code <img src="file://img/teste.png" alt="Sgdoc" />
     */
    private function _buildHtml()
    {
        $matches = array();
        preg_match_all("#<img.*?src=['\"]file://([^'\"]+)#i",
                       $this->getBodyHtml(true),
                       $matches);
        $matches = array_unique($matches[1]);
        if (count($matches ) > 0) {
            $this->getObjZMail()->setType(Zend_Mime::MULTIPART_RELATED);
            foreach ($matches as $key => $filename) {
                $fullFilename = APPLICATION_PATH . '/../public/' . ltrim($filename,'/');
                if (is_readable($fullFilename)) {
                    $at = $this->getObjZMail()->createAttachment(file_get_contents($fullFilename));
                    $at->type = $this->mimeByExtension($fullFilename);
                    $at->disposition = Zend_Mime::DISPOSITION_INLINE;
                    $at->encoding = Zend_Mime::ENCODING_BASE64;
                    $at->id = 'cid_' . md5_file($fullFilename);
                    $this->setBodyHtml(str_replace('file://' . $filename,
                                       'cid:' . $at->id,
                                       $this->getBodyHtml()),
                                       'UTF-8',
                                       Zend_Mime::ENCODING_8BIT);
                }
            }
        }
        return $this;
    }

    public function mimeByExtension($filename)
    {
        if (is_readable($filename) ) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            switch ($extension) {
                case 'gif':
                    $type = 'image/gif';
                    break;
                case 'jpg':
                case 'jpeg':
                    $type = 'image/jpg';
                    break;
                case 'png':
                    $type = 'image/png';
                    break;
                default:
                    $type = 'application/octet-stream';
            }
      }

      return $type;
  }


}