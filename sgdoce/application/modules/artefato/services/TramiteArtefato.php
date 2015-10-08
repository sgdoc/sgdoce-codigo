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
 * Classe para Service de Artefato
 *
 * @package  Artefato
 * @category Service
 * @name     TramiteArtefato
 * @version  1.0.0
 */
class TramiteArtefato extends \Core_ServiceLayer_Service_CrudDto
{

    const FIRST_TRAMITE_NUMBER = 1;

    /**
     * @var string
     */
    protected $_entityName = 'app:TramiteArtefato';

    /**
     * @var string
     */
    protected $_entityArtefato = 'app:Artefato';

    /**
     * @var string
     */
    protected $_entityVwUltimoTramiteArtefato = 'app:VwUltimoTramiteArtefato';
    /**
     * @var string
     */
    protected $_entityVwAreaTrabalho = 'app:VwAreaTrabalho';

    /**
     *
     * @param integer $sqArtefato
     * @return \Sgdoce\Model\Repository\VwUltimoTramiteArtefato
     */
    public function getLastTramite ($sqArtefato)
    {
        return $this->_getRepository('app:VwUltimoTramiteArtefato')->find($sqArtefato);
    }

    /**
     *
     * @param integer $sqArtefato
     *
     * @return \Sgdoce\Model\Entity\TramiteArtefato
     */
    public function insertFirstTramite ($sqArtefato)
    {
        if (!$sqArtefato) {
            throw new \InvalidArgumentException('Values must not be empty.');
        }

        $sqPessoa = \Core_Integration_Sica_User::getPersonId();
        $sqUnidadeOrg = \Core_Integration_Sica_User::getUserUnit();
        $date = new \Zend_Date();

        $entityTramiteArtefato = $this->_newEntity('app:TramiteArtefato');
        $entityTramiteArtefato->setNuTramite(self::FIRST_TRAMITE_NUMBER);
        $entityTramiteArtefato->setDtTramite($date);
        $entityTramiteArtefato->setDtRecebimento($date);
        $entityTramiteArtefato->setInImpresso(true);

        #Artefato
        $entityArtefato = $this->getEntityManager()
                ->getPartialReference('app:Artefato', $sqArtefato);
        $entityTramiteArtefato->setSqArtefato($entityArtefato);

        #StatusTramite
        $entityStatusTramite = $this->getEntityManager()
                ->getPartialReference('app:StatusTramite', \Core_Configuration::getSgdoceStatusTramiteRecebido());
        $entityTramiteArtefato->setSqStatusTramite($entityStatusTramite);

        #PessoaTramite
        $entityPessoaRecebimento = $this->getEntityManager()
                ->getPartialReference('app:VwPessoa', $sqPessoa);
        $entityTramiteArtefato->setSqPessoaRecebimento($entityPessoaRecebimento);

        #PessoaTramite
        $entityPessoaTramite = $this->getEntityManager()
                ->getPartialReference('app:VwPessoa', $sqPessoa);
        $entityTramiteArtefato->setSqPessoaTramite($entityPessoaTramite);

        #UnidadeOrgTramite
        $entityUnidadeOrgTramite = $this->getEntityManager()
                ->getPartialReference('app:VwUnidadeOrg', $sqUnidadeOrg);
        $entityTramiteArtefato->setSqUnidadeOrgTramite($entityUnidadeOrgTramite);

        #Pessoa
        $entityPessoa = $this->getEntityManager()
                ->getPartialReference('app:VwPessoa', $sqPessoa);
        $entityTramiteArtefato->setSqPessoaDestinoInterno($entityPessoa);

        #UnidadeOrg -- destino do tramite
        $entityPessoaDestino = $this->getEntityManager()
                ->getPartialReference('app:VwPessoa', \Core_Integration_Sica_User::getUserUnit());

        if( !$entityPessoaDestino
            || empty($entityPessoaDestino) ) {
            throw new \Core_Exception_ServiceLayer(\Core_Registry::getMessage()->translate('MN201'));
        }

        $entityTramiteArtefato->setSqPessoaDestino($entityPessoaDestino);

        $this->getEntityManager()->persist($entityTramiteArtefato);
        $this->getEntityManager()->flush($entityTramiteArtefato);

        return $entityTramiteArtefato;
    }

    /**
     * Recupera o próximo número de tramite de um artefato
     *
     * @param \Core_Dto_Abstract $dto
     * @return integer
     */
    public function getNextTramiteNumber (\Core_Dto_Abstract $dto)
    {
        return $this->_getRepository()->getNextTramiteNumber($dto);
    }

    /**
     * Resgata um artefato recebido por um usuário para a area de trabalho da unidade;
     *
     * @param \Core_Dto_Abstract $dto
     * @return \Sgdoce\Model\Entity\TramiteArtefato
     */
    public function rescue (\Core_Dto_Abstract $dto)
    {
        $sqArtefato = $dto->getSqArtefato();
        $entityUTA = $this->_getRepository($this->_entityVwUltimoTramiteArtefato)->find($sqArtefato);

        $noPessoaRecebimento = 'Não identificado';
        if ($entityUTA->getSqPessoaRecebimento()) {
            $noPessoaRecebimento = $entityUTA->getSqPessoaRecebimento()->getNoPessoa();
        }

        $entityArtefato = $this->_getRepository($this->_entityArtefato)->find($sqArtefato);
        $noTipoArtefato = $entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getNoTipoArtefato();

        $params = array(
            'sqArtefato'            => $sqArtefato,
            'sqTipoRastreamento'    => NULL,
            'txCodigoRastreamento'  => NULL,
            'dtTramite'             => \Zend_Date::now(),
            'sqPessoaTramite'       => \Core_Integration_Sica_User::getPersonId(),
            'sqUnidadeOrgTramite'   => \Core_Integration_Sica_User::getUserUnit(),
            'sqStatusTramite'       => \Core_Configuration::getSgdoceStatusTramiteTramitado(),
            'sqPessoaDestino'       => \Core_Integration_Sica_User::getUserUnit(),
            'inImpresso'            => TRUE,
            'nuTramite'             => $this->getNextTramiteNumber($dto),
        );

        $entityDto = $this->montaEntidateTramite($params);
        $entityTramiteArtefato =  $this->save($entityDto);

        $serviceHA = $this->getServiceLocator()->getService('HistoricoArtefato');

        sleep(1); //só pro historico ser a ultima movimentação
        $strMessage = $serviceHA->getMessage('MH017',
                                $noTipoArtefato,
                                \Zend_Date::now()->get(\Zend_Date::DATETIME_MEDIUM),
                                $noPessoaRecebimento,
                                \Core_Integration_Sica_User::getUserName());

        $nuOcorrencia = \Core_Configuration::getSgdoceSqOcorrenciaResgateTramite();
        $serviceHA->registrar($sqArtefato,
                             $nuOcorrencia,
                             $strMessage);

        $this->finish();

        return $entityTramiteArtefato;

    }

    /**
     * @param integer $sqArtefato
     * @return mixed
     */
    public function receive ($sqArtefato)
    {
        $objUltTramiteArtefato = $this->getLastTramite($sqArtefato);

        if (!($objUltTramiteArtefato instanceof \Sgdoce\Model\Entity\VwUltimoTramiteArtefato)) {
            throw new Exception("Artefato não encontrado.");
        }
        $objArtefato = $this->_getRepository('app:Artefato')
                ->find($objUltTramiteArtefato->getSqArtefato());

        // VERIFICA SE O ARTEFATO JÁ FOI RECEBIDO
        $isReceived = false;

        /* INIT recupera informações para usar em caso de exception */
        $sqTipoArtefato = $objArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato();
        $noTipoArtefato = $sqTipoArtefato->getNoTipoArtefato();

        if ($sqTipoArtefato->getSqTipoArtefato() == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
            $artefatoErro = $this->_formatProcessNumber($objArtefato);
        } else {
            $artefatoErro = $objArtefato->getNuDigital()->getNuEtiqueta();
            $artefatoErro = (strlen($artefatoErro) < 7) ? str_pad($artefatoErro, 7, '0', STR_PAD_LEFT) : $artefatoErro;
        }
        /* END */

        if ($objUltTramiteArtefato->getSqStatusTramite()->getSqStatusTramite() == \Core_Configuration::getSgdoceStatusTramiteCancelado()) {
            throw new \Exception(sprintf(
                    \Core_Registry::getMessage()->translate('MN175'),
                    $noTipoArtefato, $artefatoErro));
        }

        if ($objUltTramiteArtefato->getSqStatusTramite()->getSqStatusTramite() != \Core_Configuration::getSgdoceStatusTramiteTramitado()) {
            $isReceived = true;
        }

        if ($objUltTramiteArtefato->getSqPessoaRecebimento() != '' || $objUltTramiteArtefato->getDtRecebimento() != '') {
            $isReceived = true;
        }

        if ($isReceived) {
            throw new \Exception(sprintf(
                    \Core_Registry::getMessage()->translate('MN165'),
                    $noTipoArtefato, $artefatoErro));
        }

        // VERIFICA SE ARTEFATO PODE SER RECEBIDO PELO USUÁRIO LOGADO.
        $canReceive = true;

        if ($objUltTramiteArtefato->getSqPessoaDestino()->getSqPessoa() != \Core_Integration_Sica_User::getUserUnit()) {
            $canReceive = false;
        }

        if ($objUltTramiteArtefato->getSqPessoaDestinoInterno() != '' && $objUltTramiteArtefato->getSqPessoaDestinoInterno()->getSqPessoa() != \Core_Integration_Sica_User::getPersonId()) {
            $canReceive = false;
        }

        if (!$canReceive) {
            throw new \Exception(sprintf(
                    \Core_Registry::getMessage()->translate('MN166'),
                    $noTipoArtefato, $artefatoErro));
        }

        // RECEBE ARTEFATO.
        $entStatusTramite = $this->_getRepository('app:StatusTramite')
                ->find(\Core_Configuration::getSgdoceStatusTramiteRecebido());
        $entPessoa = $this->_getRepository('app:VwPessoa')
                ->find(\Core_Integration_Sica_User::getPersonId());
        $objTramiteArtefato = $this->_getRepository('app:TramiteArtefato')
                ->find($objUltTramiteArtefato->getSqTramiteArtefato());

        $objTramiteArtefato->setDtRecebimento(\Zend_Date::now());
        $objTramiteArtefato->setSqPessoaRecebimento($entPessoa);
        $objTramiteArtefato->setSqStatusTramite($entStatusTramite);

        $this->getEntityManager()->persist($objTramiteArtefato);
        $this->getEntityManager()->flush($objTramiteArtefato);

        return $objTramiteArtefato;
    }

    /**
     * @param integer $sqArtefato
     * @return mixed
     */
    public function cancel ($sqArtefato)
    {
        $objUltTramiteArtefato = $this->getLastTramite($sqArtefato);

        if (!($objUltTramiteArtefato instanceof \Sgdoce\Model\Entity\VwUltimoTramiteArtefato)) {
            throw new Exception("Artefato não encontrado.");
        }
        $objArtefato = $this->_getRepository('app:Artefato')
                ->find($objUltTramiteArtefato->getSqArtefato());

        // VERIFICA SE O ARTEFATO JÁ FOI RECEBIDO
        $isReceived = false;

        if ($objUltTramiteArtefato->getSqStatusTramite()->getSqStatusTramite() != \Core_Configuration::getSgdoceStatusTramiteTramitado()) {
            $isReceived = true;
        }

        if ($objUltTramiteArtefato->getSqPessoaRecebimento() != '' || $objUltTramiteArtefato->getDtRecebimento() != '') {
            $isReceived = true;
        }

        /* INIT recupera informações para usar em caso de exception */
        $sqTipoArtefato = $objArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato();
        $noTipoArtefato = $sqTipoArtefato->getNoTipoArtefato();

        if ($sqTipoArtefato->getSqTipoArtefato() == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
            $artefatoErro = $this->_formatProcessNumber($objArtefato);
        } else {
            $artefatoErro = $objArtefato->getNuDigital()->getNuEtiqueta();
        }
        /* END */

        if ($isReceived) {
            throw new \Exception(sprintf(\Core_Registry::getMessage()->translate('MN161'), $noTipoArtefato, $artefatoErro));
        }

        // VERIFICA SE ARTEFATO PODE SER CANCELADO PELO USUÁRIO LOGADO.
        if ($objUltTramiteArtefato->getDtCancelamento() != '' ||
                ($objUltTramiteArtefato->getSqPessoaTramite()->getSqPessoa() != \Core_Integration_Sica_User::getPersonId() ||
                    $objUltTramiteArtefato->getSqUnidadeOrgTramite()->getSqUnidadeOrg() != \Core_Integration_Sica_User::getUserUnit())
                ) {
            throw new \Exception(sprintf(\Core_Registry::getMessage()->translate('MN162'), $noTipoArtefato, $artefatoErro));
        }

        // RECEBE ARTEFATO.
        $entStatusTramite = $this->_getRepository('app:StatusTramite')
                ->find(\Core_Configuration::getSgdoceStatusTramiteCancelado());
        $entPessoa = $this->_getRepository('app:VwPessoa')
                ->find(\Core_Integration_Sica_User::getPersonId());
        $objTramiteArtefato = $this->_getRepository('app:TramiteArtefato')
                ->find($objUltTramiteArtefato->getSqTramiteArtefato());

        $objTramiteArtefato->setDtCancelamento(\Zend_Date::now());
        $objTramiteArtefato->setSqPessoaRecebimento($entPessoa);
        $objTramiteArtefato->setSqStatusTramite($entStatusTramite);

        $this->getEntityManager()->persist($objTramiteArtefato);
        $this->getEntityManager()->flush($objTramiteArtefato);

        return $objTramiteArtefato;
    }

    /**
     *
     * @param integer $sqArtefato
     * @param integer $sqPessoaDestino opcional só é passado quando artefato é sigiloso
     * @param integer $sqPessoaDestinoInterno opcional só é passado quando artefato é sigiloso
     * @return \Sgdoce\Model\Entity\TramiteArtefato
     * @throws Exception
     */
    public function goBack( $sqArtefato, $sqPessoaDestino= NULL, $sqPessoaDestinoInterno=NULL )
    {
        $objUltTramiteArtefato = $this->getLastTramite($sqArtefato);

        if (!($objUltTramiteArtefato instanceof \Sgdoce\Model\Entity\VwUltimoTramiteArtefato)) {
            throw new Exception(\Core_Registry::getMessage()->translate('MN163'));
        }

        $sqPessoa     = ($sqPessoaDestinoInterno) ?:\Core_Integration_Sica_User::getPersonId();
        $sqUnidadeOrg = ($sqPessoaDestino) ?:\Core_Integration_Sica_User::getUserUnit();

        // RECEBE ARTEFATO.
        $entArtefato      = $this->getEntityManager()->getPartialReference('app:Artefato'      , $objUltTramiteArtefato->getSqArtefato());
        $entStatusTramite = $this->getEntityManager()->getPartialReference('app:StatusTramite' , \Core_Configuration::getSgdoceStatusTramiteDevolvido());
        $entPessoa        = $this->getEntityManager()->getPartialReference('app:VwPessoa'      , $sqPessoa);
        $entPessoaDestino = $this->getEntityManager()->getPartialReference('app:VwPessoa'      , $sqUnidadeOrg);
        $entUnidadeOrg    = $this->getEntityManager()->getPartialReference('app:VwUnidadeOrg'  , \Core_Integration_Sica_User::getUserUnit());

        $newTramiteArtefato= $this->_newEntity('app:TramiteArtefato');
        $artefatoDto       = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
        $nextNuTramite     = $this->getNextTramiteNumber($artefatoDto);

        $newTramiteArtefato->setSqArtefato($entArtefato);
        $newTramiteArtefato->setSqPessoaTramite($entPessoa);
        $newTramiteArtefato->setSqUnidadeOrgTramite($entUnidadeOrg);
        $newTramiteArtefato->setSqPessoaDestino($entPessoaDestino);
        $newTramiteArtefato->setSqPessoaDestinoInterno($entPessoa);
        $newTramiteArtefato->setSqPessoaRecebimento($entPessoa);
        $newTramiteArtefato->setSqStatusTramite($entStatusTramite);
        $newTramiteArtefato->setNuTramite($nextNuTramite);
        $newTramiteArtefato->setDtTramite(new \Zend_Date(\Zend_Date::now()));
        $newTramiteArtefato->setDtDevolucao(new \Zend_Date(\Zend_Date::now()));
        $newTramiteArtefato->setInImpresso(true);

        $this->getEntityManager()->persist($newTramiteArtefato);
        $this->getEntityManager()->flush($newTramiteArtefato);

        return $newTramiteArtefato;
    }

    /**
     *
     * @param array $data
     * @return array array de objetos Sgdoce\Model\Entity\Artefato
     */
    public function getArtefatoToTramite (array $data)
    {
        $arrArtefato = $this->getEntityManager()->getRepository('app:Artefato')->getArtefatoList($data);
        $arrAux = array();
        foreach ($arrArtefato as $entArtefato) {
            $arrAux[$entArtefato->getSqArtefato()] = array(
                    'hasVinculoSigiloso' => $this->_getRepository('app:ArtefatoVinculo')
                                                 ->hasVinculoSigiloso($entArtefato->getSqArtefato()),
                    'entity' => $entArtefato
                );
        }
        return $arrAux;
    }

    /**
     * Salva o tramite dos artefatos
     *
     * @param array $data
     */
    public function processTramite (array $data)
    {
        $this->getEntityManager()->getConnection()->beginTransaction();

        try {

            $sqUnidadeOrigem = \Core_Integration_Sica_User::getUserUnit();

            $params = array(
                'sqTipoRastreamento'    => $data['sqTipoRastreamento'] ? : NULL,
                'txCodigoRastreamento'  => ($data['txCodigoRastreamento']) ? trim(strip_tags(mb_strtoupper($data['txCodigoRastreamento'], 'UTF-8'))) : NULL,
                'dtTramite'             => \Zend_Date::now(),
                'sqPessoaTramite'       => \Core_Integration_Sica_User::getPersonId(),
                'sqUnidadeOrgTramite'   => \Core_Integration_Sica_User::getUserUnit(),
                'sqStatusTramite'       => \Core_Configuration::getSgdoceStatusTramiteTramitado(),
            );

            foreach ($data['sqArtefato'] as $sqArtefato) {

                $dtoSearchArtefato = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
                $params['sqArtefato'] = $sqArtefato;
                $params['nuTramite'] = $this->getNextTramiteNumber($dtoSearchArtefato);

                if ($data['tipo_tramite'] == 1) {//interno

                    $params['sqPessoaDestino'] = (integer) $data['sqUnidadeOrg'];
                    $params['sqPessoaDestinoInterno'] = ($data['sqPessoaDestinoInterno']) ?
                                                            (integer) $data['sqPessoaDestinoInterno'] :
                                                                NULL;

                    $this->_ruleTramiteInterno($sqUnidadeOrigem,$params['sqPessoaDestino']);

                    $params['inImpresso'] = TRUE;

                    $hasVinculoSigiloso = $this->_getRepository('app:ArtefatoVinculo')
                                               ->hasVinculoSigiloso($sqArtefato);

                    if (! $params['sqPessoaDestinoInterno'] && $hasVinculoSigiloso) {
                        $entityArtefato = $this->_getRepository('app:Artefato')->find($sqArtefato);
                        $helper = new \Sgdoce_View_Helper_NuArtefato();
                        $nrArtefato = $helper->nuArtefato($entityArtefato);
                        throw new \Core_Exception_ServiceLayer(
                            sprintf(\Core_Registry::getMessage()->translate('MN189'), $nrArtefato));
                    }
                } else { //externo

//                    if ($data['stImprimeGuia'] && !$data['sqEndereco']) {
//                        throw new \Core_Exception_ServiceLayer(\Core_Registry::getMessage()->translate('MN172'));
//                    }else{
                    $params['sqEndereco'] = ($data['sqEndereco']) ? $this->getEntityManager()
                                     ->getPartialReference('app:vwEndereco',  $data['sqEndereco']) : NULL;
//                    }

//                    $this->_checkArtefatoSigiloso($dtoSearchArtefato);

                    $params['sqPessoaDestino'] = $data['sqPessoaOrigem'];
                    $params['inImpresso'] = (isset($data['inImpresso'])) ? $data['inImpresso'] : TRUE;
                }

                // verifica se destino foi informado
                if( !isset($params['sqPessoaDestino'])
                    || empty($params['sqPessoaDestino']) ) {
                    throw new \Core_Exception_ServiceLayer(\Core_Registry::getMessage()->translate('MN201'));
                }

                $this->_checkHasImage($params);

                if( !is_null($params['sqTipoRastreamento']) ) {
                    $params['sqTipoRastreamento'] = $this->getEntityManager()->getPartialReference('app:TipoRastreamentoCorreio',  $data['sqTipoRastreamento']);
                } else {
                    $params['sqTipoRastreamento'] = NULL;
                }

                $entityDto = $this->montaEntidateTramite($params);
                $this->save($entityDto);
            }

            $this->getMessaging()->addSuccessMessage(\Core_Registry::getMessage()->translate('MN155'), 'User');

            $this->getMessaging()->dispatchPackets();
            $this->finish();

            $this->getEntityManager()->getConnection()->commit();
        } catch (\Exception $e) {
            $this->getEntityManager()->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * monta entidade "TramiteArtefato" apartir dos parametros enviados
     *
     * @param array $params parametros que serão utilizados para montagem da entidade
     * @return \Core_Dto_Entity
     */
    public function montaEntidateTramite (array $params)
    {
        // Endereço não é mais obrigatório para tramite externo.
        $optionsDtoEntity = array(
            'entity' => 'Sgdoce\Model\Entity\TramiteArtefato',
            'mapping' => array(
//                'sqTipoRastreamento'    => array('sqTipoRastreamentoCorreio' => 'Sgdoce\Model\Entity\TipoRastreamentoCorreio'),
                'sqPessoaDestino'       => array('sqPessoa' => 'Sgdoce\Model\Entity\VwPessoa'),
                'sqPessoaDestinoInterno'=> array('sqPessoa' => 'Sgdoce\Model\Entity\VwPessoa'),
                'sqPessoaTramite'       => array('sqPessoa' => 'Sgdoce\Model\Entity\VwPessoa'),
                'sqUnidadeOrgTramite'   => array('sqUnidadeOrg' => 'Sgdoce\Model\Entity\VwUnidadeOrg'),
                'sqStatusTramite'       => 'Sgdoce\Model\Entity\StatusTramite',
                'sqArtefato'            => 'Sgdoce\Model\Entity\Artefato',
//                'sqEndereco'            => 'Sgdoce\Model\Entity\VwEndereco',
            )
        );

        $entityDto = \Core_Dto::factoryFromData($params, 'entity', $optionsDtoEntity);
        return $entityDto;
    }

    /**
     * Verifica se os artefatos podem realmente serem tramitados pelo usuario
     *
     * @param array $arrSqArtefato
     * @return array $msg array com mensagens personalizadas com erros no tramite
     */
    public function checkCanTramitarArtefato (array $arrSqArtefato)
    {
        $arrMsg           = array();
        $arrArtefatoError = array();
        $arrErrorSigilo   = array();

        $sqPessoa = \Core_Integration_Sica_User::getPersonId();

        foreach ($arrSqArtefato as $sqArtefato) {
            $dto = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato, 'sqPessoa' => $sqPessoa), 'search');
            $this->_checkInMyDashboard($arrArtefatoError, $sqArtefato)
                 ->_checkHasSolicitacaoAberta($arrArtefatoError,$dto)
                 ->_checkHasGrauAcesso($arrErrorSigilo,$sqArtefato);
        }

        if ($arrArtefatoError) {
            $arrArtefatoError = array_unique($arrArtefatoError);
            $arrMsg[] = "O(s) seguinte(s) artefato(s): <b>'" . implode(', ', $arrArtefatoError) . "'</b> não pode(m) ser tramitado(s)";
        }
        if ($arrErrorSigilo) {
            $arrErrorSigilo = array_unique($arrErrorSigilo);
            $arrMsg[] = "O(s) seguinte(s) artefato(s): <b>'" . implode(', ', $arrErrorSigilo) . "'</b> não possue(m) Grau de Acesso definido e não pode(m) ser tramitado(s)";
        }
        return $arrMsg;
    }

    public function getArtefatoGuia (array $arrSqArtefato)
    {
        $data = array();
        //pega informações do 1º artefato para verificar o tipo de artefato esta sendo tramitado
        $entityArtefato = $this->getEntityManager()->getRepository('app:Artefato')->find($arrSqArtefato[0]);

        $sqTipoArtefato = $entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();

        #se tipoArtefato for processo recupera apenas os filhos/netos/bisnetos etc que são processo
        #se tipoArtefato for documento recupera todos os filhos/netos/bisnetos

        foreach ($arrSqArtefato as $sqArtefato) {
            $data[] = $this->getEntityManager()->getRepository($this->_entityVwAreaTrabalho)->findById($sqArtefato);            

            //demais filhos são recuperados de acorco com o tipo de artefato PAI
            if ($sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
                $this->_getProcessChildrenProcess($data, $sqArtefato);
            } else {
                $this->_getProcessChildrenDocument($data, $sqArtefato);
            }
        }

        return $data;
    }

    public function listTramiteExternoComRastreamento (\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->searchPageDto('listTramiteExternoComRastreamento', $dto);
    }

    public function getEnderecosByPessoa(\Core_Dto_Search $dto, $withSelectLabel=true)
    {
        $result = $this->_getRepository('app:VwEndereco')->listaEnderecoTramite($dto);

        $aux = array();
        if ($withSelectLabel) {
            $aux[''] = 'Selecione uma opção';
        }
        foreach($result as $data) {
            $nuEndereco = trim($data['nuEndereco']);
            $endereco   = "[{$data['noTipoEndereco']}] ";
            $endereco  .= $data['noBairro'];
            $endereco  .= ', ' . $data['txEndereco'];
            $endereco  .= ', ' . (is_null($nuEndereco) ? 'S/N': 'Nº ' . $nuEndereco );
            $endereco  .= ', ' . $data['txComplemento'];

            $aux[$data['sqEndereco']] = rtrim(trim($endereco), ',');
        }
        return $aux;
    }

    /**
     * Verifica se um artefato é sigiloso
     *
     * @param \Core_Dto_Search $dto
     * @return \Artefato\Service\TramiteArtefato
     * @throws \Core_Exception_ServiceLayer
     */
    private function _checkArtefatoSigiloso (\Core_Dto_Search $dto)
    {
        $entityGAA = $this->getServiceLocator()->getService('GrauAcessoArtefato')->getGrauAcessoArtefato($dto);

        $entityTipoArtefato = $entityGAA->getSqArtefato()->getSqTipoArtefatoAssunto()->getSqTipoArtefato();
        $sqTipoArtefato = $entityTipoArtefato->getSqTipoArtefato();

        $artefatoError = null;
        if ($entityGAA->getSqGrauAcesso()->getSqGrauAcesso() == \Core_Configuration::getSgdoceGrauAcessoSigiloso()) {
            if ($sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
                $artefatoError = $this->_formatProcessNumber($entityGAA->getSqArtefato());
            } else {
                $artefatoError = $entityGAA->getSqArtefato()->getNuDigital()->getNuEtiqueta();
            }
            $msg = "O %s <b>'%s'</b> é sigiloso e não pode ser tramitado externamente.";
            throw new \Core_Exception_ServiceLayer(
                    sprintf($msg, $entityTipoArtefato->getNoTipoArtefato(), $artefatoError));
        }

        return $this;
    }

    private function _formatProcessNumber (\Sgdoce\Model\Entity\Artefato $entityArtefato)
    {
        return $this->getServiceLocator()->getService('Processo')->formataProcessoAmbitoFederal($entityArtefato);
    }

    private function _formatDigitalNumber (\Sgdoce\Model\Entity\Artefato $entityArtefato)
    {
        $nuDigital = $entityArtefato->getNuDigital()->getNuEtiqueta();

        if (strlen($nuDigital) < 7) {
            $nuDigital = str_pad($nuDigital, 7, '0', STR_PAD_LEFT);
        }

        return $nuDigital;
    }

    /**
     *
     * @param array $data
     * @param integer $sqArtefato
     */
    private function _getProcessChildrenProcess (array &$data, $sqArtefato)
    {
        $dto = \Core_Dto::factoryFromData(array(
                    'sqArtefato' => $sqArtefato,
                    'sqTipoArtefato' => \Core_Configuration::getSgdoceTipoArtefatoProcesso(),
                    'sqPessoaFuncao' => \Core_Configuration::getSgdocePessoaFuncaoAutor(),
                        ), 'search');

        $children = $this->getEntityManager()->getRepository('app:ArtefatoVinculo')->findProcessGuiaProcess($dto);
        $data = array_merge($data, $children);
    }

    /**
     *
     * @param array $data
     * @param integer $sqArtefato
     */
    private function _getProcessChildrenDocument (array &$data, $sqArtefato)
    {
        $dto = \Core_Dto::factoryFromData(array(
                    'sqArtefato' => $sqArtefato,
                    'sqTipoArtefato' => \Core_Configuration::getSgdoceTipoArtefatoDocumento(),
                    'sqPessoaFuncao' => \Core_Configuration::getSgdocePessoaFuncaoOrigem(),
                        ), 'search');

        $children = $this->getEntityManager()->getRepository('app:ArtefatoVinculo')->findProcessGuiaDocument($dto);
        $data = array_merge($data, $children);
    }

    private function _ruleTramiteInterno ($sqUnidadeOrigem, $sqUnidadeDestino)
    {
        $dtoUnidadeOrigem  = \Core_Dto::factoryFromData(array('sqUnidadeOrg' => $sqUnidadeOrigem), 'search');
        $dtoUnidadeDestino = \Core_Dto::factoryFromData(array('sqUnidadeOrg' => $sqUnidadeDestino), 'search');

        $origemIsSede  = $this->_getRepository('app:VwUnidadeOrg')->isSede($dtoUnidadeOrigem);
        $destinoIsSede = $this->_getRepository('app:VwUnidadeOrg')->isSede($dtoUnidadeDestino);

        if (! $origemIsSede && $destinoIsSede && ($sqUnidadeDestino != \Core_Configuration::getSgdoceUnidadeSedoc())) {
            throw new \Core_Exception_ServiceLayer(\Core_Registry::getMessage()->translate('MN190'));
        }
        return $this;
    }

    private function _checkInMyDashboard (&$arrError, $sqArtefato)
    {
        $service = $this->getServiceLocator()->getService('Artefato');

        if (!$service->inMyDashboard($sqArtefato)) {
            $entityArtefato = $this->_getRepository($this->_entityArtefato)->find($sqArtefato);
            $sqTipoArtefato = $entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();
            if ($sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
                $arrError[] = $this->_formatProcessNumber($entityArtefato);
            } else {
                $arrError[] = $entityArtefato->getNuDigital()->getNuEtiqueta();
            }
        }
        return $this;
    }

    private function _checkHasSolicitacaoAberta (&$arrError, $dto)
    {
        $areaTrabalho = $this->_getRepository($this->_entityVwAreaTrabalho)->findArtefato($dto);

        //se não tem aretaTrabalho é pq não esta na area de trabalho da pessoa logada
        if (!$areaTrabalho || $areaTrabalho->getHasSolicitacaoAberta() ) {
            if ($areaTrabalho->getSqTipoArtefato() == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
                $entityArtefato = $this->_getRepository($this->_entityArtefato)->find($dto->getSqArtefato());
                $arrError[] = $this->_formatProcessNumber($entityArtefato);
            } else {
                $arrError[] = $areaTrabalho->getNuDigital();
            }
        }
        return $this;
    }

    private function _checkHasGrauAcesso (&$arrError, $sqArtefato)
    {
        $repoGrauAcessoArtefato = $this->getEntityManager()->getRepository('app:GrauAcessoArtefato');
        $entityGrauAcessoArtefato = $repoGrauAcessoArtefato->findBySqArtefato($sqArtefato);

        if (! $entityGrauAcessoArtefato) {
            $entityArtefato = $this->_getRepository($this->_entityArtefato)->find($sqArtefato);
            $sqTipoArtefato = $entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();
            if ($sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
                $arrError[] = $this->_formatProcessNumber($entityArtefato);
            } else {
                $arrError[] = $this->_formatDigitalNumber($entityArtefato);
            }
        }
        return $this;
    }

    /**
     * Verifica se o artefato a ser tramitado possui imagem
     *
     * @param array $params
     * @return \Artefato\Service\TramiteArtefato
     * @throws \Core_Exception_ServiceLayer
     */
    private function _checkHasImage (array $params)
    {
        $entityUTA = $this->getEntityManager()
                          ->getRepository($this->_entityVwUltimoTramiteArtefato)
                          ->find($params['sqArtefato']);

        $repoArtefato       = $this->getEntityManager()->getRepository($this->_entityArtefato);
        $entityArtefato     = $repoArtefato->find($params['sqArtefato']);
        $sqTipoArtefato     = $entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();
        $hasImage           = $this->getServiceLocator()->getService('ArtefatoImagem')->hasImage($params['sqArtefato'], $sqTipoArtefato);
        $sqPessoaDestinoUTA = $entityUTA->getSqPessoaDestino()->getSqPessoa();

        //pode tramitar sem imagem
        if (!$hasImage) {
            //se tiver só um tramite e o novo tramite é para mesma unidade
            if (($sqPessoaDestinoUTA != $params['sqPessoaDestino']) || ($entityUTA->getNuTramite() != self::FIRST_TRAMITE_NUMBER)) {
                $entityTipoArtefato = $entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato();
                $sqTipoArtefato     = $entityTipoArtefato->getSqTipoArtefato();

                if ($sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
                    $artefatoErro = $this->_formatProcessNumber($entityArtefato);
                } else {
                    $artefatoErro = $entityArtefato->getNuDigital()->getNuEtiqueta();
                }

                $pessoaOrigemTramite = $this->getEntityManager()->getPartialReference('app:VwPessoa', $sqPessoaDestinoUTA);

                $msg = sprintf(\Core_Registry::getMessage()->translate('MN173'),
                               $entityTipoArtefato->getNoTipoArtefato(),
                               $artefatoErro, $pessoaOrigemTramite->getNoPessoa());

                if (($entityUTA->getNuTramite() != self::FIRST_TRAMITE_NUMBER)) {
                    if ($entityArtefato->getStMigracao()) {
                        $msg = "O artefato {$artefatoErro} é oriundo de migração. Para realizar novo trâmite a imagem é obrigatória.";
                    }else{
                        $msg = "O artefato {$artefatoErro} já foi tramitado sem imagem. Para realizar novo trâmite a imagem é obrigatória.";
                    }
                }
                throw new \Core_Exception_ServiceLayer($msg);
            }
        }
        return $this;
    }


}
