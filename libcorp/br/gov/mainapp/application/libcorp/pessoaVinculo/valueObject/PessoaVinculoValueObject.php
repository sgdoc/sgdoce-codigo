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
namespace br\gov\mainapp\application\libcorp\pessoaVinculo\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness,
    br\gov\mainapp\application\libcorp\tipoVinculo\mvcb\business\TipoVinculoBusiness;

/**
  * SISICMBio
  *
  * @name PessoaVinculoValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.pessoaVinculo
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="pessoa_vinculo")
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class PessoaVinculoValueObject extends ParentValueObject
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
     *  name="dtFimVinculo",
     *  database="dt_fim_vinculo",
     *  type="timestamp",
     *  nullable="TRUE",
     *  get="getDtFimVinculo",
     *  set="setDtFimVinculo"
     * )
     * */
     private $_dtFimVinculo;

    /**
     * @attr (
     *  name="dtInicioVinculo",
     *  database="dt_inicio_vinculo",
     *  type="timestamp",
     *  nullable="TRUE",
     *  get="getDtInicioVinculo",
     *  set="setDtInicioVinculo"
     * )
     * */
     private $_dtInicioVinculo;

    /**
     * @attr (
     *  name="noCargo",
     *  database="no_cargo",
     *  type="string",
     *  nullable="TRUE",
     *  get="getNoCargo",
     *  set="setNoCargo"
     * )
     * */
     private $_noCargo;

    /**
     * @attr (
     *  name="sqTipoVinculo",
     *  database="sq_tipo_vinculo",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoVinculo",
     *  set="setSqTipoVinculo"
     * )
     * */
     private $_sqTipoVinculo;

    /**
     * @attr (
     *  name="sqPessoaRelacionamento",
     *  database="sq_pessoa_relacionamento",
     *  type="integer",
     *  nullable="FALSE",
     *  foreingKeyAlias="sqPessoa",
     *  get="getSqPessoaRelacionamento",
     *  set="setSqPessoaRelacionamento"
     * )
     * */
     private $_sqPessoaRelacionamento;

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
     *  name="sqPessoaVinculo",
     *  database="sq_pessoa_vinculo",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqPessoaVinculo",
     *  set="setSqPessoaVinculo"
     * )
     * */
     private $_sqPessoaVinculo;

    /**
     * @param boolean $stRegistroAtivo
     * @param timestamp $dtFimVinculo
     * @param timestamp $dtInicioVinculo
     * @param string $noCargo
     * @param integer $sqTipoVinculo
     * @param integer $sqPessoaRelacionamento
     * @param integer $sqPessoa
     * @param integer $sqPessoaVinculo
     * */
    public function __construct ($stRegistroAtivo = NULL,
                                 $dtFimVinculo = NULL,
                                 $dtInicioVinculo = NULL,
                                 $noCargo = NULL,
                                 $sqTipoVinculo = NULL,
                                 $sqPessoaRelacionamento = NULL,
                                 $sqPessoa = NULL,
                                 $sqPessoaVinculo = NULL)
    {
        parent::__construct();
        $this->setStRegistroAtivo($stRegistroAtivo)
             ->setDtFimVinculo($dtFimVinculo)
             ->setDtInicioVinculo($dtInicioVinculo)
             ->setNoCargo($noCargo)
             ->setSqTipoVinculo($sqTipoVinculo)
             ->setSqPessoaRelacionamento($sqPessoaRelacionamento)
             ->setSqPessoa($sqPessoa)
             ->setSqPessoaVinculo($sqPessoaVinculo);
    }

    /**
     * @return boolean
     * */
    public function getStRegistroAtivo ()
    {
        return $this->_stRegistroAtivo;
    }

    /**
     * @return timestamp
     * */
    public function getDtFimVinculo ()
    {
        return $this->_dtFimVinculo;
    }

    /**
     * @return timestamp
     * */
    public function getDtInicioVinculo ()
    {
        return $this->_dtInicioVinculo;
    }

    /**
     * @return string
     * */
    public function getNoCargo ()
    {
        return $this->_noCargo;
    }

    /**
     * @return TipoVinculoValueObject
     * */
    public function getSqTipoVinculo ()
    {
        if ((NULL != $this->_sqTipoVinculo) && !($this->_sqTipoVinculo instanceof parent)) {
            $this->_sqTipoVinculo = TipoVinculoBusiness::factory(NULL, 'libcorp')->find($this->_sqTipoVinculo);
        }
        return $this->_sqTipoVinculo;
    }

    /**
     * @return PessoaValueObject
     * */
    public function getSqPessoaRelacionamento ()
    {
        if ((NULL != $this->_sqPessoaRelacionamento) && !($this->_sqPessoaRelacionamento instanceof parent)) {
            $this->_sqPessoaRelacionamento = PessoaBusiness::factory(NULL, 'libcorp')->find($this->_sqPessoaRelacionamento);
        }
        return $this->_sqPessoaRelacionamento;
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
    public function getSqPessoaVinculo ()
    {
        return $this->_sqPessoaVinculo;
    }

    /**
     * @param boolean $stRegistroAtivo
     * @return PessoaVinculoValueObject
     * */
    public function setStRegistroAtivo ($stRegistroAtivo = NULL)
    {
        $this->_stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }

    /**
     * @param timestamp $dtFimVinculo
     * @return PessoaVinculoValueObject
     * */
    public function setDtFimVinculo ($dtFimVinculo = NULL)
    {
        $this->_dtFimVinculo = $dtFimVinculo;
        return $this;
    }

    /**
     * @param timestamp $dtInicioVinculo
     * @return PessoaVinculoValueObject
     * */
    public function setDtInicioVinculo ($dtInicioVinculo = NULL)
    {
        $this->_dtInicioVinculo = $dtInicioVinculo;
        return $this;
    }

    /**
     * @param string $noCargo
     * @return PessoaVinculoValueObject
     * */
    public function setNoCargo ($noCargo = NULL)
    {
        $this->_noCargo = $noCargo;
        return $this;
    }

    /**
     * @param integer $sqTipoVinculo
     * @return PessoaVinculoValueObject
     * */
    public function setSqTipoVinculo ($sqTipoVinculo = NULL)
    {
        $this->_sqTipoVinculo = $sqTipoVinculo;
        return $this;
    }

    /**
     * @param integer $sqPessoaRelacionamento
     * @return PessoaVinculoValueObject
     * */
    public function setSqPessoaRelacionamento ($sqPessoaRelacionamento = NULL)
    {
        $this->_sqPessoaRelacionamento = $sqPessoaRelacionamento;
        return $this;
    }

    /**
     * @param integer $sqPessoa
     * @return PessoaVinculoValueObject
     * */
    public function setSqPessoa ($sqPessoa = NULL)
    {
        $this->_sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * @param integer $sqPessoaVinculo
     * @return PessoaVinculoValueObject
     * */
    public function setSqPessoaVinculo ($sqPessoaVinculo = NULL)
    {
        $this->_sqPessoaVinculo = $sqPessoaVinculo;
        return $this;
    }
}