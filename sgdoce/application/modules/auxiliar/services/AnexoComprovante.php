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

namespace Auxiliar\Service;

use Doctrine\ORM\Query\ParameterTypeInferer;

/**
 * Classe para Service de AnexoComprovante
 *
 * @package  Auxiliar
 * @category Service
 * @name     AnexoComprovante
 * @version  1.0.0
 */
class AnexoComprovante extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:AnexoComprovante';

    public function deleteAnexoComprovante($dto)
    {
        $entity = $this->_getRepository()->findOneBy(array(
            'sqAnexoComprovante' => $dto->getSqAnexoComprovante()
        ));

        if($entity) {
            $this->apagarArquivo($entity);

            $this->getEntityManager()->remove($entity);
            $this->getEntityManager()->flush();
        }
    }

    public function deleteByEnderecoSgdoce(\Core_Dto_Abstract $dto)
    {
        $entity = $this->_getRepository()->findOneBy(array(
            'sqEnderecoSgdoce' => $dto->getSqEnderecoSgdoce()
        ));

        if($entity) {
            $this->deleteAnexoComprovante($entity);
        }
    }

    public function saveDocumento(\Core_Dto_Abstract $dto)
    {
        \Zend_Registry::get('doctrine')->getEntityManager()->clear();

        $entityEndereco = $this->_getRepository()->findOneBy(array(
            'sqEnderecoSgdoce' => $dto->getSqEnderecoSgdoce()
        ));

        $result = (!$entityEndereco)
            ? $this->insertArquivo($dto)
            : $this->updateArquivo($dto, $entityEndereco);

        return array(
            'entity'   => $result,
            'isUpdate' => (!$entityEndereco) ? false : true
        );
    }

    protected function updateArquivo($dto, $entity)
    {
        $this->apagarArquivo($entity);

        $_em = \Zend_Registry::get('doctrine')->getEntityManager();

        $result = $_em->createQueryBuilder()
            ->update($this->_entityName, 'ac')
            ->set('ac.deCaminhoArquivo', $_em->createQueryBuilder()->expr()->literal($dto->getDeCaminhoArquivo()))
            ->where('ac.sqEnderecoSgdoce = :sqEnderecoSgdoce')
            ->setParameter('sqEnderecoSgdoce', $dto->getSqEnderecoSgdoce())
            ->getQuery()
            ->execute();

        return $entity;
    }

    protected function insertArquivo($dto)
    {
        $entityAnexo     = $this->_newEntity('app:AnexoComprovante');
        $serviceEndereco = $this->getServiceLocator()->getService('EnderecoSgdoce');

        $entityAnexo->setSqEnderecoSgdoce($serviceEndereco->find($dto->getSqEnderecoSgdoce()))
            ->setDeCaminhoArquivo($dto->getDeCaminhoArquivo());

        $_em = \Zend_Registry::get('doctrine')->getEntityManager();
        $_em->persist($entityAnexo);
        $_em->flush();

        return $entityAnexo;
    }

    protected function apagarArquivo($entity)
    {
        $config = \Zend_Registry::get('configs');
        $path   = $config['upload']['endereco']['destination'];

        @unlink($path . '/'. $entity->getDeCaminhoArquivo());
    }
}
