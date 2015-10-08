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
namespace br\gov\mainapp\application\libcorp\email\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract,
    br\gov\mainapp\application\libcorp\tipoEmail\mvcb\business\TipoEmailBusiness;

/**
  * SISICMBio
  *
  * @name EmailValueObject
  * @package br.gov.mainapp.application.libcorp.email
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="email")
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class EmailValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="txEmail",
     *  database="tx_email",
     *  type="string",
     *  nullable="FALSE",
     *  get="getTxEmail",
     *  set="setTxEmail"
     * )
     * */
     private $_txEmail;

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
     *  name="sqTipoEmail",
     *  database="sq_tipo_email",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoEmail",
     *  set="setSqTipoEmail"
     * )
     * */
     private $_sqTipoEmail;

    /**
     * @attr (
     *  name="sqEmail",
     *  database="sq_email",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqEmail",
     *  set="setSqEmail"
     * )
     * */
     private $_sqEmail;

    /**
     * @param string $txEmail
     * @param integer $sqPessoa
     * @param integer $sqTipoEmail
     * @param integer $sqEmail
     * */
    public function __construct ($txEmail = NULL,
                                 $sqPessoa = NULL,
                                 $sqTipoEmail = NULL,
                                 $sqEmail = NULL)
    {
        parent::__construct();
        $this->setTxEmail($txEmail)
             ->setSqPessoa($sqPessoa)
             ->setSqTipoEmail($sqTipoEmail)
             ->setSqEmail($sqEmail);
    }

    /**
     * @return string
     * */
    public function getTxEmail ()
    {
        return $this->_txEmail;
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
     * @return TipoEmailValueObject
     * */
    public function getSqTipoEmail ()
    {
        if ((NULL != $this->_sqTipoEmail) && !($this->_sqTipoEmail instanceof parent)) {
            $this->_sqTipoEmail = TipoEmailBusiness::factory(NULL, 'libcorp')->find($this->_sqTipoEmail);
        }
        return $this->_sqTipoEmail;
    }

    /**
     * @return integer
     * */
    public function getSqEmail ()
    {
        return $this->_sqEmail;
    }

    /**
     * @param string $txEmail
     * @return EmailValueObject
     * */
    public function setTxEmail ($txEmail = NULL)
    {
        $this->_txEmail = $txEmail;
        return $this;
    }

    /**
     * @param integer $sqPessoa
     * @return EmailValueObject
     * */
    public function setSqPessoa ($sqPessoa = NULL)
    {
        $this->_sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * @param integer $sqTipoEmail
     * @return EmailValueObject
     * */
    public function setSqTipoEmail ($sqTipoEmail = NULL)
    {
        $this->_sqTipoEmail = $sqTipoEmail;
        return $this;
    }

    /**
     * @param integer $sqEmail
     * @return EmailValueObject
     * */
    public function setSqEmail ($sqEmail = NULL)
    {
        $this->_sqEmail = $sqEmail;
        return $this;
    }
}