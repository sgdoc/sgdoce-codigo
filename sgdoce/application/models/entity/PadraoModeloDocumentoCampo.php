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
 * Sgdoce\Model\Entity\PadraoModeloDocumentoCampo
 *
 * @ORM\Table(name="padrao_modelo_documento_campo")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\PadraoModeloDocumentoCampo")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class PadraoModeloDocumentoCampo extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqPadraoModeloDocumentoCam
     *
     * @ORM\Column(name="sq_padrao_modelo_documento_cam", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqPadraoModeloDocumentoCam;

    /**
     * @var boolean $inObrigatorio
     *
     * @ORM\Column(name="in_obrigatorio", type="boolean", nullable=false)
     */
    private $inObrigatorio;

    /**
     * @var boolean $inVisivelDocumento
     *
     * @ORM\Column(name="in_visivel_documento", type="boolean", nullable=false)
     */
    private $inVisivelDocumento;

    /**
     * @var integer $nuOrdem
     *
     * @ORM\Column(name="nu_ordem", type="integer", nullable=false)
     */
    private $nuOrdem;

    /**
     * @var Sgdoce\Model\Entity\Campo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Campo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_campo", referencedColumnName="sq_campo")
     * })
     */
    private $sqCampo;

    /**
     * @var Sgdoce\Model\Entity\PadraoModeloDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\PadraoModeloDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_padrao_modelo_documento", referencedColumnName="sq_padrao_modelo_documento")
     * })
     */
    private $sqPadraoModeloDocumento;

    /**
     * @var Sgdoce\Model\Entity\ModeloDocumentoCampo
     *
     * @ORM\OneToMany(targetEntity="Sgdoce\Model\Entity\ModeloDocumentoCampo", mappedBy="sqPadraoModeloDocumentoCam")
     */
    private $sqModeloDocumentoCampo;

    /**
     * Set sqPadraoModeloDocumentoCam
     *
     * @param boolean $sqPadraoModeloDocumentoCam
     * @return PadraoModeloDocumentoCampo
     */
    public function setSqPadraoModeloDocumentoCam($sqPadraoModeloDocumentoCam)
    {
        $this->sqPadraoModeloDocumentoCam = $sqPadraoModeloDocumentoCam;
        return $this;
    }

    /**
     * Get sqPadraoModeloDocumentoCam
     *
     * @return integer
     */
    public function getSqPadraoModeloDocumentoCam()
    {
        return $this->sqPadraoModeloDocumentoCam;
    }

    /**
     * Set inObrigatorio
     *
     * @param boolean $inObrigatorio
     * @return PadraoModeloDocumentoCampo
     */
    public function setInObrigatorio($inObrigatorio)
    {
        $this->inObrigatorio = $inObrigatorio;
        return $this;
    }

    /**
     * Get inObrigatorio
     *
     * @return boolean
     */
    public function getInObrigatorio()
    {
        return $this->inObrigatorio;
    }

    /**
     * Set nuOrdem
     *
     * @param integer $nuOrdem
     * @return PadraoModeloDocumentoCampo
     */
    public function setNuOrdem($nuOrdem)
    {
        $this->nuOrdem = $nuOrdem;
        return $this;
    }

    /**
     * Get nuOrdem
     *
     * @return integer
     */
    public function getNuOrdem()
    {
        return $this->nuOrdem;
    }

    /**
     * Set sqCampo
     *
     * @param Sgdoce\Model\Entity\Campo $sqCampo
     * @return PadraoModeloDocumentoCampo
     */
    public function setSqCampo(\Sgdoce\Model\Entity\Campo $sqCampo = NULL)
    {
        $this->sqCampo = $sqCampo;
        return $this;
    }

    /**
     * Get sqCampo
     *
     * @return Sgdoce\Model\Entity\Campo
     */
    public function getSqCampo()
    {
        return $this->sqCampo;
    }

    /**
     * Set sqPadraoModeloDocumento
     *
     * @param Sgdoce\Model\Entity\PadraoModeloDocumento $sqPadraoModeloDocumento
     * @return PadraoModeloDocumentoCampo
     */
    public function setSqPadraoModeloDocumento(
                        \Sgdoce\Model\Entity\PadraoModeloDocumento $sqPadraoModeloDocumento = NULL)
    {
        $this->sqPadraoModeloDocumento = $sqPadraoModeloDocumento;
        return $this;
    }

    /**
     * Get sqPadraoModeloDocumento
     *
     * @return Sgdoce\Model\Entity\PadraoModeloDocumento
     */
    public function getSqPadraoModeloDocumento()
    {
        return $this->sqPadraoModeloDocumento;
    }

    /**
     * Set sqModeloDocumentoCampo
     *
     * @param $sqModeloDocumentoCampo
     * @return PadraoModeloDocumentoCampo
     */
    public function setSqModeloDocumentoCampo($sqModeloDocumentoCampo)
    {
        $this->sqModeloDocumentoCampo = $sqModeloDocumentoCampo;
        return $this;
    }

    /**
     * Get sqModeloDocumentoCampo
     *
     * @return Sgdoce\Model\Entity\ModeloDocumentoCampo
     */
    public function getSqModeloDocumentoCampo()
    {
        return $this->sqModeloDocumentoCampo;
    }

    /**
     * Set $inVisivelDocumento
     *
     * @param boolean $inVisivelDocumento
     * @return PadraoModeloDocumentoCampo
     */
    public function setInVisivelDocumento($inVisivelDocumento)
    {
        $this->inVisivelDocumento = $inVisivelDocumento;
        return $this;
    }

    /**
     * Get inObrigatorio
     *
     * @return boolean
     */
    public function getInVisivelDocumento()
    {
        return $this->inVisivelDocumento;
    }
}
