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

use Doctrine\DBAL\Types\BigIntType;
use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
 * Sgdoce\Model\Entity\ArtefatoProcesso
 *
 * @ORM\Table(name="artefato_processo")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ArtefatoProcesso")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ArtefatoProcesso extends \Core_Model_Entity_Abstract
{

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\Artefato", mappedBy="sqArtefato")
     * @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     */
    private $sqArtefato;

    /**
     * @var Sgdoce\Model\Entity\VwEstado
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwEstado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_estado", referencedColumnName="sq_estado")
     * })
     */
    private $sqEstado;

    /**
     * @var integer $nuPaginaProcesso
     *
     * @ORM\Column(name="nu_pagina_processo", type="integer", nullable=true)
     */
    private $nuPaginaProcesso;

    /**
     * @var $coAmbitoProcesso
     * @ORM\Column(name="co_ambito_processo", type="string", nullable=true)
     */
    private $coAmbitoProcesso;

    /**
     * @var boolean in_numeracao_verso
     * @ORM\Column(name="in_numeracao_verso", type="boolean", nullable=true)
     */
    private $inNumeracaoVerso;

    /**
     * @var Sgdoce\Model\Entity\VwMunicipio
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwMunicipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_municipio", referencedColumnName="sq_municipio")
     * })
     */
    private $sqMunicipio;

    /**
     * @var $nuVolume
     * @ORM\Column(name="nu_volume", type="integer", nullable=true)
     */
    private $nuVolume;

    /**
     * Set sqArtefato
     *
     * @param bigint $sqArtefato
     * @return bigint
     */
    public function setSqArtefato ($sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
    }

    public function getSqArtefato ()
    {
        return $this->sqArtefato;
    }

    /**
     * Set sqEstado
     *
     * @param Sgdoce\Model\Entity\Estado $sqEstado
     * @return Municipio
     */
    public function setSqEstado (VwEstado $sqEstado = NULL)
    {
        $this->sqEstado = $sqEstado;
        return $this;
    }

    /**
     * Get sqEstado
     *
     * @return Sgdoce\Model\Entity\Estado
     */
    public function getSqEstado ()
    {
        return $this->sqEstado ? $this->sqEstado : new VwEstado();
    }

    public function setNuPaginaProcesso ($nuPaginaProcesso)
    {
        $this->nuPaginaProcesso = $nuPaginaProcesso;
        return $this;
    }

    /**
     * Get nuPaginaProcesso
     *
     * @return integer
     */
    public function getNuPaginaProcesso ()
    {
        return $this->nuPaginaProcesso;
    }

    /**
     * Get coAmbitoProcesso
     *
     * @return char
     */
    public function getCoAmbitoProcesso ()
    {
        return $this->coAmbitoProcesso;
    }

    public function setCoAmbitoProcesso ($coAmbitoProcesso = NULL)
    {
        $this->coAmbitoProcesso = $coAmbitoProcesso;
    }

    public function getInNumeracaoVerso ()
    {
        return $this->inNumeracaoVerso;
    }

    public function setInNumeracaoVerso ($inNumeracaoVerso)
    {
        $this->inNumeracaoVerso = $inNumeracaoVerso;
    }

    /**
     * Set sqMunicipio
     *
     * @param Sgdoce\Model\Entity\VwMunicipio $sqMunicipio
     * @return Municipio
     */
    public function setSqMunicipio (VwMunicipio $sqMunicipio = NULL)
    {
        $this->sqMunicipio = $sqMunicipio;
        return $this;
    }

    /**
     * Get sqMunicipio
     *
     * @return Sgdoce\Model\Entity\VwMunicipio
     */
    public function getSqMunicipio ()
    {
        return $this->sqMunicipio ? $this->sqMunicipio : new VwMunicipio();
    }

    /**
     * @param mixed $nuVolume
     */
    public function setNuVolume ($nuVolume)
    {
        $this->nuVolume = $nuVolume;
    }

    /**
     * @return mixed
     */
    public function getNuVolume ()
    {
        return !empty($this->nuVolume) ? $this->nuVolume : NULL;
    }

    /**
     * @param integer $sqArtefato
     */
    public function setSqArtefatoDestino ($sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
    }

    /**
     * @return \Sgdoce\Model\Entity\Artefato
     */
    public function getSqArtefatoDestino ()
    {
        return $this->sqArtefato;
    }

}
