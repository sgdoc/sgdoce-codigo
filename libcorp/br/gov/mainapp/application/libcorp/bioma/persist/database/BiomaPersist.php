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
namespace br\gov\mainapp\application\libcorp\bioma\persist\database;
use br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\persist\database\Persist as ParentPersist,
    br\gov\mainapp\application\libcorp\pais\valueObject\PaisValueObject,
    br\gov\mainapp\application\libcorp\estado\valueObject\EstadoValueObject,
    br\gov\mainapp\application\libcorp\municipio\valueObject\MunicipioValueObject;

/**
  * SISICMBio
  *
  * @package br.gov.mainapp.application.libcorp.bioma.persist
  * @subpackage database
  * @author Álvaro Pereira Flôres <alvaro.flores@icmbio.gov.br>
* */
class BiomaPersist extends ParentPersist
{

    const EXP_PERSIST = 'Um erro inesperado ocorreu ao tentar executar a recuperação dos dados.';

    const EXP_ILLEGALARGUMENT = 'Um ou mais parametros informados para a montagem da query foi avaliado como inválido.';

    /**
     * Efetua pesquisa de bioma por Município
     *
     * @param MunicipioValueObject
     * @return ResultSet
     * */
    public function findByMunicipio (MunicipioValueObject $municipio)
    {
        try {
            $query = 'SELECT bio.sq_bioma,
                             bio.no_bioma
                      FROM corporativo.municipio m
                      INNER JOIN corporativo.bioma bio
                                ON bio.the_geom && m.the_geom AND st_intersects(bio.the_geom, m.the_geom)
                      WHERE m.sq_municipio = :sqMunicipio';

            $params['sqMunicipio']        = new \stdClass();
            $params['sqMunicipio']->type  = 'integer';
            $params['sqMunicipio']->value = (integer) $municipio->getSqMunicipio();

            # executa query
            return $this->execute($query, $params);

        } catch (IllegalArgumentException $iae) {
            throw new PersistException(
                self::EXP_ILLEGALARGUMENT
            );
        } catch (\exception $exp) {
            throw new PersistException(
                self::EXP_PERSIST
            );
        }
    }

    /**
     * Efetua pesquisa de bioma por Estado
     *
     * @param EstadoValueObject
     * @return ResultSet
     * */
    public function findByEstado (EstadoValueObject $estado)
    {
        try {
            $query = 'SELECT bio.sq_bioma,
                             bio.no_bioma
                      FROM corporativo.estado uf
                      INNER JOIN corporativo.bioma bio 
                            ON bio.the_geom && uf.the_geom AND st_intersects(bio.the_geom, uf.the_geom)
                      WHERE uf.sq_estado = :sqEstado';

            $params['sqEstado']        = new \stdClass();
            $params['sqEstado']->type  = 'integer';
            $params['sqEstado']->value = (integer) $estado->getSqEstado();

            # executa query
            return $this->execute($query, $params);

        } catch (IllegalArgumentException $iae) {
            throw new PersistException(self::EXP_ILLEGALARGUMENT);
        } catch (\exception $exp) {
            throw new PersistException(self::EXP_PERSIST);
        }
    }

    /**
     * Efetua pesquisa de bioma por Pais
     *
     * @param PaisValueObject
     * @return ResultSet
     * */
    public function findByPais (PaisValueObject $pais)
    {
        try {
            $query = 'SELECT bio.sq_bioma,
                             bio.no_bioma
                      FROM corporativo.pais p
                      INNER JOIN corporativo.bioma bio ON bio.the_geom && p.the_geom AND st_intersects(bio.the_geom, p.the_geom)
                      WHERE p.sq_pais = :sqPais';

            $params['sqPais']        = new \stdClass();
            $params['sqPais']->type  = 'integer';
            $params['sqPais']->value = (integer) $pais->getSqPais();

            # executa query
            return $this->execute($query, $params);

        } catch (IllegalArgumentException $iae) {
            throw new PersistException(
                self::EXP_ILLEGALARGUMENT
            );
        } catch (\exception $exp) {
            throw new PersistException(
                self::EXP_PERSIST
            );
        }
    }
}