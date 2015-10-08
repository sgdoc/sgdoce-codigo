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
class Core_Registry extends Zend_Registry
{
    const MESSAGE = 'MESSAGE_UC';

    const CONTAINERS = 'CONTAINERS';

    const ACL  = 'acl';

    public static function setMessage(Zend_Translate $message)
    {
        if (!$message->getAdapter() instanceof Core_Translate_Message) {
            throw new InvalidArgumentException('O adapter dever ser Core_Translate_Message');
        }

        static::set(static::MESSAGE, $message);
    }

    public static function getMessage()
    {
        return static::get(self::MESSAGE);
    }

    public static function getContainers()
    {
        if (static::isRegistered(static::CONTAINERS)) {
            return (array) static::get(static::CONTAINERS);
        }

        return array();
    }

    public static function setContainer($index, $container)
    {
        $containers = static::getContainers();
        $containers[$index] = $container;

        static::set(static::CONTAINERS, $containers);
    }

    public static function setAcl($acl)
    {
        static::set(static::ACL, $acl);
    }

    public static function getAcl()
    {
        static::get(static::ACL);
    }
}
