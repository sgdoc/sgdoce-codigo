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
 * Sgdoce\Model\Entity\PadraoModeloDocumento
 *
 * @ORM\Table(name="padrao_modelo_documento")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\PadraoModeloDocumento")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class PadraoModeloDocumento extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqPadraoModeloDocumento
     *
     * @ORM\Column(name="sq_padrao_modelo_documento", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqPadraoModeloDocumento;

    /**
     * @var string $noPadraoModeloDocumento
     *
     * @ORM\Column(name="no_padrao_modelo_documento", type="string", length=40, nullable=false)
     */
    private $noPadraoModeloDocumento;

    /**
     * Set sqPadraoModeloDocumento
     *
     * @param $sqPadraoModeloDocumento
     * @return PadraoModeloDocumento
     */
    public function setSqPadraoModeloDocumento($sqPadraoModeloDocumento)
    {
        $this->sqPadraoModeloDocumento = $sqPadraoModeloDocumento;
        return $this;
    }

    /**
     * Get sqPadraoModeloDocumento
     *
     * @return integer
     */
    public function getSqPadraoModeloDocumento()
    {
        return $this->sqPadraoModeloDocumento;
    }

    /**
     * Set noPadraoModeloDocumento
     *
     * @param string $noPadraoModeloDocumento
     * @return PadraoModeloDocumento
     */
    public function setNoPadraoModeloDocumento($noPadraoModeloDocumento)
    {
        $this->noPadraoModeloDocumento = $noPadraoModeloDocumento;
        return $this;
    }

    /**
     * Get noPadraoModeloDocumento
     *
     * @return string
     */
    public function getNoPadraoModeloDocumento()
    {
        return $this->noPadraoModeloDocumento;
    }
}