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
 * Classe para Entity Pessoa Vinculo
 *
 * @package      Model
 * @subpackage     Entity
 * @name         Pessoa Vinculo
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sica\Model\Entity\PessoaVinculo
 *
 * @ORM\Table(name="vw_pessoa_vinculo")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\PessoaVinculo", readOnly=true)
 * @OWM\Endpoint(configKey="libcorp" , repositoryClass="Sica\Model\Repository\PessoaVinculoWs")
 */
class PessoaVinculo extends \Core_Model_Entity_Abstract
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
     * @var Sica\Model\Entity\TipoVinculo
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\TipoVinculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_vinculo", referencedColumnName="sq_tipo_vinculo")
     * })
     */
    private $sqTipoVinculo;

    /**
     * @var Sica\Model\Entity\Pessoa
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Pessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_relacionamento", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaRelacionamento;

    /**
     * @var Sica\Model\Entity\Pessoa
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Pessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * Set sqPessoaVinculo
     *
     * @param string $sqPessoaVinculo
     * @return PessoaVinculo
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
     */
    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }

    /**
     * Set sqTipoVinculo
     *
     * @param Sica\Model\Entity\TipoVinculo $sqTipoVinculo
     * @return PessoaVinculo
     */
    public function setSqTipoVinculo(TipoVinculo $sqTipoVinculo = NULL)
    {
        $this->sqTipoVinculo = $sqTipoVinculo;
        return $this;
    }

    /**
     * Get sqTipoVinculo
     *
     * @return Sica\Model\Entity\TipoVinculo
     */
    public function getSqTipoVinculo()
    {
        return $this->sqTipoVinculo ? $this->sqTipoVinculo : new TipoVinculo();
    }

    /**
     * Set sqPessoaRelacionamento
     *
     * @param Sica\Model\Entity\Pessoa $sqPessoaRelacionamento
     * @return Pessoa
     */
    public function setSqPessoaRelacionamento(Pessoa $sqPessoaRelacionamento = NULL)
    {
        $this->sqPessoaRelacionamento = $sqPessoaRelacionamento;
        return $this;
    }

    /**
     * Get sqPessoaRelacionamento
     *
     * @return Sica\Model\Entity\Pessoa
     */
    public function getSqPessoaRelacionamento()
    {
        return $this->sqPessoaRelacionamento ? $this->sqPessoaRelacionamento : new Pessoa();
    }

    /**
     * Set sqPessoa
     *
     * @param Sica\Model\Entity\Pessoa $sqPessoa
     * @return PessoaVinculo
     */
    public function setSqPessoa(\Sica\Model\Entity\Pessoa $sqPessoa = NULL)
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

}