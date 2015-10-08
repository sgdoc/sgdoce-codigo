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

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SISICMBio
 *
 * Classe para Entity Estado
 *
 * @package      Model
 * @subpackage   Entity
 * @name         Estado
 * @version      1.0.0
 * @since        2012-07-31
 */

/**
 * Sica\Model\Entity\Estado
 *
 * @ORM\Table(name="vw_estado")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\Estado", readOnly=true)
 */
class Estado extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqEstado
     *
     * @ORM\Column(name="sq_estado", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqEstado;

    /**
     * @var string $noEstado
     *
     * @ORM\Column(name="no_estado", type="string", length=50, nullable=false)
     */
    private $noEstado;

    /**
     * @var string $sgEstado
     *
     * @ORM\Column(name="sg_estado", type="string", nullable=false)
     */
    private $sgEstado;

    /**
     * @var integer $coIbge
     *
     * @ORM\Column(name="co_ibge", type="integer", nullable=true)
     */
    private $coIbge;

    /**
     * @var Sica\Model\Entity\Pais
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Pais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pais", referencedColumnName="sq_pais")
     * })
     */
    private $sqPais;

    /**
     * Get sqEstado
     *
     * @return integer
     */
    public function setSqEstado($sqEstado)
    {
        $this->sqEstado = $sqEstado;
        return $this;
    }

    /**
     * Get sqEstado
     *
     * @return integer
     */
    public function getSqEstado()
    {
        return $this->sqEstado;
    }

    /**
     * Set sgEstado
     *
     * @param string $sgEstado
     * @return Estado
     */
    public function setSgEstado($sgEstado)
    {
        $this->sgEstado = $sgEstado;
        return $this;
    }

    /**
     * Get sgEstado
     *
     * @return string
     */
    public function getSgEstado()
    {
        return $this->sgEstado;
    }

    /**
     * Set noEstado
     *
     * @param string $noEstado
     * @return Estado
     */
    public function setNoEstado($noEstado)
    {
        $this->noEstado = $noEstado;
        return $this;
    }

    /**
     * Get noEstado
     *
     * @return string
     */
    public function getNoEstado()
    {
        return $this->noEstado;
    }

    /**
     * Set coIbge
     *
     * @param integer $coIbge
     * @return Estado
     */
    public function setCoIbge($coIbge)
    {
        $this->coIbge = $coIbge;
        return $this;
    }

    /**
     * Get coIbge
     *
     * @return integer
     */
    public function getCoIbge()
    {
        return $this->coIbge;
    }

    /**
     * Set sqPais
     *
     * @param Sica\Model\Entity\Pais $sqPais
     * @return Estado
     */
    public function setSqPais(Pais $sqPais = NULL)
    {
        $this->sqPais = $sqPais;
        return $this;
    }

    /**
     * Get sqPais
     *
     * @return Sica\Model\Entity\Pais
     */
    public function getSqPais()
    {
        if (NULL === $this->sqPais) {
            $this->setSqPais(new Pais());
        }

        return $this->sqPais;
    }

}
