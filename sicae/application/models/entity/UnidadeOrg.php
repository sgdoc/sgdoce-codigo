<?php

/*
 * Copyright 2012 ICMBio
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
/**
 * SISICMBio
 *
 * Classe para Entity Unidade Org
 *
 * @package      Model
 * @subpackage     Entity
 * @name         UnidadeOrg
 * @version     1.0.0
 * @since        2012-06-26
 */

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sica\Model\Entity\UnidadeOrg
 *
 * @ORM\Table(name="vw_unidade_org")
 * @ORM\Entity(repositoryClass="\Sica\Model\Repository\UnidadeOrg", readOnly=true)
 */
class UnidadeOrg extends \Core_Model_Entity_Abstract
{

    /**
     * @var Sica\Model\Entity\Pessoa
     *
     * @ORM\Id
     * @ORM\Column(name="sq_pessoa", type="integer", nullable=false)
     */
    private $sqPessoa;

    /**
     * @var string $noPessoa
     *
     * @ORM\Column(name="no_pessoa", type="string", length=100, nullable=false)
     */
    private $noPessoa;

    /**
     * @var integer $sqUnidadeSuperior
     *
     * @ORM\Column(name="sq_unidade_superior", type="integer", nullable=true)
     */
    private $sqUnidadeSuperior;

    /**
     * @var integer $sqUnidadeAdmPai
     *
     * @ORM\Column(name="sq_unidade_adm_pai", type="integer", nullable=true)
     */
    private $sqUnidadeAdmPai;

    /**
     * @var integer $sqUnidadeFinPai
     *
     * @ORM\Column(name="sq_unidade_fin_pai", type="integer", nullable=true)
     */
    private $sqUnidadeFinPai;

    /**
     * @var integer
     *
     * @ORM\Column(name="sq_tipo_unidade", type="integer", nullable=true)
     */
    private $sqTipoUnidade;

    /**
     * @var string $coUorg
     *
     * @ORM\Column(name="co_uorg", type="string", length=11, nullable=true)
     */
    private $coUorg;

    /**
     * @var string $sgUnidadeOrg
     *
     * @ORM\Column(name="sg_unidade_org", type="string", length=120, nullable=true)
     */
    private $sgUnidadeOrg;

    /**
     * @var integer $nuNup
     *
     * @ORM\Column(name="nu_nup", type="integer", nullable=true)
     */
    private $nuNup;

    /**
     * @var integer $nuNup
     *
     * @ORM\Column(name="st_ativo", type="boolean", nullable=true)
     */
    private $stAtivo;

    /**
     * @var Sica\Model\Entity\Pessoa
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\Pessoa", inversedBy="unidade", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeOrgPessoa;

    /**
     * Set sqPessoa
     *
     * @param integer $sqPessoa
     * @return VwUnidadeOrg
     */
    public function setSqPessoa($sqPessoa)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return integer
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa;
    }

    /**
     * Set noPessoa
     *
     * @param integer $noPessoa
     * @return VwUnidadeOrg
     */
    public function setNoPessoa($noPessoa)
    {
        $this->noPessoa = $noPessoa;
        return $this;
    }

    /**
     * Get noPessoa
     *
     * @return integer
     */
    public function getNoPessoa()
    {
        return $this->noPessoa;
    }

    /**
     * Set sqUnidadeSuperior
     *
     * @param integer $sqUnidadeSuperior
     * @return VwUnidadeOrg
     */
    public function setSqUnidadeSuperior($sqUnidadeSuperior)
    {
        $this->sqUnidadeSuperior = $sqUnidadeSuperior;
        return $this;
    }

    /**
     * Get sqUnidadeSuperior
     *
     * @return integer
     */
    public function getSqUnidadeSuperior()
    {
        return $this->sqUnidadeSuperior;
    }

    /**
     * Set sqUnidadeAdmPai
     *
     * @param integer $sqUnidadeAdmPai
     * @return VwUnidadeOrg
     */
    public function setSqUnidadeAdmPai($sqUnidadeAdmPai)
    {
        $this->sqUnidadeAdmPai = $sqUnidadeAdmPai;
        return $this;
    }

    /**
     * Get sqUnidadeSuperior
     *
     * @return integer
     */
    public function getSqUnidadeAdmPai()
    {
        return $this->sqUnidadeAdmPai;
    }

    /**
     * Set sqUnidadeFinPai
     *
     * @param integer $sqUnidadeFinPai
     * @return VwUnidadeOrg
     */
    public function setSqUnidadeFinPai($sqUnidadeFinPai)
    {
        $this->sqUnidadeFinPai = $sqUnidadeFinPai;
        return $this;
    }

    /**
     * Get sqUnidadeSuperior
     *
     * @return integer
     */
    public function getSqUnidadeFinPai()
    {
        return $this->sqUnidadeFinPai;
    }

    /**
     * Set sqTipoUnidade
     *
     * @param integer $sqTipoUnidade
     * @return VwUnidadeOrg
     */
    public function setSqTipoUnidade($sqTipoUnidade)
    {
        $this->sqTipoUnidade = $sqTipoUnidade;
        return $this;
    }

    /**
     * Get sqTipoUnidade
     *
     * @return integer
     */
    public function getSqTipoUnidade()
    {
        return $this->sqTipoUnidade;
    }

    /**
     * Set coUorg
     *
     * @param integer $coUorg
     * @return VwUnidadeOrg
     */
    public function setCoUorg($coUorg)
    {
        $this->coUorg = $coUorg;
        return $this;
    }

    /**
     * Get coUorg
     *
     * @return integer
     */
    public function getCoUorg()
    {
        return $this->coUorg;
    }

    /**
     * Set sgUnidadeOrg
     *
     * @param string $sgUnidadeOrg
     * @return VwUnidadeOrg
     */
    public function setSgUnidadeOrg($sgUnidadeOrg)
    {
        $this->sgUnidadeOrg = $sgUnidadeOrg;
        return $this;
    }

    /**
     * Get coUorg
     *
     * @return string
     */
    public function getSgUnidadeOrg()
    {
        return $this->sgUnidadeOrg;
    }

    /**
     * Set sqUnidadeOrgPessoa
     *
     * @param integer $nuNup
     * @return VwUnidadeOrg
     */
    public function setSqUnidadeOrgPessoa(Pessoa $sqUnidadeOrgPessoa)
    {
        $this->sqUnidadeOrgPessoa = $sqUnidadeOrgPessoa;
        return $this;
    }

    /**
     * Get coUorg
     *
     * @return string
     */
    public function getSqUnidadeOrgPessoa()
    {
        return $this->sqUnidadeOrgPessoa;
    }

}