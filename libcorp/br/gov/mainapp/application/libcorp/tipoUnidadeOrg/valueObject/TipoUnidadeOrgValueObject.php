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
namespace br\gov\mainapp\application\libcorp\tipoUnidadeOrg\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\tipoUnidadeOrg\mvcb\business\TipoUnidadeOrgBusiness;

/**
  * SISICMBio
  *
  * @name TipoUnidadeOrgValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.tipoUnidadeOrg
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="tipo_unidade_org")
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class TipoUnidadeOrgValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="stRegistroAtivo",
     *  database="st_registro_ativo",
     *  type="boolean",
     *  nullable="FALSE",
     *  get="getStRegistroAtivo",
     *  set="setStRegistroAtivo"
     * )
     * */
     private $_stRegistroAtivo;

    /**
     * @attr (
     *  name="inEstrutura",
     *  database="in_estrutura",
     *  type="boolean",
     *  nullable="FALSE",
     *  get="getInEstrutura",
     *  set="setInEstrutura"
     * )
     * */
     private $_inEstrutura;

    /**
     * @attr (
     *  name="sgTipoUnidade",
     *  database="sg_tipo_unidade",
     *  type="string",
     *  nullable="FALSE",
     *  get="getSgTipoUnidade",
     *  set="setSgTipoUnidade"
     * )
     * */
     private $_sgTipoUnidade;

    /**
     * @attr (
     *  name="noTipoUnidadeOrg",
     *  database="no_tipo_unidade_org",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNoTipoUnidadeOrg",
     *  set="setNoTipoUnidadeOrg"
     * )
     * */
     private $_noTipoUnidadeOrg;

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
     *  name="sqTipoUnidadeOrg",
     *  database="sq_tipo_unidade_org",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoUnidadeOrg",
     *  set="setSqTipoUnidadeOrg"
     * )
     * */
     private $_sqTipoUnidadeOrg;

    /**
     * @param boolean $stRegistroAtivo
     * @param boolean $inEstrutura
     * @param string $sgTipoUnidade
     * @param string $noTipoUnidadeOrg
     * @param integer $sqTipoUnidadePai
     * @param integer $sqTipoUnidadeOrg
     * */
    public function __construct ($stRegistroAtivo = NULL,
                                 $inEstrutura = NULL,
                                 $sgTipoUnidade = NULL,
                                 $noTipoUnidadeOrg = NULL,
                                 $sqTipoUnidadePai = NULL,
                                 $sqTipoUnidadeOrg = NULL)
    {
        parent::__construct();
        $this->setStRegistroAtivo($stRegistroAtivo)
             ->setInEstrutura($inEstrutura)
             ->setSgTipoUnidade($sgTipoUnidade)
             ->setNoTipoUnidadeOrg($noTipoUnidadeOrg)
             ->setSqTipoUnidadePai($sqTipoUnidadePai)
             ->setSqTipoUnidadeOrg($sqTipoUnidadeOrg)
             ;
    }

    /**
     * @return boolean
     * */
    public function getStRegistroAtivo ()
    {
        return $this->_stRegistroAtivo;
    }

    /**
     * @return boolean
     * */
    public function getInEstrutura ()
    {
        return $this->_inEstrutura;
    }

    /**
     * @return string
     * */
    public function getSgTipoUnidade ()
    {
        return $this->_sgTipoUnidade;
    }

    /**
     * @return string
     * */
    public function getNoTipoUnidadeOrg ()
    {
        return $this->_noTipoUnidadeOrg;
    }

    /**
     * @return integer
     * */
    public function getSqTipoUnidadePai ()
    {
        if ((NULL != $this->_sqTipoUnidadePai) && !($this->_sqTipoUnidadePai instanceof parent)) {
            $this->_sqTipoUnidadePai = TipoUnidadeOrgBusiness::factory(NULL, 'libcorp')->find($this->_sqTipoUnidadePai);
        }
        return $this->_sqTipoUnidadePai;
    }

    /**
     * @return integer
     * */
    public function getSqTipoUnidadeOrg ()
    {
        return $this->_sqTipoUnidadeOrg;
    }
    
    public function getSqTipoUnidade ()
    {
        return $this->_sqTipoUnidadeOrg;
    }

    /**
     * @param boolean $stRegistroAtivo
     * @return TipoUnidadeOrgValueObject
     * */
    public function setStRegistroAtivo ($stRegistroAtivo = NULL)
    {
        $this->_stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }

    /**
     * @param boolean $inEstrutura
     * @return TipoUnidadeOrgValueObject
     * */
    public function setInEstrutura ($inEstrutura = NULL)
    {
        $this->_inEstrutura = $inEstrutura;
        return $this;
    }

    /**
     * @param string $sgTipoUnidade
     * @return TipoUnidadeOrgValueObject
     * */
    public function setSgTipoUnidade ($sgTipoUnidade = NULL)
    {
        $this->_sgTipoUnidade = $sgTipoUnidade;
        return $this;
    }

    /**
     * @param string $noTipoUnidadeOrg
     * @return TipoUnidadeOrgValueObject
     * */
    public function setNoTipoUnidadeOrg ($noTipoUnidadeOrg = NULL)
    {
        $this->_noTipoUnidadeOrg = $noTipoUnidadeOrg;
        return $this;
    }

    /**
     * @param integer $sqTipoUnidadePai
     * @return TipoUnidadeOrgValueObject
     * */
    public function setSqTipoUnidadePai ($sqTipoUnidadePai = NULL)
    {
        $this->_sqTipoUnidadePai = $sqTipoUnidadePai;
        return $this;
    }

    /**
     * @param integer $sqTipoUnidadeOrg
     * @return TipoUnidadeOrgValueObject
     * */
    public function setSqTipoUnidadeOrg ($sqTipoUnidadeOrg = NULL)
    {
        $this->_sqTipoUnidadeOrg = $sqTipoUnidadeOrg;
        return $this;
    }
}