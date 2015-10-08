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
 * Sgdoce\Model\Entity\Fecho
 *
 * @ORM\Table(name="fecho")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Fecho")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Fecho extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqFecho
     *
     * @ORM\Id
     * @ORM\Column(name="sq_fecho", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqFecho;

    /**
     * @var string $noFecho
     *
     * @ORM\Column(name="no_fecho", type="string", length=30, nullable=false)
     */
    private $noFecho;

    /**
     * Set sqFecho
     *
     * @param integer $sqFecho
     * @return integer
     */
    public function setSqFecho($sqFecho = NULL)
    {
        $this->sqFecho = $sqFecho;
        if (!$sqFecho) {
            $this->sqFecho = NULL;
        }
        return $this;
    }

    /**
     * Get sqFecho
     *
     * @return integer
     */
    public function getSqFecho()
    {
        return $this->sqFecho;
    }

    /**
     * Set noFecho
     *
     * @param string $noFecho
     * @return Fecho
     */
    public function setNoFecho($noFecho)
    {
        $this->noFecho = $noFecho;
        return $this;
    }

    /**
     * Get noFecho
     *
     * @return string
     */
    public function getNoFecho()
    {
        return $this->noFecho;
    }
}