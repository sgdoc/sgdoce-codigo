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
 * FgDas
 *
 * @ORM\Table(name="vw_fg_das")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwFgDas")
 */
 class VwFgDas extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqFgDas
     *
     * @ORM\Column(name="sq_fg_das", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqFgDas;

    /**
     * @var string $coFgDas
     *
     * @ORM\Column(name="co_fg_das", type="string", length=6, nullable=false)
     */
    private $coFgDas;

    /**
     * @var string $noFgDas
     *
     * @ORM\Column(name="no_fg_das", type="string", length=50, nullable=false)
     */
    private $noFgDas;

    /**
     * @var integer $nuQuantidade
     *
     * @ORM\Column(name="nu_quantidade", type="integer", nullable=false)
     */
    private $nuQuantidade;


    /**
     * Get sqFgDas
     *
     * @return integer
     */
    public function getSqFgDas()
    {
        return $this->sqFgDas;
    }

    /**
     * Set coFgDas
     *
     * @param string $coFgDas
     * @return FgDas
     */
    public function setCoFgDas($coFgDas)
    {
        $this->coFgDas = $coFgDas;
        return $this;
    }

    /**
     * Get coFgDas
     *
     * @return string
     */
    public function getCoFgDas()
    {
        return $this->coFgDas;
    }

    /**
     * Set noFgDas
     *
     * @param string $noFgDas
     * @return FgDas
     */
    public function setNoFgDas($noFgDas)
    {
        $this->noFgDas = $noFgDas;
        return $this;
    }

    /**
     * Get noFgDas
     *
     * @return string
     */
    public function getNoFgDas()
    {
        return $this->noFgDas;
    }

    /**
     * Set nuQuantidade
     *
     * @param integer $nuQuantidade
     * @return FgDas
     */
    public function setNuQuantidade($nuQuantidade)
    {
        $this->nuQuantidade = $nuQuantidade;
        return $this;
    }

    /**
     * Get nuQuantidade
     *
     * @return integer
     */
    public function getNuQuantidade()
    {
        return $this->nuQuantidade;
    }
}