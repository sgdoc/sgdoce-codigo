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

namespace Sgdoce\Model\Entity;



use Doctrine\ORM\Mapping as ORM;

/**
 * UnidadeOrgInterna
 *
 * @ORM\Table(name="vw_unidade_org_interna")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwUnidadeOrgInterna")
 */
 class VWUnidadeOrgInterna extends \Core_Model_Entity_Abstract
{
    /**
     * @var string $sgUnidadeOrg
     *
     * @ORM\Column(name="sg_unidade_org", type="string", length=120, nullable=true)
     */
    private $sgUnidadeOrg;

    /**
     * @var bigint $coCnuc
     *
     * @ORM\Column(name="co_cnuc", type="bigint", nullable=true)
     */
    private $coCnuc;

    /**
     * @var Sgdoce\Model\Entity\VwEsfera
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwEsfera")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_esfera", referencedColumnName="sq_esfera")
     * })
     */
    private $sqEsfera;

    /**
     * @var Sgdoce\Model\Entity\VWTipoUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwTipoUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_unidade", referencedColumnName="sq_tipo_unidade_org")
     * })
     */
    private $sqTipoUnidade;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;


    /**
     * Set sgUnidadeOrg
     *
     * @param string $sgUnidadeOrg
     * @return UnidadeOrgInterna
     */
    public function setSgUnidadeOrg($sgUnidadeOrg)
    {
        $this->sgUnidadeOrg = $sgUnidadeOrg;
        return $this;
    }

    /**
     * Get sgUnidadeOrg
     *
     * @return string
     */
    public function getSgUnidadeOrg()
    {
        return $this->sgUnidadeOrg;
    }

    /**
     * Set coCnuc
     *
     * @param bigint $coCnuc
     * @return UnidadeOrgInterna
     */
    public function setCoCnuc($coCnuc)
    {
        $this->coCnuc = $coCnuc;
        return $this;
    }

    /**
     * Get coCnuc
     *
     * @return bigint
     */
    public function getCoCnuc()
    {
        return $this->coCnuc;
    }

    /**
     * Set sqEsfera
     *
     * @param Sgdoce\Model\Entity\Esfera $sqEsfera
     * @return UnidadeOrgInterna
     */
    public function setSqEsfera(\Sgdoce\Model\Entity\VwEsfera $sqEsfera = NULL)
    {
        $this->sqEsfera = $sqEsfera;
        return $this;
    }

    /**
     * Get sqEsfera
     *
     * @return Sgdoce\Model\Entity\Esfera
     */
    public function getSqEsfera()
    {
        return $this->sqEsfera;
    }

    /**
     * Set sqTipoUnidade
     *
     * @param Sgdoce\Model\Entity\TipoUnidadeOrg $sqTipoUnidade
     * @return UnidadeOrgInterna
     */
    public function setSqTipoUnidade(\Sgdoce\Model\Entity\VwTipoUnidadeOrg $sqTipoUnidade = NULL)
    {
        $this->sqTipoUnidade = $sqTipoUnidade;
        return $this;
    }

    /**
     * Get sqTipoUnidade
     *
     * @return Sgdoce\Model\Entity\TipoUnidadeOrg
     */
    public function getSqTipoUnidade()
    {
        return $this->sqTipoUnidade;
    }

    /**
     * Set sqPessoa
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqPessoa
     * @return UnidadeOrgInterna
     */
    public function setSqPessoa(\Sgdoce\Model\Entity\VwPessoa $sqPessoa)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return Sgdoce\Model\Entity\Pessoa
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa;
    }
}