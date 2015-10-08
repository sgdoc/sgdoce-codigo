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

/**
 * SISICMBio
 *
 * Classe para Configuração padrão da aplicação
 *
 * @package      Core
 * @name         Configuration
 * @version     1.0.0
 * @since        2012-06-26
 */

class Core_Configuration extends Zend_Config
{
    protected static $_instance;

    protected static $_entityName = 'Core\Model\Entity\SicaConfiguracao';

    final public function __construct()
    {
        $backtrace  = debug_backtrace();
        $class      = $backtrace[1]['class'];

        if ($class !== get_class($this)) {
            throw new RuntimeException('Não permitida chamada externa');
        }

        $method     = strtolower($backtrace[1]['function']);

        if ($method !== 'getinstance') {
            throw new RuntimeException('Não permitida chamada externa');
        }

        parent::__construct(array(), TRUE);
    }

    public static function __callStatic($method, array $args)
    {
        $object = static::getInstance();

        $command = substr($method, 0, 3);
        $const   = substr($method, 3);

        if ('get' !== $command) {
            throw new BadMethodCallException("Método inexistente. {$command}{$const}");
        }

        $filter = new Zend_Filter_Word_CamelCaseToUnderscore();
        $const  = strtoupper($filter->filter($const));

        if (!isset($object->$const)) {
            throw new BadMethodCallException("Método inexistente. '{$const}'");
        }

        return $object->get($const);
    }

    public static function getInstance()
    {
        if (NULL === self::$_instance) {
            static::$_instance = new static();
            static::$_instance->initConstants();
        }

        return static::$_instance;
    }

    protected function initConstants()
    {
        $emManager = \Zend_Registry::get('doctrine')
                            ->getEntityManager();

        $query = $emManager->createQuery(sprintf('select c from %s c', static::$_entityName));
        $query->useResultCache(TRUE, 3600, 'SGDOCe_Main_Application_Constant');
        $consts = $query->getResult();

        $emManager->clear(static::$_entityName);

        foreach ($consts as $const) {
            $name        = $this->_normalizeName($const->getNoConstante());
            $this->$name = $const->getSqValor();
        }

        $this->setReadOnly();
    }

    public static function setEntityName($emName)
    {
        if (!is_subclass_of($emName, 'Core_Model_Entity_Configuracao_Interface')) {
            throw new InvalidArgumentException("Argumento '{$emName}' deve ser uma entidade do tipo Core_Model_Entity_Configuracao_Interface.");
        }

        static::$_entityName = $emName;
    }

    protected function _normalizeName($const)
    {
        return strtoupper(str_replace(' ', '_', $const));
    }
}
