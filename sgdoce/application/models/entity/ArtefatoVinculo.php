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
use Core\Model\OWM\Mapping as OWM;

/**
 * Sgdoce\Model\Entity\ArtefatoVinculo
 *
 * @ORM\Table(name="artefato_vinculo")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ArtefatoVinculo")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ArtefatoVinculo extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqArtefatoVinculo
     *
     * @ORM\Id
     * @ORM\Column(name="sq_artefato_vinculo", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqArtefatoVinculo;

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato",inversedBy="sqArtefatoPai")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_artefato_pai", referencedColumnName="sq_artefato")
     * })
     */
    private $sqArtefatoPai;

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ ORM\Column(name="sq_artefato_filho", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato", inversedBy="sqArtefatoFilho")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_artefato_filho", referencedColumnName="sq_artefato")
     * })
     */
    private $sqArtefatoFilho;

    /**
     * @var Sgdoce\Model\Entity\TipoVinculoArtefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoVinculoArtefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_vinculo_artefato", referencedColumnName="sq_tipo_vinculo_artefato")
     * })
     */
    private $sqTipoVinculoArtefato;

    /**
     * @var \Sgdoce\Model\Entity\AnexoArtefatoVinculo
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\AnexoArtefatoVinculo", mappedBy="sqArtefatoVinculo")
     */
    private $sqAnexoArtefatoVinculo;

    /**
     * @var zenddate $dtVinculo
     *
     * @ORM\Column(name="dt_vinculo", type="zenddate", nullable=false)
     */
    private $dtVinculo;

    /**
     * @var datetime $dtRemocaoVinculo
     *
     * @ORM\Column(name="dt_remocao_vinculo", type="zenddate", nullable=true)
     */
    private $dtRemocaoVinculo;

    /**
     * @var text $txMotivoRemocao
     *
     * @ORM\Column(name="tx_motivo_remocao", type="text", nullable=true)
     */
    private $txMotivoRemocao;

    /**
     * @var BooleanType $inOriginal
     *
     * @ORM\Column(name="in_original", type="boolean", nullable=true)
     */
    private $inOriginal;

    /**
     * @var integer $nuOrdem
     *
     * @ORM\Column(name="nu_ordem", type="integer", nullable=true)
     */
    private $nuOrdem;
    
    public function getSqArtefatoVinculo()
    {
    	return $this->sqArtefatoVinculo;
    }

    public function setSqArtefatoVinculo($sqArtefatoVinculo)
    {
    	$this->sqArtefato = $sqArtefatoVinculo;
    }

    /**
     *
     * @return Artefato
     */
    public function getSqArtefatoPai()
    {
        return $this->sqArtefatoPai;
    }

    public function setSqArtefatoPai($sqArtefatoPai)
    {
        $this->sqArtefatoPai = $sqArtefatoPai;
        return $this;
    }

    /**
     *
     * @return Artefato
     */
    public function getSqArtefatoFilho()
    {
        return $this->sqArtefatoFilho;
    }

    public function setSqArtefatoFilho($sqArtefatoFilho)
    {
        $this->sqArtefatoFilho = $sqArtefatoFilho;
        return $this;
    }

    /**
     *
     * @return TipoVinculoArtefato
     */
    public function getSqTipoVinculoArtefato()
    {
        return $this->sqTipoVinculoArtefato ?  $this->sqTipoVinculoArtefato : new TipoVinculoArtefato();
    }

    public function setSqTipoVinculoArtefato(TipoVinculoArtefato $sqTipoVinculoArtefato = NULL)
    {
        $this->sqTipoVinculoArtefato = $sqTipoVinculoArtefato;
        return $this;
    }

    /**
     *
     * @return \Zend_Date
     */
    public function getDtVinculo()
    {
        return $this->dtVinculo;
    }

    public function setDtVinculo($dtVinculo)
    {
        $this->dtVinculo = $dtVinculo;
        return $this;
    }

    /**
     *
     * @return \Zend_Date
     */
    public function getDtRemocaoVinculo()
    {
        return $this->dtRemocaoVinculo;
    }

    public function setDtRemocaoVinculo($dtRemocaoVinculo)
    {
        $this->dtRemocaoVinculo = $dtRemocaoVinculo;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getTxMotivoRemocao()
    {
        return $this->txMotivoRemocao;
    }

    public function setTxMotivoRemocao($txMotivoRemocao)
    {
        $this->txMotivoRemocao = $txMotivoRemocao;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function getInOriginal()
    {
        return $this->inOriginal;
    }

    public function setInOriginal($inOriginal)
    {
        $this->inOriginal = $inOriginal;
        return $this;
    }

    public function setSqAnexoArtefatoVinculo($sqAnexoArtefatoVinculo)
    {
        $this->sqAnexoArtefatoVinculo = $sqAnexoArtefatoVinculo;
        return $this;
    }

    /**
     * @return AnexoArtefatoVinculo
     */
    public function getSqAnexoArtefatoVinculo()
    {
        return $this->sqAnexoArtefatoVinculo;
    }
    
    /**
     * @return integer
     */
    function getNuOrdem() {
        return $this->nuOrdem;
    }

    /**
     * @param integer $nuOrdem
     * @return \Sgdoce\Model\Entity\ArtefatoVinculo
     */
    function setNuOrdem($nuOrdem) {
        $this->nuOrdem = $nuOrdem;
        return $this;
    }
}