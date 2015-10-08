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
 * SISICMBio
 *
 * Classe para Entity Pessoa
 *
 * @package      Model
 * @subpackage   Entity
 * @name         TipoDocumento
 * @version      1.0.0
 * @since        2012-06-26
 */

/**
 * Sgdoce\Model\Entity\VwTipoDocumento
 *
 * @ORM\Table(name="vw_tipo_documento")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwTipoDocumento", readOnly=true)
 */
class VwTipoDocumento extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoDocumento
     *
     * @ORM\Column(name="sq_tipo_documento", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    private $sqTipoDocumento;

    /**
     * @var string $noTipoDocumento
     *
     * @ORM\Column(name="no_tipo_documento", type="string", nullable=true)
     */
    private $noTipoDocumento;

    /**
     * @var Sgdoce\Model\Repository\VwAtributoTipoDocumento
     *
     * @ORM\OneToMany(targetEntity="Sgdoce\Model\Entity\VwAtributoTipoDocumento", mappedBy="sqTipoDocumento")
     */
    private $sqAtributoTipoDocumento;

    public function getSqTipoDocumento()
    {
        return $this->sqTipoDocumento;
    }

    public function setNoTipoDocumento($noTipoDocumento)
    {
        $this->noTipoDocumento = $noTipoDocumento;

        return $this;
    }

    public function getNoTipoDocumento()
    {
        return $this->noTipoDocumento;
    }

    /**
     * Set \Doctrine\Common\Collections\ArrayCollection
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