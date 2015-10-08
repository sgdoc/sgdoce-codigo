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
 * Sgdoce\Model\Entity\TipoArtefato
 *
 * @ORM\Table(name="tipo_artefato")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\TipoArtefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoArtefato extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqTipoArtefato
     *
     * @ORM\Column(name="sq_tipo_artefato", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoArtefato;

    /**
     * @var string $noTipoArtefato
     *
     * @ORM\Column(name="no_tipo_artefato", type="string", length=20, nullable=false)
     */
    private $noTipoArtefato;

    /**
     * Set sqTipoArtefato
     *
     * @return integer
     */
    public function setSqTipoArtefato($sqTipoArtefato)
    {
        $this->sqTipoArtefato = $sqTipoArtefato;
        return $this;
    }

    /**
     * Get sqTipoArtefato
     *
     * @return integer
     */
    public function getSqTipoArtefato()
    {
        return $this->sqTipoArtefato;
    }

    /**
     * Set noTipoArtefato
     *
     * @param string $noTipoArtefato
     * @return TipoArtefato
     */
    public function setNoTipoArtefato($noTipoArtefato)
    {
        $this->noTipoArtefato = $noTipoArtefato;
        return $this;
    }

    /**
     * Get noTipoArtefato
     *
     * @return string
     */
    public function getNoTipoArtefato()
    {
        return $this->noTipoArtefato;
    }

}