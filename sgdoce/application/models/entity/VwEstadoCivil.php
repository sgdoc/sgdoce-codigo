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
 * Classe para entity Estado Civil
 *
 * @package      Model
 * @subpackage     Entity
 * @name         EstadoCivil
 * @version     1.0.0
 * @since        2012-08-26
 */

/**
 * Sgdoce\Model\Entity\VwEstadoCivil
 *
 * @ORM\Table(name="vw_estado_civil")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwEstadoCivil", readOnly=true)
 */
class VwEstadoCivil extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqEstadoCivil
     *
     * @ORM\Column(name="sq_estado_civil", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqEstadoCivil;

    /**
     * @var string $noEstadoCivil
     *
     * @ORM\Column(name="no_estado_civil", type="string", nullable=false)
     */
    private $noEstadoCivil;

    /**
     * Set sqEstadoCivil
     *
     * @return integer
     */
    public function setSqEstadoCivil($sqEstadoCivil)
    {
        $this->sqEstadoCivil = $sqEstadoCivil;
        return $this;
    }

    /**
     * Get sqEstadoCivil
     *
     * @return integer
     */
    public function getSqEstadoCivil()
    {
        return $this->sqEstadoCivil;
    }

    /**
     * Set noEstadoCivil
     *
     * @param string $noEstadoCivil
     * @return EstadoCivil
     */
    public function setNoEstadoCivil($noEstadoCivil)
    {
        $this->noEstadoCivil = $noEstadoCivil;
        return $this;
    }

    /**
     * Get noEstadoCivil
     *
     * @return string
     */
    public function getNoEstadoCivil()
    {
        return $this->noEstadoCivil;
    }

}