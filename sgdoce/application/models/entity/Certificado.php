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
 * Sgdoce\Model\Entity\Certificado
 *
 * @ORM\Table(name="certificado")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Certificado")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Certificado extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqCertificado
     *
     * @ORM\Id
     * @ORM\Column(name="sq_certificado", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqCertificado;

    /**
     * @var string $noEmissor
     *
     * @ORM\Column(name="no_emissor", type="string", length=100, nullable=false)
     */
    private $noEmissor;

    /**
     * @var string $noProprietario
     *
     * @ORM\Column(name="no_proprietario", type="string", length=100, nullable=false)
     */
    private $noProprietario;

    /**
     * @var string $nuSerial
     *
     * @ORM\Column(name="nu_serial", type="string", length=40, nullable=false)
     */
    private $nuSerial;

    /**
     * @var blob $txCertificado
     *
     * @ORM\Column(name="tx_certificado", type="blob", nullable=false)
     */
    private $txCertificado;

    /**
     * @var Sgdoce\Model\Entity\SgdoceCertificado
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Certificado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_certificado_pai", referencedColumnName="sq_certificado")
     * })
     */
    private $sqCertificadoPai;


    /**
     * Get sqCertificado
     *
     * @return integer
     */
    public function getSqCertificado()
    {
        return $this->sqCertificado;
    }

    /**
     * Set noEmissor
     *
     * @param string $noEmissor
     * @return Certificado
     */
    public function setNoEmissor($noEmissor)
    {
        $this->noEmissor = $noEmissor;
        return $this;
    }

    /**
     * Get noEmissor
     *
     * @return string
     */
    public function getNoEmissor()
    {
        return $this->noEmissor;
    }

    /**
     * Set noProprietario
     *
     * @param string $noProprietario
     * @return Certificado
     */
    public function setNoProprietario($noProprietario)
    {
        $this->noProprietario = $noProprietario;
        return $this;
    }

    /**
     * Get noProprietario
     *
     * @return string
     */
    public function getNoProprietario()
    {
        return $this->noProprietario;
    }

    /**
     * Set nuSerial
     *
     * @param string $nuSerial
     * @return Certificado
     */
    public function setNuSerial($nuSerial)
    {
        $this->nuSerial = $nuSerial;
        return $this;
    }

    /**
     * Get nuSerial
     *
     * @return string
     */
    public function getNuSerial()
    {
        return $this->nuSerial;
    }

    /**
     * Set txCertificado
     *
     * @param blob $txCertificado
     * @return Certificado
     */
    public function setTxCertificado($txCertificado)
    {
        $this->txCertificado = $txCertificado;
        return $this;
    }

    /**
     * Get txCertificado
     *
     * @return blob
     */
    public function getTxCertificado()
    {
        return $this->txCertificado;
    }

    /**
     * Set sqCertificadoPai
     *
     * @param Sgdoce\Model\Entity\SgdoceCertificado $sqCertificadoPai
     * @return Certificado
     */
    public function setSqCertificadoPai(\Sgdoce\Model\Entity\SgdoceCertificado $sqCertificadoPai = NULL)
    {
        $this->sqCertificadoPai = $sqCertificadoPai;
        return $this;
    }

    /**
     * Get sqCertificadoPai
     *
     * @return Sgdoce\Model\Entity\SgdoceCertificado
     */
    public function getSqCertificadoPai()
    {
        return $this->sqCertificadoPai;
    }
}