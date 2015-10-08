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
namespace br\gov\mainapp\application\libcorp\cepLogradouro\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract;

/**
  * SISICMBio
  *
  * @name CepLogradouroValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.cepLogradouro
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="cep_logradouro")
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class CepLogradouroValueObject extends ValueObjectAbstract
{
    /**
     * @attr (
     *  name="sqCepLogradouro",
     *  database="sq_cep_logradouro",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqCepLogradouro",
     *  set="setSqCepLogradouro"
     * )
     * */
     private $_sqCepLogradouro;

    /**
     * @attr (
     *  name="sgUfLogradouro",
     *  database="sg_uf_logradouro",
     *  type="string",
     *  nullable="FALSE",
     *  get="getSgUfLogradouro",
     *  set="setSgUfLogradouro"
     * )
     * */
     private $_sgUfLogradouro;

    /**
     * @attr (
     *  name="sqLocalidade",
     *  database="sq_localidade",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqLocalidade",
     *  set="setSqLocalidade"
     * )
     * */
     private $_sqLocalidade;

    /**
     * @attr (
     *  name="sqBairroInicial",
     *  database="sq_bairro_inicial",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqBairroInicial",
     *  set="setSqBairroInicial"
     * )
     * */
     private $_sqBairroInicial;

    /**
     * @attr (
     *  name="sqBairroFinal",
     *  database="sq_bairro_final",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqBairroFinal",
     *  set="setSqBairroFinal"
     * )
     * */
     private $_sqBairroFinal;

    /**
     * @attr (
     *  name="noLogradouro",
     *  database="no_logradouro",
     *  type="string",
     *  get="getNoLogradouro",
     *  set="setNoLogradouro"
     * )
     * */
     private $_noLogradouro;

    /**
     * @attr (
     *  name="txComplemento",
     *  database="tx_complemento",
     *  type="string",
     *  get="getTxComplemento",
     *  set="setTxComplemento"
     * )
     * */
     private $_txComplemento;

    /**
     * @attr (
     *  name="coCep",
     *  database="co_cep",
     *  type="string",
     *  get="getCoCep",
     *  set="setCoCep"
     * )
     * */
     private $_coCep;

    /**
     * @attr (
     *  name="noTipoLogradouro",
     *  database="no_tipo_logradouro",
     *  type="string",
     *  get="getNoTipoLogradouro",
     *  set="setNoTipoLogradouro"
     * )
     * */
     private $_noTipoLogradouro;

    /**
     * @attr (
     *  name="stUtilizacao",
     *  database="st_utilizacao",
     *  type="boolean",
     *  get="getStUtilizacao",
     *  set="setStUtilizacao"
     * )
     * */
     private $_stUtilizacao;

    /**
     * @attr (
     *  name="txAbreviacao",
     *  database="tx_abreviacao",
     *  type="string",
     *  get="getTxAbreviacao",
     *  set="setTxAbreviacao"
     * )
     * */
     private $_txAbreviacao;

    /**
     * @param integer sqCepLogradouro
     * @param string  sgUfLogradouro
     * @param integer sqLocalidade
     * @param integer sqBairroInicial
     * @param integer sqBairroFinal
     * @param string  noLogradouro
     * @param string  txComplemento
     * @param string  coCep
     * @param integer noTipoLogradouro
     * @param boolean stUtilizacao
     * @param string  txAbreviacao
     * */
    public function __construct ($sqCepLogradouro = NULL,
                                 $sgUfLogradouro = NULL,
                                 $sqLocalidade = NULL,
                                 $sqBairroInicial = NULL,
                                 $sqBairroFinal = NULL,
                                 $noLogradouro = NULL,
                                 $txComplemento = NULL,
                                 $coCep = NULL,
                                 $noTipoLogradouro = NULL,
                                 $stUtilizacao = NULL,
                                 $txAbreviacao = NULL
            )
    {
        parent::__construct();
        $this->setSqCepLogradouro($sqCepLogradouro)
             ->setSgUfLogradouro($sgUfLogradouro)
             ->setSqLocalidade($sqLocalidade)
             ->setSqBairroInicial($sqBairroInicial)
             ->setSqBairroFinal($sqBairroFinal)
             ->setNoLogradouro($noLogradouro)
             ->setTxComplemento($txComplemento)
             ->setCoCep($coCep)
             ->setNoTipoLogradouro($noTipoLogradouro)
             ->setStUtilizacao($stUtilizacao)
             ->setTxAbreviacao($txAbreviacao)
             ;
    }

    /**
     * @return integer
     * */
    public function getSqCepLogradouro ()
    {
        return $this->_sqCepLogradouro;
    }

    /**
     * @return string
     * */
    public function getSgUfLogradouro ()
    {
        return $this->_sgUfLogradouro;
    }

    /**
     * @return integer
     * */
    public function getSqLocalidade ()
    {
        return $this->_sqLocalidade;
    }

    /**
     * @return integer
     * */
    public function getSqBairroInicial ()
    {
        return $this->_sqBairroInicial;
    }

    /**
     * @return integer
     * */
    public function getSqBairroFinal ()
    {
        return $this->_sqBairroFinal;
    }

    /**
     * @return string
     * */
    public function getNoLogradouro ()
    {
        return $this->_noLogradouro;
    }

    /**
     * @return string
     * */
    public function getTxComplemento ()
    {
        return $this->_txComplemento;
    }

    /**
     * @return string
     * */
    public function getCoCep ()
    {
        return $this->_coCep;
    }

    /**
     * @return string
     * */
    public function getNoTipoLogradouro ()
    {
        return $this->_noTipoLogradouro;
    }

    /**
     * @return boolean
     * */
    public function getStUtilizacao ()
    {
        return $this->_stUtilizacao;
    }

    /**
     * @return string
     * */
    public function getTxAbreviacao ()
    {
        return $this->_txAbreviacao;
    }

    /**
     * @param integer $sqCepLogradouro
     * @return br\gov\icmbio\sisicmbio\application\libcorp\cepLogradouro\valueObject\CepLogradouroValueObject
     * */
    public function setSqCepLogradouro ($sqCepLogradouro = NULL)
    {
        $this->_sqCepLogradouro = $sqCepLogradouro;
        return $this;
    }

    /**
     * @param string $sgUfLogradouro
     * @return br\gov\icmbio\sisicmbio\application\libcorp\cepLogradouro\valueObject\CepLogradouroValueObject
     * */
    public function setSgUfLogradouro ($sgUfLogradouro = NULL)
    {
        $this->_sgUfLogradouro = $sgUfLogradouro;
        return $this;
    }

    /**
     * @param integer $sqLocalidade
     * @return br\gov\icmbio\sisicmbio\application\libcorp\cepLogradouro\valueObject\CepLogradouroValueObject
     * */
    public function setSqLocalidade ($sqLocalidade = NULL)
    {
        $this->_sqLocalidade = $sqLocalidade;
        return $this;
    }

    /**
     * @param integer $sqBairroInicial
     * @return br\gov\icmbio\sisicmbio\application\libcorp\cepLogradouro\valueObject\CepLogradouroValueObject
     * */
    public function setSqBairroInicial ($sqBairroInicial = NULL)
    {
        $this->_sqBairroInicial = $sqBairroInicial;
        return $this;
    }

    /**
     * @param integer $sqBairroFinal
     * @return br\gov\icmbio\sisicmbio\application\libcorp\cepLogradouro\valueObject\CepLogradouroValueObject
     * */
    public function setSqBairroFinal ($sqBairroFinal = NULL)
    {
        $this->_sqBairroFinal = $sqBairroFinal;
        return $this;
    }

    /**
     * @param string
     * @return br\gov\icmbio\sisicmbio\application\libcorp\cepLogradouro\valueObject\CepLogradouroValueObject
     * */
    public function setNoLogradouro ($noLogradouro = NULL)
    {
        $this->_noLogradouro = $noLogradouro;
        return $this;
    }

    /**
     * @param string
     * @return br\gov\icmbio\sisicmbio\application\libcorp\cepLogradouro\valueObject\CepLogradouroValueObject
     * */
    public function setTxComplemento ($txComplemento = NULL)
    {
        $this->_txComplemento = $txComplemento;
        return $this;
    }

    /**
     * @param string
     * @return br\gov\icmbio\sisicmbio\application\libcorp\cepLogradouro\valueObject\CepLogradouroValueObject
     * */
    public function setCoCep ($coCep = NULL)
    {
        $this->_coCep = $coCep;
        return $this;
    }

    /**
     * @param string
     * @return br\gov\icmbio\sisicmbio\application\libcorp\cepLogradouro\valueObject\CepLogradouroValueObject
     * */
    public function setNoTipoLogradouro ($noTipoLogradouro = NULL)
    {
        $this->_noTipoLogradouro = $noTipoLogradouro;
        return $this;
    }

    /**
     * @param boolean
     * @return br\gov\icmbio\sisicmbio\application\libcorp\cepLogradouro\valueObject\CepLogradouroValueObject
     * */
    public function setStUtilizacao ($stUtilizacao = NULL)
    {
        $this->_stUtilizacao = $stUtilizacao;
        return $this;
    }

    /**
     * @param string
     * @return br\gov\icmbio\sisicmbio\application\libcorp\cepLogradouro\valueObject\CepLogradouroValueObject
     * */
    public function setTxAbreviacao ($txAbreviacao = NULL)
    {
        $this->_txAbreviacao = $txAbreviacao;
        return $this;
    }
}