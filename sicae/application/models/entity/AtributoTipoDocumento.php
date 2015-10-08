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
 * Classe para Entity Tipo AtributoTipoDocumento
 *
 * @package      Model
 * @subpackage     Entity
 * @name         AtributoTipoDocumento
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sica\Model\Entity\AtributoTipoDocumento
 *
 * @ORM\Table(name="vw_atributo_tipo_documento")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\AtributoTipoDocumento", readOnly=true)
 */
class AtributoTipoDocumento extends \Core_Model_Entity_Abstract
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
     * @var Sica\Model\Entity\AtributoDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\AtributoDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_atributo_documento", referencedColumnName="sq_atributo_documento")
     * })
     */
    private $sqAtributoDocumento;

    /**
     * @var Sica\Model\Entity\TipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\TipoDocumento")
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
    public function setSqAtributoTipoDocumento($sqAtributoTipoDocumento)
    {
        $this->sqAtributoTipoDocumento = $sqAtributoTipoDocumento;
        return $this;
    }

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
     * Get $sqAtributoDocumento
     *
     * @return integer
     */
    public function setSqAtributoDocumento(\Sica\Model\Entity\AtributoDocumento $sqAtributoDocumento)
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
        return $this->sqAtributoDocumento ? $this->sqAtributoDocumento : new \Sica\Model\Entity\AtributoDocumento();
    }

    /**
     * Get $sqTipoDocumento
     *
     * @return integer
     */
    public function setSqTipoDocumento(\Sica\Model\Entity\TipoDocumento $sqTipoDocumento)
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
        return $this->sqTipoDocumento ? $this->sqTipoDocumento : new \Sica\Model\Entity\TipoDocumento();
    }

}