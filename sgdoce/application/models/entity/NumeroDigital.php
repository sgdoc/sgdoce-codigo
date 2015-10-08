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
 * Sgdoce\Model\Entity\NumeroDigital
 *
 * @ORM\Table(name="numero_digital")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\NumeroDigital")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class NumeroDigital extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqNumeroDigital
     *
     * @ORM\Column(name="sq_numero_digital", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqNumeroDigital;

    /**
     * @var integer $nuAno
     *
     * @ORM\Column(name="nu_ano", type="integer", nullable=false)
     */
    private $nuAno;

    /**
     * @var integer $nuDigital
     *
     * @ORM\Column(name="nu_digital", type="integer", nullable=false)
     */
    private $nuDigital;


    /**
     * Get sqNumeroDigital
     *
     * @return integer
     */
    public function getSqNumeroDigital()
    {
        return $this->sqNumeroDigital;
    }

    /**
     * Set nuAno
     *
     * @param integer $nuAno
     * @return NumeroDigital
     */
    public function setNuAno($nuAno)
    {
        $this->nuAno = $nuAno;
        return $this;
    }

    /**
     * Get nuAno
     *
     * @return integer
     */
    public function getNuAno()
    {
        return $this->nuAno;
    }

    /**
     * Set nuDigital
     *
     * @param integer $nuDigital
     * @return NumeroDigital
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
}