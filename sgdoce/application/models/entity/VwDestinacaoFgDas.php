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
 * DestinacaoFgDas
 *
 * @ORM\Table(name="vw_destinacao_fg_das")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwDestinacaoFgDas")
 */
 class VwDestinacaoFgDas extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqDestinacaoFgDas
     *
     * @ORM\Column(name="sq_destinacao_fg_das", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqDestinacaoFgDas;

    /**
     * @var Sgdoce\Model\Entity\VwFgDas
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwFgDas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_fg_das", referencedColumnName="sq_fg_das")
     * })
     */
    private $sqFgDas;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_org_destinada", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeOrgDestinada;

    //chefia

    /**
     * Get sqDestinacaoFgDas
     *
     * @return integer
     */
    public function getSqDestinacaoFgDas()
    {
        return $this->sqDestinacaoFgDas;
    }

    /**
     * Set sqFgDas
     *
     * @param Sgdoce\Model\Entity\FgDas $sqFgDas
     * @return DestinacaoFgDas
     */
    public function setSqFgDas(\Sgdoce\Model\Entity\VwFgDas $sqFgDas = NULL)
    {
        $this->sqFgDas = $sqFgDas;
        return $this;
    }

    /**
     * Get sqFgDas
     *
     * @return Sgdoce\Model\Entity\FgDas
     */
    public function getSqFgDas()
    {
        return $this->sqFgDas;
    }

    /**
     * Set sqUnidadeOrgDestinada
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqUnidadeOrgDestinada
     * @return DestinacaoFgDas
     */
    public function setSqUnidadeOrgDestinada(\Sgdoce\Model\Entity\VwPessoa $sqUnidadeOrgDestinada = NULL)
    {
        $this->sqUnidadeOrgDestinada = $sqUnidadeOrgDestinada;
        return $this;
    }

    /**
     * Get sqUnidadeOrgDestinada
     *
     * @return Sgdoce\Model\Entity\Pessoa
     */
    public function getSqUnidadeOrgDestinada()
    {
        return $this->sqUnidadeOrgDestinada;
    }
}