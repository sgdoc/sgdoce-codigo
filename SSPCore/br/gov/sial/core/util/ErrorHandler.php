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
namespace br\gov\sial\core\util;
use br\gov\sial\core\SIALAbstract;


/**
 * SIAL
 *
 * manipulacao de Erros
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @name ErrorHandler
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class ErrorHandler extends SIALAbstract
{
    /**
     * @var integer
     */
    private $_erroNumber;

    /**
     * @var String
     */
    private $_erroStr;

    /**
     * Construtor do Class de Error
     */
    public function setError ()
    {
        set_error_handler(array(&$this, 'errorHandlerSial'));
    }

    /**
     * Trata dos erros, warnings, notices, etc, gerados
     * @param integer $errNo
     * @param string $errStr
     * @param string $errFile
     * @param string $errLine
     */
    public function errorHandlerSial ($errNo, $errStr, $errFile, $errLine)
    {
        $this->_erroNumber = $errNo;
        $this->_erroStr    = $errStr;

        return $this;
    }

    /**
     * Retorna o numero do erro
     *
     * @return integer
     */
    public function getErroNumber ()
    {
        return $this->_erroNumber;
    }

    /**
     * Retorna a string do erro
     *
     * @return string
     */
    public function getErroStr ()
    {
        return $this->_erroStr;
    }
}