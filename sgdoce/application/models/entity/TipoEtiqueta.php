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
use Core\Model\OWM\Mapping as OWM;

/**
 * Sgdoce\Model\Entity\TipoEtiqueta
 *
 * @ORM\Table(name="tipo_etiqueta")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\TipoEtiqueta")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoEtiqueta extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqTipoEtiqueta
     *
     * @ORM\Id
     * @ORM\Column(name="sq_tipo_etiqueta", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoEtiqueta;

    /**
     * @var string $noTipoEtiqueta
     *
     * @ORM\Column(name="no_tipo_etiqueta", type="string", length=15, nullable=false)
     */
    private $noTipoEtiqueta;


    /**
     * Set sqTipoEtiqueta
     *
     * @param $sqTipoEtiqueta
     * @return integer
     */
    public function setSqTipoEtiqueta($sqTipoEtiqueta)
    {
        $this->sqTipoEtiqueta = $sqTipoEtiqueta;
        return $this;
    }

    /**
     * Set noTipoEtiqueta
     *
     * @param $noTipoEtiqueta
     * @return string
     */
    public function setNoTipoEtiqueta($noTipoEtiqueta)
    {
        $this->noTipoEtiqueta = $noTipoEtiqueta;
        return $this;
    }

    /**
     * Get sqTipoEtiqueta
     *
     * @return integer
     */
    public function getSqTipoEtiqueta()
    {
        return $this->sqTipoEtiqueta;
    }

    /**
     * Get noTipoEtiqueta
     *
     * @return string
     */
    public function getNoTipoEtiqueta()
    {
        return $this->noTipoEtiqueta;
    }
}