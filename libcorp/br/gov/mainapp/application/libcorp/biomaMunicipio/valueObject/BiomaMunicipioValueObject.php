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
namespace br\gov\mainapp\application\libcorp\biomaMunicipio\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract,
    br\gov\mainapp\application\libcorp\municipio\mvcb\business\MunicipioBusiness;

/**
  * SISICMBio
  *
  * @package br.gov.mainapp.application.libcorp.biomaMunicipio
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="bioma_municipio")
  * @author J. Augusto <augustowebd@gmail.com>
  * @log(name="all")
  * */
class BiomaMunicipioValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="sqBiomaMunicipio",
     *  database="sq_bioma_municipio",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqBiomaMunicipio",
     *  set="setSqBiomaMunicipio"
     * )
     * */
     private $_sqBiomaMunicipio;

    /**
     * @attr (
     *  name="sqBioma",
     *  database="sq_bioma",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqBioma",
     *  set="setSqBioma"
     * )
     * */
     private $_sqBioma;

    /**
     * @attr (
     *  name="sqMunicipio",
     *  database="sq_municipio",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqMunicipio",
     *  set="setSqMunicipio"
     * )
     * */
     private $_sqMunicipio;

    /**
     * @param integer $sqBiomaMunicipio
     * @param integer $sqBioma
     * @param integer $sqMunicipio
     * */
    public function __construct ($sqBiomaMunicipio = NULL,
                                 $sqBioma = NULL,
                                 $sqMunicipio = NULL)
    {
        parent::__construct();
        $this->setSqBiomaMunicipio($sqBiomaMunicipio)
             ->setSqBioma($sqBioma)
             ->setSqMunicipio($sqMunicipio)
             ;
    }

    /**
     * @return integer
     * */
    public function getSqBiomaMunicipio ()
    {
        return $this->_sqBiomaMunicipio;
    }

    /**
     * @return integer
     * */
    public function getSqBioma ()
    {
        return $this->_sqBioma;
    }

    /**
     * @return MunicipioValueObject
     * */
    public function getSqMunicipio ()
    {
        if (!($this->_sqMunicipio instanceof parent)) {
            $this->_sqMunicipio = MunicipioBusiness::factory(NULL, 'libcorp')->find($this->_sqMunicipio);
        }
        return $this->_sqMunicipio;
    }

    /**
     * @param integer $sqBiomaMunicipio
     * @return BiomaMunicipioValueObject
     * */
    public function setSqBiomaMunicipio ($sqBiomaMunicipio = NULL)
    {
        $this->_sqBiomaMunicipio = $sqBiomaMunicipio;
        return $this;
    }

    /**
     * @param integer $sqBioma
     * @return BiomaMunicipioValueObject
     * */
    public function setSqBioma ($sqBioma = NULL)
    {
        $this->_sqBioma = $sqBioma;
        return $this;
    }

    /**
     * @param integer $sqMunicipio
     * @return BiomaMunicipioValueObject
     * */
    public function setSqMunicipio ($sqMunicipio = NULL)
    {
        $this->_sqMunicipio = $sqMunicipio;
        return $this;
    }
}