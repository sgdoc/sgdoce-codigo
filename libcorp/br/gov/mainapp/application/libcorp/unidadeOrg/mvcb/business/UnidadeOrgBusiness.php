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
namespace br\gov\mainapp\application\libcorp\unidadeOrg\mvcb\business;
use br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\estado\valueObject\EstadoValueObject,
    br\gov\mainapp\application\libcorp\pessoa\valueObject\PessoaValueObject,
    br\gov\mainapp\application\libcorp\municipio\valueObject\MunicipioValueObject,
    br\gov\mainapp\application\libcorp\unidadeOrg\valueObject\UnidadeOrgValueObject,
    br\gov\mainapp\application\libcorp\tipoUnidadeOrg\valueObject\TipoUnidadeOrgValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name UnidadeOrgBusiness
  * @package br.gov.icmbio.sisicmbio.application.libcorp.unidadeOrg.mvcb
  * @subpackage business
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * */
class UnidadeOrgBusiness extends ParentBusiness
{
    /**
     * Efetua a busca de Unidades Organizacionais por UF (<b>EstadoValueObject</b>::<i>sqEstado</i>)
     *
     * @example UnidadeOrgBusiness::findByUf
     * @code
     *     # cria filtro usado pelo email
     *     $voEstado = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voEstado = EstadoValueObject::factory();
     *     $voEstado->setSqEstado(1);
     *
     *     # efetua pesquisa
     *     $uniOrgBussiness = UnidadeOrgBusiness::factory();
     *     $uniOrgBussiness->findByUf($voEstado);
     * <?php
     * @endcode
     *
     * @param EstadoValueObject $voEstado
     * @return DataViewObjectAbstract[]
     * @throws BusinessException
     */
    public function findByUf (EstadoValueObject $voEstado)
    {
        try {
            return $this->_findByUf($voEstado)->getAllDataViewObject();
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }

    /**
     * Metodo auxiliar para pesquisa por UF
     * @param EstadoValueObject $voEstado
     */
    private function _findByUf(EstadoValueObject $voEstado)
    {
        try {
            return $this->getModelPersist('libcorp')->findByUf($voEstado);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }

    /**
     * Efetua a busca em Unidades Descentralizadas por Nome Pessoa (<b>PessoaValueObject</b>::<i>noPessoa</i>)
     * Solicitacao efetuada via ticket #459
     *
     * @example UnidadeOrgBusiness::findUndDescentralizadaByNome
     * @code
     * <?php
     *     # cria filtro usado pelo email
     *     $voPessoa = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voPessoa = PessoaValueObject::factory();
     *     $voPessoa->setnoPessoa('foo');
     *
     *     # efetua pesquisa
     *     $uniOrgBussiness = UnidadeOrgBusiness::factory();
     *     $uniOrgBussiness->findUndDescentralizadaByNome($voPessoa);
     * ?>
     * @endcode
     *
     * @param PessoaValueObject $voPessoa
     * @return DataViewObjectAbstract[]
     * @throws BusinessException
     */
    public function findUndDescentralizadaByNome (PessoaValueObject $voPessoa)
    {
        try {
            return $this->_findUndDescentralizadaByNome($voPessoa)->getAllDataViewObject();
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }

    /**
     * Método auxiliar que efetua a busca em unidades Descentralizadas por Nome Pessoa
     * @param PessoaValueObject $voPessoa
     * @throws BusinessException
     */
    private function _findUndDescentralizadaByNome (PessoaValueObject $voPessoa)
    {
        try {
            return $this->getModelPersist('libcorp')->findundDescentralizadaByNome($voPessoa);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }

    /**
     * Efetua busca em Unids Descentralizadas por Categoria (<b>TipoUnidadeOrgValueObject</b>::<i>noTipoUnidadeOrg</i>)
     * Solicitacao efetuada via ticket #459
     *
     * @example UnidadeOrgBusiness::findUndDescentralizadaByCategoria
     * @code
     * <?php
     *     # cria filtro usado pelo email
     *     $voTipoUnidade = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voTipoUnidade = TipoUnidadeOrgValueObject::factory();
     *     $voTipoUnidade->setNoTipoUnidadeOrg('foo');
     *
     *     # efetua pesquisa
     *     $uniOrgBussiness = UnidadeOrgBusiness::factory();
     *     $uniOrgBussiness->findUndDescentralizadaByCategoria($voTipoUnidade);
     * ?>
     * @endcode
     *
     * @param TipoUnidadeOrgValueObject $voTipoUnidade
     * @throws BusinessException
     * @return DataViewObject[]
     */
    public function findUndDescentralizadaByCategoria (TipoUnidadeOrgValueObject $voTipoUnidade)
    {
        try {
            return $this->_findUndDescentralizadaByCategoria($voTipoUnidade)->getAllDataViewObject();
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }

    /**
     * Método auxiliar que efetua a busca em unidades Descentralizadas por Categoria
     * @param TipoUnidadeOrgValueObject $voTipoUnidade
     * @throws BusinessException
     */
    private function _findUndDescentralizadaByCategoria (TipoUnidadeOrgValueObject $voTipoUnidade)
    {
        try {
            return $this->getModelPersist('libcorp')->findUndDescentralizadaByCategoria($voTipoUnidade);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }

    /**
     * Efetua a busca em Unidades Descentralizadas por Cidade (<b>MunicipioValueObject</b>::<i>noMunicipio</i>)
     * Solicitacao efetuada via ticket #459
     *
     * @example UnidadeOrgBusiness::findUndDescentralizadaByCidade
     * @code
     * <?php
     *     # cria filtro usado pelo email
     *     $voMunicipio = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voMunicipio = MunicipioValueObject::factory();
     *     $voMunicipio->setNoMunicipio('foo');
     *
     *     # efetua pesquisa
     *     $uniOrgBussiness = UnidadeOrgBusiness::factory();
     *     $uniOrgBussiness->findUndDescentralizadaByCidade($voMunicipio);
     * ?>
     * @endcode
     *
     * @param MunicipioValueObject $voMunicipio
     * @throws BusinessException
     * @return DataViewObject[]
     */
    public function findUndDescentralizadaByCidade (MunicipioValueObject $voMunicipio)
    {
        try {
            return $this->_findUndDescentralizadaByCidade($voMunicipio)->getAllDataViewObject();
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }

    /**
     * Método auxiliar que efetua a busca em unidades Descentralizadas por Cidade
     * @param MunicipioValueObject $voMunicipio
     * @throws BusinessException
     */
    private function _findUndDescentralizadaByCidade (MunicipioValueObject $voMunicipio)
    {
        try {
            return $this->getModelPersist('libcorp')->findUndDescentralizadaByCidade($voMunicipio);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }

    /***/
    public function findByParamFilterByName (UnidadeOrgValueObject $voUnidOrg, $limit = 10, $offset = 0)
    {
        $result = $this->getModelPersist('libcorp')->findByParamFilterByName($voUnidOrg, $limit, $offset);
        // $result = $this->getModelPersist('libcorp')->findByCpf($voPessoaFisica);
        // return $result->getValueObject();
        return $result->getAllDataViewObject();
    }

    /**
     * @param PessoaValueObject $filterPessoa
     * @param integer $limit
     * @return br\gov\sial\core\valueObject\DataViewObject[]
     * @throws BusinessException
     */
    public function findUnidadeConservacao (PessoaValueObject $filterPessoa, $limit = NULL)
    {
        try {
            return $this->getModelPersist('libcorp')
                        ->findUnidadeConservacao($filterPessoa, $limit)
                        ->getAllDataViewObject();
        } catch (ModelException $exp) {
            throw new BusinessException(Messages::ERROR, $exp->getCode(), $exp);
        }
    }

    public function findUnidadeOrgAtivaById (UnidadeOrgValueObject $voUnidOrg)
    {
        try {
            $voUnidOrg->setStAtivo(TRUE);
            return $this->getModelPersist('libcorp')
                         ->findByParam($voUnidOrg)
                         ->getAllDataViewObject();
        } catch (ModelException $exp) {
            throw new BusinessException(Messages::ERROR, $exp->getCode(), $exp);
        }
    }
}