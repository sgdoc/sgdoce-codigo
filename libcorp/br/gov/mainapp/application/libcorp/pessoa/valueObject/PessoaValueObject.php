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
namespace br\gov\mainapp\application\libcorp\pessoa\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness,
    br\gov\mainapp\application\libcorp\tipoPessoa\mvcb\business\TipoPessoaBusiness,
    br\gov\mainapp\application\libcorp\naturezaJuridica\mvcb\business\NaturezaJuridicaBusiness;

/**
  * SISICMBio
  *
  * @name PessoaValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.pessoa
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="pessoa")
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class PessoaValueObject extends ValueObjectAbstract
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
     *  name="sqTipoPessoa",
     *  database="sq_tipo_pessoa",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoPessoa",
     *  set="setSqTipoPessoa"
     * )
     * */
     private $_sqTipoPessoa;

    /**
     * @attr (
     *  name="noPessoa",
     *  database="no_pessoa",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNoPessoa",
     *  set="setNoPessoa"
     * )
     * */
     private $_noPessoa;

    /**
     * @attr (
     *  name="stRegistroAtivo",
     *  database="st_registro_ativo",
     *  type="boolean",
     *  get="getStRegistroAtivo",
     *  set="setStRegistroAtivo"
     * )
     * */
     private $_stRegistroAtivo;

    /**
     * @attr (
     *  name="sqPessoaHierarquia",
     *  database="sq_pessoa_hierarquia",
     *  type="integer",
     *  get="getSqPessoaHierarquia",
     *  set="setSqPessoaHierarquia"
     * )
     * */
     private $_sqPessoaHierarquia;

    /**
     * @attr (
     *  name="sqNaturezaJuridica",
     *  database="sq_natureza_juridica",
     *  type="integer",
     *  get="getSqNaturezaJuridica",
     *  set="setSqNaturezaJuridica"
     * )
     * */
     private $_sqNaturezaJuridica;

    /**
     * @param integer $sqPessoa
     * @param integer $sqTipoPessoa
     * @param string $noPessoa
     * @param boolean $stAtivo
     * @param integer $sqPessoaHierarquia
     * @param integer $sqNaturezaJuridica
     * */
    public function __construct ($sqPessoa = NULL,
                                 $noPessoa = NULL,
                                 $sqTipoPessoa = NULL,
                                 $stAtivo = NULL,
                                 $sqPessoaHierarquia = NULL,
                                 $sqNaturezaJuridica = NULL)
    {
        parent::__construct();
        $this->setNoPessoa($noPessoa)
             ->setSqTipoPessoa($sqTipoPessoa)
             ->setSqPessoa($sqPessoa)
             ->setStRegistroAtivo($stAtivo)
             ->setSqPessoaHierarquia($sqPessoaHierarquia)
             ->setSqNaturezaJuridica($sqNaturezaJuridica)
             ;
    }

    /**
     * @return integer
     **/
    public function getSqPessoa ()
    {
        return $this->_sqPessoa;
    }

    /**
     * apelido para getSqPessoa
     * @return integer
     */
    public function getSqPessoaResponsavel ()
    {
        return $this->_sqPessoa;
    }

    public function getSqPessoaContratada ()
    {
        return $this->_sqPessoa;
    }

    public function getSqPessoaRepresentante ()
    {
        return $this->_sqPessoa;
    }

    public function getSqFiscalIcmbio ()
    {
        return $this->_sqPessoa;
    }

    public function getSqGestorIcmbio ()
    {
        return $this->_sqPessoa;
    }

    /**
     * apelido para getSqPessoa
     * @return integer
     * */
    public function getSqUnidadeOrg ()
    {
        return $this->_sqPessoa;
    }

    /**
     * @return TipoPessoaValueObject
     */
    public function getSqTipoPessoa ()
    {
        if ((NULL != $this->_sqTipoPessoa) && !($this->_sqTipoPessoa instanceof parent)) {
            $this->_sqTipoPessoa = TipoPessoaBusiness::factory(NULL, 'libcorp')->find($this->_sqTipoPessoa);
        }

        return $this->_sqTipoPessoa;
    }

    /**
     * @return string
     */
    public function getNoPessoa ()
    {
        return $this->_noPessoa;
    }

    /**
     * @return boolean
     * */
    public function getStRegistroAtivo ()
    {
        return (boolean) $this->_stRegistroAtivo;
    }

    /**
     * @return integer
     * */
    public function getSqPessoaHierarquia ()
    {
        if ((NULL != $this->_sqPessoaHierarquia) && !($this->_sqPessoaHierarquia instanceof parent)) {
            $this->_sqPessoaHierarquia = PessoaBusiness::factory(NULL, 'libcorp')->find($this->_sqPessoaHierarquia);
        }
        return $this->_sqPessoaHierarquia;
    }

    /**
     * @return integer
     * */
    public function getSqNaturezaJuridica ()
    {
        if ((NULL != $this->_sqNaturezaJuridica) && !($this->_sqNaturezaJuridica instanceof parent)) {
            $this->_sqNaturezaJuridica = NaturezaJuridicaBusiness::factory(NULL, 'libcorp')->find($this->_sqNaturezaJuridica);
        }
        return $this->_sqNaturezaJuridica;
    }

    /**
     * @param integer $sqPessoa
     * @return PessoaValueObject
     */
    public function setSqPessoa ($sqPessoa = NULL)
    {
        $this->_sqPessoa = $sqPessoa;
        return $this;
    }


    /**
     * @param integer $sqTipoPessoa
     * @return PessoaValueObject
     * */
    public function setSqTipoPessoa ($sqTipoPessoa = NULL)
    {
        $this->_sqTipoPessoa = $sqTipoPessoa;
        return $this;
    }

    /**
     * @param string $noPessoa
     * @return PessoaValueObject
     * */
    public function setNoPessoa ($noPessoa= NULL)
    {
        $this->_noPessoa = $noPessoa;
        return $this;
    }

    /**
     * @param  integer $sqPessoa
     * @return PessoaValueObject
     * */
    public function getSqAvaliado ($sqPessoa = NULL)
    {
        return $this->_sqPessoa;
    }

    /**
     * @param  integer $sqPessoa
     * @return PessoaValueObject
     * */
    public function getSqAvaliador ($sqPessoa = NULL)
    {
        return $this->_sqPessoa;
    }

    /**
     * @param boolean $stRegistroAtivo
     * @return PessoaValueObject
     * */
    public function setStRegistroAtivo ($stRegistroAtivo = NULL)
    {
        $this->_stRegistroAtivo = (boolean) $stRegistroAtivo;
        return $this;
    }

    /**
     * @param integer $sqPessoaHierarquia
     * @return PessoaValueObject
     * */
    public function setSqPessoaHierarquia ($sqPessoaHierarquia = NULL)
    {
        $this->_sqPessoaHierarquia = $sqPessoaHierarquia;
        return $this;
    }

    /**
     * @param integer $sqNaturezaJuridica
     * @return PessoaValueObject
     * */
    public function setSqNaturezaJuridica ($sqNaturezaJuridica = NULL)
    {
        $this->_sqNaturezaJuridica = $sqNaturezaJuridica;
        return $this;
    }

    /**
     * @return integer
     */
    public function getSqPessoaRelacionamento ()
    {
        return $this->_sqPessoa;
    }
}