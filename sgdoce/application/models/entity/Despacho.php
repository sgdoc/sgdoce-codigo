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
 * Sgdoce\Model\Entity\Despacho
 *
 * @ORM\Table(name="despacho")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Despacho")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Despacho extends \Core_Model_Entity_Abstract {

    /**
     * @var integer $sqDespacho
     * @ORM\Id
     * @ORM\Column(name="sq_despacho", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqDespacho;

    /**
     * @var text $txConteudo
     *
     * @ORM\Column(name="tx_conteudo", type="text", nullable=true)
     */
    private $txConteudo;

    /**
     * @var integer $nuDigital
     *
     * @ORM\Column(name="nu_digital", type="integer", nullable=true)
     */
    private $nuDigital;

    /**
     * @var datetime $dtDespacho
     *
     * @ORM\Column(name="dt_despacho", type="datetime", nullable=true)
     */
    private $dtDespacho;

    /**
     * @var text $txObservacao
     *
     * @ORM\Column(name="tx_observacao", type="text", nullable=true)
     */
    private $txObservacao;

    /**
     * @var Sgdoce\Model\Entity\SgdoceAssinatura
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Assinatura")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_assinatura", referencedColumnName="sq_assinatura")
     * })
     */
    private $sqAssinatura;

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
     * Set sqDespacho
     *
     * @param integer $sqDespacho
     * @return Despacho
     */
    public function setSqDespacho($sqDespacho)
    {
        $this->sqDespacho = $sqDespacho;
        return $this;
    }

    /**
     * Get sqDespacho
     *
     * @return integer
     */
    public function getSqDespacho()
    {
        return $this->sqDespacho;
    }

    /**
     * Set txConteudo
     *
     * @param text $txConteudo
     * @return Despacho
     */
    public function setTxConteudo($txConteudo)
    {
        $this->txConteudo = $txConteudo;
        return $this;
    }

    /**
     * Get txConteudo
     *
     * @return text
     */
    public function getTxConteudo()
    {
        return $this->txConteudo;
    }

    /**
     * Set nuDigital
     *
     * @param integer $nuDigital
     * @return Despacho
     */
    public function setNuDigital($nuDigital)
    {
        $this->nuDigital = $nuDigital;
        return $this;
    }

    /**
     * Get nuDigital
     *
     * @return integer
     */
    public function getNuDigital()
    {
        return $this->nuDigital;
    }

    /**
     * Set dtDespacho
     *
     * @param datetime $dtDespacho
     * @return Despacho
     */
    public function setDtDespacho($dtDespacho)
    {
        $this->dtDespacho = $dtDespacho;
        return $this;
    }

    /**
     * Get dtDespacho
     *
     * @return datetime
     */
    public function getDtDespacho()
    {
        return $this->dtDespacho;
    }

    /**
     * Set txObservacao
     *
     * @param text $txObservacao
     * @return Despacho
     */
    public function setTxObservacao($txObservacao)
    {
        $this->txObservacao = $txObservacao;
        return $this;
    }

    /**
     * Get txObservacao
     *
     * @return text
     */
    public function getTxObservacao()
    {
        return $this->txObservacao;
    }

    /**
     * Set sqAssinatura
     *
     * @param Sgdoce\Model\Entity\SgdoceAssinatura $sqAssinatura
     * @return Despacho
     */
    public function setSqAssinatura(\Sgdoce\Model\Entity\SgdoceAssinatura $sqAssinatura = NULL)
    {
        $this->sqAssinatura = $sqAssinatura;
        return $this;
    }

    /**
     * Get sqAssinatura
     *
     * @return Sgdoce\Model\Entity\SgdoceAssinatura
     */
    public function getSqAssinatura()
    {
        return $this->sqAssinatura;
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return Despacho
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

}