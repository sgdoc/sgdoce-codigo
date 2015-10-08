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
use Core\Model\OWM\Mapping as OWM;

/**
 * Sgdoce\Model\Entity\SolicitacaoDownloadImagem
 *
 * @ORM\Table(name="solicitacao_download_imagem")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\SolicitacaoDownloadImagem")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class SolicitacaoDownloadImagem extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqSolicitacao
     *
     * @ORM\Column(name="sq_solicitacao_download_imagem", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqSolicitacaoDownloadImagem;

    /**
     * @var Artefato $sqArtefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     * })
     */
    private $sqArtefato;

    /**
     * @var VwPessoa $sqPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * @var VwUnidadeOrg $sqUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_org", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeOrg;

    /**
     * @var \Zend_Date $dtSolicitacao
     *
     * @ORM\Column(name="dt_solicitacao", type="zenddate", nullable=false)
     */
    private $dtSolicitacao;

    /**
     * @var boolean $stProcessado
     *
     * @ORM\Column(name="st_processado", type="boolean", nullable=false)
     */
    private $stProcessado;

    /**
     * @var Integer $stProcessado
     *
     * @ORM\Column(name="in_tentativa", type="integer", nullable=false)
     */
    private $inTentativa;

    /**
     * @var string $txLink
     *
     * @ORM\Column(name="tx_link", type="string", length=500, nullable=true)
     */
    private $txLink;

    /**
     * @var \Zend_Date $dtDownload
     *
     * @ORM\Column(name="dt_download", type="zenddate", nullable=true)
     */
    private $dtDownload;

    /**
     * @var string $txEmail
     *
     * @ORM\Column(name="tx_email", type="string", length=500, nullable=true)
     */
    private $txEmail;

    /**
     *
     * @return integer
     */
    public function getSqSolicitacaoDownloadImagem ()
    {
        return $this->sqSolicitacaoDownloadImagem;
    }

    /**
     *
     * @return Artefato
     */
    public function getSqArtefato ()
    {
        return $this->sqArtefato;
    }

    /**
     *
     * @return VwPessoa
     */
    public function getSqPessoa ()
    {
        return $this->sqPessoa;
    }

    /**
     *
     * @return VwUnidadeOrg
     */
    public function getSqUnidadeOrg ()
    {
        return $this->sqUnidadeOrg;
    }

    /**
     *
     * @return \Zend_Date
     */
    public function getDtSolicitacao ()
    {
        return $this->dtSolicitacao;
    }

    /**
     *
     * @return boolean
     */
    public function getStProcessado ()
    {
        return $this->stProcessado;
    }

    /**
     *
     * @return integer
     */
    public function getInTentativa ()
    {
        return $this->inTentativa;
    }

    /**
     *
     * @return string
     */
    public function getTxLink ()
    {
        return $this->txLink;
    }

    /**
     *
     * @return \Zend_Date
     */
    public function getDtDownload ()
    {
        return $this->dtDownload;
    }

    /**
     *
     * @return string
     */
    public function getTxEmail ()
    {
        return $this->txEmail;
    }

    function setSqSolicitacaoDownloadImagem ($sqSolicitacaoDownloadImagem)
    {
        $this->sqSolicitacaoDownloadImagem = $sqSolicitacaoDownloadImagem;
        return $this;
    }

    function setSqArtefato (Artefato $sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    function setSqPessoa (VwPessoa $sqPessoa)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    function setSqUnidadeOrg (VwUnidadeOrg $sqUnidadeOrg)
    {
        $this->sqUnidadeOrg = $sqUnidadeOrg;
        return $this;
    }

    function setDtSolicitacao (\Zend_Date $dtSolicitacao)
    {
        $this->dtSolicitacao = $dtSolicitacao;
        return $this;
    }

    function setStProcessado ($stProcessado = FALSE)
    {
        $this->stProcessado = $stProcessado;
        return $this;
    }

    function setInTentativa ($inTentativa = 0)
    {
        $this->inTentativa = $inTentativa;
        return $this;
    }

    function setTxLink ($txLink)
    {
        $this->txLink = $txLink;
        return $this;
    }

    function setDtDownload (\Zend_Date $dtDownload)
    {
        $this->dtDownload = $dtDownload;
        return $this;
    }

    function setTxEmail ($txEmail)
    {
        $this->txEmail = $txEmail;
        return $this;
    }
}
