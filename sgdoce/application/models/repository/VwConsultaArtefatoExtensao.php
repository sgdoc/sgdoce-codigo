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
class VwConsultaArtefatoExtensao extends \Core_Model_Repository_Base
{
    public function getNuArtefato(\Doctrine\ORM\QueryBuilder &$queryBuilder, \Core_Dto_Search $dto)
    {
        if ($dto->getNuArtefato())
    	{
    		$nuArtefato = str_replace('.', '',str_replace('/', '',(str_replace('-', '', $dto->getNuArtefato()))));
    		$queryBuilder->andWhere('vwca.nuArtefato like :nuArtefato')
    		->setParameter('nuArtefato', '%'.$nuArtefato.'%');
    	}
    }

    public function getNuDigital(\Doctrine\ORM\QueryBuilder &$queryBuilder, \Core_Dto_Search $dto)
    {
    	if ($dto->getNuArtefato())
        {
        	$queryBuilder->andWhere('vwca.nuDigital like :nuDigital')
                         ->setParameter('nuDigital', '%'.strtoupper($dto->getNuArtefato()).'%');
        }
    }

    public function getNuArtefatoNuDigital(\Doctrine\ORM\QueryBuilder &$queryBuilder, \Core_Dto_Search $dto)
    {
    	if ($dto->getNuArtefato())
        {
        	$nuArtefato = str_replace(' ', '',str_replace('.', '',
        				  str_replace('/', '',(str_replace('-', '', $dto->getNuArtefato())))));
        	$queryBuilder->orWhere('vwca.nuDigital like :nuDigital')
                          ->setParameter('nuDigital', '%'.$dto->getNuArtefato().'%');
            $queryBuilder->orWhere('vwca.nuArtefato like :nuArtefato')
                         ->setParameter('nuArtefato', '%'.$nuArtefato.'%');
        }
    }

    public function listGridConsultaArtefatoAvancado($dto)
    {
        $queryAssuntoComplementar = mb_strtolower($dto->getTxAssuntoComplementar(), 'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder()
                ->select('vwca')
                ->from('app:VwConsultaArtefato', 'vwca')
                ->orderBy('vwca.sqArtefato');

        $this->whereListArtefato($queryBuilder, $dto, $queryAssuntoComplementar);

        return $queryBuilder;
    }

//Faz o tratamento se o tipo da pesquisa for PROCESSO
    public function filtraPesquisaProcesso(\Doctrine\ORM\QueryBuilder &$queryBuilder, \Core_Dto_Search $dto)
    {
        if ($dto->getDtArtefatoInicio() && $dto->getDtArtefatoFim())
        {
            $queryBuilder->andWhere('vwca.dtArtefato >= :inicial')
                    ->setParameter('inicial', $dto->getDtArtefatoInicio())
                    ->andWhere('vwca.dtPrazo <= :final')
                    ->setParameter('final', $dto->getDtArtefatoFim())
                    ->andWhere('vwca.sqTipoArtefato = 2');
        } else if ($dto->getDtArtefatoInicio()) {
            $queryBuilder->andWhere('vwca.dtArtefato >= :inicial')
                    ->setParameter('inicial', $dto->getDtArtefatoInicio())
                    ->andWhere('vwca.sqTipoArtefato = 2');
        } else if ($dto->getDtArtefatoFim()) {
            $queryBuilder->andWhere('vwca.dtArtefato <= :final')
                    ->setParameter('final', $dto->getDtArtefatoFim())
                    ->andWhere('vwca.sqTipoArtefato = 2');
        }
        return $queryBuilder;
    }

    public function whereData(\Doctrine\ORM\QueryBuilder &$queryBuilder, \Core_Dto_Search $dto)
    {
    }

    //Faz o tratamento se o tipo da pesquisa for DIGITAL
    public function filtraPesquisaDigital(\Doctrine\ORM\QueryBuilder &$queryBuilder, \Core_Dto_Search $dto)
    {
        if ($dto->getTipoPesquisa() == static::TIPO_PESQUISA_AVANCADA_DIGITAL)
        {
            if ($dto->getDtArtefatoInicio() && $dto->getDtArtefatoFim())
            {
                    $queryBuilder->andWhere('vwca.dtArtefato >= :inicial')
                        ->setParameter('inicial', $dto->getDtArtefatoInicio())
                        ->andWhere('vwca.dtPrazo <= :final')
                        ->setParameter('final', $dto->getDtArtefatoFim())
                        ->andWhere('vwca.sqTipoArtefato = 1')
                        ->orWhere('vwca.sqTipoArtefato = 3');
            } else if ($dto->getDtArtefatoInicio())
            {
                    $queryBuilder->andWhere('vwca.dtArtefato >= :inicial')
                        ->setParameter('inicial', $dto->getDtArtefatoInicio())
                        ->andWhere('vwca.sqTipoArtefato = 1')
                        ->orWhere('vwca.sqTipoArtefato = 3');
            } else if ($dto->getDtArtefatoFim())
            {
                    $queryBuilder->andWhere('vwca.dtArtefato <= :final')
                        ->setParameter('final', $dto->getDtArtefatoFim())
                        ->andWhere('vwca.sqTipoArtefato = 1')
                        ->orWhere('vwca.sqTipoArtefato = 3');
            }
        }

        return $queryBuilder;
    }

    //Faz o tratamento se o tipo de informação for DESPACHO
    //OBS: COLOCAR DT_DESPACHO NO LUGAR DE DT_ARTEFATO
    public function filtraTipoInformacaoDespacho(\Doctrine\ORM\QueryBuilder &$queryBuilder, \Core_Dto_Search $dto)
    {
        if ($dto->getDtArtefatoInicio() && $dto->getDtArtefatoFim()) {
            $queryBuilder->andWhere('vwca.dtArtefato BETWEEN :inicial AND :final')
                    ->setParameter('inicial', $dto->getDtArtefatoInicio())
                    ->setParameter('final', $dto->getDtArtefatoFim());
        } else if ($dto->getDtArtefatoInicio()) {
            $queryBuilder->andWhere('vwca.dtArtefato >= inicial')
                    ->setParameter('inicial', $dto->getDtArtefatoInicio());
        } else if ($dto->getDtArtefatoFim()) {
            $queryBuilder->andWhere('vwca.dtArtefato >= final')
                    ->setParameter('final', $dto->getDtArtefatoFim());
        }
        return $queryBuilder;
    }

    //Faz o tratamento se o tipo de informação for COMENTARIO
    //OBS: COLOCAR DT_COMENTARIO NO LUGAR DE DT_ARTEFATO
    public function filtraTipoInformacaoComentario(\Doctrine\ORM\QueryBuilder &$queryBuilder, \Core_Dto_Search $dto)
    {
        if ($dto->getDtArtefatoInicio() && $dto->getDtArtefatoFim()) {
            $queryBuilder->andWhere('vwca.dtArtefato BETWEEN :inicial AND :final')
                    ->setParameter('inicial', $dto->getDtArtefatoInicio())
                    ->setParameter('final', $dto->getDtArtefatoFim());
        } else if ($dto->getDtArtefatoInicio()) {
            $queryBuilder->andWhere('vwca.dtArtefato >= inicial')
                    ->setParameter('inicial', $dto->getDtArtefatoInicio());
        } else if ($dto->getDtArtefatoFim()) {
            $queryBuilder->andWhere('vwca.dtArtefato >= final')
                    ->setParameter('final', $dto->getDtArtefatoFim());
        }
        return $queryBuilder;
    }
}