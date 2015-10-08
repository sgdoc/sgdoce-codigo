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
 * Classe para Entity Tipo AtributoTipoDocumento
 *
 * @package      Model
 * @subpackage   Entity
 * @name         AtributoTipoDocumento
 * @version      1.0.0
 * @since        2012-06-26
 */

/**
 * Sgdoce\Model\Entity\VwAtributoTipoDocumento
 *
 * @ORM\Table(name="vw_atributo_tipo_documento")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwAtributoTipoDocumento", readOnly=true)
 */
class VwAtributoTipoDocumento extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqAtributoTipoDocumento
     *
     * @ORM\Column(name="sq_atributo_tipo_documento", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqAtributoTipoDocumento;

    /**
     * @var Sgdoce\Model\Entity\VwAtributoDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwAtributoDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_atributo_documento", referencedColumnName="sq_atributo_documento")
     * })
     */
    private $sqAtributoDocumento;

    /**
     * @var Sgdoce\Model\Entity\VwTipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwTipoDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_documento", referencedColumnName="sq_tipo_documento")
     * })
     */
    private $sqTipoDocumento;

    /**
     * Get sqAtributoDocumento
     *
     * @return integer
     */
    public function getSqAtributoTipoDocumento()
    {
        return $this->sqAtributoTipoDocumento;
    }

    /**
     * Get sqAtributoTipoDocumento
     *
     * @return integer
     */
    public function setSqAtributoTipoDocumento($sqAtributoTipoDocumento)
    {
        $this->sqAtributoTipoDocumento = $sqAtributoTipoDocumento;

        return $this;
    }

    /**
     * Get $sqAtributoDocumento
     *
     * @return integer
     */
    public function setSqAtributoDocumento(VwAtributoDocumento $sqAtributoDocumento)
    {
        $this->sqAtributoDocumento = $sqAtributoDocumento;
        return $this;
    }

    /**
     * Get sqAtributoDocumento
     *
     * @return integer
     */
    public function getSqAtributoDocumento()
    {
        return $this->sqAtributoDocumento ? $this->sqAtributoDocumento : new VwAtributoDocumento();
    }

    /**
     * Get $sqTipoDocumento
     *
     * @return integer
     */
    public function setSqTipoDocumento(TipoDocumento $sqTipoDocumento)
    {
        $this->sqTipoDocumento = $sqTipoDocumento;
        return $this;
    }

    /**
     * Get $sqTipoDocumento
     *
     * @return integer
     */
    public function getSqTipoDocumento()
    {
        return $this->sqTipoDocumento ? $this->sqTipoDocumento : new VwTipoDocumento();
    }

}