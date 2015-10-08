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
 * Sgdoce\Model\Entity\VwAreaTrabalho
 *
 * @ORM\Table(name="vw_area_trabalho")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\VwAreaTrabalho", readOnly=true)
 */
class VwAreaTrabalho extends \Core_Model_Entity_Abstract
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
     * @var string $nuDigital
     * @ORM\Column(name="nu_digital", type="string", nullable=true)
     */
    private $nuDigital;

    /**
     * @var string $nuArtefato
     * @ORM\Column(name="nu_artefato", type="string", nullable=true)
     */
    private $nuArtefato;

    /**
     * @var integer $nuTramite
     * @ORM\Column(name="nu_tramite", type="integer", nullable=false)
     */
    private $nuTramite;

    /**
     * @var \Zend_Date $dtCadastro
     * @ORM\Column(name="dt_cadastro", type="zenddate", nullable=false)
     */
    private $dtCadastro;

    /**
     * @var string $noTipoDocumento
     *
     * @ORM\Column(name="no_tipo_documento", type="string", nullable=false)
     */
    private $noTipoDocumento;

    /**
     * @var boolean $inAbreProcesso
     * @ORM\Column(name="in_abre_processo", type="boolean", nullable=false)
     */
    private $inAbreProcesso;

    /**
     * @var string $txAssunto
     *
     * @ORM\Column(name="tx_assunto", type="string", nullable=false)
     */
    private $txAssunto;

    /**
     * @var string $noPessoaOrigem
     * @ORM\Column(name="no_pessoa_origem", type="string", nullable=false)
     */
    private $noPessoaOrigem;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\Column(name="sq_pessoa_recebimento", type="integer", nullable=true)
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa_recebimento", referencedColumnName="sq_pessoa")})
     */
    private $sqPessoaRecebimento;

    /**
     * @var Sgdoce\Model\Entity\StatusTramite
     *
     * @ORM\Column(name="sq_status_tramite", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\StatusTramite")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_status_tramite", referencedColumnName="sq_status_tramite")})
     */
    private $sqStatusTramite;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\Column(name="sq_pessoa_destino", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa_destino", referencedColumnName="sq_pessoa")})
     */
    private $sqPessoaDestino;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\Column(name="sq_pessoa_destino_interno", type="integer", nullable=true)
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa_destino_interno", referencedColumnName="sq_pessoa")})
     */
    private $sqPessoaDestinoInterno;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\Column(name="sq_pessoa_origem", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa_origem", referencedColumnName="sq_pessoa")})
     */
    private $sqPessoaOrigem;

    /**
     * @var Sgdoce\Model\Entity\Prioridade
     *
     * @ORM\Column(name="sq_prioridade", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Prioridade")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_prioridade", referencedColumnName="sq_prioridade")})
     */
    private $sqPrioridade;

    /**
     * @var string $txCodigoRastreamento
     *
     * @ORM\Column(name="tx_codigo_rastreamento", type="string", nullable=true)
     */
    private $txCodigoRastreamento;

    /**
     * @var string $txMovimentacao
     *
     * @ORM\Column(name="tx_movimentacao", type="string", nullable=false)
     */
    private $txMovimentacao;

    /**
     * @var char $coAmbitoProcesso
     *
     * @ORM\Column(name="co_ambito_processo", type="string", nullable=true)
     */
    private $coAmbitoProcesso;


    /**
     * @var Sgdoce\Model\Entity\TipoArtefato
     *
     * @ORM\Column(name="sq_tipo_artefato", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoArtefato")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_tipo_artefato", referencedColumnName="sq_tipo_artefato")})
     */
    private $sqTipoArtefato;

    /**
     * @var Sgdoce\Model\Entity\VwTipoPessoa
     *
     * @ORM\Column(name="sq_tipo_pessoa_destino", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwTipoPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_tipo_pessoa_destino", referencedColumnName="sq_tipo_pessoa")})
     */
    private $sqTipoPessoaDestino;

    /**
     * @var boolean $podeCancelarTramite
     * @ORM\Column(name="pode_cancelar_tramite", type="boolean", nullable=false)
     */
    private $podeCancelarTramite;

    /**
     * @var boolean $podeReceberTramite
     * @ORM\Column(name="pode_receber_tramite", type="boolean", nullable=false)
     */
    private $podeReceberTramite;

    /**
     * @var boolean $hasVinculo
     * @ORM\Column(name="has_vinculo", type="boolean", nullable=false)
     */
    private $hasVinculo;

    /**
     * @var boolean $hasTramiteRastreamento
     * @ORM\Column(name="has_tramite_rastreamento", type="boolean", nullable=false)
     */
    private $hasTramiteRastreamento;

    /**
     * @var boolean $podeArquivar
     * @ORM\Column(name="pode_arquivar", type="boolean", nullable=false)
     */
    private $podeArquivar;

    /**
     * @var boolean $arquivado
     * @ORM\Column(name="arquivado", type="boolean", nullable=false)
     */
    private $arquivado;

    /**
     * @var boolean $hasImagem
     * @ORM\Column(name="has_imagem", type="boolean", nullable=false)
     */
    private $hasImagem;

    /**
     * @var boolean $foiCitado
     * @ORM\Column(name="foi_citado", type="boolean", nullable=false)
     */
    private $foiCitado;

    /**
     * @var boolean $isTramiteExterno
     * @ORM\Column(name="is_tramite_externo", type="boolean", nullable=false)
     */
    private $isTramiteExterno;

    /**
     * @var VwUnidadeOrg
     *
     * @ORM\Column(name="sq_unidade_org_origem_tramite", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_unidade_org_origem_tramite", referencedColumnName="sq_pessoa")})
     */
    private $sqUnidadeOrgOrigemTramite;

    /**
     * @var boolean $hasSolicitacaoAberta
     * @ORM\Column(name="has_solicitacao_aberta", type="boolean", nullable=false)
     */
    private $hasSolicitacaoAberta;

    /**
     * @var Sgdoce\Model\Entity\TipoDocumento
     *
     * @ORM\Column(name="sq_tipo_documento", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoDocumento")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_tipo_documento", referencedColumnName="sq_tipo_documento")})
     */
    private $sqTipoDocumento;

    /**
     * @var boolean $isInconsistente
     * @ORM\Column(name="is_inconsistente", type="boolean", nullable=false)
     */
    private $isInconsistente;




    public function getSqArtefato ()
    {
        return $this->sqArtefato;
    }

    public function getNuDigital ()
    {
        return $this->nuDigital;
    }

    public function getNuArtefato ()
    {
        return $this->nuArtefato;
    }

    public function getNuTramite ()
    {
        return $this->nuTramite;
    }

    public function getDtCadastro ()
    {
        return $this->dtCadastro;
    }

    public function getNoTipoDocumento ()
    {
        return $this->noTipoDocumento;
    }

    public function getInAbreProcesso ()
    {
        return $this->inAbreProcesso;
    }

    public function getTxAssunto ()
    {
        return $this->txAssunto;
    }

    public function getNoPessoaOrigem ()
    {
        return $this->noPessoaOrigem;
    }

    public function getSqPessoaRecebimento ()
    {
        return $this->sqPessoaRecebimento;
    }

    public function getSqStatusTramite ()
    {
        return $this->sqStatusTramite;
    }

    public function getSqPessoaDestino ()
    {
        return $this->sqPessoaDestino;
    }

    public function getSqPessoaDestinoInterno ()
    {
        return $this->sqPessoaDestinoInterno;
    }

    public function getSqPessoaOrigem ()
    {
        return $this->sqPessoaOrigem;
    }

    public function getSqPrioridade ()
    {
        return $this->sqPrioridade;
    }

    public function getTxCodigoRastreamento ()
    {
        return $this->txCodigoRastreamento;
    }

    public function getTxMovimentacao ()
    {
        return $this->txMovimentacao;
    }

    public function getCoAmbitoProcesso ()
    {
        return $this->coAmbitoProcesso;
    }

    public function getSqTipoArtefato ()
    {
        return $this->sqTipoArtefato;
    }

    public function getPodeCancelarTramite ()
    {
        return $this->podeCancelarTramite;
    }

    public function getPodeReceberTramite ()
    {
        return $this->podeReceberTramite;
    }

    public function getSqTipoPessoaDestino ()
    {
        return $this->sqTipoPessoaDestino;
    }

    public function getHasVinculo ()
    {
        return $this->hasVinculo;
    }

    public function getHasTramiteRastreamento ()
    {
        return $this->hasTramiteRastreamento;
    }

    /**
     *
     * @return boolean
     */
    public function getPodeArquivar ()
    {
        return $this->podeArquivar;
    }

    /**
     *
     * @return boolean
     */
    public function getArquivado ()
    {
        return $this->arquivado;
    }

    /**
     *
     * @return boolean
     */
    public function getHasImagem ()
    {
        return $this->hasImagem;
    }

    /**
     *
     * @return boolean
     */
    public function getFoiCitado ()
    {
        return $this->foiCitado;
    }

    /**
     *
     * @return boolean
     */
    public function getIsTramiteExterno ()
    {
        return $this->isTramiteExterno;
    }

    /**
     *
     * @return VwUnidadeOrg
     */
    public function getSqUnidadeOrgOrigemTramite ()
    {
        return $this->sqUnidadeOrgOrigemTramite;
    }

    /**
     *
     * @return boolean
     */
    public function getHasSolicitacaoAberta ()
    {
        return $this->hasSolicitacaoAberta;
    }

    /**
     *
     * @return TipoDocumento
     */
    function getSqTipoDocumento ()
    {
        return $this->sqTipoDocumento;
    }

    /**
     *
     * @return isInconsistente
     */
    public function getIsInconsistente ()
    {
        return $this->isInconsistente;
    }



    public function setSqArtefato ($sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    public function setNuDigital ($nuDigital)
    {
        $this->nuDigital = $nuDigital;
        return $this;
    }

    public function setNuArtefato ($nuArtefato)
    {
        $this->nuArtefato = $nuArtefato;
        return $this;
    }

    public function setNuTramite ($nuTramite)
    {
        $this->nuTramite = $nuTramite;
        return $this;
    }

    public function setDtCadastro (\Zend_Date $dtCadastro)
    {
        $this->dtCadastro = $dtCadastro;
        return $this;
    }

    public function setNoTipoDocumento ($noTipoDocumento)
    {
        $this->noTipoDocumento = $noTipoDocumento;
        return $this;
    }

    public function setInAbreProcesso ($inAbreProcesso)
    {
        $this->inAbreProcesso = $inAbreProcesso;
        return $this;
    }

    public function setTxAssunto ($txAssunto)
    {
        $this->txAssunto = $txAssunto;
        return $this;
    }

    public function setNoPessoaOrigem ($noPessoa)
    {
        $this->noPessoaOrigem = $noPessoa;
        return $this;
    }

    public function setSqPessoaRecebimento ($sqPessoaRecebimento)
    {
        $this->sqPessoaRecebimento = $sqPessoaRecebimento;
        return $this;
    }

    public function setSqStatusTramite ($sqStatusTramite)
    {
        $this->sqStatusTramite = $sqStatusTramite;
        return $this;
    }

    public function setSqPessoaDestino ($sqPessoaDestino)
    {
        $this->sqPessoaDestino = $sqPessoaDestino;
        return $this;
    }

    public function setSqPessoaDestinoInterno ($sqPessoaDestinoInterno)
    {
        $this->sqPessoaDestinoInterno = $sqPessoaDestinoInterno;
        return $this;
    }

    public function setSqPessoaOrigem ($sqPessoaOrigem)
    {
        $this->sqPessoaOrigem = $sqPessoaOrigem;
        return $this;
    }

    public function setSqPrioridade ($sqPrioridade)
    {
        $this->sqPrioridade = $sqPrioridade;
        return $this;
    }

    public function setTxCodigoRastreamento ($txCodigoRastreamento)
    {
        $this->txCodigoRastreamento = $txCodigoRastreamento;
        return $this;
    }

    public function setTxMovimentacao ($txMovimentacao)
    {
        $this->txMovimentacao = $txMovimentacao;
        return $this;
    }

    public function setCoAmbitoProcesso ($coAmbitoProcesso)
    {
        $this->coAmbitoProcesso = $coAmbitoProcesso;
        return $this;
    }

    public function setSqTipoArtefato ($sqTipoArtefato)
    {
        $this->sqTipoArtefato = $sqTipoArtefato;
        return $this;
    }

    public function setSqTipoPessoaDestino ($sqTipoPessoaDestino)
    {
        $this->sqTipoPessoaDestino = $sqTipoPessoaDestino;
        return $this;
    }

    public function setPodeCancelarTramite ($podeCancelarTramite)
    {
        $this->podeCancelarTramite = $podeCancelarTramite;
        return $this;
    }

    public function setPodeReceberTramite ($podeReceverTramite)
    {
        $this->podeReceberTramite = $podeReceverTramite;
        return $this;
    }

    public function setHasVinculo ($hasVinculo)
    {
        $this->hasVinculo = $hasVinculo;
        return $this;
    }

    public function setHasTramiteRastreamento ($hasTramiteRastreamento)
    {
        $this->hasTramiteRastreamento = $hasTramiteRastreamento;
        return $this;
    }

    /**
     *
     * @param boolean $podeArquivar
     * @return VwAreaTrabalho
     */
    public function setPodeArquivar ($podeArquivar)
    {
        $this->podeArquivar = $podeArquivar;
        return $this;
    }

    /**
     *
     * @param boolean $arquivado
     * @return VwAreaTrabalho
     */
    public function setArquivado ($arquivado)
    {
        $this->arquivado = $arquivado;
        return $this;
    }

    /**
     *
     * @param boolean $hasImagem
     * @return VwAreaTrabalho
     */
    public function setHasImagem ($hasImagem)
    {
        $this->hasImagem = $hasImagem;
        return $this;
    }

    /**
     *
     * @param boolean $foiCitado
     * @return VwAreaTrabalho
     */
    public function setFoiCitado ($foiCitado)
    {
        $this->foiCitado = $foiCitado;
        return $this;
    }

    /**
     *
     * @param boolean $isTramiteExterno
     * @return VwAreaTrabalho
     */
    public function setIsTramiteExterno ($isTramiteExterno)
    {
        $this->isTramiteExterno = $isTramiteExterno;
        return $this;
    }

    /**
     *
     * @param VwUnidadeOrg $sqUnidadeOrgOrigemTramite
     * @return VwAreaTrabalho
     */
    public function setSqUnidadeOrgOrigemTramite (VwUnidadeOrg $sqUnidadeOrgOrigemTramite)
    {
        $this->sqUnidadeOrgOrigemTramite = $sqUnidadeOrgOrigemTramite;
        return $this;
    }

    /**
     *
     * @param boolean $hasSolicitacaoAberta
     * @return VwAreaTrabalho
     */
    public function setHasSolicitacaoAberta ($hasSolicitacaoAberta)
    {
        $this->hasSolicitacaoAberta = $hasSolicitacaoAberta;
        return $this;
    }

    /**
     *
     * @param TipoDocumento $sqTipoDocumento
     * @return VwAreaTrabalho
     */
    function setSqTipoDocumento (TipoDocumento $sqTipoDocumento)
    {
        $this->sqTipoDocumento = $sqTipoDocumento;
        return $this;
    }

    /**
     *
     * @param boolean $isInconsistente
     * @return VwAreaTrabalho
     */
    public function setIsInconsistente ($isInconsistente)
    {
        $this->isInconsistente = $isInconsistente;
        return $this;
    }


}