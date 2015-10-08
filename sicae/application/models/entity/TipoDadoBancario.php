<?php

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sica\Model\Entity\TipoDadoBancario
 *
 * @ORM\Table(name="vw_tipo_dado_bancario")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\TipoDadoBancario", readOnly=true)
 */
class TipoDadoBancario extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoDadoBancario
     *
     * @ORM\Column(name="sq_tipo_dado_bancario", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoDadoBancario;

    /**
     * @var string $noTipoDadoBancario
     *
     * @ORM\Column(name="no_tipo_dado_bancario", type="string", length=30, nullable=false)
     */
    private $noTipoDadoBancario;


    /**
     * Get sqTipoDadoBancario
     *
     * @return integer
     */
    public function getSqTipoDadoBancario()
    {
        return $this->sqTipoDadoBancario;
    }

    /**
     * Set noTipoDadoBancario
     *
     * @param string $noTipoDadoBancario
     * @return TipoDadoBancario
     */
    public function setNoTipoDadoBancario($noTipoDadoBancario)
    {
        $this->noTipoDadoBancario = $noTipoDadoBancario;
        return $this;
    }

    /**
     * Get noTipoDadoBancario
     *
     * @return string
     */
    public function getNoTipoDadoBancario()
    {
        return $this->noTipoDadoBancario;
    }
}