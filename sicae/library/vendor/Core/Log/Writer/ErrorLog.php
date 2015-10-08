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

class Core_Log_Writer_ErrorLog extends Zend_Log_Writer_Abstract
{
    /**
     * Create a new instance of Zend_Log_Writer_ZendMonitor
     *
     * @param  array|Zend_Config $config
     * @return Zend_Log_Writer_ZendMonitor
     */
    static public function factory($config)
    {
        return new self();
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event log data event
     * @return void
     */
    protected function _write($event)
    {
        $message  = $event['message'];

        if ($this->_formatter instanceof Zend_Log_Formatter_Interface) {
            $message = $this->_formatter->format($event);
        }

        error_log($message);
    }
}
