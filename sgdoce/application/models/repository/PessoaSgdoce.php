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
class PessoaSgdoce extends PessoaSgdoceExtensao
{

    protected $_enName = 'app:PessoaSgdoce';

    /**
     * Realiza busca para grid
     * @param array $params
     * @return array
     */
    public function listDestinatario($search)
    {
        $query = $this->_em
            ->createQueryBuilder()
            ->select('a.sqArtefato, p.sqPessoaSgdoce, pf.sqPessoaFuncao, p.noPessoa, e.coCep, te.noTipoEndereco, e.txEndereco, e.nuEndereco')
            ->from('app:Artefato', 'a')
            ->innerJoin('a.sqPessoaArtefato', 'pa')
            ->innerJoin('pa.sqPessoaFuncao', 'pf')
            ->innerJoin('pa.sqPessoaSgdoce', 'p')
            ->leftJoin('pa.sqEnderecoSgdoce', 'e')
            ->leftJoin('e.sqTipoEndereco', 'te')
            ->leftJoin('pa.sqTratamentoVocativo', 'tv')
            ->leftJoin('tv.sqTratamento', 't')
            ->andWhere('a.sqArtefato = :sqArtefato')
            ->setParameter('sqArtefato', $search->getSqArtefato())
            ->andWhere('pf.sqPessoaFuncao = :sqPessoaFuncao')
            ->setParameter('sqPessoaFuncao', $search->getSqPessoaFuncao());
        return $query;
    }

    /**
     * Realiza busca para grid
     * @param integer $search
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listInteressado($search)
    {
        $query = $this->_em
            ->createQueryBuilder()
            ->select('a.sqArtefato'
                    ,'p.sqPessoaSgdoce'
                    ,'p.noPessoa'
                    ,'coalesce(p.nuCpfCnpjPassaporte, pf.nuCpf) AS nuCpfCnpjPassaporte'
                    ,'tp.sqTipoPessoa'
                    ,'pais.sqPais'
                    ,'pc.sqPessoa as sqPessoaCorporativo')
            ->from('app:Artefato', 'a')
            ->innerJoin('a.sqPessoaInteressadaArtefato', 'pa')
            ->innerJoin('pa.sqPessoaSgdoce', 'p')
            ->innerJoin('p.sqTipoPessoa', 'tp')
            ->leftJoin('p.sqPessoaCorporativo', 'pc')
            ->leftJoin('pc.sqPessoaFisica', 'pf')
            ->leftJoin('pf.sqNacionalidade', 'n')
            ->leftJoin('pf.sqPais', 'pais');
        if ($search->getSqArtefato()) {
            $query->andWhere('a.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $search->getSqArtefato());
        }
        
        return $query;
    }

    /**
     * Realiza busca para grid
     * @param integer $params
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listDocumento($search)
    {
        $query = $this->_em
            ->createQueryBuilder()
            ->select('a.sqArtefato,
                      a.nuArtefato,
                      p.sqPessoaSgdoce,
                      p.noPessoa,
                      p.nuCpfCnpjPassaporte,
                      tp.sqTipoPessoa,
                      ta.noTipoArtefato')
            ->from('app:ArtefatoVinculo', 'av')
            ->innerJoin('av.sqArtefatoFilho', 'a')
            ->innerJoin('a.sqPessoaInteressadaArtefato', 'pa')
            ->innerJoin('a.sqTipoArtefatoAssunto', 'taa')
            ->innerJoin('taa.sqTipoArtefato', 'ta')
            ->innerJoin('pa.sqPessoaSgdoce', 'p')
            ->innerJoin('p.sqTipoPessoa', 'tp');
        if ($search->getSqArtefato()) {
            $query->andWhere('av.sqArtefatoPai = :sqArtefato')
                ->setParameter('sqArtefato', $search->getSqArtefato());
        }

        return $query;
    }

    /**
     * Obtém select por tipo de pessoa
     * @param  $search
     * @return string
     */
    protected function getSelect($search)
    {
        $select = 'p';
        switch ($search->getSqTipoPessoaInteressado()) {
            case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica() :
                $select = 'p, pf';
                break;
            case \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica() :
                $select = 'p, pj';
                break;
            case \Core_Configuration::getSgdoceTipoPessoaEstrangeiro() :
                $select = 'p, d';
                break;
        }
        return $select;
    }

    /**
     * Obtém documento pessoa
     * @param $search
     * @param $pessoa
     * @return integer
     */
    protected function getCpfCnpjPassaporte($search, $pessoa)
    {
        $nuCpfCnpjPassaporte = '';

        switch ($search->getSqTipoPessoaInteressado()) {
            case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica() :
                $nuCpfCnpjPassaporte = ($pessoa->getSqPessoaFisica() != NULL ?
                        $pessoa->getSqPessoaFisica()->getNuCpf() : NULL);
                break;
            case \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica() :
                $nuCpfCnpjPassaporte = ($pessoa->getSqPessoaJuridica()->getNuCnpj() != NULL ?
                        $pessoa->getSqPessoaJuridica()->getNuCnpj() : NULL);
                break;
            case \Core_Configuration::getSgdoceTipoPessoaEstrangeiro() :
                $nuCpfCnpjPassaporte = ($pessoa->getSqPessoaDocumento() != NULL ?
                        $pessoa->getSqPessoaDocumento()->getTxValor() : NULL);
                break;
        }
        return $nuCpfCnpjPassaporte;
    }

    /**
     * Valida dados do documento pessoa
     * @param  $search
     * @return array
     */
    public function validaDados($search)
    {
        $result = $this->_em->getRepository('app:VwPEssoa')->getDocumento();

        $nuCpfCnpjPassaporte = str_replace('-', '', $search->getNuCPFInteressado());
        $nuCpfCnpjPassaporte = str_replace('/', '', $nuCpfCnpjPassaporte);
        $nuCpfCnpjPassaporte = str_replace('.', '', $nuCpfCnpjPassaporte);

        $select = $this->getSelect($search);

        $query = $this->_em
            ->createQueryBuilder()
            ->select($select)
            ->from('app:VwPessoa', 'p');

        if ($search->getSqPessoaCorporativo()) {
            $query->andWhere('p.sqPessoa = :sqPessoa')
                ->setParameter('sqPessoa', $search->getSqPessoaCorporativo());
        }

        switch ($search->getSqTipoPessoaInteressado()) {
            case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica() :
                $query->leftJoin('p.sqPessoaFisica', 'pf');
                if ($nuCpfCnpjPassaporte != '') {
                    $query->andWhere('pf.nuCpf = :nuCpf')
                        ->setParameter('nuCpf', $nuCpfCnpjPassaporte);
                }
                break;

            case \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica() :
                $query->leftJoin('p.sqPessoaJuridica', 'pj');
                if ($nuCpfCnpjPassaporte != '') {
                    $query->andWhere('pj.nuCnpj = :nuCnpj')
                        ->setParameter('nuCnpj', $nuCpfCnpjPassaporte);
                }
                break;

            case \Core_Configuration::getSgdoceTipoPessoaEstrangeiro() :
                $query->leftJoin('p.sqPessoaDocumento', 'd');
                if ($nuCpfCnpjPassaporte != '') {
                    $query->andWhere('d.sqAtributoTipoDocumento = :sqAtributoTipoDocumento')
                        ->setParameter('sqAtributoTipoDocumento', $result['sqAtributoTipoDocumento']);
                }
                break;
        }

        $out = array();
        $result = $query->getQuery()->execute();
        foreach ($result as $value) {
            $out[] = array('sqPessoa' => $value->getSqPessoa(),
                'noPessoa' => $value->getNoPessoa(),
                'nuCpfCnpjPassaporte' => $this->getCpfCnpjPassaporte($search, $value));
        }
        return $out;
    }

    /**
     * Método que obtén informarções das pessoas relacionadas ao artefato
     * @param \Core_Dto_Search $dto
     * @return json
     */
    public function findbyPessoa($dto)
    {
        return $this->_em->createQueryBuilder()
                ->select('p,a,pf,tp,tv')
                ->from($this->_enName, 'p')
                ->innerJoin('p.sqArtefato', 'a')
                ->innerJoin('p.sqPessoaFuncao', 'pf')
                ->innerJoin('p.sqTipoPessoa', 'tp')
                ->leftJoin('p.sqTratamentoVocativo', 'tv')
                ->andWhere('a.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $dto->getSqArtefato())
                ->getQuery()
                ->execute();
    }

    /**
     * Obtén dados da pessoa de origem de uma minuta
     * @param $dto
     * @return array
     */
    public function getPessoaArtefatoOrigem($dto)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('p.sqPessoa,
            p.noPessoa,
            p.nuTelefone,
            p.coCep,
            p.txEndereco,
            p.nuNumeroEndereco,
            p.noBairro,
            p.txComplemento,
            p.txEnderecoEletronico,
            p.noProfissao,
            p.noUnidadeOrg,
            pf.noPessoaFuncao,
            m.noMunicipio,
            e.sgEstado,
            t.noTratamento,
            c.sqCabecalho,
            c.deArquivoImagem,
            c.txCabecalho,
            td.noTipoDocumento,
            a.deImagemRodape,
            a.nuArtefato,
            a.txEmenta,
            a.dtArtefato,
            a.deImagemRodape,
            a.txReferencia,
            ass.txAssunto,
            a.txTextoArtefato,
            a.dtPrazo,
            a.inDiasCorridos,
            a.txDescricaoPrazo,
            a.nuDiasPrazo,
            a.noCargoInterno,
            f.noFecho,
            md.sqModeloDocumento,
            ptd.sqPosicaoTipoDocumento,
            pdt.sqPosicaoData
            ')
            ->from($this->_enName, 'p')
            ->innerJoin('p.sqPessoaFuncao', 'pf')
            ->leftJoin('p.sqTratamentoVocativo', 'tv')
            ->leftJoin('tv.sqTratamento', 't')
            ->innerJoin('p.sqArtefato', 'a')
            ->leftJoin('a.sqMunicipio', 'm')
            ->leftJoin('m.sqEstado', 'e')
            ->leftJoin('a.sqFecho', 'f')
            ->innerJoin('a.sqTipoArtefatoAssunto', 'taa')
            ->innerJoin('taa.sqAssunto', 'ass')
            ->innerJoin('a.sqModeloDocumento', 'md')
            ->leftJoin('md.sqCabecalho', 'c')
            ->leftJoin('md.sqPosicaoTipoDocumento', 'ptd')
            ->leftJoin('md.sqPosicaoData', 'pdt')
            ->innerJoin('md.sqTipoDocumento', 'td')
            ->andWhere('pf.sqPessoaFuncao = :sqPessoaFuncao')
            ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoOrigem())
            ->andWhere('p.sqArtefato = :sqArtefato')
            ->setParameter('sqArtefato', $dto->getSqArtefato()->getSqArtefato());

        if (empty($query)) {
            return NULL;
        }

        return $query->getQuery()->getSingleResult();
    }
}
