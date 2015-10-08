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
 * @package     Core
 * @subpackage  Util
 * @name        Class
 * @category    Utilities for class manipulation
 */
class Core_Util_Class
{
    public static function resolveNameEntity($className, $entityManager = null)
    {
        if (false !== strpos($className, ':')) {
            list($namespaceAlias, $simpleClassName) = explode(':', $className);

            if (null === $entityManager) {
                $entityManager = Zend_Registry::get('doctrine')
                                    ->getEntityManager();
            }

            if (is_string($entityManager)) {
                $entityManager = Zend_Registry::get('doctrine')
                                    ->getEntityManager($entityManager);
            }

            $className = $entityManager->getConfiguration()->getEntityNamespace($namespaceAlias) . '\\' . $simpleClassName;
        }

        return $className;
    }

    public static function newEntity($entity, array $data = array())
    {
        $class  = static::resolveNameEntity($entity);
        $object = new $class();

        if (count($data) && method_exists($object, 'fromArray')) {
            $object->fromArray($data);
        }

        return $object;
    }
}
