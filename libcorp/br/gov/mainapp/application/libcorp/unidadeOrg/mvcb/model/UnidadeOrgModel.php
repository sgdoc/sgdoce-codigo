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
namespace br\gov\mainapp\application\libcorp\unidadeOrg\mvcb\model;
use br\gov\mainapp\application\libcorp\estado\valueObject\EstadoValueObject,
    br\gov\mainapp\application\libcorp\pessoa\valueObject\PessoaValueObject,
    br\gov\mainapp\application\libcorp\municipio\valueObject\MunicipioValueObject,
    br\gov\mainapp\application\libcorp\unidadeOrg\valueObject\UnidadeOrgValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\model\ModelAbstract as ParentModel,
    br\gov\mainapp\application\libcorp\tipoUnidadeOrg\valueObject\TipoUnidadeOrgValueObject;

/**
  * SISICMBio
  *
  * @name UnidadeOrgModel
  * @package br.gov.icmbio.sisicmbio.application.libcorp.unidadeOrg.mvcb
  * @subpackage model
  * @author Fabio Lima <fabioolima@gmail.com>
  * @since 2012-04-17
  * @version $Id$
  * */
class UnidadeOrgModel extends ParentModel
{
    /**
     * Efetua a busca de Unidades Organizacionais por UF
     * @param EstadoValueObject $voEstado
     * @throws ModelException
     */
    public function findByUf (EstadoValueObject $voEstado)
    {
        try {
            $this->_resultSet = $this->_persist->findByUf($voEstado);
            return $this;
        } catch (PersistException $pExc) {
            # efetua log de erro
           ;
            throw new ModelException(self::REQUIRE_DATA_ERROR_MESSAGE);
        }
    }

    /**
     * Efetua a busca em Unidades Descentralizadas por Nome
     * @param PessoaValueObject $voPessoa
     * @throws ModelException
     * @return ModelAbstractObject
     */
    public function findundDescentralizadaByNome (PessoaValueObject $voPessoa)
    {
        try {
            $this->_resultSet = $this->_persist->findundDescentralizadaByNome($voPessoa);
            return $this;
        } catch (PersistException $pExc) {
            # efetua log de erro
           ;
            throw new ModelException(self::REQUIRE_DATA_ERROR_MESSAGE);
        }
    }

    /**
     * Efetua a busca em Unidades Descentralizadas por Categoria
     * @param PessoaValueObject $voPessoa
     * @throws ModelException
     * @return ModelAbstractObject
     */
    public function findUndDescentralizadaByCategoria (TipoUnidadeOrgValueObject $voTipoUnidade)
    {
        try {
            $this->_resultSet = $this->_persist->findUndDescentralizadaByCategoria($voTipoUnidade);
            return $this;
        } catch (PersistException $pExc) {
            # efetua log de erro
           ;
            throw new ModelException(self::REQUIRE_DATA_ERROR_MESSAGE);
        }
    }

    /**
     * Efetua a busca em Unidades Descentralizadas por Cidade
     * @param MunicipioValueObject $voMunicipio
     * @throws ModelException
     * @return ModelAbstractObject
     */
    public function findUndDescentralizadaByCidade (MunicipioValueObject $voMunicipio)
    {
        try {
            $this->_resultSet = $this->_persist->findUndDescentralizadaByCidade($voMunicipio);
            return $this;
        } catch (PersistException $pExc) {
            # efetua log de erro
           ;
            throw new ModelException(self::REQUIRE_DATA_ERROR_MESSAGE);
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
        try {
            $this->_resultSet = $this->_persist->findByParamFilterByName($voUnidOrg, $limit, $offset);
            return $this;
        } catch (PersistException $pExc) {
            # efetua log de erro
           ;
            throw new ModelException(self::REQUIRE_DATA_ERROR_MESSAGE);
        }
    }
    
    /**
     * @param PessoaValueObject $filterPessoa
     * @param integer $limit
     * @throws ModelException
     * @return DestinacaoFgDasModel
     */
    public function findUnidadeConservacao (PessoaValueObject $filterPessoa, $limit = NULL)
    {
        try {
            $this->_resultSet = $this->getPersist()->findUnidadeConservacao($filterPessoa, $limit);
            return $this;
        } catch (PersistException $exp) {
            throw new ModelException($exp->getMessage(), $exp->getCode(), $exp);
        }
    }    
}