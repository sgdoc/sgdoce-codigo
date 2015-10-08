<?php
/*
 * Copyright 2013 ICMBio
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
namespace br\gov\sial\core\util\flashMessage;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\Session,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * Utilitário de Flash Message
 *
 * @package br.gov.sial.core.util
 * @subpackage flashMessage
 * @name FlashMessage
 * @code
 * <?php
 *  $flash = new FlashMessage();
 *
 *  // Default type - warning.
 *  $flash->setFlashMessage('Atention... ');
 *  print_r($flash->getFlashMessage());
 *
 *  // Custom type - error.
 *  $flash->setFlashMessage('This field is required', 'error');
 *  print_r($flash->getFlashMessage('error'));
 *
 *  // Check if is a flash message for display.
 *  if ($flash->hasFlashMessage('success')) {
 *      print_r($flash->getFlashMessage('success'));
 *  }
 * ?>
 * @encode
 * */
class FlashMessage extends SIALAbstract
{
    /**
     * Tipo de mensagem padrão - warning.
     * @var string
     */
    private $_defaultType;

    /**
     *
     * @var Session
     */
    private $_session;

    /**
     * Construtor.
     * @param string $type
     */
    public function __construct ($type = NULL)
    {   $this->_defaultType = (!empty($type)) ? $type : 'warning';
        $this->_session = Session::start($this->getNamespace());
    }

    /**
     * Cria flash message
     * @param string $message
     * @param string $type
     */
    public function setFlashMessage ($message, $type = NULL)
    {
        if (empty($message)) {
            throw IllegalArgumentException::argumentCantBeNull('message');
        }

        $this->_session->set($this->_getType($type), $message);
    }

    /**
     * Recupera flash message
     * @param string $type
     * @return string
     */
    public function getFlashMessage ($type = NULL)
    {
        $type  = $this->_getType($type);
        $flash = $this->_session->get($type);
        $this->_clean($type);

        return $flash;
    }

    /**
     * Verifica se existe(m) flash message(s)
     * @param string $type
     * @return boolean
     */
    public function hasFlashMessage ($type = NULL)
    {
        $flash = $this->_session->get($this->_getType($type));

        if (empty($flash)) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Limpa uma mensagem da sessão após ser recuperada pela aplicação.
     * @param string $type
     */
    private function _clean ($type = NULL)
    {
        $this->_session->delete($this->_getType($type));
    }

    /**
     * Recupera o tipo de flash message
     * @param string $type
     * @return string
     */
    private function _getType ($type = NULL)
    {
        return ($type == NULL) ? $this->_defaultType : $type;
    }

    /**
     * Recupera o padrão de tipo de flash message.
     * @return string
     */
    public function getDefaultType ()
    {
        return $this->_defaultType;
    }
}