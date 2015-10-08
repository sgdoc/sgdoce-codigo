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
namespace br\gov\mainapp\application\libcorp\naturezaJuridica\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\naturezaJuridica\mvcb\business\NaturezaJuridicaBusiness;

/**
  * SISICMBio
  *
  * @name NaturezaJuridicaValueObject
  * @package br.gov.mainapp.application.libcorp.naturezaJuridica
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="natureza_juridica")
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class NaturezaJuridicaValueObject extends ParentValueObject
{

    /**
     * @attr (
     *  name="noNaturezaJuridica",
     *  database="no_natureza_juridica",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNoNaturezaJuridica",
     *  set="setNoNaturezaJuridica"
     * )
     * */
     private $_noNaturezaJuridica;

    /**
     * @attr (
     *  name="sqNaturezaJuridicaPai",
     *  database="sq_natureza_juridica_pai",
     *  type="integer",
     *  nullable="TRUE",
     *  get="getSqNaturezaJuridicaPai",
     *  set="setSqNaturezaJuridicaPai"
     * )
     * */
     private $_sqNaturezaJuridicaPai;

    /**
     * @attr (
     *  name="sqNaturezaJuridica",
     *  database="sq_natureza_juridica",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqNaturezaJuridica",
     *  set="setSqNaturezaJuridica"
     * )
     * */
     private $_sqNaturezaJuridica;

    /**
     * @param string  $noNaturezaJuridica
     * @param integer $sqNaturezaJuridicaPai
     * @param integer $sqNaturezaJuridica
     * */
    public function __construct ($noNaturezaJuridica = NULL,
                                 $sqNaturezaJuridicaPai = NULL,
                                 $sqNaturezaJuridica = NULL)
    {
        parent::__construct();
        $this->setNoNaturezaJuridica($noNaturezaJuridica)
             ->setSqNaturezaJuridicaPai($sqNaturezaJuridicaPai)
             ->setSqNaturezaJuridica($sqNaturezaJuridica)
             ;
    }

    /**
     * @return string
     * */
    public function getNoNaturezaJuridica ()
    {
        return $this->_noNaturezaJuridica;
    }

    /**
     * @return integer
     * */
    public function getSqNaturezaJuridicaPai ()
    {
        if ((NULL != $this->_sqNaturezaJuridicaPai) && !($this->_sqNaturezaJuridicaPai instanceof parent)) {
            $this->_sqNaturezaJuridicaPai = NaturezaJuridicaBusiness::factory(NULL, 'libcorp')->find($this->_sqNaturezaJuridicaPai);
        }
        return $this->_sqNaturezaJuridicaPai;
    }

    /**
     * @return integer
     * */
    public function getSqNaturezaJuridica ()
    {
        return $this->_sqNaturezaJuridica;
    }

    /**
     * @param integer $noNaturezaJuridica
     * @return NaturezaJuridicaValueObject
     * */
    public function setNoNaturezaJuridica ($noNaturezaJuridica = NULL)
    {
        $this->_noNaturezaJuridica = $noNaturezaJuridica;
        return $this;
    }

    /**
     * @param integer $sqNaturezaJuridicaPai
     * @return NaturezaJuridicaValueObject
     * */
    public function setSqNaturezaJuridicaPai ($sqNaturezaJuridicaPai = NULL)
    {
        $this->_sqNaturezaJuridicaPai = $sqNaturezaJuridicaPai;
        return $this;
    }

    /**
     * @param integer $sqNaturezaJuridica
     * @return NaturezaJuridicaValueObject
     * */
    public function setSqNaturezaJuridica ($sqNaturezaJuridica = NULL)
    {
        $this->_sqNaturezaJuridica = $sqNaturezaJuridica;
        return $this;
    }
}