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
* Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA
* */
namespace Sgdoce\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
* Sgdoce\Model\Entity\ArtefatoImagem
*
* @ORM\Table(name="artefato_imagem")
* @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ArtefatoImagem")
* @OWM\Logger(eventLog="insert::update::delete")
*/
class ArtefatoImagem extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqArtefatoImagem
     *
     * @ORM\Id
     * @ORM\Column(name="sq_artefato_imagem", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqArtefatoImagem;

    /**
     * @var integer $sqArtefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     * })
     */
    private $sqArtefato;

    /**
     * @var integer $sqPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * @var integer $sqUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_org", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeOrg;

    /**
     * @var string $noArquivo
     *
     * @ORM\Column(name="no_arquivo", type="string", length=32, nullable=false)
     */
    private $noArquivo;

    /**
     * @var string $txHash
     *
     * @ORM\Column(name="tx_hash", type="string", length=64, nullable=false)
     */
    private $txHash;

    /**
     * @var bigint $nuBytes
     *
     * @ORM\Column(name="nu_bytes", type="bigint", nullable=false)
     */
    private $nuBytes;

    /**
     * @var integer $nuQtdePaginas
     *
     * @ORM\Column(name="nu_qtde_paginas", type="integer", nullable=false)
     */
    private $nuQtdePaginas;

    /**
     * @var string $txObservacao
     *
     * @ORM\Column(name="tx_observacao", type="string", length=500, nullable=true)
     */
    private $txObservacao;

    /**
     * @var zenddate $dtOperacao
     *
     * @ORM\Column(name="dt_Operacao", type="zenddate", nullable=true)
     */
    private $dtOperacao;

    /**
     * @var boolean $stAtivo
     *
     * @ORM\Column(name="st_ativo", type="boolean", nullable=true)
     */
    private $stAtivo;

    /**
     * @var integer $sqPessoaInativacao
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_inativacao", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaInativacao;

    /**
     * @var integer $sqUnidadeOrgInativacao
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_org_inativacao", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeOrgInativacao;

    /**
     * @var \Zend_Date $dtInativacao
     *
     * @ORM\Column(name="dt_inativacao", type="zenddate", nullable=true)
     */
    private $dtInativacao;

    /**
     * Set sqArtefatoImagem
     *
     * @param integer $sqArtefatoImagem
     * @return Sgdoce\Model\Entity\ArtefatoImagem
     */
    public function setSqArtefatoImagem ($sqArtefatoImagem)
    {
        $this->sqArtefatoImagem = $sqArtefatoImagem;
        return $this;
    }

    /**
     * Get sqArtefatoImagem
     *
     * @return integer
     */
    public function getSqArtefatoImagem ()
    {
        return $this->sqArtefatoImagem;
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return Sgdoce\Model\Entity\ArtefatoImagem
     */
    public function setSqArtefato (\Sgdoce\Model\Entity\Artefato $sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    /**
     * Get sqArtefato
     *
     * @return Sgdoce\Model\Entity\Artefato
     */
    public function getSqArtefato ()
    {
        return $this->sqArtefato;
    }

    /**
     * Set sqPessoa
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqPessoa
     * @return Sgdoce\Model\Entity\ArtefatoImagem
     */
    public function setSqPessoa ($sqPessoa)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return Sgdoce\Model\Entity\VwPessoa
     */
    public function getSqPessoa ()
    {
        return $this->sqPessoa;
    }

    /**
     * Set sqUnidadeOrg
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqUnidadeOrg
     * @return Sgdoce\Model\Entity\ArtefatoImagem
     */
    public function setSqUnidadeOrg ($sqUnidadeOrg)
    {
        $this->sqUnidadeOrg = $sqUnidadeOrg;
        return $this;
    }

    /**
     * Get sqUnidadeOrg
     *
     * @return Sgdoce\Model\Entity\VwUnidadeOrg
     */
    public function getSqUnidadeOrg ()
    {
        return $this->sqUnidadeOrg;
    }

    /**
     * Set noArquivo
     *
     * @param string $noArquivo
     * @return Sgdoce\Model\Entity\ArtefatoImagem
     */
    public function setNoArquivo ($noArquivo)
    {
        $this->noArquivo = $noArquivo;
        return $this;
    }

    /**
     * Get noArquivo
     *
     * @return string
     */
    public function getNoArquivo ()
    {
        return $this->noArquivo;
    }

    /**
     * Set txHash
     *
     * @param string $txHash
     * @return Sgdoce\Model\Entity\ArtefatoImagem
     */
    public function setTxHash ($txHash)
    {
        $this->txHash = $txHash;
        return $this;
    }

    /**
     * Get txHash
     *
     * @return string
     */
    public function getTxHash ()
    {
        return $this->txHash;
    }

    /**
     * Set nuBytes
     *
     * @param bigint $nuBytes
     * @return Sgdoce\Model\Entity\ArtefatoImagem
     */
    public function setNuBytes ($nuBytes)
    {
        $this->nuBytes = $nuBytes;
        return $this;
    }

    /**
     * Get nuBytes
     *
     * @return bigint
     */
    public function getNuBytes ()
    {
        return $this->nuBytes;
    }

    /**
     * Set nuQtdePaginas
     *
     * @param integer $nuQtdePaginas
     * @return Sgdoce\Model\Entity\ArtefatoImagem
     */
    public function setNuQtdePaginas ($nuQtdePaginas)
    {
        $this->nuQtdePaginas = $nuQtdePaginas;
        return $this;
    }

    /**
     * Get nuQtdePaginas
     *
     * @return integer
     */
    public function getNuQtdePaginas ()
    {
        return $this->nuQtdePaginas;
    }

    /**
     * Set txObservacao
     *
     * @param string $txObservacao
     * @return Sgdoce\Model\Entity\ArtefatoImagem
     */
    public function setTxObservacao ($txObservacao)
    {
        $this->txObservacao = $txObservacao;
        return $this;
    }

    /**
     * Get txObservacao
     *
     * @return string
     */
    public function getTxObservacao ()
    {
        return $this->txObservacao;
    }

    /**
     * Set dtOperacao
     *
     * @param Zend_Date $dtOperacao
     * @return Sgdoce\Model\Entity\ArtefatoImagem
     */
    public function setDtOperacao (\Zend_Date $dtOperacao)
    {
        $this->dtOperacao = $dtOperacao;
        return $this;
    }

    /**
     * Get dtOperacao
     *
     * @return Zend_Date
     */
    public function getDtOperacao ()
    {
        return $this->dtOperacao;
    }

    function getSqPessoaInativacao() {
        return $this->sqPessoaInativacao;
    }

    function getSqUnidadeOrgInativacao() {
        return $this->sqUnidadeOrgInativacao;
    }

    function setSqPessoaInativacao(VwPessoa $sqPessoaInativacao) {
        $this->sqPessoaInativacao = $sqPessoaInativacao;
        return $this;
    }

    function setSqUnidadeOrgInativacao( VwUnidadeOrg $sqUnidadeOrgInativacao) {
        $this->sqUnidadeOrgInativacao = $sqUnidadeOrgInativacao;
        return $this;
    }

    function getStAtivo() {
        return $this->stAtivo;
    }

    function setStAtivo($stAtivo = TRUE) {
        $this->stAtivo = $stAtivo;
        return $this;
    }

    function getDtInativacao() {
        return $this->dtInativacao;
    }

    function setDtInativacao(\Zend_Date $dtInativacao) {
        $this->dtInativacao = $dtInativacao;
        return $this;
    }
}