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
 * VwUltimoHistoricoCaixaArquivo
 *
 * @ORM\Table(name="vw_ultimo_historico_caixa_arquivo")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwUltimoHistoricoCaixaArquivo")
 */
 class VwUltimoHistoricoCaixaArquivo extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqArtefato
     *
     * @ORM\Column(name="sq_artefato", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqArtefato;

    /**
     * @var \Sgdoce\Model\Entity\CaixaHistorico
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\CaixaHistorico")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_caixa_historico", referencedColumnName="sq_caixa_historico", nullable=false)})
     */
    private $sqCaixaHistorico;

    /**
     * @var \Sgdoce\Model\Entity\Caixa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Caixa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_caixa", referencedColumnName="sq_caixa", nullable=false)})
     */
    private $sqCaixa;

    /**
     * @var \Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa_operacao", referencedColumnName="sq_pessoa", nullable=false)})
     */
    private $sqPessoaOperacao;

    /**
     * @var \Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_unidade_org_usuario_operacao", referencedColumnName="sq_pessoa", nullable=false)})
     */
    private $sqUnidadeOperacao;

    /**
     * @var \Sgdoce\Model\Entity\TipoHistoricoArquivo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoHistoricoArquivo")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="sq_tipo_historico_arquivo", referencedColumnName="sq_tipo_historico_arquivo", nullable=false)
     * })
     */
    private $sqTipoHistorico;

    /**
     * @var \Zend_Date $dtOperacao
     *
     * @ORM\Column(name="dt_operacao", type="zenddate", nullable=false)
     */
    private $dtOperacao;

    /**
     * @var boolean $emprestimo
     *
     * @ORM\Column(name="emprestimo", type="boolean", nullable=false)
     */
    private $emprestimo;


    public function getSqArtefato ()
    {
        return $this->sqArtefato;
    }

    public function getSqCaixaHistorico ()
    {
        return $this->sqCaixaHistorico;
    }

    public function getSqCaixa ()
    {
        return $this->sqCaixa;
    }

    public function getSqPessoaOperacao ()
    {
        return $this->sqPessoaOperacao;
    }

    public function getSqUnidadeOperacao ()
    {
        return $this->sqUnidadeOperacao;
    }

    public function getSqTipoHistorico ()
    {
        return $this->sqTipoHistorico;
    }

    public function getDtOperacao ()
    {
        return $this->dtOperacao;
    }

    public function getEmprestimo ()
    {
        return $this->emprestimo;
    }

    public function setSqArtefato ($sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    public function setSqCaixaHistorico (\Sgdoce\Model\Entity\CaixaHistorico $sqCaixaHistorico)
    {
        $this->sqCaixaHistorico = $sqCaixaHistorico;
        return $this;
    }

    public function setSqCaixa (\Sgdoce\Model\Entity\Caixa $sqCaixa)
    {
        $this->sqCaixa = $sqCaixa;
        return $this;
    }

    public function setSqPessoaOperacao (\Sgdoce\Model\Entity\VwPessoa $sqPessoaOperacao)
    {
        $this->sqPessoaOperacao = $sqPessoaOperacao;
        return $this;
    }

    public function setSqUnidadeOperacao (\Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeOperacao)
    {
        $this->sqUnidadeOperacao = $sqUnidadeOperacao;
        return $this;
    }

    public function setSqTipoHistorico (\Sgdoce\Model\Entity\TipoHistoricoArquivo $sqTipoHistorico)
    {
        $this->sqTipoHistorico = $sqTipoHistorico;
        return $this;
    }

    public function setDtOperacao (\Zend_Date $dtOperacao)
    {
        $this->dtOperacao = $dtOperacao;
        return $this;
    }

    public function setEmprestimo ($emprestimo)
    {
        $this->emprestimo = $emprestimo;
        return $this;
    }




}