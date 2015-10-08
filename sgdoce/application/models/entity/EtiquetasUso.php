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
 * Sgdoce\Model\Entity\EtiquetasUso
 *
 * @ORM\Table(name="etiquetas_uso")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\EtiquetasUso")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class EtiquetasUso extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqLoteEtiqueta
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\EtiquetaNupSiorg" )
     * @ORM\JoinColumn(name="sq_lote_etiqueta", referencedColumnName="sq_lote_etiqueta", nullable=false)
     */
    private $sqLoteEtiqueta;

    /**
     * @var bigint $nuEtiqueta
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\EtiquetaNupSiorg" )
     * @ORM\JoinColumn(name="nu_etiqueta", referencedColumnName="nu_etiqueta", nullable=false)
     */
    private $nuEtiqueta;

    /**
     * Set sqLoteEtiqueta
     *
     * @param $sqLoteEtiqueta
     * @return self
     */
    public function setSqLoteEtiqueta($sqLoteEtiqueta)
    {
        $this->sqLoteEtiqueta = $sqLoteEtiqueta;
        return $this;
    }

    /**
     * Set nuEtiqueta
     *
     * @param $nuEtiqueta
     * @return self
     */
    public function setNuEtiqueta($nuEtiqueta)
    {
        $this->nuEtiqueta = $nuEtiqueta;
        return $this;
    }

    /**
     * Get sqLoteEtiqueta
     *
     * @return integer
     */
    public function getSqLoteEtiqueta()
    {
        return $this->sqLoteEtiqueta;
    }

    /**
     * Get nuEtiqueta
     *
     * @return integer
     */
    public function getNuEtiqueta()
    {
        return $this->nuEtiqueta;
    }

}