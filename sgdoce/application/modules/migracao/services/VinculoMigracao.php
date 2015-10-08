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

namespace Migracao\Service;

use Doctrine\Common\Util\Debug;

/**
 *
 *
 * @package  Minuta
 * @category Service
 * @name     Artefato
 * @version  1.0.0
 */
class VinculoMigracao extends \Core_ServiceLayer_Service_CrudDto
{

    /**
     * @var string
     */
    protected $_entityName   = 'app:ArtefatoVinculo';

    protected $_isAllOk      = true;

    protected $_arResult     = array();

    protected $_arSqArtefatosProcesso = array();

    /**
     * @return
     */
    public function getChilds(\Core_Dto_Search $dto)
    {
        $listTreeview = $this->getServiceLocator()->getService('ArtefatoVinculo')->mostarArvoreMigracao($dto);
        if( $listTreeview ) {
            $listTreeviewProcessed = $this->treeviewToTable($listTreeview);

            $this->_arResult['isOk'] = $this->_isAllOk;
            $this->_arResult['list'] = $listTreeviewProcessed;

            return $this->_arResult;
        }

        return $listTreeview;
    }

    /**
     * @param array $listTreeview
     */
    protected function treeviewToTable( $listTreeview )
    {
        $list = array();
        $padding = 0;
        $firstElement = current($listTreeview);
        foreach( $listTreeview as $key => $item )
        {
            if( $item ) {
                $itemProcessed = array();

                $itemProcessed = $this->process($item);
                $itemProcessed['isChildOk'] = true;
                $itemProcessed['padding'] = $padding;

                if( ($firstElement == $item) ) {
                    $this->_arResult['sqArtefatoPai'] = $key;
                }

                $list[] = $itemProcessed;

                if( count( $item['filhos'] ) ) {
                    $kPai = (count($list) - 1);
                    $list = $this->handleChilds( $kPai, $item['filhos'], $list, $padding + 14 );
                }
            }
        }

        return $list;
    }

    /**
     * @return boolean
     */
    protected function handleChilds( $kPai, $listFilhos, $list, $padding )
    {
        foreach( $listFilhos as $item )
        {
            $itemProcessed = array();
            $itemProcessed = $this->process($item);
            $itemProcessed['padding'] = $padding;

            if( $list[$kPai]['isChildOk'] ) {
                $list[$kPai]['isChildOk'] = $itemProcessed['isOk'];
            }

            $list[] = $itemProcessed;

            if( count( $item['filhos'] ) ) {
                $kPai = (count($list) - 1);
                $list = $this->handleChilds( $kPai, $item['filhos'], $list, $padding + 14 );
            }
        }

        return $list;
    }

    /**
     * @param array $item
     * @todo ao inserir solicitação de migração dos documentos, verificar se o mesmo possui imagem já vinculada,
     * tramita para usuário logado após correção completa da arvore.
     */
    protected function process($item)
    {
        $isOk       = true;
        $listIcons  = array();

        $itemProcessed = $item;

        unset($itemProcessed['filhos']);

        $itemProcessed['sqArtefato'] = $item['sqArtefatoFilho'];

        if( is_null($itemProcessed['sqArtefato']) ) {
            $itemProcessed['sqArtefato'] = $item['sqArtefatoPai'];
        }

        if( !isset($itemProcessed['sqTipoArtefato']) ) {
            $itemProcessed['sqTipoArtefato'] = ($itemProcessed['nuDigital']) ?
                                                    \Core_Configuration::getSgdoceTipoArtefatoDocumento() :
                                                    \Core_Configuration::getSgdoceTipoArtefatoProcesso();
        }

        if( $itemProcessed['sqTipoArtefato'] == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ) {
            $this->_arSqArtefatosProcesso[] = $itemProcessed['sqArtefato'];
        }

        // INSERT SOLICITAÇÃO.
        if( !$itemProcessed['isOrigemValid'] ) {
            $isOk = false;
        }

        if( !$itemProcessed['isDestinoValid'] ) {
            $isOk = false;
        }

        if( !$itemProcessed['isInteressadoValid'] ) {
            $isOk = false;
        }

        if( !$itemProcessed['isAutorValid'] ) {
            $isOk = false;
        }

        if( !$itemProcessed['isAssuntoValid'] ) {
            $isOk = false;
        }

        if( !$itemProcessed['isDatasValid'] ) {
            $isOk = false;
        }

        try {
            $hasImage = $this->getServiceLocator()
                             ->getService('ArtefatoImagem')
                             ->hasImage($itemProcessed['sqArtefato']);
        } catch (\Core_Exception_ServiceLayer $ex) {
            $hasImage = false;
        }

        if( !$isOk ){
            $listIcons[] = "thumbs-down";
            $itemProcessed['icons'] = $listIcons;
        } else {
            $listIcons[] = "thumbs-up";
            $itemProcessed['icons'] = $listIcons;
        }

        $itemProcessed['isChildOk'] = true;
        $itemProcessed['isOk']      = $isOk;

        // Criando solicitação de migração da digital
        if( !$hasImage ) {
            $dtoSearch = \Core_Dto::factoryFromData(array(
                'sqPessoa' => \Core_Integration_Sica_User::getPersonId(),
                'sqUnidadeOrg' => \Core_Integration_Sica_User::getUserUnit(),
                'sqArtefato' => $itemProcessed['sqArtefato']
            ), 'search');

            $this->addSolicitacaoMigracao($dtoSearch);
        }

        $itemProcessed['hasImage']  = $hasImage;

        if( $this->_isAllOk && $itemProcessed['isInconsistent'] ){
            // TRATAMENTO DIFERENTE PARA PROCESSO, QUE NÃO POSSUI IMAGEM
            if( $itemProcessed['sqTipoArtefato'] == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ) {
                $dtoInconsistente = \Core_Dto::factoryFromData(array('sqArtefato' => $itemProcessed['sqArtefato']), 'search');
                $this->_isAllOk = !$this->getServiceLocator()
                                        ->getService('Artefato')
                                        ->isInconsistent( $dtoInconsistente, false, true );
            } else {
                $this->_isAllOk = !$itemProcessed['isInconsistent'];
            }
        }

        return $itemProcessed;
    }

    /**
     * @return
     */
    public function isChild($dto)
    {
        return $this->_getRepository()->isChild($dto);
    }

    /**
     *
     */
    public function verificaImagemArvore( $treeviewData )
    {
        return $this->handleTreeviewImage($treeviewData);
    }

    /**
     * @param array $treeView
     */
    public function handleTreeviewImage( $treeView )
    {
        try {
            foreach( $treeView as $item ) {
                $hasImage = null;

                $item['sqArtefato'] = $item['sqArtefatoFilho'];

                if( is_null($item['sqArtefato']) ) {
                    $item['sqArtefato'] = $item['sqArtefatoPai'];
                }

                if( $item['sqTipoArtefato'] == \Core_Configuration::getSgdoceTipoArtefatoDocumento() ) {
                    $hasImage = $this->getServiceLocator()
                                     ->getService('ArtefatoImagem')
                                     ->hasImage($item['sqArtefato']);
                    if( !$hasImage ) {
                        $dtoSearch = \Core_Dto::factoryFromData(array(
                            'sqPessoa' => \Core_Integration_Sica_User::getPersonId(),
                            'sqUnidadeOrg' => \Core_Integration_Sica_User::getUserUnit(),
                            'sqArtefato' => $item['sqArtefato']
                        ), 'search');

                            $this->addSolicitacaoMigracao($dtoSearch);
                        }
                    }

                if( count($item['filhos']) ) {
                    $this->handleTreeviewImage($item['filhos']);
                }
            }
            return true;
        } catch( \Exception $e ) {
            return $e->getCode();
        }

    }

    /**
     * @return
     */
    public function addSolicitacaoMigracao( $dtoSearch )
    {
        $entArtefato = $this->_getRepository('app:Artefato')->find($dtoSearch->getSqArtefato());

        if( !$entArtefato->isProcesso() ) {
            $entSolicitacao =  $this->_getRepository('app:SolicitacaoMigracaoImagem')
                                    ->findRequest($dtoSearch);
            $entImagemArtefato =  $this->_getRepository('app:VwUltimaImagemArtefato')->find($dtoSearch->getSqArtefato());

            if (!$entImagemArtefato &&
                    !$entSolicitacao &&
                        $this->getServiceLocator()->getService("Artefato")->isMigracao($dtoSearch) &&
                            $this->getServiceLocator()->getService("Artefato")->isInconsistent($dtoSearch, true) ) {

                $entSolicMigracaoImagem = $this->_newEntity('app:SolicitacaoMigracaoImagem');

                $entArtefato = $this->getEntityManager()
                                    ->getPartialReference('app:Artefato',  $dtoSearch->getSqArtefato());

                $entVwUnidOrg         = $this->getEntityManager()
                                             ->getPartialReference('app:VwUnidadeOrg',  $dtoSearch->getSqUnidadeOrg());
                $entVwPessoa          = $this->getEntityManager()
                                             ->getPartialReference('app:VwPessoa',  $dtoSearch->getSqPessoa());
                $objZendDate          = new \Zend_Date();

                $entEmail = $this->_getRepository('app:VwEmail')->findOneBy(array(
                    'sqPessoa'    => $dtoSearch->getSqPessoa(),
                    'sqTipoEmail' => \Core_Configuration::getCorpTipoEmailInstitucional(),
                ));

                $entSolicMigracaoImagem->setSqArtefato($entArtefato);
                $entSolicMigracaoImagem->setSqPessoa($entVwPessoa);
                $entSolicMigracaoImagem->setSqUnidadeOrg($entVwUnidOrg);
                $entSolicMigracaoImagem->setDtSolicitacao($objZendDate);
                $entSolicMigracaoImagem->setStProcessado(0);
                $entSolicMigracaoImagem->setInTentativa(0);
                $entSolicMigracaoImagem->setTxEmail(trim($entEmail->getTxEmail()));

                $this->getEntityManager()->getUnitOfWork()->detach($entSolicMigracaoImagem);
                $this->getEntityManager()->persist($entSolicMigracaoImagem);
                $this->getEntityManager()->flush();
            }
        }
    }

    /**
     * @param integer $sqArtefato
     */
    public function setArtefatoCorrigido( $sqArtefato, $setHistorico = true )
    {
        $dto = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
        return $this->_inserirTramite($dto, $setHistorico);
    }

    public function setMigracaoConcluida( $sqArtefato )
    {

        $entArtefato      = $this->getEntityManager()
                                 ->getPartialReference('app:Artefato', $sqArtefato);
        // Validação para migração do artefato processo ao corrigir todas as inconsistẽncias.
        if( $entArtefato instanceof \Sgdoce\Model\Entity\Artefato
            && $entArtefato->getSqTipoArtefatoAssunto()
                           ->getSqTipoArtefato()
            && $entArtefato->getSqTipoArtefatoAssunto()
                           ->getSqTipoArtefato()
                           ->getSqTipoArtefato() == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ) {
            $dtoInconsistente = \Core_Dto::factoryFromData(array('sqArtefato' => $entArtefato->getSqArtefato()), 'search');

            // Valida somente dados
            if( !$this->getServiceLocator()
                     ->getService('Artefato')
                     ->isInconsistent( $dtoInconsistente, false, true ) ) {
                $this->getServiceLocator()
                     ->getService('DocumentoMigracao')
                     ->setHasImage( $entArtefato->getSqArtefato() );

                foreach( $this->_arSqArtefatosProcesso as $sqArtefato ) {
                    $this->getServiceLocator()
                         ->getService('DocumentoMigracao')
                         ->setHasImage( $sqArtefato );
                }
            }
        }
        // Histórico
        // #HistoricoArtefato::save();
        $strMessage = $this->getServiceLocator()
                ->getService('HistoricoArtefato')
                ->getMessage('MH023');

        $this->getServiceLocator()
             ->getService('HistoricoArtefato')
             ->registrar($entArtefato->getSqArtefato(),
                         \Core_Configuration::getSgdoceSqOcorrenciaCorrigirMigracao(),
                         $strMessage);
    }

    /**
     * @param type $dto
     * @return type
     */
    protected function _inserirTramite( $dto)
    {
        $this->getEntityManager()->getConnection()->beginTransaction();

        try {
            $objUltTramiteArtefato = $this->getServiceLocator()
                                          ->getService('TramiteArtefato')
                                          ->findBy(array('sqArtefato' => $dto->getSqArtefato()));

            $objUltTramiteArtefato = current($objUltTramiteArtefato);

            $entArtefato      = $this->getEntityManager()
                                     ->getPartialReference('app:Artefato', $dto->getSqArtefato());

            if ( $objUltTramiteArtefato instanceof \Sgdoce\Model\Entity\TramiteArtefato ) {

                // RECEBE ARTEFATO.
                $entStatusTramite = $this->getEntityManager()->getPartialReference('app:StatusTramite' ,\Core_Configuration::getSgdoceStatusTramiteRecebido());
                $entPessoa        = $this->getEntityManager()->getPartialReference('app:VwPessoa'      ,\Core_Integration_Sica_User::getPersonId());
                $entPessoaDestino = $this->getEntityManager()->getPartialReference('app:VwPessoa'      ,\Core_Integration_Sica_User::getUserUnit());
                $entUnidadeOrg    = $this->getEntityManager()->getPartialReference('app:VwUnidadeOrg'  ,\Core_Integration_Sica_User::getUserUnit());

                $newTramiteArtefato= $this->_newEntity('app:TramiteArtefato');
                $artefatoDto       = \Core_Dto::factoryFromData(array('sqArtefato' => $dto->getSqArtefato()), 'search');
                $nextNuTramite     = $this->getServiceLocator()
                                          ->getService('TramiteArtefato')
                                          ->getNextTramiteNumber($artefatoDto);

                $newTramiteArtefato->setSqArtefato($entArtefato);
                $newTramiteArtefato->setSqPessoaTramite($entPessoa);
                $newTramiteArtefato->setSqUnidadeOrgTramite($entUnidadeOrg);
                $newTramiteArtefato->setSqPessoaDestino($entPessoaDestino);
                $newTramiteArtefato->setSqPessoaDestinoInterno($entPessoa);
                $newTramiteArtefato->setSqPessoaRecebimento($entPessoa);
                $newTramiteArtefato->setSqStatusTramite($entStatusTramite);
                $newTramiteArtefato->setNuTramite($nextNuTramite);
                $newTramiteArtefato->setDtTramite(\Zend_Date::now());
                $newTramiteArtefato->setDtRecebimento(\Zend_Date::now()->addSecond(1));
                $newTramiteArtefato->setInImpresso(true);

                $this->getEntityManager()->persist($newTramiteArtefato);
                $this->getEntityManager()->flush($newTramiteArtefato);

            } else {

                $newTramiteArtefato = $this->getServiceLocator()
                                           ->getService('TramiteArtefato')
                                           ->insertFirstTramite($dto->getSqArtefato());

            }

            $this->getEntityManager()->getConnection()->commit();
        } catch (\Exception $e) {
            $this->getEntityManager()->getConnection()->rollback();
            throw $e;
        }

        return $newTramiteArtefato;

    }

    /**
     * Método que retorna pesquisa do banco para preencher combo para as pessoas do corporativo
     * @return array
     */
    public function searchPessoaFisica($dtoSearch)
    {
        return $this->_getRepository('app:VwPessoaFisica')->searchPessoaFisica($dtoSearch);
    }

}
