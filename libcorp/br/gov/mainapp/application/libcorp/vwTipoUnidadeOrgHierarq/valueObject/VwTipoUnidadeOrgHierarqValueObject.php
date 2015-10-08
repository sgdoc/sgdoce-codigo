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
namespace br\gov\mainapp\application\libcorp\vwTipoUnidadeOrgHierarq\valueObject;
use br\gov\mainapp\application\libcorp\vwTipoUnidadeOrgHierarq\valueObject\VwTipoUnidadeOrgHierarqValueObject,
    br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject;

/**
  * SISICMBio
  *
  * @name vwTipoUnidadeOrgHierarq
  * @package br.gov.icmbio.sisicmbio.application.libcorp.vwTipoUnidadeOrgHierarq
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="tipo_unidade_org_hierarq")
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * */
class VwTipoUnidadeOrgHierarqValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="sqTipoUnidadeOrg",
     *  database="sq_tipo_unidade_org",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoUnidadeOrg",
     *  set="setSqUnidOrgMunicipio"
     * )
     * */
    private $_sqTipoUnidadeOrg;

    /**
     * @attr (
     *  name="sqTipoUnidadePai",
     *  database="sq_tipo_unidade_pai",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoUnidadePai",
     *  set="setSqTipoUnidadePai"
     * )
     * */
    private $_sqTipoUnidadePai;

    /**
     * @attr (
     *  name="nivel",
     *  database="nivel",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getNivel",
     *  set="setNivel"
     * )
     * */
    private $_nivel;

    /**
     * @attr (
     *  name="trilha",
     *  database="trilha",
     *  type="string",
     *  nullable="TRUE",
     *  get="getTrilha",
     *  set="setTrilha"
     * )
     * */
    private $_trilha;

    /**
     * @attr (
     *  name="trilhaSigla",
     *  database="trilha_sigla",
     *  type="string",
     *  nullable="TRUE",
     *  get="getTrilhaSigla",
     *  set="setTrilhaSigla"
     * )
     * */
    private $_trilhaSigla;

    /**
     * @param integer $sqTipoUnidadeOrg
     * @param integer $sqTipoUnidadePai
     * @param integer $nivel
     * @param integer $trilha
     * @param boolean $trilha_sigla
     * */
    public function __construct($sqTipoUnidadeOrg = NULL,
                                $sqTipoUnidadePai = NULL,
                                $nivel= NULL,
                                $trilha = NULL,
                                $trilha_sigla = NULL)
    {
        parent::__construct();
        $this->setSqTipoUnidadeOrg($sqTipoUnidadeOrg)
             ->setSqTipoUnidadePai($sqTipoUnidadePai)
             ->setNivel($nivel)
             ->setTrilha($trilha)
             ->setTrilhaSigla($trilha_sigla);
    }

    /**
     * @return integer
     */
    public function getSqTipoUnidadeOrg ()
    {
        return $this->_sqTipoUnidadeOrg;
    }

    /**
     * @return integer
     */
    public function getSqTipoUnidadePai ()
    {
        return $this->_sqTipoUnidadePai;
    }

    /**
     * @return integer
     */
    public function getNivel ()
    {
        return $this->_nivel;
    }

    /**
     * @return string
     */
    public function getTrilha ()
    {
        return $this->_trilha;
    }

    /**
     * @return string
     */
    public function getTrilhaSigla ()
    {
        return $this->_trilhaSigla;
    }

    /**
     * @param integer $sqTipoUnidadeOrg
     * @return VwTipoUnidadeOrgHierarqValueObject
     */
    public function setSqTipoUnidadeOrg ($sqTipoUnidadeOrg = NULL)
    {
        $this->_sqTipoUnidadeOrg = $sqTipoUnidadeOrg;
        return $this;
    }

    /**
     * @param integer $sqTipoUnidadePai
     * @return VwTipoUnidadeOrgHierarqValueObject
     */
    public function setSqTipoUnidadePai ($sqTipoUnidadePai = NULL)
    {
        $this->_sqTipoUnidadePai = $sqTipoUnidadePai;
        return $this;
    }

    /**
     * @param integer $nivel
     * @return VwTipoUnidadeOrgHierarqValueObject
     */
    public function setNivel ($nivel = NULL)
    {
        $this->_nivel = $nivel;
        return $this;
    }

    /**
     * @param string $trilha
     * @return VwTipoUnidadeOrgHierarqValueObject
     */
    public function setTrilha ($trilha = NULL)
    {
        $this->_trilha = $trilha;
        return $this;
    }

    /**
     * @param string $trilhaSigla
     * @return VwTipoUnidadeOrgHierarqValueObject
     */
    public function setTrilhaSigla ($trilhaSigla = NULL)
    {
        $this->_trilhaSigla = $trilhaSigla;
        return $this;
    }
}