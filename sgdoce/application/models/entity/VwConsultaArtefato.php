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
 * Classe para Entity vwConsultaArtefato
 *
 * @package      Model
 * @subpackage   Entity
 * @name         vwConsultaArtefato
 * @version      1.0.0
 * @since        2013-06-07
 */

/**
 * Sgdoce\Model\Entity\VwConsultaArtefato
 *
 * @ORM\Table(name="vw_consulta_artefato")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwConsultaArtefato", readOnly=true)
 */
class VwConsultaArtefato extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqArtefato
     *
     * @ORM\Column(name="sq_artefato", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    private $sqArtefato;

    /**
     * @var string $nuDigital
     *
     * @ORM\Column(name="nu_digital", type="string", nullable=true)
     */
    private $nuDigital;

    /**
     * @var integer $nuArtefato
     *
     * @ORM\Column(name="nu_artefato", type="string", nullable=true)
     */
    private $nuArtefato;

    /**
     * @var string $dtArtefato
     *
     * @ORM\Column(name="dt_artefato", type="zenddate", nullable=true)
     */
    private $dtArtefato;

    /**
     * @var string $txAssuntoComplementar
     *
     * @ORM\Column(name="tx_assunto_complementar", type="string", nullable=true)
     */
    private $txAssuntoComplementar;

    /**
     * @var Sgdoce\Model\Entity\TipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoDocumento")
     * @ORM\JoinColumn(name="sq_tipo_documento", referencedColumnName="sq_tipo_documento")
     */
    private $sqTipoDocumento;

    /**
     * @var string $noTipoDocumento
     *
     * @ORM\Column(name="no_tipo_documento", type="string", nullable=false)
     *
     */
    private $noTipoDocumento;

    /**
     * @var string $noTipoArtefato
     *
     * @ORM\Column(name="no_tipo_artefato", type="string", nullable=false)
     *
     */
    private $noTipoArtefato;

    /**
     * @var integer $sqTipoArtefato
     *
     * @ORM\Column(name="sq_tipo_artefato", type="integer", nullable=false)
     */
    private $sqTipoArtefato;

    /**
     * @var integer $sqPessoaSgdoceOrigem
     *
     * @ORM\Column(name="sq_pessoa_sgdoce_origem", type="integer", nullable=false)
     */
    private $sqPessoaSgdoceOrigem;

    /**
     * @var string $noPessoaOrigem
     *
     * @ORM\Column(name="no_pessoa_origem", type="string", nullable=true)
     */
    private $noPessoaOrigem;

    /**
     * @var integer $sqPessoaInteressada
     *
     * @ORM\Column(name="sq_pessoa_interessada", type="integer", nullable=false)
     */
    private $sqPessoaInteressada;

    /**
     * @var string $noPessoaInteressada
     *
     * @ORM\Column(name="no_pessoa_interessada", type="string", nullable=true)
     */
    private $noPessoaInteressada;

    /**
     * @var integer $sqPessoaDestino
     *
     * @ORM\Column(name="sq_pessoa_destino", type="integer", nullable=true)
     */
    private $sqPessoaDestino;

    /**
     * @var integer $sqPessoaRecebido
     *
     * @ORM\Column(name="sq_pessoa_recebido", type="integer", nullable=true)
     */
    private $sqPessoaRecebido;

    /**
     * @var string $unidadeOrg
     *
     * @ORM\Column(name="unidade_org", type="string", nullable=true)
     */
    private $unidadeOrg;

    /**
     * @var string $empreendimento
     *
     * @ORM\Column(name="empreendimento", type="string", nullable=true)
     */
    private $empreendimento;

    /**
     * @var string $taxon
     *
     * @ORM\Column(name="taxon", type="string", nullable=true)
     */
    private $taxon;

    /**
     * @var string $caverna
     *
     * @ORM\Column(name="caverna", type="string", nullable=true)
     */
    private $caverna;

    /**
     * @var string $nuCpfCnpjPassaporteOrigem
     *
     * @ORM\Column(name="nu_cpf_cnpj_passaporte_origem", type="string", nullable=true)
     */
    private $nuCpfCnpjPassaporteOrigem;

    /**
     * @var string $dtPrazo
     *
     * @ORM\Column(name="dt_prazo", type="zenddate", nullable=true)
     */
    private $dtPrazo;

    /**
     * @var integer $sqAssunto
     *
     * @ORM\Column(name="sq_assunto", type="integer", nullable=true)
     */
    private $sqAssunto;

    /**
     * @var string $txAssunto
     *
     * @ORM\Column(name="tx_assunto", type="string", nullable=true)
     */
    private $txAssunto;

    /**
     * @var string $txComentario
     *
     * @ORM\Column(name="tx_comentario", type="string", nullable=true)
     */
    private $txComentario;

    /**
     * @var string $txReferencia
     *
     * @ORM\Column(name="tx_referencia", type="string", nullable=true)
     */
    private $txReferencia;

    /**
     * @var string $noPrioridade
     *
     * @ORM\Column(name="no_prioridade", type="string", nullable=true)
     */
    private $noPrioridade;

    /**
     * @var string $txDespacho
     *
     * @ORM\Column(name="tx_despacho", type="string", nullable=true)
     */
    private $txDespacho;

    /**
     * @var string $noTitulo
     *
     * @ORM\Column(name="no_titulo", type="string", nullable=true)
     */
    private $noTitulo;

    /**
     * @var integer $sqPessoaEncaminhado
     *
     * @ORM\Column(name="sq_pessoa_encaminhado", type="integer", nullable=true)
     */
    private $sqPessoaEncaminhado;

    /**
     * @var string $movimentacao
     *
     * @ORM\Column(name="movimentacao", type="string", nullable=true)
     */
    private $movimentacao;

    /**
     * @var boolean $inAbreProcesso
     * @ORM\Column(name="in_abre_processo", type="boolean", nullable=true)
     */
    private $inAbreProcesso;

    /**
     * @var boolean $inAutuacao
     * @ORM\Column(name="in_autuacao", type="boolean", nullable=true)
     */
    private $inAutuacao;

    /**
     * @var boolean $inAutuacaoFilho
     * @ORM\Column(name="in_autuacao_filho", type="boolean", nullable=true)
     */
    private $inAutuacaoFilho;

    /**
     * @var boolean $inOriundoMinuta
     * @ORM\Column(name="in_oriundo_minuta", type="boolean", nullable=true)
     */
    private $inOriundoMinuta;

    public function getSqArtefato()
    {
        return $this->sqArtefato;
    }

    public function getNuDigital()
    {
        return $this->nuDigital;
    }

    public function getNuArtefato()
    {
        return $this->nuArtefato;
    }

    public function getDtArtefato()
    {
        return $this->dtArtefato;
    }

    public function getTxAssuntoComplementar()
    {
        return $this->txAssuntoComplementar;
    }

    public function getSqTipoDocumento()
    {
        return $this->sqTipoDocumento;
    }

    public function getNoTipoDocumento()
    {
        return $this->noTipoDocumento;
    }

    public function getNoTipoArtefato()
    {
        return $this->noTipoArtefato;
    }

    public function getSqTipoArtefato()
    {
        return $this->sqTipoArtefato;
    }

    public function getSqPessoaSgdoceOrigem()
    {
        return $this->sqPessoaSgdoceOrigem;
    }

    public function getNoPessoaOrigem()
    {
        return $this->noPessoaOrigem;
    }

    public function getSqPessoaInteressada()
    {
        return $this->sqPessoaInteressada;
    }

    public function getNoPessoaInteressada()
    {
        return $this->noPessoaInteressada;
    }

    public function getSqPessoaDestino()
    {
        return $this->sqPessoaDestino;
    }

    public function getSqPessoaRecebido()
    {
        return $this->sqPessoaRecebido;
    }

    public function getUnidadeOrg()
    {
        return $this->unidadeOrg;
    }

    public function getEmpreendimento()
    {
        return $this->empreendimento;
    }

    public function getTaxon()
    {
        return $this->taxon;
    }

    public function getCaverna()
    {
        return $this->caverna;
    }

    public function getNuCpfCnpjPassaporteOrigem()
    {
        return $this->nuCpfCnpjPassaporteOrigem;
    }

    public function getDtPrazo()
    {
        return $this->dtPrazo;
    }

    public function getSqAssunto()
    {
        return $this->sqAssunto;
    }

    public function getTxAssunto()
    {
        return $this->txAssunto;
    }

    public function getTxComentario()
    {
        return $this->txComentario;
    }

    public function getTxReferencia()
    {
        return $this->txReferencia;
    }

    public function getNoPrioridade()
    {
        return $this->noPrioridade;
    }

    public function getTxDespacho()
    {
        return $this->txDespacho;
    }

    public function getNoTitulo()
    {
        return $this->noTitulo;
    }

    public function getSqPessoaEncaminhado()
    {
        return $this->sqPessoaEncaminhado;
    }

    public function getMovimentacao()
    {
        return $this->movimentacao;
    }

    public function getInAbreProcesso()
    {
        return $this->inAbreProcesso;
    }

    public function setSqArtefato($sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
    }

    public function setNuDigital($nuDigital)
    {
        $this->nuDigital = $nuDigital;
    }

    public function setNuArtefato($nuArtefato)
    {
        $this->nuArtefato = $nuArtefato;
    }

    public function setDtArtefato($dtArtefato)
    {
        $this->dtArtefato = $dtArtefato;
    }

    public function setTxAssuntoComplementar($txAssuntoComplementar)
    {
        $this->txAssuntoComplementar = $txAssuntoComplementar;
    }

    public function setSqTipoDocumento(Sgdoce\Model\Entity\TipoDocumento $sqTipoDocumento)
    {
        $this->sqTipoDocumento = $sqTipoDocumento;
    }

    public function setNoTipoDocumento($noTipoDocumento)
    {
        $this->noTipoDocumento = $noTipoDocumento;
    }

    public function setNoTipoArtefato($noTipoArtefato)
    {
        $this->noTipoArtefato = $noTipoArtefato;
    }

    public function setSqTipoArtefato($sqTipoArtefato)
    {
        $this->sqTipoArtefato = $sqTipoArtefato;
    }

    public function setSqPessoaSgdoceOrigem($sqPessoaSgdoceOrigem)
    {
        $this->sqPessoaSgdoceOrigem = $sqPessoaSgdoceOrigem;
    }

    public function setNoPessoaOrigem($noPessoaOrigem)
    {
        $this->noPessoaOrigem = $noPessoaOrigem;
    }

    public function setSqPessoaInteressada($sqPessoaInteressada)
    {
        $this->sqPessoaInteressada = $sqPessoaInteressada;
    }

    public function setNoPessoaInteressada($noPessoaInteressada)
    {
        $this->noPessoaInteressada = $noPessoaInteressada;
    }

    public function setSqPessoaDestino($sqPessoaDestino)
    {
        $this->sqPessoaDestino = $sqPessoaDestino;
    }

    public function setSqPessoaRecebido($sqPessoaRecebido)
    {
        $this->sqPessoaRecebido = $sqPessoaRecebido;
    }

    public function setUnidadeOrg($unidadeOrg)
    {
        $this->unidadeOrg = $unidadeOrg;
    }

    public function setEmpreendimento($empreendimento)
    {
        $this->empreendimento = $empreendimento;
    }

    public function setTaxon($taxon)
    {
        $this->taxon = $taxon;
    }

    public function setCaverna($caverna)
    {
        $this->caverna = $caverna;
    }

    public function setNuCpfCnpjPassaporteOrigem($nuCpfCnpjPassaporteOrigem)
    {
        $this->nuCpfCnpjPassaporteOrigem = $nuCpfCnpjPassaporteOrigem;
    }

    public function setDtPrazo($dtPrazo)
    {
        $this->dtPrazo = $dtPrazo;
    }

    public function setSqAssunto($sqAssunto)
    {
        $this->sqAssunto = $sqAssunto;
    }

    public function setTxAssunto($txAssunto)
    {
        $this->txAssunto = $txAssunto;
    }

    public function setTxComentario($txComentario)
    {
        $this->txComentario = $txComentario;
    }

    public function setTxReferencia($txReferencia)
    {
        $this->txReferencia = $txReferencia;
    }

    public function setNoPrioridade($noPrioridade)
    {
        $this->noPrioridade = $noPrioridade;
    }

    public function setTxDespacho($txDespacho)
    {
        $this->txDespacho = $txDespacho;
    }

    public function setNoTitulo($noTitulo)
    {
        $this->noTitulo = $noTitulo;
    }

    public function setSqPessoaEncaminhado($sqPessoaEncaminhado)
    {
        $this->sqPessoaEncaminhado = $sqPessoaEncaminhado;
    }

    public function setMovimentacao($movimentacao)
    {
        $this->movimentacao = $movimentacao;
    }

    public function setInAbreProcesso($inAbreProcesso)
    {
        $this->inAbreProcesso = $inAbreProcesso;
    }

    public function getInAutucao($inAutucao)
    {
    	$this->inAutucao = $inAutucao;
    }

    public function setInAutucao($inAutucao)
    {
    	$this->inAutucao = $inAutucao;
    }

    public function getInAutucaoFilho($inAutucaoFilho)
    {
    	$this->inAutucaoFilho = $inAutucaoFilho;
    }

    public function setInAutucaoFilho($inAutucaoFilho)
    {
    	$this->inAutucaoFilho = $inAutucaoFilho;
    }

    public function getInOriundoMinuta()
    {
        return $this->inOriundoMinuta;
    }

    public function setInOriundoMinuta($inOriundoMinuta)
    {
        $this->inOriundoMinuta = $inOriundoMinuta;
    }

}