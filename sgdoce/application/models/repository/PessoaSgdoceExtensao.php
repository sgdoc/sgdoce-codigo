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

namespace Sgdoce\Model\Repository;

use Doctrine\DBAL\Types\IntegerType,
    Doctrine\ORM\Mapping\Entity;

/**
 * SISICMBio
 *
 * Classe para Repository de PessoaSgdoce
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Pessoa
 * @version      1.0.0
 * @since        2012-11-20
 */
class PessoaSgdoceExtensao extends \Core_Model_Repository_Base
{

    protected $_enName = 'app:PessoaSgdoce';

     /**
     * Obtén dados da pessoa de destino de uma minuta
     * @param $dto
     * @return array
     */
    public function getPessoaArtefatoDestinatario($dto)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('pf.sqPessoaFuncao,
                                pf.noPessoaFuncao,
                                p.sqPessoa,
                                p.noPessoa,
                                p.noProfissao,
                                t.noTratamento,
                                p.txPosTratamento,
                                tv.deEnderecamento,
                                v.noVocativo,
                                p.txPosVocativo,
                                cid.noMunicipio,
                                est.noEstado,
                                p.coCep,
                                p.noUnidadeOrg,
                                vtuo.noTipoUnidadeOrg
                                ')
            ->from($this->_enName, 'p')
            ->innerJoin('p.sqPessoaFuncao', 'pf')
            ->leftJoin('p.sqTratamentoVocativo', 'tv')
            ->leftJoin('tv.sqTratamento', 't')
            ->leftJoin('tv.sqVocativo', 'v')
            ->leftJoin('p.sqMunicipioEndereco', 'cid')
            ->leftJoin('cid.sqEstado', 'est')
            ->innerJoin('p.sqArtefato', 'a')
            ->leftJoin('p.sqPessoaCorporativo', 'vp')
            ->leftJoin('vp.sqPessoaParaUnidadeOrg', 'vuo')
            ->leftJoin('vuo.sqTipoUnidade', 'vtuo')
            ->andWhere('p.sqArtefato = :sqArtefato')
            ->setParameter('sqArtefato', $dto->getSqArtefato())
            ->andWhere('p.sqPessoaFuncao = :sqPessoaFuncao')
            ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoDestinatario())
            ->getQuery()
            ->execute();

        if (empty($query)) {
            return NULL;
        }

        return $query[0];
    }

    /**
     * Obtén dados da pessoa de autoria de uma minuta
     * @param $dto
     * @return array
     */
    public function getPessoaArtefatoAutor($dto)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('pf.sqPessoaFuncao, pf.noPessoaFuncao, p.sqPessoa, p.noPessoa, p.noProfissao,
                                t.noTratamento, tv.deEnderecamento, v.noVocativo, cid.noMunicipio, est.noEstado,
                                p.coCep, a.txEmenta')
            ->from($this->_enName, 'p')
            ->innerJoin('p.sqPessoaFuncao', 'pf')
            ->leftJoin('p.sqTratamentoVocativo', 'tv')
            ->leftJoin('tv.sqTratamento', 't')
            ->leftJoin('tv.sqVocativo', 'v')
            ->leftJoin('p.sqMunicipioEndereco', 'cid')
            ->leftJoin('cid.sqEstado', 'est')
            ->innerJoin('p.sqArtefato', 'a')
            ->andWhere('p.sqArtefato = :sqArtefato')
            ->setParameter('sqArtefato', $dto->getSqArtefato())
            ->andWhere('p.sqPessoaFuncao = :sqPessoaFuncao')
            ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoAutor())
            ->getQuery()
            ->execute();

        if (empty($query)) {
            return NULL;
        }

        return $query[0];
    }

    public function listBySqTipoPessoa($sqTipoPessoa)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('p.noPessoa, p.sqPessoa')
            ->from($this->_enName, 'p')
            ->innerJoin('p.sqTipoPessoa', 'tp')
            ->where($query->expr()->eq('sqTipoPessoa', ':sqTipoPessoa'))
            ->setParameter(':stTipoPessoa', $sqTipoPessoa)
            ->orderBy('p.noPessoa');

        return $query->getQuery()->getArrayResult();
    }

    public function findPessoaSgdoce($entityPessoaSgdoce)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('MAX(p.sqPessoaSgdoce)')
            ->from('app:PessoaSgdoce', 'p')
            ->andWhere('p.sqPessoaCorporativo = :sqPessoaCorporativo')
            ->setParameter('sqPessoaCorporativo', $entityPessoaSgdoce->getSqPessoaCorporativo()->getSqPessoa());
        return $query->getQuery()->getSingleScalarResult();
    }

    public function findPessoaAssinaturaArtefato($entityPessoaSgdoce)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('p.sqPessoaSgdoce')
            ->from('app:Artefato', 'a')
            ->innerJoin('a.sqPessoaArtefato', 'pa')
            ->innerJoin('pa.sqPessoaSgdoce', 'p')
            ->innerJoin('pa.sqPessoaFuncao', 'pf')
            ->andWhere('a.sqArtefato = :sqArtefato')
            ->setParameter('sqArtefato', $entityPessoaSgdoce->getSqArtefato())
            ->andWhere('p.sqPessoaCorporativo = :sqPessoaCorporativo')
            ->setParameter('sqPessoaCorporativo', $entityPessoaSgdoce->getSqPessoaCorporativo())
            ->andWhere('pf.sqPessoaFuncao = :sqPessoaFuncao')
            ->setParameter('sqPessoaFuncao', $entityPessoaSgdoce->getSqPessoaFuncao());

        return $query->getQuery()->execute();
    }

    /**
     * método que pesquisa assinatura para preencher autocomplete
     * @param string $term
     * @return multitype:NULL
     */
    public function findAssinatura($term)
    {
        $term = mb_strtolower($term, 'UTF-8');
        $queryBuilder = $this->_em
            ->createQueryBuilder()
            ->select('td')
            ->from('app:PessoaSgdoce', 'td')
            ->where("LOWER(td.noPessoa) like '%$term%'");

        $res = $queryBuilder->getQuery()->getArrayResult();

        $out = array();
        foreach ($res as $item) {
            $out[$item['sqPessoaSgdoce']] = $item['noPessoa'];
        }

        return $out;
    }

    public function findPessoaDestinatarioArtefato($dto)
    {
        $query = $this->_em->createQueryBuilder()
        ->select('p.sqPessoaSgdoce,
                  p.noPessoa,
                  e.sqEnderecoSgdoce,
                  te.sqTipoEndereco,
                  te.noTipoEndereco,
                  e.coCep,
                  e.txEndereco,
                  e.nuEndereco,
                  es.sqEstado,
                  es.noEstado,
                  m.sqMunicipio,
                  m.noMunicipio')
        ->from('app:Artefato', 'a')
        ->leftJoin('a.sqPessoaArtefato', 'pa')
        ->leftJoin('pa.sqPessoaSgdoce', 'p')
        ->leftJoin('p.sqPessoaCorporativo', 'pc')

        ->leftJoin('p.sqPessoaEndereco', 'e')

        ->leftJoin('e.sqTipoEndereco', 'te')
        ->leftJoin('e.sqMunicipio', 'm')
        ->leftJoin('m.sqEstado', 'es');

        if($dto->getNuCPFDestinatario()){

            $nuCpfCnpjPassaporte = str_replace('-', '', $dto->getNuCPFDestinatario());
            $nuCpfCnpjPassaporte = str_replace('/', '', $nuCpfCnpjPassaporte);
            $nuCpfCnpjPassaporte = str_replace('.', '', $nuCpfCnpjPassaporte);
            $query->andWhere('p.nuCpfCnpjPassaporte = :nuCpfCnpjPassaporte')
            ->setParameter('nuCpfCnpjPassaporte', $nuCpfCnpjPassaporte);
        }

        $query->andWhere('pa.sqPessoaFuncao = :sqPessoaFuncao')
        ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoDestinatario());
//         ->orWhere('e.sqEnderecoSgdoce IS NULL');
        if($dto->hasSqEnderecoSgdoce()){
            $query->andWhere('e.sqEnderecoSgdoce = :sqEnderecoSgdoce')
            ->setParameter('sqEnderecoSgdoce', $dto->getSqEnderecoSgdoce());
        }
        if($dto->getSqPessoa()){
            $query->andWhere('p.sqPessoaCorporativo = :sqPessoaCorporativo')
            ->setParameter('sqPessoaCorporativo', $dto->getSqPessoa());
        }

        if($dto->getSqPessoaCorporativo()){

            $query->andWhere('p.sqPessoaCorporativo = :sqPessoaCorporativo')
            ->setParameter('sqPessoaCorporativo', $dto->getSqPessoaCorporativo());
        }
        $query->groupBy('p.sqPessoaSgdoce,
                  p.noPessoa,
                  e.sqEnderecoSgdoce,
                  te.sqTipoEndereco,
                  te.noTipoEndereco,
                  e.coCep,
                  e.txEndereco,
                  es.sqEstado,
                  es.noEstado,
                  m.sqMunicipio,
                  m.noMunicipio');
        $query->orderBy('e.dtCadastro','asc');

        return $query->getQuery()->execute();
    }

    /**
     * método que pesquisa assinatura para preencher autocomplete
     * @param string $term
     * @return multitype:NULL
     */
    public function findPessoaJuridicaByCnpj($nuCnpj = NULL, $sqPessoa = NULL)
    {
        $query = $this->_em->createQueryBuilder()
                ->select('p')
                ->from('app:VwPessoa', 'p')
                ->leftJoin('p.sqPessoaJuridica', 'pj');

        if ($sqPessoa) {
            $query->andWhere('p.sqPessoa = :sqPessoa')
                  ->setParameter('sqPessoa', $sqPessoa);
        }

        if($nuCnpj){
            $query->orWhere('pj.nuCnpj = :nuCnpj')
                  ->setParameter('nuCnpj', $nuCnpj);
        }
        
        $arrResult = $query->getQuery()->getResult();
        
        if( is_array($arrResult) ){
            return current($arrResult);
        } 
        
        return $arrResult;
    }

    public function updatePessoa($arrDto)
    {
        $documento = ($arrDto[0]->getSqTipoPessoa()->getSqTipoPessoa() == 1)
            ? $arrDto[0]->getNuCpf()
            : $arrDto[0]->getNuCnpj();

        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->update('app:PessoaSgdoce', 'ps')
            ->set('ps.sqTipoPessoa', $arrDto[0]->getSqTipoPessoa()->getSqTipoPessoa())
            ->set('ps.nuCpfCnpjPassaporte', $queryBuilder->expr()->literal($documento))
            ->set('ps.noPessoa', $queryBuilder->expr()->literal($arrDto[1]->getNoPessoa()))
            ->where('ps.sqPessoaCorporativo = :sqPessoa')
            ->setParameter('sqPessoa', $arrDto[1]->getSqPessoa());


        if($arrDto[0]->getNoMae()) {
            $queryBuilder->set('ps.noMae', $queryBuilder->expr()->literal($arrDto[0]->getNoMae()));
        }

        if($arrDto[0]->getEstadoCivil()) {
            $queryBuilder->set('ps.sqEstadoCivil', $arrDto[0]->getEstadoCivil());
        }

        if($arrDto[0]->getNoProfissao()) {
            $queryBuilder->set('ps.noProfissao', $queryBuilder->expr()->literal($arrDto[0]->getNoProfissao()));
        }

        $return = $queryBuilder->getQuery()->execute();

        return $return;
    }
}
