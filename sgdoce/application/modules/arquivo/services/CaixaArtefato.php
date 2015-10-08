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
 * Classe para Service de CaixaArtefato
 *
 * @package  Arquivo
 * @category Service
 * @name     CaixaArtefato
 * @version  1.0.0
 */
class CaixaArtefato extends \Core_ServiceLayer_Service_CrudDto
{
    const T_ARQUIVAMENTO_AUTHOR_NOT_FOUND = 'Não foi possível definir autoria do arquivamento';

    private $_entityPessoaLogada = null;
    private $_entityUnidadeLogada = null;


    /**
     * Variavel para receber o nome da entidade
     *
     * @var string
     * @access protected
     * @name $_entityName
     */
    protected $_entityName = 'app:CaixaArtefato';

    /**
     * @param Core_Dto_Entity
     * @throws Exception
     * */
    public function arquivar (\Core_Dto_Entity $dto)
    {
        # persiste o arquivamento
        $this->getEntityManager()->persist($dto->getEntity());
        # persiste o historico para o arquivamento
        $dtOperacao = \Zend_Date::now();
        $sqTipoHistorico = \Core_Configuration::getSgdoceTipoHistoricoArquivoArquivado();

        $this->_doInsertHistorico($dto->getEntity(), $dtOperacao, $sqTipoHistorico);

        # persiste o historico no artefato
        $serviceHA = $this->getServiceLocator()->getService('HistoricoArtefato');

        $sqOcorrencia = \Core_Configuration::getSgdoceSqOcorrenciaArquivar();
        $pessoaOperacao = $this->_getUserOperation()->getNoPessoa();
        $strMessage = $serviceHA->getMessage('MH016',
                                             $dtOperacao->toString('dd/MM/YYYY HH:mm:ss'),
                                             $pessoaOperacao);
        $serviceHA->registrar($dto->getEntity()->getSqArtefato()->getSqArtefato(), $sqOcorrencia, $strMessage);

        $this->finish();
    }

    public function desarquivar(\Core_Dto_Search $dto)
    {
        try {

            $serviceHA = $this->getServiceLocator()->getService('HistoricoArtefato');
            foreach($dto->getSqArtefato()->getApi() as $method) {
                $entityArtefato = $this->_getRepository('app:Artefato')->find($dto->getSqArtefato()->$method());

                $this->_checkArtefatoEmprestado($entityArtefato);

                $entityCaixaArtefato = $this->_getRepository()->findOneBy(array('sqArtefato'=>$entityArtefato->getSqArtefato()));

                if (NULL === $entityCaixaArtefato) {
                    throw new \Core_Exception_ServiceLayer_Verification('Artefato não localizado no arquivo. Já deve ter sido desarquivado');
                }

                //remove 1 segundo para garantir que o historico a ser salvo em seguida seja a ultima movimentação
                $date            = \Zend_Date::now()->subSecond(1);
                $sqTipoHistorico = \Core_Configuration::getSgdoceTipoHistoricoArquivoDesarquivado();

                # persiste o historico do arquivo
                $this->_doInsertHistorico($entityCaixaArtefato, $date, $sqTipoHistorico);

                # persiste um tramite para quem faz o desarquivamento
                $dtoSearchArtefato = \Core_Dto::factoryFromData(array('sqArtefato' => $entityArtefato->getSqArtefato()), 'search');
                $tramiteArtefatoService = $this->getServiceLocator()->getService('TramiteArtefato');
                $entityTramiteArtefato = $this->_newEntity('app:TramiteArtefato');
                $entityTramiteArtefato->setDtTramite($date)
                        ->setDtRecebimento($date)
                        ->setSqArtefato($entityArtefato)
                        ->setSqPessoaTramite($this->_getUserOperation())
                        ->setSqUnidadeOrgTramite($this->_getUnitOperation())
                        ->setNuTramite($tramiteArtefatoService->getNextTramiteNumber($dtoSearchArtefato))
                        ->setSqPessoaDestinoInterno($this->_getUserOperation())
                        ->setInImpresso(true)
                        ->setSqPessoaRecebimento($this->_getUserOperation())
                        ->setSqStatusTramite($this->getEntityManager()->getPartialReference('app:StatusTramite',
                                                \Core_Configuration::getSgdoceStatusTramiteRecebido()))
                        ->setSqPessoaDestino($this->getEntityManager()->getPartialReference('app:VwPessoa',
                                                $this->_getUnitOperation()->getSqUnidadeOrg()))
                        ;

                $this->getEntityManager()->persist($entityTramiteArtefato);

                # persiste o historico no artefato
                $sqOcorrencia = \Core_Configuration::getSgdoceSqOcorrenciaDesarquivar();
                $pessoaOperacao = $this->_getUserOperation()->getNoPessoa();
                $strMessage = $serviceHA->getMessage('MH020',
                                                    \Zend_Date::now()->toString('dd/MM/YYYY HH:mm:ss'),
                                                     $pessoaOperacao);
                $serviceHA->registrar($entityArtefato->getSqArtefato(), $sqOcorrencia, $strMessage);

                /**
                 * remove registro da caixa de artefato
                 *
                 * a remoção do artefato da caixa deve ser feita no final devido ao relacionamento reverso
                 * de sqCaixaArtefato existem em Artefato
                 */
                $this->getEntityManager()->remove($entityCaixaArtefato);
                $this->getEntityManager()->flush();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * @param \Sgdoce\Model\Entity\CaixaArtefato $entityCaixaArtefato
     * @param \Zend_Date $dtOperacao
     * @param integer $sqTipoHistorico
     * @return \Sgdoce\Model\Entity\CaixaHistorico
     */
    public function insertHistorico(\Sgdoce\Model\Entity\CaixaArtefato $entityCaixaArtefato, \Zend_Date $dtOperacao, $sqTipoHistorico)
    {
        return $this->_doInsertHistorico($entityCaixaArtefato, $dtOperacao, $sqTipoHistorico);
    }

    /**
     * Verifica se artefato esta arquivado
     * 
     * @param \Core_Dto_Search $dto
     * @return boolean
     */
    public function isArquivado(\Core_Dto_Search $dto)
    {
        $data = $this->_getRepository()->findOneBySqArtefato($dto->getSqArtefato());

        return ($data) ? TRUE : FALSE;
    }


    /**
     *
     * @param \Sgdoce\Model\Entity\CaixaArtefato $entityCaixaArtefato
     * @return \Sgdoce\Model\Entity\CaixaHistorico
     * @throws \Exception
     */
    private function _doInsertHistorico(\Sgdoce\Model\Entity\CaixaArtefato $entityCaixaArtefato, \Zend_Date $dtOperacao, $sqTipoHistorico)
    {
        # recupera referencia da pessoa que está realizando operacao
        $entityVwPessoa = $this->_getUserOperation();
        # recupera a unidade organizacional da pessoa que esta manipulando o comentario
        $entityVwUnidadeOrgOperacao = $this->_getUnitOperation();

        # verifica o autor existe (usuario da sessao existe na base)
        # isso poderá ocorrer quando a sessao cair
        if (!count($entityVwPessoa)) {
            throw new \Exception(self::T_ARQUIVAMENTO_AUTHOR_NOT_FOUND);
        }

        $entityTHA = $this->getEntityManager()->getPartialReference('app:TipoHistoricoArquivo',$sqTipoHistorico);

        $entityCaixaHistorico = $this->_newEntity('app:CaixaHistorico');
        $entityCaixaHistorico->setSqArtefato($entityCaixaArtefato->getSqArtefato())
                             ->setSqCaixa($entityCaixaArtefato->getSqCaixa())
                             ->setDtOperacao($dtOperacao)
                             ->setSqPessoaOperacao($entityVwPessoa)
                             ->setSqUnidadeOrgOperacao($entityVwUnidadeOrgOperacao)
                             ->setSqTipoHistoricoArquivo($entityTHA);

        $this->getEntityManager()->persist($entityCaixaHistorico);

        return $entityCaixaHistorico;
    }

    /**
     *
     * @return \Sgdoce\Model\Entity\VwPessoa
     */
    private function _getUserOperation()
    {
        if (null === $this->_entityPessoaLogada) {
            $this->_entityPessoaLogada = $this->_getRepository('app:VwPessoa')
                        ->find(\Core_Integration_Sica_User::getPersonId());
        }
        return $this->_entityPessoaLogada;
    }

    /**
     *
     * @return \Sgdoce\Model\Entity\VwUnidadeOrg
     */
    private function _getUnitOperation()
    {
        if (null === $this->_entityUnidadeLogada){
            $this->_entityUnidadeLogada = $this->_getRepository('app:VwUnidadeOrg')
                                               ->find(\Core_Integration_Sica_User::getUserUnit());
        }

        return $this->_entityUnidadeLogada;
    }

    /**
     *
     * @param \Sgdoce\Model\Entity\Artefato $entityArtefato
     * @return \Arquivo\Service\CaixaArtefato
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    private function _checkArtefatoEmprestado(\Sgdoce\Model\Entity\Artefato $entityArtefato)
    {
        $entityVwUltimoHistoricoCaixaArquivo = $this->_getRepository('app:VwUltimoHistoricoCaixaArquivo')
                                                    ->findOneBy(array('sqArtefato'=>$entityArtefato->getSqArtefato()));

        if ($entityVwUltimoHistoricoCaixaArquivo->getEmprestimo()) {
            throw new \Core_Exception_ServiceLayer_Verification('mensagem para quando o artefato estiver emprestado');
        }

        return $this;

    }
}
