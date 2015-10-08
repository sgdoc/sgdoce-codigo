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
 * Sgdoce\Model\Entity\ModeloDocumentoCampo
 *
 * @ORM\Table(name="modelo_documento_campo")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ModeloDocumentoCampo")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ModeloDocumentoCampo extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqModeloDocumentoCampo
     *
     * @ORM\Column(name="sq_modelo_documento_campo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqModeloDocumentoCampo;

    /**
     * @var Sgdoce\Model\Entity\PadraoModeloDocumentoCampo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\PadraoModeloDocumentoCampo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_padrao_modelo_documento_cam", referencedColumnName="sq_padrao_modelo_documento_cam")
     * })
     */
    private $sqPadraoModeloDocumentoCam;

    /**
     * @var Sgdoce\Model\Entity\ModeloDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\ModeloDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_modelo_documento", referencedColumnName="sq_modelo_documento")
     * })
     */
    private $sqModeloDocumento;

    /**
     *Set sqModeloDocumentoCampo
     *
     * @param $sqModeloDocumentoCampo
     * @return ModeloDocumentoCampo
     */
    public function setSqModeloDocumentoCampo($sqModeloDocumentoCampo)
    {
        $this->sqModeloDocumentoCampo = $sqModeloDocumentoCampo;
        return $this;
    }

    /**
     * Get sqModeloDocumentoCampo
     *
     * @return integer
     */
    public function getSqModeloDocumentoCampo()
    {
        return $this->sqModeloDocumentoCampo;
    }

    /**
     * Set sqPadraoModeloDocumentoCam
     *
     * @param Sgdoce\Model\Entity\PadraoModeloDocumentoCampo $sqPadraoModeloDocumentoCam
     * @return ModeloDocumento
     */
    public function setSqPadraoModeloDocumentoCam(
            \Sgdoce\Model\Entity\PadraoModeloDocumentoCampo $sqPadraoModeloDocumentoCam = NULL)
    {
        $this->sqPadraoModeloDocumentoCam = $sqPadraoModeloDocumentoCam;
        return $this;
    }

    /**
     * Get sqPadraoModeloDocumentoCam
     *
     * @return Sgdoce\Model\Entity\PadraoModeloDocumentoCampo
     */
    public function getSqPadraoModeloDocumentoCam()
    {
        return $this->sqPadraoModeloDocumentoCam;
    }

    /**
     * Set sqModeloDocumento
     *
     * @param Sgdoce\Model\Entity\ModeloDocumento $sqModeloDocumento
     * @return ModeloDocumento
     */
    public function setSqModeloDocumento(\Sgdoce\Model\Entity\ModeloDocumento $sqModeloDocumento = NULL)
    {
        $this->sqModeloDocumento = $sqModeloDocumento;
        return $this;
    }

    /**
     * Get sqModeloDocumento
     *
     * @return Sgdoce\Model\Entity\ModeloDocumento
     */
    public function getSqModeloDocumento()
    {
        return $this->sqModeloDocumento;
    }
}