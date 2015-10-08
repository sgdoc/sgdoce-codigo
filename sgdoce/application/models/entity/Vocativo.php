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
 * Sgdoce\Model\Entity\Vocativo
 *
 * @ORM\Table(name="vocativo")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Vocativo")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Vocativo extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqVocativo
     *
     * @ORM\Id
     * @ORM\Column(name="sq_vocativo", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqVocativo;

    /**
     * @var string $noVocativo
     *
     * @ORM\Column(name="no_vocativo", type="string", length=50, nullable=false)
     */
    private $noVocativo;

    /**
     * Set sqVocativo
     *
     * @param integer $sqVocativo
     * @return integer
     */
    public function setSqVocativo($sqVocativo = NULL)
    {
        $this->sqVocativo = $sqVocativo;
        if(!$sqVocativo){
            $this->sqVocativo  = NULL;
        }
        return $this;
    }

    /**
     * Get sqVocativo
     *
     * @return integer
     */
    public function getSqVocativo()
    {
        return $this->sqVocativo;
    }

    /**
     * Set noVocativo
     *
     * @param string $noVocativo
     * @return Vocativo
     */
    public function setNoVocativo($noVocativo)
    {
        $this->noVocativo = $noVocativo;
        return $this;
    }

    /**
     * Get noVocativo
     *
     * @return string
     */
    public function getNoVocativo()
    {
        return $this->noVocativo;
    }
}