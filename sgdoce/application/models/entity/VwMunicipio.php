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
 * Classe para Entity VwMunicipio
 *
 * @package      Model
 * @subpackage     Entity
 * @name         Municipio
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sgdoce\Model\Entity\VwMunicipio
 *
 * @ORM\Table(name="vw_municipio")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwMunicipio", readOnly=true)
 */
class VwMunicipio extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqMunicipio
     *
     * @ORM\Column(name="sq_municipio", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqMunicipio;

    /**
     * @var integer $coIbge
     *
     * @ORM\Column(name="co_ibge", type="integer", nullable=false)
     */
    private $coIbge;

    /**
     * @var text $noMunicipio
     *
     * @ORM\Column(name="no_municipio", type="text", nullable=false)
     */
    private $noMunicipio;

    /**
     * @var Sgdoce\Model\Entity\VwEstado
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwEstado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_estado", referencedColumnName="sq_estado")
     * })
     */
    private $sqEstado;

    public function setSqMunicipio($sqMunicipio = NULL)
    {
        $this->sqMunicipio = $sqMunicipio;
        if (!$sqMunicipio) {
            $this->sqMunicipio = NULL;
        }
        return $this;
    }
    /**
     * Get sqMunicipio
     *
     * @return integer
     */
    public function getSqMunicipio()
    {
        return $this->sqMunicipio;
    }

    /**
     * Set coIbge
     *
     * @param integer $coIbge
     * @return Municipio
     */
    public function setCoIbge($coIbge)
    {
        $this->coIbge = $coIbge;
        return $this;
    }

    /**
     * Get coIbge
     *
     * @return integer
     */
    public function getCoIbge()
    {
        return $this->coIbge;
    }

    /**
     * Set noMunicipio
     *
     * @param text $noMunicipio
     * @return Municipio
     */
    public function setNoMunicipio($noMunicipio)
    {
        $this->noMunicipio = $noMunicipio;
        return $this;
    }

    /**
     * Get noMunicipio
     *
     * @return text
     */
    public function getNoMunicipio()
    {
        return $this->noMunicipio;
    }

    /**
     * Set sqEstado
     *
     * @param Sgdoce\Model\Entity\Estado $sqEstado
     * @return Municipio
     */
    public function setSqEstado(VwEstado $sqEstado = NULL)
    {
        $this->sqEstado = $sqEstado;
        return $this;
    }

    /**
     * Get sqEstado
     *
     * @return Sgdoce\Model\Entity\Estado
     */
    public function getSqEstado()
    {
        return $this->sqEstado ? $this->sqEstado : new VwEstado();
    }

}