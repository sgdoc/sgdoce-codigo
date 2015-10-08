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

/**
 * SISICMBio
 *
 * Classe para Repository de ArtefatoMinuta
 *
 * @package      Model
 * @subpackage   Repository
 * @name         ArtefatoMinuta
 * @version      1.0.0
 * @since        2012-11-20
 */
class ArtefatoMinuta extends \Core_Model_Repository_Base
{
    public function getPessoaOrigemArtefato($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
          ->select('ptd.sqPosicaoTipoDocumento,
                    pd.sqPosicaoData,
                    c.sqCabecalho,
                    c.deArquivoImagem,
                    c.txCabecalho,
                    ens.txEndereco,
                    ens.nuEndereco,
                    m.noMunicipio,
                    am.txEmenta,
                    ens.coCep,
                    ts.nuTelefone,
                    ts.nuDdd,
                    ems.txEmail,
                    td.noTipoDocumento,
                    a.nuArtefato,
                    a.nuDigital,
                    a.dtArtefato,
                    a.noCargoInterno,
                    ps.noPessoa,
                    ass.txAssunto,
                    am.txReferencia,
                    am.txTextoArtefato,
                    a.txDescricaoPrazo,
                    a.inDiasCorridos,
                    a.nuDiasPrazo,
                    a.txAssuntoComplementar,
                    f.noFecho,
                    a.sqArtefato,
                    a.dtPrazo')
        ->from('app:ArtefatoMinuta', 'am')
        ->innerJoin('am.sqArtefato', 'a')
        ->innerJoin('a.sqPessoaArtefato', 'pa')
        ->innerJoin('pa.sqPessoaFuncao', 'pf')
        ->innerJoin('pa.sqPessoaSgdoce', 'ps')
        ->leftJoin('pa.sqEnderecoSgdoce', 'ens')
        ->leftJoin('pa.sqEmailSgdoce', 'ems')
        ->leftJoin('pa.sqTelefoneSgdoce', 'ts')
        ->leftJoin('am.sqMunicipio', 'm')
        ->innerJoin('am.sqModeloDocumento', 'md')
        ->innerJoin('md.sqPosicaoData', 'pd')
        ->leftJoin('md.sqCabecalho', 'c')
        ->leftJoin('md.sqPosicaoTipoDocumento', 'ptd')
        ->leftJoin('md.sqPosicaoData', 'pdt')
        ->innerJoin('md.sqTipoDocumento', 'td')
        ->leftJoin('md.sqAssunto', 'ass')
        ->leftJoin('a.sqFecho', 'f')
        ->andWhere('a.sqArtefato = :sqArtefato')
        ->setParameter('sqArtefato', $dto->getSqArtefato())
        ->andWhere('pf.sqPessoaFuncao = :sqPessoaFuncao')
        ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoOrigem());

        $resultQuery = $queryBuilder->getQuery()->execute();

        if(count($resultQuery) > 0){
            return $resultQuery[0];
        }

        return FALSE;
    }

    public function getPessoaDestinatarioArtefato($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
          ->select('t.noTratamento,
                    pa.txPosTratamento,
                    puo.noUnidadeOrg,
                    ps.noPessoa,
                    vtuo.noTipoUnidadeOrg,
                    vtuo2.noTipoUnidadeOrg noTipoUnidadeOrg2,
                    puo.noCargo,
                    m.noMunicipio,
                    vest.noEstado,
                    es.coCep,
                    pa.txPosVocativo,
                    pc.sqTipoPessoa,
                    voc.noVocativo')
        ->from('app:ArtefatoMinuta', 'am')
        ->innerJoin('am.sqArtefato', 'a')
        ->innerJoin('a.sqPessoaArtefato', 'pa')
        ->innerJoin('pa.sqPessoaFuncao', 'pf')
        ->innerJoin('pa.sqPessoaSgdoce', 'ps')
        ->leftJoin('ps.sqPessoaCorporativo', 'pc')
        ->leftJoin('pa.sqPessoaUnidadeOrg', 'puo')
        ->leftJoin('ps.sqVwPessoaUnidadeOrg', 'vuo')
        ->leftJoin('vuo.sqTipoUnidade', 'vtuo2')
        ->leftJoin('puo.sqPessoaUnidadeOrgCorp', 'vpuo')
        ->leftJoin('vpuo.sqTipoUnidade', 'vtuo')
        ->leftJoin('pa.sqEnderecoSgdoce', 'es')
        ->leftJoin('es.sqMunicipio', 'm')
        ->leftJoin('m.sqEstado', 'vest')
        ->leftJoin('pa.sqTratamentoVocativo', 'tv')
        ->leftJoin('tv.sqTratamento', 't')
        ->leftJoin('tv.sqVocativo', 'voc')
        ->andWhere('a.sqArtefato = :sqArtefato')
        ->setParameter('sqArtefato', $dto->getSqArtefato())
        ->andWhere('pf.sqPessoaFuncao = :sqPessoaFuncao')
        ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoDestinatario());

        $res = $queryBuilder->getQuery()->getScalarResult();

        $res['qtdDestinatario'] = count($res);

        return $res;
    }

    public function getPessoaAssinaturaArtefato($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
            ->select('a.sqArtefato,
                      puo.noCargo,
                      ta.sqTipoAssinante,
                      puo.noUnidadeOrg,
                      ps.sqPessoaSgdoce,
                      ps.noPessoa')
            ->from('app:ArtefatoMinuta', 'am')
            ->innerJoin('am.sqArtefato', 'a')
            ->leftJoin('a.sqPessoaAssinanteArtefato', 'paa')
            ->leftJoin('paa.sqTipoAssinante', 'ta')
            ->leftJoin('paa.sqPessoaUnidadeOrg', 'puo')
            ->innerJoin('puo.sqPessoaSgdoce', 'ps')
            ->andWhere('a.sqArtefato = :sqArtefato')
            ->setParameter('sqArtefato', $dto->getSqArtefato());

        $queryBuilder2 = $queryBuilder;
        $resQuery = $queryBuilder2->getQuery()->execute();

        $motivacaoQuery = array();
        if (empty($resQuery[0]['sqTipoAssinante'])) {

        	$queryBuilderMotivacao = $this->_em->createQueryBuilder()
        	->select('tmot.noTipoMotivacao,
                      tmot.sqTipoMotivacao,
                      mot.deMotivacao,
        			  puo.sqPessoaUnidadeOrg,
        			  ps.sqPessoaSgdoce,
        			  a.sqArtefato')
        	 ->from('app:Motivacao', 'mot')
        	 ->innerJoin('mot.sqArtefato', 'a')
        	 ->innerJoin('mot.sqTipoMotivacao', 'tmot')
        	 ->innerJoin('mot.sqPessoaUnidadeOrg', 'puo')
        	 ->innerJoin('puo.sqPessoaSgdoce', 'ps')
        	 ->andWhere('a.sqArtefato = :sqArtefato')
        	 ->setParameter('sqArtefato', $dto->getSqArtefato());

        	 $motivacaoQuery = $queryBuilderMotivacao->getQuery()->execute();
        }
        else {
            $resQuery = $queryBuilder->getQuery()->execute();
        }
        foreach ($resQuery as $keyPrincipal => $queryPrincipal) {
        	foreach ($motivacaoQuery as $key => $queryMotivacao) {
        		if($queryPrincipal['sqPessoaSgdoce'] == $queryMotivacao['sqPessoaSgdoce']){
        			$resQuery[$keyPrincipal]['noTipoMotivacao'] = $queryMotivacao['noTipoMotivacao'];
        			$resQuery[$keyPrincipal]['sqTipoMotivacao'] = $queryMotivacao['sqTipoMotivacao'];
        			$resQuery[$keyPrincipal]['deMotivacao'] = $queryMotivacao['deMotivacao'];
        		}
        	}
        }
        return $resQuery;

    }

    /**
     * Retorna subquery que verifica se a pessoa que esta logada está na assinatura da minuta ou não
     * @param integer $sqUsuario
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function subQueryAssinaturaArtefato($sqUsuario)
    {
        $subQueryInAss = $this->_em->createQueryBuilder()
            ->select('svwps.sqPessoa')
            ->from('app:Pessoa', 'sps')
            ->leftJoin('sps.sqPessoaCorporativo', 'svwps')
            ->innerJoin('sps.sqPessoaFuncao', 'svwpf')
            ->andWhere('sps.sqArtefato = vcm.sqArtefato')
            ->andWhere('svwps.sqPessoa = :sqPessoa')
            ->setParameter('sqPessoa', $sqUsuario)
            ->andWhere('sps.sqPessoaFuncao = ' .
                \Core_Configuration::getSgdocePessoaFuncaoAssinatura())
            ->getQuery()
            ->getDQL();

        return $subQueryInAss;
    }

    /**
     * método que pesquisa Titulo do Dossie para preencher autocomplete
     */
    public function searchReferencia($dto)
    {
    	$query = mb_strtolower($dto->getQuery(), 'UTF-8');
    	$queryBuilder = $this->_em->createQueryBuilder()
          ->select(
                        'a.sqArtefato', 'am.txReferencia'
                )
                ->from('app:ArtefatoMinuta', 'am')
                ->innerJoin('am.sqArtefato', 'a')
                ->andWhere('LOWER(am.txReferencia) like :txReferencia')
                ->setParameter('txReferencia', '%' . $query . '%')
                ->orderBy('am.txReferencia');

    	$res = $queryBuilder->getQuery()->execute();
    	$out = array();
    	foreach ($res as $item){

    		$out[$item['sqArtefato']] = $item['txReferencia'];
    	}
    	return $out;
    }
}