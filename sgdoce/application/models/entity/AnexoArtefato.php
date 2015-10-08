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
 * Sgdoce\Model\Entity\AnexoArtefato
 *
 * @ORM\Table(name="anexo_artefato")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\AnexoArtefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class AnexoArtefato extends \Core_Model_Entity_Abstract {

    /**
     * @var integer $sqAnexoArtefato
     *
     * @ORM\Id
     * @ORM\Column(name="sq_anexo_artefato", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqAnexoArtefato;

    /**
     * @var string $deCaminhoArquivo
     *
     * @ORM\Column(name="de_caminho_arquivo", type="string", length=200, nullable=false)
     */
    private $deCaminhoArquivo;

    /**
     * @var integer $nuPagina
     *
     * @ORM\Column(name="nu_pagina", type="integer", length=4, nullable=true)
     */
    private $nuPagina;

    /**
     * @var boolean $inFrente
     *
     * @ORM\Column(name="in_frente", type="boolean", nullable=false)
     */
    private $inFrente;

    /**
     * @var boolean $inVersoBranco
     *
     * @ORM\Column(name="in_verso_branco", type="boolean", nullable=true)
     */
    private $inVersoBranco;

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato" , inversedBy="sqArtefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     * })
     */
    private $sqArtefato;

    /**
     * @var integer $nuTamanhoArquivo
     *
     * @ORM\Column(name="nu_tamanho_arquivo", type="integer", nullable=true)
     */
    private $nuTamanhoArquivo;

    /**
     * @var integer $deExtensaoArquivo
     *
     * @ORM\Column(name="de_extensao_arquivo", type="string", length=5, nullable=true)
     */
    private $deExtensaoArquivo;

    /**
     * Set sqArtefato
     *
     * @param integer $sqArtefato
     * @return integer
     */
    public function setSqAnexoArtefato($sqAnexoArtefato = NULL)
    {
        $this->sqAnexoArtefato = $sqAnexoArtefato;
        if (!$sqAnexoArtefato) {
            $this->sqAnexoArtefato = NULL;
        }
        return $this;
    }

    /**
     * Get sqAnexoArtefato
     *
     * @return integer
     */
    public function getSqAnexoArtefato()
    {
        return $this->sqAnexoArtefato;
    }

    /**
     * Set deCaminhoArquivo
     *
     * @param string $deCaminhoArquivo
     * @return AnexoArtefato
     */
    public function setDeCaminhoArquivo($deCaminhoArquivo)
    {
        $this->assert('deCaminhoArquivo',$deCaminhoArquivo,$this);
        $this->deCaminhoArquivo = $deCaminhoArquivo;
        return $this;
    }

    /**
     * Get deCaminhoArquivo
     *
     * @return string
     */
    public function getDeCaminhoArquivo()
    {
        return $this->deCaminhoArquivo;
    }

    /**
     * Set nuPagina
     *
     * @param integer $nuPagina
     * @return AnexoArtefato
     */
    public function setNuPagina($nuPagina)
    {
        $this->assert('nuPagina',$nuPagina,$this);
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
     * Set inFrente
     *
     * @param boolean $inFrente
     * @return AnexoArtefato
     */
    public function setInFrente($inFrente)
    {
        $this->inFrente = $inFrente;
        return $this;
    }

    /**
     * Get inFrente
     *
     * @return boolean
     */
    public function getInFrente()
    {
        return $this->inFrente;
    }

    /**
     * Set $inVersoBranco
     *
     * @param boolean $inVersoBranco
     * @return AnexoArtefato
     */
    public function setInVersoBranco($inVersoBranco)
    {
        $this->inVersoBranco = $inVersoBranco;
        return $this;
    }

    /**
     * Get inVersoBranco
     *
     * @return boolean
     */
    public function getInVersoBranco()
    {
        return $this->inVersoBranco;
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return AnexoArtefato
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

    /**
     * Set deExtensaoArquivo
     *
     * @param $deExtensaoArquivo
     * @return $deExtensaoArquivo
     */
    public function setDeExtensaoArquivo($deExtensaoArquivo)
    {
        $this->deExtensaoArquivo = $deExtensaoArquivo;
        return $this;
    }

    /**
     * Get deExtensaoArquivo
     *
     * @return deExtensaoArquivo
     */
    public function getDeExtensaoArquivo()
    {
        return $this->deExtensaoArquivo;
    }

    /**
     * Set nuTamanhoArquivo
     *
     * @param $deExtensaoArquivo
     * @return $nuTamanhoArquivo
     */
    public function setNuTamanhoArquivo($nuTamanhoArquivo)
    {
        $this->nuTamanhoArquivo = $nuTamanhoArquivo;
        return $this;
    }

    /**
     * Get nuTamanhoArquivo
     *
     * @return nuTamanhoArquivo
     */
    public function getNuTamanhoArquivo()
    {
        return $this->nuTamanhoArquivo;
    }
}