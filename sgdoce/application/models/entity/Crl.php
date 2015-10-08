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
 * Sgdoce\Model\Entity\Crl
 *
 * @ORM\Table(name="crl")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Crl")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Crl extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqCrl
     *
     * @ORM\Id
     * @ORM\Column(name="sq_crl", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqCrl;

    /**
     * @var string $noEmissor
     *
     * @ORM\Column(name="no_emissor", type="string", length=100, nullable=false)
     */
    private $noEmissor;

    /**
     * @var string $nuSerial
     *
     * @ORM\Column(name="nu_serial", type="string", length=40, nullable=false)
     */
    private $nuSerial;

    /**
     * @var blob $txCrl
     *
     * @ORM\Column(name="tx_crl", type="blob", nullable=false)
     */
    private $txCrl;


    /**
     * Get sqCrl
     *
     * @return integer
     */
    public function getSqCrl()
    {
        return $this->sqCrl;
    }

    /**
     * Set noEmissor
     *
     * @param string $noEmissor
     * @return Crl
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
     * Set nuSerial
     *
     * @param string $nuSerial
     * @return Crl
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
     * Set txCrl
     *
     * @param blob $txCrl
     * @return Crl
     */
    public function setTxCrl($txCrl)
    {
        $this->txCrl = $txCrl;
        return $this;
    }

    /**
     * Get txCrl
     *
     * @return blob
     */
    public function getTxCrl()
    {
        return $this->txCrl;
    }
}