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

use Artefato\Service\ArtefatoImagem as ArtefatoImagemService;

/**
 * Classe para Service de Artefato
 *
 * @package  Artefato
 * @category Service
 * @name     TramiteArtefato
 * @version  1.0.0
 */
class StatusSolicitacao extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * Demanda Finalizada
     *
     *  @var integer
     * */
    const T_DEMANDA_FINALIZADA = 'Finalizada';

    /**
     * @var string
     */
    protected $_entityName = 'app:StatusSolicitacao';

    /**
     * @return
     */
    public function newStatusSolicitacao( \Core_Dto_Search $dto )
    {
        $entStatusSolicitacao = $this->_getRepository('app:StatusSolicitacao')
                                     ->findBy(array(
                                         'sqSolicitacao'            => $dto->getSqSolicitacao(),
                                         'sqPessoaTriagem'          => $dto->getSqPessoaTriagem(),
                                         'sqTipoStatusSolicitacao'  => $dto->getSqTipoStatusSolicitacao(),
                                         'txComentario'             => $dto->getTxComentario()
                                     ));
        
        if( count($entStatusSolicitacao) ) {
            throw new \Core_Exception_ServiceLayer('Requisição enviada mais de uma vez. Já existe uma solicitação com essa situação.');
        }
        
        $entStatusSolicitacao = $this->_newEntity('app:StatusSolicitacao');

        $entTipoStatusSolicitacao = $this->getEntityManager()
                                         ->getPartialReference('app:TipoStatusSolicitacao', $dto->getSqTipoStatusSolicitacao());
        $entStatusSolicitacao->setSqTipoStatusSolicitacao($entTipoStatusSolicitacao);

        $entSolicitacao = null;

        if( $dto->getSqSolicitacao() instanceof \Sgdoce\Model\Entity\Solicitacao) {
            $entStatusSolicitacao->setSqSolicitacao( $dto->getSqSolicitacao() );
            $entSolicitacao = $dto->getSqSolicitacao();
        } else {
            $entSolicitacao = $this->getEntityManager()
                                   ->find('app:Solicitacao', $dto->getSqSolicitacao());

            $entStatusSolicitacao->setSqSolicitacao( $entSolicitacao );
        }

        if( $dto->getSqPessoaTriagem() != '' ) {
            $sqPessoaTriagem = $dto->getSqPessoaTriagem();
            $entPessoaTriagem = $this->getEntityManager()
                                     ->getPartialReference('app:VwPessoa', $sqPessoaTriagem);
            $entStatusSolicitacao->setSqPessoaTriagem($entPessoaTriagem);

        }

        if( $dto->getSqPessoaResponsavel() != '' ) {
            $sqPessoaResponsavel = $dto->getSqPessoaResponsavel();
            $entPessoaResponsavel = $this->getEntityManager()
                                         ->getPartialReference('app:VwPessoa', $sqPessoaResponsavel);
            $entStatusSolicitacao->setSqPessoaResponsavel($entPessoaResponsavel);
        }

        if( $dto->getTxComentario() != '' ) {
            $entStatusSolicitacao->setTxComentario($dto->getTxComentario());
        }

        $entStatusSolicitacao->setDtOperacao(\Zend_Date::now());

        $this->getEntityManager()->persist($entStatusSolicitacao);
        $this->getEntityManager()->flush();

        if( $entSolicitacao->getSqArtefato() instanceof \Sgdoce\Model\Entity\Artefato){
            $entSolicitacao->setSqArtefato($this->getEntityManager()
                                    ->find('app:Artefato', $entSolicitacao->getSqArtefato()->getSqArtefato()));
        }

        if( isset($entSolicitacao) ) {
            if($entStatusSolicitacao->getSqTipoStatusSolicitacao()
                                    ->getSqTipoStatusSolicitacao() != \Core_Configuration::getSgdoceTipoStatusSolicitacaoDevolvidaTriagem() ){
                $this->_sendStatus($entSolicitacao, $dto->getTxComentario());
            }
        }

        return $entStatusSolicitacao;
    }

    /**
     * @param integer $sqSolicitacao
     */
    public function getUltimoStatusSolicitacao( $sqSolicitacao )
    {
        return $this->_getRepository()
                    ->getUltimoStatusSolicitacao($sqSolicitacao);
    }

    /**
     * @param integer $sqSolicitacao
     */
    public function getStatusPorTipo( $sqSolicitacao, $sqTipoStatusSolicitacao )
    {
        return $this->_getRepository()
                    ->getStatusPorTipo($sqSolicitacao, $sqTipoStatusSolicitacao);
    }

    /**
     * @param Solicitacao $entSolicitacao
     *
     * @return boolean
     */
    protected function _sendStatus( $entSolicitacao , $txComentario)
    {
        // EMAIL
        $ultimoStatus = $this->getUltimoStatusSolicitacao($entSolicitacao->getSqSolicitacao());
        $listaAndamentos = array();
        $ultimoAndamento = array();

        if( $ultimoStatus['sqTipoStatusSolicitacao'] == \Core_Configuration::getSgdoceTipoStatusSolicitacaoEmAndamento() ){
            $listaAndamentos = $this->getStatusPorTipo($entSolicitacao->getSqSolicitacao(), \Core_Configuration::getSgdoceTipoStatusSolicitacaoEmAndamento());
            $ultimoAndamento = current($listaAndamentos);
        }

        $status = array(
            \Core_Configuration::getSgdoceTipoStatusSolicitacaoAberta()         => 'Aguardando atendimento',
            \Core_Configuration::getSgdoceTipoStatusSolicitacaoEmAndamento()    => 'Com o atendente',
            \Core_Configuration::getSgdoceTipoStatusSolicitacaoFinalizada()     => 'Atendimento concluído'
        );

        if( count($listaAndamentos)
            && $ultimoAndamento['rownum'] > 1 ) {
            $status[\Core_Configuration::getSgdoceTipoStatusSolicitacaoEmAndamento()]
                    = "Encaminhado para um novo atendente";
        }


        $subject = 'Andamento da solicitação nº ' . $entSolicitacao->getSqSolicitacao() . "/"
                   . $entSolicitacao->getDtSolicitacao()->get(\Zend_Date::YEAR) . ". ("
                   . $ultimoStatus['noTipoStatusSolicitacao'] . ")";

        $arguments = array(
            'status'            => $status,
            'entSolicitacao'    => $entSolicitacao,
            'ultimoStatus'      => $ultimoStatus,
            'imgLogo'           => ArtefatoImagemService::PATH_IMAGE_LOGO,
            'txComentario'      => ($ultimoStatus['noTipoStatusSolicitacao'] == self::T_DEMANDA_FINALIZADA) ? $txComentario : NULL
        );
        
        $ultimoStatus['txEmail'] = trim($ultimoStatus['txEmail']);
        $objZMail = new \Zend_Validate_EmailAddress();
        
        if( $objZMail->isValid($ultimoStatus['txEmail']) ) {
            $SgdoceMail = new \Sgdoce_Mail();
            $SgdoceMail->prepareBodyHtml('solicitacao_status.phtml', $arguments);
            $SgdoceMail->setRecipients(array(
                'para' => array(
                    $ultimoStatus['noPessoaSolicitacao'] => $ultimoStatus['txEmail']
                )
            ));
            $SgdoceMail->setSubject($subject);
            $SgdoceMail->send();
        } else {
            $this->getMessaging()->addErrorMessage('MN177', 'User');
        }        
        $this->getMessaging()->dispatchPackets();
    }
}