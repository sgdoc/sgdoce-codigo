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

namespace Artefato\Service;
/**
 * Classe para Service de Prazo
 *
 * @package  Artefato
 * @category Service
 * @name     Prazo
 * @version  1.0.0
 */

class Solicitacao extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:Solicitacao';

    /**
     *
     * @param $entity
     * @param \Core_Dto $dto
     */
    public function preInsert($entity, $dto = NULL)
    {
        $dtoDuplicado = \Core_Dto::factoryFromData(array(
            'sqArtefato' => $dto->getSqArtefato(),
            'sqTipoAssuntoSolicitacao' => $dto->getSqTipoAssuntoSolicitacao(),
            'sqPessoa'  => \Core_Integration_Sica_User::getPersonId(),
            'sqUnidadeOrg' => \Core_Integration_Sica_User::getUserUnit()
        ), 'search');

        $listResult = $this->_getRepository()->getSolicitacaoDuplicado($dtoDuplicado);

        if( count($listResult) ) {
            throw new \Core_Exception_ServiceLayer(\Core_Registry::getMessage()->translate('MN206'));
        }

        # artefatos inconsistêntes não podem abrir solicitação de alguns tipos de solicitação
        $isInconsistent = $this->getServiceLocator()->getService('Artefato')->isInconsistent($dtoDuplicado);

        if( $isInconsistent
            && in_array($dto->getSqTipoAssuntoSolicitacao(), $this->getTipoAssuntoSolcOnlyConsistent())) {
            throw new \Core_Exception_ServiceLayer(\Core_Registry::getMessage()->translate('MN205'));
        }

        # se for exclusão de volume e só houver um volume cadastrado, não permite a criação
        if ( ($dto->getSqTipoAssuntoSolicitacao() == \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoVolumeDeProcesso())
            && !$this->_getRepository('app:ProcessoVolume')->notTheOnlyVolume($dto->getSqArtefato())) {
            throw new \Core_Exception_ServiceLayer(\Core_Registry::getMessage()->translate('MN207'));
        }
        //Tira os Espaços do 'enter' para salvar com 500 caracteres
        $dsSolicitacao = $this->getServiceLocator()->getService('MinutaEletronica')
                    ->fixNewlines($entity->getDsSolicitacao());
        $entity->setDsSolicitacao((!$dsSolicitacao) ? NULL : $dsSolicitacao);

        $this->getEntityManager()->getConnection()->beginTransaction();

        try {
            $entSqPessoa = $this->getEntityManager()
                                ->getPartialReference('app:VwPessoa',
                                \Core_Integration_Sica_User::getPersonId());

            $entSqUnidadeOrg = $this->getEntityManager()
                                ->getPartialReference('app:VwUnidadeOrg',
                                \Core_Integration_Sica_User::getUserUnit());

            $entTipoAssuntoSolicitacao = $this->_getRepository('app:TipoAssuntoSolicitacao')->find($dto->getSqTipoAssuntoSolicitacao());

            $entity->setDtSolicitacao(\Zend_Date::now());
            $entity->setSqPessoa($entSqPessoa);
            $entity->setSqUnidadeOrg($entSqUnidadeOrg);
            $entity->setSqTipoAssuntoSolicitacao($entTipoAssuntoSolicitacao);
        } catch (\Exception $e) {
            $this->getEntityManager()->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     *
     * @param $entity
     * @param \Cor_Dto_Search $dto
     */
    public function postInsert($entity, $dto = NULL)
    {
        try {
            $statusSolicitacaoData = array(
                'sqSolicitacao' => $entity,
                'sqTipoStatusSolicitacao' => \Core_Configuration::getSgdoceTipoStatusSolicitacaoAberta(),
            );

            $dtoStatusSolicitacao = \Core_Dto::factoryFromData($statusSolicitacaoData, 'search');

            $this->getServiceLocator()
                 ->getService('StatusSolicitacao')
                 ->newStatusSolicitacao($dtoStatusSolicitacao);

            $this->getEntityManager()->getConnection()->commit();
        } catch (\Exception $e) {
            $this->getEntityManager()->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function comboTipoAssuntoSolicitacao($dto)
    {
        $list = $this->_getRepository('app:TipoAssuntoSolicitacao')
                     ->listTipoAssuntoSolicitacao($dto);

        $out  = array();
        foreach( $list as $item ) {
            $out[$item['sqTipoAssuntoSolicitacao']] = $item['noTipoAssuntoSolicitacao'];
        }

        return $out;
    }

    /**
     * @param \Core_Dto_Search $dto
     * @return array
     */
    public function comboArtefato( $dto )
    {
        $sqTipoArtefato = $dto->getSqTipoArtefato();

        $field = "nuDigital";

        if( $sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ) {
            $field = "nuArtefato";
        }

        $list  = $this->_getRepository()->searchArtefato($dto);

        if( !isset($list['__NO_CLICK__']) ){
            $out = array();

            foreach( $list as $item ){
                $out[$item['sqArtefato']] = "{$item[$field]}";
            }

            return $out;
        }

        return $list;
    }

    /**
     * @param \Core_Dto_Search $dto
     * @return
     */
    public function listGrid(\Core_Dto_Search $dto )
    {
        return $this->_getRepository()->searchPageDto('listGrid', $dto, TRUE);
    }

    public function listGridHistorico(\Core_Dto_Search $dto )
    {
        return $this->_getRepository()->searchPageDto('listGridHistorico', $dto, TRUE);
    }

    public function getSolicitacaoAberta (\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->getSolicitacaoAberta ($dto);
    }

    public function hasDemandaAbertaByAssuntoPessoaResponsavel(\Core_Dto_Search $dto, $listTipoAssuntoSolicitacao = array())
    {
        $demandaAberta = $this->_getRepository()->getSolicitacaoAbertaByAssuntoPessoaResponsavel ($dto, $listTipoAssuntoSolicitacao);
        return (count($demandaAberta) > 0);
    }

    public function getTipoAssuntoSolcOnlyConsistent()
    {
        return array(
            \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoAlterarCadastro(),
            \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoVolumeDeProcesso(),
        );
    }

    /**
     * Verifica se existe demanda aberta
     *
     * @param \Core_Dto_Search $dto
     * @return boolean
     */
    public function hasDemandaAberta(\Core_Dto_Search $dto)
    {
        $result = $this->_getRepository()->getSolicitacaoAberta ($dto);

        return ($result) ? TRUE : FALSE;
    }
}