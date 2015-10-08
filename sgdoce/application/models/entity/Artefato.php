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
 * Sgdoce\Model\Entity\Artefato
 *
 * @ORM\Table(name="artefato")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Artefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Artefato extends \Core_Model_Entity_Abstract
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
     * @var string $nuArtefato
     * @ORM\Column(name="nu_artefato", type="string", length=120, nullable=true)
     */
    private $nuArtefato;

    /**
     * @var bigint $nuDigital
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\EtiquetaNupSiorg")
     * @ORM\JoinColumn(name="nu_digital", referencedColumnName="nu_etiqueta", nullable=true)
     */
    private $nuDigital;

    /**
     * @var integer $sqLoteEtiqueta
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\EtiquetaNupSiorg")
     * @ORM\JoinColumn(name="sq_lote_etiqueta", referencedColumnName="sq_lote_etiqueta", nullable=true)
     */
    private $sqLoteEtiqueta;

    /**
     * @var zenddate $dtArtefato
     * @ORM\Column(name="dt_artefato", type="zenddate", nullable=false)
     */
    private $dtArtefato;

    /**
     * @var \Zend_Date $dtEntrada
     * @ORM\Column(name="dt_entrada", type="zenddate", nullable=true)
     */
    private $dtEntrada;

    /**
     * @var \Zend_Date $dtCadastro
     * @ORM\Column(name="dt_cadastro", type="zenddate", nullable=false)
     */
    private $dtCadastro;

    /**
     * @var integer $sqPessoaRecebimento
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumn(name="sq_pessoa_recebimento", referencedColumnName="sq_pessoa", nullable=true)
     */
    private $sqPessoaRecebimento;

    /**
     * @var Sgdoce\Model\Entity\TipoArtefatoAssunto
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoArtefatoAssunto", cascade={"persist"} )
     * @ORM\JoinColumn(name="sq_tipo_artefato_assunto", referencedColumnName="sq_tipo_artefato_assunto")
     */
    private $sqTipoArtefatoAssunto;

    /**
     * @var string $txAssuntoComplementar
     * @ORM\Column(name="tx_assunto_complementar", type="string", nullable=true)
     */
    private $txAssuntoComplementar;

    /**
     * @var zenddate $dtPrazo
     * @ORM\Column(name="dt_prazo", type="zenddate", nullable=true)
     */
    private $dtPrazo;

    /**
     * @var integer $nuDiasPrazo
     * @ORM\Column(name="nu_dias_prazo", type="integer", nullable=true)
     */
    private $nuDiasPrazo;

    /**
     * @var boolean $inDiasCorridos
     * @ORM\Column(name="in_dias_corridos", type="boolean", nullable=true)
     */
    private $inDiasCorridos;

    /**
     * @var Sgdoce\Model\Entity\TipoPrioridade
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoPrioridade")
     * @ORM\JoinColumn(name="sq_tipo_prioridade", referencedColumnName="sq_tipo_prioridade", nullable=true)
     */
    private $sqTipoPrioridade;

    /**
     * @var Sgdoce\Model\Entity\TipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoDocumento")
     * @ORM\JoinColumn(name="sq_tipo_documento", referencedColumnName="sq_tipo_documento",nullable=true)
     */
    private $sqTipoDocumento;

    /**
     * @var string $deImagemRodape
     * @ORM\Column(name="de_imagem_rodape", type="string", length=200, nullable=true)
     */
    private $deImagemRodape;

    /**
     * @var string $txDescricaoPrazo
     * @ORM\Column(name="tx_descricao_prazo", type="string", length=300, nullable=true)
     */
    private $txDescricaoPrazo;

    /**
     * @var Sgdoce\Model\Entity\Fecho
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Fecho")
     * @ORM\JoinColumn(name="sq_fecho", referencedColumnName="sq_fecho")
     */
    private $sqFecho;

    /**
     * @var bigint $inAssinaturaDigital
     * @ORM\Column(name="in_assinatura_digital", type="bigint", nullable=true)
     */
    private $inAssinaturaDigital;

    /**
     * @var string $noCargoInterno
     * @ORM\Column(name="no_cargo_interno", type="string", length=100, nullable=true)
     */
    private $noCargoInterno;

    /**
     * @var Sgdoce\Model\Entity\AnexoArtefato
     *
     * @ORM\OneToOne(targetEntity="\Sgdoce\Model\Entity\AnexoArtefato", mappedBy="sqArtefato")
     */
    private $sqAnexoArtefato;

    /**
     * @var Sgdoce\Model\Entity\ArtefatoMinuta
     *
     * @ORM\OneToOne(targetEntity="\Sgdoce\Model\Entity\ArtefatoMinuta", mappedBy="sqArtefato",cascade={"remove"})
     */
    private $sqArtefatoMinuta;

    /**
     * @var Sgdoce\Model\Entity\ArtefatoVinculo
     *
     * @ORM\OneToOne(targetEntity="\Sgdoce\Model\Entity\ArtefatoVinculo", mappedBy="sqArtefatoPai",cascade={"remove"})
     */
    private $sqArtefatoPai;

    /**
     * @var Sgdoce\Model\Entity\ArtefatoVinculo
     *
     * @ORM\OneToOne(targetEntity="\Sgdoce\Model\Entity\ArtefatoVinculo", mappedBy="sqArtefatoFilho", cascade={"remove"})
     */
    private $sqArtefatoFilho;

    /**
     * @var Sgdoce\Model\Entity\ArtefatoDossie
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\ArtefatoDossie", mappedBy="sqArtefato", cascade={"remove"})
     */
    private $sqArtefatoDossie;

    /**
     * @var Sgdoce\Model\Entity\ArtefatoProcesso
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\ArtefatoProcesso", mappedBy="sqArtefato", cascade={"remove"})
     */
    private $sqArtefatoProcesso;

    /**
     * @var Sgdoce\Model\Entity\PessoaAssinanteArtefato
     *
     * @ORM\OneToMany(targetEntity="Sgdoce\Model\Entity\PessoaAssinanteArtefato", mappedBy="sqArtefato")
     */
    private $sqPessoaAssinanteArtefato;

    /**
     * @var Sgdoce\Model\Entity\PessoaInteressadaArtefato
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\PessoaInteressadaArtefato", mappedBy="sqArtefato",cascade={"remove"})
     */
    private $sqPessoaInteressadaArtefato;

    /**
     * @var Sgdoce\Model\Entity\PessoaArtefato
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\PessoaArtefato", mappedBy="sqArtefato", cascade={"remove"})
     */
    private $sqPessoaArtefato;

    /**
     * @var Sgdoce\Model\Entity\GrauAcessoArtefato
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\GrauAcessoArtefato", mappedBy="sqArtefato",cascade={"remove"})
     */
    private $sqGrauAcessoArtefato;

     /**
     * @var Sgdoce\Model\Entity\HistoricoArtefato
     *
     * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\HistoricoArtefato", mappedBy="sqArtefato",cascade={"remove"})
     */
    private $sqHistoricoArtefato;

     /**
     * @var Sgdoce\Model\Entity\ComentarioArtefato
     *
     * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\ComentarioArtefato", mappedBy="sqArtefato",cascade={"remove"})
     */
    private $sqComentarioArtefato;

    /**
     * @var Sgdoce\Model\Entity\PessoaArtefato
     *
     * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\PessoaArtefato", mappedBy="sqArtefato", cascade={"remove"})
     */
    private $sqPessoa;

//
//    /**
//     * @var Sgdoce\Model\Entity\ProcessoCaverna
//     *
//     * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\ProcessoCaverna", mappedBy="sqArtefato", cascade={"remove"})
//     */
//    private $sqProcessoCaverna;
//
//        /**
//     * @var Sgdoce\Model\Entity\ProcessoTaxon
//     *
//     * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\ProcessoTaxon", mappedBy="sqArtefato", cascade={"remove"})
//     */
//    private $sqProcessoTaxon;
//
//    /**
//     * @var Sgdoce\Model\Entity\ProcessoEmpreendimento
//     *
//     * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\ProcessoEmpreendimento", mappedBy="sqArtefato", cascade={"remove"})
//     */
//    private $sqProcessoEmpreendimento;

    /**
     * @var Sgdoce\Model\Entity\ProcessoUnidadeOrg
     *
     * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\ProcessoUnidadeOrg", mappedBy="sqArtefato", cascade={"remove"})
     */
    private $sqProcessoUnidadeOrg;

    /**
     * @var boolean $inEletronico
     * @ORM\Column(name="in_eletronico", type="boolean", nullable=true)
     */
    private $inEletronico;

    /**
     * @var Sgdoce\Model\Entity\ArtefatoClassificacao
     *
     * @ORM\OneToOne(targetEntity="\Sgdoce\Model\Entity\ArtefatoClassificacao", mappedBy="sqArtefato")
     *
     */
    private $sqArtefatoClassificacao;

    /**
    * @var Sgdoce\Model\Entity\VwUltimaImagemArtefato
    *
    * @ORM\OneToOne(targetEntity="\Sgdoce\Model\Entity\VwUltimaImagemArtefato", mappedBy="sqArtefato")
    */
    private $sqArtefatoImagem;

    /**
     * @var Sgdoce\Model\Entity\CaixaArtefato
     *
     * @ORM\OneToOne(targetEntity="\Sgdoce\Model\Entity\CaixaArtefato", mappedBy="sqArtefato")
     *
     */
    private $sqCaixaArtefato;

    /**
     * @var boolean $stMigracao
     * @ORM\Column(name="st_migracao", type="boolean", nullable=true)
     */
    private $stMigracao;

    /**
     * @var boolean $arInconsistencia
     * @ORM\Column(name="ar_inconsistencia", type="string", nullable=true)
     */
    private $arInconsistencia;

    /**
     * Getter.
     *
     * @return type
     */
    public function getSqArtefato()
    {
        return $this->sqArtefato;
    }

    public function getNuArtefato()
    {
        return $this->nuArtefato;
    }

    public function getInEletronico()
    {
        return $this->inEletronico;
    }

    /**
     * Get sqArtefatoClassificacao
     *
     * @return Sgdoce\Model\Entity\ArtefatoClassificacao
     */
    public function getSqArtefatoClassificacao()
    {
        return $this->sqArtefatoClassificacao;
    }

    /**
    * Get sqArtefatoImagem
    *
    * @return Sgdoce\Model\Entity\VwUltimaImageArtefato
    */
    public function getSqArtefatoImagem()
    {
        return $this->sqArtefatoImagem;
    }

    /**
     * Get sqArtefatoClassificacao
     *
     * @return Sgdoce\Model\Entity\CaixaArtefato
     */
    public function getSqCaixaArtefato()
    {
        return $this->sqCaixaArtefato;
    }

    public function getDtCadastro ()
    {
        return $this->dtCadastro;
    }

    /**
     * @return EtiquetaNupSiorg
     */
    public function getNuDigital()
    {
        return $this->nuDigital;
    }

    public function getSqLoteEtiqueta()
    {
        return $this->sqLoteEtiqueta;
    }

    public function getDtEntrada()
    {
        return $this->dtEntrada;
    }

    public function getSqPessoaRecebimento()
    {
        return $this->sqPessoaRecebimento;
    }

    public function getDtArtefato()
    {
        return $this->dtArtefato;
    }

    public function getTxAssuntoComplementar()
    {
        return $this->txAssuntoComplementar;
    }

    public function getDtPrazo()
    {
        return $this->dtPrazo;
    }

    public function getNuDiasPrazo()
    {
        return $this->nuDiasPrazo;
    }

    public function getInDiasCorridos()
    {
        return $this->inDiasCorridos;
    }

    public function getDeImagemRodape()
    {
        return $this->deImagemRodape;
    }

    public function getTxDescricaoPrazo()
    {
        return $this->txDescricaoPrazo;
    }

    public function getInAssinaturaDigital()
    {
        return $this->inAssinaturaDigital;
    }

    public function getSqFecho()
    {
        return $this->sqFecho;
    }

    public function getSqTipoPrioridade()
    {
        return $this->sqTipoPrioridade;
    }

    /**
     * @return TipoArtefatoAssunto
     */
    public function getSqTipoArtefatoAssunto()
    {
        return $this->sqTipoArtefatoAssunto ? $this->sqTipoArtefatoAssunto : new TipoArtefatoAssunto();
    }

    public function getSqTipoDocumento()
    {
        return $this->sqTipoDocumento;
    }

    public function getNoCargoInterno()
    {
        return $this->noCargoInterno;
    }

    public function getSqComentarioArtefato()
    {
        return $this->sqComentarioArtefato;
    }


    public function getSqHistoricoArtefato()
    {
        return $this->sqHistoricoArtefato;
    }

    public function getSqArtefatoPai()
    {
        return $this->sqArtefatoPai;
    }

    public function getSqArtefatoFilho()
    {
        return $this->sqArtefatoFilho;
    }

    public function getSqArtefatoDossie()
    {
        return $this->sqArtefatoDossie;
    }

    public function getSqPessoaArtefato()
    {
        return $this->sqPessoaArtefato;
    }

    public function getSqGrauAcessoArtefato()
    {
        return $this->sqGrauAcessoArtefato;
    }

    /**
     *
     * @return \Sgdoce\Model\Entity\ArtefatoProcesso
     */
    public function getSqArtefatoProcesso()
    {
        return $this->sqArtefatoProcesso;
    }


    public function getSqPessoaAssinanteArtefato()
    {
        return $this->sqPessoaAssinanteArtefato;
    }

    public function setSqPessoaAssinanteArtefato($sqPessoaAssinanteArtefato)
    {
        $this->sqPessoaAssinanteArtefato = $sqPessoaAssinanteArtefato;
    }

    public function getSqPessoaInteressadaArtefato()
    {
        return $this->sqPessoaInteressadaArtefato;
    }

    public function getSqArtefatoMinuta()
    {
        return $this->sqArtefatoMinuta;
    }

    public function getStMigracao()
    {
        return $this->stMigracao;
    }

    public function getArInconsistencia()
    {
        return $this->arInconsistencia;
    }

    /**
     * Set sqArtefato
     *
     * @param integer $sqArtefato
     * @return integer
     */
    public function setSqArtefato($sqArtefato = NULL)
    {
        $this->sqArtefato = $sqArtefato;
        if (!$sqArtefato) {
            $this->sqArtefato = NULL;
        }
        return $this;
    }

    public function setNuArtefato($nuArtefato)
    {
        $this->assert('nuArtefato',$nuArtefato,$this);
        $this->nuArtefato = $nuArtefato;
    }

    public function setNuDigital($nuDigital)
    {
        $this->nuDigital = $nuDigital;
    }

    public function setNuEtiqueta($nuEtiqueta)
    {
        $this->setNuDigital($nuEtiqueta);
        return $this;
    }

    public function setSqLoteEtiqueta($sqLoteEtiqueta)
    {
        $this->sqLoteEtiqueta = $sqLoteEtiqueta;
        return $this;
    }

    public function setDtEntrada($dtEntrada)
    {
        $this->dtEntrada = $dtEntrada;
        return $this;
    }

    public function setDtArtefato($dtArtefato)
    {
        $this->dtArtefato = $dtArtefato;
    }

    public function setTxAssuntoComplementar($txAssuntoComplementar)
    {
        $this->assert('txAssuntoComplementar',$txAssuntoComplementar,$this);
        $this->txAssuntoComplementar = $txAssuntoComplementar;
    }

    public function setDtPrazo($dtPrazo)
    {
        $this->dtPrazo = $dtPrazo;
    }

    public function setNuDiasPrazo($nuDiasPrazo)
    {
        $this->nuDiasPrazo = empty($nuDiasPrazo) ? NULL : $nuDiasPrazo;
    }

    public function setInDiasCorridos($inDiasCorridos)
    {
        if (!is_bool($inDiasCorridos)) {
            if( $inDiasCorridos === 'N' ) {
                $inDiasCorridos = FALSE;
            }else if( $inDiasCorridos === 'S' ) {
                $inDiasCorridos = TRUE;
            }else{
                $inDiasCorridos = NULL;
            }
        }
        $this->inDiasCorridos = $inDiasCorridos;
    }

    public function setDeImagemRodape($deImagemRodape)
    {
        $this->deImagemRodape = $deImagemRodape;
    }

    public function setSqPessoaRecebimento($sqPessoaRecebimento)
    {
        $this->sqPessoaRecebimento = $sqPessoaRecebimento;
        return $this;
    }

    public function setTxDescricaoPrazo($txDescricaoPrazo)
    {
        $this->txDescricaoPrazo = $txDescricaoPrazo;
    }

    public function setInAssinaturaDigital($inAssinaturaDigital)
    {
    	if( is_null($inAssinaturaDigital)
    		|| $inAssinaturaDigital == '' ) {
			$inAssinaturaDigital = NULL;
    	} else {
    		$inAssinaturaDigital = (boolean) $inAssinaturaDigital;
    	}

        $this->inAssinaturaDigital = $inAssinaturaDigital;
    }

    public function setSqFecho(Fecho $sqFecho = NULL)
    {
        $this->sqFecho = $sqFecho;
        return $this;
    }

    public function setSqTipoPrioridade($sqTipoPrioridade)
    {
        $this->sqTipoPrioridade = $sqTipoPrioridade;
        return $this;
    }

    public function setSqTipoArtefatoAssunto($sqTipoArtefatoAssunto)
    {
        $this->sqTipoArtefatoAssunto = $sqTipoArtefatoAssunto;
        return $this;
    }

    public function setSqTipoDocumento($sqTipoDocumento)
    {
        $this->sqTipoDocumento = $sqTipoDocumento;

        return $this;
    }

    public function setNoCargoInterno($noCargoInterno)
    {
        $this->noCargoInterno = $noCargoInterno;
    }

    public function setSqComentarioArtefato($sqComentarioArtefato)
    {
        $this->sqComentarioArtefato = $sqComentarioArtefato;
    }

    public function setSqArtefatoPai($sqArtefatoPai)
    {
        $this->sqArtefatoPai = $sqArtefatoPai;
    }

    public function setSqPessoaArtefato($sqPessoaArtefato)
    {
        $this->sqPessoaArtefato = $sqPessoaArtefato;
    }

    public function setSqArtefatoFilho($sqArtefatoFilho)
    {
        $this->sqArtefatoFilho = $sqArtefatoFilho;
    }

    public function setSqGrauAcessoArtefato($sqGrauAcessoArtefato)
    {
        $this->sqGrauAcessoArtefato = $sqGrauAcessoArtefato;
    }

    public function setSqArtefatoProcesso($sqArtefatoProcesso)
    {
        $this->sqArtefatoProcesso = $sqArtefatoProcesso;
        return $this;
    }

    public function setSqPessoaInteressadaArtefato($sqPessoaInteressadaArtefato)
    {
        $this->sqPessoaInteressadaArtefato = $sqPessoaInteressadaArtefato;
    }

    public function setSqHistoricoArtefato($sqHistoricoArtefato)
    {
        $this->sqHistoricoArtefato = $sqHistoricoArtefato;
    }

    public function setSqArtefatoMinuta($sqArtefatoMinuta)
    {
        $this->sqArtefatoMinuta = $sqArtefatoMinuta;
    }

    public function setInEletronico( $inEletronico )
    {
    	$inEletronico = (boolean) $inEletronico;
    	return $this->inEletronico = $inEletronico;
    }

    public function setDtCadastro ($dtCadastro)
    {
        $this->dtCadastro = $dtCadastro;
        return $this;
    }

    /**
     * Set sqArtefatoClassificacao
     *
     * @param Sgdoce\Model\Entity\ArtefatoClassificacao $sqArtefatoClassificacao = NULL
     * @return Sgdoce\Model\Entity\Artefato
     */
    public function setSqArtefatoClassificacao($sqArtefatoClassificacao = NULL)
    {
        $this->sqArtefatoClassificacao = $sqArtefatoClassificacao;
        return $this;
    }

    /**
    * Set sqArtefatoImagem
    *
    * @param Sgdoce\Model\Entity\VwUltimaImageArtefato $sqArtefatoImagem = NULL
    * @return Sgdoce\Model\Entity\Artefato
    */
    public function setSqArtefatoImagem($sqArtefatoImagem = NULL)
    {
        $this->sqArtefatoImagem = $sqArtefatoImagem;
        return $this;
    }

    /**
     * Set sqCaixaArtefato
     *
     * @param Sgdoce\Model\Entity\CaixaArtefato $sqCaixaArtefato = NULL
     * @return Sgdoce\Model\Entity\Artefato
     */
    public function setSqCaixaArtefato($sqCaixaArtefato = NULL)
    {
        $this->sqCaixaArtefato = $sqCaixaArtefato;
        return $this;
    }


    /**
    * Set stMigracao
    *
    * @param boolean $stMigracao = NULL
    * @return Sgdoce\Model\Entity\Artefato
    */
    public function setStMigracao($stMigracao = FALSE)
    {
        $this->stMigracao = $stMigracao;
        return $this;
    }

    /**
     * Set arInconsistencia
     *
     * @param string $arInconsistencia = {t,t,t,t,t}
     * @return Sgdoce\Model\Entity\Artefato
     */
    public function setArInconsistencia($arInconsistencia = NULL)
    {
        $this->arInconsistencia = $arInconsistencia;
        return $this;
    }



    /**
     * ARTEFATO E UM PROCESSO?.
     *
     * @return boolean
     */
    public function isProcesso()
    {
        return (boolean)$this->sqArtefatoProcesso;
    }
}
