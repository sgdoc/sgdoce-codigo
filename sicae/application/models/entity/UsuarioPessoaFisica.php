<?php

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
 * Sica\Model\Entity\UsuarioPessoaFisica
 *
 * @ORM\Table(name="usuario_pessoa_fisica")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\UsuarioPessoaFisica")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class UsuarioPessoaFisica extends \Core_Model_Entity_Abstract
{

    /**
     * @var boolean $inNacionalidadeBrasileira
     *
     * @ORM\Column(name="in_nacionalidade_brasileira", type="boolean", nullable=false)
     */
    private $inNacionalidadeBrasileira = TRUE;

    /**
     * @var string $nuCpf
     *
     * @ORM\Column(name="nu_cpf", type="string", length=11, nullable=false)
     */
    private $nuCpf;

    /**
     * @var string $nuPassaporte
     *
     * @ORM\Column(name="nu_passaporte", type="string", length=50, nullable=true)
     */
    private $nuPassaporte;

    /**
     * @var string $nuRegistroGeral
     *
     * @ORM\Column(name="nu_registro_geral", type="string", length=50, nullable=true)
     */
    private $nuRegistroGeral;

    /**
     * @var string $sgSexo
     *
     * @ORM\Column(name="sg_sexo", type="string", nullable=true)
     */
    private $sgSexo;

    /**
     * @var string $deProfissao
     *
     * @ORM\Column(name="de_profissao", type="string", length=100, nullable=true)
     */
    private $deProfissao;

    /**
     * @var Sica\Model\Entity\UsuarioExterno
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\UsuarioExterno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_usuario_externo", referencedColumnName="sq_usuario_externo")
     * })
     */
    private $sqUsuarioExterno;

    /**
     * @var Sica\Model\Entity\SicaeTipoEscolaridade
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\TipoEscolaridade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_escolaridade", referencedColumnName="sq_tipo_escolaridade")
     * })
     */
    private $sqTipoEscolaridade;

    /**
     * @var Sica\Model\Entity\Pais
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Pais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pais_origem", referencedColumnName="sq_pais")
     * })
     */
    private $sqPaisOrigem;

    /**
     * Set inNacionalidadeBrasileira
     *
     * @param boolean $inNacionalidadeBrasileira
     * @return UsuarioPessoaFisica
     */
    public function setInNacionalidadeBrasileira($inNacionalidadeBrasileira)
    {
        $this->inNacionalidadeBrasileira = $inNacionalidadeBrasileira;
        return $this;
    }

    /**
     * Get inNacionalidadeBrasileira
     */
    public function getInNacionalidadeBrasileira()
    {
        return $this->inNacionalidadeBrasileira;
    }

    /**
     * Set nuCpf
     *
     * @param string $nuCpf
     * @return UsuarioPessoaFisica
     */
    public function setNuCpf($nuCpf)
    {
        $this->nuCpf = $nuCpf;
        return $this;
    }

    /**
     * Get nuCpf
     *
     * @return string
     */
    public function getNuCpf()
    {
        return $this->nuCpf;
    }

    /**
     * Set nuPassaporte
     *
     * @param string $nuPassaporte
     * @return UsuarioPessoaFisica
     */
    public function setNuPassaporte($nuPassaporte)
    {
        $this->nuPassaporte = $nuPassaporte;
        return $this;
    }

    /**
     * Get nuPassaporte
     *
     * @return string
     */
    public function getNuPassaporte()
    {
        return $this->nuPassaporte;
    }

    /**
     * Set nuRegistroGeral
     *
     * @param string $nuRegistroGeral
     * @return UsuarioPessoaFisica
     */
    public function setNuRegistroGeral($nuRegistroGeral)
    {
        $this->nuRegistroGeral = $nuRegistroGeral;
        return $this;
    }

    /**
     * Get nuRegistroGeral
     *
     * @return string
     */
    public function getNuRegistroGeral()
    {
        return $this->nuRegistroGeral;
    }

    /**
     * Set sgSexo
     *
     * @param string $sgSexo
     * @return UsuarioPessoaFisica
     */
    public function setSgSexo($sgSexo)
    {
        $this->sgSexo = $sgSexo;
        return $this;
    }

    /**
     * Get sgSexo
     *
     * @return string
     */
    public function getSgSexo()
    {
        return $this->sgSexo;
    }

    /**
     * Set deProfissao
     *
     * @param string $deProfissao
     * @return UsuarioPessoaFisica
     */
    public function setDeProfissao($deProfissao)
    {
        $this->deProfissao = $deProfissao;
        return $this;
    }

    /**
     * Get deProfissao
     *
     * @return string
     */
    public function getDeProfissao()
    {
        return $this->deProfissao;
    }

    /**
     * Set sqUsuarioExterno
     *
     * @param Sica\Model\Entity\SicaeUsuarioExterno $sqUsuarioExterno
     * @return UsuarioPessoaFisica
     */
    public function setSqUsuarioExterno($sqUsuarioExterno)
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
        return $this->sqUsuarioExterno ? $this->sqUsuarioExterno : new UsuarioExterno();
    }

    /**
     * Set sqTipoEscolaridade
     *
     * @param Sica\Model\Entity\SicaeTipoEscolaridade $sqTipoEscolaridade
     * @return UsuarioPessoaFisica
     */
    public function setSqTipoEscolaridade(\Sica\Model\Entity\TipoEscolaridade $sqTipoEscolaridade = NULL)
    {
        $this->sqTipoEscolaridade = $sqTipoEscolaridade;
        return $this;
    }

    /**
     * Get sqTipoEscolaridade
     *
     * @return Sica\Model\Entity\SicaeTipoEscolaridade
     */
    public function getSqTipoEscolaridade()
    {
        return $this->sqTipoEscolaridade ? $this->sqTipoEscolaridade : new TipoEscolaridade();
    }

    /**
     * Set sqPaisOrigem
     *
     * @param Sica\Model\Entity\Pais $pais
     * @return UsuarioPessoaFisica
     */
    public function setSqPaisOrigem(\Sica\Model\Entity\Pais $pais = NULL)
    {
        $this->sqPaisOrigem = $pais;
        return $this;
    }

    /**
     * Get sqPaisOrigem
     *
     * @return Sica\Model\Entity\Pais
     */
    public function getSqPaisOrigem()
    {
        return $this->sqPaisOrigem ? $this->sqPaisOrigem : new Pais();
    }

}
