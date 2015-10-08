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
use Doctrine\DBAL\Types\BooleanType;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sgdoce\Model\Entity\VwUltimoTramiteArtefato
 *
 * @ORM\Table(name="vw_ultimo_tramite_artefato")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\VwUltimoTramiteArtefato", readOnly=true)
 */
class VwUltimoTramiteArtefato extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqArtefato
     *
     * @ORM\Id
     * @ORM\Column(name="sq_artefato", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqArtefato;


    /**
     * @var integer $sqTramiteArtefato
     * @ORM\Column(name="sq_tramite_artefato", type="integer", nullable=false)
     */
    private $sqTramiteArtefato;

    /**
     * @var Sgdoce\Model\Entity\StatusTramite
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\StatusTramite")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_status_tramite", referencedColumnName="sq_status_tramite")})
     */
    private $sqStatusTramite;

    /**
     * @var Sgdoce\Model\Entity\TipoRastreamentoCorreio
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoRastreamentoCorreio")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_tipo_rastreamento", referencedColumnName="sq_tipo_rastreamento_correio")})
     */
    private $sqTipoRastreamento;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa_recebimento", referencedColumnName="sq_pessoa")})
     */
    private $sqPessoaRecebimento;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa_tramite", referencedColumnName="sq_pessoa")})
     */
    private $sqPessoaTramite;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_destino", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaDestino;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa_destino_interno", referencedColumnName="sq_pessoa")})
     */
    private $sqPessoaDestinoInterno;

    /**
     * @var Sgdoce\Model\Entity\Endereco
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwEndereco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_endereco", referencedColumnName="sq_endereco")
     * })
     */
    private $sqEndereco;

    /**
     * @var integer $nuTramite
     *
     * @ORM\Column(name="nu_tramite", type="integer", nullable=false)
     */
    private $nuTramite;

    /**
     * @var boolean $inImpresso
     *
     * @ORM\Column(name="in_impresso", type="boolean", nullable=false)
     */
    private $inImpresso;

    /**
     * @var boolean $inAvisoRecebimento
     *
     * @ORM\Column(name="in_aviso_recebimento", type="boolean", nullable=true)
     */
    private $inAvisoRecebimento;

    /**
     * @var string $txCodigoRastreamento
     *
     * @ORM\Column(name="tx_codigo_rastreamento", type="string", length=15, nullable=true)
     */
    private $txCodigoRastreamento;

    /**
     * @var zenddate $dtRecebimento
     *
     * @ORM\Column(name="dt_recebimento", type="zenddate", nullable=true)
     */
    private $dtRecebimento;

    /**
     * @var zenddate $dtCancelamento
     *
     * @ORM\Column(name="dt_cancelamento", type="zenddate", nullable=true)
     */
    private $dtCancelamento;

    /**
     * @var zenddate $dtDevolucao
     *
     * @ORM\Column(name="dt_devolucao", type="zenddate", nullable=true)
     */
    private $dtDevolucao;

    /**
     * @var string $txJustificativaDevolucao
     *
     * @ORM\Column(name="tx_justificativa_devolucao", type="string", length=250, nullable=true)
     */
    private $txJustificativaDevolucao;

    /**
     * @var zenddate $dtTramite
     *
     * @ORM\Column(name="dt_tramite", type="zenddate", nullable=false)
     */
    private $dtTramite;

    /**
     * @var VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_unidade_org_tramite", referencedColumnName="sq_pessoa")})
     */
    private $sqUnidadeOrgTramite;

    /**
     * Get sqTramiteArtefato
     *
     * @return integer
     */
    public function getSqTramiteArtefato()
    {
        return $this->sqTramiteArtefato;
    }

    /**
     * Get sqTramiteArtefato
     *
     * @return Artefato
     */
    public function getSqArtefato()
    {
        return $this->sqArtefato;
    }

    /**
     * Get sqStatusTramite
     *
     * @return StatusTramite
     */
    public function getSqStatusTramite()
    {
        return $this->sqStatusTramite;
    }

    /**
     * Get sqTipoRastreamento
     *
     * @return TipoRastreamentoCorreio
     */
    public function getSqTipoRastreamento()
    {
        return $this->sqTipoRastreamento;
    }

    /**
     * Get sqPessoaRecebimento
     *
     * @return VwPessoa
     */
    public function getSqPessoaRecebimento()
    {
        return $this->sqPessoaRecebimento;
    }

    /**
     * Get sqPessoaTramite
     *
     * @return VwPessoa
     */
    public function getSqPessoaTramite()
    {
        return $this->sqPessoaTramite;
    }

    /**
     * Get sqPessoaDestino
     *
     * @return Sgdoce\Model\Entity\VwUnidadeOrg
     */
    public function getSqPessoaDestino()
    {
        return $this->sqPessoaDestino;
    }

    /**
     * Get sqPessoaDestinoInterno
     *
     * @return VwPessoa
     */
    public function getSqPessoaDestinoInterno()
    {
        return $this->sqPessoaDestinoInterno;
    }

    /**
     * Get sqEndereco
     *
     * @return VwEndereco
     */
    public function getSqEndereco()
    {
        return $this->sqEndereco;
    }

    /**
     * Get nuTramite
     *
     * @return integer
     */
    public function getNuTramite()
    {
        return $this->nuTramite;
    }

    /**
     * Get inImpresso
     *
     * @return boolean
     */
    public function getInImpresso()
    {
        return $this->inImpresso;
    }

    /**
     * Get inAvisoRecebimento
     *
     * @return boolean
     */
    public function getInAvisoRecebimento()
    {
        return $this->inAvisoRecebimento;
    }

    /**
     * Get txEmailConhecimento
     *
     * @return string
     */
    public function getTxCodigoRastreamento()
    {
        return $this->txCodigoRastreamento;
    }

    /**
     * Get dtRecebimento
     *
     * @return Zend_Date
     */
    public function getDtRecebimento()
    {
        return $this->dtRecebimento;
    }

    /**
     * Get dtCancelamento
     *
     * @return Zend_Date
     */
    public function getDtCancelamento()
    {
        return $this->dtCancelamento;
    }

    /**
     * Get dtDevolucao
     *
     * @return Zend_Date
     */
    public function getDtDevolucao()
    {
        return $this->dtDevolucao;
    }

    /**
     * Get txJustificativaDevolucao
     *
     * @return string
     */
    public function getTxJustificativaDevolucao()
    {
        return $this->txJustificativaDevolucao;
    }

    /**
     * Get dtTramite
     *
     * @return Zend_Date
     */
    public function getDtTramite()
    {
        return $this->dtTramite;
    }

    /**
     * Get sqUnidadeOrgTramite
     *
     * @return VwUnidadeOrg
     */
    public function getSqUnidadeOrgTramite ()
    {
        return $this->sqUnidadeOrgTramite;
    }

    /**
     * Set sqArtefato
     *
     * @param Artefato $entityArtefato
     * @return VwUltimoTramiteArtefato
     */
    public function setSqArtefato(Artefato $entityArtefato)
    {
        $this->sqArtefato = $entityArtefato;
        return $this;
    }

    /**
     * Set sqStatusTramite
     *
     * @param StatusTramite $entityStatusTramite
     * @return VwUltimoTramiteArtefato
     */
    public function setSqStatusTramite(StatusTramite $entityStatusTramite)
    {
        $this->sqStatusTramite = $entityStatusTramite;
        return $this;
    }

    /**
     * Set sqTipoRastreamento
     *
     * @param TipoRastreamentoCorreio $entityTipoRastreamento
     * @return VwUltimoTramiteArtefato
     */
    public function setSqTipoRastreamento(TipoRastreamentoCorreio $entityTipoRastreamento = null)
    {
        $this->sqTipoRastreamento = $entityTipoRastreamento;
        return $this;
    }

    /**
     * Set sqPessoaRecebimento
     *
     * @param VwPessoa $entityPessoa
     * @return VwUltimoTramiteArtefato
     */
    public function setSqPessoaRecebimento(VwPessoa $entityPessoa = null)
    {
        $this->sqPessoaRecebimento = $entityPessoa;
        return $this;
    }

    /**
     * Set sqPessoaRecebimento
     *
     * @param VwPessoa $entityPessoa
     * @return VwUltimoTramiteArtefato
     */
    public function setSqPessoaTramite(VwPessoa $entityPessoa)
    {
        $this->sqPessoaTramite = $entityPessoa;
        return $this;
    }

    /**
     * Set sqPessoaDestino
     *
     * @param Sgdoce\Model\Entity\VwPessoa $entityPessoa
     * @return VwUltimoTramiteArtefato
     */
    public function setSqPessoaDestino(VwPessoa $entityPessoa = null)
    {
        $this->sqPessoaDestino = $entityPessoa;
        return $this;
    }

    /**
     * Set sqPessoaDestinoInterno
     *
     * armazenar a pessoa de destino do tramite interno (SO É USADO PARA TRAMITE DENTRO DA MESMA UNIDADE)
     *
     * @param VwPessoa $entityPessoa
     * @return VwUltimoTramiteArtefato
     */
    public function setSqPessoaDestinoInterno(VwPessoa $entityPessoa = null)
    {
        $this->sqPessoaDestinoInterno = $entityPessoa;
        return $this;
    }

    /**
     * Set sqEndereco
     *
     * @param VwEndereco $entityVwEndereco
     * @return VwUltimoTramiteArtefato
     */
    public function setSqEndereco(VwEndereco $entityVwEndereco = NULL)
    {
        $this->sqEndereco = $entityVwEndereco;
        return $this;
    }

    /**
     * Set nuTramite
     *
     * @param integer $nuTramite
     * @return VwUltimoTramiteArtefato
     */
    public function setNuTramite($nuTramite)
    {
        $this->nuTramite = $nuTramite;
        return $this;
    }

    /**
     * Set inImpresso
     *
     * @param boolean $inImpresso
     * @return VwUltimoTramiteArtefato
     */
    public function setInImpresso($inImpresso = true)
    {
        $this->inImpresso = $inImpresso;
        return $this;
    }

    /**
     * Set inAvisoRecebimento
     *
     * @param boolean $inAvisoRecebimento
     * @return VwUltimoTramiteArtefato
     */
    public function setInAvisoRecebimento($inAvisoRecebimento)
    {
        $this->inAvisoRecebimento = $inAvisoRecebimento;
        return $this;
    }

    /**
     * Set txCodigoRastreamento
     *
     * @param string $txCodigoRastreamento
     * @return VwUltimoTramiteArtefato
     */
    public function setTxCodigoRastreamento($txCodigoRastreamento)
    {
        $this->txCodigoRastreamento = $txCodigoRastreamento;
        return $this;
    }

    /**
     * Set dtRecebimento
     *
     * @param Zend_Date $dtRecebimento
     * @return VwUltimoTramiteArtefato
     */
    public function setDtRecebimento(\Zend_Date $dtRecebimento = null)
    {
        $this->dtRecebimento = $dtRecebimento;
        return $this;
    }

    /**
     * Set dtCancelamento
     *
     * @param Zend_Date $dtCancelamento
     * @return TramiteArtefato
     */
    public function setDtCancelamento(\Zend_Date $dtCancelamento = null)
    {
        $this->dtCancelamento = $dtCancelamento;
        return $this;
    }

    /**
     * Set dtDevolucao
     *
     * @param Zend_Date $dtDevolucao
     * @return TramiteArtefato
     */
    public function setDtDevolucao(\Zend_Date $dtDevolucao = null)
    {
        $this->dtDevolucao = $dtDevolucao;
        return $this;
    }

    /**
     * Set txJustificativaDevolucao
     *
     * @param string $txJustificativaDevolucao
     * @return VwUltimoTramiteArtefato
     */
    public function setTxJustificativaDevolucao($txJustificativaDevolucao)
    {
        $this->txJustificativaDevolucao = $txJustificativaDevolucao;
        return $this;
    }

    /**
     * Set dtTramite
     *
     * @param Zend_Date $dtTramite
     * @return VwUltimoTramiteArtefato
     */
    public function setDtTramite(\Zend_Date $dtTramite)
    {
        $this->dtTramite = $dtTramite;
        return $this;
    }

    /**
     *
     * @param VwUnidadeOrg $sqUnidadeOrgTramite
     * @return VwUltimoTramiteArtefato
     */
    public function setSqUnidadeOrgTramite (VwUnidadeOrg $sqUnidadeOrgTramite)
    {
        $this->sqUnidadeOrgTramite = $sqUnidadeOrgTramite;
        return $this;
    }

    public function isReceived()
    {
        return ($this->getSqStatusTramite()->getSqStatusTramite() == \Core_Configuration::getSgdoceStatusTramiteRecebido());
    }

    public function isTramited()
    {
        return ($this->getSqStatusTramite()->getSqStatusTramite() == \Core_Configuration::getSgdoceStatusTramiteTramitado());
    }

    public function isCanceled()
    {
        return ($this->getSqStatusTramite()->getSqStatusTramite() == \Core_Configuration::getSgdoceStatusTramiteCancelado());
    }

    public function isReturned()
    {
        return ($this->getSqStatusTramite()->getSqStatusTramite() == \Core_Configuration::getSgdoceStatusTramiteDevolvido());
    }
}