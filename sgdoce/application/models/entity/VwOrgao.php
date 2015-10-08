<?php

namespace Sgdoce\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade representativa dos Orgao do SIORG
 *
 * @ORM\Table(name="vw_orgao")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\VwOrgao", readOnly=true)
 */
class VwOrgao extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $coSiorg
     *
     * @ORM\Id
     * @ORM\Column(name="co_siorg", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $coSiorg;

    /**
     * @var VwOrgao
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwOrgao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="co_siorg_pai", referencedColumnName="co_siorg")
     * })
     */
    private $coSiorgPai;

    /**
     * @var VwUnidadeOrg
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeOrg;

    /**
     * @var VwOrgao
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwOrgao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="co_org_entidade", referencedColumnName="co_siorg")
     * })
     */
    private $coOrgEntidade;

    /**
     * @var string $noOrgao
     *
     * @ORM\Column(name="no_orgao", type="string", nullable=true)
     */
    private $noOrgao;

    /**
     * @var string $sgOrgao
     *
     * @ORM\Column(name="sg_orgao", type="string", nullable=true)
     */
    private $sgOrgao;

    /**
     * @var string $coTipoUnidade
     *
     * @ORM\Column(name="co_tipo_unidade", type="string", length=2, nullable=true)
     */
    private $coTipoUnidade;

    /**
     * @var integer $sqEsfera
     *
     * @ORM\Column(name="sq_esfera", type="integer", nullable=true)
     */
    private $sqEsfera;

    /**
     * @var integer $sqPoder
     *
     * @ORM\Column(name="sq_poder", type="integer", nullable=true)
     */
    private $sqPoder;

    /**
     * @var integer $sqNaturezaJuridica
     *
     * @ORM\Column(name="sq_natureza_juridica", type="integer", nullable=true)
     */
    private $sqNaturezaJuridica;

    /**
     * @var integer $sqSubNaturezaJuridica
     *
     * @ORM\Column(name="sq_sub_natureza_juridica", type="integer", nullable=true)
     */
    private $sqSubNaturezaJuridica;

    /**
     *
     * @return integer
     */
    public function getCoSiorg ()
    {
        return $this->coSiorg;
    }

    /**
     *
     * @return VwOrgao
     */
    public function getCoSiorgPai ()
    {
        return $this->coSiorgPai ? : new VwOrgao();
    }

    /**
     *
     * @return VwUnidadeOrg
     */
    public function getSqUnidadeOrg ()
    {
        return $this->sqUnidadeOrg;
    }

    /**
     *
     * @return VwOrgao
     */
    public function getCoOrgEntidade ()
    {
        return $this->coOrgEntidade ? : new VwOrgao();
    }

    /**
     *
     * @return string
     */
    public function getNoOrgao ()
    {
        return $this->noOrgao;
    }

    /**
     *
     * @return string
     */
    public function getSgOrgao ()
    {
        return $this->sgOrgao;
    }

    /**
     *
     * @return string
     */
    public function getCoTipoUnidade ()
    {
        return $this->coTipoUnidade;
    }

    /**
     *
     * @return integer
     */
    public function getSqEsfera ()
    {
        return $this->sqEsfera;
    }

    /**
     *
     * @return integer
     */
    public function getSqPoder ()
    {
        return $this->sqPoder;
    }

    /**
     *
     * @return integer
     */
    public function getSqNaturezaJuridica ()
    {
        return $this->sqNaturezaJuridica;
    }

    /**
     *
     * @return integer
     */
    public function getSqSubNaturezaJuridica ()
    {
        return $this->sqSubNaturezaJuridica;
    }

    public function setCoSiorg ($coSiorg)
    {
        $this->coSiorg = $coSiorg;
        return $this;
    }

    public function setCoSiorgPai (VwOrgao $coSiorgPai)
    {
        $this->coSiorgPai = $coSiorgPai;
        return $this;
    }

    public function setSqUnidadeOrg (VwUnidadeOrg $sqUnidadeOrg)
    {
        $this->sqUnidadeOrg = $sqUnidadeOrg;
        return $this;
    }

    public function setCoOrgEntidade (VwOrgao $coOrgEntidade)
    {
        $this->coOrgEntidade = $coOrgEntidade;
        return $this;
    }

    public function setNoOrgao ($noOrgao)
    {
        $this->noOrgao = $noOrgao;
        return $this;
    }

    public function setSgOrgao ($sgOrgao)
    {
        $this->sgOrgao = $sgOrgao;
        return $this;
    }

    public function setCoTipoUnidade ($coTipoUnidade)
    {
        $this->coTipoUnidade = $coTipoUnidade;
        return $this;
    }

    public function setSqEsfera ($sqEsfera)
    {
        $this->sqEsfera = $sqEsfera;
        return $this;
    }

    public function setSqPoder ($sqPoder)
    {
        $this->sqPoder = $sqPoder;
        return $this;
    }

    public function setSqNaturezaJuridica ($sqNaturezaJuridica)
    {
        $this->sqNaturezaJuridica = $sqNaturezaJuridica;
        return $this;
    }

    public function setSqSubNaturezaJuridica ($sqSubNaturezaJuridica)
    {
        $this->sqSubNaturezaJuridica = $sqSubNaturezaJuridica;
        return $this;
    }


}