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

/**
 * SISICMBio
 *
 * Classe para Entity Tipo Vinculo
 *
 * @package      Model
 * @subpackage     Entity
 * @name         TipoVinculo
 * @version     1.0.0
 * @since        2012-06-26
 */
use Doctrine\ORM\Mapping as ORM;

/**
 * Sica\Model\Entity\TipoVinculo
 *
 * @ORM\Table(name="vw_tipo_vinculo")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\TipoVinculo", readOnly=true)
 */
class TipoVinculo extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqTipoVinculo
     *
     * @ORM\Column(name="sq_tipo_vinculo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoVinculo;

    /**
     * @var string $noTipoVinculo
     *
     * @ORM\Column(name="no_tipo_vinculo", type="string", length=50, nullable=false)
     */
    private $noTipoVinculo;

    /**
     * @var Sica\Model\Entity\TipoVinculo
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\TipoVinculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_vinculo_pai", referencedColumnName="sq_tipo_vinculo")
     * })
     */
    private $sqTipoVinculoPai;

    /**
     * Get sqTipoVinculo
     *
     * @return integer
     */
    public function getSqTipoVinculo()
    {
        return $this->sqTipoVinculo;
    }

    /**
     * Set noTipoVinculo
     *
     * @param string $noTipoVinculo
     * @return TipoVinculo
     */
    public function setNoTipoVinculo($noTipoVinculo)
    {
        $this->noTipoVinculo = $noTipoVinculo;
        return $this;
    }

    /**
     * Get noTipoVinculo
     *
     * @return string
     */
    public function getNoTipoVinculo()
    {
        return $this->noTipoVinculo;
    }

    /**
     * Set sqTipoVinculoPai
     *
     * @param Sica\Model\Entity\TipoVinculo $sqTipoVinculoPai
     * @return TipoVinculo
     */
    public function setSqTipoVinculoPai(\Sica\Model\Entity\TipoVinculo $sqTipoVinculoPai = NULL)
    {
        $this->sqTipoVinculoPai = $sqTipoVinculoPai;
        return $this;
    }

    /**
     * Get sqTipoVinculoPai
     *
     * @return Sica\Model\Entity\TipoVinculo
     */
    public function getSqTipoVinculoPai()
    {
        return $this->sqTipoVinculoPai ? $this->sqTipoVinculo : new \Sica\Model\Entity\TipoVinculo();
    }

}