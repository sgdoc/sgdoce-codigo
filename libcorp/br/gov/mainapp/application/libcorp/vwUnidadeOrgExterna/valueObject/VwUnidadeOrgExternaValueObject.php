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
namespace br\gov\mainapp\application\libcorp\vwUnidadeOrgExterna\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract;

/**
 * @package br.gov.mainapp.application.libcorp.vwUnidadeOrgExterna
 * @subpackage valueObject
 * @author J. Augusto <augustowebd@gmail.com>
 * @schema(name="{corporativo}")
 * @entity(name="{vw_unidade_org_externa}")
 * */
class VwUnidadeOrgExternaValueObject extends ValueObjectAbstract
{
    /**
     * @attr (
     *  name="coCnuc",
     *  database="co_cnuc",
     *  type="integer",
     *  nullable="TRUE",
     *  get="getCoCnuc",
     *  set="setCoCnuc"
     * )
     * */
    private $_coCnuc;

    /**
     * @attr (
     *  name="sgUnidadeOrg",
     *  database="sg_unidade_org",
     *  type="string",
     *  nullable="FALSE",
     *  get="getSgUnidadeOrg",
     *  set="setSgUnidadeOrg"
     * )
     * */
    private $_sgUnidadeOrg;

    /**
     * @attr (
     *  name="sqEsfera",
     *  database="sq_esfera",
     *  type="integer",
     *  nullable="TRUE",
     *  get="getSqEsfera",
     *  set="setSqEsfera"
     * )
     * */
    private $_sqEsfera;

    /**
     * @attr (
     *  name="sqTipoUnidade",
     *  database="sq_tipo_unidade",
     *  type="integer",
     *  nullable="TRUE",
     *  get="getSqTipoUnidade",
     *  set="setSqTipoUnidade"
     * )
     * */
    private $_sqTipoUnidade;

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
     *  name="sqTipoPessoa",
     *  database="sq_tipo_pessoa",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoPessoa",
     *  set="setSqTipoPessoa"
     * )
     * */
    private $_sqTipoPessoa;

    public function __construct
    (
        $coCnuc = NUL,
        $sgUnidadeOrg = NULL,
        $sqEsfera = NULL,
        $sqTipoUnidade = NULL,
        $sqPessoa = NULL,
        $noPessoa = NULL,
        $sqTipoPessoa = NULL
    )
    {
        parent::__construct();
        $this->getCoCnuc($coCnuc)
             ->getSgUnidadeOrg($sgUnidadeOrg)
             ->getSqEsfera($sqEsfera)
             ->getSqTipoUnidade($sqTipoUnidade)
             ->getSqPessoa($sqPessoa)
             ->getNoPessoa($noPessoa)
             ->getSqTipoPessoa($sqTipoPessoa);
    }

    /**
     * @return mixed
     * */
    public function getCoCnuc ()
    {
        return $this->_coCnuc;
    }

    /**
     * @return mixed
     * */
    public function getSgUnidadeOrg ()
    {
        return $this->_sgUnidadeOrg;
    }

    /**
     * @return mixed
     * */
    public function getSqEsfera ()
    {
        return $this->_sqEsfera;
    }

    /**
     * @return mixed
     * */
    public function getSqTipoUnidade ()
    {
        return $this->_sqTipoUnidade;
    }

    /**
     * @return mixed
     * */
    public function getSqPessoa ()
    {
        return $this->_sqPessoa;
    }

    /**
     * @return mixed
     * */
    public function getNoPessoa ()
    {
        return $this->_noPessoa;
    }

    /**
     * @return integer
     * */
    public function getSqTipoPessoa ()
    {
        return $this->_sqTipoPessoa;
    }

    /**
     * @param integer $coCnuc
     * @return VwUnidadeOrgExternaValueObject
     * */
    public function setCoCnuc ($coCnuc = NULL)
    {
        $this->_coCnuc = $coCnuc;
        return $this;
    }

    /**
     * @param mixed $sgUnidadeOrg
     * @return VwUnidadeOrgExternaValueObject
     * */
    public function setSgUnidadeOrg ($sgUnidadeOrg = NULL)
    {
        $this->_sgUnidadeOrg = $sgUnidadeOrg;
        return $this;
    }

    /**
     * @param integer $sqEsfera
     * @return VwUnidadeOrgExternaValueObject
     * */
    public function setSqEsfera ($sqEsfera = NULL)
    {
        $this->_sqEsfera = $sqEsfera;
        return $this;
    }

    /**
     * @param integer $sqTipoUnidade
     * @return VwUnidadeOrgExternaValueObject
     * */
    public function setSqTipoUnidade ($sqTipoUnidade = NULL)
    {
        $this->_sqTipoUnidade = $sqTipoUnidade;
        return $this;
    }

    /**
     * @param integer $sqPessoa
     * @return VwUnidadeOrgExternaValueObject
     * */
    public function setSqPessoa ($sqPessoa = NULL)
    {
        $this->_sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * @param string $noPessoa
     * @return VwUnidadeOrgExternaValueObject
     * */
    public function setNoPessoa ($noPessoa = NULL)
    {
        $this->_noPessoa = $noPessoa;
        return $this;
    }

    /**
     * @param integer $sqTipoPessoa
     * @return VwUnidadeOrgExternaValueObject
     * */
    public function setSqTipoPessoa ($sqTipoPessoa = NULL)
    {
        $this->_sqTipoPessoa = $sqTipoPessoa;
        return $this;
    }
}