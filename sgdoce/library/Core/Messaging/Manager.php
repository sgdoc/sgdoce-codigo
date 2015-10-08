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
 * Componente de mensageria - Gateway
 *
 * @package    Core
 * @subpackage Messaging
 * @name       Manager
 * @category   Gerente
 * @author     Pablo Santiago Sánchez <phackwer@gmail.com>
 */
abstract class Core_Messaging_Manager
{
    /**
     * Coleção de Gateways
     * @var array
     */
    private static $_gateways;

    /**
     * Adiciona um Gateway com um identificador específico e um adapter específico
     * @param $gwId - Identificador
     * @param $gwAdapter - Instância ou classe do adaptador a ser utilizado
     * @param $config - Configurações para o Adapter (vide documentação do adapter)
     * @return Core_Messaging_Gateway
     */
    public static function addGateway($gwId, $gwAdapter = null, $configAdapter = null)
    {
        if (!is_array(self::$_gateways)) {
            self::$_gateways = array();
        }

        if (!isset(self::$_gateways[$gwId])) {
            self::$_gateways[$gwId] = new Core_Messaging_Gateway($gwId, $gwAdapter, $configAdapter);
        }

        return self::$_gateways[$gwId];
    }

    /**
     * Obtém o gateway especificado
     * @param $gwId - Name of the connection
     * @return Core_Messaging_Gateway
     */
    public static function getGateway($gwId)
    {
        if (isset(self::$_gateways[$gwId])) {
            return self::$_gateways[$gwId];
        }

        return self::addGateway($gwId);
    }

    /**
     * Todos os gateways
     * @param $gwId - Name of the connection
     * @return Core_Messaging_Gateway
     */
    public static function getAll()
    {
        if (is_array(self::$_gateways)) {
            return self::$_gateways;
        }

        return null;
    }
}