<?php

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
 * Sica\Model\Entity\UsuarioExterno
 *
 * @ORM\Table(name="usuario_externo")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\UsuarioExterno")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class UsuarioExterno extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqUsuarioExterno
     *
     * @ORM\Column(name="sq_usuario_externo", type="integer", nullable=false)
     * @ORM\Id
     */
    private $sqUsuarioExterno;

    /**
     * @var string $noUsuarioExterno
     *
     * @ORM\Column(name="no_usuario_externo", type="string", length=250, nullable=false)
     */
    private $noUsuarioExterno;

    /**
     * @var string $txEmail
     *
     * @ORM\Column(name="tx_email", type="string", length=100, nullable=false)
     */
    private $txEmail;

    /**
     * @var string $txSenha
     *
     * @ORM\Column(name="tx_senha", type="string", length=128, nullable=false)
     */
    private $txSenha;

    /**
     * @var integer $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="smallint", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     * @var integer $dtCadastro
     *
     * @ORM\Column(name="dt_cadastro", type="zenddate", nullable=false)
     */
    private $dtCadastro;

    /**
     * @var Sica\Model\Entity\UsuarioExterno
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\UsuarioPessoaFisica", mappedBy="sqUsuarioExterno")
     */
    private $sqUsuarioPessoaFisica;

    /**
     * @var Sica\Model\Entity\UsuarioPessoaJuridica
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\UsuarioPessoaJuridica", mappedBy="sqUsuarioExterno")
     */
    private $sqUsuarioPessoaJuridica;

    /**
     * @var Sica\Model\Entity\UsuarioPessoaJuridica
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\UsuarioExternoDadoComplementar", mappedBy="sqUsuarioExterno")
     */
    private $dadoComplementar;

    /**
     * @var Sica\Model\Entity\SicaeUsuarioExterno
     *
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\UsuarioExternoPerfil", mappedBy="sqUsuarioExterno")
     */
    private $sqUsuarioPerfil;

    /**
     * Get sqUsuarioExterno
     *
     * @return integer
     */
    public function setSqUsuarioExterno($sqUsuarioExterno)
    {
        return $this->sqUsuarioExterno = $sqUsuarioExterno;
    }

    /**
     * Get sqUsuarioExterno
     *
     * @return integer
     */
    public function getSqUsuarioExterno()
    {
        return $this->sqUsuarioExterno;
    }

    /**
     * Set noUsuarioExterno
     *
     * @param string $noUsuarioExterno
     * @return UsuarioExterno
     */
    public function setNoUsuarioExterno($noUsuarioExterno)
    {
        $this->noUsuarioExterno = $noUsuarioExterno;
        return $this;
    }

    /**
     * Get noUsuarioExterno
     *
     * @return string
     */
    public function getNoUsuarioExterno()
    {
        return $this->noUsuarioExterno;
    }

    /**
     * Set txEmail
     *
     * @param string $txEmail
     * @return UsuarioExterno
     */
    public function setTxEmail($txEmail)
    {
        $this->txEmail = $txEmail;
        return $this;
    }

    /**
     * Get txEmail
     *
     * @return string
     */
    public function getTxEmail()
    {
        return $this->txEmail;
    }

    /**
     * Set txSenha
     *
     * @param string $txSenha
     * @return UsuarioExterno
     */
    public function setTxSenha($txSenha)
    {
        $this->txSenha = $txSenha;
        return $this;
    }

    /**
     * Get txSenha
     *
     * @return string
     */
    public function getTxSenha()
    {
        return $this->txSenha;
    }

    /**
     * Set stRegistroAtivo
     *
     * @param boolean $stRegistroAtivo
     * @return UsuarioExterno
     */
    public function setStRegistroAtivo($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }

    /**
     * Get stRegistroAtivo
     */
    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }

    public function getSqUsuarioPessoaFisica()
    {
        return $this->sqUsuarioPessoaFisica ?
                $this->sqUsuarioPessoaFisica :
                new UsuarioPessoaFisica();
    }

    public function setSqUsuarioPessoaFisica(UsuarioPessoaFisica $sqUsuarioPessoaFisica = NULL)
    {
        $this->sqUsuarioPessoaFisica = $sqUsuarioPessoaFisica;
    }

    public function getSqUsuarioPessoaJuridica()
    {
        return $this->sqUsuarioPessoaJuridica ?
                $this->sqUsuarioPessoaJuridica :
                new UsuarioPessoaJuridica();
    }

    public function getSqUsuarioExternoPerfil()
    {
        return $this->sqUsuarioPerfil ? $this->sqUsuarioPerfil : new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function setSqUsuarioPessoaJuridica(UsuarioPessoaJuridica $sqUsuarioPessoaJuridica = NULL)
    {
        $this->sqUsuarioPessoaJuridica = $sqUsuarioPessoaJuridica;
    }

    public function getDadoComplementar()
    {
        return $this->dadoComplementar ?
                $this->dadoComplementar :
                new \Sica\Model\Entity\UsuarioExternoDadoComplementar();
    }

    public function setSqUsuarioExternoPerfil($sqUsuarioExternoPerfil)
    {
        $this->sqUsuarioPerfil = $sqUsuarioExternoPerfil;
    }

    /**
     * @param int $dtCadastro
     */
    public function setDtCadastro($dtCadastro)
    {
        $this->dtCadastro = $dtCadastro;
    }

    /**
     * @return int
     */
    public function getDtCadastro()
    {
        return $this->dtCadastro;
    }
}
