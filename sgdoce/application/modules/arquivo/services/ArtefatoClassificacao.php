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
 * Classe para Service de Classificacao de Artefato
 *
 * @package  Arquivo
 * @category Service
 * @name     ArtefatoClassificacao
 * @version  1.0.0
 */
class ArtefatoClassificacao extends \Core_ServiceLayer_Service_Crud
{

    /**
     * Variavel para receber o nome da entidade
     *
     * @var string
     * @access protected
     * @name $_entityName
     */
    protected $_entityName = 'app:ArtefatoClassificacao';

    /**
     * Metódo que retorna os dados da Unidade
     * @return array
     */
    public function searchClassificacaoArtefato($arrParans)
    {
        return $this->_getRepository('app:Classificacao')->searchClassificacaoParaArtefato($arrParans);
    }

    /**
     * Método que popula os objetos para serem salvos no banco
     * @return void
     */
    public function setOperationalEntity ($entityName = NULL)
    {
        $this->_data['sqClassificacao'] = $this->_getRepository('app:Classificacao' )->find($this->_data['sqClassificacao']);
        $this->_data['sqArtefato'     ] = $this->_getRepository('app:Artefato' )->find($this->_data['sqArtefato']);
    }


    /**
     * Metodo responsavel pro verificar se o artefato esta na area de trabalho
     * da pessoa logada, se possui demanda em aberto para o artefato ou o usuario é SGI
     *
     * @param integer $sqArtefato
     * @return boolean
     */
    public function checkPermisionArtefato($sqArtefato)
    {
        $dto = \Core_Dto::factoryFromData(array('sqArtefato'=>$sqArtefato), 'search');
        $hasSolicitacaoAberta = $this->getServiceLocator()->getService('Solicitacao')->hasDemandaAberta($dto);
        
        $inMyDashboard        = $this->getServiceLocator()->getService("Artefato")->inMyDashboard($sqArtefato);

        return ($inMyDashboard && !$hasSolicitacaoAberta) || \Zend_Registry::get('isUserSgi');
    }

    public function preSave ($service)
    {
        //só pode ser classificado se não tiver solicitação aberta ou o usuiario logado for SGI
        try {
            $sqArtefato = $service->getEntity()->getSqArtefato()->getSqArtefato();
            if (!$this->checkPermisionArtefato($sqArtefato)) {
                throw new \Core_Exception_ServiceLayer_Verification(
                \Core_Registry::getMessage()->translate('MN156'));
            }
        } catch (\Exception $e) {
            $this->getMessaging()->addErrorMessage($e->getMessage(), 'User');
            throw $e;
        }
    }

    public function postSave ($service)
    {
        $data     = $service->getData();
        $arquivar = (boolean) $data['arquivar'];


        # persiste o historico no artefato
        $serviceHA = $this->getServiceLocator()->getService('HistoricoArtefato');

        $sqOcorrencia = \Core_Configuration::getSgdoceSqOcorrenciaClassificarArtefato();
        $strMessage = $serviceHA->getMessage('MH015',
                                             $service->getEntity()->getSqClassificacao()->getNuClassificacao(),
                                             \Zend_Date::now()->toString('dd/MM/YYYY HH:mm:ss'),
                                             \Core_Integration_Sica_User::getUserName());
        $serviceHA->registrar($service->getEntity()->getSqArtefato()->getSqArtefato(), $sqOcorrencia, $strMessage);

        if ($arquivar) {
            try {
                if (!isset($data['sqCaixa']) || !$data['sqCaixa']) {
                    throw new \Core_Exception_ServiceLayer_Verification('Nenhuma caixa informada para arquivamento após a classificação');
                }

                $entityCaixaArtefato = \Core_Dto::factoryFromData(
                    array(),
                    'entity',
                    array('entity' => '\Sgdoce\Model\Entity\CaixaArtefato')
                );

                $entitiCaixa = $this->getServiceLocator()->getService('CaixaArquivo')->find($data['sqCaixa']);
                $entityCaixaArtefato->setSqArtefato( $service->getEntity()->getSqArtefato() );
                $entityCaixaArtefato->setSqCaixa($entitiCaixa);
//                sleep(1); //retarda o arquivamento para não gerar historicos com mesma data
                $this->getServiceLocator()->getService('CaixaArtefato')->arquivar($entityCaixaArtefato);

                $nuCaixa = $entitiCaixa->getNuCaixa().'/'.$entitiCaixa->getNuAno();
                $unidade = $entitiCaixa->getSqUnidadeOrg()->getNoUnidadeOrg();

                $msg  = "Artefato Classificado <b>{$service->getEntity()->getSqClassificacao()->getNuClassificacao()}</b> ";
                $msg .= "e arquivado na caixa n° {$nuCaixa} da {$unidade}";

            } catch (\Exception $e) {
                $msg  = "Artefato Classificado <b>{$service->getEntity()->getSqClassificacao()->getNuClassificacao()}</b> ";
                $msg .= "porém ocorreu um erro ao efetuar o arquivamento. Tente arquivar novamente mais tarde";

                $msgLog = sprintf(
                    '[SGDoc-e] Exception %s in %s(%d): "%s"',
                    get_class($e),
                    __METHOD__,
                    $service->getEntity()->getSqArtefato()->getSqArtefato(),
                    $e->getMessage()
                );
                error_log( $msgLog );
            }

            $this->getMessaging()->addInfoMessage($msg, 'User');
        }else{
            $this->getMessaging()->addInfoMessage(
                    "Artefato Classificado <b>{$service->getEntity()->getSqClassificacao()->getNuClassificacao()}</b>", 'User');
        }
    }
}
