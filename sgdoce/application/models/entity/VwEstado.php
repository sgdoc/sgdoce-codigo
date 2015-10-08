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

/**
 * Sgdoce\Model\Entity\VwEstado
 *
 * @ORM\Table(name="vw_estado")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\VwEstado", readOnly=true)
 */
class VwEstado extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqEstado
     *
     * @ORM\Id
     * @ORM\Column(name="sq_estado", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqEstado;

    /**
     * @var integer $coIbge
     * @ORM\Column(name="co_ibge", type="integer", nullable=false)
     */
    private $coIbge;

    /**
     * @var string $noEstado
     * @ORM\Column(name="no_estado", type="string", nullable=false)
     */
    private $noEstado;

    /**
     * @var Sgdoce\Model\Entity\VwPais
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwPais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pais", referencedColumnName="sq_pais")
     * })
     */
    private $sqPais;

    /**
     * @var integer $sgEstado
     * @ORM\Column(name="sg_estado", type="string", nullable=true)
     */
    private $sgEstado;

    public function setSqEstado($sqEstado)
    {
        $this->sqEstado = $sqEstado;
        if (!$sqEstado) {
            $this->sqEstado = NULL;
        }
        return $this;
    }

    public function getSqEstado()
    {
        return $this->sqEstado;
    }

    public function setCoIbge($coIbge)
    {
        $this->coIbge = $coIbge;
    }

    public function getCoIbge()
    {
        return $this->coIbge;
    }

    public function setNoEstado($noEstado)
    {
        $this->noEstado = $noEstado;
    }

    public function getNoEstado()
    {
        return $this->noEstado;
    }

    public function setSqPais($sqPais)
    {
        $this->sqPais = $sqPais;
    }

    public function getSqPais()
    {
        return $this->sqPais;
    }

    public function setSgEstado($sgEstado)
    {
        $this->sgEstado = $sgEstado;
    }

    public function getSgEstado()
    {
        return $this->sgEstado;
    }

}