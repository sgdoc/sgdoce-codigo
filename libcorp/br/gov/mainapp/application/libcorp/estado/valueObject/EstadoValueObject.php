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
namespace br\gov\mainapp\application\libcorp\estado\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\pais\mvcb\business\PaisBusiness,
    br\gov\mainapp\application\libcorp\regiao\mvcb\business\RegiaoBusiness;

/**
  * SISICMBio
  *
  * @name EstadoValueObject
  * @package br.gov.mainapp.application.libcorp.estado
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="estado")
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class EstadoValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="sqEstado",
     *  database="sq_estado",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqEstado",
     *  set="setSqEstado"
     * )
     * */
     private $_sqEstado;

    /**
     * @attr (
     *  name="sgEstado",
     *  database="sg_estado",
     *  type="string",
     *  nullable="FALSE",
     *  get="getSgEstado",
     *  set="setSgEstado"
     * )
     * */
     private $_sgEstado;

    /**
     * @attr (
     *  name="sqPais",
     *  database="sq_pais",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqPais",
     *  set="setSqPais"
     * )
     * */
     private $_sqPais;

    /**
     * @attr (
     *  name="noEstado",
     *  database="no_estado",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNoEstado",
     *  set="setNoEstado"
     * )
     * */
     private $_noEstado;

    /**
     * @attr (
     * name="coIbge",
     * database="co_ibge",
     * type="integer",
     * get="getCoIbge",
     * set="setCoIbge"
    */
     private $_coIbge;

    /**
     * @attr (
     * name="sqRegiao",
     * database="sq_regiao",
     * type="integer",
     * get="getSqRegiao",
     * set="setSqRegiao"
    */
     private $_sqRegiao;

    /**
     * @param integer $sqEstado
     * @param string $sgEstado
     * @param integer $sqPais
     * @param string $noEstado
     * @param integer $coIbge
     * @param integer $sqRegiao
     * */
    public function __construct ($sqEstado = NULL,
                                 $sgEstado = NULL,
                                 $sqPais = NULL,
                                 $noEstado = NULL,
                                 $coIbge = NULL,
                                 $sqRegiao = NULL)
    {
        parent::__construct();
        $this->setSqEstado($sqEstado)
             ->setSgEstado($sgEstado)
             ->setSqPais($sqPais)
             ->setNoEstado($noEstado)
             ->setCoIbge($coIbge)
             ->setSqRegiao($sqRegiao)
             ;
    }

    /**
     * @return integer
     * */
    public function getSqEstado ()
    {
        return $this->_sqEstado;
    }

    /**
     * @return string
     * */
    public function getSgEstado ()
    {
        return $this->_sgEstado;
    }

    /**
     * @return PaisValueObject
     * */
    public function getSqPais ()
    {
        if (!($this->_sqPais instanceof parent)) {
            $this->_sqPais = PaisBusiness::factory(NULL, 'libcorp')->find($this->_sqPais);
        }
        return $this->_sqPais;
    }

    /**
     * @return string
     * */
    public function getNoEstado ()
    {
        return $this->_noEstado;
    }

    /**
     * @return integer
     */
    public function getCoIbge ()
    {
        return $this->_coIbge;
    }

    /**
     * @return RegiaoValueObject
     */
    public function getSqRegiao ()
    {
        if (!($this->_sqRegiao instanceof parent)) {
            $this->_sqRegiao = RegiaoBusiness::factory(NULL, 'libcorp')->find($this->_sqRegiao);
        }
        return $this->_sqRegiao;
    }

    /**
     * @param integer $sqEstado
     * @return EstadoValueObject
     * */
    public function setSqEstado ($sqEstado = NULL)
    {
        $this->_sqEstado = $sqEstado;
        return $this;
    }

    /**
     * @param string $sgEstado
     * @return EstadoValueObject
     * */
    public function setSgEstado ($sgEstado = NULL)
    {
        $this->_sgEstado = $sgEstado;
        return $this;
    }

    /**
     * @param integer $sqPais
     * @return EstadoValueObject
     * */
    public function setSqPais ($sqPais = NULL)
    {
        $this->_sqPais = $sqPais;
        return $this;
    }

    /**
     * @param string $noEstado
     * @return EstadoValueObject
     * */
    public function setNoEstado ($noEstado = NULL)
    {
        $this->_noEstado = $noEstado;
        return $this;
    }

    /**
     * @param integer $coIbge
     * @return EstadoValueObject
     */
    public function setCoIbge ($coIbge = NULL)
    {
        $this->_coIbge = $coIbge;
        return $this;
    }

    /**
     * @param integer $sqRegiao
     * @return EstadoValueObject
     */
    public function setSqRegiao ($sqRegiao = NULL)
    {
        $this->_sqRegiao = $sqRegiao;
        return $this;
    }
}