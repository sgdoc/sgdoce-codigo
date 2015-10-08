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
namespace br\gov\mainapp\application\libcorp\bioma\mvcb\model;
use br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\mainapp\application\libcorp\pais\valueObject\PaisValueObject,
    br\gov\mainapp\application\libcorp\estado\valueObject\EstadoValueObject,
    br\gov\mainapp\application\libcorp\municipio\valueObject\MunicipioValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\model\ModelAbstract as ParentModel;

/**
  * SISICMBio
  *
  * @name BiomaModel
  * @package br.gov.icmbio.sisicmbio.application.libcorp.bioma.mvcb
  * @subpackage model
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * */
class BiomaModel extends ParentModel
{
    /**
     * efetua pesquisa de bioma por Municipio
     *
     * @param MunicipioValueObject
     * @return BiomaModel
     * */
    public function findByMunicipio (MunicipioValueObject $municipio)
    {
        try {
            $this->_resultSet = $this->_persist->findByMunicipio($municipio);
            return $this;
        } catch (PersistException $pExc) {
            # efetua log de erro
            ;
            throw new ModelException(self::REQUIRE_DATA_ERROR_MESSAGE);
        }
    }

    /**
     * efetua pesquisa de bioma por UF
     *
     * @param EstadoValueObject
     * @return BiomaModel
     * */
    public function findByEstado (EstadoValueObject $estado)
    {
        try {
            $this->_resultSet = $this->_persist->findByEstado($estado);
            return $this;
        } catch (PersistException $pExc) {
            # efetua log de erro
            ;
            throw new ModelException(self::REQUIRE_DATA_ERROR_MESSAGE);
        }
    }

    /**
     * efetua pesquisa de bioma por Pais
     *
     * @param PaisValueObject
     * @return BiomaModel
     * */
    public function findByPais (PaisValueObject $pais)
    {
        try {
            $this->_resultSet = $this->_persist->findByPais($pais);
            return $this;
        } catch (PersistException $pExc) {
            # efetua log de erro
            ;
            throw new ModelException(self::REQUIRE_DATA_ERROR_MESSAGE);
        }
    }
}