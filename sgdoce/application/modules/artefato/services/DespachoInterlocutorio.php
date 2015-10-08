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
 * Classe para Service de Despacho Interlocutorio
 *
 * @package  Minuta
 * @category Service
 * @name      DespachoInterlocutorio
 * @version  1.0.0
 */
class DespachoInterlocutorio extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:DespachoInterlocutorio';

    /**
     * Método que obtém dados para a grid
     * @param \Core_Dto_Search $dto
     * @param boolean $withoutCount indica que a query já possui a coluna total_record para totalizador da grid
     * @return array
     */
    public function getGrid(\Core_Dto_Search $dto, $withoutCount = TRUE)
    {
        $result = $this->_getRepository()
                ->searchPageDto('listGridHistoricoDespacho', $dto, $withoutCount);
        return $result;
    }

    /**
     * Método que retorna pesquisa do banco para preencher combo
     * @return array
     */
    public function comboCargo()
    {
        $bEstado = $this->_getRepository()->buscarCargo();
        $arrComboEstado = array();
        foreach ($bEstado as $comboEstado) {
            $arrComboEstado[$comboEstado->getSqEstado()] = $comboEstado->getNoEstado();
        }
        return $arrComboEstado;
    }

    /**
     * implementa a regra de negocio para remover o despacho
     *
     * @param integer
     * @throws Exception
     * @return Comentario
     * */
    public function preDelete($id)
    {
        $entity = $this->_getRepository()->find($id);

        /**
         * valida se esta em minha area de trabalho e não possui
         */
        if (!$this->checkPermisionArtefato($entity->getSqArtefato()->getSqArtefato())) {
            throw new \Exception(\Core_Registry::getMessage()->translate('MN146'));
        }

        $hasDemandaDespacho = $this->getServiceLocator()
                                    ->getService('Solicitacao')
                                    ->hasDemandaAbertaByAssuntoPessoaResponsavel(
            \Core_Dto::factoryFromData(array(
                'sqArtefato'=>$entity->getSqArtefato()->getSqArtefato(),
                'sqTipoAssuntoSolicitacao' => \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoDespacho()
            ), 'search'));



        /**
         * se a data do despacho é menor que a data do último tramite NÃO pode excluir
         * 0 = equal, 1 = later, -1 = earlier
         */
        if ((\Zend_Registry::get('isUserSgi') && !$hasDemandaDespacho) && ($this->_checkArtefatoLastTramite($entity) < 1)) {
            throw new \Exception(\Core_Registry::getMessage()->translate('MN047'));
        }

        return $this;
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

    public function checkPermisionArtefato($sqArtefato)
    {
        $dto = \Core_Dto::factoryFromData(array('sqArtefato'=>$sqArtefato), 'search');
        $hasSolicitacaoAberta = $this->getServiceLocator()->getService('Solicitacao')->hasDemandaAberta($dto);
        return ($this->_checkArtefatoInMyDashboard($sqArtefato) && !$hasSolicitacaoAberta);
    }

    public function preSave($entity, $dto = NULL)
    {
        $this->getEntityManager()->getConnection()->beginTransaction();
        try{

            /**
             * valida se esta em minha area de trabalho
             */
            if (!$this->checkPermisionArtefato($entity->getSqArtefato()->getSqArtefato())) {
                throw new \Core_Exception_ServiceLayer(\Core_Registry::getMessage()->translate('MN146'));
            }

            if ($entity->getSqDespachoInterlocutorio()) {
                $entity->setDtDespacho(new \Zend_Date($entity->getDtDespacho()));


                $hasDemandaDespacho = $this->getServiceLocator()
                                            ->getService('Solicitacao')
                                            ->hasDemandaAbertaByAssuntoPessoaResponsavel(
                    \Core_Dto::factoryFromData(array(
                        'sqArtefato'=>$entity->getSqArtefato()->getSqArtefato(),
                        'sqTipoAssuntoSolicitacao' => \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoDespacho()
                    ), 'search'));

                /**
                 * se a data do despacho é menor que a data do último tramite NÃO pode excluir
                 * 0 = equal, 1 = later, -1 = earlier
                 */
                if ((\Zend_Registry::get('isUserSgi') && !$hasDemandaDespacho) && ($this->_checkArtefatoLastTramite($entity) < 1)) {
                    throw new \Core_Exception_ServiceLayer(\Core_Registry::getMessage()->translate('MN147'));
                }
                $this->getMessaging()->addSuccessMessage('MD002','User');
            }else{
                $this->getMessaging()->addSuccessMessage('MD001','User');
            }

            $sqCargoAssinatura = $dto->getSqCargoAssinatura();
            $sqFuncaoAssinatura = $dto->getSqFuncaoAssinatura();

            if( empty($sqCargoAssinatura)
                && empty($sqFuncaoAssinatura) ) {
                $this->getMessaging()->addErrorMessage(\Core_Registry::getMessage()->translate('MN179'), 'User');
            } else {

                if( !empty($sqCargoAssinatura) ) {
                    $entCargoAssinatura = $this->getEntityManager()
                                               ->getPartialReference('app:VwCargo',  $sqCargoAssinatura);
                    $entity->setSqCargoAssinatura($entCargoAssinatura);
                } else {
                    $entity->setSqCargoAssinatura(null);
                }

                if( !empty($sqFuncaoAssinatura) ) {
                    $entFuncaoAssinatura = $this->getEntityManager()
                                               ->getPartialReference('app:VwFuncao',  $sqFuncaoAssinatura);
                    $entity->setSqFuncaoAssinatura($entFuncaoAssinatura);
                } else {
                    $entity->setSqFuncaoAssinatura(null);
                }

            }

            if (!$entity->getSqDespachoInterlocutorio()) {
                $entity->setDtDespacho(\Zend_Date::now());
            }

            $this->getMessaging()->dispatchPackets();

            $this->getEntityManager()->getConnection()->commit();
        } catch (\Exception $e) {
            $this->getEntityManager()->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * Metodo responsavel por comparar a data do despacho com a data do ultimo tramite
     * do artefato
     *
     * @param \Sgdoce\Model\Entity\DespachoInterlocutorio $entityDespachoInterlocutorio
     * @return integer 0 = equal, 1 = later, -1 = earlier
     */
    private function _checkArtefatoLastTramite(\Sgdoce\Model\Entity\DespachoInterlocutorio $entityDespachoInterlocutorio)
    {
        $sqArtefato             = $entityDespachoInterlocutorio->getSqArtefato()->getSqArtefato();
        $entityUltimoTramite    = $this->_getRepository('app:VwUltimoTramiteArtefato')->find($sqArtefato);
        $dtUltimoTramiteArtefato= $entityUltimoTramite->getDtTramite();
        return $entityDespachoInterlocutorio->getDtDespacho()->compare($dtUltimoTramiteArtefato);
    }

}
