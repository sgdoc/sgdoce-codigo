<?php

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
 * Sica\Model\Entity\UsuarioExternoPerfil
 *
 * @ORM\Table(name="usuario_externo_perfil")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\UsuarioExternoPerfil")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class UsuarioExternoPerfil extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqUsuarioExternoPerfil
     *
     * @ORM\Column(name="sq_usuario_externo_perfil", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqUsuarioPerfil;

    /**
     * @var Sica\Model\Entity\SicaePerfil
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_perfil", referencedColumnName="sq_perfil")
     * })
     */
    private $sqPerfil;

    /**
     * @var Sica\Model\Entity\SicaeUsuarioExterno
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\UsuarioExterno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_usuario_externo", referencedColumnName="sq_usuario_externo")
     * })
     */
    private $sqUsuarioExterno;


    /**
     * Get sqUsuarioExternoPerfil
     *
     * @return integer
     */
    public function getSqUsuarioExternoPerfil()
    {
        return $this->sqUsuarioPerfil;
    }

    /**
     * Set sqPerfil
     *
     * @param Sica\Model\Entity\SicaePerfil $sqPerfil
     * @return UsuarioExternoPerfil
     */
    public function setSqPerfil(\Sica\Model\Entity\Perfil $sqPerfil = NULL)
    {
        $this->sqPerfil = $sqPerfil;
        return $this;
    }

    /**
     * Get sqPerfil
     *
     * @return Sica\Model\Entity\Perfil
     */
    public function getSqPerfil()
    {
        return $this->sqPerfil ? $this->sqPerfil : new \Sica\Model\Entity\Perfil();
    }

    /**
     * Set sqUsuarioExterno
     *
     * @param Sica\Model\Entity\UsuarioExterno $sqUsuarioExterno
     * @return UsuarioExternoPerfil
     */
    public function setSqUsuarioExterno(\Sica\Model\Entity\UsuarioExterno $sqUsuarioExterno = NULL)
    {
        $this->sqUsuarioExterno = $sqUsuarioExterno;
        return $this;
    }

    /**
     * Get sqUsuarioExterno
     *
     * @return Sica\Model\Entity\UsuarioExterno
     */
    public function getSqUsuarioExterno()
    {
        return $this->sqUsuarioExterno ? $this->sqUsuarioExterno : new \Sica\Model\Entity\UsuarioExterno();
    }
}