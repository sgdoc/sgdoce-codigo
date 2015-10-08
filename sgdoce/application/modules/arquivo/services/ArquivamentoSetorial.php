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

namespace Arquivo\Service;

/**
 * Classe para Service de Arquivamento Setorial
 *
 * @package  Arquivo
 * @category Service
 * @name     ArquivamentoSetorial
 * @version  1.0.0
 */
class ArquivamentoSetorial extends \Core_ServiceLayer_Service_Crud
{

    const T_ARQUIVAMENTO_AUTHOR_NOT_FOUND = 'Não foi possível definir autoria do arquivamento';

    /**
     * Variavel para receber o nome da entidade
     *
     * @var string
     * @access protected
     * @name $_entityName
     */
    protected $_entityName = 'app:ArtefatoArquivoSetorial';


    /**
     *
     * @param integer $sqArtefato
     * @throws \Exception
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    public function archive ($sqArtefato)
    {
        $this->getEntityManager()->beginTransaction();
        try {

            if (! $sqArtefato) {
                throw new \Core_Exception_ServiceLayer_Verification('Nenhum artefato encontrado');
            }

            $this->_checkArquivamento($sqArtefato)
                 ->_checkSolicitacaoAberta($sqArtefato)
                 ->_checkStatusTramite($sqArtefato);

            $entityArtefato = $this->getEntityManager()->getPartialReference('app:Artefato'    , $sqArtefato);
            $entityUnidade  = $this->getEntityManager()->getPartialReference('app:VwUnidadeOrg', \Core_Integration_Sica_User::getUserUnit());
            $entityPessoa   = $this->getEntityManager()->getPartialReference('app:VwPessoa'    , \Core_Integration_Sica_User::getPersonId());

            $entity = $this->_newEntity($this->_entityName);

            $entity->setSqArtefato($entityArtefato)
                   ->setSqUnidadeArquivamento($entityUnidade)
                   ->setSqPessoaArquivamento($entityPessoa)
                   ->setDtArquivamento(\Zend_Date::now());

            # persiste o arquivamento
            $this->getEntityManager()->persist($entity);

            # persiste o historico para o arquivamento
            $dtOperacao = \Zend_Date::now();

            # persiste o historico no artefato
            $serviceHA    = $this->getServiceLocator()->getService('HistoricoArtefato');
            $sqOcorrencia = \Core_Configuration::getSgdoceSqOcorrenciaArquivarSetor();
            $strMessage   = $serviceHA->getMessage('MH025',
                                                   \Core_Integration_Sica_User::getUserUnitName(),
                                                   $dtOperacao->get(\Zend_Date::DATETIME_MEDIUM),
                                                   \Core_Integration_Sica_User::getUserName());

            $serviceHA->registrar($sqArtefato, $sqOcorrencia, $strMessage);

            $this->getEntityManager()->commit();
        } catch (\Exception $e) {
            $this->getEntityManager()->rollback();
            throw $e;
        }
    }


    private function _getNuArtefato($sqArtefato)
    {
        $entArtefato = $this->_getRepository('app:Artefato')->find($sqArtefato);

        if ($entArtefato->isProcesso()) {
            return "Processo " . $this->getServiceLocator()->getService('Processo')
                    ->formataProcessoAmbitoFederal($entArtefato);
        }else{
            return "Documento " . $entArtefato->getNudigital()->getNuEtiqueta(TRUE);
        }
    }

    private function _checkArquivamento($sqArtefato)
    {
        if( $this->_getRepository()->hasArquivamento($sqArtefato) ) {
            $nuArtefato = $this->_getNuArtefato($sqArtefato);
            throw new \Core_Exception_ServiceLayer_Verification("{$nuArtefato} já encontra-se arquivado.");
        }
        return $this;
    }

    private function _checkSolicitacaoAberta($sqArtefato)
    {
        $dto = \Core_Dto::factoryFromData(array('sqArtefato'=>$sqArtefato), 'search');
        $solicitacaoAberta = $this->_getRepository('app:Solicitacao')->getSolicitacaoAberta($dto);

        if ($solicitacaoAberta) {
            $nuArtefato = $this->_getNuArtefato($sqArtefato);
            throw new \Core_Exception_ServiceLayer_Verification("{$nuArtefato} possui demanda de suporte aberta e não pode ser arquivado.");
        }

        return $this;
    }

    private function _checkStatusTramite($sqArtefato)
    {
        $entVwUltimoTramite = $this->_getRepository('app:VwUltimoTramiteArtefato')->find($sqArtefato);

        if ($entVwUltimoTramite->isTramited()) {
            $nuArtefato = $this->_getNuArtefato($sqArtefato);
            throw new \Core_Exception_ServiceLayer_Verification("{$nuArtefato} possui demanda de suporte aberta e não pode ser arquivado.");
        }

        return $this;
    }


    /**
     *
     * @param integer $sqArtefato
     * @throws \Exception
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    public function unarchive($sqArtefato)
    {
        $this->getEntityManager()->beginTransaction();
        try {

            if(! $this->_getRepository()->hasArquivamento($sqArtefato) ) {
                $nuArtefato = $this->_getNuArtefatoToUnarchive($sqArtefato);
                throw new \Core_Exception_ServiceLayer_Verification("Artefato {$nuArtefato} já foi desarquivado.");
            }

            $entityArquivoSetorial = $this->_getRepository()->find($this->_getRepository()->getKeyArquivamento($sqArtefato));

            if (NULL === $entityArquivoSetorial) {
                $nuArtefato = $this->_getNuArtefatoToUnarchive($sqArtefato);
                throw new \Core_Exception_ServiceLayer_Verification("Artefato {$nuArtefato} não localizado no arquivo setorial. Já deve ter sido desarquivado");
            }

            $entityPessoaDesarquivamento = $this->getEntityManager()->getPartialReference('app:VwPessoa'    , \Core_Integration_Sica_User::getPersonId());

            $dtDesarquivamento = \Zend_Date::now();
            $entityArquivoSetorial->setDtDesarquivamento($dtDesarquivamento)
                    ->setSqPessoaDesarquivamento($entityPessoaDesarquivamento);

            $this->getEntityManager()->persist($entityArquivoSetorial);

            # persiste o historico no artefato
            $serviceHA    = $this->getServiceLocator()->getService('HistoricoArtefato');
            $sqOcorrencia = \Core_Configuration::getSgdoceSqOcorrenciaDesarquivarSetor();
            $strMessage   = $serviceHA->getMessage('MH026',
                                                   \Core_Integration_Sica_User::getUserUnitName(),
                                                   $dtDesarquivamento->get(\Zend_Date::DATETIME_MEDIUM),
                                                   \Core_Integration_Sica_User::getUserName());

            $serviceHA->registrar($sqArtefato, $sqOcorrencia, $strMessage);
            sleep(1); //só pra organizar o histórico
            $this->_processTramite($entityArquivoSetorial->getSqArtefato());

            $this->getEntityManager()->flush($entityArquivoSetorial);

            $this->getEntityManager()->commit();
        } catch (\Exception $e) {
            $this->getEntityManager()->rollback();
            throw $e;
        }
    }

    /**
     *
     * @param integer $sqArtefato
     * @return string
     */
    private function _getNuArtefatoToUnarchive($sqArtefato)
    {
        $entArtefato = $this->_getRepository('app:Artefato')->find($sqArtefato);

        if ($entArtefato->getNuDigital()) {
            $nuArtefato = $entArtefato->getNuDigital()->getNuEtiqueta(TRUE);
        } else {
            $nuArtefato = $this->getServiceLocator()->getService('Processo')
                    ->formataProcessoAmbitoFederal($entArtefato);
        }
        return $nuArtefato;
    }

    /**
     *
     * @param \Sgdoce\Model\Entity\Artefato $entityArtefato
     * @return \Arquivo\Service\ArquivamentoSetorial
     */
    private function _processTramite(\Sgdoce\Model\Entity\Artefato $entityArtefato)
    {
        $sqArtefato = $entityArtefato->getSqArtefato();

        $entityUltimoTramite = $this->_getRepository('app:VwUltimoTramiteArtefato')->find($sqArtefato);

        $sqPessoaLogada  = (integer) \Core_Integration_Sica_User::getPersonId();
        $sqUnidadeLogada = (integer) \Core_Integration_Sica_User::getUserUnit();
        $serviceTramite  = $this->getServiceLocator()->getService('TramiteArtefato');

        $dtoSearchArtefato = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
        /**
         * Se não tem ultimo Tramite é porque o artefato ainda não foi corrigido.
         * Verificar, mesmo assim, se tem tramite pois a view de ultimo tramite faz join
         * que pode não retornar registro caso documento não tenha sido corrido
         */
        if (! $entityUltimoTramite) {
            $objZendDate = \Zend_Date::now();

            $dataTramite = array(
                'sqArtefato'             => $sqArtefato,
                'sqPessoaDestino'        => $sqUnidadeLogada,
                'sqPessoaDestinoInterno' => $sqPessoaLogada,
                'sqUnidadeOrgTramite'    => $sqUnidadeLogada,
                'sqStatusTramite'        => \Core_Configuration::getSgdoceStatusTramiteRecebido(),
                'dtTramite'              => $objZendDate,
                'sqPessoaTramite'        => $sqPessoaLogada,
                'inImpresso'             => TRUE,
                'nuTramite'              => $serviceTramite->getNextTramiteNumber($dtoSearchArtefato)
            );

            $entityDtoTramite = $serviceTramite->montaEntidateTramite($dataTramite);
            $entityPessoaRecebimento = $this->getEntityManager()->getPartialReference('app:VwPessoa' , $sqPessoaLogada);
            $entityDtoTramite->getEntity()->setSqPessoaRecebimento($entityPessoaRecebimento);

            $objZendDateRecebimento = clone $objZendDate;
            $entityDtoTramite->getEntity()->setDtRecebimento($objZendDateRecebimento->addSecond(1));

            $entityTramite = $serviceTramite->save($entityDtoTramite);
        }else{
            $sqPessoaRecebimento = $entityUltimoTramite->getSqPessoaRecebimento()->getSqPessoa();
            $sqPessoaDestino     = $entityUltimoTramite->getSqPessoaDestino()->getSqPessoa();

            /**
             * caso o ultimo tramite do artefato não for da pessoa logada
             * deve-se registrar um tramite para pessoa logada (SGI) para que este
             * possa encaminhar para quem solicitou a desanexação
             */
            if ($sqPessoaRecebimento != $sqPessoaLogada || $sqPessoaDestino != $sqUnidadeLogada ) {

                $dtoSearchArtefato = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');

                $objZendDate = \Zend_Date::now();

                $dataTramite = array(
                    'sqArtefato'             => $sqArtefato,
                    'sqPessoaDestino'        => $sqUnidadeLogada,
                    'sqPessoaDestinoInterno' => $sqPessoaLogada,
                    'sqUnidadeOrgTramite'    => $sqUnidadeLogada,
                    'sqStatusTramite'        => \Core_Configuration::getSgdoceStatusTramiteRecebido(),
                    'dtTramite'              => $objZendDate,
                    'sqPessoaTramite'        => $sqPessoaLogada,
                    'inImpresso'             => TRUE,
                    'nuTramite'              => $serviceTramite->getNextTramiteNumber($dtoSearchArtefato)
                );

                $entityDtoTramite = $serviceTramite->montaEntidateTramite($dataTramite);


                $entityPessoaRecebimento = $this->getEntityManager()->getPartialReference('app:VwPessoa' , $sqPessoaLogada);
                $entityDtoTramite->getEntity()->setSqPessoaRecebimento($entityPessoaRecebimento);
                $objZendDateRecebimento = clone $objZendDate;
                $entityDtoTramite->getEntity()->setDtRecebimento($objZendDateRecebimento->addSecond(1));

                $entityTramite = $serviceTramite->save($entityDtoTramite);
            }
        }
        return $this;
    }

}