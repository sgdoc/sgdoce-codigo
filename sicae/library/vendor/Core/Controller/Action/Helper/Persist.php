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
class Core_Controller_Action_Helper_Persist extends Zend_Controller_Action_Helper_Abstract
{
    const PERSIST = 'ROUTE';

    protected static $_session;

    public function __construct()
    {
        if (NULL === static::$_session) {
            static::$_session = new Core_Session_Namespace(static::PERSIST, TRUE, TRUE);
        }

        static::$_session->unlock();
        static::$_session->setExpirationHops(1, NULL, TRUE);
    }

    public function getSession()
    {
        return static::$_session;
    }

    public function set($alias, $data)
    {
        static::$_session->{$alias} = $data;
        return $this;
    }

    public function has($alias)
    {
        return isset(static::$_session->{$alias});
    }

    public function clear()
    {
        static::$_session->unsetAll();
    }

    public function get($alias)
    {
        if (!$this->has($alias)) {
            return NULL;
        }

        return static::$_session->{$alias};
    }
}
