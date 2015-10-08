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
 * Profissional
 *
 * @ORM\Table(name="vw_profissional_interno")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwProfissional")
 */
 class VwProfissional extends \Core_Model_Entity_Abstract
{

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_profissional", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqProfissional;

    /**
     * @var datetime $dtIngressoOrgao
     *
     * @ORM\Column(name="dt_ingresso_orgao", type="zenddate", nullable=false)
     */
    private $dtIngressoOrgao;

    /**
     * @var string $nuMatriculaSiape
     *
     * @ORM\Column(name="nu_matricula_siape", type="string", nullable=false)
     */
    private $nuMatriculaSiape;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_lotacao", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeLotacao;

    /**
     * @var Sgdoce\Model\Entity\VwAtribuicao
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwAtribuicao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_atribuicao", referencedColumnName="sq_atribuicao")
     * })
     */
    private $sqAtribuicao;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_exercicio", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeExercicio;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_exercicio", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeExercicioInterna;

    /**
     * @var Sgdoce\Model\Entity\VwCargo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwCargo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_cargo", referencedColumnName="sq_cargo")
     * })
     */
    private $sqCargo;

    /**
     * @var Sgdoce\Model\Entity\VwSituacaoFuncional
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwSituacaoFuncional")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_situacao_funcional", referencedColumnName="sq_situacao_funcional")
     * })
     */
    private $sqSituacaoFuncional;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_profissional", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * Set dtIngressoOrgao
     *
     * @param datetime $dtIngressoOrgao
     * @return Profissional
     */
    public function setDtIngressoOrgao($dtIngressoOrgao)
    {
        $this->dtIngressoOrgao = $dtIngressoOrgao;
        return $this;
    }

    /**
     * Get dtIngressoOrgao
     *
     * @return datetime
     */
    public function getDtIngressoOrgao()
    {
        return $this->dtIngressoOrgao;
    }

    /**
     * Set nuMatriculaSiape
     *
     * @param string $nuMatriculaSiape
     * @return Profissional
     */
    public function setNuMatriculaSiape($nuMatriculaSiape)
    {
        $this->nuMatriculaSiape = $nuMatriculaSiape;
        return $this;
    }

    /**
     * Get nuMatriculaSiape
     *
     * @return string
     */
    public function getNuMatriculaSiape()
    {
        return $this->nuMatriculaSiape;
    }

    /**
     * Set sqUnidadeLotacao
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqUnidadeLotacao
     * @return Profissional
     */
    public function setSqUnidadeLotacao(\Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeLotacao = NULL)
    {
        $this->sqUnidadeLotacao = $sqUnidadeLotacao;
        return $this;
    }

    /**
     * Get sqUnidadeLotacao
     *
     * @return Sgdoce\Model\Entity\Pessoa
     */
    public function getSqUnidadeLotacao()
    {
        return $this->sqUnidadeLotacao;
    }

    /**
     * Set sqAtribuicao
     *
     * @param Sgdoce\Model\Entity\VwAtribuicao $sqAtribuicao
     * @return Profissional
     */
    public function setSqAtribuicao(\Sgdoce\Model\Entity\VwAtribuicao $sqAtribuicao = NULL)
    {
        $this->sqAtribuicao = $sqAtribuicao;
        return $this;
    }

    /**
     * Get sqAtribuicao
     *
     * @return Sgdoce\Model\Entity\Atribuicao
     */
    public function getSqAtribuicao()
    {
        return $this->sqAtribuicao;
    }

    /**
     * Set sqUnidadeExercicio
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqUnidadeExercicio
     * @return Profissional
     */
    public function setSqUnidadeExercicio(VwUnidadeOrg $sqUnidadeExercicio = NULL)
    {
        $this->sqUnidadeExercicio = $sqUnidadeExercicio;
        return $this;
    }

    /**
     * Get sqUnidadeExercicio
     *
     * @return Sgdoce\Model\Entity\Pessoa
     */
    public function getSqUnidadeExercicio()
    {
        return $this->sqUnidadeExercicio ? $this->sqUnidadeExercicio : new VwUnidadeOrg();
    }

    /**
     * Set sqCargo
     *
     * @param Sgdoce\Model\Entity\VwCargo $sqCargo
     * @return Profissional
     */
    public function setSqCargo(\Sgdoce\Model\Entity\VwCargo $sqCargo = NULL)
    {
        $this->sqCargo = $sqCargo;
        return $this;
    }

    /**
     * Get sqCargo
     *
     * @return Sgdoce\Model\Entity\Cargo
     */
    public function getSqCargo()
    {
        return $this->sqCargo;
    }

    /**
     * Set sqSituacaoFuncional
     *
     * @param Sgdoce\Model\Entity\SituacaoFuncional $sqSituacaoFuncional
     * @return Profissional
     */
    public function setSqSituacaoFuncional(\Sgdoce\Model\Entity\VwSituacaoFuncional $sqSituacaoFuncional = NULL)
    {
        $this->sqSituacaoFuncional = $sqSituacaoFuncional;
        return $this;
    }

    /**
     * Get sqSituacaoFuncional
     *
     * @return Sgdoce\Model\Entity\SituacaoFuncional
     */
    public function getSqSituacaoFuncional()
    {
        return $this->sqSituacaoFuncional;
    }

    /**
     * Set sqProfissional
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqProfissional
     * @return Profissional
     */
    public function setSqProfissional(\Sgdoce\Model\Entity\VwPessoa $sqProfissional)
    {
        $this->sqProfissional = $sqProfissional;
        return $this;
    }

    /**
     * Get sqProfissional
     *
     * @return Sgdoce\Model\Entity\VwPessoa
     */
    public function getSqProfissional()
    {
        return $this->sqProfissional;
    }
}