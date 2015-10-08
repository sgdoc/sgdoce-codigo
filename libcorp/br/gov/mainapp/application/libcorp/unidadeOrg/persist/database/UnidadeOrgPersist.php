<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */
namespace br\gov\mainapp\application\libcorp\unidadeOrg\persist\database;
use br\gov\sial\core\persist\exception\PersistException,
    br\gov\mainapp\library\persist\database\Persist as ParentPersist,
    br\gov\mainapp\application\libcorp\pessoa\valueObject\PessoaValueObject,
    br\gov\mainapp\application\libcorp\unidadeOrg\valueObject\UnidadeOrgValueObject,

    # @todo revisar imports antigos
    br\gov\mainapp\application\libcorp\estado\valueObject\EstadoValueObject,
    br\gov\mainapp\application\libcorp\municipio\valueObject\MunicipioValueObject,
    br\gov\mainapp\application\libcorp\pessoaJuridica\valueObject\PessoaJuridicaValueObject,
    br\gov\mainapp\application\libcorp\tipoUnidadeOrg\valueObject\TipoUnidadeOrgValueObject,
    br\gov\mainapp\application\libcorp\unidOrgMunicipio\valueObject\UnidOrgMunicipioValueObject,
    br\gov\mainapp\application\libcorp\vwTipoUnidadeOrgHierarq\valueObject\VwTipoUnidadeOrgHierarqValueObject;

/**
  * SISICMBio
  *
  * @name UnidadeOrgPersist
  * @package br.gov.icmbio.sisicmbio.application.libcorp.unidadeOrg.persist
  * @subpackage database
  * @author Fabio Lima <fabioolima@gmail.com>
  * @since 2012-04-17
  * @version $Id$
  * */
class UnidadeOrgPersist extends ParentPersist
{
/**
     * Parametro para efetuar busca por Unidades Descentralizadas
     * @var string
     */
    const PAR_UNIDADE_DESCENTRALIZADA = 'ud--';

    /**
     * Efetua a busca de Unidades Organizacional por Estado
     * @param EstadoValueObject $voEstado
     * @throws PersistException
     */
    public function findByUf (EstadoValueObject $voEstado)
    {
        try {
            $query = 'SELECT
                        uo.sq_pessoa,
                        uo.sq_unidade_superior,
                        uo.sq_unidade_adm_pai,
                        uo.sq_unidade_fin_pai,
                        uo.sq_tipo_unidade,
                        uo.co_uorg,
                        uo.co_unidade_gestora,
                        uo.sg_unidade_org,
                        uo.st_ativo,
                        uo.nu_latitude,
                        uo.nu_longitude,
                        uo.in_unidade_financeira
                    FROM corporativo.vw_unidade_org uo
                    JOIN corporativo.estado e 
                            ON st_intersects(
                                    ST_CollectionExtract(uo.the_geom, 3), ST_CollectionExtract(e.the_geom, 3)
                            )
                    WHERE e.sq_estado = :sqEstado';

            $params['sqEstado']        = new \stdClass();
            $params['sqEstado']->type  = 'integer';
            $params['sqEstado']->value = $voEstado->getSqEstado();

            # executa query
            return $this->execute($query, $params);
        } catch (IllegalArgumentException $iae) {
            throw new PersistException(
                'Um ou mais paramentros informados para na montagem da query foi avaliado como inválido', 0, $iae
            );
        } catch (\Exception $exp) {
            throw new PersistException(
                'Um erro inesperado ocorreu ao tentar executar a recuperação dos dados', 0, $exp
            );
        }
    }


    public function findSedeUorgByUf ($sqEstado)
    {
        $query = 'SELECT vw_unidade_org.sq_pessoa,
                         vw_unidade_org.no_pessoa
                    FROM corporativo.vw_unidade_org
              INNER JOIN corporativo.vw_endereco  ON vw_unidade_org.sq_pessoa = vw_endereco.sq_pessoa
              INNER JOIN corporativo.vw_municipio ON vw_endereco.sq_municipio = vw_municipio.sq_municipio
                   WHERE vw_municipio.sq_estado = :sqEstado
                ORDER BY vw_unidade_org.no_pessoa';

        $params['sqEstado']        = new \stdClass();
        $params['sqEstado']->type  = 'integer';
        $params['sqEstado']->value = $sqUf;

        return $this->execute($query, $params);
    }

    /**
     * Efetua a busca por UnidadeDescentralizada e por Nome
     * Solicitacao via Ticket #459
     * @param PessoaValueObject $voPessoa
     * @throws PersistException
     */
    public function findundDescentralizadaByNome (PessoaValueObject $voPessoa)
    {
        try {
            /*
             * SELECT [fields]
             * FROM tipo_unidade_org_hierarq vwTipo
             * JOIN unidade_org uniOrg ON (vwTipo.sq_tipo_unidade_org = uniOrg.sq_tipo_unidade)
             * INNER JOIN pessoa pes ON (uniOrg.sq_pessoa = pes.sq_pessoa)
             * WHERE trilha_sigla ilike '%ud--%' AND pes.no_pessoa ilike '%Parque%'
             */

            # obtem a entidade com base na anotacao
           $eUnidadeOrg    = $this->getEntity(array('unOrg'    => $this->annotation()->load()->class));

            # Busca pela View
           $eVwTipoUnidade = $this->getEntity(
                                array('vwTipoUnidadeHierarq' => VwTipoUnidadeOrgHierarqValueObject::factory())
                              );

            $ePessoa        = $this->getEntity(array('pess'     => PessoaValueObject::factory()));

            $query          = $this->getQuery($eVwTipoUnidade)

            # efetua join da View com UnidadeOrg
           ->join($eUnidadeOrg, $eVwTipoUnidade->column('sqTipoUnidadeOrg')
                                                ->equals($eUnidadeOrg->column('sqTipoUnidade'))
                  )

            # inner Join
           ->join($ePessoa, $eUnidadeOrg->column('sqPessoa')->equals($ePessoa->column('sqPessoa')))

            # aplica filtro
           ->where($eVwTipoUnidade->column('trilhaSigla')->ilike('%' . self::PAR_UNIDADE_DESCENTRALIZADA . '%'))
            ->and($ePessoa->column('noPessoa')->ilike('%' . $voPessoa->getNoPessoa() . '%'));

            # executa query
           return $this->execute($query);

        } catch (IllegalArgumentException $iae) {
            throw new PersistException(
                'Um ou mais paramentros informados para na montagem da query foi avaliado como inválido', 0, $iae
            );
        } catch (\Exception $exp) {
            throw new PersistException(
                'Um erro inesperado ocorreu ao tentar executar a recuperação dos dados', 0, $exp
            );
        }
    }

    /**
     * Efetua a busca por UnidadeDescentralizada e por Categoria
     * Solicitacao via Ticket #459
     * @param TipoUnidadeOrgValueObject $voTipoUnidade
     * @throws PersistException
     */
    public function findUndDescentralizadaByCategoria (TipoUnidadeOrgValueObject $voTipoUnidade)
    {
        try {
            /*
             * SELECT [fields]
             * FROM tipo_unidade_org_hierarq vwTipo
             * JOIN unidade_org uniOrg ON (vwTipo.sq_tipo_unidade_org = uniOrg.sq_tipo_unidade)
             * INNER JOIN tipo_unidade_org tipoUnd ON (tipoUnd.sq_tipo_unidade_org = vwTipo.sq_tipo_unidade_org)
             * WHERE trilha_sigla ilike '%ud--%' AND tipoUnd.no_tipo_unidade_org ilike '%Esta%'
             */

            # obtem a entidade com base na anotacao
           $eUnidadeOrg    = $this->getEntity(array('unOrg'   => $this->annotation()->load()->class));

            # Busca pela View
           $eVwTipoUnidade = $this->getEntity(
                                array('vwTipoUnidadeHierarq'   => VwTipoUnidadeOrgHierarqValueObject::factory())
                              );

            $eTipoUnidade   = $this->getEntity(array('tipoUni' => TipoUnidadeOrgValueObject::factory()));

            $query          = $this->getQuery($eVwTipoUnidade)

            # efetua join da View com UnidadeOrg
           ->join($eUnidadeOrg, $eVwTipoUnidade->column('sqTipoUnidadeOrg')
                                                ->equals($eUnidadeOrg->column('sqTipoUnidade'))
                  )

            # inner Join
           ->join($eTipoUnidade, $eUnidadeOrg->column('sqTipoUnidade')
                                              ->equals($eTipoUnidade->column('sqTipoUnidadeOrg'))
                  )

            # aplica filtro
           ->where($eVwTipoUnidade->column('trilhaSigla')->ilike('%' . self::PAR_UNIDADE_DESCENTRALIZADA . '%'))
             ->and($eTipoUnidade->column('noTipoUnidadeOrg')->ilike('%' . $voTipoUnidade->getNoTipoUnidadeOrg() . '%'));

            # executa query
           return $this->execute($query);

        } catch (IllegalArgumentException $iae) {
            throw new PersistException(
                'Um ou mais paramentros informados para na montagem da query foi avaliado como inválido', 0, $iae
            );
        } catch (\Exception $exp) {
            throw new PersistException(
                'Um erro inesperado ocorreu ao tentar executar a recuperação dos dados', 0, $exp
            );
        }
    }

    /**
     * Efetua a busca por UnidadeDescentralizada e por Cidade
     * Solicitacao via Ticket #459
     * @param MunicipioValueObject $voMunicipio
     * @throws PersistException
     */
    public function findUndDescentralizadaByCidade (MunicipioValueObject $voMunicipio)
    {
        try {

            $query = sprintf("SELECT *
                      FROM corporativo.tipo_unidade_org_hierarq vwTipo
                      JOIN corporativo.unidade_org uniOrg ON (vwTipo.sq_tipo_unidade_org = uniOrg.sq_tipo_unidade)
                      JOIN corporativo.municipio mun ON public.ST_CollectionExtract(uniOrg.the_geom, 3) && mun.the_geom
                                        AND public.ST_INTERSECTS(public.ST_CollectionExtract(uniOrg.the_geom, 3), mun.the_geom)
                      WHERE trilha_sigla ilike '%%%s%%' and mun.no_municipio ilike :noMunicipio;
                     ",
                      self::PAR_UNIDADE_DESCENTRALIZADA);

            $params['noMunicipio']        = new \stdClass();
            $params['noMunicipio']->type  = 'string';
            $params['noMunicipio']->value = "%{$voMunicipio->getNoMunicipio()}%";

            # executa query
           return $this->getConnect()
                        ->prepare($query, $params)
                        ->retrieve();

        } catch (IllegalArgumentException $iae) {
            throw new PersistException(
                'Um ou mais paramentros informados para na montagem da query foi avaliado como inválido', 0, $iae
            );
        } catch (\Exception $exp) {
            throw new PersistException(
                'Um erro inesperado ocorreu ao tentar executar a recuperação dos dados', 0, $exp
            );
        }
    }

    /**
     * @param UnidadeOrgValueObject $voUnidOrg
     * @param integer $limit
     * @param integer $offset
     * @return UnidadeOrgModel
     * */
    public function findByParamFilterByName (UnidadeOrgValueObject $voUnidOrg, $limit = 10, $offset = 0)
    {
        # cria entidade de banco do VO informado
         $eUnidadeOrg    = $this->getEntity($voUnidOrg);

         # cria consulta
         $query = $this->getQuery($eUnidadeOrg)
                       ->where($eUnidadeOrg->column('sgUnidadeOrg')->ilike('%' . $voUnidOrg->getSgUnidadeOrg() . '%'))
                       ->limit($limit, $offset);

        try {
            return $this->execute($query);
        } catch (PersistException $pExc) {
            # efetua log de erro
           ;
            throw new ModelException(self::REQUIRE_DATA_ERROR_MESSAGE);
        }
    }
    
    /**
     * @param PessoaValueObject $filterPessoa
     * @param integer $limit
     * @return br\gov\sial\core\persist\ResultSet
     * @throws PersistException
     */
    public function findUnidadeConservacao (PessoaValueObject $filterPessoa, $limit = NULL)
    {
        try {
            $sql = "select
                        p.*
                    from
                        corporativo.unidade_org uo
                    inner join
                        corporativo.pessoa p on p.sq_pessoa = uo.sq_pessoa 
                    inner join
                        corporativo.tipo_unidade_org_hierarq tp on 
                            uo.sq_tipo_unidade = tp.sq_tipo_unidade_org and 
                            tp.trilha_sigla ilike '%-->uc-->%' ";

            $this->_query = $sql;

            $ePessoa = $this->getEntity(array('p' => $filterPessoa));

            $condicional = array(
                array('entity' => $ePessoa,
                      'field' => 'noPessoa',
                      'value' => '%' . $filterPessoa->getNoPessoa())
            );

            $this->buildWhere($condicional);

            if ($limit) {
                $this->_query .= " limit $limit";
            }
            
            return $this->execute($this->_query, $this->_params);

        } catch (\Exception $exp) {
            throw new PersistException($exp->getMessage(), $exp->getCode(), $exp);
        }
    }    
}