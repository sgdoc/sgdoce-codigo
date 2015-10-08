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
 * Sgdoce\Model\Entity\DespachoInterlocutorio
 *
 * @ORM\Table(name="despacho_interlocutorio")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\DespachoInterlocutorio")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class DespachoInterlocutorio extends \Core_Model_Entity_Abstract {

    /**
     * limite de char por comentario
     *
     *  @var integer
     * */
    const T_TX_DESPACHO_LIMIT = 1000;

    /**
     * @var integer $sqDespachoInterlocutorio
     * @ORM\Id
     * @ORM\Column(name="sq_despacho_interlocutorio", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqDespachoInterlocutorio;

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     * })
     */
    private $sqArtefato;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_assinatura", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeAssinatura;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_assinatura", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaAssinatura;

    /**
     * @var Sgdoce\Model\Entity\VwCargo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwCargo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_cargo_assinatura", referencedColumnName="sq_cargo")
     * })
     */
    private $sqCargoAssinatura;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_destino", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeDestino;

    /**
     * @var text $txDespacho
     *
     * @ORM\Column(name="tx_despacho", type="text", length=1000, nullable=false)
     */
    private $txDespacho;

    /**
     * @var zenddate $dtDespacho
     *
     * @ORM\Column(name="dt_despacho", type="zenddate", nullable=false)
     */
    private $dtDespacho;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_operacao", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaOperacao;


    /**
     * @var Sgdoce\Model\Entity\VwFuncao
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwFuncao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_funcao_assinatura", referencedColumnName="sq_funcao")
     * })
     */
    private $sqFuncaoAssinatura;
    
    /**
     * Set sqDespachoInterlocutorio
     *
     * @param integer $sqDespachoInterlocutorio
     * @return DespachoInterlocutorio
     */
    public function setSqDespachoInterlocutorio($sqDespachoInterlocutorio)
    {
        $this->sqDespachoInterlocutorio = $sqDespachoInterlocutorio;
        return $this;
    }

    /**
     * Get sqDespachoInterlocutorio
     *
     * @return integer
     */
    public function getSqDespachoInterlocutorio()
    {
        return $this->sqDespachoInterlocutorio;
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return DespachoInterlocutorio
     */
    public function setSqArtefato(\Sgdoce\Model\Entity\Artefato $sqArtefato = NULL)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    /**
     * Get sqArtefato
     *
     * @return Sgdoce\Model\Entity\Artefato
     */
    public function getSqArtefato()
    {
        return $this->sqArtefato;
    }

    /**
     * Set sqUnidadeAssinatura
     *
     * @param integer $sqUnidadeAssinatura
     * @return DespachoInterlocutorio
     */
    public function setSqUnidadeAssinatura($sqUnidadeAssinatura)
    {
        $this->sqUnidadeAssinatura = $sqUnidadeAssinatura;
        return $this;
    }

    /**
     * Get sqUnidadeAssinatura
     *
     * @return integer
     */
    public function getSqUnidadeAssinatura()
    {
        return $this->sqUnidadeAssinatura;
    }

    /**
     * Set sqPessoaAssinatura
     *
     * @param integer $sqPessoaAssinatura
     * @return DespachoInterlocutorio
     */
    public function setSqPessoaAssinatura($sqPessoaAssinatura)
    {
        $this->sqPessoaAssinatura = $sqPessoaAssinatura;
        return $this;
    }

    /**
     * Get sqPessoaAssinatura
     *
     * @return integer
     */
    public function getSqPessoaAssinatura()
    {
        return $this->sqPessoaAssinatura;
    }

    /**
     * Set sqCargoAssinatura
     *
     * @param integer $sqCargoAssinatura
     * @return DespachoInterlocutorio
     */
    public function setSqCargoAssinatura($sqCargoAssinatura)
    {
        $this->sqCargoAssinatura = $sqCargoAssinatura;
        return $this;
    }

    /**
     * Get sqCargoAssinatura
     *
     * @return integer
     */
    public function getSqCargoAssinatura()
    {
        return $this->sqCargoAssinatura;
    }

    /**
     * Set sqUnidadeDestino
     *
     * @param integer $sqUnidadeDestino
     * @return DespachoInterlocutorio
     */
    public function setSqUnidadeDestino($sqUnidadeDestino)
    {
        $this->sqUnidadeDestino = $sqUnidadeDestino;
        return $this;
    }

    /**
     * Get sqUnidadeDestino
     *
     * @return integer
     */
    public function getSqUnidadeDestino()
    {
        return $this->sqUnidadeDestino;
    }

    /**
     * Set txDespacho
     *
     * @param text $txDespacho
     * @return DespachoInterlocutorio
     */
    public function setTxDespacho($txDespacho)
    {
        $this->txDespacho  = mb_substr(str_replace(chr(13), '', $txDespacho ), 0, self::T_TX_DESPACHO_LIMIT, 'UTF-8');
        return $this;
    }

    /**
     * Get txDespacho
     *
     * @return text
     */
    public function getTxDespacho()
    {
        return $this->txDespacho;
    }

    /**
     * Set dtDespacho
     *
     * @param zenddate $dtDespacho
     * @return DespachoInterlocutorio
     */
    public function setDtDespacho($dtDespacho)
    {
        $this->dtDespacho = $dtDespacho;
        return $this;
    }

    /**
     * Get dtDespacho
     *
     * @return zenddate
     */
    public function getDtDespacho()
    {
        return $this->dtDespacho;
    }

    /**
     * Get sqPessoaOperacao
     *
     * @return zenddate
     */
    public function getSqPessoaOperacao()
    {
        return $this->sqPessoaOperacao;
    }

    /**
     * Set sqPessoaOperacao
     *
     * @param zenddate $sqPessoaOperacao
     * @return DespachoInterlocutorio
     */
    public function setSqPessoaOperacao($sqPessoaOperacao)
    {
        $this->sqPessoaOperacao = $sqPessoaOperacao;
        return $this;
    }

    function getSqFuncaoAssinatura() {
        return $this->sqFuncaoAssinatura;
    }

    function setSqFuncaoAssinatura($sqFuncaoAssinatura) {
        $this->sqFuncaoAssinatura = $sqFuncaoAssinatura;
        return $this;
    }


}