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
 * Classe para Service de DesmembramentoDesentranhamento
 *
 * @package  Artefato
 * @category Service
 * @name     DesmembramentoDesentranhamento
 * @version  1.0.0
 */

class DesmembramentoDesentranhamento extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:DesmembramentoDesentranhamento';

    /**
     * (non-PHPdoc)
     * @see Core_ServiceLayer_Service_CrudDto::preSave()
     */
    public function preInsert($objEntity, $dto = NULL)
    {
        if( $objEntity->getDtOperacao() ){
            $objEntity->setDtOperacao(new \Zend_Date($objEntity->getDtOperacao()));
        } else {
            $objEntity->setDtOperacao(\Zend_Date::now());
        }

        $txNumeroPecas = str_replace(array('-',','), array(' a ',', '), $objEntity->getTxNumeroPecas());
        $objEntity->setTxNumeroPecas($txNumeroPecas);
    }

    /**
     * @param type $entity
     * @param type $dto
     */
    public function postInsert($entity, $dto = NULL)
    {
        // Salvando histórico.
        $txNumeroPecas = $entity->getTxNumeroPecas();
        $sqArtefato = $entity->getSqArtefato()->getSqArtefato();

        $artefatoEnt = $this->getServiceLocator()
                            ->getService('Artefato')->find($sqArtefato);

        $nuArtefato = $artefatoEnt->getNuArtefato();

        if( $entity->getStDesmembramento() ) {
            $sqArtefatoDestino = $entity->getSqArtefatoDestino()->getSqArtefato();

            $artefatoDestinoEnt = $this->getServiceLocator()
                                       ->getService('Artefato')->find($sqArtefatoDestino);

            $nuArtefatoDestino = $artefatoDestinoEnt->getNuArtefato();

            // #HistoricoArtefato::save();
            $strMessage = $this->getServiceLocator()
                               ->getService('HistoricoArtefato')
                               ->getMessage('MH010', $txNumeroPecas, $nuArtefato, $nuArtefatoDestino);
        } else {

            // #HistoricoArtefato::save();
            $strMessage = $this->getServiceLocator()
                               ->getService('HistoricoArtefato')
                               ->getMessage('MH011', $txNumeroPecas, $nuArtefato);
        }

        $this->getServiceLocator()
             ->getService('HistoricoArtefato')
             ->registrar($sqArtefato,
                         \Core_Configuration::getSgdoceSqOcorrenciaCadastrar(),
                         $strMessage);
    }
}
