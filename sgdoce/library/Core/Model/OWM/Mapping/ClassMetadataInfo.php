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
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

class Core_Model_OWM_Mapping_ClassMetadataInfo implements ClassMetadata
{
    public $name;

    public $namespace;

    public $reflClass;

    public $customRepositoryClassName;

    public $rootEntityName;

    public $configKey;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function initializeReflection($reflService)
    {
        $this->reflClass = $reflService->getClass($this->name);
        $this->namespace = $reflService->getClassNamespace($this->name);

        if ($this->reflClass) {
            $this->name = $this->rootEntityName = $this->reflClass->getName();
        }
    }

    public function setCustomRepositoryClass($repositoryClassName)
    {
        if ($repositoryClassName !== null && strpos($repositoryClassName, '\\') === false
                && strlen($this->namespace) > 0) {
            $repositoryClassName = $this->namespace . '\\' . $repositoryClassName;
        }
        $this->customRepositoryClassName = $repositoryClassName;
    }

    public function getName()
    {
    }

    public function getIdentifier()
    {
    }

    public function getReflectionClass()
    {
    }

    public function isIdentifier($fieldName)
    {
    }

    public function hasField($fieldName)
    {
    }

    public function hasAssociation($fieldName)
    {
    }

    public function isSingleValuedAssociation($fieldName)
    {
    }

    public function isCollectionValuedAssociation($fieldName)
    {
    }

    public function getFieldNames()
    {
    }

    public function getIdentifierFieldNames()
    {
    }

    public function getAssociationNames()
    {
    }

    public function getTypeOfField($fieldName)
    {
    }

    public function getAssociationTargetClass($assocName)
    {
    }

    public function isAssociationInverseSide($assocName)
    {
    }

    public function getAssociationMappedByTargetField($assocName)
    {
    }

    public function getIdentifierValues($object)
    {
    }
}
