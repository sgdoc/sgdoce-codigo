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
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\MappedSuperclass
 */
/**
 * @package     Core
 * @subpackage  Model
 * @subpackage  Entity
 * @name        Abstract
 * @category    Abstract Model
 */
class Core_Model_Entity_Abstract
{
    protected function assert($column,$value,$entity)
    {
        $doctrine = Zend_Registry::get('doctrine');
        $metadata = $doctrine->getEntityManager()->getClassMetadata(get_class($entity));
        $field = $metadata->fieldMappings[$column];

        if (!$field['nullable']){
            if (empty($value)){
                throw new \Core_Exception_ServiceLayer('Campo de preenchimento obrigatório não preenchido');
            }
        }

        if (!empty($field['length'])){
            if (strlen($value) > $field['length']){
                throw new \Core_Exception_ServiceLayer('Campo de preenchimento obrigatório não preenchido');
            }
        }
    }

    /**
     * @return Core_Messaging_Gateway
     */
    public function getMessaging()
    {
        return Core_Messaging_Manager::getGateway('Model');
    }

    public function fromArray(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . $key;
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }

        return $this;
    }

    public function toArray()
    {
        $data = array();
        $methods = get_class_methods($this);
        foreach($methods as $method) {
            if ('get' === substr($method, 0, 3)) {
                $data[lcfirst(substr($method, 3))] = $this->$method();
            }
        }
        return $data;
    }

    public function toArrayIgnoreFields(array $ignoreFields = array())
    {
        $data          = $this->toArray();
        $ignoreFields += (array) $this->getIgnoreFields();

        foreach ($ignoreFields as $field) {
            if (array_key_exists($field, $data)) {
                unset($data[$field]);
            }
        }

        return $data;
    }

    public function getIgnoreFields()
    {
        return array('stRegistroAtivo', 'txSenha');
    }
}
