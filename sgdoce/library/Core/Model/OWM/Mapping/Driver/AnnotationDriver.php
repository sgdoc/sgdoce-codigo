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
use Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\Persistence\Mapping\ClassMetadata,
    Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver as AbstractAnnotationDriver;

class Core_Model_OWM_Mapping_Driver_AnnotationDriver extends AbstractAnnotationDriver
{
    /**
     * {@inheritDoc}
     */
    protected $entityAnnotationClasses = array(
        'Core\Model\OWM\Mapping\Endpoint' => 1,
        'Core_Model_Entity_Abstract'      => 2
    );

    /**
     * {@inheritDoc}
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        // this happens when running annotation driver in combination with
        // static reflection services. This is not the nicest fix
        $class = new \ReflectionClass($metadata->name);

        $classAnnotations = $this->reader->getClassAnnotations($class);

        if ($classAnnotations && is_numeric(key($classAnnotations))) {
            foreach ($classAnnotations as $annot) {
                $classAnnotations[get_class($annot)] = $annot;
            }
        }

        if (isset($classAnnotations['Core\Model\OWM\Mapping\Endpoint'])) {
            $entityAnnot = $classAnnotations['Core\Model\OWM\Mapping\Endpoint'];

            if ($entityAnnot->repositoryClass !== NULL) {
                $metadata->setCustomRepositoryClass($entityAnnot->repositoryClass);
            }

            if ($entityAnnot->configKey !== NULL) {
                $metadata->configKey = $entityAnnot->configKey;
            }
        }
    }
}
