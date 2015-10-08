<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
namespace Sgdoce\Model\Entity;

use Doctrine\DBAL\Types\BigIntType;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sgdoce\Model\Entity\VwUnidadeOrg
 *
 * @ORM\Table(name="vw_unidade_org")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\VwUnidadeOrg", readOnly=true)
 */
class VwUnidadeOrg extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqUnidadeOrg
     *
     * @ORM\Id
     * @ORM\Column(name="sq_pessoa", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumn(name="sq_unidade_org", referencedColumnName="sq_pessoa")
     */
    private $sqUnidadeOrg;

    /**
     * @var string $noUnidadeOrg
     *
     * @ORM\Column(name="no_pessoa", type="string", length=100, nullable=false)
     */
    private $noUnidadeOrg;

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
     * @var Sgdoce\Model\Entity\VwTipoUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwTipoUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_unidade", referencedColumnName="sq_tipo_unidade_org")
     * })
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
     * @var integer $sqTipoPessoa
     *
     * @ORM\Column(name="sq_tipo_pessoa", type="string", nullable=true)
     */
    private $sqTipoPessoa;

    /**
     * @ORM\OneToMany(targetEntity="Sgdoce\Model\Entity\VwProfissional", mappedBy="sqUnidadeExercicio")
     */
    private $sqProfissional;

    /**
     * Set sqTipoPessoa
     *
     * @param integer $sqTipoPessoa
     * @return TipoPessoa
     */
    public function setSqTipoPessoa($sqTipoPessoa)
    {
        $this->sqTipoPessoa = $sqTipoPessoa;
        return $this;
    }

    /**
     * Get sqTipoPessoa
     *
     * @return integer
     */
    public function getSqTipoPessoa()
    {
        return $this->sqTipoPessoa;
    }

    /**
     * Set sqUnidadeOrg
     *
     * @param integer $sqUnidadeOrg
     * @return UnidadeOrg
     */
    public function setSqUnidadeOrg($sqUnidadeOrg)
    {
        $this->sqUnidadeOrg = $sqUnidadeOrg;
    }

    /**
     * Get sqUnidadeOrg
     *
     * @return integer
     */
    public function getSqUnidadeOrg()
    {
        return $this->sqUnidadeOrg;
    }

    /**
     * Set noUnidadeOrg
     *
     * @param integer $noUnidadeOrg
     * @return UnidadeOrg
     */
    public function setNoUnidadeOrg($noUnidadeOrg)
    {
        $this->noUnidadeOrg = $noUnidadeOrg;
        return $this;
    }

    /**
     * Get noUnidadeOrg
     *
     * @return integer
     */
    public function getNoUnidadeOrg()
    {
        return $this->noUnidadeOrg;
    }

    /**
     * Set sqUnidadeSuperior
     *
     * @param integer $sqUnidadeSuperior
     * @return UnidadeOrg
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
     * @return UnidadeOrg
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
     * @return UnidadeOrg
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
     * @return UnidadeOrg
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
     * @return UnidadeOrg
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
     * @return UnidadeOrg
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
     * Set nuNup
     *
     * @param integer $nuNup
     * @return UnidadeOrg
     */
    public function setNuNup($nuNup)
    {
        $this->nuNup = $nuNup;
        return $this;
    }

    /**
     * Get coUorg
     *
     * @return string
     */
    public function getNuNup()
    {
        return $this->nuNup;
    }
    
    /**
     * @param type $sqUnidadeDestino
     * @return \Sgdoce\Model\Entity\VwUnidadeOrg
     */
    public function setSqUnidadeDestino($sqUnidadeDestino)
    {
        $this->setSqUnidadeOrg($sqUnidadeDestino);
        return $this;
    }
    
    /**
     * @param type $sqUnidadeAssinatura
     * @return \Sgdoce\Model\Entity\VwUnidadeOrg
     */
    public function setSqUnidadeAssinatura($sqUnidadeAssinatura)
    {
        $this->setSqUnidadeOrg($sqUnidadeAssinatura);
        return $this;
    }
    
    /**
     * @param type $sqUnidadeSolicitacao
     * @return \Sgdoce\Model\Entity\VwUnidadeOrg
     */
    public function setSqUnidadeSolicitacao($sqUnidadeSolicitacao)
    {
        $this->setSqUnidadeOrg($sqUnidadeSolicitacao);
        return $this;
    }

    /**
     * @return integer
     */
    public function getSqUnidadeSolicitacao()
    {
        return $this->getSqUnidadeOrg();        
    }
}