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
 * Sgdoce\Model\Entity\CrlAssinatura
 *
 * @ORM\Table(name="crl_assinatura")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\CrlAssinatura")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class CrlAssinatura extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqCrlAssinatura
     *
     * @ORM\Id
     * @ORM\Column(name="sq_crl_assinatura", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqCrlAssinatura;

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
     * @var Sgdoce\Model\Entity\SgdoceCrl
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Crl")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_crl", referencedColumnName="sq_crl")
     * })
     */
    private $sqCrl;


    /**
     * Get sqCrlAssinatura
     *
     * @return integer
     */
    public function getSqCrlAssinatura()
    {
        return $this->sqCrlAssinatura;
    }

    /**
     * Set sqAssinatura
     *
     * @param Sgdoce\Model\Entity\SgdoceAssinatura $sqAssinatura
     * @return CrlAssinatura
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
     * Set sqCrl
     *
     * @param Sgdoce\Model\Entity\SgdoceCrl $sqCrl
     * @return CrlAssinatura
     */
    public function setSqCrl(\Sgdoce\Model\Entity\SgdoceCrl $sqCrl = NULL)
    {
        $this->sqCrl = $sqCrl;
        return $this;
    }

    /**
     * Get sqCrl
     *
     * @return Sgdoce\Model\Entity\SgdoceCrl
     */
    public function getSqCrl()
    {
        return $this->sqCrl;
    }
}