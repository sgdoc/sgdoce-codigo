<?php

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
 * Sica\Model\Entity\UsuarioPessoaJuridica
 *
 * @ORM\Table(name="usuario_pessoa_juridica")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\UsuarioPessoaJuridica")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class UsuarioPessoaJuridica extends \Core_Model_Entity_Abstract
{
    /**
     * @var string $nuCnpj
     *
     * @ORM\Column(name="nu_cnpj", type="string", length=20, nullable=false)
     */
    private $nuCnpj;

    /**
     * @var string $noRazaoSocial
     *
     * @ORM\Column(name="no_fantasia", type="string", length=250, nullable=false)
     */
    private $noFantasia;

    /**
     * @var Sica\Model\Entity\SicaeUsuarioExterno
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\UsuarioExterno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_usuario_externo", referencedColumnName="sq_usuario_externo")
     * })
     */
    private $sqUsuarioExterno;


    /**
     * Set nuCnpj
     *
     * @param string $nuCnpj
     * @return UsuarioPessoaJuridica
     */
    public function setNuCnpj($nuCnpj)
    {
        $this->nuCnpj = $nuCnpj;
        return $this;
    }

    /**
     * Get nuCnpj
     *
     * @return string
     */
    public function getNuCnpj()
    {
        return $this->nuCnpj;
    }

    /**
     * Set noRazaoSocial
     *
     * @param string $$noFantasia
     * @return UsuarioPessoaJuridica
     */
    public function setNoFantasia($noFantasia)
    {
        $this->noFantasia = $noFantasia;
        return $this;
    }

    /**
     * Get noRazaoSocial
     *
     * @return string
     */
    public function getNoFantasia()
    {
        return $this->noFantasia;
    }

    /**
     * Set sqUsuarioExterno
     *
     * @param Sica\Model\Entity\SicaeUsuarioExterno $sqUsuarioExterno
     * @return UsuarioPessoaJuridica
     */
    public function setSqUsuarioExterno(\Sica\Model\Entity\UsuarioExterno $sqUsuarioExterno = NULL)
    {
        $this->sqUsuarioExterno = $sqUsuarioExterno;
        return $this;
    }

    /**
     * Get sqUsuarioExterno
     *
     * @return Sica\Model\Entity\SicaeUsuarioExterno
     */
    public function getSqUsuarioExterno()
    {
        return $this->sqUsuarioExterno ? $this->sqUsuarioExterno : new Sica\Model\Entity\UsuarioExterno();
    }
}