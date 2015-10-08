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
namespace br\gov\mainapp\application\libcorp\endereco\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract,
    br\gov\mainapp\application\libcorp\municipio\mvcb\business\MunicipioBusiness,
    br\gov\mainapp\application\libcorp\tipoEndereco\mvcb\business\TipoEnderecoBusiness;

/**
  * SISICMBio
  *
  * @name EnderecoValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.endereco
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="endereco")
  * @author J. Augusto <augustowebd@gmail.com>
  * @author Michael Fernandes <michael.rodrigues@icmbio.gov.br>
  * @version $Id$
  * @log(name="all")
  * */
class EnderecoValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="sqEndereco",
     *  database="sq_endereco",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqEndereco",
     *  set="setSqEndereco"
     * )
     * */
     private $_sqEndereco;

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
     * @attr (
     *  name="sqTipoEndereco",
     *  database="sq_tipo_endereco",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoEndereco",
     *  set="setSqTipoEndereco"
     * )
     * */
     private $_sqTipoEndereco;

    /**
     * @attr (
     *  name="sqCep",
     *  database="sq_cep",
     *  type="string",
     *  nullable="FALSE",
     *  get="getSqCep",
     *  set="setSqCep"
     * )
     * */
     private $_sqCep;

    /**
     * @attr (
     *  name="sqPessoa",
     *  database="sq_pessoa",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqPessoa",
     *  set="setSqPessoa"
     * )
     * */
     private $_sqPessoa;

    /**
     * @attr (
     *  name="noBairro",
     *  database="no_bairro",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNoBairro",
     *  set="setNoBairro"
     * )
     * */
     private $_noBairro;

    /**
     * @attr (
     *  name="txEndereco",
     *  database="tx_endereco",
     *  type="string",
     *  nullable="FALSE",
     *  get="getTxEndereco",
     *  set="setTxEndereco"
     * )
     * */
     private $_txEndereco;

    /**
     * @attr (
     *  name="nuEndereco",
     *  database="nu_endereco",
     *  type="string",
     *  nullable="TRUE",
     *  get="getNuEndereco",
     *  set="setNuEndereco"
     * )
     * */
     private $_nuEndereco;

    /**
     * @attr (
     *  name="txComplemento",
     *  database="tx_complemento",
     *  type="string",
     *  nullable="TRUE",
     *  get="getTxComplemento",
     *  set="setTxComplemento"
     * )
     * */
     private $_txComplemento;

    /**
     * @attr (
     *  name="inCorrespondencia",
     *  database="in_correspondencia",
     *  type="boolean",
     *  nullable="TRUE",
     *  get="getInCorrespondencia",
     *  set="setInCorrespondencia"
     * )
     * */
     private $_inCorrespondencia;

    /**
     * @param integer $sqEndereco
     * @param integer $sqMunicipio
     * @param integer $sqTipoEndereco
     * @param string  $sqCep
     * @param integer $sqPessoa
     * @param string  $noBairro
     * @param string  $txEndereco
     * @param string  $nuEndereco
     * @param string  $txComplemento
     * @param boolean $inCorrespondencia
     * */
    public function __construct ($sqEndereco = NULL,
                                 $sqMunicipio = NULL,
                                 $sqTipoEndereco = NULL,
                                 $sqCep = NULL,
                                 $sqPessoa = NULL,
                                 $noBairro = NULL,
                                 $txEndereco = NULL,
                                 $nuEndereco = NULL,
                                 $txComplemento = NULL,
                                 $inCorrespondencia = NULL)
    {
        parent::__construct();
        $this->setSqEndereco($sqEndereco)
             ->setSqMunicipio($sqMunicipio)
             ->setSqTipoEndereco($sqTipoEndereco)
             ->setSqCep($sqCep)
             ->setSqPessoa($sqPessoa)
             ->setNoBairro($noBairro)
             ->setTxEndereco($txEndereco)
             ->setNuEndereco($nuEndereco)
             ->setTxComplemento($txComplemento)
             ->setInCorrespondencia($inCorrespondencia)
             ;
    }

    /**
     * @return integer
     * */
    public function getSqEndereco ()
    {
        return $this->_sqEndereco;
    }

    /**
     * @return MunicipioValueObject
     * */
    public function getSqMunicipio ()
    {
        if ((NULL != $this->_sqMunicipio) && !($this->_sqMunicipio instanceof parent)) {
            $this->_sqMunicipio = MunicipioBusiness::factory(NULL, 'libcorp')->find($this->_sqMunicipio);
        }
        return $this->_sqMunicipio;
    }

    /**
     * @return TipoEnderecoValueObject
     * */
    public function getSqTipoEndereco ()
    {
        if ((NULL != $this->_sqTipoEndereco) && !($this->_sqTipoEndereco instanceof parent)) {
            $this->_sqTipoEndereco = TipoEnderecoBusiness::factory(NULL, 'libcorp')->find($this->_sqTipoEndereco);
        }
        return $this->_sqTipoEndereco;
    }

    /**
     * @return integer
     * */
    public function getSqCep ()
    {
        return $this->_sqCep;
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
     * @return string
     * */
    public function getNoBairro ()
    {
        return $this->_noBairro;
    }

    /**
     * @return string
     * */
    public function getTxEndereco ()
    {
        return $this->_txEndereco;
    }

    /**
     * @return string
     * */
    public function getNuEndereco ()
    {
        return $this->_nuEndereco;
    }

    /**
     * @return string
     * */
    public function getTxComplemento ()
    {
        return $this->_txComplemento;
    }

    /**
     * @return boolean
     * */
    public function getInCorrespondencia ()
    {
        return $this->_inCorrespondencia;
    }

    /**
     * @param integer $sqEndereco
     * @return EnderecoValueObject
     * */
    public function setSqEndereco ($sqEndereco = NULL)
    {
        $this->_sqEndereco = $sqEndereco;
        return $this;
    }

    /**
     * @param integer $sqMunicipio
     * @return EnderecoValueObject
     * */
    public function setSqMunicipio ($sqMunicipio = NULL)
    {
        $this->_sqMunicipio = $sqMunicipio;
        return $this;
    }

    /**
     * @param integer $sqTipoEndereco
     * @return EnderecoValueObject
     * */
    public function setSqTipoEndereco ($sqTipoEndereco = NULL)
    {
        $this->_sqTipoEndereco = $sqTipoEndereco;
        return $this;
    }

    /**
     * @param integer $sqCep
     * @return EnderecoValueObject
     * */
    public function setSqCep ($sqCep = NULL)
    {
        $this->_sqCep = $sqCep;
        return $this;
    }

    /**
     * @param integer $sqPessoa
     * @return EnderecoValueObject
     * */
    public function setSqPessoa ($sqPessoa = NULL)
    {
        $this->_sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * @param string $noBairro
     * @return EnderecoValueObject
     * */
    public function setNoBairro ($noBairro = NULL)
    {
        $this->_noBairro = $noBairro;
        return $this;
    }

    /**
     * @param string $txEndereco
     * @return EnderecoValueObject
     * */
    public function setTxEndereco ($txEndereco = NULL)
    {
        $this->_txEndereco = $txEndereco;
        return $this;
    }

    /**
     * @param string $nuEndereco
     * @return EnderecoValueObject
     * */
    public function setNuEndereco ($nuEndereco = NULL)
    {
        $this->_nuEndereco = $nuEndereco;
        return $this;
    }

    /**
     * @param string $txComplemento
     * @return EnderecoValueObject
     * */
    public function setTxComplemento ($txComplemento = NULL)
    {
        $this->_txComplemento = $txComplemento;
        return $this;
    }

    /**
     * @param boolean $inCorrespondencia
     * @return EnderecoValueObject
     * */
    public function setInCorrespondencia ($inCorrespondencia = NULL)
    {
        $this->_inCorrespondencia = $inCorrespondencia;
        return $this;
    }
}
