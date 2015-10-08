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
 * Classe para Entity Tipo Email
 *
 * @package      Model
 * @subpackage   Entity
 * @name         Email
 * @version      1.0.0
 * @since        2012-06-26
 */

/**
 * Sgdoce\Model\Entity\VwTipoEmail
 *
 * @ORM\Table(name="vw_tipo_sociedade")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwTipoSociedade", readOnly=true)
 */
class VwTipoSociedade extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqTipoSociedade
     *
     * @ORM\Id
     * @ORM\Column(name="sq_tipo_sociedade", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoSociedade;

    /**
     * @var string $noTipoSociedade
     *
     * @ORM\Column(name="no_tipo_sociedade", type="string", length=200, nullable=false)
     */
    private $noTipoSociedade;

    /**
     * Set $sqTipoSociedade
     *
     * @param  $sqTipoSociedade
     * @return VwTipoSociedade
     */
    public function setSqTipoSociedade($sqTipoSociedade)
    {
        $this->sqTipoSociedade = $sqTipoSociedade;

        return $this;
    }

    /**
     * Get $sqTipoSociedade
     *
     * @return $sqTipoSociedade
     */
    public function getSqTipoSociedade()
    {
        return $this->sqTipoSociedade;
    }

    /**
     * Set $noTipoSociedade
     *
     * @param string $noTipoSociedade
     * @return VwTipoSociedade
     */
    public function setNoTipoSociedade($noTipoSociedade)
    {
        $this->noTipoSociedade = $noTipoSociedade;

        return $this;
    }

    /**
     * Get $noTipoSociedade
     *
     * @return string
     */
    public function getNoTipoSociedade()
    {
        return $this->noTipoSociedade;
    }

}