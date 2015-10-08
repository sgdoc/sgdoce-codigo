<?php

/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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

/**
 * SISICMBio
 *
 * Classe Bootstrap
 *
 * @package      Application
 * @name         Bootstrap
 * @version     1.0.0
 * @since        2012-10-10
 */
class Bootstrap extends Core_Application_Bootstrap_Bootstrap
{

    /**
     * Inicializa os storage para a Session
     * @return
     */
    protected function _initAuthStorage()
    {
        $storage = new Core_Auth_Storage_Session();
        Zend_Auth::getInstance()->setStorage($storage);
    }

    protected function _initConfiguracao()
    {
        $this->bootstrap('doctrine');
        Core_Configuration::setEntityName('Sica\Model\Entity\Configuracao');
        Core_Configuration::getInstance();
    }

    protected function _initSession()
    {
        Zend_Session::start(true);
        if (Zend_Session::sessionExists()) {
            $phpSettings = $this->getOption('phpSettings');
            $sessionConfig = $phpSettings['session'];
            // Prorrogando o tempo de vida do cookie ;)
            setcookie(
                $sessionConfig['name'],
                Zend_Session::getId(),
                $sessionConfig['cookie_lifetime'] + time(),
                $sessionConfig['cookie_path'],
                $sessionConfig['cookie_domain'],
                $sessionConfig['cookie_secure'],
                $sessionConfig['cookie_httponly']
            );
        }
    }

    protected function _initMailer()
    {
        $this->bootstrap('mail');
        $this->bootstrap('view');

        $options = $this->getPluginResource('mail')->getOptions();
        $view = $this->getResource('view');
        Core_Mailer::setDefaultLayoutOptions($options);
        Core_Mailer::setDefaultView($view);
        $mailer = new Core_Mailer(new Zend_Mail('utf-8'));

        Core_Registry::setMailer($mailer);

        return $mailer;
    }

}
