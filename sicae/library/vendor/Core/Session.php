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
class Core_Session extends Zend_Session
{
    public static function namespaceGet($namespace)
    {
        $currentData  = (isset($_SESSION[$namespace]) && is_object($_SESSION[$namespace])) ?
            $_SESSION[$namespace] : new stdClass();
        $expiringData = (isset(self::$_expiringData[$namespace]) && is_object(self::$_expiringData[$namespace])) ?
            self::$_expiringData[$namespace] : new stdClass();

        // @todo melhorar performance com SPL Iterator ou Datastructure
        $data = array();
        if (is_object($currentData)) {
            if (method_exists($currentData, 'toArray()')) {
                $data = $currentData->toArray();
            } else {
                foreach ($currentData as $key => $value) {
                    $data[(string) $key] = $value;
                }
            }
        }

        if (is_object($expiringData)) {
            if (method_exists($expiringData, 'toArray()')) {
                $data = array_merge($data, $expiringData->toArray());
            } else {
                foreach ($expiringData as $key => $value) {
                    $data[(string) $key] = $value;
                }
            }
        }

        if (count($data)) {
            return (object) $data;
        }

        return parent::namespaceGet($namespace);
    }

    public static function getIterator()
    {
        if (parent::$_readable === false) {
            /** @see Zend_Session_Exception */
            require_once 'Zend/Session/Exception.php';
            throw new Zend_Session_Exception(parent::_THROW_NOT_READABLE_MSG);
        }

        $spaces  = array();
        if (isset($_SESSION)) {
            $spaces = array_keys($_SESSION);
            foreach($spaces as $key => $space) {
                if (!strncmp($space, '__', 2) ||
                    !is_array($_SESSION[$space]) &&
                    !is_object($_SESSION[$space]))
                {
                    unset($spaces[$key]);
                }
            }
        }

        return new ArrayObject(array_merge($spaces, array_keys(parent::$_expiringData)));
    }
}
