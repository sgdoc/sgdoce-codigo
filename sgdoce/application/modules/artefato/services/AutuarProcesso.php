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
 * Classe para Service de ProcessoEletronico
 *
 * @package  Artefato
 * @category     Service
 * @name         ProcessoEletronico
 * @version  1.0.0
 */
class AutuarProcesso extends ProcessoEletronico
{

	public function getPersonId()
	{
		return \Core_Integration_Sica_User::getPersonId();
	}

    /**
     * @var string
     */
    protected $_entityName = 'app:Artefato';

    public function preUpdate($entity, $dto = NULL, $dtoArtefatoProcesso = NULL)
    {
    	$numeroProcesso =  str_replace('.','',str_replace('/','',str_replace('-', '', $dto->getNuArtefato())));
    	$entity->setNuArtefato($numeroProcesso);

        if($dtoArtefatoProcesso && $dtoArtefatoProcesso->getSqEstado()){
        	$dtoArtefatoProcesso->setNuPaginaProcesso($dto->getNuPaginaProcesso());
            $this->saveArtefatoProcesso($dtoArtefatoProcesso);
        }
        if((!$dto->getDtPrazo()) && (!$dto->getNuDiasPrazo()) ){
            $entity->setDtPrazo(NULL);
            $entity->setNuDiasPrazo(NULL);
            $entity->setInDiasCorridos(NULL);
        }else{
            if(!$dto->getDtPrazo()){
                $entity->setDtPrazo(NULL);
            }else{
                $entity->setNuDiasPrazo(NULL);
                $entity->setInDiasCorridos(NULL);
            }
        }

        $entityTipoArtefatoAssunto = $this->_getRepository('app:TipoArtefatoAssunto')
        ->findOneBy(array('sqAssunto' => $dto->getSqAssunto() ,'sqTipoArtefato' =>  $dto->getSqTipoArtefato()));

        if($entityTipoArtefatoAssunto){
            $entity->setSqTipoArtefatoAssunto($entityTipoArtefatoAssunto);
        }
        $this->validateError($entity);
    }
}
