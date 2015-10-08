<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
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
 * Classe para Service de Artefato Processo
 *
 * @package  Artefato
 * @category Service
 * @name      ArtefatoProcesso
 * @version  1.0.0
 */
class ArtefatoProcesso extends Artefato
{
    /**
     * @var string
     */
    protected $_entityName = 'app:ArtefatoProcesso';

    /**
     * (non-PHPdoc)
     * @see \Artefato\Service\ArtefatoExtensao::delete()
     */
    public function delete($sqArtefato)
    {
        parent::delete($sqArtefato);
    }
    
    /**
     * @return 
     */
    public function isProcesso( $sqArtefato, $message = true )
    {
        $retorno = true;
        
        $inMyDashboard = $this->getServiceLocator()
                              ->getService('ArtefatoProcesso')
                              ->inMyDashboard($sqArtefato);
        
        if( !$inMyDashboard && $message ){
            $this->getMessaging()->addErrorMessage("Artefato não esta na sua área de trabalho.", "User");
        }
        
        if( !$sqArtefato && $message ) {
            $this->getMessaging()->addErrorMessage("É necessário informar um processo.", "User");
        }
        
        $objProcesso = $this->find($sqArtefato);
        
        if( !count($objProcesso) && $message){
            $this->getMessaging()->addErrorMessage("Processo não existe.", "User");
        }
        
        if( !$inMyDashboard || !$sqArtefato || !count($objProcesso) ){            
            $retorno = false;
        }
        
        if(!$retorno){
            $this->getMessaging()->dispatchPackets();
            return $retorno;
        }
        
        return $objProcesso;
    }
    
    /**
     * @return Entity
     */
    public function getEntityDto( $params = array(), $options = array() )
    {
        if( !empty($options) ) {
            $dto = \Core_Dto::factoryFromData($params, 'entity', $options);
            return $dto->getEntity();
        } else {            
            return $this->getEntityManager()->getPartialReference($this->_entityName, 0);
        }
    }
}