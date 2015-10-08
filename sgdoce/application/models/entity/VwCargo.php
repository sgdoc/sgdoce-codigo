<?php

namespace Sgdoce\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sgdoce\Model\Entity\VwCargo
 *
 * @ORM\Table(name="vw_cargo")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\VwCargo")
 */
class VwCargo extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqCargo
     *
     * @ORM\Id
     * @ORM\Column(name="sq_cargo", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqCargo;

    /**
     * @var string $noCargo
     *
     * @ORM\Column(name="no_cargo", type="string", length=50, nullable=false)
     */
    private $noCargo;

    /**
     * Get sqCargo
     *
     * @return integer
     */
    public function getSqCargo()
    {
        return $this->sqCargo;
    }

    /**
     * Get sqCargo
     *
     * @return void
     */
    public function setSqCargo( $sqCargo )
    {
    	$this->sqCargo = $sqCargo;
    }
    
    /**
     * Set noCargo
     *
     * @param string $noCargo
     * @return Cargo
     */
    public function setNoCargo($noCargo)
    {
        $this->noCargo = $noCargo;
        return $this;
    }

    /**
     * Get noCargo
     *
     * @return string
     */
    public function getNoCargo()
    {
        return $this->noCargo;
    }

    public function setSqCargoAssinatura($sqCargoAssinatura)
    {
        $this->sqCargo = $sqCargoAssinatura;
        return $this;
    }
}