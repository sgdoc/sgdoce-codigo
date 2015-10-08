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
namespace br\gov\mainapp\application\libcorp\dadoBancario\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract,
    br\gov\mainapp\application\libcorp\agencia\mvcb\business\AgenciaBusiness,
    br\gov\mainapp\application\libcorp\tipoDadoBancario\mvcb\business\TipoDadoBancarioBusiness;

/**
  * SISICMBio
  *
  * @name DadoBancarioValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.dadoBancario
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="dado_bancario")
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class DadoBancarioValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="sqDadoBancario",
     *  database="sq_dado_bancario",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqDadoBancario",
     *  set="setSqDadoBancario"
     * )
     * */
     private $_sqDadoBancario;

    /**
     * @attr (
     *  name="sqAgencia",
     *  database="sq_agencia",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqAgencia",
     *  set="setSqAgencia"
     * )
     * */
     private $_sqAgencia;

    /**
     * @attr (
     *  name="nuConta",
     *  database="nu_conta",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNuConta",
     *  set="setNuConta"
     * )
     * */
     private $_nuConta;

    /**
     * @attr (
     *  name="nuContaDv",
     *  database="nu_conta_dv",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNuContaDv",
     *  set="setNuContaDv"
     * )
     * */
     private $_nuContaDv;

    /**
     * @attr (
     *  name="sqTipoDadoBancario",
     *  database="sq_tipo_dado_bancario",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoDadoBancario",
     *  set="setSqTipoDadoBancario"
     * )
     * */
     private $_sqTipoDadoBancario;

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
     *  name="coOperacao",
     *  database="co_operacao",
     *  type="string",
     *  nullable="TRUE",
     *  get="getCoOperacao",
     *  set="setCoOperacao"
     * )
     * */
     private $_coOperacao;

    /**
     * @param integer $sqDadoBancario
     * @param integer $sqAgencia
     * @param string $nuConta
     * @param string $nuContaDv
     * @param integer $sqTipoDadoBancario
     * @param integer $sqPessoa
     * @param integer $coOperacao
     * */
    public function __construct ($sqDadoBancario = NULL,
                                 $sqAgencia = NULL,
                                 $nuConta = NULL,
                                 $nuContaDv = NULL,
                                 $sqTipoDadoBancario = NULL,
                                 $sqPessoa = NULL,
                                 $coOperacao = NULL)
    {
        parent::__construct();
        $this->setSqDadoBancario($sqDadoBancario)
             ->setSqAgencia($sqAgencia)
             ->setNuConta($nuConta)
             ->setNuContaDv($nuContaDv)
             ->setSqTipoDadoBancario($sqTipoDadoBancario)
             ->setSqPessoa($sqPessoa)
             ->setCoOperacao($coOperacao)
             ;
    }

    /**
     * @return integer
     * */
    public function getSqDadoBancario ()
    {
        return $this->_sqDadoBancario;
    }

    /**
     * @return AgenciaValueObject
     * */
    public function getSqAgencia ()
    {
        if ((NULL != $this->_sqAgencia) && !($this->_sqAgencia instanceof parent)) {
            $this->_sqAgencia = AgenciaBusiness::factory(NULL, 'libcorp')->find($this->_sqAgencia);
        }
        return $this->_sqAgencia;
    }

    /**
     * @return string
     * */
    public function getNuConta ()
    {
        return $this->_nuConta;
    }

    /**
     * @return string
     * */
    public function getNuContaDv ()
    {
        return $this->_nuContaDv;
    }

    /**
     * @return TipoDadoBancarioValueObject
     * */
    public function getSqTipoDadoBancario ()
    {
        if ((NULL != $this->_sqTipoDadoBancario) && !($this->_sqTipoDadoBancario instanceof parent)) {
            $this->_sqTipoDadoBancario = TipoDadoBancarioBusiness::factory(NULL, 'libcorp')->find($this->_sqTipoDadoBancario);
        }
        return $this->_sqTipoDadoBancario;
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
     * @return integer
     * */
    public function getCoOperacao ()
    {
        return $this->_coOperacao;
    }

    /**
     * @param integer $sqDadoBancario
     * @return DadoBancarioValueObject
     * */
    public function setSqDadoBancario ($sqDadoBancario = NULL)
    {
        $this->_sqDadoBancario = $sqDadoBancario;
        return $this;
    }

    /**
     * @param integer $sqAgencia
     * @return DadoBancarioValueObject
     * */
    public function setSqAgencia ($sqAgencia = NULL)
    {
        $this->_sqAgencia = $sqAgencia;
        return $this;
    }

    /**
     * @param string $nuConta
     * @return DadoBancarioValueObject
     * */
    public function setNuConta ($nuConta = NULL)
    {
        $this->_nuConta = $nuConta;
        return $this;
    }

    /**
     * @param string $nuContaDv
     * @return DadoBancarioValueObject
     * */
    public function setNuContaDv ($nuContaDv = NULL)
    {
        $this->_nuContaDv = $nuContaDv;
        return $this;
    }

    /**
     * @param integer $sqTipoDadoBancario
     * @return DadoBancarioValueObject
     * */
    public function setSqTipoDadoBancario ($sqTipoDadoBancario = NULL)
    {
        $this->_sqTipoDadoBancario = $sqTipoDadoBancario;
        return $this;
    }

    /**
     * @param integer $sqPessoa
     * @return DadoBancarioValueObject
     * */
    public function setSqPessoa ($sqPessoa = NULL)
    {
        $this->_sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * @param integer $coOperacao
     * @return DadoBancarioValueObject
     * */
    public function setCoOperacao ($coOperacao = NULL)
    {
        $this->_coOperacao = $coOperacao;
        return $this;
    }
}