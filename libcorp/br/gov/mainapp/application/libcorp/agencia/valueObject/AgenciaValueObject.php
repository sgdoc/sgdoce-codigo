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
namespace br\gov\mainapp\application\libcorp\agencia\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\banco\mvcb\business\BancoBusiness,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract;

/**
  * SISICMBio
  *
  * @name AgenciaValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.agencia
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="agencia")
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class AgenciaValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="noAgencia",
     *  database="no_agencia",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNoAgencia",
     *  set="setNoAgencia"
     * )
     * */
     private $_noAgencia;

    /**
     * @attr (
     *  name="coAgencia",
     *  database="co_agencia",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getCoAgencia",
     *  set="setCoAgencia"
     * )
     * */
     private $_coAgencia;

    /**
     * @attr (
     *  name="sqBanco",
     *  database="sq_banco",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqBanco",
     *  set="setSqBanco"
     * )
     * */
     private $_sqBanco;

    /**
     * @attr (
     *  name="sqAgencia",
     *  database="sq_agencia",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqAgencia",
     *  set="setSqAgencia"
     * )
     * */
     private $_sqAgencia;

    /**
     * @attr (
     *  name="coDigitoAgencia",
     *  database="co_digito_agencia",
     *  type="integer",
     *  nullable="TRUE",
     *  get="getCoDigitoAgencia",
     *  set="setCoDigitoAgencia"
     * )
     * */
     private $_coDigitoAgencia;

    /**
     * @param string $noAgencia
     * @param integer $coAgencia
     * @param integer $sqBanco
     * @param integer $sqAgencia
     * @param integer $coDigitoAgencia
     * */
    public function __construct ($noAgencia = NULL,
                                 $coAgencia = NULL,
                                 $sqBanco = NULL,
                                 $sqAgencia = NULL,
                                 $coDigitoAgencia = NULL)
    {
        parent::__construct();
        $this->setNoAgencia($noAgencia)
             ->setCoAgencia($coAgencia)
             ->setSqBanco($sqBanco)
             ->setSqAgencia($sqAgencia)
             ->setCoDigitoAgencia($coDigitoAgencia)
             ;
    }

    /**
     * @return string
     * */
    public function getNoAgencia ()
    {
        return $this->_noAgencia;
    }

    /**
     * @return integer
     * */
    public function getCoAgencia ()
    {
        return $this->_coAgencia;
    }

    /**
     * @return BancoValueObject
     * */
    public function getSqBanco ()
    {
        if ((NULL != $this->_sqBanco) && !($this->_sqBanco instanceof parent)) {
            $this->_sqBanco = BancoBusiness::factory(NULL, 'libcorp')->find($this->_sqBanco);
        }
        return $this->_sqBanco;
    }

    /**
     * @return integer
     * */
    public function getSqAgencia ()
    {
        return $this->_sqAgencia;
    }

    /**
     * @return integer
     * */
    public function getCoDigitoAgencia ()
    {
        return $this->_coDigitoAgencia;
    }

    /**
     * @param string $noAgencia
     * @return br\gov\mainapp\application\libcorp\agencia\valueObject\AgenciaValueObject
     * */
    public function setNoAgencia ($noAgencia = NULL)
    {
        $this->_noAgencia = $noAgencia;
        return $this;
    }

    /**
     * @param integer $coAgencia
     * @return br\gov\mainapp\application\libcorp\agencia\valueObject\AgenciaValueObject
     * */
    public function setCoAgencia ($coAgencia = NULL)
    {
        $this->_coAgencia = $coAgencia;
        return $this;
    }

    /**
     * @param integer $sqBanco
     * @return br\gov\mainapp\application\libcorp\agencia\valueObject\AgenciaValueObject
     * */
    public function setSqBanco ($sqBanco = NULL)
    {
        $this->_sqBanco = $sqBanco;
        return $this;
    }

    /**
     * @param integer $sqAgencia
     * @return br\gov\mainapp\application\libcorp\agencia\valueObject\AgenciaValueObject
     * */
    public function setSqAgencia ($sqAgencia = NULL)
    {
        $this->_sqAgencia = $sqAgencia;
        return $this;
    }

    /**
     * @param integer $coDigitoAgencia
     * @return br\gov\mainapp\application\libcorp\agencia\valueObject\AgenciaValueObject
     * */
    public function setCoDigitoAgencia ($coDigitoAgencia = NULL)
    {
        $this->_coDigitoAgencia = $coDigitoAgencia;
        return $this;
    }
}