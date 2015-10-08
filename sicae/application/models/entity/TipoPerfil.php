<?php

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
 * Sica\Model\Entity\TipoPerfil
 *
 * @ORM\Table(name="tipo_perfil")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\TipoPerfil")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoPerfil extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoPerfil
     *
     * @ORM\Column(name="sq_tipo_perfil", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoPerfil;

    /**
     * @var string $noPerfil
     *
     * @ORM\Column(name="no_tipo_perfil", type="string", length=50, nullable=true)
     */
    private $noPerfil;

    /**
     * @var string $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=true)
     */
    private $stRegistroAtivo;


    /**
     * Get sqTipoPerfil
     *
     * @return integer
     */
    public function getSqTipoPerfil()
    {
        return $this->sqTipoPerfil;
    }

    /**
     * Get sqTipoPerfil
     *
     * @return integer
     */
    public function setSqTipoPerfil($sqTipoPerfil)
    {
        $this->sqTipoPerfil = $sqTipoPerfil;
        return $this;
    }

    /**
     * Set $noPerfil
     *
     * @param string $noPerfil
     * @return Banco
     */
    public function setNoPerfil($noPerfil)
    {
        $this->noPerfil = $noPerfil;
        return $this;
    }

    /**
     * Get noPerfil
     *
     * @return string
     */
    public function getNoPerfil()
    {
        return $this->noPerfil;
    }

    /**
     * Set stRegistroAtivo
     *
     * @param string $stRegistroAtivo
     * @return Banco
     */
    public function setStRegistroAtivo($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }

    /**
     * Get stRegistroAtivo
     *
     * @return string
     */
    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }
}