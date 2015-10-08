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
namespace br\gov\mainapp\application\libcorp\bioma\mvcb\business;
use br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\pais\valueObject\PaisValueObject,
    br\gov\mainapp\application\libcorp\estado\valueObject\EstadoValueObject,
    br\gov\mainapp\application\libcorp\municipio\valueObject\MunicipioValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name BiomaBusiness
  * @package br.gov.icmbio.sisicmbio.application.libcorp.bioma.mvcb
  * @subpackage business
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * */
class BiomaBusiness extends ParentBusiness
{
    /**
     * Recupera o(s) bioma(s) do municipio informado (<b>MunicipioValueObject</b>::<i>sqMunicipio</i>)
     *
     * @example BiomaBusiness::findByMunicipio
     * @code
     * <?php
     *     # Filtro a ser utilizado na pesquisa
     *     $voMunicipio = ValueObjectAbstract::factory('fullNameSpace');
     *     # Também poderá ser utilizado
     *     # $voMunicipio = MunicipioValueObject::factory();
     *
     *     # Efetua a pesquisa
     *     $biomaBusiness = BiomaBusiness::factory();
     *     $biomaBusiness->findByMunicipio($voMunicipio);
     * ?>
     * @endcode
     *
     * @param MunicipioValueObject
     * @return ValueObjectAbstract[]
     * @throws BusinessException
     * */
    public function findByMunicipio (MunicipioValueObject $municipio)
    {
        return $this->_findByMunicipio($municipio)->getAllValueObject();
    }

    /**
     * Recupera o(s) bioma(s) do estado informado (<b>EstadoValueObject</b>::<i>sqEstado</i>)
     *
     * @example BiomaBusiness::findByEstado
     * @code
     * <?php
     *    # cria filtro usado pelo bioma
     *    $ufValueObject = ValueObjectAbstract::factory('fullnamespace');
     *
     *    # efetua pesquisa
     *    $biomaBusiness = BiomaBusiness::factory();
     *    $biomaBusiness->findByEstado($ufValueObject);
     * ?>
     * @endcode
     *
     * @param EstadoValueObject
     * @return ValueObjectAbstract[]
     * @throws BusinessException
     * */
    public function findByEstado (EstadoValueObject $estado)
    {
        return $this->_findByEstado($estado)->getAllValueObject();
    }

    /**
     * Recupera o(s) bioma(s) do pais informado (<b>PaisValueObject</b>::<i>sqPais</i>)
     *
     * @example BiomaBusiness::findByPais
     * @code
     * <?php
     *    # cria filtro usado pelo bioma
     *    $paisValueObject = ValueObjectAbstract::factory('fullnamespace');
     *    # outra forma de utilizar
     *    # $paisValueObject = PaisValueObject::factory();
     *    $paisValueObject->setSqPais(1);
     *
     *    # efetua pesquisa
     *    $biomaBusiness = BiomaBusiness::factory();
     *    $biomaBusiness->findByPais($paisValueObject);
     * ?>
     * @endcode
     *
     * @param PaisValueObject
     * @return ValueObjectAbstract[]
     * @throws BusinessException
     * */
    public function findByPais (PaisValueObject $pais)
    {
        return $this->_findByPais($pais)->getAllValueObject();
    }


    /**
     * @internal
     * @param MunicipioValueObject
     * @return ResultSet
     * @throws BusinessException
     * */
    protected function _findByMunicipio(MunicipioValueObject $municipio)
    {
        try {
            return $this->getModelPersist('libcorp')->findByMunicipio($municipio);
        } catch (ModelException $mExc) {
            throw new BusinessException(self::GET_DATA_ERROR_MESSAGE);
        }
    }

    /**
     * @internal
     * @param EstadoValueObject
     * @return ResultSet
     * @throws BusinessException
     * */
    protected function _findByEstado (EstadoValueObject $estado)
    {
        try {
            return $this->getModelPersist('libcorp')->findByEstado($estado);
        } catch (ModelException $mExc) {
            throw new BusinessException(self::GET_DATA_ERROR_MESSAGE);
        }
    }

    /**
     * @internal
     * @param
     * @return ResultSet
     * @throws BusinessException
     * */
    protected function _findByPais (PaisValueObject $pais)
    {
        try {
            return $this->getModelPersist('libcorp')
                        ->findByPais($pais);
        } catch (ModelException $mExc) {
            throw new BusinessException(self::GET_DATA_ERROR_MESSAGE);
        }
    }
}