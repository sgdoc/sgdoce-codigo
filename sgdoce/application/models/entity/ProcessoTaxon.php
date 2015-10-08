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
 * Sgdoce\Model\Entity\ProcessoTaxon
 *
 * @ORM\Table(name="processo_taxon")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ProcessoTaxon")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ProcessoTaxon extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $processoTaxon
     *
     * @ORM\Column(name="sq_processo_taxon", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqProcessoTaxon;

    /**
     * @var Sgdoce\Model\Entity\VwIntegracaoCanie
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwIntegracaoTaxon")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_taxon", referencedColumnName="codigo")
     * })
     */
    private $sqTaxon;

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
     * Get processoTaxon
     *
     * @return integer
     */
    public function getSqProcessoTaxon()
    {
        return $this->sqProcessoTaxon;
    }

    /**
     * Set sqTaxon
     *
     * @param integer $sqTaxon
     * @return ProcessoTaxon
     */
    public function setSqTaxon(\Sgdoce\Model\Entity\VwIntegracaoTaxon $sqTaxon)
    {
        $this->sqTaxon = $sqTaxon;
        return $this;
    }

    /**
     * Get sqTaxon
     *
     * @return integer
     */
    public function getSqTaxon()
    {
        return $this->sqTaxon ? $this->sqTaxon : new VwIntegracaoTaxon();
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return ProcessoTaxon
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
        return $this->sqArtefato;
    }
}