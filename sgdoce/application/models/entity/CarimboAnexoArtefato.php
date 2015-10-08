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
use Core\Model\OWM\Mapping as OWM;

/**
 * Sgdoce\Model\Entity\CarimboAnexoArtefato
 *
 * @ORM\Table(name="carimbo_anexo_artefato")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\CarimboAnexoArtefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class CarimboAnexoArtefato extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqCarimboAnexoArtefato
     *
     * @ORM\Column(name="sq_carimbo_anexo_artefato", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqCarimboAnexoArtefato;

    /**
     * @var integer $nuPosicaoX
     *
     * @ORM\Column(name="nu_posicao_x", type="integer", nullable=false)
     */
    private $nuPosicaoX;

    /**
     * @var integer $nuPosicaoY
     *
     * @ORM\Column(name="nu_posicao_y", type="integer", nullable=false)
     */
    private $nuPosicaoY;

    /**
     * @var datetime $dtCarimbo
     *
     * @ORM\Column(name="dt_carimbo", type="datetime", nullable=false)
     */
    private $dtCarimbo;

    /**
     * @var Sgdoce\Model\Entity\AnexoArtefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\AnexoArtefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_anexo_artefato", referencedColumnName="sq_anexo_artefato")
     * })
     */
    private $sqAnexoArtefato;

    /**
     * @var Sgdoce\Model\Entity\Carimbo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Carimbo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_carimbo", referencedColumnName="sq_carimbo")
     * })
     */
    private $sqCarimbo;


    /**
     * Get sqCarimboAnexoArtefato
     *
     * @return integer
     */
    public function getSqCarimboAnexoArtefato()
    {
        return $this->sqCarimboAnexoArtefato;
    }

    /**
     * Set nuPosicaoX
     *
     * @param integer $nuPosicaoX
     * @return CarimboAnexoArtefato
     */
    public function setNuPosicaoX($nuPosicaoX)
    {
        $this->nuPosicaoX = $nuPosicaoX;
        return $this;
    }

    /**
     * Get nuPosicaoX
     *
     * @return integer
     */
    public function getNuPosicaoX()
    {
        return $this->nuPosicaoX;
    }

    /**
     * Set nuPosicaoY
     *
     * @param integer $nuPosicaoY
     * @return CarimboAnexoArtefato
     */
    public function setNuPosicaoY($nuPosicaoY)
    {
        $this->nuPosicaoY = $nuPosicaoY;
        return $this;
    }

    /**
     * Get nuPosicaoY
     *
     * @return integer
     */
    public function getNuPosicaoY()
    {
        return $this->nuPosicaoY;
    }

    /**
     * Set dtCarimbo
     *
     * @param datetime $dtCarimbo
     * @return CarimboAnexoArtefato
     */
    public function setDtCarimbo($dtCarimbo)
    {
        $this->dtCarimbo = $dtCarimbo;
        return $this;
    }

    /**
     * Get dtCarimbo
     *
     * @return datetime
     */
    public function getDtCarimbo()
    {
        return $this->dtCarimbo;
    }

    /**
     * Set sqCarimbo
     *
     * @param Sgdoce\Model\Entity\Carimbo $sqCarimbo
     * @return CarimboAnexoArtefato
     */
    public function setSqCarimbo(\Sgdoce\Model\Entity\Carimbo $sqCarimbo = NULL)
    {
        $this->sqCarimbo = $sqCarimbo;
        return $this;
    }

    /**
     * Get sqCarimbo
     *
     * @return Sgdoce\Model\Entity\Carimbo
     */
    public function getSqCarimbo()
    {
        return $this->sqCarimbo;
    }
}