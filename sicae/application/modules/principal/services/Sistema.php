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
 * Classe Service Usuario
 *
 * @package      Principal
 * @subpackage   Services
 * @name         Usuario
 * @version      1.0.0
 * @since        2012-07-24
 */

namespace Principal\Service;

class Sistema extends \Sica_Service_Crud
{

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:Sistema';

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->_getRepository()->getAll();
    }

    public function systemsActives($sqTipoPerfil = 1, array $arrTipoPerfil = array(1, 2))
    {
        return $this->_getRepository()->systemsActives($sqTipoPerfil, $arrTipoPerfil);
    }

    public function getSistemasPorTipoPerfil($sqTipoPerfil = 1, array $arrTipoPerfil = array(1, 2), $exc = TRUE)
    {
        return $this->_getRepository()->getSistemasPorTipoPerfil($sqTipoPerfil, $arrTipoPerfil, $exc);
    }

    public function findAllArchitetures()
    {
        return $this->_getRepository('app:Arquitetura')->findArchitectures();
    }

    public function findAllArchiteturesBinds()
    {
        return $this->_getRepository('app:Arquitetura')->findArchitectures(TRUE);
    }

    public function findResponsible($nome)
    {
        return $this->_getRepository('app:PessoaFisica')->findResponsible($nome);
    }

    public function preSave($entity, $dto = NULL)
    {
        $repository = $this->_getRepository();

        if ($repository->validateRnSixOne($entity)) {
            $this->getMessaging()->addErrorMessage('MN024');
            throw new \Core_Exception_ServiceLayer_Verification();
        }

        if ($repository->validateRnSixTwo($entity)) {
            $this->getMessaging()->addErrorMessage('MN025');
            throw new \Core_Exception_ServiceLayer_Verification();
        }

        $filename = $this->_upload();

        if ($filename) {
            $entity->setTxEnderecoImagem($filename);
        }
    }

    protected function _upload()
    {
        $configs = \Core_Registry::get('configs');
        $upload = new \Core_Upload('Http', FALSE, $configs['upload']['sistema']);

        if ($upload->getFileName()) {
            // retirando obrigatpriedade do campo
            $upload->setOptions(array('ignoreNoFile' => TRUE));
            return $upload->upload();
        }

        return NULL;
    }

    /**
     * @param int $sqSistema
     */
    public function findMenuBySystem($sqSistema, $ativo = FALSE)
    {
        return $this->_getRepository('app:Menu')->findMenuBySystem($sqSistema, $ativo);
    }

    public function getStatus()
    {
        return array(
            0 => 'Inativo',
            1 => 'Ativo'
        );
    }

    public function findById($identifier)
    {
        return $this->_getRepository()->findById($identifier);
    }

    public function findByNoSistema($noSistema)
    {
        return $this->_getRepository()->findByNoSistema($noSistema);
    }

    public function findSystemFull($identifier)
    {
        return $this->_getRepository()->findSystemFull($identifier);
    }

    public function findLogo($identifier)
    {
        $configs = \Core_Registry::get('configs');
        $path = realpath($configs['upload']['sistema']['destination']);

        if (!is_writable($path)) {
            return;
        }

        $imagem = $this->find($identifier);
        if (!($imagem instanceof \Sica\Model\Entity\Sistema) || !$imagem->getTxEnderecoImagem()) {
            return;
        }

        $filename = $path . '/' . $imagem->getTxEnderecoImagem();

        if (!is_writable($filename) || !file_exists($filename)) {
            return;
        }

        return $filename;
    }

    public function retrieveLogo($identifier)
    {
        $filename = $this->findLogo($identifier);
        return $filename ? readfile($filename) : '';
    }

    public function findAllLayouts()
    {
        return $this->_getRepository('app:Leiaute')->findBy(array(), array('noLeiaute' => 'ASC'));
    }

    public function postSave($entity, $dto = NULL)
    {
        if ($dto->getSqPerfilExternoPadrao()->getSqPerfil()) {
            $sqPerfil = $this->_getRepository('app:Perfil')->find($dto->getSqPerfilExternoPadrao()->getSqPerfil());
            $entity->setSqPerfilExternoPadrao($sqPerfil);
        } else {
            $emManager = $this->getEntityManager();
            $entityPerfil = $entity->getSqPerfilExternoPadrao();
            $identifier = $emManager->getClassMetadata(get_class($entityPerfil));
            $ids = $identifier->getIdentifierValues($entityPerfil);
            if (!$ids) {
                $ids = array_fill_keys($identifier->getIdentifierColumnNames(), 0);
            }
            $uow = $emManager->getUnitOfWork()->registerManaged(
                    $entityPerfil, $ids, array()
            );
        }
    }

    public function findSystemsFull($dtoSearch)
    {
        return $this->_getRepository()->findSystemsFull($dtoSearch);
    }

    public function preInsert($entity, $dto = NULL)
    {
        $entity->setStRegistroAtivo(TRUE);
    }

}
