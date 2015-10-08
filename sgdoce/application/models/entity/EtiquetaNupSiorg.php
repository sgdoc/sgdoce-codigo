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
 * @ORM\Table(name="etiqueta_nup_siorg")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\EtiquetaNupSiorg")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class EtiquetaNupSiorg extends \Core_Model_Entity_Abstract
{

    /**
     * @var bigint $sqLoteEtiqueta
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\LoteEtiqueta" )
     * @ORM\JoinColumn(name="sq_lote_etiqueta", referencedColumnName="sq_lote_etiqueta", nullable=false)
     */
    private $sqLoteEtiqueta;

    /**
     * @var bigint $nuEtiqueta
     *
     * @ORM\Id
     * @ORM\Column(name="nu_etiqueta", type="bigint", nullable=false)
     */
    private $nuEtiqueta;

    /**
     * @var string $nuNupSiorg
     *
     * @ORM\Column(name="nu_nup_siorg", type="string", length=21, nullable=true)
     */
    private $nuNupSiorg;

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
     * Set nuNupSiorg
     *
     * @param string $nuNupSiorg
     * @return self
     */
    public function setNuNupSiorg ($nuNupSiorg)
    {
        $this->nuNupSiorg = $nuNupSiorg;
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
    public function getNuEtiqueta($formated=FALSE)
    {
        return ($formated && strlen($this->nuEtiqueta) < 7) ? str_pad($this->nuEtiqueta, 7, '0', STR_PAD_LEFT) : $this->nuEtiqueta;
    }

    /**
     * Get nuNupSiorg
     *
     * @return string
     */
    public function getNuNupSiorg ($withMask=FALSE)
    {
        if ($withMask) {
            $filter = new \Core_Filter_MaskNumber(array('mask'=>'nup'));
            return $filter->filter($this->nuNupSiorg);
        }
        return $this->nuNupSiorg;
    }

}