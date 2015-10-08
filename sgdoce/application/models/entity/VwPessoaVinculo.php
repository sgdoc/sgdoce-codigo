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
use Doctrine\ORM\Mapping as ORM;

/**
 * SISICMBio
 *
 * Classe para Entity Pessoa Vinculo
 *
 * @package      Model
 * @subpackage   Entity
 * @name         Pessoa Vinculo
 * @version      1.0.0
 * @since        2012-06-26
 */

/**
 * Sgdoce\Model\Entity\VwPessoaVinculo
 *
 * @ORM\Table(name="vw_pessoa_vinculo")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwPessoaVinculo", readOnly=true)
 */
class VwPessoaVinculo extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqPessoaVinculo
     *
     * @ORM\Column(name="sq_pessoa_vinculo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqPessoaVinculo;

    /**
     * @var string $noCargo
     *
     * @ORM\Column(name="no_cargo", type="string", length=50, nullable=true)
     */
    private $noCargo;

    /**
     * @var datetime $dtInicioVinculo
     *
     * @ORM\Column(name="dt_inicio_vinculo", type="zenddate", nullable=true)
     */
    private $dtInicioVinculo;

    /**
     * @var datetime $dtFimVinculo
     *
     * @ORM\Column(name="dt_fim_vinculo", type="zenddate", nullable=true)
     */
    private $dtFimVinculo;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     * @var Sgdoce\Model\Entity\VwTipoVinculo
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwTipoVinculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_vinculo", referencedColumnName="sq_tipo_vinculo")
     * })
     */
    private $sqTipoVinculo;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_relacionamento", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeOrgRelacionamento;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     */
    private $sqPessoa;

    /**
     * Set sqPessoaVinculo
     *
     * @param string $sqPessoaVinculo
     * @return VwPessoaVinculo
     */
    public function setSqPessoaVinculo($sqPessoaVinculo)
    {
        $this->sqPessoaVinculo = $sqPessoaVinculo;
        return $this;
    }
    /**
     * Get sqPessoaVinculo
     *
     * @return integer
     */
    public function getSqPessoaVinculo()
    {
        return $this->sqPessoaVinculo;
    }

    /**
     * Set noCargo
     *
     * @param string $noCargo
     * @return PessoaVinculo
     */
    public function setNoCargo($noCargo)
    {
        $this->noCargo = $noCargo;
        return $this;
    }

    /**
     * Get noCargo
     *
     * @return string
     */
    public function getNoCargo()
    {
        return $this->noCargo;
    }

    /**
     * Set dtInicioVinculo
     *
     * @param datetime $dtInicioVinculo
     * @return PessoaVinculo
     */
    public function setDtInicioVinculo($dtInicioVinculo)
    {
        $this->dtInicioVinculo = $dtInicioVinculo;
        return $this;
    }

    /**
     * Get dtInicioVinculo
     *
     * @return datetime
     */
    public function getDtInicioVinculo()
    {
        return $this->dtInicioVinculo;
    }

    /**
     * Set dtFimVinculo
     *
     * @param datetime $dtFimVinculo
     * @return PessoaVinculo
     */
    public function setDtFimVinculo($dtFimVinculo)
    {
        $this->dtFimVinculo = $dtFimVinculo;
        return $this;
    }

    /**
     * Get dtFimVinculo
     *
     * @return datetime
     */
    public function getDtFimVinculo()
    {
        return $this->dtFimVinculo;
    }

    /**
     * Set stRegistroAtivo
     *
     * @param boolean $stRegistroAtivo
     * @return PessoaVinculo
     */
    public function setStRegistroAtivo($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }

    /**
     * Get stRegistroAtivo
     *
     * @return boolean
     */
    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }

    /**
     * Set sqTipoVinculo
     *
     * @param \Sgdoce\Model\Entity\VwTipoVinculo $sqTipoVinculo
     * @return PessoaVinculo
     */
    public function setSqTipoVinculo(VwTipoVinculo $sqTipoVinculo = NULL)
    {
        $this->sqTipoVinculo = $sqTipoVinculo;
        return $this;
    }

    /**
     * Get sqTipoVinculo
     *
     * @return \Sgdoce\Model\Entity\TipoVinculo
     */
    public function getSqTipoVinculo()
    {
        return $this->sqTipoVinculo ? $this->sqTipoVinculo : new VwTipoVinculo();
    }

    /**
     * Set sqUnidadeOrgRelacionamento
     *
     * @param Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeOrgRelacionamento
     * @return UnidadeOrgVinculo
     */
    public function setSqUnidadeOrgRelacionamento(VwUnidadeOrg $sqUnidadeOrgRelacionamento = NULL)
    {
        $this->sqUnidadeOrgRelacionamento = $sqUnidadeOrgRelacionamento;
        return $this;
    }

    /**
     * Get sqUnidadeOrgRelacionamento
     *
     * @return Sgdoce\Model\Entity\VwUnidadeOrg
     */
    public function getSqUnidadeOrgRelacionamento()
    {
        return $this->sqUnidadeOrgRelacionamento ?
        $this->sqUnidadeOrgRelacionamento :
        new \Sgdoce\Model\Entity\VwUnidadeOrg();
    }

    public function setSqPessoa(VwPessoa $sqPessoa)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    public function getSqPessoa()
    {
        return $this->sqPessoa ? $this->sqPessoa : new VwPessoa();
    }
}