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
 * Componente de mensageria - Adapter para Doctrine
 *
 * @package    Core
 * @subpackage Messaging
 * @subpackage Adapter
 * @name       Doctrine
 * @category   Adapter
 * @author     Pablo Santiago Sánchez <phackwer@gmail.com>
 */
class Core_Messaging_Adapter_Doctrine extends Core_Messaging_Adapter_Abstract
{
    /**
     * Construtor
     */
    public function __construct($configs = null)
    {
        //@TODO
    }

    /**
     * Persiste os pacotes de mensagens
     */
    protected function _persistPackets()
    {
        //@TODO
    }

    /**
     * Recupera os pacotes de mensagens da persistência
     */
    protected function _aquirePackets()
    {
        //@TODO
    }
}