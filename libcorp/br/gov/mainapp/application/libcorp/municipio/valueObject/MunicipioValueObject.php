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
namespace br\gov\mainapp\application\libcorp\municipio\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\estado\mvcb\business\EstadoBusiness,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract;

/**
  * SISICMBio
  *
  * @name MunicipioValueObject
  * @package br.gov.mainapp.application.libcorp.municipio
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="municipio")
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class MunicipioValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="sqMunicipio",
     *  database="sq_municipio",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqMunicipio",
     *  set="setSqMunicipio"
     * )
     * */
     private $_sqMunicipio;

    /**
     * @attr (
     *  name="coIbge",
     *  database="co_ibge",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getCoIbge",
     *  set="setCoIbge"
     * )
     * */
     private $_coIbge;

    /**
     * @attr (
     *  name="sqEstado",
     *  database="sq_estado",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqEstado",
     *  set="setSqEstado"
     * )
     * */
     private $_sqEstado;

    /**
     * @attr (
     *  name="noMunicipio",
     *  database="no_municipio",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNoMunicipio",
     *  set="setNoMunicipio"
     * )
     * */
     private $_noMunicipio;

    /**
     * @param integer $sqMunicipio
     * @param integer $coIbge
     * @param integer $sqEstado
     * @param string $noMunicipio
     * */
    public function __construct ($sqMunicipio = NULL,
                                 $coIbge = NULL,
                                 $sqEstado = NULL,
                                 $noMunicipio = NULL)
    {
        parent::__construct();
        $this->setSqMunicipio($sqMunicipio)
             ->setCoIbge($coIbge)
             ->setSqEstado($sqEstado)
             ->setNoMunicipio($noMunicipio)
             ;
    }

    /**
     * (non-PHPdoc)
     * Utilizado pois em Municipio existe a chamada deste método, devido a persistencia chamar automaticamente
     * é necessário fazer o apelido aqui !!
     * @see br\gov\icmbio\sial.SIALAbstract::__call()
     */
    public function __call ($name, $args)
    {
        if ('getSqNaturalidade' == $name) {
            return $this->getSqMunicipio();
        }
    }

    /**
     * @return integer
     * */
    public function getSqMunicipio ()
    {
        return $this->_sqMunicipio;
    }

    /**
     * @return integer
     * */
    public function getCoIbge ()
    {
        return $this->_coIbge;
    }

    /**
     * @return EstadoValueObject
     * */
    public function getSqEstado ()
    {
        if ((NULL != $this->_sqEstado) && !($this->_sqEstado instanceof parent)) {
            $this->_sqEstado = EstadoBusiness::factory(NULL, 'libcorp')->find($this->_sqEstado);
        }
        return $this->_sqEstado;
    }

    /**
     * @return string
     * */
    public function getNoMunicipio ()
    {
        return $this->_noMunicipio;
    }

    /**
     * @param integer $sqMunicipio
     * @return MunicipioValueObject
     * */
    public function setSqMunicipio ($sqMunicipio = NULL)
    {
        $this->_sqMunicipio = $sqMunicipio;
        return $this;
    }

    /**
     * @param integer $coIbge
     * @return MunicipioValueObject
     * */
    public function setCoIbge ($coIbge = NULL)
    {
        $this->_coIbge = $coIbge;
        return $this;
    }

    /**
     * @param integer $sqEstado
     * @return MunicipioValueObject
     * */
    public function setSqEstado ($sqEstado = NULL)
    {
        $this->_sqEstado = $sqEstado;
        return $this;
    }

    /**
     * @param string $noMunicipio
     * @return MunicipioValueObject
     * */
    public function setNoMunicipio ($noMunicipio = NULL)
    {
        $this->_noMunicipio = $noMunicipio;
        return $this;
    }
}