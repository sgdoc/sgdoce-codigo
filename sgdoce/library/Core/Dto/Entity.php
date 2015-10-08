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
class Core_Dto_Entity extends Core_Dto_Base
{
    /**
     * @var object
     */
    protected $_entity;

    public function __construct($entity)
    {
        if (is_string($entity)) {
            $entity = new $entity();
        }

        if (!$entity instanceof Core_Model_Entity_Abstract) {
            throw new RuntimeException('');
        }

        $this->_entity = $entity;
    }

    public function __call($method, $args)
    {
        $command = substr($method, 0, 3);

        $args[0] = isset($args[0])
                 ? $args[0]
                 : NULL;

        if ('get' === $command) {
            return $this->_get($method);
        } else if ('set' === $command) {
            $this->_set($method, $args[0]);
        } else if ('add' === $command) {
            $this->_add($method, $args[0]);
        }
    }

    protected function _add($method, $arg)
    {
        if (method_exists($this->_entity, $method)) {
            $this->_entity->$method($arg);
        }
    }

    protected function _set($method, $arg)
    {
        if (method_exists($this->_entity, $method)) {
            $this->_entity->$method($arg);
        }
    }

    protected function _get($method)
    {
        if (!method_exists($this->_entity, $method)) {
            return NULL;
        }

        $value = $this->_entity->$method();
        /*if (is_object($value)) {
            // transformação recursiva dto
            $value = new self($value);
        }*/

        return $value;
    }

    public function __get($attr)
    {
        return NULL;
    }

    public function getEntity()
    {
        if (defined('APPLICATION_ENV')) {
            if ('development' === APPLICATION_ENV) {
                $trace = debug_backtrace();
                if (!is_subclass_of($trace[1]['class'], 'Core_ServiceLayer_Service_Abstract') &&
                    $trace[1]['class'] !== 'Core_Dto') {
                    throw new RuntimeException('Este método deve ser chamado somente no service.');
                }
            }
        }
        return $this->_entity;
    }
}