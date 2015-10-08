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
 * Chefia
 *
 * @ORM\Table(name="vw_chefia")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwChefia")
 */
 class VwChefia extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqChefia
     *
     * @ORM\Column(name="sq_chefia", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqChefia;

    /**
     * @var Sgdoce\Model\Entity\VwDestinacaoFgDas
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwDestinacaoFgDas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_destinacao_fg_das", referencedColumnName="sq_destinacao_fg_das")
     * })
     */
    private $sqDestinacaoFgDas;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_profissional_titular", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqProfissionalTitular;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_profissional_substituto", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqProfissionalSubstituto;

    /**
     * Get sqChefia
     *
     * @return integer
     */
    public function getSqChefia()
    {
        return $this->sqChefia;
    }

    /**
     * Set sqProfissionalTitular
     *
     * @param Sgdoce\Model\Entity\Profissional $sqProfissionalTitular
     * @return Chefia
     */
    public function setSqProfissionalTitular(\Sgdoce\Model\Entity\VwProfissional $sqProfissionalTitular = NULL)
    {
        $this->sqProfissionalTitular = $sqProfissionalTitular;
        return $this;
    }

    /**
     * Get sqProfissionalTitular
     *
     * @return Sgdoce\Model\Entity\Profissional
     */
    public function getSqProfissionalTitular()
    {
        return $this->sqProfissionalTitular;
    }

    /**
     * Set sqProfissionalSubstituto
     *
     * @param Sgdoce\Model\Entity\Profissional $sqProfissionalSubstituto
     * @return Chefia
     */
    public function setSqProfissionalSubstituto(\Sgdoce\Model\Entity\VwProfissional $sqProfissionalSubstituto = NULL)
    {
        $this->sqProfissionalSubstituto = $sqProfissionalSubstituto;
        return $this;
    }

    /**
     * Get sqProfissionalSubstituto
     *
     * @return Sgdoce\Model\Entity\Profissional
     */
    public function getSqProfissionalSubstituto()
    {
        return $this->sqProfissionalSubstituto;
    }

    /**
     * Set sqDestinacaoFgDas
     *
     * @param Sgdoce\Model\Entity\VwDestinacaoFgDas $sqDestinacaoFgDas
     * @return Chefia
     */
    public function setSqDestinacaoFgDas(\Sgdoce\Model\Entity\VwDestinacaoFgDas $sqDestinacaoFgDas = NULL)
    {
        $this->sqDestinacaoFgDas = $sqDestinacaoFgDas;
        return $this;
    }

    /**
     * Get sqDestinacaoFgDas
     *
     * @return Sgdoce\Model\Entity\VwDestinacaoFgDas
     */
    public function getSqDestinacaoFgDas()
    {
        return $this->sqDestinacaoFgDas;
    }
}