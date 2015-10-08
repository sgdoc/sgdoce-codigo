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
 * Classe para Entity VwPessoaJuridica
 *
 * @package      Model
 * @subpackage  Entity
 * @name         Pessoa
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sgdoce\Model\Entity\VwPessoaJuridica
 *
 * @ORM\Table(name="vw_pessoa_juridica")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwPessoaJuridica", readOnly=true)
 * @OWM\Endpoint(configKey="libcorp" , repositoryClass="Sgdoce\Model\Repository\VwPessoaJuridicaWs")
 */
class VwPessoaJuridica extends \Core_Model_Entity_Abstract
{
    /**
     * @var $sqPessoaJuridica
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa", inversedBy="sqPessoaJuridica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaJuridica;

    /**
     * @var $sqPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * @var $sqTipoPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwTipoPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_pessoa", referencedColumnName="sq_tipo_pessoa")
     * })
     */
    private $sqTipoPessoa;

    /**
     * @var $sqDocumento
     *
     * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\VwDocumento", mappedBy="sqPessoa")
     */
    private $sqDocumento;

    /**
     * @var string $nuCnpj
     *
     * @ORM\Column(name="nu_cnpj", type="string", length=18, nullable=true)
     */
    private $nuCnpj;

    /**
     * @var string $noFantasia
     *
     * @ORM\Column(name="no_fantasia", type="string")
     */
    private $noFantasia;

    /**
     * @var string $noPessoa
     *
     * @ORM\Column(name="no_pessoa", type="string")
     */
    private $noPessoa;

    /**
     * @var string $sgEmpresa
     *
     * @ORM\Column(name="sg_empresa", type="string")
     */
    private $sgEmpresa;

    /**
     * Get $sqPessoaJuridica
     */
    public function getSqPessoaJuridica()
    {
        return $this->sqPessoaJuridica ? : new VwPessoa();
    }

    /**
     * Get $sqPessoa
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa;
    }

    /**
     * Set $sqPessoa
     *
     * @param  $sqPessoa
     * @return VwPessoaJuridica
     */
    public function setSqPessoa($sqPessoa)
    {
        $this->sqPessoa = $sqPessoa;

        return $this;
    }

    /**
     * Get $sqTipoPessoa
     */
    public function getSqTipoPessoa()
    {
        return $this->sqTipoPessoa;
    }

    /**
     * Set $sqTipoPessoa
     *
     * @param  $sqTipoPessoa
     * @return VwPessoaJuridica
     */
    public function setSqTipoPessoa($sqTipoPessoa)
    {
        $this->sqTipoPessoa = $sqTipoPessoa;

        return $this;
    }

    /**
     * Get $sqDocumento
     *
     * @return VwDocumento
     */
    public function getSqDocumento()
    {
        return $this->sqDocumento ? $this->sqDocumento : new VwDocumento();
    }

    /**
     * Set $sqDocumento
     *
     * @param  VwDocumento $sqDocumento
     * @return VwPessoaJuridica
     */
    public function setSqDocumento($sqDocumento)
    {
        $this->sqDocumento = $sqDocumento;

        return $this;
    }

    /**
     * Get $noFantasia
     */
    public function getNoFantasia()
    {
        return $this->noFantasia;
    }

    /**
     * Set $noFantasia
     *
     * @param  $noFantasia
     * @return VwPessoaJuridica
     */
    public function setNoFantasia($noFantasia)
    {
        $this->noFantasia = $noFantasia;

        return $this;
    }

    /**
     * Get $noPessoa
     *
     * @return string
     */
    public function getNoPessoa()
    {
        return $this->noPessoa;
    }

    /**
     * Set $noPessoa
     *
     * @param VwPessoaJuridica
     */
    public function setNoPessoa($noPessoa)
    {
        $this->noPessoa = $noPessoa;

        return $this;
    }

    /**
     * Get $nuCnpj
     */
    public function getNuCnpj()
    {
        return $this->nuCnpj;
    }

    /**
     * Set $nuCnpj
     *
     * @param  $nuCnpj
     * @return VwPessoaJuridica
     */
    public function setNuCnpj($nuCnpj)
    {
        $this->nuCnpj = $nuCnpj;

        return $this;
    }

    /**
     * Get $sgEmpresa
     *
     * @return string
     */
    public function getSgEmpresa()
    {
        return $this->sgEmpresa;
    }

    /**
     * Set $sgEmpresa
     *
     * @param  string $sgEmpresa
     * @return VwPessoaJuridica
     */
    public function setSgEmpresa($sgEmpresa)
    {
        $this->sgEmpresa = $sgEmpresa;

        return $this;
    }
}