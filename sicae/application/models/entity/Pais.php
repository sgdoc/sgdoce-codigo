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
 * Classe para Entity Pais
 *
 * @package      Model
 * @subpackage     Entity
 * @name         Pais
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sica\Model\Entity\Pais
 *
 * @ORM\Table(name="vw_pais")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\Pais", readOnly=true)
 */
class Pais extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqPais
     *
     * @ORM\Column(name="sq_pais", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqPais;

    /**
     * @var string $noPais
     *
     * @ORM\Column(name="no_pais", type="string", length=50, nullable=false)
     */
    private $noPais;

    /**
     * @var blob $theGeom
     *
     * @ORM\Column(name="the_geom", type="blob", nullable=true)
     */
    private $theGeom;

    /**
     * @var boolean $inFazFronteira
     *
     * @ORM\Column(name="in_faz_fronteira", type="boolean", nullable=false)
     */
    private $inFazFronteira;

    /**
     * Set sqPais
     *
     * @return integer
     */
    public function setSqPais($sqPais)
    {
        $this->sqPais = $sqPais;
        return $this;
    }

    /**
     * Get sqPais
     *
     * @return integer
     */
    public function getSqPais()
    {
        return $this->sqPais;
    }

    /**
     * Set noPais
     *
     * @param string $noPais
     * @return Pais
     */
    public function setNoPais($noPais)
    {
        $this->noPais = $noPais;
        return $this;
    }

    /**
     * Get noPais
     *
     * @return string
     */
    public function getNoPais()
    {
        return $this->noPais;
    }

    /**
     * Set theGeom
     *
     * @param blob $theGeom
     * @return Pais
     */
    public function setTheGeom($theGeom)
    {
        $this->theGeom = $theGeom;
        return $this;
    }

    /**
     * Get theGeom
     *
     * @return blob
     */
    public function getTheGeom()
    {
        return $this->theGeom;
    }

    /**
     * Set inFazFronteira
     *
     * @param boolean $inFazFronteira
     * @return Pais
     */
    public function setInFazFronteira($inFazFronteira)
    {
        $this->inFazFronteira = $inFazFronteira;
        return $this;
    }

    /**
     * Get inFazFronteira
     *
     * @return string
     */
    public function getInFazFronteira()
    {
        return $this->inFazFronteira;
    }

}