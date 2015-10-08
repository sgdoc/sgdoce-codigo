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
use Doctrine\Common\Util\Debug;

/**
 * Classe para Service de Dossie
 *
 * @package  Artefato
 * @category Service
 * @name      Dossie
 * @version  1.0.0
 */
class Dossie extends Artefato
{

    /**validarPessoa
     * @var string
     */
    protected $_entityName = 'app:Artefato';

    /**
     * método para pesquisa de grid vinculação de documentos
     * @param \Core_Dto_Search $dto
     */
    public function listGridVinculacao(\Core_Dto_Search $dto)
    {
        return $this->_getRepository('app:Artefato')->searchPageDto('listGridDocumentoVinculo', $dto);
    }

    /**
     * método para pesquisa de grid vinculação de documentos
     * @param \Core_Dto_Search $dto
     */
    public function listGridVinculacaoInsercao(\Core_Dto_Search $dto)
    {
        return $this->_getRepository('app:Artefato')->searchPageDto('listGridVinculacaoInsercao', $dto);
    }

    /**
     * método para pesquisa de grid de material de apoio
     * @param \Core_Dto_Search $dto
     */
    public function listGridMaterial(\Core_Dto_Search $dto)
    {
        return $this->_getRepository('app:Artefato')->searchPageDto('listGridMaterialApoio', $dto);
    }

    /**
     * método para pesquisa de grid de documentos
     * @param \Core_Dto_Search $dto
     */
    public function listGridDocumentos(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->searchPageDto('listGridDocumentos', $dto);
    }

    /**
     * método para persistencia  - Grau de Acesso
     * @param \Core_Dto_Search $dto
     */
    public function alterarArtefato($dto)
    {
        $sqTipoDocumento = $this->_getRepository('app:TipoDocumento')->find($dto->getSqTipoDocumento());
        $criteria = array(
                    'sqAssunto' => $dto->getSqAssunto(),
                    'sqTipoArtefato' => \Core_Configuration::getSgdoceTipoArtefatoDossie()
                );
        $sqTipoArtefatoAssunto = $this->_getRepository('app:TipoArtefatoAssunto')->findBy($criteria);

        $entity = $this->_getRepository('app:Artefato')->find($dto->getSqArtefato());
        $entity->setNuArtefato($dto->getNuArtefato());
        $entity->setSqTipoDocumento($sqTipoDocumento);
        $entity->setTxAssuntoComplementar($dto->getTxAssuntoComplementar());

        $entity->setSqTipoArtefatoAssunto($this->_getRepository('app:TipoArtefatoAssunto')
                ->find($sqTipoArtefatoAssunto[0]->getSqTipoArtefatoAssunto()));

        $entityArtefatoDossie = $this->_getRepository('app:ArtefatoDossie')->find($dto->getSqArtefato());
        if(!$entityArtefatoDossie){
            $entityArtefatoDossie = new \Sgdoce\Model\Entity\ArtefatoDossie();
        }
        $entityArtefatoDossie->setNoTitulo($dto->getNoTitulo());
        $entityArtefatoDossie->setTxObservacao($dto->getTxObservacao());
        $entityArtefatoDossie->setSqArtefato($entity);

        $this->getEntityManager()->persist($entityArtefatoDossie);
        $this->getEntityManager()->flush($entityArtefatoDossie);

        //Persistir PessoaAssinanteArtefato
        $this->persistPessoaAssinanteArtefato($entity, $dto);

        self::_salvaOrigem($entity, $dto);

        // RN - Caso não exista Grau de Acesso ao Artefato sera por default publico(1)
        if (!$dto->getSqGrauAcesso()) {
            $data = array('sqGrauAcesso' => \Core_Configuration::getSgdoceGrauAcessoPublico());
            $dto = new \Core_Dto_Mapping($data, array_keys($data));
            $sqGrauAcesso = $this->_getRepository('app:GrauAcesso')->find($dto->getSqGrauAcesso());
        } else {
            $sqGrauAcesso = $this->_getRepository('app:GrauAcesso')->find($dto->getSqGrauAcesso());
        }

        // realizando a persistencia do Grau de Acesso
        $test = $this->persistGrauAcessoArtefato($entity, $sqGrauAcesso);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush($entity);
    }

    /**
     * realiza a persistencia de grau de acesso ao artefato
     * @param \Sgdoce\Model\Entity\Artefato $entity
     * @param \Core_Dto_Search $dto
     * @return NULL
     */
    public function persistPessoaAssinanteArtefato($entity, \Core_Dto_Search $dto)
    {
        // verificando se existe registro em PessoaAssinanteArtefato
        $criteria = array('sqArtefato' => $entity->getSqArtefato());
        $sqPessoaSgdoce = $this->_getRepository('app:PessoaSgdoce')->findBy(array('sqPessoaCorporativo' => $dto->getAssinatura()));
        // verificando se existe Pessoa cadastrada no PessoaSgdoce

        if(!count($sqPessoaSgdoce)){
        	$entPessoaFisica = $this->_getRepository('app:VwPessoaFisica')->find($dto->getAssinatura());
        	$sqPessoaSgdoce = $this->addPessoaSgdoce($dto->getAssinatura(), $entPessoaFisica->getNoPessoaFisica(), $entPessoaFisica->getNuCpf());
        }else{
        	$sqPessoaSgdoce = $sqPessoaSgdoce[0];
        }
        $pessoaUnidadeOrg = $this->getServiceLocator()->getService('Documento')->hasPessoaUnidadeOrg($sqPessoaSgdoce);
        $resPessoaAssinante = $this->_getRepository('app:PessoaAssinanteArtefato')->findOneBy($criteria);

        // verificando se existe registro
        if (count($resPessoaAssinante)) {
        	// atualizando PessoaAssinanteArtefato
        	$resPessoaAssinante->setSqPessoaUnidadeOrg($pessoaUnidadeOrg);
        } else {
        	// Preparando Entidade para salvar
        	$resPessoaAssinante = $this->_newEntity('app:PessoaAssinanteArtefato');
        	$resPessoaAssinante->setSqArtefato($this->_getRepository('app:Artefato')->find($entity->getSqArtefato()));
        	$resPessoaAssinante->setSqPessoaUnidadeOrg($pessoaUnidadeOrg);
        }

        // salvando PessoaAssinanteArtefato
        $this->getEntityManager()->persist($resPessoaAssinante);
        $this->getEntityManager()->flush($resPessoaAssinante);
    }
    /**
     * realiza a persistencia de grau de acesso ao artefato
     */
    public function persistGrauAcessoArtefato($entArtefato, $entGrauAcesso)
    {
    	$grauAcessoArtefato = $this->_getRepository('app:GrauAcessoArtefato')
                ->findOneBy(array('sqArtefato'=>$entArtefato->getSqArtefato()));

    	if($grauAcessoArtefato){
    		$grauAcessoArtefato->setSqGrauAcesso($entGrauAcesso);
    		$grauAcessoArtefato->setDtAtribuicao(\Zend_Date::now());
    	}else{
    		$grauAcessoArtefato = new \Sgdoce\Model\Entity\GrauAcessoArtefato();
    		$grauAcessoArtefato->setSqArtefato($entArtefato);
    		$grauAcessoArtefato->setSqGrauAcesso($entGrauAcesso);
    		$grauAcessoArtefato->setDtAtribuicao(\Zend_Date::now());
    		$grauAcessoArtefato->setStAtivo(TRUE);
    	}

        $this->getEntityManager()->persist($grauAcessoArtefato);
        $this->getEntityManager()->flush($grauAcessoArtefato);

        return $grauAcessoArtefato;
    }

    /**
     *
     * @param \Core_Dto_Search $dto
     *
     * @return void
     */
    public function deleteArtefatoVinculo($dto)
    {
        $entity = $this->getEntityManager()->getRepository('app:ArtefatoVinculo')
                        ->find($dto->getSqArtefatoVinculo());

        $result = $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * método que pesquisa assinatura no banco para preencher combo
     * @param string $term
     */
    public function findAssinatura(\Core_Dto_Abstract $dto)
    {
        return $this->getEntityManager()->getRepository('app:VwProfissional')->searchPessoa($dto);
    }

    /**
     * Retorna pessoa formato json
     * @return string
     */
    public function findNumeroArtefato(\Core_Dto_Abstract $dto)
    {        
        return $this->getEntityManager()->getRepository('app:Artefato')->findNumeroArtefato($dto);
    }

    /**
     * método que pesquisa assinatura no banco para preencher combo
     * @param string $term
     */
    public function findNumeroDigital($term, $dto = NULL, $limit = NULL)
    {
        return $this->getEntityManager()->getRepository('app:Artefato')->findNumeroDigitalDossie($term, $dto, $limit);
    }
    /**
     * método que pesquisa unidade no banco para preencher combo
     * @param String $term
     */
    public function searchUnidade($term)
    {
        return $this->_getRepository('app:VwUnidadeOrg')->searchUnidade($term);
    }

    /**
     * método que pesquisa unidade interna no banco para preencher combo
     * @param String $term
     */
    public function searchUnidadeInterna($term)
    {
        return $this->getEntityManager()->getRepository('app:VwUnidadeOrgInterna')->searchUnidadeInterna($term);
    }

    /**
     * método para cadastrar pessoa na entidade pessoa sgdoce caso necessario
     * @param \Core_Dto_Search $dto
     */
    public function validarPessoa($dto)
    {
        $criteria = array('sqPessoaCorporativo' => $dto->getSqPessoaCorporativo());
        $pessoaSgdoce = $this->_getRepository('app:PessoaSgdoce')->findBy($criteria);
        if (!$pessoaSgdoce) {
            $criteria = array('sqPessoa' => $dto->getSqPessoaCorporativo());
            $pessoa = $this->_getRepository('app:VwPessoa')->findBy($criteria);
            $pessoa = $pessoa[0];
            $sqTipoPessoa = $this->_getRepository('app:VwTipoPessoa')->find($pessoa->getSqTipoPessoa());
            $sqPessoaCorp = $this->_getRepository('app:VwPessoa')->find($pessoa->getSqPessoa());

            $entity =  new \Sgdoce\Model\Entity\PessoaSgdoce();
            $entity->setNoPessoa($pessoa->getNoPessoa());
            $entity->setSqTipoPessoa($sqTipoPessoa);
            $entity->setNoPessoa($pessoa->getNoPessoa());
            $entity->setSqPessoaCorporativo($sqPessoaCorp);

            if ($sqPessoaCorp->getSqTipoPessoa() == \Core_Configuration::getSgdoceTipoPessoaPessoaFisica()) {
                $pessoa = $this->_getRepository('app:VwPessoaFisica')->find($pessoa->getSqPessoa());
                $entity->setNuCpfCnpjPassaporte($pessoa->getNuCpf());
            }

            if ($sqPessoaCorp->getSqTipoPessoa() == \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica()) {
                $pessoa = $this->_getRepository('app:VwPessoaJuridica')->find($pessoa->getSqPessoa());
                $entity->setNuCpfCnpjPassaporte($pessoa->getNuCnpj());
            }

            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * método para excluir artefato
     * @param Query
     */
    public function excluirDossie($dto)
    {
        parent::delete($dto->getSqArtefato());
    }

    /**
     * método que pesquisa artefato pelo numero da digital
     * @param \Core_Dto_Search $dto
     */
    public function findByNuDigital($dto)
    {
        $criteria = array('nuDigital' => $dto->getNuDigital());
        $sqArtefato = $this->_getRepository('app:Artefato')->findBy($criteria);
        return $sqArtefato;
    }

    /**
     * método para validar a RN-3.4
     * @param \Core_Dto_Search $dto
     */
    public function analisarDuplicidade($dto)
    {
        $sqArtefato = $this->_getRepository('app:Artefato')->analisarDuplicidade($dto);

        if ($sqArtefato) {
            $this->getMessaging()->addErrorMessage('Já existe documento cadastrado com essas informações');
            throw new \Core_Exception_ServiceLayer_Verification();
        }
        return $sqArtefato;
    }

    /**
     * método para auto complete via ajax da modal de vinculacao
     * @param \Core_Dto_Search $dto
     */
    public function findAutoComplete($dto)
    {
        if (!$dto->getNuDigital()) {
            $criteria = array('nuArtefato' => $dto->getNuArtefato());
        }else{
            $criteria = array('nuDigital' => $dto->getNuDigital());
        }
        return $this->_getRepository('app:Artefato')->findBy($criteria);
    }

    /**
     * método que traz a lotacao atual do usuario
     * @param \Core_Dto_Abstract $dto
     */
    public function unidadeOrigemPessoa(\Core_Dto_Abstract $dto)
    {
        return $this->getEntityManager()->getRepository('app:VwProfissional')->find($dto->getSqProfissional());
    }

    public function findArtefato(\Core_Dto_Search $dto)
    {
        $criteria = array('nuArtefato' => $dto->getNuArtefato(),'nuDigital' => $dto->getNuDigital());
        return $this->_getRepository('app:Artefato')->findBy($criteria);
    }

    public function verificaDuplicidade($dto)
    {
        return $this->_getRepository('app:Artefato')->verificaDuplicidade($dto);
    }
}