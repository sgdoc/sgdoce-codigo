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
 * @category    Dto
 * @package     Core
 * @name        Dto
 */
abstract class Core_Dto
{
    public static function factoryFromData(array $data, $type, $options = array())
    {
        try {
            switch (strtolower($type)) {
                case 'entity':
                    if (!isset($options['entity'])) {
                        throw new Core_Dto_Exception_EntityNotSet();
                    }

                    $dto =  new Core_Dto_Entity($options['entity']);

                    $mapping = NULL;
                    if (isset($options['mapping'])) {
                        $mapping = static::_createMapping($data, $options['mapping']);
                    }

                    return static::_populateObjectFromArray(
                        $dto,
                        $data,
                        $mapping
                    );
                    break;
                case 'search':
                    return new Core_Dto_Search($data);
                    break;
                default:
                    return new $type($data, $options);
                    break;
            }
        } catch (Core_Dto_Exception $e) {
            trigger_error('DTO Fatal Error: could not create a DTO Object.', E_USER_ERROR);
        }
    }

    protected static function _populateObjectFromArray($object, array $data, Core_Dto_Mapping_Entity $mapping = NULL)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . $key;

            if (NULL !== $mapping) {

                if ($mapping->has($key)) {
                    $methodGet = 'get' . $key;
                    $value     = $mapping->$methodGet();
                    if (is_array($value)) {
                        foreach ($value as $valueEntity) {
                             $object->{$methodGet}()->add($valueEntity->getEntity());
                        }
                        continue;
                    }
                    $value = $value->getEntity();
                }
            } else if (is_object($value) && !$value instanceof Core_Dto_Abstract) {
                throw new RuntimeException('Invalid argument.');
            }
            $object->$method($value);
        }

        return $object;
    }

    public static function factoryFromEntity(Core_Model_Entity_Abstract $entity)
    {

    }

    protected static function _createMapping($data, $mapping)
    {
        $map = array();

        foreach ($data as $key => $value){
            if (array_key_exists($key, $mapping)) {
                $map[$key] = $value;
            }
        }

        return new Core_Dto_Mapping_Entity($map, $mapping);
    }
}
