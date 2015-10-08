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
namespace br\gov\sial\core\acl;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage acl
 * @name Acl
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Acl extends SIALAbstract
{
     /**
     * Permissões
     *
     * @var Role[]
     * */
    private $_roles = array();

    /**
     * User ID
     *
     * @var integer
     * */
    private $_owner;

    /**
     * Construtor.
     *
     * @param integer $owner
     * @throws IllegalArgumentException
     * */
    public function __construct ($owner)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull(0 < ($this->_owner = (integer) $owner), 'O identificador do dono da ACL não pode ser nulo.');
    }

    /**
     * Adiciona permissão a ACL.
     *
     * @param Role $role
     * @return Acl
     * */
    public function addRole (Role $role)
    {
        /* a forma de definicao da role abaixo elimina duas role de mesmo nome */
        $this->_roles[self::handleName($role->getName())] = $role;
        return $this;
    }

    /**
     * Remove todas as permissões.
     *
     * @return Acl
     * */
    public function clean ()
    {
        $this->_roles = array();
        return $this;
    }

    /**
     * Verifica se a Acl possui uma permissão.
     *
     * @param string $roleName
     * @return boolean
     * */
    public function hasRole ($roleName)
    {
        return isset($this->_roles[self::handleName($roleName)]);
    }

    /**
     * Converte a string informada em nome de permissão.
     *
     * @param string $roleName
     * @return string
     * @throws IllegalArgumentException
     * */
    public static function handleName ($roleName)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull(!empty($roleName), 'Nome da Role é inválida');
        $roleName = preg_replace('/[^a-zA-Z]/', '_', $roleName);
        return strtoupper($roleName);
    }

    /**
     *
     * Verifica permissão por chave de identificação.
     *
     * @param mixed $key
     * @return boolean
     */
    public function hasPermission ($key)
    {
        foreach ($this->_roles as $keyRole => $val) {
            if (TRUE == $this->_roles[$keyRole]->hasPermission($key)) {
                return TRUE;
            }
        }
        return FALSE;
    }
}