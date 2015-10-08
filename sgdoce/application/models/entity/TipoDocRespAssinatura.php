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
 * Sgdoce\Model\Entity\TipoDocRespAssinatura
 *
 * @ORM\Table(name="tipo_doc_resp_assinatura")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\TipoDocRespAssinatura")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoDocRespAssinatura extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoDocRespAssinatura
     *
     * @ORM\Column(name="sq_tipo_doc_resp_assinatura", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoDocRespAssinatura;

    /**
     * @var Sgdoce\Model\Entity\SgdoceTipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_documento", referencedColumnName="sq_tipo_documento")
     * })
     */
    private $sqTipoDocumento;

    /**
     * @var Sgdoce\Model\Entity\SgdoceResponsavelAssinatura
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\ResponsavelAssinatura")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_responsavel_assinatura", referencedColumnName="sq_responsavel_assinatura")
     * })
     */
    private $sqResponsavelAssinatura;


    /**
     * Get sqTipoDocRespAssinatura
     *
     * @return integer
     */
    public function getSqTipoDocRespAssinatura()
    {
        return $this->sqTipoDocRespAssinatura;
    }

    /**
     * Set sqTipoDocumento
     *
     * @param Sgdoce\Model\Entity\SgdoceTipoDocumento $sqTipoDocumento
     * @return TipoDocRespAssinatura
     */
    public function setSqTipoDocumento(\Sgdoce\Model\Entity\TipoDocumento $sqTipoDocumento = NULL)
    {
        $this->sqTipoDocumento = $sqTipoDocumento;
        return $this;
    }

    /**
     * Get sqTipoDocumento
     *
     * @return Sgdoce\Model\Entity\SgdoceTipoDocumento
     */
    public function getSqTipoDocumento()
    {
        return $this->sqTipoDocumento;
    }

    /**
     * Set sqResponsavelAssinatura
     *
     * @param Sgdoce\Model\Entity\SgdoceResponsavelAssinatura $sqResponsavelAssinatura
     * @return TipoDocRespAssinatura
     */
    public function setSqResponsavelAssinatura(
                        \Sgdoce\Model\Entity\ResponsavelAssinatura $sqResponsavelAssinatura = NULL)
    {
        $this->sqResponsavelAssinatura = $sqResponsavelAssinatura;
        return $this;
    }

    /**
     * Get sqResponsavelAssinatura
     *
     * @return Sgdoce\Model\Entity\SgdoceResponsavelAssinatura
     */
    public function getSqResponsavelAssinatura()
    {
        return $this->sqResponsavelAssinatura;
    }
}