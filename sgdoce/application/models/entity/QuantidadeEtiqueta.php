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
 * Sgdoce\Model\Entity\QuantidadeEtiqueta
 *
 * @ORM\Table(name="quantidade_etiqueta")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\QuantidadeEtiqueta")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class QuantidadeEtiqueta extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqQuantidadeEtiqueta
     *
     * @ORM\Id
     * @ORM\Column(name="sq_quantidade_etiqueta", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqQuantidadeEtiqueta;

    /**
     * @var string $dsQuantidadeEtiqueta
     *
     * @ORM\Column(name="ds_quantidade_etiqueta", type="string", length=35, nullable=false)
     */
    private $dsQuantidadeEtiqueta;

    /**
     * @var integer $nuQuantidade
     *
     * @ORM\Column(name="nu_quantidade", type="integer", nullable=false)
     */
    private $nuQuantidade;


    /**
     * Set sqQuantidadeEtiqueta
     *
     * @param $sqQuantidadeEtiqueta
     * @return self
     */
    public function setSqQuantidadeEtiqueta($sqQuantidadeEtiqueta)
    {
        $this->sqQuantidadeEtiqueta = $sqQuantidadeEtiqueta;
        return $this;
    }

    /**
     * Set dsQuantidadeEtiqueta
     *
     * @param $dsQuantidadeEtiqueta
     * @return self
     */
    public function setDsQuantidadeEtiqueta($dsQuantidadeEtiqueta)
    {
        $this->dsQuantidadeEtiqueta = $dsQuantidadeEtiqueta;
        return $this;
    }

    /**
     * Set nuQuantidade
     *
     * @param $nuQuantidade
     * @return self
     */
    public function setNuQuantidade($nuQuantidade)
    {
        $this->nuQuantidade = $nuQuantidade;
        return $this;
    }

    /**
     * Get sqQuantidadeEtiqueta
     *
     * @return integer
     */
    public function getSqQuantidadeEtiqueta()
    {
        return $this->sqQuantidadeEtiqueta;
    }

    /**
     * Get dsQuantidadeEtiqueta
     *
     * @return string
     */
    public function getDsQuantidadeEtiqueta()
    {
        return $this->dsQuantidadeEtiqueta;
    }

    /**
     * Get nuQuantidade
     *
     * @return integer
     */
    public function getNuQuantidade()
    {
        return $this->nuQuantidade;
    }
}