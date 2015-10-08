<?php
/**
 * Copyright 2012 do ICMBio
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
namespace Artefato\Service;

/**
 * Classe para Service de Comentário
 *
 * @package  Minuta
 * @category Service
 * @name     Comentario
 * @version  1.0.0
 */
class Comentario extends \Core_ServiceLayer_Service_CrudDto
{
    const T_COMENTARIO_ALREADY_EXISTS   = 'Comentário já existe';
    const T_COMENTARIO_AUTHOR_NOT_FOUND = 'Não foi possível definir autoria do comentário';

    protected $_entityName = 'app:ComentarioArtefato';

    /**
     * @param Core_Dto_Entity
     * @throws Exception
     * */
    public function register (\Core_Dto_Entity $dto)
    {
        # @todo implementar regra de negocio que verifica se o comentario pode ser alterado
        # falta a definicao da estrutura tabela

        # recupera referencia da pessoa que está realizando operacao
        $pessoa = $this->_getRepository('app:VwPessoa')
                       ->find(\Core_Integration_Sica_User::getPersonId());

        # verifica o autor existe (usuario da sessao existe na base)
        # isso poderá ocorrer quando a sessao cair perando a realizacao
        # da operacao
        if (!count($pessoa)) {
            throw new \Exception(self::T_COMENTARIO_AUTHOR_NOT_FOUND);
        }

        $dto->setSqPessoa($pessoa);

        # recupera a unidade organizacional da pessoa que esta manipulando o comentario
        $dto->setSqUnidadeOrg($this->_getRepository('app:VwUnidadeOrg')->find(\Core_Integration_Sica_User::getUserUnit()));

        # verifica se o registro já existe
        $this->_alreadyExists($dto);

        # se o sq_comentario_artefato existir indica uma alteracao.
        # Devido a propriedade de tempo nunca será possível repedir um registro
        # se levar esta propriedade em consideracao
        if ($dto->getSqComentarioArtefato()) {
            $this->_update($dto);
            $this->getMessaging()->addSuccessMessage('MD002', 'User');
        }else{
            # define a hora de registro/alteracao
            $dto->setDtComentario(\Zend_Date::now());

            # delega a operação de salvar os dados para superclasse
            $this->_save($dto);
            $this->getMessaging()->addSuccessMessage('MD001', 'User');
        }


        $this->finish();
        $this->getMessaging()->dispatchPackets();
    }

    /**
     * implementa a regra de negocio para remover o comentario
     *
     * @param integer
     * @throws Exception
     * @return Comentario
     * */
    public function preDelete ($id)
    {
        $entity = $this->_getRepository()->find($id);

        $hasDemandaComentario = $this->getServiceLocator()
                                            ->getService('Solicitacao')
                                            ->hasDemandaAbertaByAssuntoPessoaResponsavel(
                \Core_Dto::factoryFromData(array(
                    'sqArtefato'=>$entity->getSqArtefato()->getSqArtefato(),
                    'sqTipoAssuntoSolicitacao' => \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoComentario()
                ), 'search'));

        /**
         * se a data do despacho é menor que a data do último tramite NÃO pode excluir
         * 0 = equal, 1 = later, -1 = earlier
         */
        if ((\Zend_Registry::get('isUserSgi') && !$hasDemandaComentario) && ($this->_checkArtefatoLastTramite($entity) < 1)) {
            throw new \Core_Exception_ServiceLayer(\Core_Registry::getMessage()->translate('MN047'));
        }

        return $this;
    }

    /*
     * @param \Core_Dto_Entity $dto
     * @throws Exception
     * */
    private function _update (\Core_Dto_Entity $dto)
    {
        $hasDemandaComentario = $this->getServiceLocator()
                                            ->getService('Solicitacao')
                                            ->hasDemandaAbertaByAssuntoPessoaResponsavel(
                \Core_Dto::factoryFromData(array(
                    'sqArtefato'=>$dto->getEntity()->getSqArtefato()->getSqArtefato(),
                    'sqTipoAssuntoSolicitacao' => \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoComentario()
                ), 'search'));

        /**
         * se a data do despacho é menor que a data do último tramite NÃO pode excluir
         * 0 = equal, 1 = later, -1 = earlier
         */
        if ((\Zend_Registry::get('isUserSgi') && !$hasDemandaComentario) && ($this->_checkArtefatoLastTramite($dto->getEntity()) < 1)) {
            throw new \Core_Exception_ServiceLayer(\Core_Registry::getMessage()->translate('MN147'));
        }

        $this->_getRepository()->update($dto->getEntity());
    }

    /*
     * @param \Core_Dto_Entity $dto
     * @throws Exception
     * */
    private function _save (\Core_Dto_Entity $dto)
    {
        $this->_getRepository()->save($dto->getEntity());
    }

    /*
     * verifica se o comentario já existe
     *
     * @param \Core_Dto_Entity $dto
     * @throws Exception
     * */
    private function _alreadyExists (\Core_Dto_Entity $dto)
    {
        $assert = $this->findBy(array(
        		'txComentario' => $dto->getTxComentario(),
        		'sqPessoa'     => \Core_Integration_Sica_User::getPersonId(),
        		'sqArtefato'   => $dto->getSqArtefato()
        ));

        if (count($assert)) {
            throw new \Exception(self::T_COMENTARIO_ALREADY_EXISTS);
        }
    }

    /**
     *
     * @param integer $sqArtefato
     * @return boolean
     */
    public function checkPermissaoArtefato($sqArtefato)
    {
        $dto = \Core_Dto::factoryFromData(array('sqArtefato'=>$sqArtefato), 'search');
        $hasSolicitacaoAberta = $this->getServiceLocator()->getService('Solicitacao')->hasDemandaAberta($dto);
        return ($this->_checkArtefatoInMyDashboard($sqArtefato) && !$hasSolicitacaoAberta);
    }

    public function listGrid(\Core_Dto_Search $dto, $withoutCount=TRUE)
    {
        $result = $this->_getRepository()->searchPageDto('listGrid', $dto, $withoutCount);
        return $result;
    }

     /**
     * Metodo responsavel por comparar a data do despacho com a data do ultimo tramite
     * do artefato
     *
     * @param \Sgdoce\Model\Entity\DespachoInterlocutorio $entityComentarioArtefato
     * @return integer 0 = equal, 1 = later, -1 = earlier
     */
    private function _checkArtefatoLastTramite(\Sgdoce\Model\Entity\ComentarioArtefato $entityComentarioArtefato)
    {
        $sqArtefato             = $entityComentarioArtefato->getSqArtefato()->getSqArtefato();
        $entityUltimoTramite    = $this->_getRepository('app:VwUltimoTramiteArtefato')->find($sqArtefato);
        $dtUltimoTramiteArtefato= $entityUltimoTramite->getDtTramite();
        return $entityComentarioArtefato->getDtComentario()->compare($dtUltimoTramiteArtefato);
    }

    /**
     * Metodo responsavel pro verificar se o artefato esta na area de trabalho
     * da pessoa logada
     *
     * @param integer $sqArtefato
     * @return boolean
     */
    private function _checkArtefatoInMyDashboard($sqArtefato)
    {
        return $this->getServiceLocator()
                        ->getService("Artefato")
                        ->inMyDashboard($sqArtefato);
    }
}