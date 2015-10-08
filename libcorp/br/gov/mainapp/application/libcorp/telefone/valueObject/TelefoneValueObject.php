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
namespace br\gov\mainapp\application\libcorp\telefone\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness,
    br\gov\mainapp\application\libcorp\tipoTelefone\mvcb\business\TipoTelefoneBusiness;

/**
  * SISICMBio
  *
  * @name TelefoneValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.telefone
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="telefone")
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class TelefoneValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="nuTelefone",
     *  database="nu_telefone",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNuTelefone",
     *  set="setNuTelefone"
     * )
     * */
     private $_nuTelefone;

    /**
     * @attr (
     *  name="nuDdd",
     *  database="nu_ddd",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNuDdd",
     *  set="setNuDdd"
     * )
     * */
     private $_nuDdd;

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
     *  name="sqTipoTelefone",
     *  database="sq_tipo_telefone",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoTelefone",
     *  set="setSqTipoTelefone"
     * )
     * */
     private $_sqTipoTelefone;

    /**
     * @attr (
     *  name="sqTelefone",
     *  database="sq_telefone",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTelefone",
     *  set="setSqTelefone"
     * )
     * */
     private $_sqTelefone;

    /**
     * @param string $nuTelefone
     * @param string $nuDdd
     * @param integer $sqPessoa
     * @param integer $sqTipoTelefone
     * @param integer $sqTelefone
     * */
    public function __construct ($nuTelefone = NULL,
                                 $nuDdd = NULL,
                                 $sqPessoa = NULL,
                                 $sqTipoTelefone = NULL,
                                 $sqTelefone = NULL)
    {
        parent::__construct();
        $this->setNuTelefone($nuTelefone)
             ->setNuDdd($nuDdd)
             ->setSqPessoa($sqPessoa)
             ->setSqTipoTelefone($sqTipoTelefone)
             ->setSqTelefone($sqTelefone)
             ;
    }

    /**
     * @return string
     * */
    public function getNuTelefone ()
    {
        return $this->_nuTelefone;
    }

    /**
     * @return string
     * */
    public function getNuDdd ()
    {
        return $this->_nuDdd;
    }

    /**
     * @return PessoaValueObject
     * */
    public function getSqPessoa ()
    {
        if (!($this->_sqPessoa instanceof parent)) {
            $this->_sqPessoa = PessoaBusiness::factory(NULL, 'libcorp')->find($this->_sqPessoa);
        }
        return $this->_sqPessoa;
    }

    /**
     * @return TipoTelefoneValueObject
     * */
    public function getSqTipoTelefone ()
    {
        if (!($this->_sqTipoTelefone instanceof parent)) {
            $this->_sqTipoTelefone = TipoTelefoneBusiness::factory(NULL, 'libcorp')->find($this->_sqTipoTelefone);
        }
        return $this->_sqTipoTelefone;
    }

    /**
     * @return integer
     * */
    public function getSqTelefone ()
    {
        return $this->_sqTelefone;
    }

    /**
     * @param string $nuTelefone
     * @return TelefoneValueObject
     * */
    public function setNuTelefone ($nuTelefone = NULL)
    {
        $this->_nuTelefone = $nuTelefone;
        return $this;
    }

    /**
     * @param string $nuDdd
     * @return TelefoneValueObject
     * */
    public function setNuDdd ($nuDdd = NULL)
    {
        $this->_nuDdd = $nuDdd;
        return $this;
    }

    /**
     * @param integer $sqPessoa
     * @return TelefoneValueObject
     * */
    public function setSqPessoa ($sqPessoa = NULL)
    {
        $this->_sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * @param integer $sqTipoTelefone
     * @return TelefoneValueObject
     * */
    public function setSqTipoTelefone ($sqTipoTelefone = NULL)
    {
        $this->_sqTipoTelefone = $sqTipoTelefone;
        return $this;
    }

    /**
     * @param integer $sqTelefone
     * @return TelefoneValueObject
     * */
    public function setSqTelefone ($sqTelefone = NULL)
    {
        $this->_sqTelefone = $sqTelefone;
        return $this;
    }
}