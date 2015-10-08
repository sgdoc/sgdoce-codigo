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
 * Classe para Service de AnexoComprovanteDocumento
 *
 * @package  Auxiliar
 * @category Service
 * @name     AnexoComprovanteDocumento
 * @version  1.0.0
 */
class AnexoComprovanteDocumento extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:AnexoComprovanteDocumento';

    public function deleteAnexoComprovanteDocumento(\Core_Dto_Abstract $dto)
    {
        $entity = $this->_getRepository('app:AnexoComprovanteDocumento')->findOneBy(array(
            'sqPessoaSgdoce' => $dto->getSqPessoaSgdoce(),
            'sqTipoDocumento' => $dto->getSqTipoDocumento()
        ));

        if($entity) {
            $this->deleteArquivo($entity);
        }
    }

    public function saveDocumento(\Core_Dto_Abstract $dto)
    {
        \Zend_Registry::get('doctrine')->getEntityManager()->clear();

        $entityAnexo  = $this->_getRepository()->findOneBy(array(
            'sqPessoaSgdoce'  => $dto->getSqPessoaSgdoce(),
            'sqTipoDocumento' => $dto->getSqTipoDocumento()
        ));

        $result = $entityAnexo
            ? $this->updateArquivo($entityAnexo, $dto)
            : $this->insertArquivo($dto);

        return $entityAnexo
            ? 'update'
            : 'insert';
    }

    protected function updateArquivo($entity, $dto)
    {
        $this->deleteArquivo($entity);
        $this->insertArquivo($dto);
    }

    protected function insertArquivo($dto)
    {
        $entityAnexo   = $this->_newEntity('app:AnexoComprovanteDocumento');
        $servicePessoa = $this->getServiceLocator()->getService('PessoaSgdoce');

        $entityAnexo->setSqPessoaSgdoce($servicePessoa->find($dto->getSqPessoaSgdoce()))
            ->setDeCaminhoImagem($dto->getDeCaminhoImagem())
            ->setSqTipoDocumento($dto->getSqTipoDocumento());

        $_em = \Zend_Registry::get('doctrine')->getEntityManager();
        $_em->persist($entityAnexo);
        $_em->flush();
    }

    protected function deleteArquivo($entity)
    {
        $config = \Zend_Registry::get('configs');
        $path   = $config['upload']['documento']['destination'];

        if($entity) {
            unlink($path . '/'. $entity->getDeCaminhoImagem());

            $this->getEntityManager()->remove($entity);
            $this->getEntityManager()->flush();
        }
    }
}
