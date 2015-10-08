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
namespace Sgdoce\Model\Entity;

use Doctrine\ORM\Mapping as ORM,
    Core\Model\OWM\Mapping as OWM;
/**
 * SISICMBio
 *
 * Classe para Entity PessoaFisica
 *
 * @package      Model
 * @subpackage  Entity
 * @name         Pessoa
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sgdoce\Model\Entity\VwPessoaFisica
 *
 * @ORM\Table(name="vw_pessoa_fisica")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwPessoaFisica", readOnly=true)
 * @OWM\Endpoint(configKey="libcorp" , repositoryClass="Sgdoce\Model\Repository\VwPessoaFisicaWs")
 */
class VwPessoaFisica extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqPessoaFisica
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa", inversedBy="sqPessoaFisica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa", nullable=false)
     * })
     */
    private $sqPessoaFisica;

    /**
     * @var string $noPessoa
     *
     * @ORM\Column(name="no_pessoa", type="string", length=120, nullable=false)
     */
    private $noPessoaFisica;

    /**
     * @var string $nuCpf
     *
     * @ORM\Column(name="nu_cpf", type="string", length=14, nullable=true)
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
     * @var Sgdoce\Model\Entity\VwMunicipio
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwMunicipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_naturalidade", referencedColumnName="sq_municipio")
     * })
     */
    private $sqMunicipio;

    /**
     * @var Sgdoce\Model\Entity\VwPais
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_nacionalidade", referencedColumnName="sq_pais")
     * })
     */
    private $sqPais;

    /**
     *
     * @var Sgdoce\Model\Entity\VwEmail
     * @ORM\OneToMany(targetEntity="Sgdoce\Model\Entity\VwEmail", mappedBy="sqPessoa")
     */
    private $sqEmail;

    /**
     *
     * @var Sgdoce\Model\Entity\VwTelefone
     * @ORM\OneToMany(targetEntity="Sgdoce\Model\Entity\VwTelefone", mappedBy="sqPessoa")
     */
    private $sqTelefone;

    /**
     *
     * @var Sgdoce\Model\Entity\VwEndereco
     * @ORM\OneToMany(targetEntity="Sgdoce\Model\Entity\VwEndereco", mappedBy="sqPessoa")
     */
    private $sqEndereco;

    /**
     * @var Sgdoce\Model\Entity\VwTipoPessoa
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwTipoPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_pessoa", referencedColumnName="sq_tipo_pessoa")
     * })
     */
    private $sqTipoPessoa;

    /**
     * @var Sgdoce\Model\Entity\VwEstadoCivil
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwEstadoCivil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_estado_civil", referencedColumnName="sq_estado_civil")
     * })
     */
    private $sqEstadoCivil;

    /**
     * @var Sgdoce\Model\Entity\VwPais
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_nacionalidade", referencedColumnName="sq_pais")
     * })
     */
    private $sqNacionalidade;

    public function getSqPessoaFisica()
    {
        return $this->sqPessoaFisica ? $this->sqPessoaFisica : new VwPessoa();
    }

    public function getNoPessoaFisica()
    {
        return $this->noPessoaFisica;
    }

    public function setNoPessoaFisica($noPessoaFisica)
    {
        $this->assert('noPessoaFisica',$noPessoaFisica,$this);
        $this->noPessoaFisica = $noPessoaFisica;
        return $this;
    }

    public function getNuCpf()
    {
        return $this->nuCpf;
    }

    public function setNuCpf($nuCpf)
    {
        $nuCpf = \Zend_Filter::filterStatic($nuCpf, 'Digits');
        $this->assert('nuCpf',$nuCpf,$this);
        $this->nuCpf = $nuCpf;
        return $this;
    }

    /**
     * Set noMae
     *
     * @param string $noMae
     * @return PessoaFisica
     */
    public function setNoMae($noMae)
    {
        $this->assert('noMae',$noMae,$this);
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
     * Set sqEstadoCivil
     *
     * @param Sgdoce\Model\Entity\VwEstadoCivil $sqEstadoCivil
     * @return PessoaFisica
     */
    public function setSqEstadoCivil(VwEstadoCivil $sqEstadoCivil = NULL)
    {
        $this->sqEstadoCivil = $sqEstadoCivil;
        return $this;
    }

    /**
     * Get sqEstadoCivil
     *
     * @return Sgdoce\Model\Entity\VwEstadoCivil
     */
    public function getSqEstadoCivil()
    {
        return $this->sqEstadoCivil ? $this->sqEstadoCivil : new VwEstadoCivil();
    }

    /**
     * Set Municipio
     *
     * @param Sgdoce\Model\Entity\VwMunicipio $sqMunicipio
     * @return PessoaFisica
     */
    public function setSqMunicipio(VwMunicipio $sqMunicipio = NULL)
    {
        $this->sqMunicipio = $sqMunicipio;
        return $this;
    }

    /**
     * Get sqNaturalidade
     *
     * @return Sgdoce\Model\Entity\VwMunicipio
     */
    public function getSqMunicipio()
    {
        return $this->sqMunicipio ? $this->sqMunicipio : new VwMunicipio();
    }

    /**
     * Set $sqPais
     *
     * @param Sgdoce\Model\Entity\VwPais $sqPais
     * @return PessoaFisica
     */
    public function setSqPais(VwPais $sqPais = NULL)
    {
        $this->sqPais = $sqPais;
        return $this;
    }

    /**
     * Get sqNaturalidade
     *
     * @return Sgdoce\Model\Entity\VwPais
     */
    public function getSqPais()
    {
        return $this->sqPais ? $this->sqPais : new VwPais();
    }

    public function getSqEmail()
    {
        return $this->sqEmail;
    }

    public function setSqEmail($sqEmail)
    {
        $this->sqEmail = $sqEmail;
    }

    public function getSqTelefone()
    {
        return $this->sqTelefone;
    }

    public function setSqTelefone($sqTelefone)
    {
        $this->sqTelefone = $sqTelefone;
    }

    public function getSqEndereco()
    {
        return $this->sqEndereco;
    }

    public function setSqEndereco($sqEndereco)
    {
        $this->sqEndereco = $sqEndereco;
    }

    /**
     * Set sqTipoPessoa
     *
     * @param Sgdoce\Model\Entity\VwTipoPessoa $sqTipoPessoa
     * @return PessoaJuridica
     */
    public function setSqTipoPessoa(VwTipoPessoa $sqTipoPessoa)
    {
        $this->sqTipoPessoa = $sqTipoPessoa;
        return $this;
    }

    /**
     * Get sqTipoPessoa
     *
     * @return Sgdoce\Model\Entity\VwTipoPessoa
     */
    public function getSqTipoPessoa()
    {
        if (NULL === $this->sqTipoPessoa) {
            $this->setSqTipoPessoa(new VwTipoPessoa());
        }
        return $this->sqTipoPessoa;
    }

    public function setSqNacionalidade(VwPais $sqNacionalidade = NULL)
    {
        $this->sqNacionalidade = $sqNacionalidade;
    }

    /**
     * Set sqNacionalidade
     *
     * @param Sgdoce\Model\Entity\VwPais $sqNacionalidade
     * @return VwPais
     */
    public function getSqNacionalidade()
    {
        return $this->sqNacionalidade ? $this->sqNacionalidade : new VwPais();
    }
}