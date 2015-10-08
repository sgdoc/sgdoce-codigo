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
 * Classe para Service de ProcessoVolume
 *
 * @package  Minuta
 * @category Service
 * @name     ProcessoVolume
 * @version  1.0.0
 */

class ProcessoVolume extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:ProcessoVolume';



    /**
     * método para pesquisa de grid de material de apoio
     * @param \Core_Dto_Search $dto
     */
    public function listGrid(\Core_Dto_Search $dto) {
        return $this->_getRepository()->searchPageDto('listGrid', $dto);
    }

    /**
     * @param type $entity
     * @param type $dto
     */
    public function postSave($entity, $dto = NULL)
    {
        if( $entity->getNuFolhaFinal() ){
            $entArtefatoProcesso = $this->_getRepository('app:ArtefatoProcesso')
                                        ->find($entity->getSqArtefato()->getSqArtefato());
            $entArtefatoProcesso->setNuVolume($entity->getNuVolume());
            $entArtefatoProcesso->setNuPaginaProcesso($entity->getNuFolhaFinal());

            $this->getEntityManager()->merge($entArtefatoProcesso);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param integer $sqArtefato
     * @return integer
     */
    public function getLastVolumeEncerrado($sqArtefato)
    {
        $dto = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
        $sqVolume = $this->_getRepository()->getLastEncerrado($dto);
        $sqVolume = current($sqVolume);
        if( $sqVolume ) {
            $sqVolume = $sqVolume['sqVolume'];
            return $this->_getRepository()->find($sqVolume);
        }
        return false;
    }

    /**
     * @param integer $sqArtefato
     * @return integer
     */
    public function getLastVolumeAberto($sqArtefato)
    {
        $dto = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
        $sqVolume = $this->_getRepository()->getLastAberto($dto);
        $sqVolume = current($sqVolume);
        if( $sqVolume ) {
            $sqVolume = $sqVolume['sqVolume'];
            return $this->_getRepository()->find($sqVolume);
        }
        return false;
    }

    /**
     * @return boolean
     */
    public function validaAberturaVolume($sqArtefato)
    {
        $stVolumeAberto = $this->hasVolumeAberto($sqArtefato);

        if( $stVolumeAberto ) {
            $this->getMessaging()->addErrorMessage("Artefato já possui um volume em aberto.", "User");
        }

        $this->getMessaging()->dispatchPackets();

        return $stVolumeAberto;
    }

    /**
     * @return boolean
     */
    public function validaEncerramentoVolume($sqArtefato)
    {
        $stVolumeAberto = $this->hasVolumeAberto($sqArtefato);

        if( !$stVolumeAberto ) {
            $this->getMessaging()->addErrorMessage("Artefato não possui um volume em aberto.", "User");
        }

        $this->getMessaging()->dispatchPackets();

        return $stVolumeAberto;
    }

    /**
     * @param integer $sqArtefato
     * @return integer
     */
    public function hasVolumeAberto($sqArtefato)
    {
        $volume = $this->findBy(array('sqArtefato' => $sqArtefato), array('nuVolume' => 'DESC'));
        $volume = current($volume);

        $stVolumeAberto = false;

        if( !$volume ) {
            $stVolumeAberto = false;
        } else {
            $dtEncerramento = $volume->getDtEncerramento();

            if( is_null($dtEncerramento) ) {
                $stVolumeAberto = true;
            }
        }

        return $stVolumeAberto;
    }

    /**
     * Método resumido para adicionar volume, sem gerar termo.
     *
     * @return void
     */
    public function addVolume( $volume )
    {
        $dtoVolume = \Core_Dto::factoryFromData($volume, 'search');

        $entPessoaAberturaEncerramento = $this->getEntityManager()
                                              ->getPartialReference('app:VwPessoa',
                                                        $dtoVolume->getSqPessoa());

        $entUnidadeOrgAberturaEncerramento = $this->getEntityManager()
                                                  ->getPartialReference('app:VwUnidadeOrg',
                                                            $dtoVolume->getSqUnidadeOrg());

        $entArtefato                       = $this->getEntityManager()
                                                  ->getPartialReference('app:Artefato',
                                                            $dtoVolume->getSqArtefato());


        $entVolume = $this->_newEntity('app:ProcessoVolume');
        $entVolume->setSqArtefato($entArtefato);
        $entVolume->setNuVolume($dtoVolume->getNuVolume());
        $entVolume->setNuFolhaInicial($dtoVolume->getNuFolhaInicial());
        $entVolume->setSqPessoaAbertura($entPessoaAberturaEncerramento);
        $entVolume->setSqUnidadeOrgAbertura($entUnidadeOrgAberturaEncerramento);
        if( $dtoVolume->getNuFolhaFinal() != null ) {
            $entVolume->setNuFolhaFinal($dtoVolume->getNuFolhaFinal());
            $entVolume->setSqPessoaEncerramento($entPessoaAberturaEncerramento);
            $entVolume->setSqUnidadeOrgEncerramento($entUnidadeOrgAberturaEncerramento);
            $entVolume->setDtEncerramento($dtoVolume->getDtEncerramento());
        }
        $entVolume->setDtAbertura($dtoVolume->getDtAbertura());

        $this->getEntityManager()->persist($entVolume);
        $this->getEntityManager()->flush();

        return $entVolume;
    }

    /**
     *
     * @param integer $sqArtefato
     */
    public function getArtefatoProcesso( $sqArtefato )
    {
        $artefato = $this->_getRepository('app:Artefato')->find($sqArtefato);

        if(!$artefato){
            $artefato = false;
            $this->getMessaging()->addErrorMessage("Artefato não encontrato.", "User");
        }
//      Removido para qualquer unidade abrir volume
//        if( $this->getServiceLocator()
//                 ->getService('AutuarDocumento')
//                 ->isUnidadeProtocolizadora(false) == false ){
//            $this->getMessaging()->addErrorMessage("Somente a Unidade Protocolizadora pode abrir ou encerrar volume.", "User");
//            $artefato = false;
//        }

        if( $artefato && $this->getServiceLocator()
                              ->getService("Artefato")
                              ->inMyDashboard($artefato->getSqArtefato()) == false ) {
            $this->getMessaging()->addErrorMessage("O Processo deve estar em sua Área de Trabalho.", "User");
            $artefato = false;
        }

        $this->getMessaging()->dispatchPackets();

        return $artefato;
    }

    public function update(array $postData)
    {
        $configs   = \Core_Registry::get('configs');

        if (!$postData['sqPessoaAssinaturaAbertura'] && $postData['sqPessoaAssinaturaAberturaBD_hidden']) {
            $postData['sqPessoaAssinaturaAbertura'] = $postData['sqPessoaAssinaturaAberturaBD_hidden'];
        }
        if (!$postData['sqPessoaAssinaturaEncerramento'] && $postData['sqPessoaAssinaturaEncerramentoBD_hidden']) {
            $postData['sqPessoaAssinaturaEncerramento'] = $postData['sqPessoaAssinaturaEncerramentoBD_hidden'];
        }

        $dtoVolume = \Core_Dto::factoryFromData($postData, 'search');

        if( $dtoVolume->getNuFolhaFinal() && ((integer)$dtoVolume->getNuFolhaFinal() <= (integer)$dtoVolume->getNuFolhaInicial()) ){
            throw new \Core_Exception_ServiceLayer('A Folha Final deve ser maior que a Folha Inicial.');
        }

        if( ((integer)$dtoVolume->getNuFolhaFinal() - (integer)$dtoVolume->getNuFolhaInicial()) > (integer)$configs['volume']['maxPagePerVolume'] ){
            throw new \Core_Exception_ServiceLayer('Volume não pode ter mais de 200 páginas.');
        }

        $hasDemandaAlterarVolume = $this->getServiceLocator()
                                        ->getService('Solicitacao')
                                        ->hasDemandaAbertaByAssuntoPessoaResponsavel(
                                                \Core_Dto::factoryFromData(
                                                        array(
                                                                'sqArtefato'               => $dtoVolume->getSqArtefato(),
                                                                'sqTipoAssuntoSolicitacao' => \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoVolumeDeProcesso()
                                                        ),
                                                        'search'
                                                )
                                        );

        $entVolume = $this->_getRepository()->find($dtoVolume->getSqVolume());

        if ((\Zend_Registry::get('isUserSgi') && !$hasDemandaAlterarVolume) && ($this->_checkArtefatoLastTramite($entVolume) < 1)) {
            throw new \Core_Exception_ServiceLayer(\Core_Registry::getMessage()->translate('MN147'));
        }

        $entArtefato        = $this->getEntityManager()->getPartialReference('app:Artefato',     $dtoVolume->getSqArtefato());
        $entPessAbertura    = $this->getEntityManager()->getPartialReference('app:VwPessoa',     $dtoVolume->getSqPessoaAbertura());
        $entUOrgAbertura    = $this->getEntityManager()->getPartialReference('app:VwUnidadeOrg', $dtoVolume->getSqUnidadeOrgAbertura());
        $entPessAssAbertura = $this->getEntityManager()->getPartialReference('app:VwPessoa',     $dtoVolume->getSqPessoaAssinaturaAbertura());

        $sqPessoaAbertura     = \Core_Integration_Sica_User::getPersonId();
        $sqUnidadeOrgAbertura = \Core_Integration_Sica_User::getUserUnit();

        $entVolume->setSqVolume($dtoVolume->getSqVolume())
                  ->setSqArtefato($entArtefato)
                  ->setNuVolume($dtoVolume->getNuVolume())
                  ->setNuFolhaInicial($dtoVolume->getNuFolhaInicial())
                  ->setDtAbertura($dtoVolume->getDtAbertura())
                  ->setSqPessoaAbertura($entPessAbertura)
                  ->setSqUnidadeOrgAbertura($entUOrgAbertura)
                  ->setSqPessoaAssinaturaAbertura($entPessAssAbertura);

        if ($dtoVolume->getSqCargoAssinaturaAbertura()) {
            $entCargoAbertura = $this->getEntityManager()->getPartialReference('app:VwCargo', $dtoVolume->getSqCargoAssinaturaAbertura());
            $entVolume->setSqCargoAssinaturaAbertura($entCargoAbertura)
                      ->setSqFuncaoAssinaturaAbertura(NULL);
        } else {
            $entFuncaoAbertura = $this->getEntityManager()->getPartialReference('app:VwFuncao', $dtoVolume->getSqFuncaoAssinaturaAbertura());
            $entVolume->setSqFuncaoAssinaturaAbertura($entFuncaoAbertura)
                      ->setSqCargoAssinaturaAbertura(NULL);
        }

        if( $dtoVolume->getNuFolhaFinal()) {
            $entPessEncerramento    = $this->getEntityManager()->getPartialReference('app:VwPessoa'    , \Core_Integration_Sica_User::getPersonId());
            $entUOrgEncerramento    = $this->getEntityManager()->getPartialReference('app:VwUnidadeOrg', (integer)\Core_Integration_Sica_User::getUserUnit());
            $entPessAssEncerramento = $this->getEntityManager()->getPartialReference('app:VwPessoa'    , $dtoVolume->getSqPessoaAssinaturaEncerramento());

            $entVolume->setNuFolhaFinal($dtoVolume->getNuFolhaFinal())
                      ->setDtEncerramento($dtoVolume->getDtEncerramento())
                      ->setSqPessoaEncerramento($entPessEncerramento)
                      ->setSqUnidadeOrgEncerramento($entUOrgEncerramento)
                      ->setSqPessoaAssinaturaEncerramento($entPessAssEncerramento);

            if ($dtoVolume->getSqCargoAssinaturaEncerramento()) {
                $entCargoEncerramento = $this->getEntityManager()->getPartialReference('app:VwCargo', $dtoVolume->getSqCargoAssinaturaEncerramento());
                $entVolume->setSqCargoAssinaturaEncerramento($entCargoEncerramento)
                          ->setSqFuncaoAssinaturaEncerramento(NULL);
            } else {
                $entFuncaoEncerramento = $this->getEntityManager()->getPartialReference('app:VwFuncao', $dtoVolume->getSqFuncaoAssinaturaEncerramento());
                $entVolume->setSqFuncaoAssinaturaEncerramento($entFuncaoEncerramento)
                          ->setSqCargoAssinaturaEncerramento(NULL);
            }
        } else {
            $entVolume->setNuFolhaFinal(NULL)
                       ->setDtEncerramento(NULL)
                       ->setSqPessoaEncerramento(NULL)
                       ->setSqUnidadeOrgEncerramento(NULL)
                       ->setSqPessoaAssinaturaEncerramento(NULL)
                       ->setSqCargoAssinaturaEncerramento(NULL)
                       ->setSqFuncaoAssinaturaEncerramento(NULL);
        }

        $this->getEntityManager()->persist($entVolume);
        $this->getEntityManager()->flush();
    }

    /**
     * implementa a regra de negócio para remover o volume
     *
     * @param integer
     * @throws Exception
     * @return Volume
     * */
    public function preDelete($id)
    {
        $entity = $this->_getRepository()->find($id);

        # apenas usuário SGI pode excluir
        if (!\Zend_Registry::get('isUserSgi')) {
            throw new \Exception(\Core_Registry::getMessage()->translate('MN203'));
        }

        $arrFromData = array(
                'sqArtefato'               => $entity->getSqArtefato()->getSqArtefato(),
                'sqTipoAssuntoSolicitacao' => \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoVolumeDeProcesso()
        );

        $hasDemandaExcluirVolume = $this->getServiceLocator()
                                        ->getService('Solicitacao')
                                        ->hasDemandaAbertaByAssuntoPessoaResponsavel(
                                                \Core_Dto::factoryFromData(
                                                    $arrFromData,
                                                    'search'));

        # tem que ter demanda de exclusão
        if (!$hasDemandaExcluirVolume) {
            throw new \Exception(\Core_Registry::getMessage()->translate('MN047'));
        }

        return $this;
    }

    public function checkPermisionArtefato($sqArtefato)
    {
        $dto = \Core_Dto::factoryFromData(array('sqArtefato'=>$sqArtefato), 'search');
        $hasSolicitacaoAberta = $this->getServiceLocator()->getService('Solicitacao')->hasDemandaAberta($dto);
        return ($this->_checkArtefatoInMyDashboard($sqArtefato) && !$hasSolicitacaoAberta);
    }

    public function checkIfVolumeCanBeDeleted($sqArtefato)
    {
        return $this->_getRepository()->notTheOnlyVolume($sqArtefato);
    }

    /**
     * Metodo responsável por verificar se o artefato está na área de trabalho da pessoa logada
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

     /**
     * Metodo responsável por comparar a data de abertura do volume com a data do último tramite do artefato
     *
     * @param \Sgdoce\Model\Entity\ProcessoVolume $entityProcessoVolume
     * @return integer 0 = equal, 1 = later, -1 = earlier
     */
    private function _checkArtefatoLastTramite(\Sgdoce\Model\Entity\ProcessoVolume $entityProcessoVolume)
    {
        $sqArtefato              = $entityProcessoVolume->getSqArtefato()->getSqArtefato();
        $entityUltimoTramite     = $this->_getRepository('app:VwUltimoTramiteArtefato')->find($sqArtefato);
        $dtUltimoTramiteArtefato = $entityUltimoTramite->getDtTramite();

        return $entityProcessoVolume->getDtAbertura()->compare($dtUltimoTramiteArtefato);
    }

    /**
     * Método que busca o último volume do artefato
     *
     * @param integer $sqArtefato
     * @return integer
     */
    public function getLastVolume($sqArtefato)
    {
        $volume = $this->findBy(array('sqArtefato' => $sqArtefato), array('nuVolume' => 'DESC'));
        $volume = current($volume);

        $sqVolume = NULL;

        if( $volume ) {
            $sqVolume = $volume->getSqVolume();
        }

        return $sqVolume;
    }

    public function getDatasMaxMin($sqVolume)
    {
    	return current($this->_getRepository()->getDatasMaxMin($sqVolume));
    }
}