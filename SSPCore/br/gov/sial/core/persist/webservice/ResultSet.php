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
namespace br\gov\sial\core\persist\webservice;
use br\gov\sial\core\persist\ResultSet as ParentResultSet,
    br\gov\sial\core\persist\webservice\Connect;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage webservice
 * @name ResultSet
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class ResultSet extends ParentResultSet
{
    /**
     * @var Connect
     */
    private $_connect;

    /**
     * Construtor.
     *
     * @param Connect $connect
     * @param ResultSet
     * */
    public function __construct (Connect $connect, $resultset)
    {
        parent::__construct($resultset);
        $this->_connect = $connect;

        # Verifico o tipo do ResultSet
        if ('object' == gettype($resultset)) {
            $resultset = json_decode(json_encode($resultset),TRUE);
            $tmpArr = array();
            $this->_prepareObject($resultset,$tmpArr);
            $this->_resultSet = $tmpArr;
        } else {
            $this->_resultSet = simplexml_load_string(utf8_decode($resultset));
        }
    }

    /**
     * @param mixed[] $arrObj
     * @param mixed[] $arrResult
     */
    private function _prepareObject ($arrObj, &$arrResult)
    {
        foreach ($arrObj as $key => $elemnt) {
            if (is_array($elemnt)) {
                $this->_prepareObject($elemnt, $arrResult);
            } else {
                $arrResult[$key] = $elemnt;
            }
        }
    }

    /**
     * {@inheritdoc}
     * */
    public function fetch ()
    {
        return $this->_resultSet;
    }

    /**
     * {@inheritdoc}
     * */
    public function count ()
    {
        return (sizeof($this->_resultSet) -1);
    }
}