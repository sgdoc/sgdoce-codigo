<?php

/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM,
    Core\Model\OWM\Mapping as OWM;

/**
 * SISICMBio
 *
 * Classe para Entity Pessoa Fisica
 *
 * @package      Model
 * @subpackage     Entity
 * @name         PessoaFisica
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sica\Model\Entity\PessoaFisica
 *
 * @ORM\Table(name="vw_pessoa_fisica")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\PessoaFisica", readOnly=true)
 * @OWM\Endpoint(configKey="libcorp" , repositoryClass="Sica\Model\Repository\PessoaFisicaWs")
 */
class PessoaFisica extends \Core_Model_Entity_Abstract
{

    /**
     * @var string $nuCpf
     *
     * @ORM\Column(name="nu_cpf", type="string", length=11, nullable=true)
     */
    private $nuCpf;

    /**
     * @var string $noMae
     *
     * @ORM\Column(name="no_mae", type="string", length=120, nullable=true)
     */
    private $noMae;

    /**
     * @var string $noPai
     *
     * @ORM\Column(name="no_pai", type="string", length=120, nullable=true)
     */
    private $noPai;

    /**
     * @var string $sgSexo
     *
     * @ORM\Column(name="sg_sexo", type="string", nullable=true)
     */
    private $sgSexo;

    /**
     * @var string $nuCurriculoLates
     *
     * @ORM\Column(name="nu_curriculo_lates", type="string", length=50, nullable=true)
     */
    private $nuCurriculoLates;

    /**
     * @var datetime $dtNascimento
     *
     * @ORM\Column(name="dt_nascimento", type="zenddate", nullable=true)
     */
    private $dtNascimento;

    /**
     * @var string $noProfissao
     *
     * @ORM\Column(name="no_profissao", type="string", length=50, nullable=true)
     */
    private $noProfissao;

    /**
     * @var Sica\Model\Entity\Pessoa
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\Pessoa", inversedBy="sqPessoaFisica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * @var Sica\Model\Entity\Municipio
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Municipio", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_naturalidade", referencedColumnName="sq_municipio")
     * })
     */
    private $sqMunicipio;

    /**
     * @var Sica\Model\Entity\Pais
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Pais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_nacionalidade", referencedColumnName="sq_pais")
     * })
     */
    private $sqPais;

    /**
     *
     * @var Sica\Model\Entity\Email
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\Email", mappedBy="sqPessoa")
     */
    private $sqEmail;

    /**
     *
     * @var Sica\Model\Entity\Telefone
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\Telefone", mappedBy="sqPessoa")
     */
    private $sqTelefone;

    /**
     *
     * @var Sica\Model\Entity\Endereco
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\Endereco", mappedBy="sqPessoa")
     */
    private $sqEndereco;

    /**
     * @var Sica\Model\Entity\TipoPessoa
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\TipoPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_pessoa", referencedColumnName="sq_tipo_pessoa")
     * })
     */
    private $sqTipoPessoa;

    /**
     * @var Sica\Model\Entity\EstadoCivil
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\EstadoCivil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_estado_civil", referencedColumnName="sq_estado_civil")
     * })
     */
    private $sqEstadoCivil;

    /**
     * Set nuCpf
     *
     * @param string $nuCpf
     * @return PessoaFisica
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
     * Set noMae
     *
     * @param string $noMae
     * @return PessoaFisica
     */
    public function setNoMae($noMae)
    {
        $this->noMae = $noMae;
        return $this;
    }

    /**
     * Get noMae
     *
     * @return string
     */
    public function getNoMae()
    {
        return $this->noMae;
    }

    /**
     * Set noPai
     *
     * @param string $noPai
     * @return PessoaFisica
     */
    public function setNoPai($noPai)
    {
        $this->noPai = $noPai;
        return $this;
    }

    /**
     * Get noPai
     *
     * @return string
     */
    public function getNoPai()
    {
        return $this->noPai;
    }

    /**
     * Set sgSexo
     *
     * @param string $sgSexo
     * @return PessoaFisica
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
     * Set nuCurriculoLates
     *
     * @param string $nuCurriculoLates
     * @return PessoaFisica
     */
    public function setNuCurriculoLates($nuCurriculoLates)
    {
        $this->nuCurriculoLates = $nuCurriculoLates;
        return $this;
    }

    /**
     * Get nuCurriculoLates
     *
     * @return string
     */
    public function getNuCurriculoLates()
    {
        return $this->nuCurriculoLates;
    }

    /**
     * Set dtNascimento
     *
     * @param datetime $dtNascimento
     * @return PessoaFisica
     */
    public function setDtNascimento($dtNascimento)
    {
        $this->dtNascimento = $dtNascimento;
        return $this;
    }

    /**
     * Get dtNascimento
     *
     * @return datetime
     */
    public function getDtNascimento()
    {
        return $this->dtNascimento;
    }

    /**
     * Set noProfissao
     *
     * @param string $noProfissao
     * @return PessoaFisica
     */
    public function setNoProfissao($noProfissao)
    {
        $this->noProfissao = $noProfissao;
        return $this;
    }

    /**
     * Get noProfissao
     *
     * @return string
     */
    public function getNoProfissao()
    {
        return $this->noProfissao;
    }

    /**
     * Set sqPessoa
     *
     * @param Sica\Model\Entity\Pessoa $sqPessoa
     * @return PessoaFisica
     */
    public function setSqPessoa(Pessoa $sqPessoa)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return Sica\Model\Entity\Pessoa
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa ? $this->sqPessoa : new Pessoa();
    }

    /**
     * Set sqEstadoCivil
     *
     * @param Sica\Model\Entity\EstadoCivil $sqEstadoCivil
     * @return PessoaFisica
     */
    public function setSqEstadoCivil(EstadoCivil $sqEstadoCivil = NULL)
    {
        $this->sqEstadoCivil = $sqEstadoCivil;
        return $this;
    }

    /**
     * Get sqEstadoCivil
     *
     * @return Sica\Model\Entity\EstadoCivil
     */
    public function getSqEstadoCivil()
    {
        return $this->sqEstadoCivil ? $this->sqEstadoCivil : new EstadoCivil();
    }

    /**
     * Set Municipio
     *
     * @param Sica\Model\Entity\Municipio $sqMunicipio
     * @return PessoaFisica
     */
    public function setSqMunicipio(Municipio $sqMunicipio = NULL)
    {
        $this->sqMunicipio = $sqMunicipio;
        return $this;
    }

    /**
     * Get sqNaturalidade
     *
     * @return Sica\Model\Entity\Municipio
     */
    public function getSqMunicipio()
    {
        return $this->sqMunicipio ? $this->sqMunicipio : new Municipio();
    }

    /**
     * Set $sqPais
     *
     * @param Sica\Model\Entity\Pais $sqPais
     * @return PessoaFisica
     */
    public function setSqPais(Pais $sqPais = NULL)
    {
        $this->sqPais = $sqPais;
        return $this;
    }

    /**
     * Get sqNaturalidade
     *
     * @return Sica\Model\Entity\Pais
     */
    public function getSqPais()
    {
        return $this->sqPais ? $this->sqPais : new \Sica\Model\Entity\Pais();
    }

    public function getSqEmail()
    {
        return $this->sqEmail ? $this->sqEmail : new \Sica\Model\Entity\Email();
    }

    public function setSqEmail($sqEmail)
    {
        $this->sqEmail = $sqEmail;
    }

    public function getSqTelefone()
    {
        return $this->sqTelefone ? $this->sqTelefone : new \Sica\Model\Entity\Telefone();
    }

    public function setSqTelefone($sqTelefone)
    {
        $this->sqTelefone = $sqTelefone;
    }

    public function getSqEndereco()
    {
        return $this->sqEndereco ? $this->sqEndereco : new \Sica\Model\Entity\Endereco();
    }

    public function setSqEndereco($sqEndereco)
    {
        $this->sqEndereco = $sqEndereco;
    }

    /**
     * Set sqTipoPessoa
     *
     * @param Sica\Model\Entity\TipoPessoa $sqTipoPessoa
     * @return PessoaJuridica
     */
    public function setSqTipoPessoa(TipoPessoa $sqTipoPessoa)
    {
        $this->sqTipoPessoa = $sqTipoPessoa;
        return $this;
    }

    /**
     * Get sqTipoPessoa
     *
     * @return Sica\Model\Entity\TipoPessoa
     */
    public function getSqTipoPessoa()
    {
        if (NULL === $this->sqTipoPessoa) {
            $this->setSqTipoPessoa(new TipoPessoa());
        }
        return $this->sqTipoPessoa;
    }

}
