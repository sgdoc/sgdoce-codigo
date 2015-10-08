<?php

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sica\Model\Entity\TipoEscolaridade
 *
 * @ORM\Table(name="vw_tipo_escolaridade")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\TipoEscolaridade")
 */
class TipoEscolaridade extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqTipoEscolaridade
     *
     * @ORM\Column(name="sq_tipo_escolaridade", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoEscolaridade;

    /**
     * @var string $noTipoEscolaridade
     *
     * @ORM\Column(name="no_tipo_escolaridade", type="string", length=100, nullable=false)
     */
    private $noTipoEscolaridade;

    /**
     * Get sqTipoEscolaridade
     *
     * @return integer
     */
    public function getSqTipoEscolaridade()
    {
        return $this->sqTipoEscolaridade;
    }

    /**
     * Set noTipoEscolaridade
     *
     * @param string $noTipoEscolaridade
     * @return TipoEscolaridade
     */
    public function setNoTipoEscolaridade($noTipoEscolaridade)
    {
        $this->noTipoEscolaridade = $noTipoEscolaridade;
        return $this;
    }

    /**
     * Get noTipoEscolaridade
     *
     * @return string
     */
    public function getNoTipoEscolaridade()
    {
        return $this->noTipoEscolaridade;
    }

}