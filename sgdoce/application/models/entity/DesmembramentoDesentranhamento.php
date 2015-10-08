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
 * Sgdoce\Model\Entity\DesmembramentoDesentranhamento
 *
 * @ORM\Table(name="desmembramento_desentranhamento")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\DesmembramentoDesentranhamento")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class DesmembramentoDesentranhamento extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqDesmembramentoDesentra
     *
     * @ORM\Column(name="sq_desmembramento_desentra", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqDesmembramentoDesentra;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\ArtefatoProcesso")
     * @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato", nullable=false)
     */
    private $sqArtefato;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\ArtefatoProcesso")
     * @ORM\JoinColumn(name="sq_artefato_destino", referencedColumnName="sq_artefato", nullable=true)
     */
    private $sqArtefatoDestino;

    /**
     * @var integer $sqUnidadeSolicitacao
     * 
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumn(name="sq_unidade_solicitacao", referencedColumnName="sq_pessoa", nullable=false) 
     */
    private $sqUnidadeSolicitacao;

    /**
     * @var string $txNumeroPecas
     * 
     * @ORM\Column(name="tx_numero_pecas", type="string", length=20, nullable=false)
     */
    private $txNumeroPecas;    

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="\Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa", nullable=false)
     */
    private $sqPessoa;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="\Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumn(name="sq_unidade_org", referencedColumnName="sq_pessoa")
     */
    private $sqUnidadeOrg;    

    /**
     * @var integer
     *  
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")     
     * @ORM\JoinColumn(name="sq_pessoa_assinatura", referencedColumnName="sq_pessoa")
     */
    private $sqPessoaAssinatura;    

    /**
     * @var integer
     *      
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwCargo")
     * @ORM\JoinColumn(name="sq_cargo", referencedColumnName="sq_cargo")
     */
    private $sqCargo;    

    /**
     * @var zenddate $dtOperacao
     * 
     * @ORM\Column(name="dt_operacao", type="zenddate", nullable=false)
     */
    private $dtOperacao;

    /**
     * @var boolean $stDesmembramento
     *
     * @ORM\Column(name="st_desmembramento", type="boolean", nullable=false)
     */
    private $stDesmembramento;    

    /**
     * @var string $txJustificativaDesentranhamento
     * 
     * @ORM\Column(name="tx_justificativa_desentranhamento", type="string", length=255, nullable=true)
     */
    private $txJustificativaDesentranhamento;    

    /**
     * Set sqDesmembramentoDesentra
     *
     * @param interger $sqDesmembramentoDesentra
     * @return void
     */
    public function setSqDesmembramentoDesentra( $sqDesmembramentoDesentra )
    {
    	$this->sqDesmembramentoDesentra = $sqDesmembramentoDesentra;
    }    

    /**
     * Get sqDesmembramentoDesentra
     *
     * @return interger
     */
    public function getSqDesmembramentoDesentra()
    {
    	return $this->sqDesmembramentoDesentra;
    }    

    /**
     * Set sqArtefato
     *
     * @param interger $sqArtefato
     * @return void
     */
    public function setSqArtefato( $sqArtefato )
    {
    	$this->sqArtefato = $sqArtefato;
    }    
    
    /**
     * Get sqArtefato
     *
     * @return interger
     */
    public function getSqArtefato()
    {
    	return $this->sqArtefato;
    }   
     
    /**
     * Set sqArtefatoDestino
     *
     * @param interger $sqArtefatoDestino
     * @return void
     */
    public function setSqArtefatoDestino( $sqArtefatoDestino )
    {
    	$this->sqArtefatoDestino = $sqArtefatoDestino;
    }
    
    
    /**
     * Get sqArtefatoDestino
     *
     * @return interger
     */
    public function getSqArtefatoDestino()
    {
    	return $this->sqArtefatoDestino;
    }
    
    /**
     * Set sqUnidadeSolicitacao
     *
     * @param interger $sqUnidadeSolicitacao
     * @return void
     */
    public function setSqUnidadeSolicitacao( $sqUnidadeSolicitacao )
    {
    	$this->sqUnidadeSolicitacao = $sqUnidadeSolicitacao;
    }
    
    
    /**
     * Get sqUnidadeSolicitacao
     *
     * @return interger
     */
    public function getSqUnidadeSolicitacao()
    {
    	return $this->sqUnidadeSolicitacao;
    }
    
    /**
     * Set txNumeroPecas
     *
     * @param string $txNumeroPecas
     * @return void
     */
    public function setTxNumeroPecas( $txNumeroPecas )
    {
    	$this->txNumeroPecas = $txNumeroPecas;
    }
    
    
    /**
     * Get txNumeroPecas
     *
     * @return string
     */
    public function getTxNumeroPecas()
    {
    	return $this->txNumeroPecas;
    }
    
    /**
     * Set sqPessoa
     *
     * @param interger $sqPessoa
     * @return void
     */
    public function setSqPessoa( $sqPessoa )
    {
    	$this->sqPessoa = $sqPessoa;
    }
    
    
    /**
     * Get sqPessoa
     *
     * @return interger
     */
    public function getSqPessoa()
    {
    	return $this->sqPessoa;
    }
    
    /**
     * Set sqUnidadeOrg
     *
     * @param interger $sqUnidadeOrg
     * @return void
     */
    public function setSqUnidadeOrg( $sqUnidadeOrg )
    {
    	$this->sqUnidadeOrg = $sqUnidadeOrg;
    }
    
    
    /**
     * Get sqUnidadeOrg
     *
     * @return interger
     */
    public function getSqUnidadeOrg()
    {
    	return $this->sqUnidadeOrg;
    }
    
    /**
     * Set sqPessoaAssinatura
     *
     * @param interger $sqPessoaAssinatura
     * @return void
     */
    public function setSqPessoaAssinatura( $sqPessoaAssinatura )
    {
    	$this->sqPessoaAssinatura = $sqPessoaAssinatura;
    }
    
    
    /**
     * Get sqPessoaAssinatura
     *
     * @return interger
     */
    public function getSqPessoaAssinatura()
    {
    	return $this->sqPessoaAssinatura;
    }
    
    /**
     * Set sqCargo
     *
     * @param interger $sqCargo
     * @return void
     */
    public function setSqCargo( $sqCargo )
    {
    	$this->sqCargo = $sqCargo;
    }
    
    
    /**
     * Get sqCargo
     *
     * @return interger
     */
    public function getSqCargo()
    {
    	return $this->sqCargo;
    }
    
    /**
     * Set dtOperacao
     *
     * @param zenddate $dtOperacao
     * @return void
     */
    public function setDtOperacao( $dtOperacao )
    {
    	$this->dtOperacao = $dtOperacao;
    }
    
    
    /**
     * Get dtOperacao
     *
     * @return zenddate
     */
    public function getDtOperacao()
    {
    	return $this->dtOperacao;
    }
    
    /**
     * Set stDesmembramento
     *
     * @param boolean $stDesmembramento
     * @return void
     */
    public function setStDesmembramento( $stDesmembramento )
    {
    	$this->stDesmembramento = $stDesmembramento;
    }
    
    
    /**
     * Get stDesmembramento
     *
     * @return boolean
     */
    public function getStDesmembramento()
    {
    	return $this->stDesmembramento;
    }
    
    /**
     * Set txJustificativaDesentranhamento
     *
     * @param string $txJustificativaDesentranhamento
     * @return void
     */
    public function setTxJustificativaDesentranhamento( $txJustificativaDesentranhamento )
    {
    	$this->txJustificativaDesentranhamento = $txJustificativaDesentranhamento;
    }
    
    
    /**
     * Get txJustificativaDesentranhamento
     *
     * @return string
     */
    public function getTxJustificativaDesentranhamento()
    {
    	return $this->txJustificativaDesentranhamento;
    }
}