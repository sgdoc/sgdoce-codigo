<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
/**
 * Classe para Bootstrap
 *
 * @category	Bootstrap
 * @name		Bootstrap
 * @version	 1.0.0
 */
class Bootstrap extends Core_Application_Bootstrap_Bootstrap
{

    public function _initSession()
    {
        Zend_Session::start();
    }

    public function _initDoctrine()
    {
        $doctrine = NULL;
        if ($this->hasPluginResource('doctrine')) {
            $plugin = $this->getPluginResource('doctrine');
            $doctrine = $plugin->init();

            $platform  = $doctrine->getConnection()
                                 ->getDatabasePlatform();

            $options  = $plugin->getOptions();
            $options  += array('dbal' =>
                array(
                    'defaultConnection' => NULL,
                    'connections'       => array()
                )
            );

            $connnectionName = $options['dbal']['defaultConnection'];

            if (!isset($options['dbal']['connections'][$connnectionName])) {
                $options['dbal']['connections'][$connnectionName] = array();
            }

            if (!isset($options['dbal']['connections'][$connnectionName]['registerDoctrineTypeMapping'])) {
                $options['dbal']['connections'][$connnectionName]['registerDoctrineTypeMapping'] = array();
            }

            $types = $options['dbal']['connections'][$connnectionName]['registerDoctrineTypeMapping'];

            foreach ((array) $types as $dbType => $typeDoctrine) {
                $platform->registerDoctrineTypeMapping($dbType, $typeDoctrine);
            }
        }

        return $doctrine;
    }

    protected function _initConfigs()
    {
        $options = $this->getOptions();

        unset($options['config']);

        Zend_Registry::set('configs', $options);

        return $options;
    }
}

