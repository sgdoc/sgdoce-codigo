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
 * Sgdoce\Model\Entity\SequencialArtefato
 *
 * @ORM\Table(name="sequencial_artefato")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\SequencialArtefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class SequencialArtefato extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqSequencialArtefato
     *
     * @ORM\Column(name="sq_sequencial_artefato", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqSequencialArtefato;

    /**
     * @var integer $nuSequencial
     *
     * @ORM\Column(name="nu_sequencial", type="integer", nullable=false)
     */
    private $nuSequencial;

    /**
     * @var integer $nuAno
     *
     * @ORM\Column(name="nu_ano", type="integer", nullable=false)
     */
    private $nuAno;

    /**
     * @var Sgdoce\Model\Entity\TipoArtefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoArtefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_artefato", referencedColumnName="sq_tipo_artefato")
     * })
     */
    private $sqTipoArtefato;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_org", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeOrg;

    /**
     * @var Sgdoce\Model\Entity\TipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoDocumento", inversedBy="sqSequencialArtefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_documento", referencedColumnName="sq_tipo_documento")
     * })
     */
    private $sqTipoDocumento;


    /**
     * Set nuSequencial
     *
     * @param integer $nuSequencial
     * @return SequencialArtefato
     */
    public function setSqSequencialArtefato($sqSequencialArtefato = NULL)
    {
    	$this->sqSequencialArtefato = $sqSequencialArtefato;
    	if (!$sqSequencialArtefato) {
    		$this->sqSequencialArtefato = NULL;
    	}
    	return $this;
    }

    /**
     * Get sqSequencialArtefato
     *
     * @return integer
     */
    public function getSqSequencialArtefato()
    {
        return $this->sqSequencialArtefato;
    }

    /**
     * Set nuSequencial
     *
     * @param integer $nuSequencial
     * @return SequencialArtefato
     */
    public function setNuSequencial($nuSequencial)
    {
        $this->nuSequencial = $nuSequencial;
        return $this;
    }

    /**
     * Get nuSequencial
     *
     * @return integer
     */
    public function getNuSequencial()
    {
        return $this->nuSequencial;
    }

    /**
     * Set nuAno
     *
     * @param integer $nuAno
     * @return SequencialArtefato
     */
    public function setNuAno($nuAno)
    {
        $this->nuAno = $nuAno;
        return $this;
    }

    /**
     * Get nuAno
     *
     * @return integer
     */
    public function getNuAno()
    {
        return $this->nuAno;
    }

    /**
     * Set sqTipoArtefato
     *
     * @param Sgdoce\Model\Entity\TipoArtefato $sqTipoArtefato
     * @return SequencialArtefato
     */
    public function setSqTipoArtefato(TipoArtefato $sqTipoArtefato = NULL)
    {
        $this->sqTipoArtefato = $sqTipoArtefato;
        return $this;
    }

    /**
     * Get sqTipoArtefato
     *
     * @return Sgdoce\Model\Entity\TipoArtefato
     */
    public function getSqTipoArtefato()
    {
        return $this->sqTipoArtefato;
    }

    /**
     * Set sqTipoDocumento
     *
     * @param Sgdoce\Model\Entity\TipoDocumento $sqTipoDocumento
     * @return SequencialArtefato
     */
    public function setSqTipoDocumento(TipoDocumento $sqTipoDocumento = NULL)
    {
        $this->sqTipoDocumento = $sqTipoDocumento;
        return $this;
    }

    /**
     * Get sqTipoDocumento
     *
     * @return Sgdoce\Model\Entity\TipoDocumento
     */
    public function getSqTipoDocumento()
    {
        return $this->sqTipoDocumento;
    }

    /**
     * Set sqUnidadeOrg
     *
     * @param Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeOrg
     * @return SequencialArtefato
     */
    public function setSqUnidadeOrg(VwUnidadeOrg $sqUnidadeOrg = NULL)
    {
        $this->sqUnidadeOrg = $sqUnidadeOrg;
        return $this;
    }

    /**
     * Get sqUnidadeOrg
     *
     * @return Sgdoce\Model\Entity\VwUnidadeOrg
     */
    public function getSqUnidadeOrg()
    {
        return $this->sqUnidadeOrg;
    }
}