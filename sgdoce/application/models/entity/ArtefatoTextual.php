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
 * Sgdoce\Model\Entity\ArtefatoTextual
 *
 * @ORM\Table(name="artefato_textual")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ArtefatoTextual")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ArtefatoTextual extends \Core_Model_Entity_Abstract {

    /**
     * @var integer $sqArtefatoTextual
     *
     * @ORM\Id
     * @ORM\Column(name="sq_artefato_textual", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqArtefatoTextual;

    /**
     * @var text $txArtefato
     *
     * @ORM\Column(name="tx_artefato", type="text", nullable=false)
     */
    private $txArtefato;

    /**
     * @var integer $nuPagina
     *
     * @ORM\Column(name="nu_pagina", type="integer", nullable=false)
     */
    private $nuPagina;

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
     * Get sqArtefatoTextual
     *
     * @return integer
     */
    public function getSqArtefatoTextual()
    {
        return $this->sqArtefatoTextual;
    }

    /**
     * Set txArtefato
     *
     * @param text $txArtefato
     * @return ArtefatoTextual
     */
    public function setTxArtefato($txArtefato)
    {
        $this->txArtefato = $txArtefato;
        return $this;
    }

    /**
     * Get txArtefato
     *
     * @return text
     */
    public function getTxArtefato()
    {
        return $this->txArtefato;
    }

    /**
     * Set nuPagina
     *
     * @param integer $nuPagina
     * @return ArtefatoTextual
     */
    public function setNuPagina($nuPagina)
    {
        $this->nuPagina = $nuPagina;
        return $this;
    }

    /**
     * Get nuPagina
     *
     * @return integer
     */
    public function getNuPagina()
    {
        return $this->nuPagina;
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return ArtefatoTextual
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