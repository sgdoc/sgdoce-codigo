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
namespace br\gov\mainapp\application\libcorp\pessoaJuridica\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness;

/**
  * SISICMBio
  *
  * @name PessoaJuridicaValueObject
  * @package br.gov.mainapp.application.libcorp.pessoaJuridica
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="pessoa_juridica")
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class PessoaJuridicaValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="dtAbertura",
     *  database="dt_abertura",
     *  type="timestamp",
     *  nullable="TRUE",
     *  get="getDtAbertura",
     *  set="setDtAbertura"
     * )
     * */
     private $_dtAbertura;

    /**
     * @attr (
     *  name="sgEmpresa",
     *  database="sg_empresa",
     *  type="string",
     *  nullable="TRUE",
     *  get="getSgEmpresa",
     *  set="setSgEmpresa"
     * )
     * */
     private $_sgEmpresa;

    /**
     * @attr (
     *  name="noFantasia",
     *  database="no_fantasia",
     *  type="string",
     *  nullable="TRUE",
     *  get="getNoFantasia",
     *  set="setNoFantasia"
     * )
     * */
     private $_noFantasia;

    /**
     * @attr (
     *  name="nuCnpj",
     *  database="nu_cnpj",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNuCnpj",
     *  set="setNuCnpj"
     * )
     * */
     private $_nuCnpj;

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
     *  name="inTipoEstabelecimento",
     *  database="in_tipo_estabelecimento",
     *  type="boolean",
     *  nullable="TRUE",
     *  get="getInTipoEstabelecimento",
     *  set="setInTipoEstabelecimento"
     * )
     * */
     private $_inTipoEstabelecimento;

    /**
     * @param timestamp $dtAbertura
     * @param string $sgEmpresa
     * @param string $noFantasia
     * @param string $nuCnpj
     * @param integer $sqPessoa
     * @param boolean $inTipoEstabelecimento
     * */
    public function __construct ($dtAbertura = NULL,
                                 $sgEmpresa = NULL,
                                 $noFantasia = NULL,
                                 $nuCnpj = NULL,
                                 $sqPessoa = NULL,
                                 $inTipoEstabelecimento = NULL)
    {
        parent::__construct();
        $this->setDtAbertura($dtAbertura)
             ->setSgEmpresa($sgEmpresa)
             ->setNoFantasia($noFantasia)
             ->setNuCnpj($nuCnpj)
             ->setSqPessoa($sqPessoa)
             ->setInTipoEstabelecimento($inTipoEstabelecimento)
             ;
    }

    /**
     * @return timestamp
     * */
    public function getDtAbertura ()
    {
        return $this->_dtAbertura;
    }

    /**
     * @return string
     * */
    public function getSgEmpresa ()
    {
        return $this->_sgEmpresa;
    }

    /**
     * @return string
     * */
    public function getNoFantasia ()
    {
        return $this->_noFantasia;
    }

    /**
     * @return string
     * */
    public function getNuCnpj ()
    {
        return $this->_nuCnpj;
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
     * @return PessoaJuridicaValueObject
     */
    public function getInTipoEstabelecimento ()
    {
        return $this->_inTipoEstabelecimento;
    }

    /**
     * @param timestamp $dtAbertura
     * @return PessoaJuridicaValueObject
     * */
    public function setDtAbertura ($dtAbertura = NULL)
    {
        $this->_dtAbertura = $dtAbertura;
        return $this;
    }

    /**
     * @param string $sgEmpresa
     * @return PessoaJuridicaValueObject
     * */
    public function setSgEmpresa ($sgEmpresa = NULL)
    {
        $this->_sgEmpresa = $sgEmpresa;
        return $this;
    }

    /**
     * @param string $noFantasia
     * @return PessoaJuridicaValueObject
     * */
    public function setNoFantasia ($noFantasia = NULL)
    {
        $this->_noFantasia = $noFantasia;
        return $this;
    }

    /**
     * @param string $nuCnpj
     * @return PessoaJuridicaValueObject
     * */
    public function setNuCnpj ($nuCnpj = NULL)
    {
        $this->_nuCnpj = $nuCnpj;
        return $this;
    }

    /**
     * @param integer $sqPessoa
     * @return PessoaJuridicaValueObject
     * */
    public function setSqPessoa ($sqPessoa = NULL)
    {
        $this->_sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * @param boolean $inTipoEstabelecimento
     * @return PessoaJuridicaValueObject
     */
    public function setInTipoEstabelecimento ($inTipoEstabelecimento = NULL)
    {
        $this->_inTipoEstabelecimento = $inTipoEstabelecimento;
        return $this;
    }
}