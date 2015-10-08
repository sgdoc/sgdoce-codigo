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
 * Sgdoce\Model\Entity\StatusArtefato
 *
 * @ORM\Table(name="status_artefato")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\StatusArtefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class StatusArtefato extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqStatusArtefato
     *
     * @ORM\Column(name="sq_status_artefato", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqStatusArtefato;

    /**
     * @var string $noStatusArtefato
     *
     * @ORM\Column(name="no_status_artefato", type="string", length=30, nullable=false)
     */
    private $noStatusArtefato;

    /**
     * Set sqStatusArtefato
     *
     * @param string $sqStatusArtefato
     * @return StatusArtefato
     */
    public function setSqStatusArtefato($sqStatusArtefato)
    {
        $this->sqStatusArtefato = $sqStatusArtefato;
        return $this;
    }

    /**
     * Get sqStatusArtefato
     *
     * @return integer
     */
    public function getSqStatusArtefato()
    {
        return $this->sqStatusArtefato;
    }

    /**
     * Set noStatusArtefato
     *
     * @param string $noStatusArtefato
     * @return StatusArtefato
     */
    public function setNoStatusArtefato($noStatusArtefato)
    {
        $this->noStatusArtefato = $noStatusArtefato;
        return $this;
    }

    /**
     * Get noStatusArtefato
     *
     * @return string
     */
    public function getNoStatusArtefato()
    {
        return $this->noStatusArtefato;
    }
}