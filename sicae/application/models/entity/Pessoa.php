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
 * Classe para Entity Pessoa
 *
 * @package      Model
 * @subpackage     Entity
 * @name         Pessoa
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sica\Model\Entity\Pessoa
 *
 * @ORM\Table(name="vw_pessoa")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\Pessoa", readOnly=true)
 * @OWM\Endpoint(configKey="libcorp" , repositoryClass="Sica\Model\Repository\PessoaWs")
 */
class Pessoa extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqPessoa
     *
     * @ORM\Column(name="sq_pessoa", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqPessoa;

    /**
     * @var string $noPessoa
     *
     * @ORM\Column(name="no_pessoa", type="string", nullable=false)
     */
    private $noPessoa;

    /**
     * @var string $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="integer", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     *
     * @var Sica\Model\Entity\PessoaFisica
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\PessoaFisica", mappedBy="sqPessoa", fetch="EAGER")
     */
    private $sqPessoaFisica;

    /**
     *
     * @var Sica\Model\Entity\PessoaJuridica
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\PessoaJuridica", mappedBy="sqPessoa", fetch="EAGER")
     */
    private $sqPessoaJuridica;

    /**
     *
     * @var Sica\Model\Entity\PessoaVinculo
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\PessoaVinculo", mappedBy="sqPessoa", fetch="EAGER")
     */
    private $sqPessoaVinculo;

    /**
     *
     * @var Sica\Model\Entity\VinculoSistemico
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\VinculoSistemico", mappedBy="sqPessoa", fetch="EAGER")
     */
    private $sqVinculoSistemico;

    /**
     * @var Sica\Model\Entity\Pessoa
     *
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\Telefone", mappedBy="sqPessoa", fetch="EAGER")
     */
    private $telefone;

    /**
     * @var Sica\Model\Entity\Email
     *
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\Email", mappedBy="sqPessoa", fetch="EAGER")
     */
    private $email;

    /**
     * @var Sica\Model\Entity\Usuario
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\Usuario", mappedBy="sqPessoa", fetch="EAGER")
     */
    private $sqUsuario;

    /**
     *
     * @var Sica\Model\Entity\UnidadeOrg
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\UnidadeOrg", mappedBy="sqUnidadeOrgPessoa", fetch="EAGER")
     */
    private $sqUnidadeOrg;

    /**
     * @var Sica\Model\Entity\CadastroSemCpf
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\CadastroSemCpf", mappedBy="sqPessoa", fetch="EAGER")
     */
    private $sqCadastroSemCpf;

    /**
     * @var Sica\Model\Entity\CadastroSemCpf
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\UnidadeOrg", mappedBy="sqUnidadeOrgPessoa")
     */
    private $unidade;

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
     * @var Sica\Model\Entity\NaturezaJuridica
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\NaturezaJuridica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_natureza_juridica", referencedColumnName="sq_natureza_juridica")
     * })
     */
    private $sqNaturezaJuridica;

    /**
     *
     * @var Sica\Model\Entity\IntegracaoPessoaInfoconv
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\IntegracaoPessoaInfoconv", mappedBy="sqPessoa", fetch="EAGER")
     */
    private $sqIntegracaoPessoaInfoconv;

    public function setSqPessoa($sqPessoa)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    public function getSqPessoa()
    {
        return $this->sqPessoa;
    }

    public function getNoPessoa()
    {
        return $this->noPessoa;
    }

    public function setNoPessoa($noPessoa)
    {
        $this->noPessoa = $noPessoa;
        return $this;
    }

    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }

    public function setStRegistroAtivo($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;
    }

    public function getSqPessoaFisica()
    {
        return $this->sqPessoaFisica ? $this->sqPessoaFisica : new \Sica\Model\Entity\PessoaFisica();
    }

    public function setSqPessoaFisica(\Sica\Model\Entity\PessoaFisica $sqPessoaFisica)
    {
        $this->sqPessoaFisica = $sqPessoaFisica;
        return $this;
    }

    public function getSqPessoaJuridica()
    {
        return $this->sqPessoaJuridica ? $this->sqPessoaJuridica : new \Sica\Model\Entity\PessoaJuridica();
    }

    public function setSqPessoaJuridica(\Sica\Model\Entity\PessoaJuridica $sqPessoaJuridica)
    {
        $this->sqPessoaJuridica = $sqPessoaJuridica;
        return $this;
    }

    public function getSqPessoaVinculo()
    {
        return $this->sqPessoaVinculo ? $this->sqPessoaVinculo : new \Sica\Model\Entity\PessoaVinculo();
    }

    public function setSqPessoaVinculo(\Sica\Model\Entity\PessoaJuridica $sqPessoaVinculo)
    {
        $this->sqPessoaVinculo = $sqPessoaVinculo;
        return $this;
    }

    public function getSqVinculoSistemico()
    {
        return $this->sqVinculoSistemico ? $this->sqVinculoSistemico : new \Sica\Model\Entity\VinculoSistemico();
    }

    public function setSqVinculoSistemico(\Sica\Model\Entity\VinculoSistemico $sqVinculoSistemico)
    {
        $this->sqVinculoSistemico = $sqVinculoSistemico;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getTelefone()
    {
        return $this->telefone;
    }

    public function getSqUsuario()
    {
        return $this->sqUsuario ? $this->sqUsuario : new \Sica\Model\Entity\Usuario();
    }

    public function setSqUsuario(\Sica\Model\Entity\Usuario $sqUsuario)
    {
        $this->sqUsuario = $sqUsuario;
    }

    public function getSqCadastroSemCpf()
    {
        return $this->sqCadastroSemCpf ? $this->sqCadastroSemCpf : new \Sica\Model\Entity\CadastroSemCpf();
    }

    public function setSqCadastroSemCpf(\Sica\Model\Entity\CadastroSemCpf $sqCadastroSemCpf)
    {
        $this->sqCadastroSemCpf = $sqCadastroSemCpf;
    }

    public function getSqTipoPessoa()
    {
        return $this->sqTipoPessoa ? $this->sqTipoPessoa : new \Sica\Model\Entity\TipoPessoa();
    }

    public function setSqTipoPessoa(\Sica\Model\Entity\TipoPessoa $sqTipoPessoa)
    {
        $this->sqTipoPessoa = $sqTipoPessoa;
    }

    /**
     * Set sqNaturezaJuridica
     *
     * @param Sica\Model\Entity\NaturezaJuridica $sqNaturezaJuridica
     * @return Pessoa
     */
    public function setSqNaturezaJuridica(\Sica\Model\Entity\NaturezaJuridica $sqNaturezaJuridica = NULL)
    {
        $this->sqNaturezaJuridica = $sqNaturezaJuridica;
        return $this;
    }

    /**
     * Get sqNaturezaJuridica
     *
     * @return Sica\Model\Entity\NaturezaJuridica
     */
    public function getSqNaturezaJuridica()
    {
        return $this->sqNaturezaJuridica ? $this->sqNaturezaJuridica : new \Sica\Model\Entity\NaturezaJuridica();
    }

    /**
     * Set sqIntegracaoPessoaInfoconv
     *
     * @param \Sica\Model\Entity\IntegracaoPessoaInfoconv $sqIntegracaoPessoaInfoconv
     * @return Pessoa
     */
    public function setSqIntegracaoPessoaInfoconv( \Sica\Model\Entity\IntegracaoPessoaInfoconv $sqIntegracaoPessoaInfoconv )
    {
        $this->sqIntegracaoPessoaInfoconv = $sqIntegracaoPessoaInfoconv;
        return $this;
    }

    /**
     * Get IntegracaoPessoaInfoconv
     *
     * @return IntegracaoPessoaInfoconv
     */
    public function getSqIntegracaoPessoaInfoconv()
    {
        return $this->sqIntegracaoPessoaInfoconv ? $this->sqIntegracaoPessoaInfoconv : new \Sica\Model\Entity\IntegracaoPessoaInfoconv();
    }

}