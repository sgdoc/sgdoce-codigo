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
 * Sgdoce\Model\Entity\TipoAssinante
 *
 * @ORM\Table(name="tipo_assinante")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\TipoAssinante")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoAssinante extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoAssinante
     *
     * @ORM\Id
     * @ORM\Column(name="sq_tipo_assinante", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoAssinante;

    /**
     * @var string $noTipoAssinante
     *
     * @ORM\Column(name="no_tipo_assinante", type="string", length=20, nullable=false)
     */
    private $noTipoAssinante;


    /**
     * Get sqTipoAssinante
     *
     * @return integer
     */
    public function getSqTipoAssinante()
    {
        return $this->sqTipoAssinante;
    }

    /**
     * Set sqTipoAssinante
     *
     * @param string $sqTipoAssinante
     * @return TipoAssinante
     */
    public function setSqTipoAssinante($sqTipoAssinante)
    {
        $this->sqTipoAssinante = $sqTipoAssinante;
        return $this;
    }

    /**
     * Set noTipoAssinante
     *
     * @param string $noTipoAssinante
     * @return TipoAssinante
     */
    public function setNoTipoAssinante($noTipoAssinante)
    {
        $this->noTipoAssinante = $noTipoAssinante;
        return $this;
    }

    /**
     * Get noTipoAssinante
     *
     * @return string
     */
    public function getNoTipoAssinante()
    {
        return $this->noTipoAssinante;
    }
}