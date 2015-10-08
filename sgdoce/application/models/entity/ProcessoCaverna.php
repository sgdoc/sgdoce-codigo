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
 * Sgdoce\Model\Entity\ProcessoCaverna
 *
 * @ORM\Table(name="processo_caverna")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ProcessoCaverna")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ProcessoCaverna extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqProcessoCaverna
     *
     * @ORM\Column(name="sq_processo_caverna", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqProcessoCaverna;

    /**
     * @var Sgdoce\Model\Entity\VwIntegracaoCanie
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwIntegracaoCanie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_caverna", referencedColumnName="codigo")
     * })
     */
    private $sqCaverna;

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     * })
     */
    private $sqArtefato;

    /**
     * Set sqProcessoCaverna
     *
     * @param integer sqProcessoCaverna
     * @return integer
     */
    public function setSqProcessoCaverna($sqProcessoCaverna = NULL)
    {
        $this->sqProcessoCaverna = $sqProcessoCaverna;
        if (!$sqProcessoCaverna) {
            $this->sqProcessoCaverna = NULL;
        }
        return $this;
    }
    /**
     * Get sqProcessoCaverna
     *
     * @return integer
     */
    public function getSqProcessoCaverna()
    {
        return $this->sqProcessoCaverna;
    }

    /**
     * Set sqCaverna
     *
     * @param integer $sqCaverna
     * @return ProcessoCaverna
     */
    public function setSqCaverna(\Sgdoce\Model\Entity\VwIntegracaoCanie $sqCaverna)
    {
        $this->sqCaverna = $sqCaverna;
        return $this;
    }

    /**
     * Get sqCaverna
     *
     * @return integer
     */
    public function getSqCaverna()
    {
        return $this->sqCaverna ? $this->sqCaverna : new VwIntegracaoCanie();
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return ProcessoCaverna
     */
    public function setSqArtefato(\Sgdoce\Model\Entity\Artefato $sqArtefato = NULL)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    /**
     * Get sqArtefato
     *
     * @return Sgdoce\Model\Entity\Artefato
     */
    public function getSqArtefato()
    {
        return $this->sqArtefato ? $this->sqArtefato : new Artefato();
    }
}