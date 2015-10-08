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
namespace br\gov\mainapp\application\libcorp\unidadeOrg\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness,
    br\gov\mainapp\application\libcorp\unidadeOrg\mvcb\business\UnidadeOrgBusiness,
    br\gov\mainapp\application\libcorp\tipoUnidadeOrg\mvcb\business\TipoUnidadeOrgBusiness;

/**
  * SISICMBio
  *
  * @name UnidadeOrgValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.unidadeOrg
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="unidade_org")
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class UnidadeOrgValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="sqPessoa",
     *  database="sq_pessoa",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqPessoa",
     *  set="setSqPessoa"
     * )
     * */
     private $_sqPessoa;

    /**
     * @attr (
     *  name="sqUnidadeSuperior",
     *  database="sq_unidade_superior",
     *  type="integer",
     *  nullable="TRUE",
     *  get="getSqUnidadeSuperior",
     *  set="setSqUnidadeSuperior"
     * )
     * */
     private $_sqUnidadeSuperior;

    /**
     * @attr (
     *  name="sqUnidadeAdmPai",
     *  database="sq_unidade_adm_pai",
     *  type="integer",
     *  nullable="TRUE",
     *  get="getSqUnidadeAdmPai",
     *  set="setSqUnidadeAdmPai"
     * )
     * */
     private $_sqUnidadeAdmPai;

    /**
     * @attr (
     *  name="sqUnidadeFinPai",
     *  database="sq_unidade_fin_pai",
     *  type="integer",
     *  nullable="TRUE",
     *  get="getSqUnidadeFinPai",
     *  set="setSqUnidadeFinPai"
     * )
     * */
     private $_sqUnidadeFinPai;

    /**
     * @attr (
     *  name="sqTipoUnidade",
     *  database="sq_tipo_unidade",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoUnidade",
     *  set="setSqTipoUnidade"
     * )
     * */
     private $_sqTipoUnidade;

    /**
     * @attr (
     *  name="coUorg",
     *  database="co_uorg",
     *  type="string",
     *  nullable="TRUE",
     *  get="getCoUorg",
     *  set="setCoUorg"
     * )
     * */
     private $_coUorg;

    /**
     * @attr (
     *  name="sgUnidadeOrg",
     *  database="sg_unidade_org",
     *  type="string",
     *  nullable="TRUE",
     *  get="getSgUnidadeOrg",
     *  set="setSgUnidadeOrg"
     * )
     * */
     private $_sgUnidadeOrg;

    /**
     * @attr (
     *  name="stAtivo",
     *  database="st_ativo",
     *  type="boolean",
     *  nullable="FALSE",
     *  get="getStAtivo",
     *  set="setStAtivo"
     * )
     * */
     private $_stAtivo;

    /**
     * @attr (
     *  name="nuLatitude",
     *  database="nu_latitude",
     *  type="string",
     *  nullable="TRUE",
     *  get="getNuLatitude",
     *  set="setNuLatitude"
     * )
     * */
     private $_nuLatitude;

    /**
     * @attr (
     *  name="nuLongitude",
     *  database="nu_longitude",
     *  type="string",
     *  nullable="TRUE",
     *  get="getNuLongitude",
     *  set="setNuLongitude"
     * )
     * */
     private $_nuLongitude;

    /**
     * @attr (
     *  name="inUnidadeFinanceira",
     *  database="in_unidade_financeira",
     *  type="boolean",
     *  nullable="TRUE",
     *  get="getInUnidadeFinanceira",
     *  set="setInUnidadeFinanceira"
     * )
     * */
     private $_inUnidadeFinanceira;

    /**
     * @attr (
     *  name="inUoExterna",
     *  database="in_uo_externa",
     *  type="boolean",
     *  nullable="TRUE",
     *  get="getInUoExterna",
     *  set="setInUoExterna"
     * )
     * */
     private $_inUoExterna;

    /**
     * @attr (
     *  name="coUnidadeGestora",
     *  database="co_unidade_gestora",
     *  type="string",
     *  nullable="TRUE",
     *  get="getCoUnidadeGestora",
     *  set="setCoUnidadeGestora"
     * )
     * */
     private $_coUnidadeGestora;

    /**
     * @attr (
     *  name="nuNup",
     *  database="nu_nup",
     *  type="integer",
     *  nullable="TRUE",
     *  get="getNuNup",
     *  set="setNuNup"
     * )
     * */
     private $_nuNup;

    /**
     * @attr (
     *  name="sqUoProtocolizadora",
     *  database="sq_uo_protocolizadora",
     *  type="integer",
     *  nullable="TRUE",
     *  get="getSqUoProtocolizadora",
     *  set="setSqUoProtocolizadora"
     * )
     * */
     private $_sqUoProtocolizadora;

    /**
     * @attr (
     *  name="coCnuc",
     *  database="co_cnuc",
     *  type="integer",
     *  nullable="TRUE",
     *  get="getCoCnuc",
     *  set="setCoCnuc"
     * )
     * */
     private $_coCnuc;

    /**
     * @attr (
     *  name="nuCnpj",
     *  database="nu_cnpj",
     *  type="string",
     *  nullable="TRUE",
     *  get="getNuCnpj",
     *  set="setNuCnpj"
     * )
     * */
     private $_nuCnpj;

     /**
      * @param integer $sqPessoa
      * @param integer $sqUnidadeSuperior
      * @param integer $sqUnidadeAdmPai
      * @param integer $sqUnidadeFinPai
      * @param integer $sqTipoUnidade
      * @param string $coUorg
      * @param string $sgUnidadeOrg
      * @param boolean $stAtivo
      * @param string $nuLatitude
      * @param string $nuLongitude
      * @param boolean $inUnidadeFinanceira
      * @param boolean $inUoExterna
      * @param string $coUnidadeGestora
      * @param integer $nuNup
      * @param integer $sqUoProtocolizadora
      * @param integer $coCnuc
      * @param string $nuCnpj
      * */
     public function __construct($sqPessoa = NULL,
                                 $sqUnidadeSuperior = NULL,
                                 $sqUnidadeAdmPai = NULL,
                                 $sqUnidadeFinPai = NULL,
                                 $sqTipoUnidade = NULL,
                                 $coUorg = NULL,
                                 $sgUnidadeOrg = NULL,
                                 $stAtivo = NULL,
                                 $nuLatitude = NULL,
                                 $nuLongitude = NULL,
                                 $inUnidadeFinanceira = NULL,
                                 $inUoExterna = NULL,
                                 $coUnidadeGestora = NULL,
                                 $nuNup = NULL,
                                 $sqUoProtocolizadora = NULL,
                                 $coCnuc = NULL,
                                 $nuCnpj = NULL)
     {
         parent::__construct();
         $this->setSqPessoa($sqPessoa)
              ->setSqUnidadeSuperior($sqUnidadeSuperior)
              ->setSqUnidadeAdmPai($sqUnidadeAdmPai)
              ->setSqUnidadeFinPai($sqUnidadeFinPai)
              ->setSqTipoUnidade($sqTipoUnidade)
              ->setCoUorg($coUorg)
              ->setSgUnidadeOrg($sgUnidadeOrg)
              ->setStAtivo($stAtivo)
              ->setNuLatitude($nuLatitude)
              ->setNuLongitude($nuLongitude)
              ->setInUnidadeFinanceira($inUnidadeFinanceira)
              ->setInUoExterna($inUoExterna)
              ->setCoUnidadeGestora($coUnidadeGestora)
              ->setNuNup($nuNup)
              ->setSqUoProtocolizadora($sqUoProtocolizadora)
              ->setCoCnuc($coCnuc)
              ->setNuCnpj($nuCnpj);
     }

    /**
     * retorna a chave primaria de unidade organizacional
     *
     * @return integer
     * */
    public function getSqUnidadeOrgPessoa ()
    {
        return $this->getSqUnidadeOrg();
    }

    /**
     * @return PessoaValueObject
     * */
    public function getSqUnidadeOrg ()
    {
        return $this->getSqPessoa();
    }

    /**
     * @return PessoaValueObject
     * */
    public function getSqPessoa ()
    {
        if ((NULL != $this->_sqPessoa) && !($this->_sqPessoa instanceof parent)) {
            $this->_sqPessoa = PessoaBusiness::factory(NULL, 'libcorp')->find($this->_sqPessoa);
        }
        return $this->_sqPessoa;
    }

    /**
     * @return UnidadeOrgValueObject
     * */
    public function getSqUnidadeSuperior ()
    {
        if ((NULL != $this->_sqUnidadeSuperior) && !($this->_sqUnidadeSuperior instanceof parent)) {
            $this->_sqUnidadeSuperior = UnidadeOrgBusiness::factory(NULL, 'libcorp')->find($this->_sqUnidadeSuperior);
        }
        return $this->_sqUnidadeSuperior;
    }

    /**
     * @return UnidadeOrgValueObject
     * */
    public function getSqUnidadeAdmPai ()
    {
        if ((NULL != $this->_sqUnidadeAdmPai) && !($this->_sqUnidadeAdmPai instanceof parent)) {
            $this->_sqUnidadeAdmPai = UnidadeOrgBusiness::factory(NULL, 'libcorp')->find($this->_sqUnidadeAdmPai);
        }
        return $this->_sqUnidadeAdmPai;
    }

    /**
     * @return UnidadeOrgValueObject
     * */
    public function getSqUnidadeFinPai ()
    {
        if ((NULL != $this->_sqUnidadeFinPai) && !($this->_sqUnidadeFinPai instanceof parent)) {
            $this->_sqUnidadeFinPai = UnidadeOrgBusiness::factory(NULL, 'libcorp')->find($this->_sqUnidadeFinPai);
        }
        return $this->_sqUnidadeFinPai;
    }

    /**
     * @return TipoUnidadeValueObject
     * */
    public function getSqTipoUnidade ()
    {
        if ((NULL != $this->_sqTipoUnidade) && !($this->_sqTipoUnidade instanceof parent)) {
            $this->_sqTipoUnidade = TipoUnidadeOrgBusiness::factory(NULL, 'libcorp')->find($this->_sqTipoUnidade);
        }
        return $this->_sqTipoUnidade;
    }

    /**
     * @return string
     * */
    public function getCoUorg ()
    {
        return $this->_coUorg;
    }

    /**
     * @return string
     * */
    public function getSgUnidadeOrg ()
    {
        return $this->_sgUnidadeOrg;
    }

    /**
     * @return string
     * */
    public function getNuLatitude ()
    {
        return $this->_nuLatitude;
    }

    /**
     * @return string
     * */
    public function getNuLongitude ()
    {
        return $this->_nuLongitude;
    }

    /**
     * @return boolean
     * */
    public function getInUnidadeFinanceira ()
    {
        return $this->_inUnidadeFinanceira;
    }

    /**
     * @return boolean
     * */
    public function getInUoExterna ()
    {
        return $this->_inUoExterna;
    }

    /**
     * @return boolean
     * */
    public function getStAtivo ()
    {
        return $this->_stAtivo;
    }

    /**
     * @return string
     * */
    public function getCoUnidadeGestora ()
    {
        return $this->_coUnidadeGestora;
    }

    /**
     * @return integer
     * */
    public function getNuNup ()
    {
        return $this->_nuNup;
    }

    /**
     * @return UnidadeOrgValueObject
     * */
    public function getSqUoProtocolizadora ()
    {
        if ((NULL != $this->_sqUoProtocolizadora) && !($this->_sqUoProtocolizadora instanceof parent)) {
            $this->_sqUoProtocolizadora = UnidadeOrgBusiness::factory(NULL, 'libcorp')->find($this->_sqUoProtocolizadora);
        }
        return $this->_sqUoProtocolizadora;
    }

    /**
     * @return integer
     * */
    public function getCoCnuc ()
    {
        return $this->_coCnuc;
    }

    /**
     * @return string
     * */
    public function getNuCnpj ()
    {
        return $this->_nuCnpj;
    }

    /**
     * @param integer $sqPessoa
     * @return UnidadeOrgValueObject
     * */
    public function setSqPessoa ($sqPessoa = NULL)
    {
        $this->_sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * @param integer $sqUnidadeSuperior
     * @return UnidadeOrgValueObject
     * */
    public function setSqUnidadeSuperior ($sqUnidadeSuperior = NULL)
    {
        $this->_sqUnidadeSuperior = $sqUnidadeSuperior;
        return $this;
    }

    /**
     * @param integer $sqUnidadeAdmPai
     * @return UnidadeOrgValueObject
     * */
    public function setSqUnidadeAdmPai ($sqUnidadeAdmPai = NULL)
    {
        $this->_sqUnidadeAdmPai = $sqUnidadeAdmPai;
        return $this;
    }

    /**
     * @param integer $_sqUnidadeFinPai
     * @return UnidadeOrgValueObject
     * */
    public function setSqUnidadeFinPai ($sqUnidadeFinPai = NULL)
    {
        $this->_sqUnidadeFinPai = $sqUnidadeFinPai;
        return $this;
    }

    /**
     * @param integer $_sqTipoUnidade
     * @return UnidadeOrgValueObject
     * */
    public function setSqTipoUnidade ($sqTipoUnidade = NULL)
    {
        $this->_sqTipoUnidade = $sqTipoUnidade;
        return $this;
    }

    /**
     * @param string $coUorg
     * @return UnidadeOrgValueObject
     * */
    public function setCoUorg ($coUorg = NULL)
    {
        $this->_coUorg = $coUorg;
        return $this;
    }

    /**
     * @param string $sgUnidadeOrg
     * @return UnidadeOrgValueObject
     * */
    public function setSgUnidadeOrg ($sgUnidadeOrg = NULL)
    {
        $this->_sgUnidadeOrg = $sgUnidadeOrg;
        return $this;
    }

    /**
     * @param boolean $stAtivo
     * @return UnidadeOrgValueObject
     * */
    public function setStAtivo ($stAtivo = NULL)
    {
        $this->_stAtivo = $stAtivo;
        return $this;
    }

    /**
     * @param string $nuLatitude
     * @return UnidadeOrgValueObject
     * */
    public function setNuLatitude ($nuLatitude = NULL)
    {
        $this->_nuLatitude = $nuLatitude;
        return $this;
    }

    /**
     * @param string $nuLongitude
     * @return UnidadeOrgValueObject
     * */
    public function setNuLongitude ($nuLongitude = NULL)
    {
        $this->_nuLongitude = $nuLongitude;
        return $this;
    }

    /**
     * @param boolean $inUnidadeFinanceira
     * @return UnidadeOrgValueObject
     * */
    public function setInUnidadeFinanceira ($inUnidadeFinanceira = NULL)
    {
        $this->_inUnidadeFinanceira = $inUnidadeFinanceira;
        return $this;
    }

    /**
     * @param boolean $inUoExterna
     * @return UnidadeOrgValueObject
     * */
    public function setInUoExterna ($inUoExterna = NULL)
    {
        $this->_inUoExterna = $inUoExterna;
        return $this;
    }

    /**
     * @param string $_coUnidadeGestora
     * */
    public function setCoUnidadeGestora ($coUnidadeGestora = NULL)
    {
        $this->_coUnidadeGestora = $coUnidadeGestora;
        return $this;
    }

    /**
     * @param integer $nuNup
     * @return UnidadeOrgValueObject
     * */
    public function setNuNup ($nuNup = NULL)
    {
        $this->_nuNup = $nuNup;
        return $this;
    }

    /**
     * @param integer $sqUoProtocolizadora
     * @return UnidadeOrgValueObject
     * */
    public function setSqUoProtocolizadora ($sqUoProtocolizadora = NULL)
    {
        $this->_sqUoProtocolizadora = $sqUoProtocolizadora;
        return $this;
    }

    /**
     * @param integer $coCnuc
     * @return UnidadeOrgValueObject
     * */
    public function setCoCnuc ($coCnuc = NULL)
    {
        $this->_coCnuc = $coCnuc;
        return $this;
    }

    /**
     * @param string $nuCnpj
     * @return UnidadeOrgValueObject
     * */
    public function setNuCnpj ($nuCnpj = NULL)
    {
        $this->_nuCnpj = $nuCnpj;
        return $this;
    }
}