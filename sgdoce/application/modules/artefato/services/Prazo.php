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

class Prazo extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:Prazo';

    /**
     * @param $entity
     * @param $dto
     */
    public function preSave($entity, $dto = NULL, $entPrazo = NULL)
    {
        $sqPessoaDestino = $entity->getSqPessoaDestino();
        if( $sqPessoaDestino
            && is_numeric($sqPessoaDestino) ) {
            $entSqPessoaDestino = $this->getEntityManager()
                                   ->getPartialReference('app:VwPessoa',
                                    $entity->getSqPessoaDestino());
            $entity->setSqPessoaDestino($entSqPessoaDestino);
        } else if( $sqPessoaDestino instanceof \Sgdoce\Model\Entity\VwPessoa ) {
            if( $entity->getSqPessoaDestino()
                && is_null($entity->getSqPessoaDestino()->getSqPessoa()) ){
                $entity->setSqPessoaDestino(NULL);
            }
        } else {
            $entity->setSqPessoaDestino(NULL);
        }


        if( is_null($entity->getDtCadastro()) ) {
            $entity->setDtCadastro(\Zend_Date::now());
        }

        $sqPrazoPai = $entity->getSqPrazoPai();

        if( !empty($sqPrazoPai)
            && is_numeric($sqPrazoPai)) {
            $entPrazoPai = $this->getEntityManager()
                                ->getPartialReference('app:Prazo', $sqPrazoPai);
            $entity->setSqPrazoPai($entPrazoPai);
        }

        $entSqUnidadeOrgPessoa = $this->getEntityManager()
                                           ->getPartialReference('app:VwUnidadeOrg',
                                               \Core_Integration_Sica_User::getUserUnit());

        $entSqPessoa           = $this->getEntityManager()
                                            ->getPartialReference('app:VwPessoa',
                                                \Core_Integration_Sica_User::getPersonId());


        if( $dto->getIsResposta() ) {
            $entity->setDtResposta(\Zend_Date::now());
            $entity->setSqUnidadeOrgPessoaResposta($entSqUnidadeOrgPessoa);
            $entity->setSqPessoaResposta($entSqPessoa);
            $entity->setDtPrazo($entPrazo->getDtPrazo());
            $entity->setTxSolicitacao($entPrazo->getTxSolicitacao());

            if( $dto->getSqArtefatoResposta() ) {
                $entArtefatoResposta = $this->getEntityManager()
                                            ->getPartialReference('app:Artefato',
                                            (integer) $dto->getSqArtefatoResposta());
                $entity->setSqArtefatoResposta($entArtefatoResposta);
            } else {
                $entity->setSqArtefatoResposta(NULL);
            }

            $entSqUnidadeOrgPessoaPrazo = $this->getEntityManager()
                                               ->getPartialReference('app:VwUnidadeOrg',
                                               $entPrazo->getSqUnidadeOrgPessoaPrazo()
                                               ->getSqUnidadeOrg());

            $entSqPessoaPrazo           = $this->getEntityManager()
                                                   ->getPartialReference('app:VwPessoa',
                                                   $entPrazo->getSqPessoaPrazo()
                                                   ->getSqPessoa());

            $entity->setSqUnidadeOrgPessoaPrazo($entSqUnidadeOrgPessoaPrazo);
            $entity->setSqPessoaPrazo($entSqPessoaPrazo);



        } else {

            $entity->setSqUnidadeOrgPessoaPrazo($entSqUnidadeOrgPessoa);
            $entity->setSqPessoaPrazo($entSqPessoa);

        }
    }

    /**
     * @return
     */
    public function listGridReceived(\Core_Dto_Search $dto)
    {
        return $this->_getRepository('app:Prazo')
                    ->searchPageDto('listGrid', $dto, FALSE);
    }

    /**
     * @return
     */
    public function listGridGenerated(\Core_Dto_Search $dto)
    {
        return $this->_getRepository('app:Prazo')
                    ->searchPageDto('listGrid', $dto, FALSE);
    }

    /**
     * @param \Core_Dto_Search $dto
     * @return Array
     */
    public function findArtefatoResposta(\Core_Dto_Search $dto, $limit = 10)
    {
        $retorno = $this->_getRepository('app:Artefato')->findArtefatoResposta($dto, $limit);

        $out = array();
        foreach ($retorno as $item) {
            if( $item['sqTipoArtefato'] == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ) {
                $out[$item['sqArtefato']] = $item['nuArtefato'];
            } else {
                $out[$item['sqArtefato']] = "{$item['nuDigital']}";
            }
        }

        return $out;
    }
}