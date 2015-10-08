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
 * Classe para Entity Tipo Documento
 *
 * @package      Model
 * @subpackage     Entity
 * @name         TipoDocumento
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sica\Model\Entity\TipoDocumento
 *
 * @ORM\Table(name="vw_tipo_documento")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\TipoDocumento", readOnly=true)
 */
class TipoDocumento extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqTipoDocumento
     *
     * @ORM\Column(name="sq_tipo_documento", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoDocumento;

    /**
     * @var string $noTipoDocumento
     *
     * @ORM\Column(name="no_tipo_documento", type="string", length=50, nullable=false)
     */
    private $noTipoDocumento;

    /**
     * @var Sica\Model\Entity\AtributoTipoDocumento
     *
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\AtributoTipoDocumento", mappedBy="sqTipoDocumento")
     */
    private $sqAtributoTipoDocumento;

    /**
     * Get sqTipoDocumento
     *
     * @return integer
     */
    public function setSqTipoDocumento($sqTipoDocumento)
    {
        $this->sqTipoDocumento = $sqTipoDocumento;
        return $this;
    }

    /**
     * Get sqTipoDocumento
     *
     * @return integer
     */
    public function getSqTipoDocumento()
    {
        return $this->sqTipoDocumento;
    }

    /**
     * Set noTipoDocumento
     *
     * @param string $noTipoDocumento
     * @return TipoDocumento
     */
    public function setNoTipoDocumento($noTipoDocumento)
    {
        $this->noTipoDocumento = $noTipoDocumento;
        return $this;
    }

    /**
     * Get noTipoDocumento
     *
     * @return string
     */
    public function getNoTipoDocumento()
    {
        return $this->noTipoDocumento;
    }

    /**
     * Get \Doctrine\Common\Collections\ArrayCollection
     *
     * @return TipoDocumento
     */
    public function setSqAtributoTipoDocumento(\Doctrine\Common\Collections\ArrayCollection $sqAtributoTipoDocumento)
    {
        $this->sqAtributoTipoDocumento = $sqAtributoTipoDocumento;
        return $this;
    }

    /**
     * Get \Doctrine\Common\Collections\ArrayCollection
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSqAtributoTipoDocumento()
    {
        return $this->sqAtributoTipoDocumento;
    }

}