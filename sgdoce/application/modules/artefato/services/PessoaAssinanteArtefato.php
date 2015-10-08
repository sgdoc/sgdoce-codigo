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
 * Classe para Service de TipoPrioridade
 *
 * @package  Minuta
 * @category Service
 * @name     GrauAcesso
 * @version  1.0.0
 */
use Doctrine\DBAL\Types\BooleanType;

class PessoaAssinanteArtefato extends \Core_ServiceLayer_Service_CrudDto
{

    /**
     * @var string
     */
    protected $_entityName = 'app:PessoaAssinanteArtefato';

    public function getAssinanteArtefato(\Core_Dto_Search $dtoSearch)
    {
        $criteria = array('sqArtefato' => $dtoSearch->getSqArtefato());
        return $this->_getRepository()->findOneBy($criteria);
    }

    /**
     * Metódo que realiza o save da Assinatura
     */
    public function saveAssinatura($pessoaEntity,$dto)
    {
        //assinatura unica, salvar pessoasgdoce, depois unidade,endereco,email,logo e pessoaassinante
        //mudar consulta para trazer pessoa de acordo com a função tb.
        $criteria = array('sqPessoaCorporativo' => $pessoaEntity['PessoaSgdoce']->getSqPessoaCorporativo());
        $entitySgdoce = $this->_getRepository('app:PessoaSgdoce')->findOneBy($criteria);

        $pessoaSgdoce1   = $pessoaEntity['PessoaSgdoce'];
        $pessoaAssinante = $pessoaEntity['PessoaAssinante'];

        if (!$entitySgdoce) {
            $entityAssinante = $this->_newEntity('app:PessoaAssinanteArtefato');
            $existsAssinante = FALSE;
            $pessoaSgdoce1   = $pessoaSgdoce1->getEntity();
            $entityTipoPessoaSgdoce = $this->getEntityManager()->getPartialReference(
                                 'app:VwTipoPessoa',$pessoaSgdoce1->getSqTipoPessoa()->getSqTipoPessoa());
            $entityCorporativo = $this->getEntityManager()->getPartialReference(
                                 'app:VwPessoa',$pessoaSgdoce1->getSqPessoaCorporativo()->getSqPessoa());

            switch ($entityTipoPessoaSgdoce->getSqTipoPessoa()) {
                case 1://fisica

                    if($entityCorporativo->getSqPessoaFisica()->getSqNacionalidade()->getSqPais() == 1 ||
                            $entityCorporativo->getSqPessoaFisica()->getSqNacionalidade()->getSqPais() == NULL)
                    {

                          $pessoaCorp = $this->_getRepository('app:VwPessoaFisica')->find($entityCorporativo->getSqPessoa());
                          $pessoaSgdoce1->setNuCpfCnpjPassaporte($pessoaCorp->getNuCpf());

                    }else{
                        $repository = $this->_getRepository('app:VwPessoa');
                        $atributoTipoDocumento = $repository->getDocumento();
                        $repositoryDoc = $this->_getRepository('app:VwDocumento');
                        $nuDocumento  = $repositoryDoc->findBy(array('sqPessoa' =>
                                $entityCorporativo->getSqPessoa(),
                                'sqAtributoTipoDocumento' => $atributoTipoDocumento['sqAtributoTipoDocumento']));
                        if(count($nuDocumento)){
                            $pessoaSgdoce1->setNuCpfCnpjPassaporte($nuDocumento[0]->getTxValor());
                        }
                    }
                break;
                case 2://juridica
                    if($entityCorporativo->getSqPessoaJuridica())
                    {
                        $pessoaSgdoce1->setNuCpfCnpjPassaporte($entityCorporativo->getSqPessoaJuridica()->getNuCnpj());
                    }
                    break;
                default:
                    ;
                break;
            }
            $pessoaSgdoce1->setSqTipoPessoa($entityTipoPessoaSgdoce);
            $pessoaSgdoce1->setSqPessoaCorporativo($entityCorporativo);
        }else{
            $existsAssinante = FALSE;
//             $pessoaSgdoce = $entitySgdoce;
            $pessoaSgdoce1 = $this->_getRepository('app:PessoaSgdoce')->find($entitySgdoce->getSqPessoaSgdoce());

            $criteria = array('sqArtefato' => $pessoaAssinante->getSqArtefato());
            $entityAssinante = $this->_getRepository('app:PessoaAssinanteArtefato')->findOneBy($criteria);

            if($entityAssinante){

				$entityPessoaUnidadeOrg = $this->getServiceLocator()->getService('Documento')->hasPessoaUnidadeOrg($pessoaSgdoce1);


                $entityAssinanteSgdoce = $this->getEntityManager()
                                         ->getPartialReference('app:TipoAssinante',$dto->getSqTipoAssinante());

                $entityAssinante->setSqPessoaUnidadeOrg($entityPessoaUnidadeOrg);
                $entityAssinante->setSqTipoAssinante($entityAssinanteSgdoce);
                $this->getEntityManager()->persist($entityAssinante);
                $this->getEntityManager()->flush($entityAssinante);
                $existsAssinante = TRUE;
            }
        }

        //salva ou altera sgdoce pessoa / endereco / email / telefone

        //salva ou altera sgdoce pessoa assinante
        $pessoaAssinante->setSqArtefato($pessoaAssinante->getSqArtefato());

        $this->getEntityManager()->persist($pessoaSgdoce1);
        $this->getEntityManager()->flush($pessoaSgdoce1);

        if(!$existsAssinante){
        	$entityPessoaAssinante = $this->getServiceLocator()->getService('PessoaUnidadeOrg')->findUnidSgdoce($pessoaSgdoce1);
        	if(!$entityPessoaAssinante){
        		$entityPessoaAssinante = $this->getServiceLocator()->getService('Documento')->hasPessoaUnidadeOrg($pessoaSgdoce1);
        	}
        	$pessoaAssinante->setSqPessoaUnidadeOrg($entityPessoaAssinante);
            $this->savePessoaAssinatura($pessoaAssinante,$dto);
        }
        return $pessoaAssinante;
    }

    /**
     * Metódo que realiza o save Pessoa Assinatura
     */
    public function savePessoaAssinatura($pessoaEntity,$dto = NULL)
    {
        $entityPessoaArtefato = $this->_newEntity('app:PessoaAssinanteArtefato');

        $entityArtefato = $this->getEntityManager()
        ->getPartialReference('app:Artefato',$pessoaEntity->getSqArtefato()->getSqArtefato());

        $entityPessoaUnidadeOrg = $this->getEntityManager()
        ->getPartialReference('app:PessoaUnidadeOrg',$pessoaEntity->getSqPessoaUnidadeOrg()->getSqPessoaUnidadeOrg());

        if($pessoaEntity->getSqTipoAssinante()){
            $entityAssinanteSgdoce = $this->getEntityManager()
            ->getPartialReference('app:TipoAssinante'
                    ,$pessoaEntity->getSqTipoAssinante()->getSqTipoAssinante());
            $entityPessoaArtefato->setSqTipoAssinante($entityAssinanteSgdoce);
        }
        if($dto){
//             $entityPessoaArtefato->setNoCargoAssinante($dto->getNoProfissao());
        }

        if($pessoaEntity->getNoCargoAssinante()){
//             $entityPessoaArtefato->setNoCargoAssinante($pessoaEntity->getNoCargoAssinante());
        }
        $entityPessoaArtefato->setSqArtefato($entityArtefato);
        $entityPessoaArtefato->setSqPessoaUnidadeOrg($entityPessoaUnidadeOrg);

        $this->getEntityManager()->persist($entityPessoaArtefato);
        $this->getEntityManager()->flush($entityPessoaArtefato);
    }

    /**
     * Metódo que recupera os dados de Assinatura Unica
     * @return array
     */
    public function getDadosAssinaturaUnica(\Core_Dto_Search $dtoSearch)
    {
        return $this->_getRepository('app:PessoaAssinanteArtefato')->getDadosAssinaturaUnica($dtoSearch);
    }

    //ajustado
    /**
     * Realiza consulta pessoa existente
     * @param \Core_Dto_Search $dtoSearch
     */
     public function findAssinaturaArtefato($entityPessoaUnidadeOrg)
     {
         return $this->_getRepository('app:PessoaAssinanteArtefato')->findAssinaturaArtefato($entityPessoaUnidadeOrg);
    }

    /**
     * Realiza consulta pessoa existente
     * @param \Core_Dto_Search $dtoSearch
     */
     public function findCargoAssinante($dtoSearch)
     {
         return $this->_getRepository('app:VwProfissional')
                 ->findOneBy(array('sqProfissional' => $dtoSearch->getSqPessoa()));
    }

    /**
     * Metódo que recupera o Dto da Assinatura
     */
    public function getDtoAssinatura($data)
    {
        $data['sqPessoaCorporativo'] = $data['sqResponsavel'];
        $data['sqTipoPessoa']        = \Core_Configuration::getSgdoceTipoPessoaPessoaFisica();
        $data['sqPessoaFuncao']      = \Core_Configuration::getSgdocePessoaFuncaoAssinatura();
        $data['sqPessoa']            = $data['sqPessoaAssinante'];
        $data['noPessoa']            = $data['sqResponsavel_autocomplete'];

        $dtoAssinatura['PessoaSgdoce'] = \Core_Dto::factoryFromData($data,
                'entity', array('entity'=> 'Sgdoce\Model\Entity\PessoaSgdoce',
                        'mapping' => array(
                                'sqTipoPessoa'         => 'Sgdoce\Model\Entity\VWTipoPessoa'
                                ,'sqPessoaCorporativo'  => array('sqPessoa' => 'Sgdoce\Model\Entity\VwPessoa'))));

        $dtoAssinatura['PessoaAssinante'] = \Core_Dto::factoryFromData($data,
                'entity', array('entity'=> 'Sgdoce\Model\Entity\PessoaAssinanteArtefato',
                        'mapping' => array(
                                'sqArtefato'        => 'Sgdoce\Model\Entity\Artefato'
                                ,'sqPessoaSgdoce'   => 'Sgdoce\Model\Entity\PessoaSgdoce'
                                ,'sqTipoAssinante'  => 'Sgdoce\Model\Entity\TipoAssinante')));
        return $dtoAssinatura;
    }

    /**
     * @param \Core_Dto_Abstract $dto
     * @return BooleanType
     */
    public function deleteAssinatura(\Core_Dto_Abstract $dto)
    {
        return $this->_getRepository('app:PessoaAssinanteArtefato')->deleteAssinatura($dto);
    }

    /**
     * @param \Core_Dto_Abstract $dto
     * @return BooleanType
     */
    public function deleteTodasAssinatura($sqArtefato)
    {
    	return $this->_getRepository('app:PessoaAssinanteArtefato')->deleteTodasAssinatura($sqArtefato);
    }


    public function findPessoaSave($dtoArtefato,$pessoaUnidadeOrg)
    {
		$criteriaPessoa = array('sqArtefato' => $dtoArtefato->getSqArtefato(), 'sqPessoaUnidadeOrg' => $pessoaUnidadeOrg->getSqPessoaUnidadeOrg());
		$pessoaAssinante = $this->findBy($criteriaPessoa);

    	if (count($pessoaAssinante)) {
    		// atualizando PessoaAssinanteArtefato
    		$pessoaAssinante = $pessoaAssinante[0];
    		$pessoaAssinante->setSqPessoaUnidadeOrg($pessoaUnidadeOrg);
    	} else {
    		// Preparando Entidade para salvar
    		$pessoaAssinante = $this->_newEntity('app:PessoaAssinanteArtefato');
    		$pessoaAssinante->setSqArtefato($this->_getRepository('app:Artefato')->find($dtoArtefato->getSqArtefato()));
    		$pessoaAssinante->setSqPessoaUnidadeOrg($pessoaUnidadeOrg);

    		// salvando PessoaAssinanteArtefato
    		$this->getEntityManager()->persist($pessoaAssinante);
    		$this->getEntityManager()->flush($pessoaAssinante);
    	}
    	return $pessoaAssinante;
    }
}
