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
class Core_Mailer
{
    protected static $_defaultLayout;

    protected static $_defaultView;

    protected $_layout;

    protected $_view;

    protected $_mailer;

    public function __construct(Zend_Mail $mailer)
    {
        $this->setMailer($mailer);
    }

    public function __call($method, $args)
    {
        if (!method_exists($this->_mailer, $method)) {
            throw new BadMethodCallException();
        }

        return call_user_func_array(array($this->_mailer, $method), $args);
    }

    public function setMailer(Zend_Mail $mailer)
    {
        $this->_mailer = $mailer;
    }

    public function getMailer()
    {
        return $this->_mailer;
    }

    public function setLayout(Zend_Layout $layout)
    {
        if ($layout->getMvcEnabled()) {
            throw new InvalidArgumentException();
        }

        $this->_layout = $layout;
        return $this;
    }

    public function getLayout()
    {
        if (NULL === $this->_layout) {
            $this->setLayout(static::getDefaultLayout());
        }
        return $this->_layout;
    }

    public function setView(Zend_View $view)
    {
        $this->_view = $view;
        return $this;
    }

    public function getView()
    {
        if (NULL === $this->_view) {
            $this->setView(static::getDefaultView());
        }

        return $this->_view;
    }

    public static function setDefaultLayout(Zend_Layout $layout)
    {
        static::$_defaultLayout = $layout;
    }

    public static function getDefaultLayout()
    {
        if (NULL === static::$_defaultLayout) {
            static::setDefaultLayout(new Zend_Layout());
        }

        return static::$_defaultLayout;
    }

    public static function setDefaultView(Zend_View $view)
    {
        static::$_defaultView = $view;
    }

    public static function getDefaultView()
    {
        if (NULL === static::$_defaultView) {
            static::setDefaultView(new Zend_View());
        }

        return static::$_defaultView;
    }


    public static function setDefaultLayoutOptions(array $options)
    {
        static::getDefaultLayout()->setOptions($options);
    }

    public function setBodyHtml($html, $template = NULL)
    {
        $layout = $this->getLayout();
        $layout->assign('content', $this->getView()->render($html));
        $this->_mailer->setBodyHtml($layout->render());
        return $this;
    }
}
