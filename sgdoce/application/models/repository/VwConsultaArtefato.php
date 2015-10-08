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
 * Classe para Repository de VwConsultaArtefato
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwConsultaArtefato
 * @version      1.0.0
 * @since        2013-06-07
 */
class VwConsultaArtefato extends VwConsultaArtefatoExtensao
{

    /**
     * Constante para receber o valor do tipo de pesquisa
     * @var    integer
     * @name   DIGITAL
     */
    const TIPO_PESQUISA_AVANCADA_DIGITAL = 1;

    /**
     * Constante para receber o valor do tipo de pesquisa
     * @var    integer
     * @name   PROCESSO
     */
    const TIPO_PESQUISA_AVANCADA_PROCESSO = 2;

    /**
     * Constante para receber o valor do tipo de informação
     * @var    integer
     * @name   COMENTARIO
     */
    const TIPO_INFORMACAO_AVANCADA_COMENTARIO = 1;

    /**
     * Constante para receber o valor do tipo de informação
     * @var    integer
     * @name   DESPACHO
     */
    const TIPO_INFORMACAO_AVANCADA_DESPACHO = 2;

    /**
     * Constante para receber o valor do tipo de informação
     * @var    integer
     * @name   OUTROS
     */
    const TIPO_INFORMACAO_AVANCADA_OUTROS = 3;

    /**
     * Consulta artefato padrão
     * @param \Core_Dto_Entity $dto
     * @return query DQL
     */
    public function listGridConsultaArtefatoPadrao ($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
                ->select('vwca')
                ->from('app:VwConsultaArtefato', 'vwca')
                ->orderBy('vwca.sqArtefato');

        //Filtro pelo tipo digital ou do processo
        if ($dto->getTipoPesquisa() == self::TIPO_PESQUISA_AVANCADA_PROCESSO) {
            $queryBuilder->andWhere('vwca.sqTipoArtefato = :tipoPesquisa')
                    ->setParameter('tipoPesquisa', $dto->getTipoPesquisa())
                    ->andWhere('vwca.sqTipoArtefato = 2');
        } else if ($dto->getTipoPesquisa() == self::TIPO_PESQUISA_AVANCADA_DIGITAL) {
            $queryBuilder->andWhere('vwca.sqTipoArtefato = :tipoPesquisa')
                    ->setParameter('tipoPesquisa', $dto->getTipoPesquisa())
                    ->andWhere('vwca.sqTipoArtefato = 1')
                    ->orWhere('vwca.sqTipoArtefato = 3');
        }

        //Filtro por número da digital ou do processo
        if ($dto->getTipoPesquisa() == self::TIPO_PESQUISA_AVANCADA_DIGITAL) {
            $this->getNuDigital($queryBuilder, $dto);
        } else if ($dto->getTipoPesquisa() == self::TIPO_PESQUISA_AVANCADA_PROCESSO) {
            $this->getNuArtefato($queryBuilder, $dto);
        } else {
            $this->getNuArtefatoNuDigital($queryBuilder, $dto);
        }

        //Filtro por Interessado
        if ($dto->getSqPessoaSgdoce()) {
            $queryBuilder->andWhere('vwca.sqPessoaInteressada like :sqPessoaSgdoce')
                    ->setParameter('sqPessoaSgdoce', '%' . $dto->getSqPessoaSgdoce() . '%');
        }

        //Filtro por Cpf, Cnpj e Passaport
        $filter = new \Zend_Filter_Digits();
        $nuCpfCnpjPassaporte = '';
        if ($dto->getNuCpfCnpjPassaporte()) {
            $nuCpfCnpjPassaporte = $filter->filter($dto->getNuCpfCnpjPassaporte());
            $queryBuilder->andWhere('vwca.nuCpfCnpjPassaporteOrigem = :nuCpfCnpjPassaporte')
                    ->setParameter('nuCpfCnpjPassaporte', $nuCpfCnpjPassaporte);
        }

        return $queryBuilder;
    }

    public function whereListArtefato (\Doctrine\ORM\QueryBuilder $queryBuilder, \Core_Dto_Search $dto, $queryAssuntoComplementar)
    {
        switch ($dto->getTipoPesquisa()) {
            case self::TIPO_PESQUISA_AVANCADA_DIGITAL:
                $this->appendPesquisaAvancadaDigital($queryBuilder, $dto, $queryAssuntoComplementar);

                break;

            case self::TIPO_PESQUISA_AVANCADA_PROCESSO:
                $this->appendPesquisaAvancadaProcesso($queryBuilder, $dto, $queryAssuntoComplementar);

                break;

            default:
                $this->getNuArtefatoNuDigital($queryBuilder, $dto);

                break;
        }

        $this->appendTipoInformacao($queryBuilder, $dto);

        $this->appendQuery($queryBuilder, $dto);
    }

    private function appendTipoInformacao ($queryBuilder, $dto)
    {
        if ($dto->getDataCombo()) {
            switch ($dto->getTipoInformacao()) {
                case self::TIPO_INFORMACAO_AVANCADA_COMENTARIO:
                    $this->filtraTipoInformacaoComentario($queryBuilder, $dto);

                    break;

                case self::TIPO_INFORMACAO_AVANCADA_DESPACHO:
                    $this->filtraTipoInformacaoDespacho($queryBuilder, $dto);

                    break;

                default :
                    break;
            }
        }
    }

    private function appendPesquisaAvancadaDigital ($queryBuilder, $dto, $queryAssuntoComplementar)
    {
        if (!$dto->getTxTituloDossie()) {
            $queryBuilder->andWhere('vwca.sqTipoArtefato = :tipoPesquisa')
                    ->setParameter('tipoPesquisa', $dto->getTipoPesquisa())
                    ->andWhere('vwca.sqTipoArtefato = 1')
                    ->orWhere('vwca.sqTipoArtefato = 3');
        }

        $this->getNuDigital($queryBuilder, $dto);

        if ($dto->getSqTipoDocumento()) {
            $queryBuilder->andWhere('vwca.sqTipoDocumento = :sqTipoDocumento')
                    ->setParameter('sqTipoDocumento', $dto->getSqTipoDocumento());
        }

        if ($dto->getTxTituloDossie()) {
            $queryBuilder->andWhere('vwca.sqArtefato = :sqArtefato')
                    ->setParameter('sqArtefato', $dto->getTxTituloDossie());
        }

        if ($dto->getDataCombo()) {
            $this->filtraPesquisaDigital($queryBuilder, $dto);
        }

        $this->appendPesquisaAvancadaDigital2($queryBuilder, $dto);
    }

    private function appendPesquisaAvancadaDigital2 ($queryBuilder, $dto)
    {
        if ($dto->getNuTipoDossie()) {
            $queryBuilder->andWhere('vwca.nuArtefato like :nuArtefato')
                    ->setParameter('nuArtefato', '%' . $dto->getNuTipoDossie() . '%');
        }

        if ($dto->getTxAssunto()) {
            $queryBuilder->andWhere('vwca.sqAssunto = :sqAssunto')
                    ->setParameter('sqAssunto', $dto->getTxAssunto())
                    ->andWhere('vwca.sqTipoArtefato = 1')
                    ->orWhere('vwca.sqTipoArtefato = 3');
        }

        if ($dto->getTxAssuntoComplementar()) {
            $queryBuilder->andWhere('LOWER(vwca.txAssuntoComplementar) like :txAssuntoComplementar')
                    ->setParameter('txAssuntoComplementar', '%' . $queryAssuntoComplementar . '%')
                    ->andWhere('vwca.sqTipoArtefato = 1')
                    ->orWhere('vwca.sqTipoArtefato = 3');
        }

        if ($dto->getTxReferencia()) {
            $queryBuilder->andWhere('vwca.sqArtefato = :sqArtefato ')
                    ->setParameter('sqArtefato', $dto->getTxReferencia())
                    ->andWhere('vwca.sqTipoArtefato = 1')
                    ->orWhere('vwca.sqTipoArtefato = 3');
        }
    }

    private function appendPesquisaAvancadaProcesso ($queryBuilder, $dto, $queryAssuntoComplementar)
    {
        if (!$dto->getTxTituloDossie()) {
            $queryBuilder->andWhere('vwca.sqTipoArtefato = :tipoPesquisa')
                    ->setParameter('tipoPesquisa', $dto->getTipoPesquisa())
                    ->andWhere('vwca.sqTipoArtefato = 2');
        }

        if ($dto->getDataCombo()) {
            $this->filtraPesquisaProcesso($queryBuilder, $dto);
        }

        $this->getNuArtefato($queryBuilder, $dto);

        if ($dto->getDataCombo()) {
            $this->filtraPesquisaProcesso($queryBuilder, $dto);
        }

        if ($dto->getTxAssunto()) {
            $queryBuilder->andWhere('vwca.sqAssunto = :sqAssunto')
                    ->setParameter('sqAssunto', $dto->getTxAssunto())
                    ->andWhere('vwca.sqTipoArtefato = 2');
        }

        if ($dto->getTxAssuntoComplementar()) {
            $queryBuilder->andWhere('LOWER(vwca.txAssuntoComplementar) like :txAssuntoComplementar')
                    ->setParameter('txAssuntoComplementar', '%' . $queryAssuntoComplementar . '%')
                    ->andWhere('vwca.sqTipoArtefato = 2');
        }

        if ($dto->getTxAssuntoComplementar()) {
            $queryBuilder->andWhere('vwca.sqArtefato = :sqArtefato ')
                    ->setParameter('sqArtefato', $dto->getTxReferencia())
                    ->andWhere('vwca.sqTipoArtefato = 2');
        }
    }

    private function appendQuery (\Doctrine\ORM\QueryBuilder $queryBuilder, \Core_Dto_Search $dto)
    {
        //Filtro por Interessado
        if ($dto->getSqPessoaSgdoce()) {
            $queryBuilder->andWhere('vwca.sqPessoaInteressada like :sqPessoaSgdoce')
                    ->setParameter('sqPessoaSgdoce', '%' . $dto->getSqPessoaSgdoce() . '%');
        }

        //Filtro por Cpf, Cnpj e Passaport
        if ($dto->getNuCpfCnpjPassaporte()) {
            $filter = new \Zend_Filter_Digits();

            $queryBuilder->andWhere('vwca.nuCpfCnpjPassaporteOrigem = :nuCpfCnpjPassaporte')
                    ->setParameter('nuCpfCnpjPassaporte', $filter->filter($dto->getNuCpfCnpjPassaporte()));
        }

        //Filtro pelo tipo de documento
        if ($dto->getSqTipoDocumento()) {
            $queryBuilder->andWhere('vwca.sqTipoDocumento = :sqTipoDocumento')
                    ->setParameter('sqTipoDocumento', $dto->getSqTipoDocumento());
        }

        if ($dto->getSqPessoaFuncao()) {
            $queryBuilder->andWhere('vwca.sqPessoaSgdoceOrigem = :sqPessoaSgdoceOrigem ')
                    ->setParameter('sqPessoaSgdoceOrigem', $dto->getSqPessoaFuncao());
        }

        $this->appendQuery2($queryBuilder, $dto);
    }

    private function appendQuery2 ($queryBuilder, $dto)
    {
        //Faz busca pelo campo Unidade Afetada.
        if ($dto->getSqNomeUnidade()) {
            $queryBuilder->andWhere('vwca.unidadeOrg like :unidadeOrg')
                    ->setParameter('unidadeOrg', '%' . $dto->getSqNomeUnidade() . '%');
        }

        //Faz busca pelo campo empreendimento
        if ($dto->getSqNomeEmpreendimento()) {
            $queryBuilder->andWhere('vwca.empreendimento like :empreendimento')
                    ->setParameter('empreendimento', '%' . $dto->getSqNomeEmpreendimento() . '%');
        }

        //Faz busca pelo campo expecie
        if ($dto->getSqNomeEspecie()) {
            $queryBuilder->andWhere('vwca.taxon like :taxon')
                    ->setParameter('taxon', '%' . $dto->getSqNomeEspecie() . '%');
        }

        if ($dto->getSqNomeCaverna()) {
            $queryBuilder->andWhere('vwca.caverna like :caverna')
                    ->setParameter('caverna', '%' . $dto->getSqNomeCaverna() . '%');
        }
    }

}
