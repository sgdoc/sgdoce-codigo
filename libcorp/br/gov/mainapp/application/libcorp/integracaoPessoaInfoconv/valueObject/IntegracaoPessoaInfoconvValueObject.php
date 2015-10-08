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
namespace br\gov\mainapp\application\libcorp\integracaoPessoaInfoconv\valueObject;

use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
        br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness;

/**
  * SISICMBio
  *
  * @name IntegracaoPessoaInfoconvValueObject
  * @package br.gov.mainapp.application.libcorp.integracaoPessoaInfoconv
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="integracao_pessoa_infoconv")
  * @author carloss
  * @version $Id$
  * @log(name="all")
  * */
class IntegracaoPessoaInfoconvValueObject extends ParentValueObject
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
     *  name="dtIntegracao",
     *  database="dt_integracao",
     *  type="timestamp",
     *  nullable="TRUE",
     *  get="getDtIntegracao",
     *  set="setDtIntegracao"
     * )
     * */
     private $_dtIntegracao;
     
     
         /**
     * @attr (
     *  name="sqPessoaAutora",
     *  database="sq_pessoa_autora",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqPessoaAutora",
     *  set="setSqPessoaAutora"
     * )
     * */
     private $_sqPessoaAutora;

    /**
     * @attr (
     *  name="txJustificativa",
     *  database="tx_justificativa",
     *  type="string",
     *  nullable="FALSE",
     *  get="getTxJustificativa",
     *  set="setTxJustificativa"
     * )
     * */
     private $_txJustificativa;
     

    /**
     * @param integer $sqPessoa
     * @param timestamp $dtIntegracao
     * @param string $txJustificativa
     * @param integer $sqPessoaAutora

     * */
    public function __construct (
            $sqPessoa = NULL,
            $dtIntegracao = NULL,
            $txJustificativa = NULL,
            $sqPessoaAutora = NULL )
    {
        parent::__construct();
        $this->setSqPessoa($sqPessoa)
                ->setDtIntegracao($dtIntegracao)
                ->setTxJustificativa($txJustificativa)
                ->setSqPessoaAutora($sqPessoaAutora);
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
     * @return timestamp
     * */
    public function getDtIntegracao ()
    {
        return $this->_dtIntegracao;
    }

    /**
     * @return integer
     * */
    public function getTxJustificativa ()
    {
        return $this->_txJustificativa;
    }
    
    /**
     * @return mixed
     */
    public function getSqPessoaAutora()
    {
        return $this->_sqPessoaAutora;
    }

    
    /**
     * @param integer $sqPessoa
     * @return IntegracaoPessoaInfoconvValueObject
     * */
    public function setSqPessoa ($sqPessoa = NULL)
    {
        $this->_sqPessoa = $sqPessoa;
        return $this;
    }
    
    
    /**
     * @param timestamp $dtIntegracao
     * @return IntegracaoPessoaInfoconvValueObject
     * */
    public function setDtIntegracao ($dtIntegracao = NULL)
    {
        $this->_dtIntegracao = $dtIntegracao;
        return $this;
    }

    
    /**
     * @param integer $txJustificativa
     * @return br\gov\icmbio\sisicmbio\application\sica\integracaoPessoaInfoconv\valueObject\IntegracaoPessoaInfoconvValueObject
     * */
    public function setTxJustificativa ($txJustificativa = NULL)
    {
        $this->_txJustificativa = $txJustificativa;
        return $this;
    }

    /**
     * @param mixed $sqPessoaAutora
     */
    public function setSqPessoaAutora($sqPessoaAutora = NULL)
    {
        $this->_sqPessoaAutora = $sqPessoaAutora;
        return $this;
    }


    

}
