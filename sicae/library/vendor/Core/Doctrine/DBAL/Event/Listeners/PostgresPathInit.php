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
use Doctrine\DBAL\Event\ConnectionEventArgs,
    Doctrine\DBAL\Events,
    Doctrine\Common\EventSubscriber;
/**
 * Listener de conexão Postgres - define determinadas configurações apenas para este banco
 *
 * @package     Core
 * @subpackage  Doctrine
 * @subpackage  DBAL
 * @subpackage  Event
 * @subpackage  Listeners
 * @name        PostgresPathInit
 * @category    Listener
 */
class Core_Doctrine_DBAL_Event_Listeners_PostgresPathInit implements EventSubscriber
{
    /**
     * @param ConnectionEventArgs $args
     * @return void
     */
    public function postConnect(ConnectionEventArgs $args)
    {
        $params = $args->getConnection()->getParams();
        $params += array('schemas' => null);

        $schemas = implode(',', (array) $params['schemas']);
        $sql = "SET search_path TO " . $schemas;
        $args->getConnection()->executeUpdate($sql);
    }

    public function getSubscribedEvents()
    {
        return array(Events::postConnect);
    }
}
