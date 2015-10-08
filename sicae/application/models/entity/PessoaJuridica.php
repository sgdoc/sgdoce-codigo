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
 * Classe para Repository de PessoaJuridica
 *
 * @package	 ModelsEntity
 * @category Entity
 * @name	 PessoaJuridica
 * @version	 1.0.0
 */

/**
 * PessoaJuridica
 *
 * @ORM\Table(name="vw_pessoa_juridica")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\PessoaJuridica")
 * @OWM\Endpoint(configKey="libcorp" , repositoryClass="Sica\Model\Repository\PessoaJuridicaWs")
 */
class PessoaJuridica extends \Core_Model_Entity_Abstract
{

    /**
     * @var string $nuCnpj
     *
     * @ORM\Column(name="nu_cnpj", type="string", length=14, nullable=true)
     */
    private $nuCnpj;

    /**
     * @var text $noFantasia
     *
     * @ORM\Column(name="no_fantasia", type="text", nullable=true)
     */
    private $noFantasia;

    /**
     * @var string $sgEmpresa
     *
     * @ORM\Column(name="sg_empresa", type="string", length=10, nullable=true)
     */
    private $sgEmpresa;

    /**
     * @var datetime $dtAbertura
     *
     * @ORM\Column(name="dt_abertura", type="zenddate", nullable=true)
     */
    private $dtAbertura;

    /**
     * @var $inTipoEstabelecimento
     *
     * @ORM\Column(name="in_tipo_estabelecimento", type="integer")
     */
    private $inTipoEstabelecimento;

    /**
     * @var Sica\Model\Entity\Pessoa
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\Pessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     *
     * @var Sica\Model\Entity\Endereco
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\Endereco", mappedBy="sqPessoa")
     */
    private $sqEndereco;

    /**
     *
     * @var Sica\Model\Entity\Telefone
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\Telefone", mappedBy="sqPessoa")
     */
    private $sqTelefone;

    /**
     *
     * @var Sica\Model\Entity\Email
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\Email", mappedBy="sqPessoa")
     */
    private $sqEmail;

    /**
     * Set nuCnpj
     *
     * @param string $nuCnpj
     * @return PessoaJuridica
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
     * Set noFantasia
     *
     * @param text $noFantasia
     * @return PessoaJuridica
     */
    public function setNoFantasia($noFantasia)
    {
        $this->noFantasia = $noFantasia;
        return $this;
    }

    /**
     * Get noFantasia
     *
     * @return text
     */
    public function getNoFantasia()
    {
        return $this->noFantasia;
    }

    /**
     * Set sgEmpresa
     *
     * @param string $sgEmpresa
     * @return PessoaJuridica
     */
    public function setSgEmpresa($sgEmpresa)
    {
        $this->sgEmpresa = $sgEmpresa;
        return $this;
    }

    /**
     * Get sgEmpresa
     *
     * @return string
     */
    public function getSgEmpresa()
    {
        return $this->sgEmpresa;
    }

    /**
     * Set dtAbertura
     *
     * @param datetime $dtAbertura
     * @return PessoaJuridica
     */
    public function setDtAbertura($dtAbertura)
    {
        $this->dtAbertura = $dtAbertura;
        return $this;
    }

    /**
     * Get dtAbertura
     *
     * @return datetime
     */
    public function getDtAbertura()
    {
        return $this->dtAbertura;
    }

    /**
     * Set $inTipoEstabelecimento
     *
     * @param datetime $inTipoEstabelecimento
     * @return PessoaJuridica
     */
    public function setInTipoEstabelecimento($inTipoEstabelecimento)
    {
        $this->inTipoEstabelecimento = $inTipoEstabelecimento;
        return $this;
    }

    /**
     * Get $inTipoEstabelecimento
     *
     * @return string
     */
    public function getInTipoEstabelecimento()
    {
        return $this->inTipoEstabelecimento;
    }

    /**
     * Set sqPessoa
     *
     * @param Sica\Model\Entity\Pessoa $sqPessoa
     * @return PessoaJuridica
     */
    public function setSqPessoa(\Sica\Model\Entity\Pessoa $sqPessoa)
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
        return $this->sqPessoa ? $this->sqPessoa : new \Sica\Model\Entity\Pessoa();
    }

    public function getSqEndereco()
    {
        return $this->sqEndereco ? $this->sqEndereco : new \Sica\Model\Entity\Endereco();
    }

    public function setSqEndereco($sqEndereco)
    {
        $this->sqEndereco = $sqEndereco;
    }

    public function getSqTelefone()
    {
        return $this->sqTelefone ? $this->sqTelefone : new \Sica\Model\Entity\Telefone();
    }

    public function setSqTelefone($sqTelefone)
    {
        $this->sqTelefone = $sqTelefone;
    }

    public function getSqEmail()
    {
        return $this->sqEmail ? $this->sqEmail : new \Sica\Model\Entity\Email();
    }

    public function setSqEmail($sqEmail)
    {
        $this->sqEmail = $sqEmail;
    }

}