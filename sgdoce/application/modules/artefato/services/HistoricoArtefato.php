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
 * Classe para Service de Historico Artefato
 *
 * @package  Artefato
 * @category Service
 * @name     HistoricoArtefato
 * @version  1.0.0
 */
class HistoricoArtefato extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:HistoricoArtefato';

    /**
     * SALVA HISTORICO.
     *
     * @param Sgdoce\Model\Entity\HistoricoArtefato $entHistoricoArtefato
     *
     * @return Sgdoce\Model\Entity\HistoricoArtefato
     */
    public function save(\Core_Dto_Entity $entHistoricoArtefato )
    {
        $arrDto = array(
            'sqPessoa'  => \Core_Integration_Sica_User::getPersonId(),
            'sqUnidade' => \Core_Integration_Sica_User::getUserUnit()
        );

        $objCDto = \Core_Dto::factoryFromData($arrDto, 'search');

        $entVwPessoa    = $this->getServiceLocator()
                               ->getService('Pessoa')
                               ->findbyPessoaCorporativo($objCDto);
        $entVwUnidOrg   = $this->getServiceLocator()
                               ->getService('VwUnidadeOrg')
                               ->getDadosUnidade($objCDto);

        return $this->_save($entHistoricoArtefato->getSqArtefato(),
                            $entVwUnidOrg,
                            $entVwPessoa,
                            $entHistoricoArtefato->getSqOcorrencia(),
                            $entHistoricoArtefato->getTxDescricaoOperacao());
    }

    /**
     * Metódo que realiza o save do Historico Artefato
     *
     * @param \Sgdoce\Model\Entity\Artefato $entArtefato
     * @param \Sgdoce\Model\Entity\VwUnidadeOrg $entVwUnidOrg
     * @param \Sgdoce\Model\Entity\VwPessoa $entVwPessoa
     * @param int $entOcorrencia
     * @param string $message
     *
     * @return \Sgdoce\Model\Entity\HistoricoArtefato
     */
    protected function _save($entArtefato, $entVwUnidOrg, $entVwPessoa, $entOcorrencia, $message)
    {
        $entHistoricoArtefato = $this->_newEntity('app:HistoricoArtefato');

        if(!($entArtefato->getSqArtefato() instanceof \Sgdoce\Model\Entity\Artefato)){
            $entArtefato = $this->getEntityManager()
                                ->getPartialReference('app:Artefato',  $entArtefato->getSqArtefato());
        }

        $entOcorrencia        = $this->getEntityManager()
                                     ->getPartialReference('app:Ocorrencia',  $entOcorrencia->getSqOcorrencia());
        $entVwUnidOrg         = $this->getEntityManager()
                                     ->getPartialReference('app:VwUnidadeOrg',  $entVwUnidOrg->getSqUnidadeOrg());
        $entVwPessoa          = $this->getEntityManager()
                                     ->getPartialReference('app:VwPessoa',  $entVwPessoa->getSqPessoa());
        $objZendDate          = new \Zend_Date();

        $entHistoricoArtefato->setSqArtefato($entArtefato);
        $entHistoricoArtefato->setSqUnidadeOrg($entVwUnidOrg);
        $entHistoricoArtefato->setSqPessoa($entVwPessoa);
        $entHistoricoArtefato->setSqOcorrencia($entOcorrencia);
        $entHistoricoArtefato->setDtOcorrencia($objZendDate);
        $entHistoricoArtefato->setTxDescricaoOperacao($message);

        $this->getEntityManager()->getUnitOfWork()->detach($entHistoricoArtefato);
        $this->getEntityManager()->persist($entHistoricoArtefato);
        $this->getEntityManager()->flush($entHistoricoArtefato);

        return $entHistoricoArtefato;
    }

    /**
     * RETORNO TEXTO DE AÇÃO.
     *
     * @return string
     */
    public function getMessage($MSGID)
    {
        try {
            $args = func_get_args();
            if(count($args) > 1){
                array_shift($args);
                array_unshift($args, \Core_Registry::getMessage()->translate($MSGID));
                return call_user_func_array('sprintf', $args);
            }
            return \Core_Registry::getMessage()->translate($MSGID);
        } catch( \Core_Exception $objException ) {
            return $objException->getMessage();
        }
    }


    /**
     * SALVA HISTORICO.
     *
     * @param integer $sqArtefato
     * @param integer $sqOcorrencia
     * @param string $strMessage
     *
     * @return Sgdoce\Model\Entity\HistoricoArtefato
     */
    public function registrar($sqArtefato, $sqOcorrencia, $strMessage)
    {
        $arrOptEntity = array(
            'entity'    => 'Sgdoce\Model\Entity\HistoricoArtefato',
            'mapping'   => array(
                'sqArtefato'    => 'Sgdoce\Model\Entity\Artefato',
                'sqOcorrencia'  => 'Sgdoce\Model\Entity\Ocorrencia',
            )
        );

        $arrData = array(
            'sqArtefato'    => $sqArtefato,
            'sqOcorrencia'  => $sqOcorrencia,
            'txDescricaoOperacao' => $strMessage
        );

        $entHistoricoArtefato = \Core_Dto::factoryFromData($arrData, 'entity', $arrOptEntity);

        return $this->save($entHistoricoArtefato);
    }
}
