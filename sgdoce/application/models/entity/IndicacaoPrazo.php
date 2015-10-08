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
 * Sgdoce\Model\Entity\IndicacaoPrazo
 *
 * @ORM\Table(name="indicacao_prazo")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\IndicacaoPrazo")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class IndicacaoPrazo extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqIndicacaoPrazo
     *
     * @ORM\Column(name="sq_indicacao_prazo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqIndicacaoPrazo;

    /**
     * @var boolean $inPrazoObrigatorio
     *
     * @ORM\Column(name="in_prazo_obrigatorio", type="boolean", nullable=true)
     */
    private $inPrazoObrigatorio;

    /**
     * @var integer $nuDiasPrazo
     *
     * @ORM\Column(name="nu_dias_prazo", type="integer", nullable=true)
     */
    private $nuDiasPrazo;

    /**
     * @var boolean $inDiasCorridos
     *
     * @ORM\Column(name="in_dias_corridos", type="boolean", nullable=true)
     */
    private $inDiasCorridos;

    /**
     * @var Sgdoce\Model\Entity\Assunto
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Assunto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_assunto", referencedColumnName="sq_assunto")
     * })
     */
    private $sqAssunto;

    /**
     * @var Sgdoce\Model\Entity\TipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_documento", referencedColumnName="sq_tipo_documento")
     * })
     */
    private $sqTipoDocumento;


    /**
     * Get sqIndicacaoPrazo
     *
     * @return integer
     */
    public function getSqIndicacaoPrazo()
    {
        return $this->sqIndicacaoPrazo;
    }

    /**
     * Set inPrazoObrigatorio
     *
     * @param boolean $inPrazoObrigatorio
     * @return IndicacaoPrazo
     */
    public function setInPrazoObrigatorio($inPrazoObrigatorio)
    {
        $this->inPrazoObrigatorio = $inPrazoObrigatorio;
        return $this;
    }

    /**
     * Get inPrazoObrigatorio
     *
     * @return boolean
     */
    public function getInPrazoObrigatorio()
    {
        return $this->inPrazoObrigatorio;
    }

    /**
     * Set nuDiasPrazo
     *
     * @param integer $nuDiasPrazo
     * @return IndicacaoPrazo
     */
    public function setNuDiasPrazo($nuDiasPrazo)
    {
        $this->nuDiasPrazo = $nuDiasPrazo;
        return $this;
    }

    /**
     * Get nuDiasPrazo
     *
     * @return integer
     */
    public function getNuDiasPrazo()
    {
        return $this->nuDiasPrazo;
    }

    /**
     * Set inDiasCorridos
     *
     * @param boolean $inDiasCorridos
     * @return IndicacaoPrazo
     */
    public function setInDiasCorridos($inDiasCorridos)
    {
        $this->inDiasCorridos = $inDiasCorridos;
        return $this;
    }

    /**
     * Get inDiasCorridos
     *
     * @return boolean
     */
    public function getInDiasCorridos()
    {
        return $this->inDiasCorridos;
    }

    /**
     * Set sqAssunto
     *
     * @param Sgdoce\Model\Entity\Assunto $sqAssunto
     * @return IndicacaoPrazo
     */
    public function setSqAssunto(\Sgdoce\Model\Entity\Assunto $sqAssunto = NULL)
    {
        $this->sqAssunto = $sqAssunto;
        return $this;
    }

    /**
     * Get sqAssunto
     *
     * @return Sgdoce\Model\Entity\Assunto
     */
    public function getSqAssunto()
    {
        return $this->sqAssunto;
    }

    /**
     * Set sqTipoDocumento
     *
     * @param Sgdoce\Model\Entity\TipoDocumento $sqTipoDocumento
     * @return IndicacaoPrazo
     */
    public function setSqTipoDocumento(\Sgdoce\Model\Entity\TipoDocumento $sqTipoDocumento = NULL)
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
}