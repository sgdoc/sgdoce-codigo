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
 * Sgdoce\Model\Entity\Carimbo
 *
 * @ORM\Table(name="carimbo")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\Carimbo")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Carimbo extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqCarimbo
     *
     * @ORM\Column(name="sq_carimbo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqCarimbo;

    /**
     * @var string $noCarimbo
     *
     * @ORM\Column(name="no_carimbo", type="string", length=100, nullable=false)
     */
    private $noCarimbo;

    /**
     * @var string $deCaminhoArquivo
     *
     * @ORM\Column(name="de_caminho_arquivo", type="string", length=200, nullable=false)
     */
    private $deCaminhoArquivo;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     * @var boolean $inEditavel
     *
     * @ORM\Column(name="in_editavel", type="boolean", nullable=false)
     */
    private $inEditavel;

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
     * Get sqCarimbo
     *
     * @return integer
     */
    public function getSqCarimbo()
    {
        return $this->sqCarimbo;
    }

    /**
     * Set noCarimbo
     *
     * @param string $noCarimbo
     * @return Carimbo
     */
    public function setNoCarimbo($noCarimbo)
    {
        $this->noCarimbo = $noCarimbo;
        return $this;
    }

    /**
     * Get noCarimbo
     *
     * @return string
     */
    public function getNoCarimbo()
    {
        return $this->noCarimbo;
    }

    /**
     * Set deCaminhoArquivo
     *
     * @param string $deCaminhoArquivo
     * @return Carimbo
     */
    public function setDeCaminhoArquivo($deCaminhoArquivo)
    {
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
     * Set stRegistroAtivo
     *
     * @param boolean $stRegistroAtivo
     * @return Carimbo
     */
    public function setStRegistroAtivo($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }

    /**
     * Get stRegistroAtivo
     *
     * @return boolean
     */
    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }

    /**
     * Set inEditavel
     *
     * @param boolean $stRegistroAtivo
     * @return Carimbo
     */
    public function setInEditavel($inEditavel)
    {
        $this->inEditavel = $inEditavel;
        return $this;
    }

    /**
     * Get inEditavel
     *
     * @return boolean
     */
    public function getinEditavel()
    {
        return $this->inEditavel;
    }

    /**
     * Set sqTipoArtefato
     *
     * @param Sgdoce\Model\Entity\TipoArtefato $sqTipoArtefato
     * @return Carimbo
     */
    public function setSqTipoArtefato(\Sgdoce\Model\Entity\TipoArtefato $sqTipoArtefato = NULL)
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
}