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
namespace br\gov\mainapp\application\libcorp\pessoaFisica\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\pais\mvcb\business\PaisBusiness,
    br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness,
    br\gov\mainapp\application\libcorp\municipio\mvcb\business\MunicipioBusiness,
    br\gov\mainapp\application\libcorp\estadoCivil\mvcb\business\EstadoCivilBusiness;

/**
  * SISICMBio
  *
  * @name PessoaFisicaValueObject
  * @package br.gov.mainapp.application.libcorp.pessoaFisica
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="pessoa_fisica")
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class PessoaFisicaValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="noProfissao",
     *  database="no_profissao",
     *  type="string",
     *  nullable="TRUE",
     *  get="getNoProfissao",
     *  set="setNoProfissao"
     * )
     * */
     private $_noProfissao;

    /**
     * @attr (
     *  name="dtNascimento",
     *  database="dt_nascimento",
     *  type="timestamp",
     *  nullable="TRUE",
     *  get="getDtNascimento",
     *  set="setDtNascimento"
     * )
     * */
     private $_dtNascimento;

    /**
     * @attr (
     *  name="nuCurriculoLates",
     *  database="nu_curriculo_lates",
     *  type="string",
     *  nullable="TRUE",
     *  get="getNuCurriculoLates",
     *  set="setNuCurriculoLates"
     * )
     * */
     private $_nuCurriculoLates;

    /**
     * @attr (
     *  name="sgSexo",
     *  database="sg_sexo",
     *  type="string",
     *  nullable="TRUE",
     *  get="getSgSexo",
     *  set="setSgSexo"
     * )
     * */
     private $_sgSexo;

    /**
     * @attr (
     *  name="noPai",
     *  database="no_pai",
     *  type="string",
     *  nullable="TRUE",
     *  get="getNoPai",
     *  set="setNoPai"
     * )
     * */
     private $_noPai;

    /**
     * @attr (
     *  name="noMae",
     *  database="no_mae",
     *  type="string",
     *  nullable="TRUE",
     *  get="getNoMae",
     *  set="setNoMae"
     * )
     * */
     private $_noMae;

    /**
     * @attr (
     *  name="nuCpf",
     *  database="nu_cpf",
     *  ldap="uid",
     *  keyLdap="uid",
     *  type="string",
     *  nullable="TRUE",
     *  get="getNuCpf",
     *  set="setNuCpf"
     * )
     * */
     private $_nuCpf;

    /**
     * @attr (
     *  name="sqEstadoCivil",
     *  database="sq_estado_civil",
     *  type="integer",
     *  nullable="TRUE",
     *  get="getSqEstadoCivil",
     *  set="setSqEstadoCivil"
     * )
     * */
     private $_sqEstadoCivil;

    /**
     * @attr (
     *  name="sqNaturalidade",
     *  database="sq_naturalidade",
     *  type="integer",
     *  nullable="TRUE",
     *  foreingKeyAlias="sqMunicipio",
     *  get="getSqNaturalidade",
     *  set="setSqNaturalidade"
     * )
     * */
     private $_sqNaturalidade;

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
     *  name="sqNacionalidade",
     *  database="sq_nacionalidade",
     *  type="integer",
     *  nullable="TRUE",
     *  foreingKeyAlias="sqPais",
     *  get="getSqNacionalidade",
     *  set="setSqNacionalidade"
     * )
     * */
     private $_sqNacionalidade;

    /**
     * @param string $noProfissao
     * @param timestamp $dtNascimento
     * @param string $nuCurriculoLates
     * @param string $sgSexo
     * @param string $noPai
     * @param string $noMae
     * @param string $nuCpf
     * @param integer $sqEstadoCivil
     * @param integer $sqPessoa
     * @param integer $sqNaturalidade
     * @param integer $sqNacionalidade
     * */
    public function __construct ($noProfissao = NULL,
                                 $dtNascimento = NULL,
                                 $nuCurriculoLates = NULL,
                                 $sgSexo = NULL,
                                 $noPai = NULL,
                                 $noMae = NULL,
                                 $nuCpf = NULL,
                                 $sqEstadoCivil = NULL,
                                 $sqPessoa = NULL,
                                 $sqNaturalidade = NULL,
                                 $sqNacionalidade = NULL)
    {
        parent::__construct();
        $this->setNoProfissao($noProfissao)
             ->setDtNascimento($dtNascimento)
             ->setNuCurriculoLates($nuCurriculoLates)
             ->setSgSexo($sgSexo)
             ->setNoPai($noPai)
             ->setNoMae($noMae)
             ->setNuCpf($nuCpf)
             ->setSqEstadoCivil($sqEstadoCivil)
             ->setSqPessoa($sqPessoa)
             ->setSqNaturalidade($sqNaturalidade)
             ->setSqNacionalidade($sqNacionalidade)
             ;
    }

    /**
     * @return integer
     * */
    public function getSqAgenteFiscalizacao ()
    {
        return $this->getSqPessoa()->getSqPessoa();
    }

    /**
     * @return string
     * */
    public function getNoProfissao ()
    {
        return $this->_noProfissao;
    }

    /**
     * @return timestamp
     * */
    public function getDtNascimento ()
    {
        return $this->_dtNascimento;
    }

    /**
     * @return string
     * */
    public function getNuCurriculoLates ()
    {
        return $this->_nuCurriculoLates;
    }

    /**
     * @return string
     * */
    public function getSgSexo ()
    {
        return $this->_sgSexo;
    }

    /**
     * @return string
     * */
    public function getNoPai ()
    {
        return $this->_noPai;
    }

    /**
     * @return string
     * */
    public function getNoMae ()
    {
        return $this->_noMae;
    }

    /**
     * @return string
     * */
    public function getNuCpf ()
    {
        return $this->_nuCpf;
    }

    /**
     * @return EstadoCivilValueObject
     * */
    public function getSqEstadoCivil ()
    {
        if ((NULL != $this->_sqEstadoCivil) && !($this->_sqEstadoCivil instanceof parent)) {
            $this->_sqEstadoCivil = EstadoCivilBusiness::factory(NULL, 'libcorp')->find($this->_sqEstadoCivil);
        }
        return $this->_sqEstadoCivil;
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
     * @return MunicipioValueObject
     * */
    public function getSqNaturalidade ()
    {
        if ((NULL != $this->_sqNaturalidade) && !($this->_sqNaturalidade instanceof parent)) {
            $this->_sqNaturalidade = MunicipioBusiness::factory(NULL, 'libcorp')->find($this->_sqNaturalidade);
        }
        return $this->_sqNaturalidade;
    }

    /**
     * @return PaisValueObject
     * */
    public function getSqNacionalidade ()
    {
        if ((NULL != $this->_sqNacionalidade) && !($this->_sqNacionalidade instanceof parent)) {
            $this->_sqNacionalidade = PaisBusiness::factory(NULL, 'libcorp')->find($this->_sqNacionalidade);
        }
        return $this->_sqNacionalidade;
    }

    /**
     * @param string $noProfissao
     * @return PessoaFisicaValueObject
     * */
    public function setNoProfissao ($noProfissao = NULL)
    {
        $this->_noProfissao = $noProfissao;
        return $this;
    }

    /**
     * @param timestamp $dtNascimento
     * @return PessoaFisicaValueObject
     * */
    public function setDtNascimento ($dtNascimento = NULL)
    {
        $this->_dtNascimento = $dtNascimento;
        return $this;
    }

    /**
     * @param string $nuCurriculoLates
     * @return PessoaFisicaValueObject
     * */
    public function setNuCurriculoLates ($nuCurriculoLates = NULL)
    {
        $this->_nuCurriculoLates = $nuCurriculoLates;
        return $this;
    }

    /**
     * @param string $sgSexo
     * @return PessoaFisicaValueObject
     * */
    public function setSgSexo ($sgSexo = NULL)
    {
        $this->_sgSexo = $sgSexo;
        return $this;
    }

    /**
     * @param string $noPai
     * @return PessoaFisicaValueObject
     * */
    public function setNoPai ($noPai = NULL)
    {
        $this->_noPai = $noPai;
        return $this;
    }

    /**
     * @param string $noMae
     * @return PessoaFisicaValueObject
     * */
    public function setNoMae ($noMae = NULL)
    {
        $this->_noMae = $noMae;
        return $this;
    }

    /**
     * @param string $nuCpf
     * @return PessoaFisicaValueObject
     * */
    public function setNuCpf ($nuCpf = NULL)
    {
        $this->_nuCpf = $nuCpf;
        return $this;
    }

    /**
     * @param integer $sqEstadoCivil
     * @return PessoaFisicaValueObject
     * */
    public function setSqEstadoCivil ($sqEstadoCivil = NULL)
    {
        $this->_sqEstadoCivil = $sqEstadoCivil;
        return $this;
    }

    /**
     * @param integer $sqPessoa
     * @return PessoaFisicaValueObject
     * */
    public function setSqPessoa ($sqPessoa = NULL)
    {
        $this->_sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * @param integer $sqNaturalidade
     * @return PessoaFisicaValueObject
     * */
    public function setSqNaturalidade ($sqNaturalidade = NULL)
    {
        $this->_sqNaturalidade = $sqNaturalidade;
        return $this;
    }

    /**
     * @param integer $sqNacionalidade
     * @return PessoaFisicaValueObject
     * */
    public function setSqNacionalidade ($sqNacionalidade = NULL)
    {
        $this->_sqNacionalidade = $sqNacionalidade;
        return $this;
    }
}
