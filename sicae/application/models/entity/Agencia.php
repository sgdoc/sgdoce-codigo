<?php

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sica\Model\Entity\Agencia
 *
 * @ORM\Table(name="vw_agencia")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\Agencia", readOnly=true)
 */
class Agencia extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqAgencia
     *
     * @ORM\Column(name="sq_agencia", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqAgencia;

    /**
     * @var integer $coAgencia
     *
     * @ORM\Column(name="co_agencia", type="integer", nullable=false)
     */
    private $coAgencia;

    /**
     * @var string $noAgencia
     *
     * @ORM\Column(name="no_agencia", type="string", length=100, nullable=false)
     */
    private $noAgencia;

    /**
     * @var integer $coDigitoAgencia
     *
     * @ORM\Column(name="co_digito_agencia", type="integer", nullable=true)
     */
    private $coDigitoAgencia;

    /**
     * @var Sica\Model\Entity\Banco
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Banco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_banco", referencedColumnName="sq_banco")
     * })
     */
    private $sqBanco;

    /**
     * Get sqAgencia
     *
     * @return integer
     */
    public function getSqAgencia()
    {
        return $this->sqAgencia;
    }

    /**
     * Set coAgencia
     *
     * @param integer $coAgencia
     * @return Agencia
     */
    public function setCoAgencia($coAgencia)
    {
        $this->coAgencia = $coAgencia;
        return $this;
    }

    /**
     * Get coAgencia
     *
     * @return integer
     */
    public function getCoAgencia()
    {
        return $this->coAgencia;
    }

    /**
     * Set noAgencia
     *
     * @param string $noAgencia
     * @return Agencia
     */
    public function setNoAgencia($noAgencia)
    {
        $this->noAgencia = $noAgencia;
        return $this;
    }

    /**
     * Get noAgencia
     *
     * @return string
     */
    public function getNoAgencia()
    {
        return $this->noAgencia;
    }

    /**
     * Set coDigitoAgencia
     *
     * @param integer $coDigitoAgencia
     * @return Agencia
     */
    public function setCoDigitoAgencia($coDigitoAgencia)
    {
        $this->coDigitoAgencia = $coDigitoAgencia;
        return $this;
    }

    /**
     * Get coDigitoAgencia
     *
     * @return integer
     */
    public function getCoDigitoAgencia()
    {
        return $this->coDigitoAgencia;
    }

    /**
     * Set sqBanco
     *
     * @param Sica\Model\Entity\Banco $sqBanco
     * @return Agencia
     */
    public function setSqBanco(Banco $sqBanco = NULL)
    {
        $this->sqBanco = $sqBanco;
        return $this;
    }

    /**
     * Get sqBanco
     *
     * @return Sica\Model\Entity\Banco
     */
    public function getSqBanco()
    {
        if (NULL === $this->sqBanco) {
            $this->setSqBanco(new Banco());
        }

        return $this->sqBanco;
    }

}