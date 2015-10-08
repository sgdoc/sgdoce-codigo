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
 * @name Role
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Role extends SIALAbstract
{
    /**
     * Nome da permissão.
     *
     * @var string
     * */
    private $_name;

    /**
     * Relação de itens da role.
     *
     * @var integer[]
     * */
    private $_items = array ();

    /**
     * Construtor.
     *
     * @param string $name
     * @param integer[] $items
     * @throws IllegalArgumentException
     * */
    public function __construct ($name, array $tems = array())
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull(trim($name), 'O nome informado é inválido');
        $this->_name = (string) $name;
        $this->addItems($tems);
    }

    /**
     * Limpa todas as permissões da regra.
     *
     * @return Role
     * */
    public function clean ()
    {
        $this->_items = array();
        return $this;
    }

    /**
     * Adiciona itens a regra.
     *
     * @param mixed[] $items
     * @return Role
     * */
    public function addItems (array $items = array())
    {
        foreach ($items as $key => $val) {
            $this->addItem($key, $val);
        }
        return $this;
    }

    /**
     * Adiciona permissão a Role.
     *
     * @param mixed $key
     * @param mixed $val
     * @return Role
     * */
    public function addItem ($key, $val)
    {
        $this->_items[$key] = $val;
        return $this;
    }

    /**
     * Recupera o nome da Role.
     * 
     * @return string
     * */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Verifica por chave se há permissão numa regra.
     *
     * @param mixed $key
     * @return boolean
     * */
    public function hasPermission ($key)
    {
        return (isset($this->_items[$key]) && TRUE === $this->_items[$key]);
    }
}