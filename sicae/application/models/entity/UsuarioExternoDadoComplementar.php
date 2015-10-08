<?php

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
 * Sica\Model\Entity\UsuarioExternoDadoComplementar
 *
 * @ORM\Table(name="usuario_externo_dado_complementar")
 * @ORM\Entity
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class UsuarioExternoDadoComplementar extends \Core_Model_Entity_Abstract
{
    /**
     * @var bigint $sqUsuarioExterno
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\UsuarioExterno", inversedBy="sqUsuarioExterno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_usuario_externo", referencedColumnName="sq_usuario_externo")
     * })
     */
    private $sqUsuarioExterno;

    /**
     * @var string $txEndereco
     *
     * @ORM\Column(name="tx_endereco", type="string", length=250, nullable=true)
     */
    private $txEndereco;

    /**
     * @var string $nuDddTelefoneFixo
     *
     * @ORM\Column(name="nu_ddd_telefone_fixo", type="string", nullable=true)
     */
    private $nuDddTelefoneFixo;

    /**
     * @var string $nuTelefoneFixo
     *
     * @ORM\Column(name="nu_telefone_fixo", type="string", length=9, nullable=true)
     */
    private $nuTelefoneFixo;

    /**
     * @var string $nuDddTelefoneCelular
     *
     * @ORM\Column(name="nu_ddd_telefone_celular", type="string", nullable=true)
     */
    private $nuDddTelefoneCelular;

    /**
     * @var string $nuTelefoneCelular
     *
     * @ORM\Column(name="nu_telefone_celular", type="string", length=9, nullable=true)
     */
    private $nuTelefoneCelular;

    /**
     * @var Sica\Model\Entity\CorporativoPais
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Pais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pais", referencedColumnName="sq_pais")
     * })
     */
    private $sqPais;

    /**
     * @var Sica\Model\Entity\CorporativoEstado
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Estado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_estado", referencedColumnName="sq_estado")
     * })
     */
    private $sqEstado;

    /**
     * @var Sica\Model\Entity\CorporativoMunicipio
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Municipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_municipio", referencedColumnName="sq_municipio")
     * })
     */
    private $sqMunicipio;

    /**
     * @var string $coCep
     *
     * @ORM\Column(name="co_cep", type="string", length=8, nullable=true)
     */
    private $coCep;

    /**
     * Get sqEnderecoUsuarioExterno
     *
     * @return integer
     */
    public function getSqEnderecoUsuarioExterno()
    {
        return $this->sqEnderecoUsuarioExterno;
    }

    /**
     * Set sqUsuarioExterno
     *
     * @param bigint $sqUsuarioExterno
     * @return EnderecoUsuarioExterno
     */
    public function setSqUsuarioExterno($sqUsuarioExterno)
    {
        $this->sqUsuarioExterno = $sqUsuarioExterno;
        return $this;
    }

    /**
     * Get sqUsuarioExterno
     *
     * @return bigint
     */
    public function getSqUsuarioExterno()
    {
        return $this->sqUsuarioExterno;
    }

    /**
     * Set txEndereco
     *
     * @param string $txEndereco
     * @return EnderecoUsuarioExterno
     */
    public function setTxEndereco($txEndereco)
    {
        $this->txEndereco = $txEndereco;
        return $this;
    }

    /**
     * Get txEndereco
     *
     * @return string
     */
    public function getTxEndereco()
    {
        return $this->txEndereco;
    }

    /**
     * Set nuDddTelefoneFixo
     *
     * @param string $nuDddTelefoneFixo
     * @return EnderecoUsuarioExterno
     */
    public function setNuDddTelefoneFixo($nuDddTelefoneFixo)
    {
        $this->nuDddTelefoneFixo = $nuDddTelefoneFixo;
        return $this;
    }

    /**
     * Get nuDddTelefoneFixo
     *
     * @return string
     */
    public function getNuDddTelefoneFixo()
    {
        return $this->nuDddTelefoneFixo;
    }

    /**
     * Set nuTelefoneFixo
     *
     * @param string $nuTelefoneFixo
     * @return EnderecoUsuarioExterno
     */
    public function setNuTelefoneFixo($nuTelefoneFixo)
    {
        $this->nuTelefoneFixo = $nuTelefoneFixo;
        return $this;
    }

    /**
     * Get nuTelefoneFixo
     *
     * @return string
     */
    public function getNuTelefoneFixo()
    {
        return $this->nuTelefoneFixo;
    }

    /**
     * Set nuDddTelefoneCelular
     *
     * @param string $nuDddTelefoneCelular
     * @return EnderecoUsuarioExterno
     */
    public function setNuDddTelefoneCelular($nuDddTelefoneCelular)
    {
        $this->nuDddTelefoneCelular = $nuDddTelefoneCelular;
        return $this;
    }

    /**
     * Get nuDddTelefoneCelular
     *
     * @return string
     */
    public function getNuDddTelefoneCelular()
    {
        return $this->nuDddTelefoneCelular;
    }

    /**
     * Set nuTelefoneCelular
     *
     * @param string $nuTelefoneCelular
     * @return EnderecoUsuarioExterno
     */
    public function setNuTelefoneCelular($nuTelefoneCelular)
    {
        $this->nuTelefoneCelular = $nuTelefoneCelular;
        return $this;
    }

    /**
     * Get nuTelefoneCelular
     *
     * @return string
     */
    public function getNuTelefoneCelular()
    {
        return $this->nuTelefoneCelular;
    }

    /**
     * Set sqPais
     *
     * @param Sica\Model\Entity\CorporativoPais $sqPais
     * @return EnderecoUsuarioExterno
     */
    public function setSqPais(Pais $sqPais = NULL)
    {
        $this->sqPais = $sqPais;
        return $this;
    }

    /**
     * Get sqPais
     *
     * @return Sica\Model\Entity\CorporativoPais
     */
    public function getSqPais()
    {
        return $this->sqPais ? $this->sqPais : new Pais();
    }

    /**
     * Set sqEstado
     *
     * @param Sica\Model\Entity\CorporativoEstado $sqEstado
     * @return EnderecoUsuarioExterno
     */
    public function setSqEstado(Estado $sqEstado = NULL)
    {
        $this->sqEstado = $sqEstado;
        return $this;
    }

    /**
     * Get sqEstado
     *
     * @return Sica\Model\Entity\CorporativoEstado
     */
    public function getSqEstado()
    {
        return $this->sqEstado ? $this->sqEstado : new Estado();
    }

    /**
     * Set sqMunicipio
     *
     * @param Sica\Model\Entity\CorporativoMunicipio $sqMunicipio
     * @return EnderecoUsuarioExterno
     */
    public function setSqMunicipio(Municipio $sqMunicipio = NULL)
    {
        $this->sqMunicipio = $sqMunicipio;
        return $this;
    }

    /**
     * Get sqMunicipio
     *
     * @return Sica\Model\Entity\CorporativoMunicipio
     */
    public function getSqMunicipio()
    {
        return $this->sqMunicipio ? $this->sqMunicipio : new Municipio();
    }

    public function setCoCep($cep)
    {
        $this->coCep = $cep;
        return $this;
    }

    public function getCoCep()
    {
        return $this->coCep;
    }
}