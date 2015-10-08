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
namespace br\gov\sial\core\persist\ldap;
use br\gov\sial\core\persist\ldap\Connect;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage ldap
 * @name ResultSet
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class ResultSet extends \br\gov\sial\core\persist\ResultSet
{

    /**
     * @var Connect
     */
    private $_connect;

    /**
     * @var \stdClass[]
     */
    private $_localResultSet;

    /**
     * @var integer
     */
    private $_ldapCount;

    /**
     * Construtor.
     *
     * @param Connect $connect
     * @param resource $resultSet
     * */
    public function __construct (Connect $connect, $resultset)
    {
        $this->_connect = $connect;
        parent::__construct((object) array('queryString' => $this->_fetch($resultset)));
        reset($this->_localResultSet);
    }

    /**
     * Fetch.
     * 
     * @return Resultset
     */
    public function fetch ()
    {
        return next($this->_localResultSet);
    }

    /**
     * {@inheritdoc}
     * @todo trocar o _localResultSet para somente _resultSet
     * @todo retirar o acrescimo do NULL para o primeiro ponteiro do array (utilizar array_unshift)
     * */
    private function _fetch ($resultSet)
    {
        $resultSet = ldap_get_entries($this->_connect->getResource(), $resultSet);
        $fieldList = $this->_connect->getParams();

        $this->_localResultSet[] = $queryString = NULL;

        $this->_ldapCount = $resultSet['count'];
        unset($resultSet['count']);

        foreach ($resultSet as $key => $value) {
            $tmpResult = new \stdClass();
            foreach ($fieldList as $field) {
                if (isset($value[$field])) {
                    $tmpResult->$field = is_array($value[$field]) ? $value[$field][0] : $value[$field];
                } else {
                    $tmpResult->$field = NULL;
                }
                $queryString[] = $field;
            }
            $this->_localResultSet[] = $tmpResult;
        }

        return $queryString ? implode(',', $queryString) : '' ;
    }

    /**
     * {@inheritdoc}
     * @todo implementar e garantir a compatibilidade entre os bancos disponiveis
     * */
    public function count ()
    {
        return $this->_ldapCount;
    }
}