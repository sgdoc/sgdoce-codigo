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

use Sgdoce\Model\Entity\VwEstadoCivil,
Sgdoce\Model\Entity\VwMunicipio;

/**
 * Classe para Service de Processo
 *
 * @package  Artefato
 * @category Service
 * @name     Processo
 * @version  1.0.0
 */
class Processo extends \Artefato\Service\Artefato
{
    const T_TIPO_AMBITO_PROCESSO_FEDERAL = 'F';

    const T_MASK_15_DIGITS = '99999.999999/99-99';
    const T_MASK_17_DIGITS = '99999.999999/9999-99';
    const T_MASK_21_DIGITS = '9999999.99999999/9999-99';
    /**
     * @var string
     */
    protected $_entityName = 'app:Artefato';

    /**
     * método que implementa pesquisa do autocomplete para listagem de unidades
     * pertencentes ao ICMBio
     * @param $nome nome ou parte do nome da unidade.
     * @return array
     */
    public function unidadeOrgIcmbio(\Core_Dto_Search $dto)
    {
        return $repository = $this->getEntityManager()
        ->getRepository('app:VwUnidadeOrg')
                ->unidadeOrgIcmbio($dto);
    }

    /**
     * método que implementa pesquisa do autocomplete para listagem de unidades
     * pertencentes ao ICMBio
     * @param $nome nome ou parte do nome da unidade.
     * @return array
     */
    public function searchVwUnidadeOrg(\Core_Dto_Search $dto)
    {
        return $repository = $this->getEntityManager()
        ->getRepository('app:VwUnidadeOrg')
        ->searchVwUnidadeOrg($dto);
    }

    /**
     * método que implementa pesquisa do autocomplete para listagem dos funcionários
     * pertencentes ao ICMBio
     * @param $nome nome ou parte do nome do funcionário
     * @return array
     */
    public function funcionarioIcmbio(\Core_Dto_Search $dto)
    {
        return $repository = $this->getEntityManager()
        ->getRepository('app:VwVinculoFuncional')
        ->searchPessoa($dto);
    }

    /**
     * método que implementa pesquisa do autocomplete para listagem dos funcionários
     * pertencentes ao ICMBio
     * @param $nome nome ou parte do nome do funcionário
     * @return array
     */
    public function searchFuncionarioIcmbio(\Core_Dto_Search $dto)
    {
        return $repository = $this->getEntityManager()
        ->getRepository('app:VwVinculoFuncional')
        ->searchFuncionarioIcmbio($dto);
    }

    public function listGridInteressados(\Core_Dto_Search $dto)
    {
        $result = $this->_getRepository()->searchPage('listGridInteressados', $params);
        return $result;
    }

    public function salvaInteressado(\Core_Dto_Mapping $dto)
    {
        if ($dto->getTpInternoExterno() == 'interno') {
            $this->interessadoInterno($dto);
            return;
        }

        $dto->interessadoExterno($dto);
    }

    protected function interessadoInterno(\Core_Dto_Mapping $dto)
    {
        $pessoa = new \Sgdoce\Model\Entity\Pessoa();
        $pessoaFuncao = new \Sgdoce\Model\Entity\PessoaFuncao();
        $artefato = new \Sgdoce\Model\Entity\Artefato();
        $tipoPessoa = new \Sgdoce\Model\Entity\VwTipoPessoa();

        $artefato->setSqArtefato($dto->getSqArtefato());

        if ($dto->getUnidFuncionario() == 'funcionario') {
            $vwPessoa = $this->getServiceLocator()->getService('VwPessoa')->find($dto->getFuncIcmbio());
            $tipoPessoa->setSqTipoPessoa(\Core_Configuration::getSgdocTipoPessoaPessoaFisica());

//             $estadoCivil = new VwEstadoCivil();
//             $estadoCivil->setSqEstadoCivil($vwPessoa->getSqPessoaFisica()->getSqEstadoCivil());

//             $pessoa->setSqEstadoCivil($estadoCivil);
            $pessoa->setNoProfissao($vwPessoa->getSqPessoaFisica()->getNoProfissao());
//             $pessoa->setNoMae($wvPessoa->getSqPessoaFisica()->getNoMae());
        } else {
            $vwPessoa = $this->getServiceLocator()->getService('VwPessoa')->find($dto->getUnidIcmbio());
            $tipoPessoa->setSqTipoPessoa(\Core_Configuration::getSgdocTipoPessoaMinisterioPublico());
        }

        $pessoaFuncao->setSqPessoaFuncao(\Core_Configuration::getSgdocPessoaFuncaoInteressado());

        $pessoa->setSqArtefato($artefato);
        $pessoa->setSqPessoaFuncao($pessoaFuncao);
        $pessoa->setNoPessoa($vwPessoa->getNoPessoa());
        $pessoa->setSqTipoPessoa($tipoPessoa);

        if($vwPessoa->getSqTelefone()->count()){
            $pessoa->setNuTelefone($vwPessoa->getSqTelefone()->first()->getNuTelefone());
        }

        if($vwPessoa->getSqEndereco()->count()){
            $pessoa->setCoCep($vwPessoa->getSqEndereco()->first()->getSqCep());
            $pessoa->setTxEndereco($vwPessoa->getSqEndereco()->first()->getTxEndereco());
            $pessoa->setNuNumeroEndereco($vwPessoa->getSqEndereco()->first()->getNuEndereco());
            $pessoa->setTxComplemento($vwPessoa->getSqEndereco()->first()->getTxComplemento());
            $pessoa->setNoBairro($vwPessoa->getSqEndereco()->first()->getNoBairro());

//             $municipio = new VwMunicipio();
//             $municipio->setSqMunicipio($vwPessoa->getSqEndereco()->first()->getSqMunicipio());

//             $pessoa->setSqMunicipioEndereco($municipio);
        }

        if ($vwPessoa->getSqEmail()->count()) {
            $pessoa->setTxEnderecoEletronico($vwPessoa->getSqEmail()->first()->getTxEmail());
        }

        $pessoa->setSqPessoaCorporativo($vwPessoa);

        $qtdNuHistorico = $this->getServiceLocator()->getService('pessoa')
                ->getNextNuHistoricoPessoaByEntity($pessoa);
        $pessoa->setNuHistoricoPessoa($qtdNuHistorico); //@todo rever sequencial

        $unidade = \Core_Integration_Sica_User::get();
        $unidade = $this->getServiceLocator()->getService('UnidadeOrg')->find($unidade->sqUnidadeOrg);

        $pessoa->setNoUnidadeOrg($unidade->getNoUnidadeOrg());

        $metadata = $this->getEntityManager()->getClassMetadata(get_class($pessoa));
        $uow  = $this->getEntityManager()->getUnitOfWork();

        foreach ($metadata->associationMappings as $field => $prop) {
            $value = $metadata->reflFields[$field]->getValue($pessoa);
            if (is_object($value)) {
                $metadataAssoc = $this->getEntityManager()->getClassMetadata(get_class($value));
                $idsFk = $metadataAssoc->getIdentifierValues($value);
                if ($idsFk) {
                    $uow->registerManaged($value, $idsFk, array());
                    $uow->removeFromIdentityMap($value);
                }
            }
        }

        $eManger = $this->getEntityManager();
        $eManger->persist($pessoa);
        $eManger->flush();
    }

    public function formataProcessoAmbitoFederal(\Sgdoce\Model\Entity\Artefato $entityArtefato)
    {
        $maskNumberFilter = new \Core_Filter_MaskNumber();
        $nuProcesso       = $entityArtefato->getNuArtefato();

        if (self::T_TIPO_AMBITO_PROCESSO_FEDERAL == $entityArtefato->getSqArtefatoProcesso()->getCoAmbitoProcesso()) {
            $mask = null;
            switch( strlen($nuProcesso) )
            {
                case 21:
                    $mask = self::T_MASK_21_DIGITS;
                break;
                case 17:
                    $mask = self::T_MASK_17_DIGITS;
                break;
                case 15:
                    $mask = self::T_MASK_15_DIGITS;
                break;
            }
            
            if( !is_null($mask) ){
                $maskNumberFilter->setMask($mask);
                $nuProcesso = $maskNumberFilter->filter($nuProcesso);
            }
        }

        return $nuProcesso;
    }
}
