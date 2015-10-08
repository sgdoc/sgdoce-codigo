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
namespace br\gov\mainapp\application\libcorp\tipoPessoa\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject;

/**
  * SISICMBio
  *
  * @name TipoPessoaValueObject
  * @package br.gov.mainapp.application.libcorp.tipoPessoa
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="tipo_pessoa")
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class TipoPessoaValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="sqTipoPessoa",
     *  database="sq_tipo_pessoa",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoPessoa",
     *  set="setSqTipoPessoa"
     * )
     * */
     private $_sqTipoPessoa;

    /**
     * @attr (
     *  name="noTipoPessoa",
     *  database="no_tipo_pessoa",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNoTipoPessoa",
     *  set="setNoTipoPessoa"
     * )
     * */
     private $_noTipoPessoa;

     /**
      * @param integer $sqTipoPessoa
      * @param integer $noTipoPessoa
      * @param string $noToken
      * */
     public function __construct ($sqTipoPessoa = NULL,
                                  $noTipoPessoa = NULL)
     {
         parent::__construct();
         $this->setSqTipoPessoa($sqTipoPessoa)
              ->setNoTipoPessoa($noTipoPessoa);
     }

    /**
     * @return integer
     */
    public function getSqTipoPessoa ()
    {
        return $this->_sqTipoPessoa;
    }

    /**
     * @return string
     */
    public function getNoTipoPessoa ()
    {
        return $this->_noTipoPessoa;
    }

    /**
     * @param integer $sqTipoPessoa
     * @return TipoPessoaValueObject
     */
    public function setSqTipoPessoa ($sqTipoPessoa = NULL)
    {
        $this->_sqTipoPessoa = $sqTipoPessoa;
        return $this;
    }

    /**
     * @param string $noTipoPessoa
     * @return TipoPessoaValueObject
     */
    public function setNoTipoPessoa ($noTipoPessoa = NULL)
    {
        $this->_noTipoPessoa = $noTipoPessoa;
        return $this;
    }
}