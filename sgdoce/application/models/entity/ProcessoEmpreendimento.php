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
 * Sgdoce\Model\Entity\ProcessoEmpreendimento
 *
 * @ORM\Table(name="processo_empreendimento")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ProcessoEmpreendimento")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ProcessoEmpreendimento extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqProcessoEmpreendimento
     *
     * @ORM\Column(name="sq_processo_empreendimento", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqProcessoEmpreendimento;

    /**
     * @var Sgdoce\Model\Entity\VwIntegracaoSgca
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwIntegracaoSgca")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_empreendimento", referencedColumnName="codigo")
     * })
     */
    private $sqEmpreendimento;

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
     * Get sqProcessoEmpreendimento
     *
     * @return integer
     */
    public function getSqProcessoEmpreendimento()
    {
        return $this->sqProcessoEmpreendimento;
    }

    /**
     * Set sqEmpreendimento
     *
     * @param integer $sqEmpreendimento
     * @return ProcessoEmpreendimento
     */
    public function setSqEmpreendimento(\Sgdoce\Model\Entity\VwIntegracaoSgca $sqEmpreendimento)
    {
        $this->sqEmpreendimento = $sqEmpreendimento;
        return $this;
    }

    /**
     * Get sqEmpreendimento
     *
     * @return integer
     */
    public function getSqEmpreendimento()
    {
        return $this->sqEmpreendimento ? $this->sqEmpreendimento : new VwIntegracaoSgca();
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return ProcessoEmpreendimento
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