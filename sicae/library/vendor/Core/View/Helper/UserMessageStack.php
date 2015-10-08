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
/**
 * @package    Core
 * @subpackage View
 * @subpackage Helper
 * @name       UserMessageStack
 * @category   View Helper
 */
class Core_View_Helper_UserMessageStack extends Zend_View_Helper_Abstract
{
    public function userMessageStack()
    {
        $userMessage = '';
        $gw = Core_Messaging_Manager::getGateway('User');

        if (!$gw) {
            return '';
        }

        $packet = $gw->retrievePackets('User');

        if (0 === count($packet)) {
             return '';
        }

        foreach ($packet->getMessages('error') as $message) {
             $userMessage .= '<div class="alert alert-error">
                <button class="close" data-dismiss="alert">×</button>
                '.$message.'
                </div>';
        }

        foreach ($packet->getMessages('alert') as $message) {
             $userMessage .= '<div class="alert">
                <button class="close" data-dismiss="alert">×</button>
                '.$message.'
                </div>';
        }

        foreach ($packet->getMessages('success') as $message) {
             $userMessage .= '<div class="alert alert-success">
                <button class="close" data-dismiss="alert">×</button>
                '.$message.'
                </div>';
        }

        foreach ($packet->getMessages('info') as $message) {
             $userMessage .= '<div class="alert alert-info">
                <button class="close" data-dismiss="alert">×</button>
               '.$message.'
                </div>';
        }

        return $userMessage;
    }
}